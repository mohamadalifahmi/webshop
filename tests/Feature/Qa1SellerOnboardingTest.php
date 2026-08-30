<?php

/*
|--------------------------------------------------------------------------
| QA TEST 1 — SELLER ONBOARDING
| Action: Register -> Apply Seller -> Admin Approve
| Expected: Seller can now access /seller dashboard
*/

use App\Livewire\Admin\SellersManager;
use App\Livewire\SellerApplication;
use App\Mail\SellerApprovedMail;
use App\Models\Seller;
use App\Models\User;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Mail;

it('lets a buyer apply, get approved by admin, and access the seller hub', function () {
    Mail::fake();
    $this->seed([RoleSeeder::class, CatalogSeeder::class]);

    $buyer = User::factory()->create();
    $admin = makeAdmin();

    // Step 1: buyer submits the seller application
    $this->actingAs($buyer);

    Livewire::test(SellerApplication::class)
        ->set('storeName', 'Ahmed Electronics')
        ->set('phone', '+961 70 123456')
        ->set('governorate', 'Beirut')
        ->call('submit')
        ->assertHasNoErrors();

    expect($buyer->fresh()->hasRole('seller'))->toBeTrue();

    $seller = Seller::where('user_id', $buyer->id)->first();
    expect($seller)->not->toBeNull()
        ->and($seller->status)->toBe('pending');

    // Pending sellers are bounced away from the hub
    $this->get(route('seller.dashboard'))->assertRedirect(route('seller.application.show'));

    // Step 2: admin approves the application
    $this->actingAs($admin);

    Livewire::test(SellersManager::class)
        ->set('statusFilter', '')
        ->call('approve', $seller->id);

    expect($seller->fresh()->status)->toBe('approved');

    // Approval email queued to the seller
    Mail::assertQueued(SellerApprovedMail::class, fn (SellerApprovedMail $mail) => $mail->seller->is($seller));

    // Step 3: approved seller can now access /seller (fresh user to drop cached relations)
    $this->actingAs($buyer->fresh())
        ->get(route('seller.dashboard'))
        ->assertOk();
});
