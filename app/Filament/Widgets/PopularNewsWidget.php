<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class PopularNewsWidget extends Widget
{
    protected static string $view = 'filament.widgets.popular-news-widget';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 3;
    
    public $popularNews;
    
    public function mount()
    {
        $this->popularNews = News::query()
            ->whereNotNull('current_version_id')
            ->with(['currentVersion', 'categories'])
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->selectRaw('news.*, (views * 1 + likes * 3) AS popularity_score')
            ->having('popularity_score', '>', 0)
            ->orderByDesc('popularity_score')
            ->limit(10)
            ->get();
    }
    
    protected function getViewData(): array
    {
        return [
            'popularNews' => $this->popularNews,
        ];
    }
}