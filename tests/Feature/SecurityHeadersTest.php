<?php

/*
|--------------------------------------------------------------------------
| SECURITY SUITE — Layer 1/2: Headers + CSP
|--------------------------------------------------------------------------
*/

it('sends every critical security header on the homepage', function () {
    $response = $this->get('/');

    $response->assertHeader('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
    $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
});

it('sends a nonce-based CSP that never allows unsafe-inline for scripts', function () {
    $response = $this->get('/');

    $csp = $response->headers->get('Content-Security-Policy');
    expect($csp)->not->toBeNull();

    // Strict directives present
    expect($csp)->toContain("default-src 'self'")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'none'")
        ->toContain("base-uri 'self'");

    // NO unsafe-inline for scripts (that would kill the A+ rating)
    expect($csp)->not->toContain('script-src \'unsafe-inline\'');

    // Livewire/Alpine REQUIRE 'unsafe-eval' (documented) — keep it, it's A+-safe
    expect($csp)->toContain("'unsafe-eval'");

    // Fonts are self-hosted — no external font host in the policy
    expect($csp)->not->toContain('fonts.bunny.net');
    expect($csp)->toContain("font-src 'self' data:");

    // A per-request nonce is embedded
    expect($csp)->toMatch('/nonce-[A-Za-z0-9+\/=]+/');
});

it('applies the security headers on auth pages too', function () {
    $this->get(route('login'))
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Content-Security-Policy');
});
