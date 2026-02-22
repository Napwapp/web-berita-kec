<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Accessor untuk mendapatkan URL foto profil, baik dari Google atau lokal
    public function getProfilePhotoAttribute()
    {
        if (!$this->profile_picture) {
            // Tidak ada foto sama sekali, pakai default
            return Storage::url('images/profile-pictures/default-profile.webp');
        }

        // Cek apakah URL eksternal (dari Google) atau path lokal
        if (str_starts_with($this->profile_picture, 'http')) {
            return $this->profile_picture;
        }

        return Storage::url($this->profile_picture);
    }

    // Satu pengguna (author) dapat memiliki banyak berita.
    public function news()
    {
        return $this->hasMany(News::class, 'author_id');
    }
}