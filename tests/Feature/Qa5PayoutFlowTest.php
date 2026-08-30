<?php

/*
|--------------------------------------------------------------------------
| QA TEST 5 — PAYOUT FLOW
| Action: Seller with $100 balance requests $50 payout -> Admin approves
| Expected: Payout status = paid, seller balance = $50, ledger row written,
|           seller notified by email. Min-$50 and double-request guards work.
*/

use App\Livewire\Admin\PayoutsManager as AdminPayouts;
use App\Livewire\Seller\PayoutsManager;
use App\Mail\PayoutPaidMail;
use App\Models\Payout;
use App\Models\Transaction;
use App\Services\PayoutService;

it('processes a payout end to end: request -> admin marks paid -> balance deducted', function () {
    Mail::fake();

    [$sellerUser, $seller] = makeApprovedSeller();
    $admin = makeAdmin();

    // Give the seller a $100 balance (simulating prior distributed earnings)
    $seller->update(['balance' => '100.00']);

    // Guards first
    expect(fn () => PayoutService::request($seller, 49.99))->toThrow(DomainException::class);

    // Request $50
    Livewire::actingAs($sellerUser)
        ->test(PayoutsManager::class)
        ->set('amount', '50')
        ->set('bankName', 'Byblos Bank')
        ->set('iban', 'LB62 0999 0000 0001 0019 0122 9114')
        ->call('requestPayout')
        ->assertHasNoErrors();

    $payout = Payout::where('seller_id', $seller->id)->firstOrFail();
    expect($payout->status)->toBe('pending')
        ->and((float) $payout->amount)->toBe(50.0)
        ->and(PayoutService::availableBalance($seller))->toBe(50.0); // pending locks it

    // Double request is blocked
    expect(fn () => PayoutService::request($seller, 20))->toThrow(DomainException::class);

    // Admin approves the payout
    $this->actingAs($admin);

    Livewire::test(AdminPayouts::class)
        ->set('statusFilter', '')
        ->call('markPaid', $payout->id);

    expect($payout->fresh()->status)->toBe('paid')
        ->and((float) $seller->fresh()->balance)->toBe(50.0);

    // Ledger written with negative payout amount
    $tx = Transaction::where('payout_id', $payout->id)->where('type', Transaction::TYPE_PAYOUT)->firstOrFail();
    expect((float) $tx->amount)->toBe(-50.0)
        ->and((float) $tx->balance_after)->toBe(50.0);

    Mail::assertQueued(PayoutPaidMail::class);
});
