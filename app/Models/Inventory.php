<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $fillable = [
        'product_variant_id',
        'quantity',
        'reserved_quantity',
        'low_stock_threshold',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * الكمية المتاحة للبيع فعليًا.
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(
            0,
            $this->quantity - $this->reserved_quantity
        );
    }

    public function isInStock(): bool
    {
        return $this->available_quantity > 0;
    }

    public function isLowStock(): bool
    {
        return $this->available_quantity <= $this->low_stock_threshold;
    }
}