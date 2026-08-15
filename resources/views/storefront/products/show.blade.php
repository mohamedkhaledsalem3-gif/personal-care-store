
@extends('storefront.layouts.app')

@section('title', $product->name . ' - Personal Care Store')

@section('content')

<section class="py-12">
    <div class="mx-auto max-w-7xl px-4">

        {{-- Messages --}}
        @if (session('success'))
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="grid gap-10 lg:grid-cols-2">

            {{-- Product Images --}}
            <div>

                @php
                    $primaryImage = $product->images->first();
                @endphp

                <div class="overflow-hidden rounded-3xl bg-gray-100">
                    @if ($primaryImage)
                        <img
                            src="{{ asset('storage/' . $primaryImage->image_path) }}"
                            alt="{{ $primaryImage->alt_text ?: $product->name }}"
                            class="aspect-square h-full w-full object-cover"
                        >
                    @else
                        <div class="flex aspect-square items-center justify-center text-gray-400">
                            لا توجد صورة للمنتج
                        </div>
                    @endif
                </div>

            </div>


            {{-- Product Information --}}
            <div>

                @if ($product->brand)
                    <p class="text-sm font-medium text-gray-500">
                        {{ $product->brand->name }}
                    </p>
                @endif

                <h1 class="mt-2 text-3xl font-bold md:text-4xl">
                    {{ $product->name }}
                </h1>

                @if ($product->short_description)
                    <p class="mt-4 text-gray-600">
                        {{ $product->short_description }}
                    </p>
                @endif


                {{-- Description --}}
                @if ($product->description)
                    <div class="prose mt-6 max-w-none">
                        {!! $product->description !!}
                    </div>
                @endif


                {{-- Variants --}}
                @if ($product->variants->isNotEmpty())

                    <form
                        method="POST"
                        action="{{ route('storefront.cart.items.store') }}"
                        class="mt-8"
                    >
                        @csrf

                        <div>
                            <h2 class="mb-4 text-lg font-semibold">
                                اختر الحجم / النوع
                            </h2>

                            <div class="space-y-3">

                                @foreach ($product->variants as $variant)

                                    @php
                                        $available = $variant->inventory
                                            ? max(
                                                0,
                                                $variant->inventory->quantity
                                                - $variant->inventory->reserved_quantity
                                            )
                                            : 0;

                                        $price = $variant->sale_price
                                            ?? $variant->price;

                                        $isAvailable =
                                            $variant->is_active
                                            && $available > 0;
                                    @endphp

                                    <label
                                        class="flex cursor-pointer items-center justify-between rounded-xl border p-4 transition
                                            {{ $isAvailable ? 'hover:border-black' : 'cursor-not-allowed opacity-50' }}"
                                    >

                                        <div class="flex items-center gap-3">

                                            <input
                                                type="radio"
                                                name="variant_id"
                                                value="{{ $variant->id }}"
                                                {{ $variant->is_default && $isAvailable ? 'checked' : '' }}
                                                {{ !$isAvailable ? 'disabled' : '' }}
                                                class="h-4 w-4"
                                            >

                                            <div>

                                                <p class="font-semibold">
                                                    {{ $variant->name }}
                                                </p>

                                                <p class="mt-1 text-sm text-gray-500">
                                                    SKU: {{ $variant->sku }}
                                                </p>

                                                @if ($isAvailable)
                                                    <p class="mt-1 text-xs text-green-600">
                                                        متوفر: {{ $available }}
                                                    </p>
                                                @else
                                                    <p class="mt-1 text-xs text-red-600">
                                                        غير متوفر
                                                    </p>
                                                @endif

                                            </div>

                                        </div>


                                        <div class="text-left">

                                            @if ($variant->sale_price)
                                                <p class="font-bold">
                                                    {{ number_format($variant->sale_price, 2) }}
                                                    ج.م
                                                </p>

                                                <p class="text-sm text-gray-400 line-through">
                                                    {{ number_format($variant->price, 2) }}
                                                    ج.م
                                                </p>
                                            @else
                                                <p class="font-bold">
                                                    {{ number_format($variant->price, 2) }}
                                                    ج.م
                                                </p>
                                            @endif

                                        </div>

                                    </label>

                                @endforeach

                            </div>
                        </div>


                        {{-- Quantity --}}
                        <div class="mt-6">

                            <label
                                for="quantity"
                                class="mb-2 block text-sm font-semibold"
                            >
                                الكمية
                            </label>

                            <input
                                id="quantity"
                                type="number"
                                name="quantity"
                                value="{{ old('quantity', 1) }}"
                                min="1"
                                class="w-32 rounded-xl border border-gray-300 px-4 py-3"
                            >

                        </div>


                        {{-- Add To Cart --}}
                        <div class="mt-6">

                            @auth

                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-black px-6 py-4 font-semibold text-white transition hover:bg-gray-800"
                                >
                                    إضافة إلى السلة
                                </button>

                            @else

                                <a
                                    href="{{ route('login') }}"
                                    class="block w-full rounded-xl bg-black px-6 py-4 text-center font-semibold text-white transition hover:bg-gray-800"
                                >
                                    سجل الدخول لإضافة المنتج إلى السلة
                                </a>

                            @endauth

                        </div>

                    </form>

                @else

                    <div class="mt-8 rounded-xl bg-yellow-50 p-4 text-yellow-700">
                        لا توجد خيارات متاحة لهذا المنتج حاليًا.
                    </div>

                @endif

            </div>

        </div>

    </div>
</section>

@endsection
