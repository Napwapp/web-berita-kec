<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Category;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function show(News $news)
    {
        $news->load(['currentVersion', 'categories', 'author']);

        $categoryIds = $news->categories->pluck('id');

        // Trending News: berita dengan jumlah view terbanyak dalam 7 hari terakhir
        $trendingNews = News::whereHas('currentVersion', function ($q) {
            $q->whereNotNull('published_at'); // atau status published
        })
            ->where('created_at', '>=', now()->subDays(7))
            ->with('currentVersion')
            ->orderByRaw('views / (TIMESTAMPDIFF(HOUR, news.created_at, NOW()) + 1) DESC')
            ->take(5)
            ->get();

        // Most used categories
        $mostUsedCategories = Category::withCount('news')
            ->having('news_count', '>=', 1)
            ->orderByDesc('news_count')
            ->take(5)
            ->get();

        // Base query untuk related news
        $baseQuery = News::whereHas('categories', fn($q) =>
            $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $news->id)
            ->whereNotNull('current_version_id')
            ->latest();

        // Ambil 5 data
        $relatedNews = (clone $baseQuery)
            ->with('currentVersion')
            ->take(5)
            ->get();

        // Hitung total
        $totalRelated = (clone $baseQuery)->count();

        // Ambil semua kalau cukup
        $allRelatedNews = $totalRelated >= 5
            ? (clone $baseQuery)->with('currentVersion')->get()
            : collect();


        return view('news.show', compact('news', 'relatedNews', 'mostUsedCategories', 'trendingNews', 'allRelatedNews'));
    }
}
