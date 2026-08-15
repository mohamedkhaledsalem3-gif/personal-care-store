@extends('storefront.layouts.app')

@section('title', 'طلباتي')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">
                طلباتي
            </h1>

            <p class="mt-2 text-sm text-gray-600">
                يمكنك متابعة جميع طلباتك السابقة والحالية.
            </p>
        </div>


        @if($orders->count() > 0)

            {{-- Orders --}}
            <div class="space-y-4">

                @foreach($orders as $order)

                    <article class="overflow-hidden rounded-xl border bg-white shadow-sm">

                        <div class="flex flex-col gap-4 p-5 md:flex-row md:items-center md:justify-between">

                            {{-- Order Information --}}
                            <div>

                                <div class="flex flex-wrap items-center gap-3">

                                    <h2 class="font-semibold text-gray-900">
                                        الطلب #{{ $order->id }}
                                    </h2>

                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700">
                                        {{ $order->status }}
                                    </span>

                                </div>


                                <div class="mt-3 space-y-1 text-sm text-gray-600">

                                    <p>
                                        التاريخ:
                                        {{ optional($order->placed_at)->format('Y-m-d H:i') ?? '—' }}
                                    </p>

                                    <p>
                                        عدد المنتجات:
                                        {{ $order->items_count }}
                                    </p>

                                </div>

                            </div>


                            {{-- Order Total --}}
                            <div class="text-right">

                                <div class="text-lg font-bold text-gray-900">
                                    {{ number_format((float) $order->total_amount, 2) }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    جنيه
                                </div>

                            </div>

                        </div>


                        {{-- Actions --}}
                        <div class="flex justify-end border-t bg-gray-50 px-5 py-3">

                            <a
                                href="{{ route('storefront.orders.show', $order) }}"
                                class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700"
                            >
                                عرض تفاصيل الطلب
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>


            {{-- Pagination --}}
            @if($orders->hasPages())

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>

            @endif


        @else

            {{-- Empty State --}}
            <div class="rounded-xl border bg-white px-6 py-16 text-center shadow-sm">

                <h2 class="text-xl font-semibold text-gray-900">
                    لا توجد طلبات حتى الآن
                </h2>

                <p class="mt-2 text-sm text-gray-600">
                    عندما تقوم بإجراء أول طلب، سيظهر هنا.
                </p>

                <a
                    href="{{ route('storefront.products.index') }}"
                    class="mt-6 inline-flex items-center rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700"
                >
                    تصفح المنتجات
                </a>

            </div>

        @endif

    </div>

@endsection