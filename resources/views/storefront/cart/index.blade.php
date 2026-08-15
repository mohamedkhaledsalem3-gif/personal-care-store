@extends('storefront.layouts.app')

@section('title', 'السلة')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-shopping-cart"></i> سلة التسوق</h1>
        </div>
    </div>

    @if($cart->items->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->productVariant->product?->name }}</strong><br>
                                        <small class="text-muted">{{ $item->productVariant->name }}</small>
                                    </td>
                                    <td>{{ $item->unit_price }} ر.س</td>
                                    <td>
                                        <form action="{{ route('storefront.cart.items.update', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" style="width: 60px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">تحديث</button>
                                        </form>
                                    </td>
                                    <td>{{ $item->quantity * $item->unit_price }} ر.س</td>
                                    <td>
                                        <form action="{{ route('storefront.cart.items.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل تريد حذف هذا المنتج?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">ملخص الطلب</h5>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span>الإجمالي الجزئي:</span>
                            <strong>{{ $cart->items->sum(fn($item) => $item->quantity * $item->unit_price) }} ر.س</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>الشحن:</span>
                            <strong>مجاني</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span><strong>الإجمالي:</strong></span>
                            <strong class="text-danger" style="font-size: 1.3rem;">{{ $cart->items->sum(fn($item) => $item->quantity * $item->unit_price) }} ر.س</strong>
                        </div>
                        <a href="{{ route('storefront.checkout.index') }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-credit-card"></i> الذهاب للدفع
                        </a>
                        <form action="{{ route('storefront.cart.clear') }}" method="POST" class="d-inline w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('هل تريد تفريغ السلة?')">
                                <i class="fas fa-trash"></i> تفريغ السلة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h5>السلة فارغة</h5>
                    <p class="text-muted">لم تضف أي منتجات إلى السلة حتى الآن</p>
                    <a href="{{ route('storefront.products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> تابع التسوق
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection