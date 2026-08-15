<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'shipping_fee',
        'discount',
        'total',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_area',
        'shipping_postal_code',
        'customer_note',
        'placed_at',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'placed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

public function items(): HasMany
{
    return $this->hasMany(
        OrderItem::class,
        'order_id'
    );
}

public function returns(): HasMany
{
    return $this->hasMany(ProductReturn::class);
    
}
public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

public function refunds(): HasMany
{
    return $this->hasMany(Refund::class);
}
}