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

        FilamentAsset::register([
            Css::make('app', Vite::asset('resources/css/app.css')),
            Js::make('app', Vite::asset('resources/js/app.js')),
        ]);
    }


}
