<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\News;
use App\Actions\Home\GetHeroNewsSectionAction;
use App\Actions\Home\GetPopularNewsAction;

class HomeController extends Controller
{
    public function index(GetHeroNewsSectionAction $heroNewsSection, GetPopularNewsAction $popularNews, )
    {
        $categories = Category::all();
        $popularNews = $popularNews->handle();

        // Untuk section berita terbaru
        $moreLatestNews = News::query()
            ->whereNotNull('current_version_id')
            ->with(['currentVersion', 'categories'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Untuk section Featured Categories
        $featuredCategories = Category::where('is_featured', true)
            ->with([
                'news' => function ($query) {
                    $query->whereNotNull('current_version_id')
                        ->with('currentVersion')
                        ->latest()
                        ->limit(5);
                }
            ])
            ->get();

        // Ambil data untuk hero section dari file action terpisah
        [
            'pinnedNews' => $pinnedNews,
            'latestNews' => $latestNews,
        ] = $heroNewsSection->handle();

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

