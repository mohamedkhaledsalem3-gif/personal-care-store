
<article class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

    <a
        href="{{ route('storefront.products.show', $product->slug) }}"
        class="block"
    >

        <div class="relative aspect-square bg-gray-100">

            @php
                $image = $product->images->first();
            @endphp

            @if($image)

                <img
                    src="{{ asset('storage/' . $image->image_path) }}"
                    alt="{{ $image->alt_text ?: $product->name }}"
                    class="h-full w-full object-cover"
                >

            @else

                <div class="flex h-full items-center justify-center text-gray-400">
                    لا توجد صورة
                </div>

            @endif


            @if($product->sale_price)

                <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-white">
                    عرض
                </span>

            @endif


            @if($product->is_new)

                <span class="absolute left-3 top-3 rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">
                    جديد
                </span>

            @endif

        </div>


        <div class="p-5">

            @if($product->brand)

                <p class="text-xs text-gray-500">
                    {{ $product->brand->name }}
                </p>

            @endif


            <h3 class="mt-2 font-semibold">
                {{ $product->name }}
            </h3>


            @if($product->category)

                <p class="mt-1 text-sm text-gray-500">
                    {{ $product->category->name }}
                </p>

            @endif


            <div class="mt-4 flex items-center gap-2">

                @if($product->sale_price)

                    <span class="text-lg font-bold">
                        {{ number_format($product->sale_price, 2) }}
                        ج.م
                    </span>

                    <span class="text-sm text-gray-400 line-through">
                        {{ number_format($product->price, 2) }}
                        ج.م
                    </span>

                @else

                    <span class="text-lg font-bold">
                        {{ number_format($product->price, 2) }}
                        ج.م
                    </span>

                @endif

            </div>


            @if($product->isInStock())

                <p class="mt-3 text-sm text-green-600">
                    متوفر في المخزون
                </p>

            @else

                <p class="mt-3 text-sm text-red-600">
                    غير متوفر
                </p>

            @endif

        </div>

    </a>

</article>