<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;


class Notification extends Model
{
    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'sender_id',
        'news_id',
        'title',
        'message',
        'url',
        'type',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    // Polymorphic relationship ke model yang menerima notifikasi (biasanya User)
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Relasi ke User sebagai pengirim notifikasi
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relasi ke News jika notifikasi terkait dengan berita tertentu
    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    // Akses content berita
    public function contents(): HasMany
    {
        return $this->hasMany(NewsContent::class);
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    // Method untuk menandai notifikasi sebagai sudah dibaca
    public function markAsRead(): static
    {
        if (!$this->is_read) {
            $this->update(['read_at' => now()]);
        }

        return $this;
    }

    // Scope untuk filter notifikasi yang belum dibaca
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Filter berdasarkan tipe.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public static function send(
        Model $notifiable,
        string $type,
        string $title,
        string $message,
        ?News $news = null,
        ?User $sender = null,
        ?string $url = null,
    ): static {
        return static::create([
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'sender_id' => $sender?->id,
            'news_id' => $news?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            // URL di-snapshot saat notifikasi dibuat agar tidak rusak
            // walau slug berita berubah di kemudian hari
            'url' => $url ?? ($news ? url('/berita/' . $news->slug) : null),
        ]);
    }
}
