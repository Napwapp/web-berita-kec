<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\News;
use App\Actions\Home\GetHeroNewsSectionAction;
use App\Actions\Home\GetPopularNewsAction;

class HomeController extends Controller
{
    public function index(GetHeroNewsSectionAction $heroNewsSection, GetPopularNewsAction $popularNews, Request $request)
    {
        $type = $request->type;
        $categories = Category::all();
        $popularNews = $popularNews->handle();

        // Untuk section berita terbaru
        $moreLatestNews = News::query()
            ->whereNotNull('current_version_id')
            ->when($type, fn($q) => $q->where('type', $type))
            ->with(['currentVersion', 'categories'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $featuredCategories = Category::where('is_featured', true)
            ->withCount([
                'news' => function ($query) use ($type) {
                    $query->whereNotNull('current_version_id')
                        ->when($type, fn($q) => $q->where('type', $type));
                }
            ])
            ->with([
                'news' => function ($query) use ($type) {
                    $query->whereNotNull('current_version_id')
                        ->when($type, fn($q) => $q->where('type', $type))
                        ->with('currentVersion')
                        ->latest()
                        ->limit(2);
                }
            ])
            ->orderByDesc('news_count')
            ->get();

        // Ambil data untuk hero section dari file action terpisah
        [
            'pinnedNews' => $pinnedNews,
            'latestNews' => $latestNews,
        ] = $heroNewsSection->handle($type);

        return view('home', compact(
            'categories',
            'pinnedNews',
            'latestNews',
            'moreLatestNews',
            'popularNews',
            'featuredCategories',
        ));
    }
}

