<?php

namespace App\Jobs;

use App\Mail\OrderItemCancelledMail;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transaction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class CancelUnshippedOrders implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function handle(): void
    {
        $lateItems = OrderItem::query()
            ->where('shipment_status', 'awaiting')
            ->whereNotNull('cancel_deadline_at')
            ->where('cancel_deadline_at', '<', now())
            ->whereHas('order', fn ($q) => $q->where('payment_status', 'paid'))
            ->with(['order.user', 'seller'])
            ->get();

        foreach ($lateItems as $item) {
            if (Transaction::where('order_item_id', $item->id)->where('type', Transaction::TYPE_REFUND)->exists()) {
                continue;
            }

            DB::transaction(function () use ($item) {
                $earningCredited = Transaction::where('order_item_id', $item->id)
                    ->where('type', Transaction::TYPE_EARNING)
                    ->exists();

                $item->update(['shipment_status' => 'cancelled']);

                if ($item->product_id && $earningCredited === false) {
                    Product::withTrashed()->whereKey($item->product_id)->increment('stock', $item->quantity);
                }

                if ($earningCredited) {
                    $seller = Seller::lockForUpdate()->findOrFail($item->seller_id);
                    $newBalance = bcsub((string) $seller->balance, (string) $item->seller_earning, 2);
                    $seller->update(['balance' => $newBalance]);

                    Transaction::create([
                        'seller_id' => $seller->id,
                        'order_item_id' => $item->id,
                        'type' => Transaction::TYPE_REFUND,
                        'amount' => '-'.(string) $item->seller_earning,
                        'balance_after' => $newBalance,
                        'description' => "Refund: item [{$item->product_name}] auto-cancelled (unshipped within deadline)",
                    ]);
                }

                $item->order->recalculateStatus();
            });

            \Mail::to($item->order->user->email)
                ->queue(new OrderItemCancelledMail($item));
        }
    }
}
