<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\News;

class RecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.recent-activity-widget';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 4;
    
    public $recentNews;
    public $recentUsers;
    
    public function mount()
    {
        $this->recentNews = News::with('currentVersion')
            ->latest()
            ->limit(5)
            ->get();
            
        $this->recentUsers = User::latest()
            ->limit(5)
            ->get();
    }
    
    protected function getViewData(): array
    {
        return [
            'recentNews' => $this->recentNews,
            'recentUsers' => $this->recentUsers,
        ];
    }
}