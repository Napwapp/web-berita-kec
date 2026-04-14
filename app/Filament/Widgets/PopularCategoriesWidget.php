<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Category;

class PopularCategoriesWidget extends Widget
{
    protected static string $view = 'filament.widgets.popular-categories-widget';
    
    protected int | string | array $columnSpan = 1;
    
    protected static ?int $sort = 5;
    
    public $categories;
    public $totalNews;
    
    public function mount()
    {
        $this->categories = Category::withCount('news')
            ->having('news_count', '>', 0)
            ->orderByDesc('news_count')
            ->limit(5)
            ->get();
            
        $this->totalNews = \App\Models\News::count();
    }
    
    protected function getViewData(): array
    {
        return [
            'categories' => $this->categories,
            'totalNews' => $this->totalNews,
        ];
    }
}