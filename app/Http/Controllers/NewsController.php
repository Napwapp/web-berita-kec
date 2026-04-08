<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends Controller
{
    public function show(News $news)
    {
        return view('news.show', compact('news'));
    }
}
