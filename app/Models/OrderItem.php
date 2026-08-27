<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'seller_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'subtotal',
        'commission_rate',
        'commission_amount',
        'seller_earning',
        'shipment_status',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancel_deadline_at',
        'earnings_available_at',
        'earnings_released',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'seller_earning' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancel_deadline_at' => 'datetime',
            'earnings_available_at' => 'datetime',
            'earnings_released' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
