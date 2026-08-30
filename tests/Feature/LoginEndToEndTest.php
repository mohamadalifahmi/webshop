<?php

it('admin can log in via the Volt login form and open the admin panel', function () {
    $this->seed();

    $component = Livewire::test('pages.auth.login')
        ->set('form.email', 'admin@soukelkom.test')
        ->set('form.password', 'password')
        ->call('login');

    $component->assertRedirect();

    // Follow the post-login dashboard redirect as the authenticated admin
    $this->actingAs(\App\Models\User::where('email', 'admin@soukelkom.test')->first())
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))->assertOk();
});

it('seller can log in and reach the seller hub', function () {
    $this->seed();

    Livewire::test('pages.auth.login')
        ->set('form.email', 'ahmed@soukelkom.test')
        ->set('form.password', 'password')
        ->call('login')
        ->assertRedirect();

    $this->actingAs(\App\Models\User::where('email', 'ahmed@soukelkom.test')->first())
        ->get(route('dashboard'))
        ->assertRedirect(route('seller.dashboard'));

    $this->get(route('seller.dashboard'))->assertOk();
});

it('rejects wrong credentials on the login form', function () {
    $this->seed();

    Livewire::test('pages.auth.login')
        ->set('form.email', 'admin@soukelkom.test')
        ->set('form.password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['form.email']);
});