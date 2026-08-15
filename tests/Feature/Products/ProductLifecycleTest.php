<?php

namespace Tests\Feature\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ProductLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ProductService::class);
    }

    protected function createCategory(
        array $attributes = []
    ): Category {
        return Category::factory()->create(array_merge([
            'name' => 'Skin Care',
            'slug' => 'skin-care-' . uniqid(),
            'is_active' => true,
        ], $attributes));
    }

    protected function createBrand(
        array $attributes = []
    ): Brand {
        return Brand::factory()->create(array_merge([
            'name' => 'Test Brand',
            'slug' => 'test-brand-' . uniqid(),
            'is_active' => true,
        ], $attributes));
    }

    protected function productData(
        Category $category,
        ?Brand $brand = null,
        array $attributes = []
    ): array {
        return array_merge([
            'category_id' => $category->id,
            'brand_id' => $brand?->id,
            'name' => 'Vitamin C Serum',
            'sku' => 'SKU-' . strtoupper(uniqid()),
            'short_description' => 'Test product',
            'description' => 'Test product description',
            'unit' => 'piece',
            'weight' => 0.250,
            'cost_price' => 100,
            'price' => 150,
            'sale_price' => 130,
            'stock_quantity' => 0,
            'low_stock_threshold' => 5,
            'status' => 'active',
            'is_featured' => false,
            'is_new' => true,
            'is_best_seller' => false,
            'meta_title' => 'Vitamin C Serum',
            'meta_description' => 'Vitamin C Serum description',
        ], $attributes);
    }

    public function test_it_creates_a_product_successfully(): void
    {
        $category = $this->createCategory();
        $brand = $this->createBrand();

        $product = $this->service->create(
            $this->productData($category, $brand)
        );

        $this->assertInstanceOf(Product::class, $product);

        $this->assertSame(
            $category->id,
            $product->category_id
        );

        $this->assertSame(
            $brand->id,
            $product->brand_id
        );

        $this->assertSame(
            'Vitamin C Serum',
            $product->name
        );

        $this->assertSame(
            'active',
            $product->status
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Vitamin C Serum',
            'sku' => $product->sku,
            'status' => 'active',
        ]);
    }

    public function test_it_generates_a_unique_slug_automatically(): void
    {
        $category = $this->createCategory();

        $product = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Vitamin C Serum',
                    'slug' => null,
                ]
            )
        );

        $this->assertNotSame('', $product->slug);

        $this->assertSame(
            'vitamin-c-serum',
            $product->slug
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'slug' => 'vitamin-c-serum',
        ]);
    }

    public function test_it_generates_a_different_slug_when_slug_already_exists(): void
    {
        $category = $this->createCategory();

        $first = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Vitamin C Serum',
                    'slug' => 'vitamin-c-serum',
                ]
            )
        );

        $second = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Vitamin C Serum New',
                    'slug' => 'vitamin-c-serum',
                ]
            )
        );

        $this->assertSame(
            'vitamin-c-serum',
            $first->slug
        );

        $this->assertSame(
            'vitamin-c-serum-2',
            $second->slug
        );

        $this->assertNotSame(
            $first->slug,
            $second->slug
        );

        $this->assertDatabaseHas('products', [
            'id' => $second->id,
            'slug' => 'vitamin-c-serum-2',
        ]);
    }

    public function test_it_rejects_product_creation_with_inactive_category(): void
    {
        $category = $this->createCategory([
            'is_active' => false,
        ]);

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'التصنيف غير نشط.'
        );

        $this->service->create(
            $this->productData($category)
        );

        $this->assertDatabaseCount(
            'products',
            0
        );
    }

    public function test_it_rejects_product_creation_with_inactive_brand(): void
    {
        $category = $this->createCategory();

        $brand = $this->createBrand([
            'is_active' => false,
        ]);

        $this->expectException(RuntimeException::class);

        $this->expectExceptionMessage(
            'العلامة التجارية غير نشطة.'
        );

        $this->service->create(
            $this->productData($category, $brand)
        );

        $this->assertDatabaseCount(
            'products',
            0
        );
    }

    public function test_it_updates_a_product_successfully(): void
    {
        $category = $this->createCategory();
        $brand = $this->createBrand();

        $product = $this->service->create(
            $this->productData($category, $brand)
        );

        $updated = $this->service->update(
            $product->id,
            [
                'name' => 'Updated Vitamin C Serum',
                'price' => 180,
                'sale_price' => 160,
                'is_featured' => true,
            ]
        );

        $this->assertSame(
            'Updated Vitamin C Serum',
            $updated->name
        );

        $this->assertEquals(
            180,
            $updated->price
        );

        $this->assertEquals(
            160,
            $updated->sale_price
        );

        $this->assertTrue(
            $updated->is_featured
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Vitamin C Serum',
            'price' => 180,
            'sale_price' => 160,
            'is_featured' => true,
        ]);
    }

    public function test_it_generates_a_new_slug_when_product_name_changes(): void
    {
        $category = $this->createCategory();

        $product = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Original Serum',
                ]
            )
        );

        $this->assertSame(
            'original-serum',
            $product->slug
        );

        $updated = $this->service->update(
            $product->id,
            [
                'name' => 'New Face Serum',
            ]
        );

        $this->assertSame(
            'New Face Serum',
            $updated->name
        );

        $this->assertSame(
            'new-face-serum',
            $updated->slug
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Face Serum',
            'slug' => 'new-face-serum',
        ]);
    }

    public function test_it_activates_a_product(): void
    {
        $category = $this->createCategory();

        $product = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'status' => 'inactive',
                ]
            )
        );

        $this->assertSame(
            'inactive',
            $product->status
        );

        $activated = $this->service->activate(
            $product->id
        );

        $this->assertSame(
            'active',
            $activated->status
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'active',
        ]);
    }

    public function test_it_deactivates_a_product(): void
    {
        $category = $this->createCategory();

        $product = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'status' => 'active',
                ]
            )
        );

        $deactivated = $this->service->deactivate(
            $product->id
        );

        $this->assertSame(
            'inactive',
            $deactivated->status
        );

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'inactive',
        ]);
    }

    public function test_it_finds_a_product_with_its_relations(): void
    {
        $category = $this->createCategory();
        $brand = $this->createBrand();

        $product = $this->service->create(
            $this->productData($category, $brand)
        );

        $found = $this->service->find(
            $product->id
        );

        $this->assertInstanceOf(
            Product::class,
            $found
        );

        $this->assertSame(
            $product->id,
            $found->id
        );

        $this->assertTrue(
            $found->relationLoaded('category')
        );

        $this->assertTrue(
            $found->relationLoaded('brand')
        );

        $this->assertTrue(
            $found->relationLoaded('images')
        );

        $this->assertTrue(
            $found->relationLoaded('variants')
        );

        $this->assertSame(
            $category->id,
            $found->category->id
        );

        $this->assertSame(
            $brand->id,
            $found->brand->id
        );
    }

    public function test_it_returns_all_products(): void
    {
        $category = $this->createCategory();

        $first = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Product One',
                ]
            )
        );

        $second = $this->service->create(
            $this->productData(
                $category,
                null,
                [
                    'name' => 'Product Two',
                ]
            )
        );

        $products = $this->service->all();

        $this->assertCount(
            2,
            $products
        );

        $ids = $products
            ->pluck('id')
            ->all();

        $this->assertContains(
            $first->id,
            $ids
        );

        $this->assertContains(
            $second->id,
            $ids
        );
    }

    public function test_it_deletes_a_product_successfully(): void
    {
        $category = $this->createCategory();

        $product = $this->service->create(
            $this->productData($category)
        );

        $this->assertTrue(
            Product::query()
                ->whereKey($product->id)
                ->exists()
        );

        $deleted = $this->service->delete(
            $product->id
        );

        $this->assertTrue($deleted);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
