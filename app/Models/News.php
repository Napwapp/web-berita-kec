<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'author_id',
        'slug',
        'status',
        'current_version_id',
        'likes',
        'views',
        'type',
        'pinned_at',
        'pin_expired_at',
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
        return $this->belongsToMany(Category::class, 'category_news');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Method untuk pin berita
    public function pin(?int $durationDays = null): void
    {
        $this->pinned_at = now();
        $this->pin_expired_at = $durationDays ? now()->addDays($durationDays) : null;
        $this->save();
    }

    // Untuk unpin
    public function unpin(): void
    {
        $this->pinned_at = null;
        $this->pin_expired_at = null;
        $this->save();
    }

    

    // Method untuk memeriksa apakah berita dipin
    public function isPinned(): bool
    {
        if (!$this->pinned_at)
            return false;
        // Jika pin_expired_at null, berarti pin tidak memiliki batas waktu, jadi tetap dipin.
        if (!$this->pin_expired_at)
            return true;

        // Jika pin_expired_at tidak null, periksa apakah tanggal saat ini masih dalam masa pin.
        return now()->lessThanOrEqualTo($this->pin_expired_at);
    }
}
