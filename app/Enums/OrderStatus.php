<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::CONFIRMED => 'تم التأكيد',
            self::PROCESSING => 'جاري التجهيز',
            self::SHIPPED => 'تم الشحن',
            self::DELIVERED => 'تم التسليم',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'primary',
            self::SHIPPED => 'success',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}