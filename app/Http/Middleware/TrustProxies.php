<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TrustProxies
 * -----------
 * When the app runs behind a reverse proxy / CDN (nginx, Cloudflare, Load
 * Balancer) in production, Laravel must trust the `X-Forwarded-Proto` header
 * so that `route()` and `URL::forceScheme('https')` build correct https URLs.
 *
 * Set `TRUSTED_PROXIES` in your .env to `*` (trust ALL proxies) or a
 * comma-separated list of proxy IPs.
 */
class TrustProxies
{
    public function handle(Request $request, Closure $next): Response
    {
        // Never trust '*' blindly: that would let any client spoof
        // X-Forwarded-Proto/Host/For, weakening HTTPS enforcement and URL
        // generation. Only trust explicit proxy IPs/CIDRs set in TRUSTED_PROXIES.
        $proxies = env('TRUSTED_PROXIES');

        if (empty($proxies) || $proxies === '*') {
            return $next($request);
        }

        $proxies = array_filter(array_map('trim', explode(',', $proxies ?: '')));

        if ($proxies === []) {
            return $next($request);
        }

        $trustedHeaderSet = Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO;

        // Symfony requires BOTH the proxy list AND the header bitmask together.
        $request->setTrustedProxies($proxies, $trustedHeaderSet);

        return $next($request);
    }
}
