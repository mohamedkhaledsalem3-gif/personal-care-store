<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\ProductReturn;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * ReturnService
 *
 * دورة الإرجاع:
 *
 * PENDING
 *    ├──→ APPROVED ──→ RECEIVED ──→ COMPLETED (يعيد المخزون فعليًا)
 *    └──→ REJECTED
 *
 * ملاحظة مهمة: على عكس إلغاء الطلب (الذي يحرر حجزًا فقط)، إتمام
 * الإرجاع (complete) يزيد quantity الفعلي في المخزون، لأن المنتج
 * ده فعليًا رجع تاني للمخزن بعد ما كان اتباع (deducted) وقت التسليم.
 */
class ReturnService
{
    /**
     * إنشاء طلب إرجاع لطلب تم تسليمه.
     */
    public function requestReturn(
        Order $order,
        int $userId,
        array $items, // [['order_item_id' => x, 'product_variant_id' => y, 'quantity' => z, 'reason' => ...], ...]
        ?string $reason = null,
        ?string $customerNote = null,
    ): ProductReturn {
        return DB::transaction(function () use ($order, $userId, $items, $reason, $customerNote) {

            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== OrderStatus::DELIVERED->value) {
                throw ValidationException::withMessages([
                    'order' => 'يمكن طلب الإرجاع فقط لطلب تم تسليمه بالفعل.',
                ]);
            }

            if (empty($items)) {
                throw ValidationException::withMessages([
                    'items' => 'يجب اختيار منتج واحد على الأقل للإرجاع.',
                ]);
            }

            $productReturn = ProductReturn::create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'return_number' => $this->generateReturnNumber(),
                'status' => ReturnStatus::PENDING->value,
                'reason' => $reason,
                'customer_note' => $customerNote,
                'requested_at' => now(),
            ]);

            foreach ($items as $item) {
                $orderItem = $order->items()
                    ->whereKey($item['order_item_id'])
                    ->firstOrFail();

                if ((int) $item['quantity'] > (int) $orderItem->quantity) {
                    throw ValidationException::withMessages([
                        'items' => "كمية الإرجاع لعنصر {$orderItem->sku} أكبر من كمية الطلب الأصلية.",
                    ]);
                }

                $productReturn->items()->create([
                    'order_item_id' => $orderItem->id,
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'reason' => $item['reason'] ?? null,
                ]);
            }

            return $productReturn->fresh('items');
        });
    }

    /**
     * موافقة الإدارة على طلب الإرجاع.
     *
     * pending -> approved
     */
    public function approve(ProductReturn $return, ?string $adminNote = null): ProductReturn
    {
        return DB::transaction(function () use ($return, $adminNote) {

            $return = $this->lockReturn($return);

            $this->ensureStatus($return, [ReturnStatus::PENDING]);

            $return->update([
                'status' => ReturnStatus::APPROVED->value,
                'admin_note' => $adminNote,
                'approved_at' => now(),
            ]);

            return $return->fresh();
        });
    }

    /**
     * رفض طلب الإرجاع.
     *
     * pending -> rejected
     */
    public function reject(ProductReturn $return, ?string $adminNote = null): ProductReturn
    {
        return DB::transaction(function () use ($return, $adminNote) {

            $return = $this->lockReturn($return);

            $this->ensureStatus($return, [ReturnStatus::PENDING]);

            $return->update([
                'status' => ReturnStatus::REJECTED->value,
                'admin_note' => $adminNote,
                'rejected_at' => now(),
            ]);

            return $return->fresh();
        });
    }

    /**
     * تسجيل استلام المنتجات المرتجعة فعليًا في المخزن (قبل فحصها/اعتمادها نهائيًا).
     *
     * approved -> received
     */
    public function markReceived(ProductReturn $return): ProductReturn
    {
        return DB::transaction(function () use ($return) {

            $return = $this->lockReturn($return);

            $this->ensureStatus($return, [ReturnStatus::APPROVED]);

            $return->update([
                'status' => ReturnStatus::RECEIVED->value,
                'received_at' => now(),
            ]);

            return $return->fresh();
        });
    }

    /**
     * إتمام الإرجاع: إعادة الكمية فعليًا إلى المخزون.
     *
     * received -> completed
     *
     * على عكس تحرير حجز (release)، هذا يزيد quantity الفعلي،
     * لأن الكمية كانت قد خُصمت فعليًا وقت التسليم (sale).
     */
    public function complete(ProductReturn $return): ProductReturn
    {
        return DB::transaction(function () use ($return) {

            $return = $this->lockReturn($return);

            $this->ensureStatus($return, [ReturnStatus::RECEIVED]);

            $return->load('items');

            foreach ($return->items as $item) {

                $inventory = Inventory::query()
                    ->where('product_variant_id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw ValidationException::withMessages([
                        'inventory' => "لا يوجد سجل مخزون للـ Product Variant رقم {$item->product_variant_id}.",
                    ]);
                }

                $quantityBefore = $inventory->quantity;
                $quantityAfter = $quantityBefore + $item->quantity;

                $inventory->update([
                    'quantity' => $quantityAfter,
                ]);

                InventoryMovement::create([
                    'inventory_id' => $inventory->id,
                    'type' => 'return',
                    'quantity' => $item->quantity,
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $quantityAfter,
                    'reference_type' => ProductReturn::class,
                    'reference_id' => $return->id,
                    'note' => "إعادة مخزون بعد اعتماد الإرجاع {$return->return_number}",
                    'created_by' => auth()->id(),
                ]);
            }

            $return->update([
                'status' => ReturnStatus::COMPLETED->value,
                'completed_at' => now(),
            ]);

            return $return->fresh('items');
        });
    }

    protected function lockReturn(ProductReturn $return): ProductReturn
    {
        return ProductReturn::query()
            ->whereKey($return->id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    protected function ensureStatus(ProductReturn $return, array $allowedStatuses): void
    {
        $current = ReturnStatus::from($return->status);

        $allowedValues = collect($allowedStatuses)
            ->map(fn ($status) => $status instanceof ReturnStatus ? $status->value : $status)
            ->all();

        if (! in_array($current->value, $allowedValues, true)) {
            throw ValidationException::withMessages([
                'status' => "لا يمكن تنفيذ العملية على الإرجاع {$return->return_number} لأن حالته الحالية هي: {$current->label()}.",
            ]);
        }
    }

    protected function generateReturnNumber(): string
    {
        do {
            $number = 'RT-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 6));
        } while (ProductReturn::query()->where('return_number', $number)->exists());

        return $number;
    }
}
