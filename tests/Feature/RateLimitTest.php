<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| SECURITY SUITE — Layer 3: Rate limiting
|--------------------------------------------------------------------------
*/

it('blocks brute-force attempts on the login route after 10 tries', function () {
    // Volt login is a GET page; throttling applies to every hit on the route.
    for ($i = 0; $i < 10; $i++) {
        $this->get(route('login'));
    }

    // The 11th attempt exceeds the 10-per-minute limit
    $this->get(route('login'))->assertStatus(429);
});

it('blocks rapid registration abuse', function () {
    for ($i = 0; $i < 10; $i++) {
        $this->get(route('register'));
    }

    $this->get(route('register'))->assertStatus(429);
});

it('limits checkout abuse to 6 requests per minute', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    for ($i = 0; $i < 6; $i++) {
        $this->get(route('checkout'));
    }

    $this->get(route('checkout'))->assertStatus(429);
});
