<?php

namespace App\Services;

use App\Enums\InventoryMovementType;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryService
{
    /**
     * إضافة كمية إلى المخزون.
     */
    public function increase(
        ProductVariant $variant,
        int $quantity,
        ?string $note = null,
        ?int $createdBy = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Inventory {
        $this->validateQuantity($quantity);

        return DB::transaction(function () use (
            $variant,
            $quantity,
            $note,
            $createdBy,
            $referenceType,
            $referenceId
        ) {
            $inventory = $this->getLockedInventory($variant);

            $before = $inventory->quantity;
            $after = $before + $quantity;

            $inventory->update([
                'quantity' => $after,
            ]);

            $this->recordMovement(
                inventory: $inventory,
                type: InventoryMovementType::IN,
                quantity: $quantity,
                quantityBefore: $before,
                quantityAfter: $after,
                note: $note,
                createdBy: $createdBy,
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            return $inventory->refresh();
        });
    }

    /**
     * خصم كمية من المخزون المتاح.
     */
    public function decrease(
        ProductVariant $variant,
        int $quantity,
        ?string $note = null,
        ?int $createdBy = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Inventory {
        $this->validateQuantity($quantity);

        return DB::transaction(function () use (
            $variant,
            $quantity,
            $note,
            $createdBy,
            $referenceType,
            $referenceId
        ) {
            $inventory = $this->getLockedInventory($variant);

            if ($inventory->available_quantity < $quantity) {
                throw new RuntimeException(
                    "الكمية المتاحة غير كافية للمنتج: {$variant->name}"
                );
            }

            $before = $inventory->quantity;
            $after = $before - $quantity;

            $inventory->update([
                'quantity' => $after,
            ]);

            $this->recordMovement(
                inventory: $inventory,
                type: InventoryMovementType::OUT,
                quantity: -$quantity,
                quantityBefore: $before,
                quantityAfter: $after,
                note: $note,
                createdBy: $createdBy,
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            return $inventory->refresh();
        });
    }

    /**
     * حجز كمية من المخزون.
     */
    public function reserve(
        ProductVariant $variant,
        int $quantity,
        ?string $note = null,
        ?int $createdBy = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Inventory {
        $this->validateQuantity($quantity);

        return DB::transaction(function () use (
            $variant,
            $quantity,
            $note,
            $createdBy,
            $referenceType,
            $referenceId
        ) {
            $inventory = $this->getLockedInventory($variant);

            if ($inventory->available_quantity < $quantity) {
                throw new RuntimeException(
                    "الكمية المتاحة غير كافية لحجز المنتج: {$variant->name}"
                );
            }

            $before = $inventory->quantity;
            $after = $before;

            $inventory->update([
                'reserved_quantity' =>
                    $inventory->reserved_quantity + $quantity,
            ]);

            $this->recordMovement(
                inventory: $inventory,
                type: InventoryMovementType::RESERVE,
                quantity: $quantity,
                quantityBefore: $before,
                quantityAfter: $after,
                note: $note,
                createdBy: $createdBy,
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            return $inventory->refresh();
        });
    }

    /**
     * تحرير كمية محجوزة.
     */
    public function release(
        ProductVariant $variant,
        int $quantity,
        ?string $note = null,
        ?int $createdBy = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Inventory {
        $this->validateQuantity($quantity);

        return DB::transaction(function () use (
            $variant,
            $quantity,
            $note,
            $createdBy,
            $referenceType,
            $referenceId
        ) {
            $inventory = $this->getLockedInventory($variant);

            if ($inventory->reserved_quantity < $quantity) {
                throw new RuntimeException(
                    "الكمية المحجوزة غير كافية لتحريرها: {$variant->name}"
                );
            }

            $before = $inventory->quantity;
            $after = $before;

            $inventory->update([
                'reserved_quantity' =>
                    $inventory->reserved_quantity - $quantity,
            ]);

            $this->recordMovement(
                inventory: $inventory,
                type: InventoryMovementType::RELEASE,
                quantity: $quantity,
                quantityBefore: $before,
                quantityAfter: $after,
                note: $note,
                createdBy: $createdBy,
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            return $inventory->refresh();
        });
    }

    /**
     * تسوية المخزون إلى كمية محددة.
     */
    public function adjust(
        ProductVariant $variant,
        int $newQuantity,
        ?string $note = null,
        ?int $createdBy = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
    ): Inventory {
        if ($newQuantity < 0) {
            throw new RuntimeException(
                'لا يمكن أن تكون كمية المخزون أقل من صفر.'
            );
        }

        return DB::transaction(function () use (
            $variant,
            $newQuantity,
            $note,
            $createdBy,
            $referenceType,
            $referenceId
        ) {
            $inventory = $this->getLockedInventory($variant);

            if ($newQuantity < $inventory->reserved_quantity) {
                throw new RuntimeException(
                    'لا يمكن جعل المخزون أقل من الكمية المحجوزة.'
                );
            }

            $before = $inventory->quantity;
            $after = $newQuantity;

            $inventory->update([
                'quantity' => $after,
            ]);

            $this->recordMovement(
                inventory: $inventory,
                type: InventoryMovementType::ADJUSTMENT,
                quantity: $after - $before,
                quantityBefore: $before,
                quantityAfter: $after,
                note: $note,
                createdBy: $createdBy,
                referenceType: $referenceType,
                referenceId: $referenceId,
            );

            return $inventory->refresh();
        });
    }

    /**
     * الحصول على Inventory مع قفل الصف أثناء المعاملة.
     */
    protected function getLockedInventory(ProductVariant $variant): Inventory
    {
        return Inventory::query()
            ->where('product_variant_id', $variant->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    /**
     * تسجيل حركة المخزون.
     */
    protected function recordMovement(
        Inventory $inventory,
        InventoryMovementType $type,
        int $quantity,
        int $quantityBefore,
        int $quantityAfter,
        ?string $note,
        ?int $createdBy,
        ?string $referenceType,
        ?int $referenceId,
    ): InventoryMovement {
        return $inventory->movements()->create([
            'type' => $type,
            'quantity' => $quantity,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'note' => $note,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * التحقق من أن الكمية موجبة.
     */
    protected function validateQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new RuntimeException(
                'يجب أن تكون الكمية أكبر من صفر.'
            );
        }
    }
}