<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'email' => ['required', 'email'],
            ],
            [
                'email.required' => 'Email tidak boleh kosong. Email diperlukan untuk mengirim link reset password.',
                'email.email' => 'Format email tidak valid. Contoh: user@domain.com',
            ]
        );

        // Kirim link reset password ke email pengguna
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset kata sandi telah dikirim ke email Anda. Silakan cek kotak masuk atau folder spam di email Anda.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Tidak dapat menemukan email. Pastikan untuk memasukkan email yang benar dan sudah terdaftar.']);
    }
}
