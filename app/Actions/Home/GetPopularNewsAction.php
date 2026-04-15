<?php

namespace App\Actions\Home;

use App\Models\News;
use Illuminate\Database\Eloquent\Collection;

class GetPopularNewsAction
{
    /**
     * Mengambil top 10 berita terpopuler minggu ini
     * berdasarkan weighted score: (views × 1) + (likes × 3)
     *
     * @return Collection
     */
    public function handle(): Collection
    {
        return News::query()
            ->whereNotNull('current_version_id') 
            ->with(['currentVersion', 'categories'])
            ->whereBetween('news.created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->selectRaw('news.*, (views * 1 + likes * 3) AS popularity_score')
            ->having('popularity_score', '>', 0)
            ->orderByDesc('popularity_score')
            ->limit(10)
            ->get();
    }
}