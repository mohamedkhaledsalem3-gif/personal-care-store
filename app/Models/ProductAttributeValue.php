<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_attribute_id',
        'value',
        'slug',
        'color_code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Attribute الذي تنتمي إليه القيمة.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(
            ProductAttribute::class,
            'product_attribute_id'
        );
    }

    /**
     * المنتجات التي تستخدم هذه القيمة.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'product_attribute_product',
            'product_attribute_value_id',
            'product_id'
        );
    }

    /**
     * Variants التي تستخدم هذه القيمة.
     */
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_attribute_value_variant',
            'product_attribute_value_id',
            'product_variant_id'
        );
    }
}