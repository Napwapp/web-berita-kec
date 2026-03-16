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

    /**
     * Helper untuk mempublikasikan berita ini, mengupdate is_published, published_at, dan current_version_id di News
     * @return $this
     */
    public function publish(): self
    {
        // Update is_published dan published_at ketika publish
        if (!$this->is_published) {
            $this->update([
                'is_published' => true,
                'published_at' => now(),
            ]);
        }

        // ensure the associated news points at this version
        if ($this->news && $this->news->current_version_id !== $this->id) {
            $this->news->update(['current_version_id' => $this->id]);
        }

        // Hapus versi lama yang memiliki news_id sama dan versi < versi yang baru dipublish
        $this->news->contents()->where('version', '<', $this->version)->delete();

        return $this;
    }

}