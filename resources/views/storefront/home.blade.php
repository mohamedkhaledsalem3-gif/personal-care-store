@php
    /*
    |--------------------------------------------------------------------------
    | Safe collections
    |--------------------------------------------------------------------------
    | نحافظ على أسماء المتغيرات التي يرسلها HomeController،
    | مع fallback آمن حتى لا تنهار الصفحة إذا كانت إحدى المجموعات فارغة.
    */

    $homeCategories = $categories ?? collect();
    $homeBrands = $brands ?? collect();

    $homeFeaturedProducts = $featuredProducts ?? collect();
    $homeNewProducts = $newProducts ?? collect();
    $homeBestSellerProducts = $bestSellerProducts ?? collect();

    $productsForHome = $homeBestSellerProducts->isNotEmpty()
        ? $homeBestSellerProducts
        : (
            $homeFeaturedProducts->isNotEmpty()
                ? $homeFeaturedProducts
                : $homeNewProducts
        );

    $productsForHome = $productsForHome->take(8);

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

$imageUrl = function ($image): ?string {
    if (!$image) {
        return null;
    }

    $path = $image->image_path ?? null;

    if (!$path) {
        return null;
    }

    if (
        str_starts_with($path, 'http://')
        || str_starts_with($path, 'https://')
        || str_starts_with($path, '/')
    ) {
        return $path;
    }

    return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
};

$productImage = function ($product) use ($imageUrl): ?string {
    $images = $product->images ?? collect();

    if (!$images instanceof \Illuminate\Support\Collection) {
        return null;
    }

    $image = $images->firstWhere('is_primary', true)
        ?? $images->firstWhere('is_active', true)
        ?? $images->first();

    return $imageUrl($image);
};

    $productVariant = function ($product) {
        $variants = $product->variants ?? collect();

        if (!$variants instanceof \Illuminate\Support\Collection) {
            return null;
        }

        return $variants->firstWhere('is_default', true)
            ?? $variants->firstWhere('is_active', true)
            ?? $variants->first();
    };

    $productPrice = function ($product) {
        return $product->sale_price ?? $product->price ?? 0;
    };

    $oldProductPrice = function ($product) {
        return $product->sale_price
            ? ($product->price ?? null)
            : null;
    };

    $ratingValue = function ($product): ?float {
        $rating = $product->average_rating
            ?? $product->rating
            ?? null;

        return is_numeric($rating)
            ? (float) $rating
            : null;
    };
@endphp


{{-- ================================================================
     1. Announcement Bar
================================================================= --}}
<div class="bg-[#66755b] px-4 py-2.5 text-center text-xs font-medium text-white sm:text-sm">
    <div class="mx-auto flex max-w-7xl items-center justify-center gap-2">
        <span>شحن مجاني للطلبات فوق 250  ج.م </span>
        <span class="hidden h-1 w-1 rounded-full bg-white/70 sm:block"></span>
        <span>عروض محدودة اليوم</span>
    </div>
</div>


