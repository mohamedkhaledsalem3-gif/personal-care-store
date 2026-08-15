<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Computed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'unit',
        'weight',
        'cost_price',
        'price',
        'sale_price',
        'stock_quantity',
        'low_stock_threshold',
        'status',
        'is_featured',
        'is_new',
        'is_best_seller',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'weight' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',

        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',

        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_best_seller' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_attribute_product',
            'product_id',
            'product_attribute_value_id'
        )->withTimestamps();
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
        return $this->current_price - (float) $this->cost_price;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * المنتج متوفر إذا كان لديه Variant نشط
     * لديه Inventory وبداخله كمية متاحة للبيع.
     *
     * المصدر الحقيقي للمخزون:
     * inventories.quantity - inventories.reserved_quantity
     */
    public function isInStock(): bool
    {
        return $this->variants()
            ->where('is_active', true)
            ->whereHas('inventory', function ($query) {
                $query->whereColumn(
                    'quantity',
                    '>',
                    'reserved_quantity'
                );
            })
            ->exists();
    }
}
