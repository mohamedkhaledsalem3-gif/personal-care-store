<?php


namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * الصفحة الرئيسية للمتجر.
     */
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | الأقسام
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | المنتجات المميزة
        |--------------------------------------------------------------------------
        |
        | Product لا يحتوي على is_active.
        | حالة المنتج موجودة في status.
        |
        */

        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->where('is_featured', true)
            ->with([
                'images',
                'variants.inventory',
            ])
            ->latest()
            ->take(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | أحدث المنتجات
        |--------------------------------------------------------------------------
        */

        $newProducts = Product::query()
            ->where('status', 'active')
            ->where('is_new', true)
            ->with([
                'images',
                'variants.inventory',
            ])
            ->latest()
            ->take(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | الأكثر مبيعًا
        |--------------------------------------------------------------------------
        */

        $bestSellerProducts = Product::query()
            ->where('status', 'active')
            ->where('is_best_seller', true)
            ->with([
                'images',
                'variants.inventory',
            ])
            ->latest()
            ->take(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | العلامات التجارية
        |--------------------------------------------------------------------------
        */

        $brands = Brand::query()
            ->where('is_active', true)
            ->latest()
            ->take(12)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | الصفحة
        |--------------------------------------------------------------------------
        */

        return view('storefront.home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'bestSellerProducts' => $bestSellerProducts,
            'brands' => $brands,
        ]);
    }
}
