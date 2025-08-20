<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $driver = DB::connection()->getDriverName();
        config(['database.like_operator' => $driver === 'pgsql' ? 'ilike' : 'like']);

        FilamentAsset::register([
            Js::make('custom-script', resource_path('js/custom.js')),
        ]);
    }
}
