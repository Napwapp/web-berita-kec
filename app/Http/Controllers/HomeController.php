<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\News;
use App\Actions\Home\GetHeroNewsSectionAction;
use App\Actions\Home\GetPopularNewsAction;

class HomeController extends Controller
{
    public function index( GetHeroNewsSectionAction $heroNewsSection, GetPopularNewsAction $popularNews, ) 
    {
        $categories = Category::all();        
        $popularNews = $popularNews->handle();

        // Ambil data untuk hero section dari file action terpisah
        [
            'pinnedNews' => $pinnedNews,
            'latestNews' => $latestNews,
        ] = $heroNewsSection->handle();

        return view('home', compact(
            'categories',
            'pinnedNews',
            'latestNews',
            'popularNews',
        ));
    }
}

