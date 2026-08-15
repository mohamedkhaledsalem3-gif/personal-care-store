<?php

namespace App\Models;
use App\Models\ProductVariant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    /**
     * السلة التي ينتمي إليها العنصر.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(
            Cart::class,
            'cart_id'
        );
    }

    /**
     * الـ Product Variant الموجود في السلة.
     */
   public function variant(): BelongsTo
{
    return $this->belongsTo(
        ProductVariant::class,
        'product_variant_id'
    );
}

public function productVariant(): BelongsTo
{
    return $this->belongsTo(
        ProductVariant::class,
        'product_variant_id'
    );
}
   
}