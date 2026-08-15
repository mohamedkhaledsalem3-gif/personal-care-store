<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'order_item_id',
        'product_variant_id',
        'quantity',
        'reason',
        'condition',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function productReturn(): BelongsTo
    {
        return $this->belongsTo(
            ProductReturn::class,
            'return_id'
        );
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'product_variant_id'
        );
    }
}