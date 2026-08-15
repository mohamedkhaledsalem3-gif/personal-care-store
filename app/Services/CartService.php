<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartService
{
    /**
     * الحصول على سلة المستخدم أو إنشاؤها.
     */
    public function getCart(int $userId): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $userId,
        ]);
    }

    /**
     * إضافة Variant إلى السلة.
     */
    public function addItem(
        int $userId,
        int $variantId,
        int $quantity = 1
    ): CartItem {
        if ($quantity < 1) {
            throw new RuntimeException(
                'الكمية يجب أن تكون أكبر من صفر.'
            );
        }

        return DB::transaction(function () use (
            $userId,
            $variantId,
            $quantity
        ) {
            $variant = ProductVariant::query()
                ->with('inventory')
                ->lockForUpdate()
                ->findOrFail($variantId);

            if (! $variant->is_active) {
                throw new RuntimeException(
                    'هذا المنتج غير متاح حاليًا.'
                );
            }

            $inventory = $variant->inventory;

            if (! $inventory) {
                throw new RuntimeException(
                    'لا يوجد سجل مخزون لهذا المنتج.'
                );
            }

            $cart = Cart::firstOrCreate([
                'user_id' => $userId,
            ]);

            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_variant_id', $variant->id)
                ->lockForUpdate()
                ->first();

            $existingQuantity = $item?->quantity ?? 0;

            $newQuantity = $existingQuantity + $quantity;

            /*
             * الكمية المتاحة للبيع:
             *
             * quantity - reserved_quantity
             */
            $available = max(
                0,
                $inventory->quantity
                    - $inventory->reserved_quantity
            );

            if ($newQuantity > $available) {
                throw new RuntimeException(
                    "الكمية المطلوبة غير متاحة. المتاح حاليًا: {$available}"
                );
            }

            /*
             * استخدام السعر الحالي للـ Variant.
             */
            $price = $variant->current_price;

            if ($item) {
                $item->update([
                    'quantity' => $newQuantity,
                    'unit_price' => $price,
                ]);
            } else {
                $item = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                ]);
            }

            return $item->fresh([
                'variant',
            ]);
        });
    }

    /**
     * تحديث كمية عنصر في السلة.
     */
    public function updateItem(
        int $userId,
        int $itemId,
        int $quantity
    ): CartItem {
        if ($quantity < 1) {
            throw new RuntimeException(
                'الكمية يجب أن تكون أكبر من صفر.'
            );
        }

        return DB::transaction(function () use (
            $userId,
            $itemId,
            $quantity
        ) {
            $item = CartItem::query()
                ->whereHas('cart', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->with('variant.inventory')
                ->lockForUpdate()
                ->findOrFail($itemId);

            $variant = $item->variant;

            if (! $variant) {
                throw new RuntimeException(
                    'المنتج المرتبط بهذا العنصر غير موجود.'
                );
            }

            if (! $variant->is_active) {
                throw new RuntimeException(
                    'هذا المنتج غير متاح حاليًا.'
                );
            }

            $inventory = $variant->inventory;

            if (! $inventory) {
                throw new RuntimeException(
                    'لا يوجد سجل مخزون لهذا المنتج.'
                );
            }

            $available = max(
                0,
                $inventory->quantity
                    - $inventory->reserved_quantity
            );

            if ($quantity > $available) {
                throw new RuntimeException(
                    "الكمية المطلوبة غير متاحة. المتاح حاليًا: {$available}"
                );
            }

            $item->update([
                'quantity' => $quantity,
                'unit_price' => $variant->current_price,
            ]);

            return $item->fresh([
                'variant',
            ]);
        });
    }

    /**
     * حذف عنصر من السلة.
     */
    public function removeItem(
        int $userId,
        int $itemId
    ): bool {
        return DB::transaction(function () use (
            $userId,
            $itemId
        ) {
            $item = CartItem::query()
                ->whereHas('cart', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->lockForUpdate()
                ->findOrFail($itemId);

            return (bool) $item->delete();
        });
    }

    /**
     * تفريغ السلة بالكامل.
     */
    public function clear(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $cart = Cart::query()
                ->where('user_id', $userId)
                ->first();

            if ($cart) {
                $cart->items()->delete();
            }
        });
    }
}
