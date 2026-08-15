<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        تأكيد الطلب {{ $order->order_number }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fa;
            color: #1f2937;
            font-family:
                "Segoe UI",
                Tahoma,
                Arial,
                sans-serif;
        }

        .container {
            width: min(1100px, calc(100% - 30px));
            margin: 40px auto;
        }

        .success-box {
            background: #ffffff;
            border-radius: 18px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.07);
            margin-bottom: 25px;
        }

        .success-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            font-weight: bold;
        }

        .success-box h1 {
            margin: 0 0 10px;
            color: #166534;
            font-size: 30px;
        }

        .success-box p {
            margin: 8px 0;
            color: #64748b;
            font-size: 16px;
        }

        .order-number {
            display: inline-block;
            margin-top: 12px;
            padding: 10px 18px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
            direction: ltr;
        }

        .grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 21px;
            color: #111827;
        }

        .item {
            display: flex;
            gap: 18px;
            padding: 18px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .item:last-child {
            border-bottom: 0;
        }

        .product-image {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            background: #f3f4f6;
            object-fit: cover;
            flex-shrink: 0;
        }

        .item-info {
            flex: 1;
        }

        .item-info h3 {
            margin: 0 0 7px;
            font-size: 17px;
        }

        .item-info p {
            margin: 4px 0;
            color: #64748b;
            font-size: 14px;
        }

        .item-price {
            text-align: left;
            min-width: 120px;
        }

        .item-price strong {
            display: block;
            font-size: 17px;
            color: #111827;
        }

        .item-price span {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 11px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .info-label {
            color: #64748b;
        }

        .info-value {
            font-weight: 600;
            text-align: left;
        }

        .total-row {
            margin-top: 15px;
            padding-top: 18px;
            border-top: 2px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-row strong {
            font-size: 23px;
            color: #111827;
        }

        .total-price {
            color: #16a34a !important;
        }

        .status {
            display: inline-block;
            padding: 7px 13px;
            border-radius: 20px;
            background: #fef3c7;
            color: #92400e;
            font-size: 13px;
            font-weight: 700;
        }

        .address {
            line-height: 1.9;
            color: #374151;
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            border: 0;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .note {
            margin-top: 18px;
            padding: 14px;
            border-radius: 10px;
            background: #fff7ed;
            color: #9a3412;
            line-height: 1.8;
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .item {
                flex-wrap: wrap;
            }

            .item-price {
                width: 100%;
                text-align: right;
            }

            .container {
                width: min(100% - 20px, 1100px);
                margin: 20px auto;
            }

            .success-box {
                padding: 25px 15px;
            }

            .success-box h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    {{-- رسالة نجاح --}}
    <div class="success-box">

        <div class="success-icon">
            ✓
        </div>

        <h1>
            تم تأكيد طلبك بنجاح
        </h1>

        <p>
            شكرًا لك، تم استلام طلبك وسيتم تجهيزه قريبًا.
        </p>

        <div class="order-number">
            رقم الطلب:
            {{ $order->order_number }}
        </div>

    </div>


    <div class="grid">

        {{-- المنتجات --}}
        <div>

            <div class="card">

                <h2>
                    تفاصيل الطلب
                </h2>

                @foreach ($order->items as $item)

                    <div class="item">

                        @php
                            $image = $item->variant?->product?->images?->first();
                        @endphp

                        @if ($image && !empty($image->path))
                            <img
                                src="{{ asset('storage/' . $image->path) }}"
                                alt="{{ $item->product_name }}"
                                class="product-image"
                            >
                        @else
                            <div class="product-image"></div>
                        @endif

                        <div class="item-info">

                            <h3>
                                {{ $item->product_name }}
                            </h3>

                            @if ($item->variant_name)
                                <p>
                                    النوع:
                                    {{ $item->variant_name }}
                                </p>
                            @endif

                            @if ($item->sku)
                                <p>
                                    SKU:
                                    {{ $item->sku }}
                                </p>
                            @endif

                            <p>
                                الكمية:
                                {{ $item->quantity }}
                            </p>

                            @if (!empty($item->attributes))

                                <p>
                                    @foreach ($item->attributes as $attribute)

                                        @if (!empty($attribute['attribute']))
                                            {{ $attribute['attribute'] }}:
                                        @endif

                                        {{ $attribute['value'] ?? '' }}

                                        @if (!$loop->last)
                                            -
                                        @endif

                                    @endforeach
                                </p>

                            @endif

                        </div>

                        <div class="item-price">

                            <strong>
                                {{ number_format((float) $item->total, 2) }}
                                ج.م
                            </strong>

                            <span>
                                {{ number_format((float) $item->unit_price, 2) }}
                                ×
                                {{ $item->quantity }}
                            </span>

                        </div>

                    </div>

                @endforeach

            </div>


            {{-- بيانات العميل --}}
            <div class="card">

                <h2>
                    بيانات التوصيل
                </h2>

                <div class="info-row">
                    <span class="info-label">
                        الاسم
                    </span>

                    <span class="info-value">
                        {{ $order->customer_name }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        الهاتف
                    </span>

                    <span class="info-value" dir="ltr">
                        {{ $order->customer_phone }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        المدينة
                    </span>

                    <span class="info-value">
                        {{ $order->shipping_city }}
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        المنطقة
                    </span>

                    <span class="info-value">
                        {{ $order->shipping_area }}
                    </span>
                </div>

                @if ($order->shipping_postal_code)

                    <div class="info-row">
                        <span class="info-label">
                            الرمز البريدي
                        </span>

                        <span class="info-value">
                            {{ $order->shipping_postal_code }}
                        </span>
                    </div>

                @endif

                <div style="margin-top: 18px;">

                    <div class="info-label"
                         style="margin-bottom: 8px;">
                        عنوان التوصيل
                    </div>

                    <div class="address">
                        {{ $order->shipping_address }}
                    </div>

                </div>

                @if ($order->customer_note)

                    <div class="note">

                        <strong>
                            ملاحظات العميل:
                        </strong>

                        <br>

                        {{ $order->customer_note }}

                    </div>

                @endif

            </div>

        </div>


        {{-- ملخص الطلب --}}
        <div>

            <div class="card">

                <h2>
                    ملخص الطلب
                </h2>

                <div class="info-row">

                    <span class="info-label">
                        رقم الطلب
                    </span>

                    <span class="info-value"
                          dir="ltr">
                        {{ $order->order_number }}
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        حالة الطلب
                    </span>

                    <span>
                        <span class="status">
                            {{ $order->status }}
                        </span>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        طريقة الدفع
                    </span>

                    <span class="info-value">
                        @if ($order->payment_method === 'cod')
                            الدفع عند الاستلام
                        @else
                            {{ $order->payment_method }}
                        @endif
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        حالة الدفع
                    </span>

                    <span class="info-value">
                        @if ($order->payment_status === 'pending')
                            قيد الانتظار
                        @else
                            {{ $order->payment_status }}
                        @endif
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        المنتجات
                    </span>

                    <span class="info-value">
                        {{ $order->items->sum('quantity') }}
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        إجمالي المنتجات
                    </span>

                    <span class="info-value">
                        {{ number_format((float) $order->subtotal, 2) }}
                        ج.م
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        الشحن
                    </span>

                    <span class="info-value">

                        @if ((float) $order->shipping_fee > 0)

                            {{ number_format((float) $order->shipping_fee, 2) }}
                            ج.م

                        @else

                            مجاني

                        @endif

                    </span>

                </div>

                @if ((float) $order->discount > 0)

                    <div class="info-row">

                        <span class="info-label">
                            الخصم
                        </span>

                        <span class="info-value">
                            -
                            {{ number_format((float) $order->discount, 2) }}
                            ج.م
                        </span>

                    </div>

                @endif

                <div class="total-row">

                    <span>
                        الإجمالي
                    </span>

                    <strong class="total-price">
                        {{ number_format((float) $order->total, 2) }}
                        ج.م
                    </strong>

                </div>

            </div>


            {{-- أزرار --}}
            <div class="card">

                <h2>
                    ماذا تريد أن تفعل؟
                </h2>

                <div class="actions">

                    <a
                        href="{{ route('storefront.products.index') }}"
                        class="btn btn-primary"
                    >
                        متابعة التسوق
                    </a>

                    <a
                        href="{{ route('storefront.orders.index') }}"
                        class="btn btn-secondary"
                    >
                        طلباتي
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
