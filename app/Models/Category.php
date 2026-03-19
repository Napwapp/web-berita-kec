<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();

        // Create slug otomatis saat create data
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        // Update slug otomatis saat update data
        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        // Saat kategori dihapus, hapus juga pivot table nya
        static::deleting(function ($category) {
            $category->news()->detach(); // hapus dari category_news
        });
    }
}