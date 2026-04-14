<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\News;

class CategoryNewsController extends Controller
{
    public function show(string $slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Semua kategori untuk navigasi filter
        $categories = Category::withCount('news')
            ->having('news_count', '>=', 1)
            ->orderByDesc('news_count')
            ->get();

        // Featured: top 1 berita terpopuler di kategori pada minggu ini
        $featuredNews = News::query()
            ->whereNotNull('current_version_id')
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->with(['currentVersion', 'categories', 'author'])
            ->whereBetween('news.created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('news.*, (views * 1 + likes * 3) AS popularity_score')
            ->orderByDesc('popularity_score')
            ->first();

        // Fallback: jika minggu ini tidak ada, ambil yang terpopuler sepanjang waktu
        if (!$featuredNews) {
            $featuredNews = News::query()
                ->whereNotNull('current_version_id')
                ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
                ->with(['currentVersion', 'categories', 'author'])
                ->selectRaw('news.*, (views * 1 + likes * 3) AS popularity_score')
                ->orderByDesc('popularity_score')
                ->first();
        }

        $sort = $request->get('sort', 'latest');

        // List berita
        $news = News::query()
            ->whereNotNull('current_version_id')
            ->whereHas('categories', fn($q) => $q->where('categories.id', $category->id))
            ->with(['currentVersion', 'categories'])
            ->when($featuredNews, fn($q) => $q->where('id', '!=', $featuredNews->id))
            ->when($sort === 'popular', fn($q) =>
                $q->orderByRaw('(views * 1 + likes * 3) DESC')
            )
            ->when($sort !== 'popular', fn($q) =>
                $q->orderByDesc('created_at')
            )
            ->paginate(9)
            ->withQueryString();

        return view('news.category.show', compact(
            'category', 'categories', 'featuredNews', 'news', 'sort'
        ));
    }
}
