<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class ProductService
{
    /**
     * الحصول على جميع المنتجات.
     */
    public function all(): Collection
    {
        return Product::query()
            ->with([
                'category',
                'brand',
                'images',
                'variants',
            ])
            ->latest()
            ->get();
    }

    /**
     * الحصول على منتج واحد.
     */
    public function find(int $productId): Product
    {
        return Product::query()
            ->with([
                'category',
                'brand',
                'images',
                'variants',
            ])
            ->findOrFail($productId);
    }

    /**
     * إنشاء منتج جديد.
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $this->validateCategory(
                (int) $data['category_id']
            );

            if (! empty($data['brand_id'])) {
                $this->validateBrand(
                    (int) $data['brand_id']
                );
            }

            $data['slug'] = $this->generateUniqueSlug(
                $data['slug'] ?? $data['name']
            );

            $data['status'] ??= 'active';

            $product = Product::create($data);

            return $product->fresh([
                'category',
                'brand',
                'images',
                'variants',
            ]);
        });
    }

    /**
     * تحديث منتج موجود.
     */
    public function update(
        int $productId,
        array $data
    ): Product {
        return DB::transaction(function () use (
            $productId,
            $data
        ) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($productId);

            if (array_key_exists('category_id', $data)) {
                $this->validateCategory(
                    (int) $data['category_id']
                );
            }

            if (array_key_exists('brand_id', $data)) {
                if (! empty($data['brand_id'])) {
                    $this->validateBrand(
                        (int) $data['brand_id']
                    );
                }
            }

            /*
             * إذا تم تغيير الاسم ولم يتم إرسال slug
             * يتم إنشاء slug جديد.
             */
            if (
                array_key_exists('name', $data)
                && ! array_key_exists('slug', $data)
            ) {
                $data['slug'] = $this->generateUniqueSlug(
                    $data['name'],
                    $product->id
                );
            }

            /*
             * إذا تم إرسال slug صراحةً،
             * نضمن أنه Unique أيضًا.
             */
            if (array_key_exists('slug', $data)) {
                $data['slug'] = $this->generateUniqueSlug(
                    $data['slug'],
                    $product->id
                );
            }

            $product->update($data);

            return $product->fresh([
                'category',
                'brand',
                'images',
                'variants',
            ]);
        });
    }

    /**
     * حذف منتج.
     */
    public function delete(int $productId): bool
    {
        return DB::transaction(function () use ($productId) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($productId);

            return (bool) $product->delete();
        });
    }

    /**
     * تفعيل المنتج.
     */
    public function activate(int $productId): Product
    {
        return DB::transaction(function () use ($productId) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($productId);

            $product->update([
                'status' => 'active',
            ]);

            return $product->fresh();
        });
    }

    /**
     * تعطيل المنتج.
     */
    public function deactivate(int $productId): Product
    {
        return DB::transaction(function () use ($productId) {

            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($productId);

            $product->update([
                'status' => 'inactive',
            ]);

            return $product->fresh();
        });
    }

    /**
     * التحقق من Category.
     */
    protected function validateCategory(int $categoryId): Category
    {
        $category = Category::query()
            ->findOrFail($categoryId);

        if (! $category->is_active) {
            throw new RuntimeException(
                'التصنيف غير نشط.'
            );
        }

        return $category;
    }

    /**
     * التحقق من Brand.
     */
    protected function validateBrand(int $brandId): Brand
    {
        $brand = Brand::query()
            ->findOrFail($brandId);

        if (! $brand->is_active) {
            throw new RuntimeException(
                'العلامة التجارية غير نشطة.'
            );
        }

        return $brand;
    }

    /**
     * إنشاء Slug فريد.
     */
    protected function generateUniqueSlug(
        string $value,
        ?int $ignoreProductId = null
    ): string {
        $baseSlug = Str::slug($value);

        /*
         * في حالة النص العربي أو النص الذي لا ينتج
         * عنه slug مناسب، نستخدم قيمة fallback.
         */
        if ($baseSlug === '') {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when(
                    $ignoreProductId !== null,
                    fn ($query) => $query->where(
                        'id',
                        '!=',
                        $ignoreProductId
                    )
                )
                ->exists()
        ) {
            $counter++;

            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }
}