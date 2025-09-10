<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\League;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Automatically pass parentLeagues to all views
        View::composer('*', function ($view) {
            $parentLeagues = League::whereNull('parent_id')->get();
            $view->with('parentLeagues', $parentLeagues);
        });
    }
}
