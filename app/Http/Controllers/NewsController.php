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
            ->paginate(10)
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
}
