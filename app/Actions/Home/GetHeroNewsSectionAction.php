<?php

namespace App\Actions\Home;

use App\Models\News;
use Illuminate\Support\Collection;

class GetHeroNewsSectionAction
{
    /**
     * Mengambil data untuk hero section halaman beranda:
     * - 1 berita yang sedang di-pin (berita utama)
     * - 3 berita terbaru (exclude pinned)
     *
     * @return array{ pinnedNews: News|null, latestNews: Collection }
     */
    public function handle(): array
    {
        // Base query biar tidak duplikat
        $baseQuery = News::query()
            ->whereNotNull('current_version_id')
            ->with(['currentVersion', 'author', 'categories']);

        // Pinned News
        $pinnedNews = (clone $baseQuery)
            ->whereNotNull('pinned_at')
            ->where(function ($q) {
                $q->whereNull('pin_expired_at')
                    ->orWhere('pin_expired_at', '>=', now());
            })
            ->first();

        // Kalau tidak ada pinned news, fallback ke berita terbaru
        if (!$pinnedNews) {
            $pinnedNews = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->first();
        }

        // 3 Latest News (exclude pinned/fallback)
        $latestNews = (clone $baseQuery)
            ->when($pinnedNews, fn($q) => $q->where('id', '!=', $pinnedNews->id))
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return compact('pinnedNews', 'latestNews');
    }
}