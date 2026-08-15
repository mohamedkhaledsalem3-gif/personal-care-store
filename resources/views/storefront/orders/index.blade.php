@extends('storefront.layouts.app')

@section('title', 'طلباتي')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1><i class="fas fa-box"></i> طلباتي</h1>
        </div>
    </div>

    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">الطلب #{{ $order->order_number }}</h5>
                        <p class="card-text">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> {{ $order->placed_at?->format('d/m/Y H:i') }}
                            </small>
                        </p>
                        <hr>
                        <div class="mb-3">
                            <strong>الحالة:</strong>
                            <span class="badge bg-info">{{ $order->status }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>الإجمالي:</strong>
                            <span class="text-danger" style="font-size: 1.2rem;">{{ $order->total }} ر.س</span>
                        </div>
                        <div>
                            <strong>عدد المنتجات:</strong>
                            {{ $order->items_count ?? 0 }}
                        </div>
                        <hr>
                        <a href="{{ route('storefront.orders.show', $order) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="row">
            <div class="col-12">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card text-center p-5">
                    <i class="fas fa-box" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h5>لا توجد طلبات</h5>
                    <p class="text-muted">لم تقم بأي طلبات حتى الآن</p>
                    <a href="{{ route('storefront.products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag"></i> ابدأ التسوق
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection