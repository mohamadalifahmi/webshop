<?php

/*
|--------------------------------------------------------------------------
| QA TEST 4 — EARNINGS DISTRIBUTION
| Action: Mark Order as Paid
| Expected: Seller A balance += 45, Seller B balance += 18,
|           transaction log created for earnings AND commissions
*/

use App\Jobs\CancelUnshippedOrders;
use App\Jobs\DistributeEarnings;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;

it('credits seller balances and writes the ledger when an order is marked paid', function () {
    [$sellerAUser, $sellerA] = makeApprovedSeller('Seller A');
    [$sellerBUser, $sellerB] = makeApprovedSeller('Seller B');

    $productA = makeActiveProduct($sellerA, 50, 10, 'Nike Shoes');
    $productB = makeActiveProduct($sellerB, 20, 10, 'T-Shirt');

    $buyer = User::factory()->create();
    $this->actingAs($buyer);

    CartService::add($buyer, $productA->id, 1);
    CartService::add($buyer, $productB->id, 1);

    $order = OrderService::place($buyer, 'manual', [
        'name' => 'Ali Buyer',
        'phone' => '+961 03 999999',
        'governorate' => 'Beirut',
        'address' => 'Hamra Street',
        'note' => null,
    ]);

    // Idempotency guard: run twice, balances credited once only
    (new DistributeEarnings($order))->handle();
    (new DistributeEarnings($order))->handle();

    expect((float) $sellerA->fresh()->balance)->toBe(45.0)
        ->and((float) $sellerB->fresh()->balance)->toBe(18.0);

    // Ledger rows: 2 earnings + 2 commissions
    $earnings = Transaction::where('type', Transaction::TYPE_EARNING)->get();
    $commissions = Transaction::where('type', Transaction::TYPE_COMMISSION)->get();

    expect($earnings)->toHaveCount(2)
        ->and($commissions)->toHaveCount(2)
        ->and((float) $earnings->where('seller_id', $sellerA->id)->sum('amount'))->toBe(45.0)
        ->and((float) $commissions->sum('amount'))->toBe(7.0);

    // balance_after snapshot matches
    $earningA = $earnings->firstWhere('seller_id', $sellerA->id);
    expect((float) $earningA->balance_after)->toBe(45.0);
});

it('refunds the buyer and claws back earnings when a paid item is never shipped', function () {
    [$sellerUser, $seller] = makeApprovedSeller();

    $product = makeActiveProduct($seller, 100, 5, 'Late Product');
    $buyer = User::factory()->create();
    $this->actingAs($buyer);

    CartService::add($buyer, $product->id, 1);

    $order = OrderService::place($buyer, 'manual', [
        'name' => 'Ali Buyer',
        'phone' => '+961 03 111111',
        'governorate' => 'Beirut',
        'address' => 'Achrafieh',
        'note' => null,
    ]);

    // Admin marks the manual payment as paid -> earnings distributed (sync queue)
    PaymentService::markPaid($order);
    expect((float) $seller->fresh()->balance)->toBe(90.0)
        ->and($order->fresh()->payment_status)->toBe('paid');

    // Simulate deadline passing: item auto-cancels, refund debits seller
    $item = $order->items->first();
    $item->update(['cancel_deadline_at' => now()->subHour()]);

    (new CancelUnshippedOrders)->handle();

    expect($item->fresh()->shipment_status)->toBe('cancelled')
        ->and((float) $seller->fresh()->balance)->toBe(0.0)
        ->and(Transaction::where('type', Transaction::TYPE_REFUND)->exists())->toBeTrue()
        ->and($order->fresh()->status)->toBe('refunded');
});
