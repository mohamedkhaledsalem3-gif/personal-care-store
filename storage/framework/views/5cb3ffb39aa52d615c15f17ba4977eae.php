


<?php $__env->startSection('title', $product->name . ' - Personal Care Store'); ?>

<?php $__env->startSection('content'); ?>

<section class="py-12">
    <div class="mx-auto max-w-7xl px-4">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="mb-6 rounded-xl bg-green-50 p-4 text-green-700">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-700">
                <ul class="space-y-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


        <div class="grid gap-10 lg:grid-cols-2">

            
            <div>

                <?php
                    $primaryImage = $product->images->first();
                ?>

                <div class="overflow-hidden rounded-3xl bg-gray-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($primaryImage): ?>
                        <img
                            src="<?php echo e(asset('storage/' . $primaryImage->image_path)); ?>"
                            alt="<?php echo e($primaryImage->alt_text ?: $product->name); ?>"
                            class="aspect-square h-full w-full object-cover"
                        >
                    <?php else: ?>
                        <div class="flex aspect-square items-center justify-center text-gray-400">
                            لا توجد صورة للمنتج
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

            </div>


            
            <div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->brand): ?>
                    <p class="text-sm font-medium text-gray-500">
                        <?php echo e($product->brand->name); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <h1 class="mt-2 text-3xl font-bold md:text-4xl">
                    <?php echo e($product->name); ?>

                </h1>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->short_description): ?>
                    <p class="mt-4 text-gray-600">
                        <?php echo e($product->short_description); ?>

                    </p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->description): ?>
                    <div class="prose mt-6 max-w-none">
                        <?php echo $product->description; ?>

                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->variants->isNotEmpty()): ?>

                    <form
                        method="POST"
                        action="<?php echo e(route('storefront.cart.items.store')); ?>"
                        class="mt-8"
                    >
                        <?php echo csrf_field(); ?>

                        <div>
                            <h2 class="mb-4 text-lg font-semibold">
                                اختر الحجم / النوع
                            </h2>

                            <div class="space-y-3">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $product->variants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <?php
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
                                    ?>

                                    <label
                                        class="flex cursor-pointer items-center justify-between rounded-xl border p-4 transition
                                            <?php echo e($isAvailable ? 'hover:border-black' : 'cursor-not-allowed opacity-50'); ?>"
                                    >

                                        <div class="flex items-center gap-3">

                                            <input
                                                type="radio"
                                                name="variant_id"
                                                value="<?php echo e($variant->id); ?>"
                                                <?php echo e($variant->is_default && $isAvailable ? 'checked' : ''); ?>

                                                <?php echo e(!$isAvailable ? 'disabled' : ''); ?>

                                                class="h-4 w-4"
                                            >

                                            <div>

                                                <p class="font-semibold">
                                                    <?php echo e($variant->name); ?>

                                                </p>

                                                <p class="mt-1 text-sm text-gray-500">
                                                    SKU: <?php echo e($variant->sku); ?>

                                                </p>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isAvailable): ?>
                                                    <p class="mt-1 text-xs text-green-600">
                                                        متوفر: <?php echo e($available); ?>

                                                    </p>
                                                <?php else: ?>
                                                    <p class="mt-1 text-xs text-red-600">
                                                        غير متوفر
                                                    </p>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                            </div>

                                        </div>


                                        <div class="text-left">

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant->sale_price): ?>
                                                <p class="font-bold">
                                                    <?php echo e(number_format($variant->sale_price, 2)); ?>

                                                    ج.م
                                                </p>

                                                <p class="text-sm text-gray-400 line-through">
                                                    <?php echo e(number_format($variant->price, 2)); ?>

                                                    ج.م
                                                </p>
                                            <?php else: ?>
                                                <p class="font-bold">
                                                    <?php echo e(number_format($variant->price, 2)); ?>

                                                    ج.م
                                                </p>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        </div>

                                    </label>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>
                        </div>


                        
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
                                value="<?php echo e(old('quantity', 1)); ?>"
                                min="1"
                                class="w-32 rounded-xl border border-gray-300 px-4 py-3"
                            >

                        </div>


                        
                        <div class="mt-6">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>

                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-black px-6 py-4 font-semibold text-white transition hover:bg-gray-800"
                                >
                                    إضافة إلى السلة
                                </button>

                            <?php else: ?>

                                <a
                                    href="<?php echo e(route('login')); ?>"
                                    class="block w-full rounded-xl bg-black px-6 py-4 text-center font-semibold text-white transition hover:bg-gray-800"
                                >
                                    سجل الدخول لإضافة المنتج إلى السلة
                                </a>

                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>

                    </form>

                <?php else: ?>

                    <div class="mt-8 rounded-xl bg-yellow-50 p-4 text-yellow-700">
                        لا توجد خيارات متاحة لهذا المنتج حاليًا.
                    </div>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>

        </div>

    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('storefront.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/products/show.blade.php ENDPATH**/ ?>