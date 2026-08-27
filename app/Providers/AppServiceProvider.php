<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(fn ($user, $ability) => $user->hasRole('admin') ? true : null);

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
