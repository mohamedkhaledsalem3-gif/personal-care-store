@extends('storefront.layouts.app')

@section('title', 'المنتجات - Personal Care Store')

@section('content')

    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4">

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold">
                    جميع المنتجات
                </h1>

                <p class="mt-2 text-gray-500">
                    اكتشف مجموعة منتجات العناية الشخصية المتاحة لدينا.
                </p>
            </div>


            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

                {{-- Filters --}}
                <aside class="lg:col-span-1">

                    <div class="rounded-2xl bg-white p-5 shadow-sm">

                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-lg font-bold">
                                تصفية المنتجات
                            </h2>

                            <a
                                href="{{ route('storefront.products.index') }}"
                                class="text-sm text-gray-500 hover:text-black"
                            >
                                مسح الكل
                            </a>
                        </div>


                        {{-- Search --}}
                        <form
                            method="GET"
                            action="{{ route('storefront.products.index') }}"
                            class="mb-8"
                        >

                            <label
                                for="search"
                                class="mb-2 block text-sm font-semibold"
                            >
                                البحث
                            </label>

                            <div class="flex gap-2">

                                <input
                                    id="search"
                                    type="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="ابحث عن منتج..."
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-black focus:ring-black"
                                >

                                <button
                                    type="submit"
                                    class="rounded-xl bg-black px-4 py-2 text-sm font-semibold text-white"
                                >
                                    بحث
                                </button>

                            </div>

                            @if(request('category'))
                                <input
                                    type="hidden"
                                    name="category"
                                    value="{{ request('category') }}"
                                >
                            @endif

                            @if(request('brand'))
                                <input
                                    type="hidden"
                                    name="brand"
                                    value="{{ request('brand') }}"
                                >
                            @endif

                        </form>


                        {{-- Categories --}}
                        <div class="mb-8">

                            <h3 class="mb-4 font-semibold">
                                الأقسام
                            </h3>

                            <div class="space-y-3">

                                <a
                                    href="{{ route('storefront.products.index', request()->except('category', 'page')) }}"
                                    class="flex items-center justify-between text-sm
                                    {{ !request('category') ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}"
                                >
                                    <span>
                                        جميع الأقسام
                                    </span>
                                </a>

                                @foreach($categories as $category)

                                    <a
                                        href="{{ route('storefront.products.index', array_merge(request()->except('page'), ['category' => $category->slug])) }}"
                                        class="flex items-center justify-between text-sm
                                        {{ request('category') === $category->slug ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}"
                                    >
                                        <span>
                                            {{ $category->name }}
                                        </span>

                                        <span class="text-xs text-gray-400">
                                            {{ $category->products_count }}
                                        </span>
                                    </a>

                                @endforeach

                            </div>

                        </div>


                        {{-- Brands --}}
                        <div>

                            <h3 class="mb-4 font-semibold">
                                العلامات التجارية
                            </h3>

                            <div class="space-y-3">

                                <a
                                    href="{{ route('storefront.products.index', request()->except('brand', 'page')) }}"
                                    class="flex items-center justify-between text-sm
                                    {{ !request('brand') ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}"
                                >
                                    <span>
                                        جميع العلامات
                                    </span>
                                </a>

                                @foreach($brands as $brand)

                                    <a
                                        href="{{ route('storefront.products.index', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}"
                                        class="flex items-center justify-between text-sm
                                        {{ request('brand') === $brand->slug ? 'font-bold text-black' : 'text-gray-600 hover:text-black' }}"
                                    >
                                        <span>
                                            {{ $brand->name }}
                                        </span>

                                        <span class="text-xs text-gray-400">
                                            {{ $brand->products_count }}
                                        </span>
                                    </a>

                                @endforeach

                            </div>

                        </div>

                    </div>

                </aside>


                {{-- Products --}}
                <div class="lg:col-span-3">

                    {{-- Active filters / result count --}}
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-sm text-gray-500">
                            عرض
                            <span class="font-semibold text-gray-900">
                                {{ $products->total() }}
                            </span>
                            منتج
                        </p>

                        @if(request('search') || request('category') || request('brand'))

                            <div class="flex flex-wrap gap-2">

                                @if(request('search'))
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        البحث: {{ request('search') }}
                                    </span>
                                @endif

                                @if(request('category'))
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        القسم: {{ request('category') }}
                                    </span>
                                @endif

                                @if(request('brand'))
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        العلامة: {{ request('brand') }}
                                    </span>
                                @endif

                            </div>

                        @endif

                    </div>


                    @if($products->isNotEmpty())

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">

                            @foreach($products as $product)

                                @include('storefront.partials.product-card', [
                                    'product' => $product,
                                ])

                            @endforeach

                        </div>


                        {{-- Pagination --}}
                        <div class="mt-10">
                            {{ $products->links() }}
                        </div>

                    @else

                        <div class="rounded-2xl bg-white p-12 text-center shadow-sm">

                            <div class="text-5xl">
                                🔍
                            </div>

                            <h2 class="mt-4 text-xl font-bold">
                                لم يتم العثور على منتجات
                            </h2>

                            <p class="mt-2 text-gray-500">
                                حاول تغيير البحث أو الفلاتر المستخدمة.
                            </p>

                            <a
                                href="{{ route('storefront.products.index') }}"
                                class="mt-6 inline-flex rounded-xl bg-black px-6 py-3 font-semibold text-white"
                            >
                                عرض جميع المنتجات
                            </a>

                        </div>

                    @endif

                </div>

            </div>

        </div>
    </section>

@endsection