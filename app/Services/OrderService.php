<?php

namespace App\Services;

use App\Mail\NewOrderAdminMail;
use App\Mail\NewOrderSellerMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    /**
     * One cart, multiple sellers, one payment.
     *
     * @param  array{name: string, phone: string, governorate: string, address: string, note?: ?string}  $data
     */
    public static function place(User $buyer, string $paymentMethod, array $data): Order
    {
        $cartItems = CartService::items($buyer);

        if ($cartItems->isEmpty()) {
            throw new \DomainException('Cart is empty.');
        }

        $shippingFee = ShippingService::feeFor($data['governorate']);

        $order = DB::transaction(function () use ($buyer, $cartItems, $data, $paymentMethod, $shippingFee) {
            $subtotal = '0';
            $lines = [];

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product()->lockForUpdate()->with('seller')->first();

                if (! $product || $product->status !== 'active' || $product->stock < $cartItem->quantity) {
                    throw new \DomainException("Product [{$cartItem->product->name}] is no longer available.");
                }

                $split = CommissionService::calculate($product, $cartItem->quantity);

                $lines[] = [
                    'product' => $product,
                    'quantity' => $cartItem->quantity,
                    'split' => $split,
                ];

                $subtotal = bcadd($subtotal, $split['subtotal'], 2);
            }

            $order = Order::create([
                'user_id' => $buyer->id,
                'order_number' => Order::generateNumber(),
                'status' => Order::STATUS_FLOW[0],
                'subtotal' => $subtotal,
                'shipping_fee' => number_format($shippingFee, 2, '.', ''),
                'total' => bcadd($subtotal, number_format($shippingFee, 2, '.', ''), 2),
                'currency' => 'USD',
                'payment_method' => $paymentMethod,
                'payment_status' => 'unpaid',
                'ship_to_name' => $data['name'],
                'ship_to_phone' => $data['phone'],
                'governorate' => $data['governorate'],
                'address' => $data['address'],
                'customer_note' => $data['note'] ?? null,
            ]);

            $deadline = now()->addHours(SettingsService::shipDeadlineHours());

            foreach ($lines as $line) {
                /** @var Product $product */
                $product = $line['product'];
                $split = $line['split'];

                $order->items()->create([
                    'seller_id' => $product->seller_id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => $split['subtotal'],
                    'commission_rate' => $split['rate'],
                    'commission_amount' => $split['commission'],
                    'seller_earning' => $split['earning'],
                    'shipment_status' => 'awaiting',
                    'cancel_deadline_at' => $deadline,
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            CartService::clear($buyer);

            return $order;
        });

        self::dispatchNotifications($order);

        return $order;
    }

    public static function dispatchNotifications(Order $order): void
    {
        $order->load('items.seller.user');

        Mail::to(config('mail.admin_address', 'admin@soukelkom.test'))
            ->queue(new NewOrderAdminMail($order));

        foreach ($order->items->groupBy('seller_id') as $group) {
            $sellerUser = $group->first()->seller?->user;
            if ($sellerUser) {
                Mail::to($sellerUser->email)->queue(new NewOrderSellerMail($order, $group));
            }
        }
    }

    public static function markShipped(OrderItem $item, string $trackingNumber): void
    {
        if ($item->shipment_status !== 'awaiting' || $item->order->payment_status !== 'paid') {
            throw new \DomainException('This item cannot be shipped right now.');
        }

        $item->update([
            'shipment_status' => 'shipped',
            'tracking_number' => $trackingNumber,
            'shipped_at' => now(),
        ]);

        $item->order->recalculateStatus();

        Mail::to($item->order->user->email)
            ->queue(new OrderShippedMail($item));
    }

    public static function markDelivered(OrderItem $item): void
    {
        if ($item->shipment_status !== 'shipped') {
            return;
        }

        $holdDays = SettingsService::holdDaysAfterDelivery();
        $availableAt = now()->addDays($holdDays);

        DB::transaction(function () use ($item, $availableAt) {
            $item->update([
                'shipment_status' => 'delivered',
                'delivered_at' => now(),
                'earnings_available_at' => $availableAt,
            ]);

            Transaction::where('order_item_id', $item->id)
                ->where('type', Transaction::TYPE_EARNING)
                ->update(['available_at' => $availableAt]);

            $item->order->recalculateStatus();
        });
    }
}
