<?php

namespace App\Services;

use App\Mail\PayoutPaidMail;
use App\Models\Payout;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PayoutService
{
    /**
     * Money the seller can actually withdraw.
     * Balance already reflects earnings credited and payouts sent;
     * pending requests are still inside the balance, so we lock them here.
     */
    public static function availableBalance(Seller $seller): float
    {
        $pending = (float) Payout::where('seller_id', $seller->id)
            ->where('status', 'pending')
            ->sum('amount');

        return round((float) $seller->balance - $pending, 2);
    }

    public static function onHold(Seller $seller): float
    {
        return (float) Transaction::where('seller_id', $seller->id)
            ->where('type', Transaction::TYPE_EARNING)
            ->whereNull('released_at')
            ->sum('amount');
    }

    public static function request(Seller $seller, float $amount, ?array $bankDetails = null): Payout
    {
        if ($amount < SettingsService::minPayout()) {
            throw new \DomainException('Minimum payout amount is $'.number_format(SettingsService::minPayout(), 2).'.');
        }

        $available = self::availableBalance($seller);

        if ($amount > $available) {
            throw new \DomainException('Requested amount exceeds your available balance.');
        }

        $hasPending = Payout::where('seller_id', $seller->id)->where('status', 'pending')->exists();

        if ($hasPending) {
            throw new \DomainException('You already have a pending payout request.');
        }

        return Payout::create([
            'seller_id' => $seller->id,
            'amount' => number_format($amount, 2, '.', ''),
            'method' => 'bank_transfer',
            'bank_details' => $bankDetails ?? ['note' => 'Bank transfer'],
            'status' => 'pending',
            'requested_at' => now(),
        ]);
    }

    public static function markPaid(Payout $payout, User $admin): void
    {
        DB::transaction(function () use ($payout, $admin) {
            if ($payout->status !== 'pending') {
                throw new \DomainException('This payout was already processed.');
            }

            $seller = Seller::lockForUpdate()->findOrFail($payout->seller_id);

            $newBalance = bcsub((string) $seller->balance, (string) $payout->amount, 2);
            $seller->forceFill(['balance' => $newBalance])->save();

            Transaction::create([
                'seller_id' => $seller->id,
                'payout_id' => $payout->id,
                'type' => Transaction::TYPE_PAYOUT,
                'amount' => '-'.(string) $payout->amount,
                'balance_after' => $newBalance,
                'description' => "Payout #{$payout->id} sent via {$payout->method}",
            ]);

            $payout->update([
                'status' => 'paid',
                'processed_by' => $admin->id,
                'processed_at' => now(),
            ]);
        });

        \Mail::to($payout->seller->user->email)->queue(new PayoutPaidMail($payout));
    }

    public static function reject(Payout $payout, User $admin, string $note): void
    {
        if ($payout->status !== 'pending') {
            throw new \DomainException('This payout was already processed.');
        }

        $payout->update([
            'status' => 'rejected',
            'admin_note' => $note,
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);
    }
}
