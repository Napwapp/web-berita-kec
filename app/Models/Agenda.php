<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Agenda extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'id',
        'category_id',
        'title',
        'description',
        'slug',
        'is_all_day',
        'start_at',
        'end_at',
        'is_online',
        'online_link',
        'location',
        'is_published',
        'status',
        'published_at',
    ];

    // Casts untuk memastikan tipe data yang benar saat mengakses atribut model
    protected $casts = [
        'is_all_day' => 'boolean',
        'is_online' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        /**
         * Setiap kali model disimpan (create atau update), sinkronkan
         * kolom status dan published_at berdasarkan nilai is_published.
         *
         * Keuntungan pendekatan ini:
         *  - Form hanya perlu peduli pada satu toggle: is_published.
         *  - status & published_at selalu konsisten, tidak perlu logika
         *    di Pages atau Resource.
         *  - Bisa dipanggil dari mana saja (seeder, tinker, API) dan
         *    hasilnya tetap benar.
         */
        static::saving(function (self $agenda) {
            // Publish jika is_published = true, tapi jangan ubah published_at jika sudah pernah diterbitkan.
            if ($agenda->is_published) {
                $agenda->status = 'published';
                $agenda->published_at ??= now();
            }
            // Untuk unpublish, tapi jika sudah archived jangan ubah statusnya, biarkan tetap archived.
            else {
                if ($agenda->status !== 'archived') {
                    $agenda->status = 'draft';
                    $agenda->published_at = null;
                }
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(CategoryAgenda::class);
    }

    // Method Publish
    public function publish(): static
    {
        if ($this->trashed()) {
            throw new \Exception('Agenda ini telah dihapus sebelumnya. Tidak dapat menerbitkan agenda yang sudah dihapus. Pulihkan datanya terlebih dahulu');
        }

        $this->update([
            'is_published' => true,
            'status' => 'published',
            'published_at' => $this->published_at ?? now(),
        ]);

        return $this;
    }

    // Method Unpublish
    public function unpublish(): static
    {
        if ($this->trashed()) {
            throw new \Exception('Agenda ini telah dihapus sebelumnya, tidak dapat mencabut status publikasi agenda ini. Pulihkan datanya terlebih dahulu');
        }

        $this->update([
            'is_published' => false,
            'status' => 'draft',
            'published_at' => null,
        ]);

        return $this;
    }

    // Method untuk archive saat Softdelete
    public function archive(): static
    {
        if ($this->trashed()) {
            throw new \Exception('Agenda ini telah dihapus sebelumnya, tidak bisa diarsipkan. Pulihkan datanya terlebih dahulu');
        }

        $this->update([
            'is_published' => false,
            'status' => 'archived',
        ]);

        return $this;
    }

    // Method untuk unarchive
    public function unarchive(): static
    {
        if ($this->trashed()) {
            throw new \Exception('Agenda ini telah dihapus sebelumnya, tidak dapat melepas arsip agenda ini. Pulihkan datanya terlebih dahulu');
        }

        $this->update([
            'is_published' => true,
            'status' => 'published',
        ]);

        return $this;
    }

    // Cek status Publish (untuk instance model)
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    // Scope untuk query builder - hanya published agenda
    public function scopeIsPublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // Untuk membantu toggle status publish/unpublish di form nya
    public function setPublishedState(bool $shouldPublish): static
    {
        return $shouldPublish ? $this->publish() : $this->unpublish();
    }


    // Ongoing agenda
    public function isOngoing(): bool
    {
        return $this->start_at <= now() && $this->end_at >= now();
    }

    // Upcoming agenda (Agenda yang akan datang)
    public function isUpcoming(): bool
    {
        return !$this->isOngoing() && !$this->isPast();
    }

    // Past agenda (Agenda yang sudah berlalu)
    public function isPast(): bool
    {
        return $this->end_at < now();
    }

}
