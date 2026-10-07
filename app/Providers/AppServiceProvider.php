<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
   public function boot() {
    // Theme'ni barcha view'larga uzatish
    view()->composer('*', function ($view) {
        $theme = session('theme') ?? (Auth::check() ? Auth::user()->theme : 'light');
        $view->with('currentTheme', $theme ?? 'light');
    });
}
}
