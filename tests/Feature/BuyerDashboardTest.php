<?php

use App\Models\User;

it('shows every buyer their own dashboard with stats and recent orders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.dashboard'))
        ->assertOk()
        ->assertSee('personal shopping dashboard')
        ->assertSee('No orders yet');
});

it('routes each role to its own dashboard after login', function () {
    [$sellerUser] = makeApprovedSeller();
    $admin = makeAdmin();
    $buyer = User::factory()->create();

    $this->actingAs($buyer)->get(route('dashboard'))->assertRedirect(route('account.dashboard'));
    $this->actingAs($admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    $this->actingAs($sellerUser)->get(route('dashboard'))->assertRedirect(route('seller.dashboard'));
});

it('forbids guests from the buyer dashboard', function () {
    $this->get(route('account.dashboard'))->assertRedirect(route('login'));
});
