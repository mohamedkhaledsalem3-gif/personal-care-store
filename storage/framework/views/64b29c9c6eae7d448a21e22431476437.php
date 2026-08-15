
<!DOCTYPE html>

<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

```
<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>إتمام الطلب | Personal Care</title>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family:
            Tahoma,
            Arial,
            sans-serif;
        background: #f5f7fa;
        color: #1f2937;
    }

    .container {
        width: min(1200px, 94%);
        margin: 40px auto;
    }

    .page-title {
        margin-bottom: 25px;
    }

    .page-title h1 {
        margin: 0 0 8px;
        font-size: 30px;
    }

    .page-title p {
        margin: 0;
        color: #6b7280;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 1fr)
            380px;

        gap: 25px;

        align-items: start;
    }

    .card {
        background: #ffffff;
        border-radius: 14px;
        padding: 25px;
        box-shadow:
            0 4px 18px
            rgba(0, 0, 0, 0.06);
    }

    .card-title {
        margin: 0 0 20px;
        font-size: 21px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 15px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 12px 13px;
        font-size: 15px;
        outline: none;
        background: #fff;
        transition: border-color 0.2s;
    }

    .form-control:focus {
        border-color: #2563eb;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .row {
        display: grid;
        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 15px;
    }

    .payment-box {
        border: 1px solid #dbeafe;
        background: #eff6ff;
        border-radius: 10px;
        padding: 15px;
    }

    .payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .payment-option input {
        width: 18px;
        height: 18px;
    }

    .payment-name {
        font-weight: 700;
    }

    .payment-description {
        margin-top: 5px;
        margin-right: 28px;
        color: #6b7280;
        font-size: 13px;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-info {
        min-width: 0;
    }

    .item-name {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .item-variant {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 5px;
    }

    .item-sku {
        color: #9ca3af;
        font-size: 12px;
    }

    .item-quantity {
        color: #4b5563;
        font-size: 14px;
        margin-top: 7px;
    }

    .item-price {
        text-align: left;
        white-space: nowrap;
        font-weight: 700;
    }

    .summary {
        margin-top: 20px;
        border-top: 1px solid #e5e7eb;
        padding-top: 15px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 12px;
    }

    .summary-label {
        color: #6b7280;
    }

    .summary-value {
        font-weight: 600;
    }

    .summary-total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #e5e7eb;
        font-size: 20px;
        font-weight: 800;
    }

    .submit-button {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 14px 18px;
        margin-top: 20px;
        background: #111827;
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.2s;
    }

    .submit-button:hover {
        opacity: 0.9;
    }

    .back-link {
        display: inline-block;
        margin-top: 15px;
        color: #2563eb;
        text-decoration: none;
    }

    .alert {
        padding: 14px 16px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .validation-errors {
        margin: 0;
        padding-right: 20px;
    }

    .stock-warning {
        color: #b45309;
        font-size: 12px;
        margin-top: 5px;
    }

    @media (max-width: 850px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .container {
            width: 92%;
            margin: 25px auto;
        }

        .card {
            padding: 18px;
        }

        .row {
            grid-template-columns: 1fr;
        }

        .page-title h1 {
            font-size: 25px;
        }
    }
</style>
```

</head>

<body>

<div class="container">

