<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\Notifications\NewsNotificationService;
use Illuminate\Support\Facades\Auth;

class NewsContent extends Model
{
    protected $fillable = [
        'news_id',
        'title',
        'thumbnail',
        'thumbnail_description',
        'excerpt',
        'content',
        'version',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Satu konten berita (NewsContent) dimiliki oleh satu berita (News).
    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    // Method untuk accept berita yang diupload user
    public function accept(): self
    {
        $isRevision = $this->news?->status === 'need_revision';

        $this->publish();

        if ($isRevision) {
            NewsNotificationService::updated(
                content: $this,
                sender: Auth::user(),
            );
        } else {
            NewsNotificationService::accepted(
                content: $this,
                sender: Auth::user(),
            );
        }

        return $this;
    }


    // Method untuk reject berita yang diupload user
    public function reject(string $reason): self
    {
        $isRevision = $this->news?->status === 'need_revision';

        // ── Update NewsContent ────────────────────────────────────────────
        $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        // ── Update News ───────────────────────────────────────────────────
        if ($this->news) {
            $updateData = ['status' => 'rejected'];

            // Jika versi yang direject adalah current_version, lepas referensinya
            if ($this->news->current_version_id === $this->id) {
                $updateData['current_version_id'] = null;
            }

            $this->news->update($updateData);
        }

        // ── Kirim notifikasi ──────────────────────────────────────────────
        // Pilih format pesan berdasarkan apakah ini reject review atau reject revisi
        if ($isRevision) {
            NewsNotificationService::revisionRejected(
                content: $this,
                reason: $reason,
                sender: Auth::user(),
            );
        } else {
            NewsNotificationService::rejected(
                content: $this,
                reason: $reason,
                sender: Auth::user(),
            );
        }

        return $this;
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

        // Memastikan bahwa versi yang dipublish adalah versi terbaru, jika tidak hapus versi lama
        if ($this->news && $this->news->current_version_id !== $this->id) {
            $this->news->update([
                'current_version_id' => $this->id,
                'status' => 'published',
            ]);
        }

        $this->news->update([
            'status' => 'published',
        ]);

        // Hapus versi lama yang memiliki news_id sama dan versi < versi yang baru dipublish
        $this->news->contents()->where('version', '<', $this->version)->delete();

        return $this;
    }
}