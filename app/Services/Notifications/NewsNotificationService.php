<?php
namespace App\Services\Notifications;

use App\Models\News;
use App\Models\NewsContent;
use App\Models\Notification;
use App\Models\User;

class NewsNotificationService
{
    // Notifikasi untuk berita yang diterima
    public static function accepted(
        NewsContent $content,
        User $sender,
    ): void {
        $news = $content->news;
        $title = $content->title;

        Notification::send(
            notifiable: $news->author,
            type: 'news_accepted',
            title: 'Berita Diterima',
            message: "Selamat! Berita yang anda tulis dengan judul \"{$title}\" telah disetujui "
            . "dan kini sudah ditayangkan ke publik.",
            news: $news,
            sender: $sender,
        );
    }

    // Notifikasi untuk berita yang ditolak
    public static function rejected(
        NewsContent $content,
        string $reason,
        User $sender,
    ): void {
        $news = $content->news;
        $title = $content->title;

        Notification::send(
            notifiable: $news->author,
            type: 'news_rejected',
            title: 'Berita Ditolak',
            message: "Mohon maaf, setelah kami periksa, berita dengan judul \"{$title}\" " . "kami tolak untuk dipublikasikan dengan alasan: {$reason}",
            news: $news,
            sender: $sender,
        );
    }


    // Notifikasi untuk berita yang diperbarui (revisi diterima)
    public static function updated(
        NewsContent $content,
        User $sender,
    ): void {
        $news = $content->news;
        $title = $content->title;

        Notification::send(
            notifiable: $news->author,
            type: 'news_accepted',
            title: 'Berita Diperbarui',
            message: "Selamat! Permintaan revisi pada berita yang anda tulis dengan judul \"{$title}\" versi {$content->version} telah diterima. Berita tersebut telah diperbarui dan dipublikasikan kembali.",
            news: $news,
            sender: $sender,
        );
    }

    // Revisi ditolak
    public static function revisionRejected(
        NewsContent $content,
        string $reason,
        User $sender,
    ): void {
        $news = $content->news;
        $title = $content->title;

        Notification::send(
            notifiable: $news->author,
            type: 'news_rejected',
            title: 'Permintaan Revisi Berita Ditolak',
            message: "Mohon maaf! setelah kami periksa, permintaan revisi konten berita "
            . "dengan judul \"{$title}\" versi {$content->version} kami tolak "
            . "untuk dipublikasikan dengan alasan: {$reason}. "
            . "Versi berita sebelumnya yang telah terpublikasi tetap terpublikasi!",
            news: $news,
            sender: $sender,
        );
    }

    // Notifikasi ke admin saat ada berita masuk untuk direview

    // Berita baru yang butuh review
    public static function submittedForReview(
        NewsContent $content,
        iterable $admins,
    ): void {
        $news = $content->news;
        $title = $content->title;

        foreach ($admins as $admin) {
            Notification::send(
                notifiable: $admin,
                type: 'news_review',
                title: 'Berita Baru Menunggu Review',
                message: "Berita baru dengan judul \"{$title}\" dari "
                . "{$news->author->name} menunggu persetujuan Anda.",
                news: $news,
                sender: $news->author,
                url: url('/admin/news-contents/' . $content->id),
            );
        }
    }

    // Berita revisi yang butuh review
    public static function submittedForRevisionReview(
        NewsContent $content,
        iterable $admins,
    ): void {
        $news = $content->news;
        $title = $content->title;

        foreach ($admins as $admin) {
            Notification::send(
                notifiable: $admin,
                type: 'news_revised',
                title: 'Revisi Berita Menunggu Review',
                message: "Revisi berita versi {$content->version} dengan judul \"{$title}\" "
                . "dari {$news->author->name} menunggu persetujuan Anda.",
                news: $news,
                sender: $news->author,
                url: url('/admin/news-contents/' . $content->id),
            );
        }
    }
}