```
<div class="page-title">
    <h1>إتمام الطلب</h1>

    <p>
        أدخل بيانات التوصيل واختر طريقة الدفع لإتمام طلبك.
    </p>
</div>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert alert-success">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
    <div class="alert alert-error">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>



<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
    <div class="alert alert-error">

        <strong>
            يرجى مراجعة البيانات التالية:
        </strong>

        <ul class="validation-errors">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <?php echo e($error); ?>

                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>

    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="checkout-grid">


    
    <div class="card">

        <h2 class="card-title">
            بيانات العميل والتوصيل
        </h2>


        <form
            method="POST"
            action="<?php echo e(route('storefront.checkout.store')); ?>"
        >

            <?php echo csrf_field(); ?>


            
            <div class="form-group">

                <label for="customer_name">
                    اسم العميل
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    class="form-control"
                    value="<?php echo e(old('customer_name', $cart->user?->name)); ?>"
                    required
                >

            </div>


            
            <div class="form-group">

                <label for="customer_phone">
                    رقم الهاتف
                    <span class="required">*</span>
                </label>

                <input
                    type="tel"
                    id="customer_phone"
                    name="customer_phone"
                    class="form-control"
                    value="<?php echo e(old('customer_phone')); ?>"
                    placeholder="01000000000"
                    required
                >

            </div>


            
            <div class="row">

                <div class="form-group">

                    <label for="shipping_city">
                        المدينة
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="shipping_city"
                        name="shipping_city"
                        class="form-control"
                        value="<?php echo e(old('shipping_city')); ?>"
                        placeholder="مثال: بنها"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="shipping_area">
                        المنطقة
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="shipping_area"
                        name="shipping_area"
                        class="form-control"
                        value="<?php echo e(old('shipping_area')); ?>"
                        placeholder="مثال: المدينة"
                        required
                    >

                </div>

            </div>


            
            <div class="form-group">

                <label for="shipping_address">
                    عنوان التوصيل
                    <span class="required">*</span>
                </label>

                <textarea
                    id="shipping_address"
                    name="shipping_address"
                    class="form-control"
                    placeholder="اكتب العنوان بالتفصيل"
                    required
                ><?php echo e(old('shipping_address')); ?></textarea>

            </div>


            
            <div class="form-group">

                <label for="shipping_postal_code">
                    الرمز البريدي
                </label>

                <input
                    type="text"
                    id="shipping_postal_code"
                    name="shipping_postal_code"
                    class="form-control"
                    value="<?php echo e(old('shipping_postal_code')); ?>"
                    placeholder="اختياري"
                >

            </div>


            
            <div class="form-group">

                <label for="customer_note">
                    ملاحظات الطلب
                </label>

                <textarea
                    id="customer_note"
                    name="customer_note"
                    class="form-control"
                    placeholder="أي ملاحظات خاصة بالتوصيل..."
                ><?php echo e(old('customer_note')); ?></textarea>

            </div>


            
            <div class="form-group">

                <label>
                    طريقة الدفع
                    <span class="required">*</span>
                </label>

                <div class="payment-box">

                    <label class="payment-option">

                        <input
                            type="radio"
                            name="payment_method"
                            value="cod"
                            <?php echo e(old('payment_method', 'cod') === 'cod' ? 'checked' : ''); ?>

                            required
                        >

                        <span class="payment-name">
                            الدفع عند الاستلام
                        </span>

                    </label>

                    <div class="payment-description">
                        ادفع قيمة الطلب عند استلام المنتجات.
                    </div>

                </div>

            </div>


            <button
                type="submit"
                class="submit-button"
            >
                تأكيد الطلب
            </button>


            <a
                href="<?php echo e(route('storefront.cart.index')); ?>"
                class="back-link"
            >
                ← العودة إلى السلة
            </a>

        </form>

    </div>


    
    <div class="card">

        <h2 class="card-title">
            ملخص الطلب
        </h2>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php
                $variant = $item->variant;
                $product = $variant?->product;

                $unitPrice = $variant
                    ? $variant->current_price
                    : $item->unit_price;

                $itemTotal =
                    $unitPrice * $item->quantity;

                $availableQuantity =
                    $variant?->inventory
                        ? $variant->inventory->available_quantity
                        : 0;
            ?>


            <div class="cart-item">

                <div class="item-info">

                    <div class="item-name">
                        <?php echo e($product?->name ?? 'منتج'); ?>

                    </div>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant?->name): ?>

                        <div class="item-variant">
                            <?php echo e($variant->name); ?>

                        </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant?->sku): ?>

                        <div class="item-sku">
                            SKU:
                            <?php echo e($variant->sku); ?>

                        </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                    <div class="item-quantity">
                        الكمية:
                        <?php echo e($item->quantity); ?>

                    </div>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableQuantity < $item->quantity): ?>

                        <div class="stock-warning">
                            الكمية المتاحة حاليًا:
                            <?php echo e($availableQuantity); ?>

                        </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>


                <div class="item-price">

                    <?php echo e(number_format($itemTotal, 2)); ?>


                    جنيه

                </div>

            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        <div class="summary">

            
            <div class="summary-row">

                <span class="summary-label">
                    إجمالي المنتجات
                </span>

                <span class="summary-value">
                    <?php echo e(number_format($cart->subtotal, 2)); ?>

                    جنيه
                </span>

            </div>


            
            <div class="summary-row">

                <span class="summary-label">
                    الشحن
                </span>

                <span class="summary-value">
                    0.00 جنيه
                </span>

            </div>


            
            <div class="summary-row">

                <span class="summary-label">
                    الخصم
                </span>

                <span class="summary-value">
                    0.00 جنيه
                </span>

            </div>


            
            <div class="summary-row summary-total">

                <span>
                    الإجمالي
                </span>

                <span>
                    <?php echo e(number_format($cart->subtotal, 2)); ?>

                    جنيه
                </span>

            </div>

        </div>

    </div>

</div>
```

</div>

</body>
</html>
<?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/checkout/index.blade.php ENDPATH**/ ?>