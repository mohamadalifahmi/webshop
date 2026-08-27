<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    protected $fillable = [
        'seller_id',
        'amount',
        'method',
        'bank_details',
        'status',
        'admin_note',
        'processed_by',
        'requested_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'bank_details' => 'json',
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
