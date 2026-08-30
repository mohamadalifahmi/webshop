<?php

namespace App\Providers;

use App\Console\Kernel;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('production')) {
            $this->app->register(\Laravel\Horizon\HorizonServiceProvider::class);
        }
    }

    public function boot(): void
    {
        $this->app->singleton(KernelContract::class, Kernel::class);
        $this->app->resolving(KernelContract::class, function ($kernel) {
            // ensure our kernel is used
        });

        Gate::before(fn ($user, $ability) => $user->hasRole('admin') ? true : null);

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}