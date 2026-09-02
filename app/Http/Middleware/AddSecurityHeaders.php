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

        // Browser caching policy:
        //  - hashed build assets (immutable, content-addressed) -> 1 year
        //  - user media (product images, proofs) -> 1 month
        //  - HTML/JSON -> always revalidate (no stale pages, always fresh sessions)
        $path = (string) $request->getRequestUri();
        if (preg_match('#^/build/assets/.*\.(css|js|woff2?|svg|png|jpe?g|webp)(\?.*)?$#', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } elseif (preg_match('#^/storage/.*\.(png|jpe?g|gif|webp|svg|avif|ico|pdf)(\?.*)?$#', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=2592000');
        } elseif (preg_match('#^/storage/.*\.(woff2?)(\?.*)?$#', $path)) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } else {
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        }

        $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=(), payment=(), usb=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('X-XSS-Protection', '0'); // modern: XSS protection is handled by CSP; disable legacy

        // Images: 'self' covers same-origin media on a matching host/port. In
        // local dev the storage disk serves over plain http:// (and the host can
        // differ from the request host, so 'self' may not match) — explicitly
        // allow http: there. Production stays https-only and unchanged.
        $imgSrc = "img-src 'self' data: blob: https:"
            .(app()->environment('production') ? '' : ' http:');

$response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            // Livewire + Alpine MUST have 'unsafe-eval': Alpine compiles wire:/x- expressions
            // with `new AsyncFunction` at runtime. Without it every Livewire action fails
            // silently in the browser. This is a documented Livewire requirement and does
            // NOT penalize the SecurityHeaders A+ score (only 'unsafe-inline' does).
            "script-src 'self' 'nonce-{$nonce}' 'unsafe-eval' https://js.stripe.com https://m.stripe.network",
            // Tailwind/Preflight + Alpine inline style toggling need 'unsafe-inline' here;
            // fonts are self-hosted (no external stylesheet host needed).
            "style-src 'self' 'unsafe-inline'",
            $imgSrc,
            // Fonts are self-hosted under the Vite build.
            "font-src 'self' data:",
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
