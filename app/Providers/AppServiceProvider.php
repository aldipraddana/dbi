<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_contains(url('/'), 'D03')) {
            app(FrontendAssets::class)->setScriptRoute(function ($handle) {
                return config('app.debug')
                    ? Route::get('/backup-lenovo/D03/public/livewire/livewire.js', $handle)
                    : Route::get('/backup-lenovo/D03/public/livewire/livewire.min.js', $handle);
            });

            app(HandleRequests::class)->setUpdateRoute(function ($handle) {
                return Route::post('/backup-lenovo/D03/public/livewire/update', $handle)->middleware('web');
            });

        }
        FilamentAsset::register([
            Js::make('custom', asset('js/custom.js') . '?v=' . time()),
            Css::make('custom', asset('css/custom.css') . '?v=' . time()),
        ]);
    }
}
