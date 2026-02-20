<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $expiredAt;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, $expiredAt = null)
    {
        $this->otp = $otp;
        $this->expiredAt = $expiredAt;
    }

    /**
     * Build the message.
     */
    public function build(): OtpMail
    {
        return $this->subject('Kode Verifikasi OTP Anda')
            ->view('emails.otp');
    }
}
