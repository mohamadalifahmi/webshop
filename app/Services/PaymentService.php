<?php

namespace App\Services;

use App\Jobs\CancelUnshippedOrders;
use App\Jobs\DistributeEarnings;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public static function submitManualProof(Order $order, string $proofPath): void
    {
        if ($order->payment_method !== 'manual' || $order->payment_status !== 'unpaid') {
            throw new \DomainException('Payment proof cannot be submitted for this order.');
        }

        $order->update([
            'payment_proof_path' => $proofPath,
            'payment_status' => 'under_review',
        ]);
    }

    public static function markPaid(Order $order, ?User $admin = null, ?string $reference = null): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        DB::transaction(function () use ($order, $reference) {
            $order->items()->update(['cancel_deadline_at' => now()->addHours(SettingsService::shipDeadlineHours())]);

            $order->forceFill([
                'payment_status' => 'paid',
                'status' => 'paid',
                'paid_at' => now(),
                'payment_reference' => $reference ?? $order->payment_reference,
            ])->save();

            $order->refresh();

            DistributeEarnings::dispatch($order);
            CancelUnshippedOrders::dispatch($order)->delay(now()->addHours(SettingsService::shipDeadlineHours())->addMinutes(5));
        });
    }
}
