<?php


namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(OrderService::class);
    }

    /**
     * إنشاء Product Variant مع Inventory.
     */
    protected function makeVariant(
        int $quantity = 10,
        float $price = 100
    ): ProductVariant {
        $category = Category::create([
            'name' => 'Hair Care',
            'slug' => 'hair-care-' . uniqid(),
        ]);

        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand-' . uniqid(),
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Test Shampoo',
            'slug' => 'shampoo-' . uniqid(),
            'sku' => 'SKU-' . uniqid(),
            'price' => $price,
            'status' => 'active',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => '400 ml',
            'sku' => 'VAR-' . uniqid(),
            'price' => $price,
            'stock_quantity' => $quantity,
            'is_active' => true,
        ]);

        Inventory::query()->updateOrCreate(
            ['product_variant_id' => $variant->id],
            [
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
            ]
        );

        return $variant->fresh();
    }

    /**
     * إنشاء Order في حالة PENDING من Variant واحد.
     */
    protected function createPendingOrder(
        ProductVariant $variant,
        int $quantity = 2
    ): Order {
        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'unit_price' => $variant->current_price,
        ]);

        return $this->orderService->createFromCart(
            userId: $user->id,
            customerData: [
                'customer_name' => 'Test Customer',
                'customer_phone' => '01000000000',
                'shipping_address' => '123 Test St',
            ],
        );
    }

    /**
     * إنشاء Order يحتوي على Variantين مختلفين.
     */
    protected function createTwoVariantOrder(
        int $quantity = 1
    ): array {
        $variantOne = $this->makeVariant(quantity: 10);
        $variantTwo = $this->makeVariant(quantity: 10);

        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantOne->id,
            'quantity' => $quantity,
            'unit_price' => $variantOne->current_price,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantTwo->id,
            'quantity' => $quantity,
            'unit_price' => $variantTwo->current_price,
        ]);

        $order = $this->orderService->createFromCart(
            userId: $user->id,
            customerData: [
                'customer_name' => 'Test Customer',
                'customer_phone' => '01000000000',
                'shipping_address' => '123 Test St',
            ],
        );

        return [
            $order,
            $variantOne,
            $variantTwo,
        ];
    }

    #[Test]
    public function creating_an_order_reserves_inventory_without_touching_quantity(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder($variant, quantity: 3);

        $inventory = Inventory::where(
            'product_variant_id',
            $variant->id
        )->firstOrFail();

        $this->assertSame(
            OrderStatus::PENDING->value,
            $order->status
        );

        $this->assertSame(10, $inventory->quantity);
        $this->assertSame(3, $inventory->reserved_quantity);

        $this->assertSame(
            7,
            $inventory->quantity - $inventory->reserved_quantity
        );
    }

    #[Test]
    public function full_happy_path_pending_to_delivered_deducts_stock_and_releases_reservation(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder($variant, quantity: 3);

        $order = $this->orderService->confirm($order);

        $this->assertSame(
            OrderStatus::CONFIRMED->value,
            $order->status
        );

        $order = $this->orderService->process($order);

        $this->assertSame(
            OrderStatus::PROCESSING->value,
            $order->status
        );

        $order = $this->orderService->ship($order);

        $this->assertSame(
            OrderStatus::SHIPPED->value,
            $order->status
        );

        $order = $this->orderService->deliver($order);

        $this->assertSame(
            OrderStatus::DELIVERED->value,
            $order->status
        );

        $this->assertNotNull($order->completed_at);

        $inventory = Inventory::where(
            'product_variant_id',
            $variant->id
        )->firstOrFail();

        $this->assertSame(7, $inventory->quantity);
        $this->assertSame(0, $inventory->reserved_quantity);
    }

    #[Test]
    public function cancelling_a_pending_order_releases_reservation_without_reducing_stock(): void
    {
        $variant = $this->makeVariant(quantity: 48);

        $order = $this->createPendingOrder($variant, quantity: 2);

        $inventory = Inventory::where(
            'product_variant_id',
            $variant->id
        )->firstOrFail();

        $this->assertSame(48, $inventory->quantity);
        $this->assertSame(2, $inventory->reserved_quantity);

        $order = $this->orderService->cancel(
            $order,
            'العميل غير رأيه'
        );

        $inventory->refresh();

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );

        $this->assertNotNull($order->cancelled_at);

        $this->assertSame(48, $inventory->quantity);
        $this->assertSame(0, $inventory->reserved_quantity);

        $this->assertSame(
            48,
            $inventory->quantity - $inventory->reserved_quantity
        );

        $this->assertStringContainsString(
            'العميل غير رأيه',
            $order->customer_note
        );
    }

    #[Test]
    public function cancelling_a_confirmed_order_is_allowed(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->cancel($order);

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );
    }

    #[Test]
    public function cancelling_a_processing_order_is_allowed(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->cancel($order);

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );
    }

    #[Test]
    public function cancelling_a_shipped_order_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->ship($order);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->cancel($order);
    }

    #[Test]
    public function cancelling_a_delivered_order_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->ship($order);

        $order = $this->orderService->deliver($order);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->cancel($order);
    }

    #[Test]
    public function delivering_a_delivered_order_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->ship($order);

        $order = $this->orderService->deliver($order);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->deliver($order);
    }

    #[Test]
    public function shipping_a_pending_order_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->ship($order);
    }

    #[Test]
    public function processing_a_pending_order_without_confirm_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->process($order);
    }

    #[Test]
    public function confirming_an_already_confirmed_order_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 1
        );

        $order = $this->orderService->confirm($order);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->confirm($order);
    }

    #[Test]
    public function creating_an_order_with_insufficient_stock_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 2);

        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
            'unit_price' => $variant->current_price,
        ]);

        $this->expectException(
            ValidationException::class
        );

        try {
            $this->orderService->createFromCart(
                userId: $user->id,
                customerData: [
                    'customer_name' => 'Test Customer',
                    'customer_phone' => '01000000000',
                    'shipping_address' => '123 Test St',
                ],
            );
        } finally {
            $inventory = Inventory::where(
                'product_variant_id',
                $variant->id
            )->firstOrFail();

            $this->assertSame(
                2,
                $inventory->quantity
            );

            $this->assertSame(
                0,
                $inventory->reserved_quantity
            );
        }
    }

    #[Test]
    public function creating_an_order_for_an_inactive_variant_is_rejected(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $variant->update([
            'is_active' => false,
        ]);

        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'unit_price' => $variant->current_price,
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->createFromCart(
            userId: $user->id,
            customerData: [
                'customer_name' => 'Test Customer',
                'customer_phone' => '01000000000',
                'shipping_address' => '123 Test St',
            ],
        );
    }

    #[Test]
    public function creating_an_order_with_an_empty_cart_is_rejected(): void
    {
        $user = User::factory()->create();

        Cart::create([
            'user_id' => $user->id,
        ]);

        $this->expectException(
            ValidationException::class
        );

        $this->orderService->createFromCart(
            userId: $user->id,
            customerData: [
                'customer_name' => 'Test Customer',
                'customer_phone' => '01000000000',
                'shipping_address' => '123 Test St',
            ],
        );
    }

    #[Test]
    public function order_creates_an_inventory_movement_audit_trail(): void
    {
        $variant = $this->makeVariant(quantity: 10);

        $order = $this->createPendingOrder(
            $variant,
            quantity: 2
        );

        $this->assertDatabaseHas(
            'inventory_movements',
            [
                'type' => 'reserve',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'quantity' => 2,
            ]
        );

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->ship($order);

        $order = $this->orderService->deliver($order);

        $this->assertDatabaseHas(
            'inventory_movements',
            [
                'type' => 'sale',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'quantity' => 2,
            ]
        );

        $this->assertDatabaseHas(
            'inventory_movements',
            [
                'type' => 'release',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
                'quantity' => 2,
            ]
        );
    }

    #[Test]
    public function deliver_rolls_back_all_inventory_changes_when_one_item_fails(): void
    {
        [$order, $variantOne, $variantTwo] =
            $this->createTwoVariantOrder(quantity: 1);

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $order = $this->orderService->ship($order);

        $inventoryOne = Inventory::where(
            'product_variant_id',
            $variantOne->id
        )->firstOrFail();

        $inventoryTwo = Inventory::where(
            'product_variant_id',
            $variantTwo->id
        )->firstOrFail();

        $this->assertSame(
            1,
            $inventoryOne->reserved_quantity
        );

        $this->assertSame(
            1,
            $inventoryTwo->reserved_quantity
        );

        /*
         * نجعل Variant الثاني غير صالح للتسليم
         * بعد أن تم تجهيز الطلب.
         */
        $inventoryTwo->update([
            'reserved_quantity' => 0,
        ]);

        $quantityOneBefore =
            $inventoryOne->fresh()->quantity;

        $reservedOneBefore =
            $inventoryOne->fresh()->reserved_quantity;

        $this->expectException(
            ValidationException::class
        );

        try {
            $this->orderService->deliver($order);
        } finally {
            $inventoryOne->refresh();
            $inventoryTwo->refresh();
            $order->refresh();
        }

        $this->assertSame(
            $quantityOneBefore,
            $inventoryOne->quantity
        );

        $this->assertSame(
            $reservedOneBefore,
            $inventoryOne->reserved_quantity
        );

        $this->assertSame(
            OrderStatus::SHIPPED->value,
            $order->status
        );

        $this->assertNull(
            $order->completed_at
        );

        /*
         * يجب ألا يتم تسجيل sale أو release
         * جديد نتيجة العملية الفاشلة.
         */
        $this->assertDatabaseMissing(
            'inventory_movements',
            [
                'type' => 'sale',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
            ]
        );

        $this->assertDatabaseMissing(
            'inventory_movements',
            [
                'type' => 'release',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
            ]
        );
    }

    #[Test]
    public function cancel_rolls_back_all_inventory_changes_when_one_item_fails(): void
    {
        [$order, $variantOne, $variantTwo] =
            $this->createTwoVariantOrder(quantity: 1);

        $order = $this->orderService->confirm($order);

        $order = $this->orderService->process($order);

        $inventoryOne = Inventory::where(
            'product_variant_id',
            $variantOne->id
        )->firstOrFail();

        $inventoryTwo = Inventory::where(
            'product_variant_id',
            $variantTwo->id
        )->firstOrFail();

        $this->assertSame(
            1,
            $inventoryOne->reserved_quantity
        );

        $this->assertSame(
            1,
            $inventoryTwo->reserved_quantity
        );

        /*
         * نجعل Variant الثاني غير صالح للإلغاء
         * حتى يحدث Exception بعد تحرير Variant الأول.
         */
        $inventoryTwo->update([
            'reserved_quantity' => 0,
        ]);

        $reservedOneBefore =
            $inventoryOne->fresh()->reserved_quantity;

        $this->expectException(
            ValidationException::class
        );

        try {
            $this->orderService->cancel(
                $order,
                'Rollback Test'
            );
        } finally {
            $inventoryOne->refresh();
            $inventoryTwo->refresh();
            $order->refresh();
        }

        /*
         * يجب أن يعود حجز Variant الأول
         * إلى القيمة السابقة.
         */
        $this->assertSame(
            $reservedOneBefore,
            $inventoryOne->reserved_quantity
        );

        /*
         * الطلب يجب أن يبقى Processing.
         */
        $this->assertSame(
            OrderStatus::PROCESSING->value,
            $order->status
        );

        $this->assertNull(
            $order->cancelled_at
        );

        /*
         * لا يجب إنشاء Release جديد
         * نتيجة Transaction فاشلة.
         */
        $this->assertDatabaseMissing(
            'inventory_movements',
            [
                'type' => 'release',
                'reference_type' => Order::class,
                'reference_id' => $order->id,
            ]
        );
    }

    #[Test]
    public function failed_order_creation_does_not_leave_partial_cart_or_order_state(): void
    {
        $variant = $this->makeVariant(quantity: 2);

        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
            'unit_price' => $variant->current_price,
        ]);

        $this->expectException(
            ValidationException::class
        );

        try {
            $this->orderService->createFromCart(
                userId: $user->id,
                customerData: [
                    'customer_name' => 'Test Customer',
                    'customer_phone' => '01000000000',
                    'shipping_address' => '123 Test St',
                ],
            );
        } finally {
            $cart->refresh();

            $inventory = Inventory::where(
                'product_variant_id',
                $variant->id
            )->firstOrFail();
        }

        /*
         * Cart يجب أن تبقى كما هي.
         */
        $this->assertDatabaseHas(
            'cart_items',
            [
                'cart_id' => $cart->id,
                'product_variant_id' => $variant->id,
                'quantity' => 5,
            ]
        );

        /*
         * Inventory يجب ألا يتأثر.
         */
        $this->assertSame(
            2,
            $inventory->quantity
        );

        $this->assertSame(
            0,
            $inventory->reserved_quantity
        );

        /*
         * لا يجب إنشاء Order نتيجة الفشل.
         */
        $this->assertDatabaseCount(
            'orders',
            0
        );
    }

    #[Test]
    public function second_order_cannot_reserve_more_than_available_inventory(): void
    {
        $variant = $this->makeVariant(quantity: 5);

        /*
         * الطلب الأول يحجز 4 من أصل 5.
         */
        $firstOrder = $this->createPendingOrder(
            $variant,
            quantity: 4
        );

        $this->assertSame(
            OrderStatus::PENDING->value,
            $firstOrder->status
        );

        $inventory = Inventory::where(
            'product_variant_id',
            $variant->id
        )->firstOrFail();

        $this->assertSame(
            5,
            $inventory->quantity
        );

        $this->assertSame(
            4,
            $inventory->reserved_quantity
        );

        $this->assertSame(
            1,
            $inventory->quantity - $inventory->reserved_quantity
        );

        /*
         * الطلب الثاني يطلب 2 بينما المتاح 1 فقط.
         */
        $user = User::factory()->create();

        $cart = Cart::create([
            'user_id' => $user->id,
        ]);

        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'unit_price' => $variant->current_price,
        ]);

        $this->expectException(
            ValidationException::class
        );

        try {
            $this->orderService->createFromCart(
                userId: $user->id,
                customerData: [
                    'customer_name' => 'Second Customer',
                    'customer_phone' => '01111111111',
                    'shipping_address' => '456 Test St',
                ],
            );
        } finally {
            $inventory->refresh();
        }

        /*
         * يجب ألا يزيد الحجز عن المتاح.
         */
        $this->assertSame(
            5,
            $inventory->quantity
        );

        $this->assertSame(
            4,
            $inventory->reserved_quantity
        );

        $this->assertSame(
            1,
            $inventory->quantity - $inventory->reserved_quantity
        );

        /*
         * لا يجب إنشاء Order ثاني.
         */
        $this->assertDatabaseCount(
            'orders',
            1
        );
    }
}