<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'author_id',
        'current_version_id',
        'likes',
        'views'
    ];

    // Satu berita dimiliki oleh satu pengguna (author).
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Satu berita dapat memiliki banyak entri konten/versi (mis. untuk menyimpan riwayat revisi).
    public function contents()
    {
        return $this->hasMany(NewsContent::class);
    }

    // Menunjuk pada versi konten saat ini untuk berita ini (satu NewsContent).
    public function currentVersion()
    {
        return $this->belongsTo(NewsContent::class, 'current_version_id');
    }

    // Berita dapat memiliki banyak kategori dan kategori dapat memiliki banyak berita.
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
