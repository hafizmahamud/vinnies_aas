<?php

namespace App\Providers;

use App\Vinnies\Helper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fixed MySQL length error
        Schema::defaultStringLength(191);

        // Simple @route directive
        Blade::if('route', function ($route) {
            return in_array(Route::currentRouteName(), (array) $route);
        });

        Collection::macro('sortKeys', function ($options = SORT_REGULAR, $descending = false) {
            $items = $this->items;
            $descending ? krsort($items, $options) : ksort($items, $options);
            return new static($items);
        });

        Collection::macro('sortKeysDesc', function ($options = SORT_REGULAR, $descending = false) {
            return Collection::sortKeys($options, true);
        });

        // Share current logged in user to all views
        view()->composer('*', function ($view) {
            $view->with('currentUser', Auth::user());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
