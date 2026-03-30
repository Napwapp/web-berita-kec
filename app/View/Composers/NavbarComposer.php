<?php

namespace App\View\Composers;

use App\Models\Category;
use Illuminate\View\View;

class NavbarComposer
{
    // Method untuk mengirim data kategori ke view navbar
    public function compose(View $view)
    {
        $view->with([
            'categories' => Category::orderBy('name')->get(),
            'latestCategories' => Category::latest()->take(3)->get(),
        ]);
    }
}