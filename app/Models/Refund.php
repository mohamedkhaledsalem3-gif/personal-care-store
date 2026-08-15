<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = [
        'order_id',
        'return_id',
        'payment_id',
        'refund_number',
        'status',
        'amount',
        'method',
        'transaction_id',
        'reason',
        'requested_at',
        'processed_at',
        'completed_at',
        'failed_at',
        'failure_reason',
        'created_by',
    ];

    protected $casts = [
        'status' => RefundStatus::class,
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function productReturn(): BelongsTo
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}