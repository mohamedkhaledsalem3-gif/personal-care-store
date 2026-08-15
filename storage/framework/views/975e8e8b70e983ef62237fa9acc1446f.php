

<?php $__env->startSection('title', 'السلة - Personal Care Store'); ?>

<?php $__env->startSection('content'); ?>

<section class="py-12">
    <div class="mx-auto max-w-7xl px-4">

        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                سلة التسوق
            </h1>

            <p class="mt-2 text-gray-500">
                راجع المنتجات والكميات قبل إتمام الطلب.
            </p>
        </div>


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-red-700">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-red-700">
                <ul class="list-disc space-y-1 pr-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cart->items->isEmpty()): ?>

            <div class="rounded-2xl bg-white p-12 text-center shadow-sm">

                <div class="text-5xl">
                    🛒
                </div>

                <h2 class="mt-4 text-xl font-bold text-gray-900">
                    السلة فارغة
                </h2>

                <p class="mt-2 text-gray-500">
                    لم تضف أي منتجات إلى السلة حتى الآن.
                </p>

                <a
                    href="<?php echo e(route('storefront.home')); ?>"
                    class="mt-6 inline-flex rounded-xl bg-black px-6 py-3 font-semibold text-white transition hover:bg-gray-800"
                >
                    العودة إلى المتجر
                </a>

            </div>

        <?php else: ?>

            <div class="grid gap-8 lg:grid-cols-3">

                
                <div class="space-y-4 lg:col-span-2">

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <?php
                            $variant = $item->variant;
                            $product = $variant?->product;

                            $image = $product?->images?->first();

                            $lineTotal =
                                $item->quantity * (float) $item->unit_price;

                            $availableQuantity = $variant?->inventory
                                ? $variant->inventory->available_quantity
                                : 0;
                        ?>

                        <article
                            class="rounded-2xl bg-white p-5 shadow-sm transition hover:shadow-md"
                        >

                            <div class="flex flex-col gap-5 sm:flex-row">

                                
                                <div
                                    class="h-36 w-full shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-36 sm:w-36"
                                >

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>

                                        <img
                                            src="<?php echo e(asset('storage/' . $image->image_path)); ?>"
                                            alt="<?php echo e($image->alt_text ?: $product?->name); ?>"
                                            class="h-full w-full object-cover"
                                        >

                                    <?php else: ?>

                                        <div class="flex h-full items-center justify-center text-sm text-gray-400">
                                            لا توجد صورة
                                        </div>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                </div>


                                
                                <div class="flex-1">

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product): ?>

                                        <h2 class="text-lg font-bold text-gray-900">
                                            <?php echo e($product->name); ?>

                                        </h2>

                                    <?php else: ?>

                                        <h2 class="text-lg font-bold text-gray-900">
                                            منتج غير متوفر
                                        </h2>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant): ?>

                                        <div class="mt-2">
                                            <span class="rounded-lg bg-gray-100 px-3 py-1 text-sm text-gray-700">
                                                <?php echo e($variant->name); ?>

                                            </span>
                                        </div>

                                        <p class="mt-2 text-xs text-gray-400">
                                            SKU: <?php echo e($variant->sku); ?>

                                        </p>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                                    
                                    <div class="mt-4">

                                        <span class="font-bold text-gray-900">
                                            <?php echo e(number_format((float) $item->unit_price, 2)); ?>

                                            ج.م
                                        </span>

                                        <span class="text-sm text-gray-500">
                                            / الوحدة
                                        </span>

                                    </div>


                                    
                                    <div class="mt-5 flex flex-wrap items-center gap-4">

                                        <span class="text-sm font-medium text-gray-600">
                                            الكمية:
                                        </span>

                                        <span
                                            class="flex h-10 min-w-16 items-center justify-center rounded-xl border border-gray-300 bg-gray-50 px-4 text-sm font-semibold"
                                        >
                                            <?php echo e($item->quantity); ?>

                                        </span>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($availableQuantity > 0): ?>

                                            <span class="text-xs text-gray-400">
                                                المتاح حاليًا: <?php echo e($availableQuantity); ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-xs font-medium text-red-600">
                                                غير متوفر حاليًا
                                            </span>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    </div>

                                </div>


                                
                                <div
                                    class="flex flex-row items-center justify-between gap-4 sm:flex-col sm:items-end sm:justify-between"
                                >

                                    <div class="text-right">

                                        <p class="text-xs text-gray-500">
                                            الإجمالي
                                        </p>

                                        <p class="mt-1 text-xl font-bold text-gray-900">
                                            <?php echo e(number_format($lineTotal, 2)); ?>

                                            ج.م
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>


                
                <aside>

                    <div class="sticky top-6 rounded-2xl bg-white p-6 shadow-sm">

                        <h2 class="text-xl font-bold text-gray-900">
                            ملخص السلة
                        </h2>


                        <div class="mt-6 space-y-4">

                            
                            <div class="flex items-center justify-between">

                                <span class="text-gray-500">
                                    عدد المنتجات
                                </span>

                                <span class="font-semibold text-gray-900">
                                    <?php echo e($cart->items_count); ?>

                                </span>

                            </div>


                            
                            <div class="flex items-center justify-between">

                                <span class="text-gray-500">
                                    المجموع الفرعي
                                </span>

                                <span class="font-semibold text-gray-900">
                                    <?php echo e(number_format($cart->subtotal, 2)); ?>

                                    ج.م
                                </span>

                            </div>


                            
                            <div class="flex items-center justify-between">

                                <span class="text-gray-500">
                                    الشحن
                                </span>

                                <span class="text-sm text-gray-500">
                                    يُحسب لاحقًا
                                </span>

                            </div>


                            
                            <div class="border-t pt-4">

                                <div class="flex items-center justify-between">

                                    <span class="text-lg font-bold text-gray-900">
                                        الإجمالي
                                    </span>

                                    <span class="text-xl font-bold text-gray-900">
                                        <?php echo e(number_format($cart->subtotal, 2)); ?>

                                        ج.م
                                    </span>

                                </div>

                            </div>

                        </div>


                        
                        <a
                            href="<?php echo e(route('storefront.checkout.index')); ?>"
                            class="mt-6 block w-full rounded-xl bg-black px-6 py-3 text-center font-semibold text-white transition hover:bg-gray-800"
                        >
                            إتمام الطلب
                        </a>


                        
                        <a
                            href="<?php echo e(route('storefront.home')); ?>"
                            class="mt-3 block text-center text-sm font-medium text-gray-600 transition hover:text-gray-900"
                        >
                            متابعة التسوق
                        </a>

                    </div>

                </aside>

            </div>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>
</section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('storefront.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/cart/index.blade.php ENDPATH**/ ?>