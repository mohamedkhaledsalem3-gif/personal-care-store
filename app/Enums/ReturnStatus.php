<?php

namespace App\Enums;

enum ReturnStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case RECEIVED = 'received';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد المراجعة',
            self::APPROVED => 'تمت الموافقة',
            self::REJECTED => 'مرفوض',
            self::RECEIVED => 'تم الاستلام',
            self::COMPLETED => 'مكتمل',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'info',
            self::REJECTED => 'danger',
            self::RECEIVED => 'primary',
            self::COMPLETED => 'success',
        };
    }
}