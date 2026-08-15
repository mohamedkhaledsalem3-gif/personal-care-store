@extends('storefront.layouts.app')

@section('title', 'الصفحة الرئيسية')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-light rounded-3 p-5 text-center">
                <h1 class="display-4 mb-3">مرحباً بك في متجر العناية الشخصية</h1>
                <p class="lead mb-4">أفضل منتجات العناية الشخصية بجودة عالية وأسعار مناسبة</p>
                <a href="{{ route('storefront.products.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> تسوق الآن
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    @if($featuredProducts->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-star"></i> المنتجات المميزة</h2>
        </div>
        @foreach($featuredProducts as $product)
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card product-card">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->image_path }}" class="card-img-top product-image" alt="{{ $product->name }}">
                @else
                    <div class="card-img-top product-image d-flex align-items-center justify-content-center">
                        <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                    <div class="mb-3">
                        @if($product->sale_price)
                            <span class="old-price">{{ $product->price }} ر.س</span>
                        @endif
                        <span class="price">{{ $product->sale_price ?? $product->price }} ر.س</span>
                    </div>
                    @if($product->isInStock())
                        <a href="{{ route('storefront.products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-ban"></i> غير متوفر
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- New Products -->
    @if($newProducts->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-sparkles"></i> أحدث المنتجات</h2>
        </div>
        @foreach($newProducts as $product)
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card product-card">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->image_path }}" class="card-img-top product-image" alt="{{ $product->name }}">
                @else
                    <div class="card-img-top product-image d-flex align-items-center justify-content-center">
                        <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                    <div class="mb-3">
                        @if($product->sale_price)
                            <span class="old-price">{{ $product->price }} ر.س</span>
                        @endif
                        <span class="price">{{ $product->sale_price ?? $product->price }} ر.س</span>
                    </div>
                    @if($product->isInStock())
                        <a href="{{ route('storefront.products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-ban"></i> غير متوفر
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Best Sellers -->
    @if($bestSellerProducts->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4"><i class="fas fa-fire"></i> الأكثر مبيعاً</h2>
        </div>
        @foreach($bestSellerProducts as $product)
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card product-card">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->image_path }}" class="card-img-top product-image" alt="{{ $product->name }}">
                @else
                    <div class="card-img-top product-image d-flex align-items-center justify-content-center">
                        <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($product->short_description, 60) }}</p>
                    <div class="mb-3">
                        @if($product->sale_price)
                            <span class="old-price">{{ $product->price }} ر.س</span>
                        @endif
                        <span class="price">{{ $product->sale_price ?? $product->price }} ر.س</span>
                    </div>
                    @if($product->isInStock())
                        <a href="{{ route('storefront.products.show', $product) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-ban"></i> غير متوفر
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Brands -->
    @if($brands->count() > 0)
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4">العلامات التجارية</h2>
        </div>
        @foreach($brands as $brand)
        <div class="col-6 col-md-3 col-lg-2 mb-4">
            <div class="card text-center p-3">
                @if($brand->logo_path)
                    <img src="{{ $brand->logo_path }}" class="card-img-top" style="height: 100px; object-fit: contain;" alt="{{ $brand->name }}">
                @endif
                <div class="card-body">
                    <h6 class="card-title">{{ $brand->name }}</h6>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection