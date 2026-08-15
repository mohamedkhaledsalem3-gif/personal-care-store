<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'cost_price',
        'price',
        'sale_price',
        'stock_quantity',
        'low_stock_threshold',
        'unit',
        'weight',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:3',

        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',

        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_attribute_value_variant',
            'product_variant_id',
            'product_attribute_value_id'
        )->withTimestamps();
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * السعر الحالي (سعر البيع إن وُجد، وإلا السعر الأساسي).
     */
    #[Computed]
    public function current_price(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    /**
     * الربح من الوحدة (السعر الحالي - تكلفة الشراء).
     */
    #[Computed]
    public function profit(): float
    {
        return $this->current_price - (float) ($this->cost_price ?? 0);
    }

    /**
     * الكمية المتاحة للبيع فعليًا.
     */
    #[Computed]
    public function available_quantity(): int
    {
        if ($this->relationLoaded('inventory')) {
            return $this->inventory?->available_quantity ?? 0;
        }

        return $this->inventory?->available_quantity ?? 0;
    }

    /**
     * هل الـVariant متاح للبيع؟
     */
    public function isInStock(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return $this->available_quantity > 0;
    }

    /**
     * هل المخزون منخفض؟
     */
    public function isLowStock(): bool
    {
        $available = $this->available_quantity;

        return $available > 0
            && $available <= $this->inventory?->low_stock_threshold;
    }
}
