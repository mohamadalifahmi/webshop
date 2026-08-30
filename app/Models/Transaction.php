<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    public const TYPE_EARNING = 'sale_earning';

    public const TYPE_COMMISSION = 'commission';

    public const TYPE_PAYOUT = 'payout';

    public const TYPE_REFUND = 'refund_debit';

    protected $fillable = [
        'seller_id',
        'order_item_id',
        'payout_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'available_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'available_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }
}
