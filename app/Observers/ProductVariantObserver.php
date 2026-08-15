<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\ProductVariant;

/**
 * جدول product_variants فيه عمود stock_quantity خاص بيه للعرض/التحرير
 * السريع من لوحة الإدارة، لكن OrderService والـ Cart يعتمدان بالكامل
 * على جدول inventories (quantity / reserved_quantity) كمصدر وحيد للحقيقة.
 *
 * بدون هذا الـ Observer: أي Variant جديد يُنشأ من Filament لن يكون له
 * سجل Inventory، فيفشل أي محاولة لإنشاء Order عليه فورًا.
 *
 * هذا الـ Observer يبقي الجدولين متزامنين تلقائيًا.
 */
class ProductVariantObserver
{
    /**
     * عند إنشاء Variant جديد: أنشئ سجل Inventory مقابل له.
     */
    public function created(ProductVariant $variant): void
    {
        Inventory::query()->firstOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'quantity' => $variant->stock_quantity ?? 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => $variant->low_stock_threshold ?? 5,
            ]
        );
    }

    /**
     * عند تعديل stock_quantity أو low_stock_threshold من نموذج الـ Variant
     * (كما يفعل VariantsRelationManager حاليًا)، نعكس التغيير على
     * سجل الـ Inventory الفعلي بدل ما يفضل الجدولين غير متزامنين.
     *
     * ملاحظة: لا نلمس reserved_quantity هنا أبدًا، لأنه مُدار حصريًا
     * بواسطة OrderService (حجز/تحرير/بيع).
     */
    public function updated(ProductVariant $variant): void
    {
        if (! $variant->wasChanged(['stock_quantity', 'low_stock_threshold'])) {
            return;
        }

        $inventory = Inventory::query()
            ->where('product_variant_id', $variant->id)
            ->first();

        if (! $inventory) {
            $this->created($variant);

            return;
        }

        $updates = [];

        if ($variant->wasChanged('stock_quantity')) {
            $updates['quantity'] = $variant->stock_quantity ?? 0;
        }

        if ($variant->wasChanged('low_stock_threshold')) {
            $updates['low_stock_threshold'] = $variant->low_stock_threshold ?? 5;
        }

        if ($updates !== []) {
            $inventory->update($updates);
        }
    }
}
