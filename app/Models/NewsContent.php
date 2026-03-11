<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsContent extends Model
{
    protected $fillable = [
        'news_id',
        'title',
        'subtitle',
        'slug',
        'thumbnail',
        'thumbnail_description',
        'excerpt',
        'content',
        'version',
        'is_published',
        'published_at'
    ];

    // Satu konten berita (NewsContent) dimiliki oleh satu berita (News).
    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

}