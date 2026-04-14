<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\News;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $weekAgo = Carbon::now()->subDays(7);
        
        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', $weekAgo)->count();
        
        $totalNews = News::count();
        $newNews = News::where('created_at', '>=', $weekAgo)->count();
        
        $totalViews = News::sum('views');
        $weeklyViews = News::where('created_at', '>=', $weekAgo)->sum('views');
        
        $totalLikes = News::sum('likes');
        $weeklyLikes = News::where('created_at', '>=', $weekAgo)->sum('likes');
        
        return [
            Stat::make('Total User', number_format($totalUsers))
                ->description($newUsers . ' user baru minggu ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([7, 3, 4, 5, 8, 6, 9]),
                
            Stat::make('Total Berita', number_format($totalNews))
                ->description($newNews . ' berita baru minggu ini')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->chart([2, 4, 3, 5, 7, 4, 6]),
                
            Stat::make('Total Views', number_format($totalViews))
                ->description(number_format($weeklyViews) . ' views minggu ini')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info')
                ->chart([15, 20, 18, 25, 30, 28, 35]),
                
            Stat::make('Total Likes', number_format($totalLikes))
                ->description(number_format($weeklyLikes) . ' likes minggu ini')
                ->descriptionIcon('heroicon-m-hand-thumb-up')
                ->color('warning')
                ->chart([5, 8, 7, 12, 10, 14, 11]),
        ];
    }
    
    // Atur posisi widget (opsional)
    protected function getColumns(): int
    {
        return 4;
    }
}