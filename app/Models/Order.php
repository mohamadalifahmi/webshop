<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_FLOW = ['pending_payment', 'paid', 'partially_shipped', 'shipped', 'delivered', 'completed'];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_fee',
        'total',
        'currency',
        'payment_method',
        'payment_status',
        'payment_proof_path',
        'payment_reference',
        'ship_to_name',
        'ship_to_phone',
        'governorate',
        'address',
        'customer_note',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'SK-'.strtoupper(substr(uniqid(), -6)).'-'.random_int(100, 999);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public function recalculateStatus(): void
    {
        if (in_array($this->status, ['pending_payment', 'cancelled', 'refunded'], true)) {
            return;
        }

        $items = $this->items()->get();
        $counts = [
            'cancelled' => $items->where('shipment_status', 'cancelled')->count(),
            'awaiting' => $items->where('shipment_status', 'awaiting')->count(),
            'shipped' => $items->where('shipment_status', 'shipped')->count(),
            'delivered' => $items->where('shipment_status', 'delivered')->count(),
        ];

        $active = $items->count() - $counts['cancelled'];

        if ($active === 0) {
            $newStatus = $this->payment_status === 'paid' ? 'refunded' : 'cancelled';
        } elseif ($counts['delivered'] === $active) {
            $newStatus = 'delivered';
        } elseif ($active === $counts['shipped'] + $counts['delivered']) {
            $newStatus = 'shipped';
        } elseif ($counts['shipped'] + $counts['delivered'] > 0) {
            $newStatus = 'partially_shipped';
        } else {
            $newStatus = 'paid';
        }

        if ($newStatus !== $this->status) {
            $this->update(['status' => $newStatus]);
        }
    }
}
