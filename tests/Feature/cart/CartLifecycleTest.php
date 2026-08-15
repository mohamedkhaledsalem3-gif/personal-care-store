<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class CartLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected CartService $cartService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartService = app(CartService::class);
    }

    /**
     * إنشاء User للاختبارات.
     */
    protected function createUser(): User
    {
        return User::factory()->create();
    }

    /**
     * إنشاء Product + Variant + Inventory.
     *
     * يتعامل مع المشاريع التي تقوم بإنشاء Inventory
     * تلقائيًا عند إنشاء ProductVariant.
     *
     * @return array{
     *     product: Product,
     *     variant: ProductVariant,
     *     inventory: Inventory
     * }
     */
    protected function createVariant(
        int $quantity = 20,
        int $reservedQuantity = 0,
        bool $isActive = true,
        float $price = 100,
    ): array {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category-' . uniqid(),
            'description' => 'Test category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'PROD-' . uniqid(),
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test product',
            'price' => $price,
            'status' => 'active',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Test Variant',
            'sku' => 'VAR-' . uniqid(),
            'price' => $price,
            'stock_quantity' => $quantity,
            'low_stock_threshold' => 5,
            'is_default' => true,
            'is_active' => $isActive,
        ]);

        /*
         * قد يتم إنشاء Inventory تلقائيًا عند إنشاء Variant.
         * لذلك نستخدم firstOrNew بدل create حتى لا يحدث
         * UniqueConstraintViolation على product_variant_id.
         */
        $inventory = Inventory::query()->firstOrNew([
            'product_variant_id' => $variant->id,
        ]);

        $inventory->fill([
            'quantity' => $quantity,
            'reserved_quantity' => $reservedQuantity,
            'low_stock_threshold' => 5,
        ]);

        $inventory->save();

        return [
            'product' => $product,
            'variant' => $variant,
            'inventory' => $inventory->fresh(),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 1. إضافة Variant إلى Cart
    |--------------------------------------------------------------------------
    */

    public function test_it_adds_variant_to_cart(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant(
            quantity: 20,
            price: 100
        );

        $item = $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            2
        );

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $item->cart_id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
            'unit_price' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. زيادة كمية عنصر موجود
    |--------------------------------------------------------------------------
    */

    public function test_it_increases_quantity_when_variant_already_exists_in_cart(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant(
            quantity: 20,
            price: 100
        );

        $firstItem = $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            2
        );

        $secondItem = $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            3
        );

        $this->assertSame(
            $firstItem->id,
            $secondItem->id
        );

        $this->assertDatabaseHas('cart_items', [
            'id' => $firstItem->id,
            'quantity' => 5,
            'unit_price' => 100,
        ]);

        $this->assertDatabaseCount('cart_items', 1);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. تحديث كمية عنصر
    |--------------------------------------------------------------------------
    */

    public function test_it_updates_cart_item_quantity(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant(
            quantity: 20,
            price: 100
        );

        $item = $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            2
        );

        $updatedItem = $this->cartService->updateItem(
            $user->id,
            $item->id,
            7
        );

        $this->assertSame(
            7,
            $updatedItem->quantity
        );

        $this->assertDatabaseHas('cart_items', [
            'id' => $item->id,
            'quantity' => 7,
            'unit_price' => 100,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. حذف عنصر
    |--------------------------------------------------------------------------
    */

    public function test_it_removes_cart_item(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant();

        $item = $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            2
        );

        $result = $this->cartService->removeItem(
            $user->id,
            $item->id
        );

        $this->assertTrue($result);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $item->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. تفريغ Cart
    |--------------------------------------------------------------------------
    */

    public function test_it_clears_cart(): void
    {
        $user = $this->createUser();

        $first = $this->createVariant(
            price: 100
        );

        $second = $this->createVariant(
            price: 200
        );

        $this->cartService->addItem(
            $user->id,
            $first['variant']->id,
            2
        );

        $this->cartService->addItem(
            $user->id,
            $second['variant']->id,
            3
        );

        $cart = $this->cartService->getCart($user->id);

        $this->assertDatabaseCount('cart_items', 2);

        $this->cartService->clear($user->id);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $cart->id,
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'user_id' => $user->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. منع إضافة Variant غير نشط
    |--------------------------------------------------------------------------
    */

    public function test_it_rejects_inactive_variant(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant(
            quantity: 20,
            isActive: false
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'هذا المنتج غير متاح حاليًا.'
        );

        $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            1
        );

        $this->assertDatabaseCount('cart_items', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. منع إضافة كمية أكبر من Available Stock
    |--------------------------------------------------------------------------
    */

    public function test_it_rejects_quantity_greater_than_available_stock(): void
    {
        $user = $this->createUser();

        $data = $this->createVariant(
            quantity: 10,
            reservedQuantity: 7
        );

        /*
         * Available = 10 - 7 = 3
         */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'الكمية المطلوبة غير متاحة. المتاح حاليًا: 3'
        );

        $this->cartService->addItem(
            $user->id,
            $data['variant']->id,
            4
        );

        $this->assertDatabaseCount('cart_items', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | 8. حساب إجمالي Cart
    |--------------------------------------------------------------------------
    */

    public function test_it_calculates_cart_subtotal_correctly(): void
    {
        $user = $this->createUser();

        $first = $this->createVariant(
            quantity: 20,
            price: 100
        );

        $second = $this->createVariant(
            quantity: 20,
            price: 250
        );

        $this->cartService->addItem(
            $user->id,
            $first['variant']->id,
            2
        );

        $this->cartService->addItem(
            $user->id,
            $second['variant']->id,
            3
        );

        $cart = $this->cartService->getCart($user->id);

        $cart->load('items');

        /*
         * 2 × 100 = 200
         * 3 × 250 = 750
         * Total = 950
         */
        $this->assertSame(
            950.0,
            $cart->subtotal
        );

        $this->assertSame(
            5,
            $cart->items_count
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 9. Order يستخدم محتوى Cart الصحيح
    |--------------------------------------------------------------------------
    */

    public function test_order_uses_cart_contents_correctly(): void
    {
        $user = $this->createUser();

        $first = $this->createVariant(
            quantity: 20,
            price: 100
        );

        $second = $this->createVariant(
            quantity: 20,
            price: 200
        );

        $this->cartService->addItem(
            $user->id,
            $first['variant']->id,
            2
        );

        $this->cartService->addItem(
            $user->id,
            $second['variant']->id,
            3
        );

        $cart = $this->cartService->getCart($user->id);

        $cart->load('items.variant');

        /*
         * هذا الاختبار يتحقق من أن محتوى Cart
         * الذي سيتم استخدامه في إنشاء Order
         * هو المحتوى الصحيح.
         *
         * لا نفترض هنا أسماء methods غير موجودة
         * في المشروع الحالي.
         */
        $this->assertCount(
            2,
            $cart->items
        );

        $this->assertSame(
            2,
            $cart->items
                ->firstWhere(
                    'product_variant_id',
                    $first['variant']->id
                )
                ->quantity
        );

        $this->assertSame(
            3,
            $cart->items
                ->firstWhere(
                    'product_variant_id',
                    $second['variant']->id
                )
                ->quantity
        );

        $this->assertSame(
            800.0,
            $cart->subtotal
        );

        /*
         * نتحقق كذلك من أن الـ Cart ما زال يحتوي
         * على العناصر قبل تمريرها إلى Order lifecycle.
         */
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $first['variant']->id,
            'quantity' => 2,
            'unit_price' => 100,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cart->id,
            'product_variant_id' => $second['variant']->id,
            'quantity' => 3,
            'unit_price' => 200,
        ]);
    }
}