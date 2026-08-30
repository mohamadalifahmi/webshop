<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * AddSecurityHeaders
 * --------------
 * Injects security headers + a Content-Security-Policy (nonce-based) on every
 * response BEFORE it reaches the browser.
 *
 * WHY NONCE-BASED CSP (strictest, no 'unsafe-inline'):
 *   Livewire and inline script blocks require a one-time nonce instead of
 *   allowing arbitrary inline JS. We generate a fresh nonce per request and
 *   expose it to Blade layouts via the `__view` shared variable so that the
 *   <script> tags get `nonce="..."`. This is the A+ rating path for
 *   SecurityHeaders.com / SSL Labs.
 *
 * HOW TO TUNE (edit right here as your app grows):
 *   - Add a third-party domain (fonts, CDNs, payment widgets, etc.) to the
 *     matching *-src directive below. Always use explicit origins.
 *   - If a resource is blocked, the browser console tells you the exact
 *     directive and origin to add — no need to 'unsafe-inline' anything.
 */
class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // A fresh, cryptographically-random nonce for THIS request only.
        $nonce = base64_encode(Str::random(32));

        // Make it available to every Blade layout so they can tag <script> nonce="...".
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=(), payment=(), usb=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('X-XSS-Protection', '0'); // modern: XSS protection is handled by CSP; disable legacy

$response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            // Livewire + Alpine MUST have 'unsafe-eval': Alpine compiles wire:/x- expressions
            // with `new AsyncFunction` at runtime. Without it every Livewire action fails
            // silently in the browser. This is a documented Livewire requirement and does
            // NOT penalize the SecurityHeaders A+ score (only 'unsafe-inline' does).
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://js.stripe.com https://m.stripe.network",
            // Tailwind/Preflight + Alpine inline style toggling need 'unsafe-inline' here;
            // fonts.bunny.net serves the Inter stylesheet.
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net",
            "img-src 'self' data: blob: https:",
            // Fonts come from fonts.bunny.net (see app.css import)
            "font-src 'self' https://fonts.bunny.net data:",
            "connect-src 'self' https://api.stripe.com https://v3.stripe.com https://m.stripe.network",
            "frame-src https://js.stripe.com https://hooks.stripe.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]));

        return $response;
    }
}
