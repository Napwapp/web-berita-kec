<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Actions\Home\GetHeroNewsSectionAction;
use App\Actions\Home\GetPopularNewsAction;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $sort = $request->get('sort', 'latest');

        // Hero section (pinned + 3 latest)
        ['pinnedNews' => $pinnedNews, 'latestNews' => $latestNews] = (new GetHeroNewsSectionAction)->handle();

        // Popular this week
        $popularNews = (new GetPopularNewsAction)->handle();

        // Semua kategori untuk filter
        $categories = Category::withCount('news')
            ->having('news_count', '>=', 1)
            ->orderByDesc('news_count')
            ->get();

        // List berita utama dengan filter & sort
        $news = News::query()
            ->whereNotNull('current_version_id')
            ->with(['currentVersion', 'categories'])
            ->when(
                $category,
                fn($q) =>
                $q->whereHas(
                    'categories',
                    fn($q) =>
                    $q->where('slug', $category)
                )
            )
            ->when(
                $sort === 'popular',
                fn($q) =>
                $q->orderByRaw('(views * 1 + likes * 3) DESC')
            )
            ->when(
                $sort === 'latest' || !$sort,
                fn($q) =>
                $q->orderByDesc('created_at')
            )
            ->paginate(9)
            ->withQueryString();

        return view('news.index', compact(
            'pinnedNews',
            'latestNews',
            'popularNews',
            'categories',
            'news',
            'category',
            'sort'
        ));
    }

    public function show(News $news)
    {
        $news->load(['currentVersion', 'categories', 'author']);

        // saat halaman diakses hitung views — exclude admin
        $this->recordView($news);

        $categoryIds = $news->categories->pluck('id');

        $trendingNews = News::whereHas('currentVersion', function ($q) {
            $q->whereNotNull('published_at');
        })
            ->where('created_at', '>=', now()->subDays(7))
            ->with('currentVersion')
            ->orderByRaw('views / (TIMESTAMPDIFF(HOUR, news.created_at, NOW()) + 1) DESC')
            ->take(5)
            ->get();

        $mostUsedCategories = Category::withCount('news')
            ->having('news_count', '>=', 1)
            ->orderByDesc('news_count')
            ->take(5)
            ->get();

        $baseQuery = News::whereHas('categories', fn($q) =>
            $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $news->id)
            ->whereNotNull('current_version_id')
            ->latest();

        $relatedNews = (clone $baseQuery)->with('currentVersion')->take(5)->get();
        $totalRelated = (clone $baseQuery)->count();
        $allRelatedNews = $totalRelated >= 5
            ? (clone $baseQuery)->with('currentVersion')->get()
            : collect();

        return view('news.show', compact('news', 'relatedNews', 'mostUsedCategories', 'trendingNews', 'allRelatedNews'));
    }


    // Method untuk hitung views
    private function recordView(News $news): void
    {
        // Exclude admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            return;
        }

        // Buat cache key unik per berita + user/IP
        // Jika login → pakai user ID, jika guest → pakai IP
        $identifier = Auth::check()
            ? 'user_' . Auth::id()
            : 'ip_' . request()->ip();

        $cacheKey = "news_view_{$news->id}_{$identifier}";

        // Cek apakah sudah dihitung dalam 30 menit terakhir
        if (Cache::has($cacheKey)) {
            return;
        }

        // Increment views
        $news->increment('views');

        // Tandai sudah dihitung, berlaku 30 menit
        Cache::put($cacheKey, true, now()->addMinutes(30));
    }

    // Method like
    public function like(News $news): JsonResponse
    {
        // Exclude admin — return error message
        if (Auth::user()->role === 'admin') {
            return response()->json([
                'message' => 'Admin tidak dapat memberikan like.'
            ], 403);
        }

        $cacheKey = "news_like_{$news->id}_user_" . Auth::id();
        $liked = Cache::get($cacheKey, false);

        if ($liked) {
            $news->decrement('likes');
            Cache::forget($cacheKey);
            $liked = false;
        } else {
            $news->increment('likes');
            Cache::put($cacheKey, true, now()->addDays(1));
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes' => $news->fresh()->likes,
        ]);
    }

    // Method search
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));

        $results = collect();
        $total = 0;

        if ($query !== '') {
            $results = News::whereHas('currentVersion', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");
            })
                ->whereNotNull('current_version_id')
                ->with(['currentVersion', 'categories'])
                ->latest()
                ->paginate(12)
                ->withQueryString();

            $total = $results->total();
        }

        return view('news.search', compact('results', 'query', 'total'));
    }


    // Manage Berita User //    
    public function userNews(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        // Batasi status berita sesuai role nya
        $allowedStatuses = $isAdmin
            ? ['published', 'draft']
            : ['published', 'review', 'need_revision', 'rejected'];

        // Redirect ke url bersih jika ada yang mencoba mengakses status diluar rolenya
        $status = $request->get('status');
        if ($status && !in_array($status, $allowedStatuses)) {
            return redirect()->route('user.news');
        }

        // Pagination per page
        $perPageRaw = $request->get('per_page', 9);
        $allowedPerPage = [9, 18, 27, 50, 'all'];

        if (!in_array($perPageRaw, $allowedPerPage, strict: true) &&
            !in_array((int) $perPageRaw, $allowedPerPage, strict: true)) { $perPageRaw = 9; }

        $showAll = $perPageRaw === 'all';
        $perPage = $showAll ? PHP_INT_MAX : (int) $perPageRaw;
        

        // Query utama
        $query = News::where('author_id', $user->id)
            ->with([
                'currentVersion',
                'categories',
            ])
            ->latest();

        // Filter by status dengan query
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereIn('status', $allowedStatuses);
        }

        $news = $query->paginate($perPage)->withQueryString();

        // Counter untuk tabs
        $statusCounts = News::where('author_id', $user->id)
            ->whereIn('status', $allowedStatuses)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [
            'all' => $statusCounts->sum(),
            'published' => $statusCounts->get('published', 0),
            'review' => $statusCounts->get('review', 0),
            'need_revision' => $statusCounts->get('need_revision', 0),
            'rejected' => $statusCounts->get('rejected', 0),
            'draft' => $statusCounts->get('draft', 0),
        ];

        // Definisikan tabs
        $tabs = [['key' => 'all', 'label' => 'Semua', 'count' => $counts['all']]];

        if ($isAdmin) {
            $tabs[] = ['key' => 'published', 'label' => 'Diterbitkan', 'count' => $counts['published']];
            $tabs[] = ['key' => 'draft', 'label' => 'Draft', 'count' => $counts['draft']];
        } else {
            $tabs[] = ['key' => 'published', 'label' => 'Diterbitkan', 'count' => $counts['published']];
            $tabs[] = ['key' => 'review', 'label' => 'Sedang Diproses', 'count' => $counts['review']];
            $tabs[] = ['key' => 'need_revision', 'label' => 'Review Ulang', 'count' => $counts['need_revision']];
            $tabs[] = ['key' => 'rejected', 'label' => 'Ditolak', 'count' => $counts['rejected']];
        }

        return view('users.dashboard.index', compact('news', 'counts', 'tabs', 'status', 'perPage', 'perPageRaw'));
    }

    // Halaman upload berita
    public function create()
    {
        return view('users.dashboard.upload.create');
    }
}
