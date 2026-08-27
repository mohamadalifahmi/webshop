<?php

use App\Livewire\Admin\SiteSettings;
use App\Models\User;
use Database\Seeders\CatalogSeeder;

it('shows the branded account page for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee('My Account');
});

it('branded auth screens render', function () {
    $this->get(route('login'))->assertOk()->assertSee('Welcome back');
    $this->get(route('register'))->assertOk()->assertSee('Create your account');
    $this->get(route('password.request'))->assertOk();
});

it('renders the admin site settings page without layout errors', function () {
    $this->seed(CatalogSeeder::class);
    $admin = makeAdmin();

    Livewire::actingAs($admin)
        ->test(SiteSettings::class)
        ->assertSee('Shipping Rates by Governorate')
        ->assertSee('Beirut')
        ->assertHasNoErrors();
});
