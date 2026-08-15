<?php


namespace Tests\Feature\Inventory;

use App\Enums\InventoryMovementType;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected InventoryService $inventoryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inventoryService = app(InventoryService::class);
    }

    protected function createInventory(
        int $quantity = 10,
        int $reservedQuantity = 0,
        int $lowStockThreshold = 5,
    ): Inventory {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category-' . uniqid(),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'PROD-' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test product',
            'price' => 100,
            'status' => 'active',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => 'Test Variant',
            'sku' => 'SKU-' . uniqid(),
            'price' => 100,
            'stock_quantity' => $quantity,
            'low_stock_threshold' => $lowStockThreshold,
            'is_default' => true,
            'is_active' => true,
        ]);

        /*
         * ProductVariant يقوم بإنشاء Inventory تلقائياً.
         * لذلك نستخدم الـ Inventory الموجود بدل إنشاء سجل جديد.
         */
        $inventory = Inventory::where(
            'product_variant_id',
            $variant->id
        )->firstOrFail();

        $inventory->update([
            'quantity' => $quantity,
            'reserved_quantity' => $reservedQuantity,
            'low_stock_threshold' => $lowStockThreshold,
        ]);

        return $inventory->fresh();
    }

    public function test_it_increases_inventory(): void
    {
        $inventory = $this->createInventory(quantity: 10);

        $result = $this->inventoryService->increase(
            $inventory->productVariant,
            5,
        );

        $this->assertSame(15, $result->quantity);
        $this->assertSame(0, $result->reserved_quantity);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 15,
        ]);
    }

    public function test_it_decreases_inventory(): void
    {
        $inventory = $this->createInventory(quantity: 10);

        $result = $this->inventoryService->decrease(
            $inventory->productVariant,
            4,
        );

        $this->assertSame(6, $result->quantity);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 6,
        ]);
    }

    public function test_it_rejects_decrease_when_stock_is_insufficient(): void
    {
        $inventory = $this->createInventory(quantity: 3);

        $this->expectException(RuntimeException::class);

        $this->inventoryService->decrease(
            $inventory->productVariant,
            5,
        );
    }

    public function test_it_reserves_available_inventory(): void
    {
        $inventory = $this->createInventory(
            quantity: 10,
            reservedQuantity: 2,
        );

        $result = $this->inventoryService->reserve(
            $inventory->productVariant,
            5,
        );

        $this->assertSame(10, $result->quantity);
        $this->assertSame(7, $result->reserved_quantity);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 10,
            'reserved_quantity' => 7,
        ]);
    }

    public function test_it_rejects_reservation_when_available_stock_is_insufficient(): void
    {
        $inventory = $this->createInventory(
            quantity: 10,
            reservedQuantity: 8,
        );

        $this->expectException(RuntimeException::class);

        $this->inventoryService->reserve(
            $inventory->productVariant,
            3,
        );
    }

    public function test_it_releases_reserved_inventory(): void
    {
        $inventory = $this->createInventory(
            quantity: 10,
            reservedQuantity: 6,
        );

        $result = $this->inventoryService->release(
            $inventory->productVariant,
            4,
        );

        $this->assertSame(10, $result->quantity);
        $this->assertSame(2, $result->reserved_quantity);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 10,
            'reserved_quantity' => 2,
        ]);
    }

    public function test_it_rejects_release_when_reserved_quantity_is_insufficient(): void
    {
        $inventory = $this->createInventory(
            quantity: 10,
            reservedQuantity: 2,
        );

        $this->expectException(RuntimeException::class);

        $this->inventoryService->release(
            $inventory->productVariant,
            3,
        );
    }

    public function test_it_adjusts_inventory_quantity(): void
    {
        $inventory = $this->createInventory(quantity: 10);

        $result = $this->inventoryService->adjust(
            $inventory->productVariant,
            25,
        );

        $this->assertSame(25, $result->quantity);

        $this->assertDatabaseHas('inventories', [
            'id' => $inventory->id,
            'quantity' => 25,
        ]);
    }

    public function test_it_rejects_adjustment_below_reserved_quantity(): void
    {
        $inventory = $this->createInventory(
            quantity: 10,
            reservedQuantity: 6,
        );

        $this->expectException(RuntimeException::class);

        $this->inventoryService->adjust(
            $inventory->productVariant,
            5,
        );
    }

    public function test_it_creates_movement_for_inventory_operations(): void
    {
        $inventory = $this->createInventory(quantity: 10);

        $this->inventoryService->increase(
            $inventory->productVariant,
            5,
        );

        $this->inventoryService->decrease(
            $inventory->productVariant,
            3,
        );

        $this->inventoryService->reserve(
            $inventory->productVariant,
            2,
        );

        $this->inventoryService->release(
            $inventory->productVariant,
            1,
        );

        $this->inventoryService->adjust(
            $inventory->productVariant,
            20,
        );

        $this->assertDatabaseCount('inventory_movements', 5);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_id' => $inventory->id,
            'type' => InventoryMovementType::IN->value,
            'quantity' => 5,
            'quantity_before' => 10,
            'quantity_after' => 15,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_id' => $inventory->id,
            'type' => InventoryMovementType::OUT->value,
            'quantity' => -3,
            'quantity_before' => 15,
            'quantity_after' => 12,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_id' => $inventory->id,
            'type' => InventoryMovementType::RESERVE->value,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_id' => $inventory->id,
            'type' => InventoryMovementType::RELEASE->value,
            'quantity' => 1,
        ]);

        $this->assertDatabaseHas('inventory_movements', [
            'inventory_id' => $inventory->id,
            'type' => InventoryMovementType::ADJUSTMENT->value,
            'quantity_before' => 12,
            'quantity_after' => 20,
        ]);
    }
}