{{-- ================================================================
     2. Hero Section
================================================================= --}}
<section class="overflow-hidden bg-[#f8f6f1]">
    <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-10 sm:px-6 sm:py-14 lg:grid-cols-2 lg:gap-16 lg:px-8 lg:py-20">

        {{-- Text --}}
        <div class="order-2 text-center lg:order-1 lg:text-right">

            <span class="mb-4 inline-flex rounded-full bg-[#eadfd7] px-4 py-2 text-xs font-semibold text-[#6c5548]">
                عناية مختارة بعناية
            </span>

            <h1 class="text-4xl font-bold leading-tight tracking-tight text-[#263126] sm:text-5xl lg:text-6xl">
                اكتشفي
                <span class="text-[#8b6f61]">جمالك الطبيعي</span>
            </h1>

            <p class="mx-auto mt-5 max-w-xl text-base leading-8 text-gray-600 sm:text-lg lg:mx-0">
                منتجات مختارة لروتين عناية يومي يمنحك تجربة بسيطة،
                هادئة ومميزة في كل يوم.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row lg:justify-start">

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-[#263126] px-7 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#3b493b]"
                >
                    تسوقي الآن
                </a>

                <a
                    href="#categories"
                    class="inline-flex items-center justify-center rounded-xl border border-[#cfc8c0] bg-white px-7 py-3.5 text-sm font-semibold text-[#263126] transition hover:bg-gray-50"
                >
                    اكتشفي مجموعتنا
                </a>

            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-5 text-xs text-gray-500 lg:justify-start">
                <span>✓ منتجات مختارة</span>
                <span>✓ تجربة تسوق سهلة</span>
                <span>✓ دعم للعملاء</span>
            </div>

        </div>


        {{-- Visual --}}
        <div class="order-1 lg:order-2">

            <div class="relative mx-auto max-w-xl">

                <div class="absolute -right-5 -top-5 h-24 w-24 rounded-full bg-[#d9c9bc]/60 blur-2xl"></div>
                <div class="absolute -bottom-5 -left-5 h-28 w-28 rounded-full bg-[#b8c3a9]/60 blur-2xl"></div>

                <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#e8ded6] via-[#f5eee8] to-[#dbe3d4] p-5 shadow-xl sm:p-8">

                    <div class="grid grid-cols-2 gap-4">

                        <div class="flex min-h-44 items-end rounded-3xl bg-white/80 p-5 shadow-sm sm:min-h-56">
                            <div>
                                <span class="text-xs text-gray-500">عناية</span>
                                <p class="mt-1 text-lg font-bold text-[#3c4639]">
                                    روتينك اليومي
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 flex min-h-44 items-start justify-end rounded-3xl bg-[#7b8b6e]/80 p-5 shadow-sm sm:min-h-56">
                            <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-[#4e5b47]">
                                Natural Care
                            </span>
                        </div>

                        <div class="-mt-3 flex min-h-36 items-center justify-center rounded-3xl bg-[#cdb9aa]/80 p-5 shadow-sm sm:min-h-44">
                            <div class="text-center">
                                <span class="text-4xl">✦</span>
                                <p class="mt-2 text-sm font-semibold text-[#59483e]">
                                    جمال طبيعي
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex min-h-36 items-center justify-center rounded-3xl bg-white/75 p-5 shadow-sm sm:min-h-44">
                            <div class="text-center">
                                <span class="text-4xl">♡</span>
                                <p class="mt-2 text-sm font-semibold text-[#59483e]">
                                    عناية تستحقينها
                                </p>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</section>


{{-- ================================================================
     3. Categories
================================================================= --}}
<section
    id="categories"
    class="bg-white px-4 py-14 sm:px-6 lg:px-8 lg:py-20"
>
    <div class="mx-auto max-w-7xl">

        <div class="mb-8 flex items-end justify-between gap-4">

            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                    اكتشفي مجموعتنا
                </span>

                <h2 class="mt-2 text-2xl font-bold text-[#263126] sm:text-3xl">
                    تسوقي حسب الفئة
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    اختاري الفئة المناسبة وابدئي رحلة العناية الخاصة بك.
                </p>
            </div>

            <a
                href="{{ route('storefront.products.index') }}"
                class="hidden text-sm font-semibold text-[#66755b] hover:text-[#3f4c38] sm:inline-flex"
            >
                عرض الكل ←
            </a>

        </div>


        @if($homeCategories->isNotEmpty())

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">

                @foreach($homeCategories->take(5) as $category)

                    @php
                        $categoryName = $category->name ?? 'تصنيف';
                        $categorySlug = $category->slug ?? $category->id;
                    @endphp

                    <a
                        href="{{ route('storefront.products.index', ['category' => $categorySlug]) }}"
                        class="group overflow-hidden rounded-2xl border border-gray-100 bg-[#faf9f7] p-4 text-center transition duration-300 hover:-translate-y-1 hover:border-[#d8d0c8] hover:bg-white hover:shadow-lg"
                    >

                        <div class="mx-auto flex aspect-square max-w-32 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-[#eee6df] to-[#dfe6da] text-3xl text-[#66755b] transition group-hover:scale-105">
                            <span>
                                {{ mb_substr($categoryName, 0, 1) }}
                            </span>
                        </div>

                        <h3 class="mt-4 text-sm font-bold text-[#303930]">
                            {{ $categoryName }}
                        </h3>

                        <span class="mt-1 block text-xs text-gray-400">
                            اكتشفي المنتجات
                        </span>

                    </a>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-10 text-center text-sm text-gray-500">
                ستظهر التصنيفات هنا قريبًا.
            </div>

        @endif

    </div>
</section>


{{-- ================================================================
     4. Best Sellers
================================================================= --}}
<section class="bg-[#f8f6f1] px-4 py-14 sm:px-6 lg:px-8 lg:py-20">

    <div class="mx-auto max-w-7xl">

        <div class="mb-8 flex items-end justify-between gap-4">

            <div>
                <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                    اختيارات العملاء
                </span>

                <h2 class="mt-2 text-2xl font-bold text-[#263126] sm:text-3xl">
                    المنتجات الأكثر مبيعًا
                </h2>

                <p class="mt-2 text-sm text-gray-500">
                    منتجات تستحق أن تكون جزءًا من روتينك اليومي.
                </p>
            </div>

            <a
                href="{{ route('storefront.products.index') }}"
                class="hidden text-sm font-semibold text-[#66755b] hover:text-[#3f4c38] sm:inline-flex"
            >
                عرض الكل ←
            </a>

        </div>


        @if($productsForHome->isNotEmpty())

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                @foreach($productsForHome as $product)

                    @php
                        $image = $productImage($product);
                        $price = $productPrice($product);
                        $oldPrice = $oldProductPrice($product);
                        $variant = $productVariant($product);
                        $rating = $ratingValue($product);

                        $brandName = data_get($product, 'brand.name');
                    @endphp

                    <article class="group overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                        <div class="relative">

                            <a
                                href="{{ route('storefront.products.show', $product) }}"
                                class="block"
                            >

                                <div class="relative aspect-square overflow-hidden bg-[#f3f1ee]">

                                    @if($image)

                                        <img
                                            src="{{ $image }}"
                                            alt="{{ $product->name }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                            loading="lazy"
                                        >

                                    @else

                                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[#eee7e0] to-[#dfe5da]">
                                            <span class="text-5xl font-light text-[#718069]">
                                                {{ mb_substr($product->name ?? 'P', 0, 1) }}
                                            </span>
                                        </div>

                                    @endif

                                </div>

                            </a>


                            <div class="absolute right-3 top-3 flex flex-col gap-2">

                                <span class="rounded-full bg-[#66755b] px-2.5 py-1 text-[10px] font-bold text-white">
                                    الأكثر مبيعًا
                                </span>

                                @if($oldPrice)
                                    <span class="rounded-full bg-[#9b6f62] px-2.5 py-1 text-[10px] font-bold text-white">
                                        عرض
                                    </span>
                                @endif

                            </div>


                            <button
                                type="button"
                                class="absolute left-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-lg text-gray-500 shadow-sm backdrop-blur transition hover:bg-white hover:text-[#9b6f62]"
                                aria-label="إضافة {{ $product->name }} إلى المفضلة"
                            >
                                ♡
                            </button>

                        </div>


                        <div class="p-4">

                            @if($brandName)
                                <p class="text-[11px] font-medium text-gray-400">
                                    {{ $brandName }}
                                </p>
                            @endif

                            <a
                                href="{{ route('storefront.products.show', $product) }}"
                                class="mt-1 block"
                            >
                                <h3 class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-[#303930] transition group-hover:text-[#66755b]">
                                    {{ $product->name }}
                                </h3>
                            </a>


                            <div class="mt-2 flex items-center gap-1">

                                @if($rating !== null)

                                    <span class="text-xs font-semibold text-[#9b765f]">
                                        {{ number_format($rating, 1) }}
                                    </span>

                                    <span class="text-xs tracking-tight text-[#d2a85b]">
                                        ★★★★★
                                    </span>

                                @else

                                    <span class="text-xs text-gray-400">
                                        كن أول من يقيّم هذا المنتج
                                    </span>

                                @endif

                            </div>


                            <div class="mt-3 flex items-end gap-2">

                                <span class="text-base font-bold text-[#263126]">
                                    {{ number_format((float) $price, 2) }}
                                    <span class="text-xs font-medium">ج.م</span>
                                </span>

                                @if($oldPrice)

                                    <span class="text-xs text-gray-400 line-through">
                                        {{ number_format((float) $oldPrice, 2) }}
ج.م	
                                    </span>

                                @endif

                            </div>


                            @if($variant)

                                <form
                                    method="POST"
                                    action="{{ route('storefront.cart.items.store') }}"
                                    class="mt-4"
                                >

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="product_variant_id"
                                        value="{{ $variant->id }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="quantity"
                                        value="1"
                                    >

                                    <button
                                        type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#263126] px-4 py-2.5 text-xs font-bold text-white transition hover:bg-[#3f4c38]"
                                    >
                                        <span>+</span>
                                        أضيفي للسلة
                                    </button>

                                </form>

                            @else

                                <a
                                    href="{{ route('storefront.products.show', $product) }}"
                                    class="mt-4 flex w-full items-center justify-center rounded-xl border border-[#d8d0c8] px-4 py-2.5 text-xs font-bold text-[#3c4938] transition hover:bg-[#f8f6f1]"
                                >
                                    عرض المنتج
                                </a>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center text-sm text-gray-500">
                ستظهر المنتجات المميزة هنا قريبًا.
            </div>

        @endif

    </div>

</section>


{{-- ================================================================
     5. Offers Banner
================================================================= --}}
<section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

    <div class="mx-auto max-w-7xl">

        <div class="relative overflow-hidden rounded-[2rem] bg-[#7c8b70] px-6 py-10 text-white sm:px-10 lg:px-16 lg:py-14">

            <div class="absolute -left-16 -top-16 h-44 w-44 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-20 right-10 h-52 w-52 rounded-full bg-black/10"></div>

            <div class="relative grid items-center gap-8 lg:grid-cols-2">

                <div>

                    <span class="inline-flex rounded-full bg-white/15 px-4 py-2 text-xs font-semibold">
                        عرض خاص
                    </span>

                    <h2 class="mt-4 text-3xl font-bold leading-tight sm:text-4xl">
                        عرض خاص على روتين العناية اليومية
                    </h2>

                    <p class="mt-4 max-w-xl text-sm leading-7 text-white/80 sm:text-base">
                        اكتشفي مجموعة مختارة من المنتجات التي تساعدك
                        على بناء روتين عناية بسيط ومتكامل.
                    </p>

                    <div class="mt-7">

                        <a
                            href="{{ route('storefront.products.index') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-[#526049] transition hover:bg-gray-100"
                        >
                            اكتشفي العرض
                        </a>

                    </div>

                </div>


                <div class="flex justify-center lg:justify-end">

                    <div class="grid w-full max-w-sm grid-cols-3 gap-3">

                        <div class="h-28 rounded-3xl bg-white/20 backdrop-blur-sm sm:h-36"></div>
                        <div class="mt-6 h-28 rounded-3xl bg-white/30 backdrop-blur-sm sm:h-36"></div>
                        <div class="h-28 rounded-3xl bg-white/15 backdrop-blur-sm sm:h-36"></div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


{{-- ================================================================
     6. Shop By Need
================================================================= --}}
<section class="bg-white px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

    <div class="mx-auto max-w-7xl">

        <div class="mb-8 text-center">

            <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                اختاري ما يناسبك
            </span>

            <h2 class="mt-2 text-2xl font-bold text-[#263126] sm:text-3xl">
                اختاري حسب احتياجك
            </h2>

            <p class="mx-auto mt-2 max-w-2xl text-sm leading-7 text-gray-500">
                الوصول إلى المنتج المناسب يبدأ من احتياجك.
            </p>

        </div>


        <div class="grid gap-4 md:grid-cols-3">

            @php
                $needs = [
                    [
                        'title' => 'بشرة جافة',
                        'description' => 'منتجات تساعدك على بناء روتين عناية لطيف.',
                        'query' => 'بشرة جافة',
                    ],
                    [
                        'title' => 'بشرة حساسة',
                        'description' => 'اختيارات مناسبة لروتين يومي هادئ.',
                        'query' => 'بشرة حساسة',
                    ],
                    [
                        'title' => 'شعر متضرر',
                        'description' => 'اكتشفي منتجات العناية بالشعر.',
                        'query' => 'شعر متضرر',
                    ],
                ];
            @endphp

            @foreach($needs as $need)

                <a
                    href="{{ route('storefront.products.index', ['q' => $need['query']]) }}"
                    class="group relative overflow-hidden rounded-3xl bg-[#f5f1ec] p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg"
                >

                    <div class="absolute -left-8 -top-8 h-28 w-28 rounded-full bg-[#d8c6b9]/50 transition group-hover:scale-125"></div>

                    <div class="relative">

                        <span class="text-xs font-semibold text-[#8b6f61]">
                            روتين مخصص
                        </span>

                        <h3 class="mt-3 text-xl font-bold text-[#303930]">
                            {{ $need['title'] }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-gray-500">
                            {{ $need['description'] }}
                        </p>

                        <span class="mt-5 inline-flex items-center text-sm font-bold text-[#66755b]">
                            تسوقي الآن
                            <span class="mr-2 transition group-hover:-translate-x-1">←</span>
                        </span>

                    </div>

                </a>

            @endforeach

        </div>

    </div>

</section>


{{-- ================================================================
     7. Brands
================================================================= --}}
<section class="bg-[#f8f6f1] px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

    <div class="mx-auto max-w-7xl">

        <div class="mb-8 text-center">

            <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                اختياراتنا
            </span>

            <h2 class="mt-2 text-2xl font-bold text-[#263126] sm:text-3xl">
                علامات نثق بها
            </h2>

        </div>


        @if($homeBrands->isNotEmpty())

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">

                @foreach($homeBrands->take(10) as $brand)

                    @php
                        $brandName = $brand->name ?? 'علامة تجارية';
                        $brandSlug = $brand->slug ?? $brand->id;
                    @endphp

                    <a
                        href="{{ route('storefront.products.index', ['brand' => $brandSlug]) }}"
                        class="flex min-h-28 items-center justify-center rounded-2xl border border-gray-100 bg-white px-4 py-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md"
                    >
                        <div>
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-[#eef1ea] text-sm font-bold text-[#66755b]">
                                {{ mb_substr($brandName, 0, 1) }}
                            </div>

                            <p class="mt-3 text-sm font-bold text-[#3b4439]">
                                {{ $brandName }}
                            </p>
                        </div>
                    </a>

                @endforeach

            </div>

        @else

            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center text-sm text-gray-500">
                ستظهر العلامات التجارية هنا قريبًا.
            </div>

        @endif

    </div>

</section>


{{-- ================================================================
     8. Our Story
================================================================= --}}
<section class="bg-white px-4 py-14 sm:px-6 lg:px-8 lg:py-20">

    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-16">

        <div class="overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#dfe6da] via-[#f2eee9] to-[#dac9bd] p-8 sm:p-12">

            <div class="flex min-h-72 items-center justify-center rounded-[1.5rem] border border-white/50 bg-white/20 backdrop-blur-sm">

                <div class="text-center">

                    <span class="text-6xl text-[#66755b]">
                        ✦
                    </span>

                    <p class="mt-4 text-lg font-bold text-[#46503f]">
                        Personal Care Store
                    </p>

                    <p class="mt-2 text-sm text-[#687064]">
                        عناية مختارة بعناية
                    </p>

                </div>

            </div>

        </div>


        <div>

            <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                السلام عليكم ورحمه الله وبركاته 💚  
معاكم د ايه خالد طبيبة صيدلانية بفضل الله معايا مؤهلات علمية متخصصة بمشاكل البشرة والشعر .. في البداية خالص كنت ببحث عن منتجات للشعر والبشرة تكون آمنة وطبيعية ١٠٠% وبدأت ادرس وابحث واخد كورسات عن تحضير التركيبات الطبيعية دي بالإضافة إلي الدراسة الأكاديمية بكلية صيدلة الزقازيق.

الحمد لله نجحت إني أبدأ تحضير التركيبات بفضل الله المنتج بعمله من أوله الي آخره ع علم بكل مادة فيه وفوائدها  💚😍  
وبإذن الله اي معلومه حابيين تعرفوها عن البشرة والشعر هكون معاكم وهحاول اعرفكم تفاصيل كل منتج واستخدامه😍  
اتمني الجروب يعجبكم واتمني اقدر أفيدكم وافرحكم😍💚
            </span>

            <h2 class="mt-3 text-3xl font-bold leading-tight text-[#263126] sm:text-4xl">
                منتجات مختارة بعناية،
                <span class="text-[#8b6f61]">وتجربة نهتم بها</span>
            </h2>

            <p class="mt-5 text-sm leading-8 text-gray-600 sm:text-base">
                نؤمن أن روتين العناية لا يحتاج إلى التعقيد.
                لذلك نعمل على تقديم مجموعة مختارة من المنتجات
                التي تجمع بين الجودة وسهولة الاختيار وتجربة تسوق مريحة.
            </p>

            <p class="mt-4 text-sm leading-8 text-gray-600 sm:text-base">
                هدفنا أن تجدي ما تحتاجينه بسهولة،
                وأن تكون كل زيارة للمتجر بداية لروتين أفضل.
            </p>

            <a
                href="{{ route('storefront.products.index') }}"
                class="mt-7 inline-flex items-center rounded-xl bg-[#263126] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#3f4c38]"
            >
                اعرفي المزيد
            </a>

        </div>

    </div>

</section>


{{-- ================================================================
     9. Customer Reviews
================================================================= --}}
<section class="bg-[#f8f6f1] px-4 py-14 sm:px-6 lg:px-8 lg:py-20">

    <div class="mx-auto max-w-7xl">

        <div class="mb-10 text-center">

            <span class="text-xs font-semibold uppercase tracking-widest text-[#8b6f61]">
                تجارب العملاء
            </span>

            <h2 class="mt-2 text-2xl font-bold text-[#263126] sm:text-3xl">
                ماذا يقول عملاؤنا؟
            </h2>

            <p class="mx-auto mt-2 max-w-xl text-sm leading-7 text-gray-500">
                ستظهر تقييمات العملاء الفعلية هنا بعد تفعيل نظام المراجعات.
            </p>

        </div>


        <div class="grid gap-4 md:grid-cols-3">

            @foreach([
                'تجربتك معنا تهمنا، وسنضيف هنا تقييمات العملاء الفعلية.',
                'آراء العملاء ستساعدك على اختيار المنتجات المناسبة بثقة.',
                'نعمل على بناء تجربة تسوق تجعل العناية أسهل وأفضل.'
            ] as $review)

                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                    <div class="text-sm tracking-widest text-[#d2a85b]">
                        ★★★★★
                    </div>

                    <p class="mt-4 text-sm leading-7 text-gray-500">
                        {{ $review }}
                    </p>

                    <div class="mt-5 border-t border-gray-100 pt-4">

                        <p class="text-sm font-bold text-[#303930]">
                            عميلنا العزيز
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            Personal Care Store
                        </p>

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</section>


{{-- ================================================================
     10. Newsletter
================================================================= --}}
<section class="px-4 py-12 sm:px-6 lg:px-8 lg:py-16">

    <div class="mx-auto max-w-5xl">

        <div
            id="newsletter"
            class="overflow-hidden rounded-[2rem] bg-[#263126] px-6 py-10 text-center text-white sm:px-10 lg:px-16 lg:py-14"
        >

            <span class="text-3xl">
                ✉
            </span>

            <h2 class="mt-4 text-2xl font-bold sm:text-3xl">
                احصلي على أحدث العروض والنصائح
            </h2>

            <p class="mx-auto mt-3 max-w-xl text-sm leading-7 text-white/70">
                اشتركي في النشرة البريدية ليصلك كل جديد من العروض
                والمنتجات والنصائح.
            </p>


            <form
                action="#newsletter"
                method="POST"
                class="mx-auto mt-7 flex max-w-2xl flex-col gap-3 sm:flex-row"
            >

                <input
                    type="email"
                    name="email"
                    placeholder="أدخلي بريدك الإلكتروني"
                    class="min-h-12 flex-1 rounded-xl border-0 bg-white px-4 text-right text-sm text-gray-900 outline-none ring-0 placeholder:text-gray-400"
                >

                <button
                    type="submit"
                    class="min-h-12 rounded-xl bg-[#b7c2a8] px-7 text-sm font-bold text-[#263126] transition hover:bg-[#c8d1bd]"
                >
                    اشتراك
                </button>

            </form>

            <p class="mt-4 text-[11px] text-white/50">
                لن نستخدم بريدك الإلكتروني إلا للتواصل المتعلق بالمتجر والعروض.
            </p>

        </div>

    </div>

</section>