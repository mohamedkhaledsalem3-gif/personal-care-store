<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * قبل إضافة ProductVariantObserver، كان ممكن ينشأ Variant بدون سجل
 * Inventory مقابل. هذه المايجريشن تنشئ السجلات الناقصة لأي Variant
 * موجود بالفعل في قاعدة البيانات، حتى لا يفشل OrderService عليه.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $missing = DB::table('product_variants')
            ->leftJoin('inventories', 'inventories.product_variant_id', '=', 'product_variants.id')
            ->whereNull('inventories.id')
            ->select('product_variants.id', 'product_variants.stock_quantity', 'product_variants.low_stock_threshold')
            ->get();

        foreach ($missing as $variant) {
            DB::table('inventories')->insert([
                'product_variant_id' => $variant->id,
                'quantity' => $variant->stock_quantity ?? 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => $variant->low_stock_threshold ?? 5,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        // لا شيء لعكسه؛ هذه مايجريشن بيانات فقط.
    }
};
