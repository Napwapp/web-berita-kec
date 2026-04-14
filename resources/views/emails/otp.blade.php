<div class="text-center" style="font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #111827;">
    <h2>Kode Verifikasi OTP Anda</h2>
    <p>Gunakan kode berikut untuk memverifikasi alamat email Anda:</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 2px;">{{ $otp }}</p>

    @if(!empty($expiredAt))
        <p>Kode ini berlaku sampai {{ $expiredAt->toDayDateTimeString() }}.</p>
    @else
        <p>Kode berlaku selama 10 menit.</p>
    @endif

    <p>Jika Anda tidak membuat akun ini, abaikan email ini.</p>
</div>
