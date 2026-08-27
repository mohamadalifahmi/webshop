<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, Searchable, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'stock',
        'status',
        'commission_rate',
        'rejection_reason',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('stock', '>', 0);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->sharpen()
            ->performOnCollections('images');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => (string) $this->description,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'price' => (float) $this->price,
        ];
    }
}
