<?php


namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * عرض جميع المنتجات.
     */
    public function index(): View
    {
        $products = Product::query()
            ->where('status', 'active')
            ->with([
                'images',
                'variants.inventory',
                'category',
                'brand',
            ])
            ->latest()
            ->paginate(12);

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('storefront.products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    /**
     * عرض منتج واحد.
     */
    public function show(Product $product): View
    {
        abort_unless(
            $product->status === 'active',
            404
        );

        $product->load([
            'images',
            'variants.inventory',
            'category',
            'brand',
        ]);

        return view('storefront.products.show', [
            'product' => $product,
        ]);
    }
}
