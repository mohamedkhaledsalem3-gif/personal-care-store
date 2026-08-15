{{-- Announcement Bar --}}
<div class="bg-[#66755b] px-4 py-2.5 text-center text-xs font-medium text-white sm:text-sm">
    <div class="mx-auto max-w-7xl">
        شحن مجاني للطلبات فوق 250 ج.م
        <span class="mx-2 text-white/60">|</span>
        عروض محدودة اليوم
    </div>
</div>

<header class="border-b border-gray-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex min-h-16 items-center justify-between gap-4">

            {{-- Mobile Menu --}}
            <button
                type="button"
                class="inline-flex rounded-lg p-2 text-gray-700 hover:bg-gray-100 lg:hidden"
                aria-label="فتح القائمة"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>


            {{-- Logo --}}
            <a
                href="{{ route('storefront.home') }}"
                class="shrink-0 text-xl font-bold tracking-tight text-[#263126] sm:text-2xl"
            >
                Personal Care Store
            </a>


            {{-- Desktop Navigation --}}
            <nav class="hidden items-center gap-6 lg:flex">

                <a
                    href="{{ route('storefront.home') }}"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    الرئيسية
                </a>

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    المتجر
                </a>

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    التصنيفات
                </a>

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    العلامات التجارية
                </a>

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    العروض
                </a>

                <a
                    href="{{ route('storefront.home') }}#story"
                    class="text-sm font-medium text-gray-700 transition hover:text-[#66755b]"
                >
                    من نحن
                </a>

            </nav>


            {{-- Actions --}}
            <div class="flex items-center gap-1 sm:gap-2">

                {{-- Search --}}
                <button
                    type="button"
                    class="inline-flex rounded-full p-2 text-gray-700 transition hover:bg-gray-100 hover:text-[#66755b]"
                    aria-label="البحث"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-4.35-4.35m1.35-5.15a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z"
                        />
                    </svg>
                </button>


                @auth

                    {{-- Account --}}
                    <a
                        href="#"
                        class="hidden rounded-full p-2 text-gray-700 transition hover:bg-gray-100 hover:text-[#66755b] sm:inline-flex"
                        aria-label="حسابي"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6.75a3.75 3.75 0 1 1-7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0"
                            />
                        </svg>
                    </a>


                    {{-- Cart --}}
                    <a
                        href="{{ route('storefront.cart.index') }}"
                        class="relative inline-flex rounded-full p-2 text-gray-700 transition hover:bg-gray-100 hover:text-[#66755b]"
                        aria-label="السلة"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.25 3h1.386c.51 0 .955.343 1.086.836L5.5 6.75m0 0h14.25l-1.5 7.5H7.25L5.5 6.75Zm2.25 10.5h10.5M9 20.25h.008v.008H9v-.008Zm7.5 0h.008v.008h-.008v-.008Z"
                            />
                        </svg>

                        @php
                            $headerCart = app(\App\Services\CartService::class)
                                ->getCart(auth()->id());

                            $headerCartItemsCount = $headerCart->items->sum('quantity');
                        @endphp

                        @if($headerCartItemsCount > 0)
                            <span
                                class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-[#66755b] px-1 text-[10px] font-bold text-white"
                            >
                                {{ $headerCartItemsCount }}
                            </span>
                        @endif
                    </a>

                @else

                    <a
                        href="{{ route('login') }}"
                        class="hidden rounded-lg px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 hover:text-[#66755b] sm:inline-flex"
                    >
                        تسجيل الدخول
                    </a>

                @endauth

            </div>

        </div>

    </div>
</header>