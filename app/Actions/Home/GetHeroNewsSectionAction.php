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
    public function handle(?string $type = null): array
    {
        $baseQuery = News::query()
            ->whereNotNull('current_version_id')
            ->when($type, fn($q) => $q->where('type', $type))
            ->with(['currentVersion', 'author', 'categories']);

        $pinnedNews = (clone $baseQuery)
            ->whereNotNull('pinned_at')
            ->where(function ($q) {
                $q->whereNull('pin_expired_at')
                    ->orWhere('pin_expired_at', '>=', now());
            })
            ->first();

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