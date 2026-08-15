<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case IN = 'in';
    case OUT = 'out';
    case RESERVE = 'reserve';
    case RELEASE = 'release';
    case ADJUSTMENT = 'adjustment';
    case SALE = 'sale';

    public function label(): string
    {
        return match ($this) {
            self::IN => 'إضافة مخزون',
            self::OUT => 'خصم مخزون',
            self::RESERVE => 'حجز مخزون',
            self::RELEASE => 'تحرير حجز',
            self::ADJUSTMENT => 'تعديل المخزون',
            self::SALE => 'بيع',
        };
    }
}