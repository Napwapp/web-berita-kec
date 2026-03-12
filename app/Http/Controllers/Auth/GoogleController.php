<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    // redirect ke Google OAuth Page
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // callback dari Google OAuth setelah user berhasil login dengan Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            // Cari atau buat pengguna berdasarkan email Google
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'profile_picture' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Jika pengguna sudah ada, perbarui informasi Google ID dan avataram
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'profile_picture' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }

            // Login pengguna
            Auth::login($user);

            // Redirect role admin
            if ($user->role === 'admin') {
                return redirect('/admin')
                    ->with('success', 'Selamat! Anda berhasil login sebagai admin.');
            }

            return redirect()->intended('/')->with('success', 'Selamat! Anda berhasil login.');
        } catch (Exception $e) {
            return redirect('/login')->withErrors('Login dengan Google gagal. Silakan coba lagi.');
        }
    }
}
