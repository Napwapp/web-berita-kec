<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryNewsController extends Controller
{
    public function show($slug)
    {
        // Ambil data berita berdasarkan kategori di slug
        $category = Category::where('slug', $slug)->firstOrFail();
        $news = $category->news()->paginate(10); 

        return view('news.category.show', compact('category', 'news'));
    }   
}
