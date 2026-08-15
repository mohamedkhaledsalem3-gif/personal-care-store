<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected PaymentService $paymentService = new PaymentService(),
    ) {
    }

    /**
     * إنشاء Order من Cart.
     *
     * يقوم بـ:
     * - التحقق من السلة
     * - التحقق من المخزون
     * - إنشاء الطلب
     * - حفظ Snapshot للمنتج والـVariant والسعر والAttributes
     * - حجز المخزون
     * - تسجيل InventoryMovement
     * - تفريغ السلة
     */
    public function createFromCart(
        int $userId,
        array $customerData,
        string $paymentMethod = 'cod',
        float $shippingFee = 0,
        float $discount = 0
    ): Order {
        return DB::transaction(function () use (
            $userId,
            $customerData,
            $paymentMethod,
            $shippingFee,
            $discount
        ) {

            $cart = Cart::query()
                ->where('user_id', $userId)
                ->with([
                    'items.variant.product',
                    'items.variant.attributeValues.attribute',
                ])
                ->lockForUpdate()
                ->first();

            if (! $cart) {
                throw ValidationException::withMessages([
                    'cart' => 'السلة غير موجودة.',
                ]);
            }

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'السلة فارغة.',
                ]);
            }

            $subtotal = 0;

            /*
             * نتحقق من المخزون أولاً
             * قبل إنشاء الطلب.
             */
            foreach ($cart->items as $cartItem) {

                $variant = $cartItem->variant;

                if (! $variant) {
                    throw ValidationException::withMessages([
                        'cart' => 'يوجد منتج في السلة لم يعد الـ Variant الخاص به موجودًا.',
                    ]);
                }

                if (! $variant->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => "المنتج {$variant->sku} غير متاح حاليًا.",
                    ]);
                }

                $inventory = Inventory::query()
                    ->where('product_variant_id', $variant->id)
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw ValidationException::withMessages([
                        'inventory' => "لا يوجد سجل مخزون للـ SKU {$variant->sku}.",
                    ]);
                }

                $available = $inventory->quantity
                    - $inventory->reserved_quantity;

                if ($cartItem->quantity > $available) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "الكمية المطلوبة من {$variant->sku} غير متاحة. "
                            . "المتاح: {$available}.",
                    ]);
                }

                $unitPrice = (float) $variant->current_price;

                $subtotal += $unitPrice * $cartItem->quantity;
            }

            $total = max(
                0,
                $subtotal + $shippingFee - $discount
            );

            /*
             * إنشاء رقم الطلب.
             */
            $orderNumber = $this->generateOrderNumber();

            /*
             * إنشاء Order.
             */
            $order = Order::create([
                'user_id' => $userId,

                'order_number' => $orderNumber,

                'status' => OrderStatus::PENDING->value,

                'payment_status' => 'pending',

                'payment_method' => $paymentMethod,

                'subtotal' => $subtotal,

                'shipping_fee' => $shippingFee,

                'discount' => $discount,

                'total' => $total,

                'customer_name' =>
                    $customerData['customer_name'] ?? null,

                'customer_phone' =>
                    $customerData['customer_phone'] ?? null,

                'shipping_address' =>
                    $customerData['shipping_address'] ?? null,

                'shipping_city' =>
                    $customerData['shipping_city'] ?? null,

                'shipping_area' =>
                    $customerData['shipping_area'] ?? null,

                'shipping_postal_code' =>
                    $customerData['shipping_postal_code'] ?? null,

                'customer_note' =>
                    $customerData['customer_note'] ?? null,

                'placed_at' => now(),
            ]);

            /*
             * إنشاء Order Items
             * + حجز المخزون.
             */
            foreach ($cart->items as $cartItem) {

                $variant = $cartItem->variant;

                /*
                 * نعيد Lock للمخزون داخل نفس Transaction
                 * لضمان عدم حدوث Race Condition.
                 */
                $inventory = Inventory::query()
                    ->where('product_variant_id', $variant->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $available = $inventory->quantity
                    - $inventory->reserved_quantity;

                if ($cartItem->quantity > $available) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "المخزون تغير أثناء إنشاء الطلب للمنتج {$variant->sku}.",
                    ]);
                }

                $unitPrice = (float) $variant->current_price;

                $itemTotal = $unitPrice * $cartItem->quantity;

                /*
                 * Snapshot للـAttributes.
                 */
                $attributes = $variant->attributeValues
                    ->map(function ($value) {
                        return [
                            'attribute' => $value->attribute?->name,
                            'value' => $value->value,
                        ];
                    })
                    ->values()
                    ->all();

                $order->items()->create([
                    'product_variant_id' => $variant->id,

                    'product_name' =>
                        $variant->product?->name ?? 'منتج',

                    'variant_name' =>
                        $variant->name,

                    'sku' =>
                        $variant->sku,

                    'quantity' =>
                        $cartItem->quantity,

                    'unit_price' =>
                        $unitPrice,

                    'total' =>
                        $itemTotal,

                    'attributes' =>
                        $attributes,
                ]);

                /*
                 * حجز المخزون.
                 */
                $reservedBefore =
                    $inventory->reserved_quantity;

                $reservedAfter =
                    $reservedBefore + $cartItem->quantity;

                $inventory->update([
                    'reserved_quantity' => $reservedAfter,
                ]);

                /*
                 * تسجيل حركة الحجز.
                 */
                InventoryMovement::create([
                    'inventory_id' => $inventory->id,

                    'type' => 'reserve',

                    'quantity' => $cartItem->quantity,

                    'quantity_before' => $reservedBefore,

                    'quantity_after' => $reservedAfter,

                    'reference_type' => Order::class,

                    'reference_id' => $order->id,

                    'note' =>
                        "حجز مخزون للطلب {$order->order_number}",

                    'created_by' => auth()->id(),
                ]);
            }

            /*
             * تفريغ السلة بعد نجاح إنشاء الطلب بالكامل.
             */
            $cart->items()->delete();

            /*
             * إنشاء سجل دفع (pending) مرتبط بالطلب.
             */
            $this->paymentService->createForOrder($order);

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * تأكيد الطلب.
     *
     * pending -> confirmed
     */
    public function confirm(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = $this->lockOrder($order);

            $this->ensureStatus($order, [
                OrderStatus::PENDING,
            ]);

            $order->update([
                'status' => OrderStatus::CONFIRMED->value,
            ]);

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * بدء تجهيز الطلب.
     *
     * confirmed -> processing
     */
    public function process(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = $this->lockOrder($order);

            $this->ensureStatus($order, [
                OrderStatus::CONFIRMED,
            ]);

            $order->update([
                'status' => OrderStatus::PROCESSING->value,
            ]);

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * شحن الطلب.
     *
     * processing -> shipped
     */
    public function ship(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = $this->lockOrder($order);

            $this->ensureStatus($order, [
                OrderStatus::PROCESSING,
            ]);

            $order->update([
                'status' => OrderStatus::SHIPPED->value,
            ]);

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * تسليم الطلب.
     *
     * shipped -> delivered
     *
     * عند التسليم:
     *
     * quantity:
     * 50 -> 48
     *
     * reserved:
     * 2 -> 0
     */
    public function deliver(Order $order): Order
    {
        return DB::transaction(function () use ($order) {

            $order = $this->lockOrder($order);

            $this->ensureStatus($order, [
                OrderStatus::SHIPPED,
            ]);

            $order->load('items');

            foreach ($order->items as $item) {

                $inventory = Inventory::query()
                    ->where(
                        'product_variant_id',
                        $item->product_variant_id
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "لا يوجد مخزون للـ SKU {$item->sku}.",
                    ]);
                }

                $quantityBefore =
                    $inventory->quantity;

                $reservedBefore =
                    $inventory->reserved_quantity;

                /*
                 * يجب أن يكون الحجز كافيًا.
                 */
                if ($reservedBefore < $item->quantity) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "الحجز غير كافٍ للمنتج {$item->sku}.",
                    ]);
                }

                /*
                 * يجب أن يكون المخزون الفعلي كافيًا.
                 */
                if ($quantityBefore < $item->quantity) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "المخزون غير كافٍ للمنتج {$item->sku}.",
                    ]);
                }

                $quantityAfter =
                    $quantityBefore - $item->quantity;

                $reservedAfter =
                    $reservedBefore - $item->quantity;

                $inventory->update([
                    'quantity' => $quantityAfter,

                    'reserved_quantity' => $reservedAfter,
                ]);

                /*
                 * حركة البيع الفعلية.
                 */
                InventoryMovement::create([
                    'inventory_id' => $inventory->id,

                    'type' => 'sale',

                    'quantity' => $item->quantity,

                    'quantity_before' => $quantityBefore,

                    'quantity_after' => $quantityAfter,

                    'reference_type' => Order::class,

                    'reference_id' => $order->id,

                    'note' =>
                        "خصم مخزون لتسليم الطلب {$order->order_number}",

                    'created_by' => auth()->id(),
                ]);

                /*
                 * تحرير الحجز.
                 */
                InventoryMovement::create([
                    'inventory_id' => $inventory->id,

                    'type' => 'release',

                    'quantity' => $item->quantity,

                    'quantity_before' => $reservedBefore,

                    'quantity_after' => $reservedAfter,

                    'reference_type' => Order::class,

                    'reference_id' => $order->id,

                    'note' =>
                        "تحرير حجز بعد تسليم الطلب {$order->order_number}",

                    'created_by' => auth()->id(),
                ]);
            }

            $order->update([
                'status' => OrderStatus::DELIVERED->value,

                'completed_at' => now(),
            ]);

            /*
             * الدفع الحالي COD فقط، لذلك التسليم = تحصيل المبلغ.
             * عند إضافة بوابة دفع إلكترونية لاحقًا، هذا الاستدعاء
             * سيقتصر على طلبات COD فقط.
             */
            $payment = $order->payments()->first();

            if ($payment && $payment->status !== \App\Enums\PaymentStatus::PAID) {
                $this->paymentService->markPaid($payment);
            }

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * إلغاء الطلب.
     *
     * pending / confirmed / processing
     *                         ↓
     *                      cancelled
     *
     * يقوم بتحرير المخزون المحجوز.
     */
    public function cancel(Order $order, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($order, $reason) {

            $order = $this->lockOrder($order);

            $this->ensureStatus($order, [
                OrderStatus::PENDING,
                OrderStatus::CONFIRMED,
                OrderStatus::PROCESSING,
            ]);

            $order->load('items');

            foreach ($order->items as $item) {

                $inventory = Inventory::query()
                    ->where(
                        'product_variant_id',
                        $item->product_variant_id
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $inventory) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "لا يوجد مخزون للـ SKU {$item->sku}.",
                    ]);
                }

                $reservedBefore =
                    $inventory->reserved_quantity;

                if ($reservedBefore < $item->quantity) {
                    throw ValidationException::withMessages([
                        'inventory' =>
                            "الحجز الحالي أقل من كمية الطلب للـ SKU {$item->sku}.",
                    ]);
                }

                $reservedAfter =
                    $reservedBefore - $item->quantity;

                $inventory->update([
                    'reserved_quantity' => $reservedAfter,
                ]);

                InventoryMovement::create([
                    'inventory_id' => $inventory->id,

                    'type' => 'release',

                    'quantity' => $item->quantity,

                    'quantity_before' => $reservedBefore,

                    'quantity_after' => $reservedAfter,

                    'reference_type' => Order::class,

                    'reference_id' => $order->id,

                    'note' =>
                        "تحرير حجز بسبب إلغاء الطلب {$order->order_number}",

                    'created_by' => auth()->id(),
                ]);
            }

            /*
             * لا يوجد عمود مخصص لسبب الإلغاء في جدول orders حاليًا،
             * لذلك نلحق السبب (إن وُجد) بـ customer_note.
             */
            $noteUpdate = [];

            if ($reason !== null && trim($reason) !== '') {
                $cancelNote = 'سبب الإلغاء: ' . trim($reason);

                $noteUpdate['customer_note'] = $order->customer_note
                    ? $order->customer_note . "\n" . $cancelNote
                    : $cancelNote;
            }

            $order->update(array_merge([
                'status' => OrderStatus::CANCELLED->value,

                'cancelled_at' => now(),
            ], $noteUpdate));

            return $order->fresh([
                'items',
                'user',
            ]);
        });
    }


    /**
     * قفل الطلب أثناء Transaction.
     */
    protected function lockOrder(Order $order): Order
    {
        return Order::query()
            ->whereKey($order->id)
            ->lockForUpdate()
            ->firstOrFail();
    }


    /**
     * التأكد من أن حالة الطلب تسمح بالعملية.
     */
    protected function ensureStatus(
        Order $order,
        array $allowedStatuses
    ): void {
        $current = OrderStatus::from($order->status);

        $allowedValues = collect($allowedStatuses)
            ->map(function ($status) {
                return $status instanceof OrderStatus
                    ? $status->value
                    : $status;
            })
            ->all();

        if (! in_array(
            $current->value,
            $allowedValues,
            true
        )) {

            throw ValidationException::withMessages([
                'status' =>
                    "لا يمكن تنفيذ العملية على الطلب "
                    . "{$order->order_number} "
                    . "لأن حالته الحالية هي: "
                    . $current->label(),
            ]);
        }
    }


    /**
     * إنشاء رقم Order فريد.
     */
    protected function generateOrderNumber(): string
    {
        do {
            $number =
                'PC-'
                . now()->format('Ymd')
                . '-'
                . strtoupper(
                    substr(
                        str_shuffle(
                            'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'
                        ),
                        0,
                        6
                    )
                );

        } while (
            Order::query()
                ->where('order_number', $number)
                ->exists()
        );

        return $number;
    }
}