<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

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

    public function getRouteKeyName(): string
    {
        return 'slug';
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
            ->sharpen(10)
            ->performOnCollections('images');

        // WebP pipeline — ss generated synchronously (nonQueued) so upload
        // compression feedback is accurate the moment a product is saved.
        $this->addMediaConversion('webp-thumb')
            ->crop('crop-center', 400, 400)
            ->sharpen(10)
            ->format('webp')
            ->quality(85)
            ->nonQueued()
            ->performOnCollections('images');

        $this->addMediaConversion('webp')
            ->width(1200)
            ->format('webp')
            ->quality(85)
            ->nonQueued()
            ->performOnCollections('images');

        // Large-format variant ready for banner imagery (1920px, WebP).
        $this->addMediaConversion('webp-banner')
            ->width(1920)
            ->format('webp')
            ->quality(85)
            ->nonQueued()
            ->performOnCollections('images');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')->useDisk('public');
    }
}
