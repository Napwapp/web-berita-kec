<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
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
        'status',
        'published_at',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryAgenda::class);
    }
}
