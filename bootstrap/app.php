<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\EnsureSellerApproved;
use App\Http\Middleware\TrustProxies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'seller.approved' => EnsureSellerApproved::class,
        ]);

        // Security-first global middleware (runs before the response is returned).
        // append() => these run AFTER session/auth so they can read cookies/proxies.
        $middleware->appendToGroup('web', [
            TrustProxies::class,
            AddSecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();