<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'description',
        'phone',
        'governorate',
        'status',
        'commission_override',
        'balance',
        'rejection_reason',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_override' => 'decimal:2',
            'balance' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
