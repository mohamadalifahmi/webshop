<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class DistributeEarnings implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $order = $this->order->fresh()->load('items.seller');

        foreach ($order->items as $item) {
            if ($item->shipment_status === 'cancelled') {
                continue;
            }

            $alreadyPaid = Transaction::where('order_item_id', $item->id)
                ->where('type', Transaction::TYPE_EARNING)
                ->exists();

            if ($alreadyPaid) {
                continue;
            }

            DB::transaction(function () use ($item) {
                $seller = Seller::lockForUpdate()->findOrFail($item->seller_id);

                $newBalance = bcadd((string) $seller->balance, (string) $item->seller_earning, 2);
                $seller->update(['balance' => $newBalance]);

                Transaction::create([
                    'seller_id' => $seller->id,
                    'order_item_id' => $item->id,
                    'type' => Transaction::TYPE_EARNING,
                    'amount' => (string) $item->seller_earning,
                    'balance_after' => $newBalance,
                    'description' => "Earning for [{$item->product_name}] x{$item->quantity} (Order {$item->order->order_number})",
                ]);

                Transaction::create([
                    'seller_id' => $seller->id,
                    'order_item_id' => $item->id,
                    'type' => Transaction::TYPE_COMMISSION,
                    'amount' => (string) $item->commission_amount,
                    'balance_after' => $newBalance,
                    'description' => 'Platform commission ('.number_format((float) $item->commission_rate, 2)."%) for [{$item->product_name}]",
                ]);
            });
        }
    }
}
