<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    public function index()
    {
        return view('auth.verify-otp');
    }

    public function verify(Request $request, OtpService $otpService)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        // Ambil user id dari session yang disimpan saat registrasi
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('verification.otp')->with('error', 'Sesi verifikasi tidak ditemukan. Silakan ulangi pendaftaran.');
        }

        // Cari user berdasarkan session
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('verification.otp')->with('error', 'Pengguna tidak ditemukan. Mungkin anda belum mendaftarkan akun anda');
        }

        // Panggil method verifikasi OTP
        $result = $otpService->verifyOtp($user, $request->otp);
        if (!$result['status']) {
            return redirect()->route('verification.otp')->with('error', $result['message']);
        }

        // Update status verified
        /** @var \App\Models\Otp $otpRecord */
        $otpRecord = $result['otp'];
        $otpService->markVerified($user, $otpRecord);

        // Hapus session
        $request->session()->forget('otp_user_id');

        return redirect()->route('login')->with('success', 'Selamat verifikasi berhasil! Silahkan Masuk ke Akun yang telah didaftarkan.');
    }

    public function resend(Request $request, OtpService $otpService)
    {
        // Ambil user id dari session yang disimpan saat registrasi
        $userId = session('otp_user_id');
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'Sesi verifikasi tidak ditemukan'], 422);
        }

        // Cari user berdasarkan session
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Pengguna tidak ditemukan'], 404);
        }

        // Panggil method generate OTP untuk kirim ulang
        $otpService->generateOtp($user);

        return response()->json(['status' => true, 'message' => 'OTP telah dikirim ulang ke email Anda.']);
    }
}
