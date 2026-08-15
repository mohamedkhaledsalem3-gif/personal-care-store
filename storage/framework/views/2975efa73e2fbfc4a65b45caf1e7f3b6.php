<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        تأكيد الطلب <?php echo e($order->order_number); ?>

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
            <?php echo e($order->order_number); ?>

        </div>

    </div>


    <div class="grid">

        
        <div>

            <div class="card">

                <h2>
                    تفاصيل الطلب
                </h2>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <div class="item">

                        <?php
                            $image = $item->variant?->product?->images?->first();
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image && !empty($image->path)): ?>
                            <img
                                src="<?php echo e(asset('storage/' . $image->path)); ?>"
                                alt="<?php echo e($item->product_name); ?>"
                                class="product-image"
                            >
                        <?php else: ?>
                            <div class="product-image"></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="item-info">

                            <h3>
                                <?php echo e($item->product_name); ?>

                            </h3>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_name): ?>
                                <p>
                                    النوع:
                                    <?php echo e($item->variant_name); ?>

                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->sku): ?>
                                <p>
                                    SKU:
                                    <?php echo e($item->sku); ?>

                                </p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <p>
                                الكمية:
                                <?php echo e($item->quantity); ?>

                            </p>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item->attributes)): ?>

                                <p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->attributes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($attribute['attribute'])): ?>
                                            <?php echo e($attribute['attribute']); ?>:
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php echo e($attribute['value'] ?? ''); ?>


                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$loop->last): ?>
                                            -
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </p>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>

                        <div class="item-price">

                            <strong>
                                <?php echo e(number_format((float) $item->total, 2)); ?>

                                ج.م
                            </strong>

                            <span>
                                <?php echo e(number_format((float) $item->unit_price, 2)); ?>

                                ×
                                <?php echo e($item->quantity); ?>

                            </span>

                        </div>

                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>


            
            <div class="card">

                <h2>
                    بيانات التوصيل
                </h2>

                <div class="info-row">
                    <span class="info-label">
                        الاسم
                    </span>

                    <span class="info-value">
                        <?php echo e($order->customer_name); ?>

                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        الهاتف
                    </span>

                    <span class="info-value" dir="ltr">
                        <?php echo e($order->customer_phone); ?>

                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        المدينة
                    </span>

                    <span class="info-value">
                        <?php echo e($order->shipping_city); ?>

                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">
                        المنطقة
                    </span>

                    <span class="info-value">
                        <?php echo e($order->shipping_area); ?>

                    </span>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->shipping_postal_code): ?>

                    <div class="info-row">
                        <span class="info-label">
                            الرمز البريدي
                        </span>

                        <span class="info-value">
                            <?php echo e($order->shipping_postal_code); ?>

                        </span>
                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div style="margin-top: 18px;">

                    <div class="info-label"
                         style="margin-bottom: 8px;">
                        عنوان التوصيل
                    </div>

                    <div class="address">
                        <?php echo e($order->shipping_address); ?>

                    </div>

                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->customer_note): ?>

                    <div class="note">

                        <strong>
                            ملاحظات العميل:
                        </strong>

                        <br>

                        <?php echo e($order->customer_note); ?>


                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>

        </div>


        
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
                        <?php echo e($order->order_number); ?>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        حالة الطلب
                    </span>

                    <span>
                        <span class="status">
                            <?php echo e($order->status); ?>

                        </span>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        طريقة الدفع
                    </span>

                    <span class="info-value">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_method === 'cod'): ?>
                            الدفع عند الاستلام
                        <?php else: ?>
                            <?php echo e($order->payment_method); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        حالة الدفع
                    </span>

                    <span class="info-value">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($order->payment_status === 'pending'): ?>
                            قيد الانتظار
                        <?php else: ?>
                            <?php echo e($order->payment_status); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        المنتجات
                    </span>

                    <span class="info-value">
                        <?php echo e($order->items->sum('quantity')); ?>

                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        إجمالي المنتجات
                    </span>

                    <span class="info-value">
                        <?php echo e(number_format((float) $order->subtotal, 2)); ?>

                        ج.م
                    </span>

                </div>

                <div class="info-row">

                    <span class="info-label">
                        الشحن
                    </span>

                    <span class="info-value">

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->shipping_fee > 0): ?>

                            <?php echo e(number_format((float) $order->shipping_fee, 2)); ?>

                            ج.م

                        <?php else: ?>

                            مجاني

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </span>

                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $order->discount > 0): ?>

                    <div class="info-row">

                        <span class="info-label">
                            الخصم
                        </span>

                        <span class="info-value">
                            -
                            <?php echo e(number_format((float) $order->discount, 2)); ?>

                            ج.م
                        </span>

                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="total-row">

                    <span>
                        الإجمالي
                    </span>

                    <strong class="total-price">
                        <?php echo e(number_format((float) $order->total, 2)); ?>

                        ج.م
                    </strong>

                </div>

            </div>


            
            <div class="card">

                <h2>
                    ماذا تريد أن تفعل؟
                </h2>

                <div class="actions">

                    <a
                        href="<?php echo e(route('storefront.products.index')); ?>"
                        class="btn btn-primary"
                    >
                        متابعة التسوق
                    </a>

                    <a
                        href="<?php echo e(route('storefront.orders.index')); ?>"
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
<?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/orders/show.blade.php ENDPATH**/ ?>