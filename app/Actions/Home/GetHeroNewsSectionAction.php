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
        // Pinned News
        $pinnedNews = News::query()
            ->whereNotNull('pinned_at')
            ->where(function ($q) {
                $q->whereNull('pin_expired_at')
                    ->orWhere('pin_expired_at', '>=', now());
            })
            ->whereNotNull('current_version_id')
            ->with(['currentVersion', 'author', 'categories'])
            ->first();

        // 3 Latest News (exclude pinned)
        $latestNews = News::query()
            ->whereNotNull('current_version_id')
            ->when($pinnedNews, fn($q) => $q->where('id', '!=', $pinnedNews->id))
            ->with(['currentVersion', 'author', 'categories'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return compact('pinnedNews', 'latestNews');
    }
}