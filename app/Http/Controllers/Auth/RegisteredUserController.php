<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => ['required', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'profile_picture' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Format email tidak valid. Harus berformat email seperti example@domain.com',
                'email.max' => 'Email tidak boleh lebih dari 255 karakter',
                'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain',
                'password.required' => 'Password tidak boleh kosong',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
                'password.min' => 'Password harus minimal 8 karakter',
                'profile_picture.image' => 'File harus berupa gambar yang valid',
                'profile_picture.mimes' => 'Format gambar harus jpg/jpeg/png/webp',
            ]
        );

        // Simpan profile picture jika ada
        $profilePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('images/profile-pictures', 'public');
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile_picture' => $profilePath,
        ]);

        // fire registered event (optional)
        event(new Registered($user));

        // Panggil Method terpisah untuk generate OTP & kirim email
        $otpService = app(\App\Services\OtpService::class);
        $otpService->generateOtp($user);

        session(['otp_user_id' => $user->id]);

        return redirect()
            ->route('verification.otp')
            ->with(
                'success',
                'Pendaftaran berhasil! Kami telah mengirimkan kode verifikasi ke email Anda. Silakan periksa email anda.'
            );
    }
}
