<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\NewsChart;
use App\Filament\Widgets\CalendarWidget;
use App\Filament\Widgets\PopularNewsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\PopularCategoriesWidget;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Filament\Http\Middleware\Logout;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')            
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                StatsOverview::class,
                CalendarWidget::class,
                NewsChart::class,
                PopularNewsWidget::class,
                RecentActivityWidget::class,
                PopularCategoriesWidget::class,
            ])
            ->plugin(
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('') 
                    ->selectable(true) // Enable drag-and-drop & resizing
                    ->editable(true) // Enable event editing (drag, resize)                
                    ->timezone(config('app.timezone'))
                    ->locale(config('app.locale'))
                    ->plugins(['interaction', 'dayGrid', 'timeGrid', 'list']) // Load necessary FullCalendar plugins
                    ->config([]) // Konfigurasi tambahan jika diperlukan
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
