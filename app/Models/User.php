<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ResetPasswordNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable implements FilamentUser
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

    // Override method untuk mengirim notifikasi reset password ke email dengan custom format
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // Menentukan apakah pengguna dapat mengakses panel admin
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    // Notifications

    // Semua Notifikasi milik user ini
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->latest();
    }

    // Notifikasi yang belum dibaca.
    public function unreadNotifications(): MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable')
            ->whereNull('read_at')
            ->latest();
    }

    //  Jumlah notifikasi belum dibaca — untuk badge di navbar. Gunakan: $user->unread_notifications_count
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->unreadNotifications()->count();
    }

}
