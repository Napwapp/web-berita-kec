<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    // Kategori dapat memiliki banyak berita dan berita dapat memiliki banyak kategori.
    public function news()
    {
        return $this->belongsToMany(News::class, 'category_news');
    }
}
