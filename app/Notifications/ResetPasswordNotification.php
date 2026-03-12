<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    // Token untuk digunakan ke pesan email yang akan dikirim ke pengguna
    public $token;

    // Membuat instance baru dari notifikasi reset password dengan token yang diberikan
    public function __construct($token)
    {
        $this->token = $token;
    }

    // Menentukan saluran notifikasi yang akan digunakan, dalam hal ini adalah email
    public function via($notifiable)
    {
        // Mengembalikan array dengan 'mail' untuk menunjukkan bahwa notifikasi ini akan dikirim melalui email
        return ['mail'];
    }

    // Method untuk membuat pesan email yang akan dikirimkan ke email pengguna
    public function toMail($notifiable)
    {
        // Url untuk link reset password, menggabungkan token dan email pengguna sebagai parameter
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Membuat pesan email
        return (new MailMessage)
            ->subject('Permintaan Reset Kata Sandi')
            ->greeting('Hallo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mereset kata sandi akun Anda, dengan email ' . $notifiable->getEmailForPasswordReset() . ' dari ' . config('app.name') . '.')
            ->action('Klik di sini untuk mereset Kata Sandi!', $url)
            ->line('Tautan ini hanya berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini. Kata sandi Anda tidak akan berubah.')
            ->salutation('Terima kasih!, dari ' . config('app.name'));
    }
}
?>