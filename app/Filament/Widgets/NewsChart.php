<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\News;
use Carbon\Carbon;

class NewsChart extends ChartWidget
{
    protected static ?string $heading = 'Trend Publikasi Berita (7 Hari Terakhir)';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->put(Carbon::now()->subDays($i)->format('Y-m-d'), 0);
        }
        
        $data = News::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subDays(6))
            ->groupBy('date')
            ->pluck('total', 'date');
            
        $mergedData = $dates->merge($data);
        
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Berita',
                    'data' => array_values($mergedData->toArray()),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_keys($mergedData->toArray()),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    // Tambahkan opsi chart
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}