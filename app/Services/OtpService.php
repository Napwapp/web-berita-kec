<?php
namespace App\Services;

use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generateOtp(User $user)
    {
        // Hapus OTP lama jika ada
        Otp::where('user_id', $user->id)->delete();

        // Generate OTP baru dengan expired nya
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiredAt = now()->addMinutes(10);

        // Simpan OTP ke database
        Otp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expired_at' => $expiredAt,
        ]);

        // Send otp
        try {
            Mail::to($user->email)->send(
                new OtpMail($otp, $expiredAt)
            );
        } catch (\Exception $e) {
            report($e);
        }
    }

    // Pengecekan otp valid & expired
    public function verifyOtp(User $user, string $otp): array
    {
        $otpRecord = Otp::where('user_id', $user->id)
            ->where('otp', $otp)
            ->first();

        if (!$otpRecord) {
            return [
                'status' => false,
                'message' => 'Kode OTP yang Anda masukkan tidak valid.'
            ];
        }

        if (now()->greaterThan($otpRecord->expired_at)) {
            return [
                'status' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.'
            ];
        }

        return [
            'status' => true,
            'otp' => $otpRecord
        ];
    }

    // Update user sebagai verified dan hapus otp
    public function markVerified(User $user, Otp $otp): void
    {
        $user->update([
            'email_verified_at' => now()
        ]);

        $otp->delete();
    }
}
?>