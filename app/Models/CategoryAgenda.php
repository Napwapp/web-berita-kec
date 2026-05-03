<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryAgenda extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color'
    ];

    public function agendas()
    {
        return $this->hasMany(Agenda::class, 'category_id');
    }
}
