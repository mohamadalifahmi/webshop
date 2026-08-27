<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasRole('seller')) {
            abort(403, 'Seller account required.');
        }

        if ($request->user()->seller?->status !== 'approved' && ! $request->routeIs('seller.application*')) {
            return redirect()->route('seller.application.show');
        }

        return $next($request);
    }
}
