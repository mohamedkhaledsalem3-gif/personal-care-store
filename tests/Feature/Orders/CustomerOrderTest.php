<?php

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CustomerOrderTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(OrderService::class);
    }

    /**
     * إنشاء Variant صالح للاختبارات.
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
            [
                'product_variant_id' => $variant->id,
            ],
            [
                'quantity' => $quantity,
                'reserved_quantity' => 0,
                'low_stock_threshold' => 5,
            ]
        );

        return $variant->fresh();
    }

    /**
     * إنشاء Order مملوك للمستخدم المحدد.
     */
    protected function createOrderForUser(
        User $user,
        int $quantity = 1
    ): Order {
        $variant = $this->makeVariant(
            quantity: 10,
            price: 100
        );

        $cart = Cart::firstOrCreate([
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
                'customer_name' => $user->name,
                'customer_phone' => '01000000000',
                'shipping_address' => '123 Test Street',
                'shipping_city' => 'Cairo',
                'shipping_area' => 'Nasr City',
            ],
            paymentMethod: 'cod',
        );
    }

    /**
     * الزائر لا يستطيع الوصول إلى قائمة الطلبات.
     */
    public function test_guest_cannot_access_customer_orders(): void
    {
        $response = $this->get(
            route('storefront.orders.index')
        );

        $response->assertRedirect(route('login'));
    }

    /**
     * العميل يستطيع عرض قائمة طلباته.
     */
    public function test_authenticated_customer_can_list_his_orders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $orderOne = $this->createOrderForUser($user);
        $orderTwo = $this->createOrderForUser($user);

        $response = $this->get(
            route('storefront.orders.index')
        );

        $response->assertOk();

        $response->assertViewIs(
            'storefront.orders.index'
        );

        $response->assertViewHas('orders');

        $orders = $response->viewData('orders');

        $this->assertTrue(
            $orders->getCollection()->contains(
                fn (Order $order) => $order->id === $orderOne->id
            )
        );

        $this->assertTrue(
            $orders->getCollection()->contains(
                fn (Order $order) => $order->id === $orderTwo->id
            )
        );
    }

    /**
     * العميل يستطيع عرض تفاصيل طلبه.
     */
    public function test_customer_can_view_his_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $response = $this->get(
            route(
                'storefront.orders.show',
                $order
            )
        );

        $response->assertOk();

        $response->assertViewIs(
            'storefront.orders.show'
        );

        $response->assertViewHas(
            'order',
            fn (Order $viewOrder) =>
                $viewOrder->id === $order->id
        );
    }

    /**
     * العميل لا يستطيع فتح Order يخص مستخدمًا آخر.
     */
    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $order = $this->createOrderForUser($owner);

        $this->actingAs($otherUser);

        $response = $this->get(
            route(
                'storefront.orders.show',
                $order
            )
        );

        $response->assertForbidden();
    }

    /**
     * العميل يستطيع إلغاء طلب Pending.
     */
    public function test_customer_can_cancel_pending_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $this->assertSame(
            OrderStatus::PENDING->value,
            $order->status
        );

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            ),
            [
                'reason' => 'Customer requested cancellation',
            ]
        );

        $response->assertRedirect(
            route(
                'storefront.orders.show',
                $order
            )
        );

        $order->refresh();

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );

        $this->assertNotNull(
            $order->cancelled_at
        );

        $this->assertStringContainsString(
            'Customer requested cancellation',
            $order->customer_note
        );
    }

    /**
     * العميل يستطيع إلغاء طلب Confirmed.
     */
    public function test_customer_can_cancel_confirmed_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $order = $this->orderService->confirm($order);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertRedirect();

        $order->refresh();

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );
    }

    /**
     * العميل يستطيع إلغاء طلب Processing.
     */
    public function test_customer_can_cancel_processing_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $order = $this->orderService->confirm($order);
        $order = $this->orderService->process($order);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertRedirect();

        $order->refresh();

        $this->assertSame(
            OrderStatus::CANCELLED->value,
            $order->status
        );
    }

    /**
     * العميل لا يستطيع إلغاء طلب Shipped.
     */
    public function test_customer_cannot_cancel_shipped_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $order = $this->orderService->confirm($order);
        $order = $this->orderService->process($order);
        $order = $this->orderService->ship($order);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertForbidden();

        $order->refresh();

        $this->assertSame(
            OrderStatus::SHIPPED->value,
            $order->status
        );
    }

    /**
     * العميل لا يستطيع إلغاء طلب Delivered.
     */
    public function test_customer_cannot_cancel_delivered_order(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $order = $this->createOrderForUser($user);

        $order = $this->orderService->confirm($order);
        $order = $this->orderService->process($order);
        $order = $this->orderService->ship($order);
        $order = $this->orderService->deliver($order);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertForbidden();

        $order->refresh();

        $this->assertSame(
            OrderStatus::DELIVERED->value,
            $order->status
        );
    }

    /**
     * العميل لا يستطيع إلغاء طلب مستخدم آخر.
     */
    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $order = $this->createOrderForUser($owner);

        $this->actingAs($otherUser);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertForbidden();

        $order->refresh();

        $this->assertSame(
            OrderStatus::PENDING->value,
            $order->status
        );
    }

    /**
     * المستخدم غير المسجل لا يستطيع فتح تفاصيل الطلب.
     */
    public function test_guest_cannot_view_order_details(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrderForUser($user);

        $response = $this->get(
            route(
                'storefront.orders.show',
                $order
            )
        );

        $response->assertRedirect(route('login'));
    }

    /**
     * المستخدم غير المسجل لا يستطيع إلغاء الطلب.
     */
    public function test_guest_cannot_cancel_order(): void
    {
        $user = User::factory()->create();

        $order = $this->createOrderForUser($user);

        $response = $this->delete(
            route(
                'storefront.orders.cancel',
                $order
            )
        );

        $response->assertRedirect(route('login'));

        $order->refresh();

        $this->assertSame(
            OrderStatus::PENDING->value,
            $order->status
        );
    }
}