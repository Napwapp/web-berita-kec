<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Vite;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Illuminate\Support\ServiceProvider;
use App\Models\NewsContent;
use App\Observers\NewsContentObserver;
use Illuminate\Support\Facades\View;
use App\View\Composers\NavbarComposer;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer untuk menghapus gambar di Cloudinary ketika NewsContent dihapus
        NewsContent::observe(NewsContentObserver::class);

        // view composer
        View::composer(
            ['components.navbar.index', 'components.navbar.all-categories'],
            NavbarComposer::class
        );

        View::composer('components.dashboard.layout', function ($view) {
            if (!Auth::check()) {
                $view->with('sidebarCounts', array_fill_keys(
                    ['all', 'published', 'review', 'need_revision', 'rejected', 'draft'],
                    0
                ));
                return;
            }

            $statusCounts = News::where('author_id', Auth::id())
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $view->with('sidebarCounts', [
                'all' => $statusCounts->sum(),
                'published' => $statusCounts->get('published', 0),
                'review' => $statusCounts->get('review', 0),
                'need_revision' => $statusCounts->get('need_revision', 0),
                'rejected' => $statusCounts->get('rejected', 0),
                'draft' => $statusCounts->get('draft', 0),
            ]);
        });

        FilamentAsset::register([
            Css::make('app', Vite::asset('resources/css/app.css')),
            Js::make('app', Vite::asset('resources/js/app.js')),
        ]);
    }


}
