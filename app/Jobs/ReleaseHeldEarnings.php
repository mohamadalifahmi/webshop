<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ReleaseHeldEarnings implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Transaction::query()
            ->where('type', Transaction::TYPE_EARNING)
            ->whereNull('released_at')
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now())
            ->with('orderItem:id,earnings_released')
            ->chunkById(200, function ($transactions) {
                foreach ($transactions as $tx) {
                    DB::transaction(function () use ($tx) {
                        $tx->update(['released_at' => now()]);

                        if ($tx->order_item_id && $tx->orderItem && ! $tx->orderItem->earnings_released) {
                            $tx->orderItem->update(['earnings_released' => true]);
                        }
                    });
                }
            });
    }
}
