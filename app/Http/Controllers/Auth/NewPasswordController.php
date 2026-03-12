<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        $token = $request->route('token');
        $email = $request->query('email');

        // Cek apakah email ada di query string
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Token dan link reset password tidak valid atau sudah kadaluarsa. Silahkan masukan email anda kembali untuk mendapatkan link reset password.']);
        }

        // Cari record berdasarkan email
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        // Cek apakah record ada dan token cocok
        if (!$record || !Hash::check($token, $record->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Link reset password tidak valid atau sudah kadaluarsa. Silakan minta link baru.']);
        }

        // Cek apakah token sudah expired (60 menit)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Token untuk link reset password sudah kadaluarsa. Silakan masukkan email anda kembali untuk mendapatkan token link reset password baru.']);
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input dari form reset password
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ],
        [
            'email.required' => 'Email tidak boleh kosong. Email diperlukan untuk mereset password.',
            'email.email' => 'Format email tidak valid. Contoh: user@domain.com',
            'password.required' => 'Password tidak boleh kosong. Password baru diperlukan untuk mereset password.',
            'password.confirmed' => 'Konfirmasi password tidak cocok. Pastikan untuk memasukkan password yang sama di kedua kolom.',
        ]
        );

        // Pesan eror saat submit reset password
        $errorMessages = [
            Password::INVALID_TOKEN => 'Link reset kata sandi tidak valid atau sudah digunakan. Silakan Minta kembali dihalaman sebelumnya.',
            Password::INVALID_USER => 'Tidak dapat menemukan email. Pastikan untuk memasukkan email yang benar dan sudah terdaftar.',
            Password::RESET_THROTTLED => 'Terlalu banyak percobaan. Silakan tunggu beberapa saat sebelum mencoba lagi.',
        ];

        // Reset password pengguna
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Hapus token reset password setelah berhasil mereset password
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Kata sandi berhasil direset. Silakan masuk dengan kata sandi baru Anda.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => $errorMessages[$status] ?? 'Terjadi kesalahan. Silakan coba lagi.']);
    }
}
