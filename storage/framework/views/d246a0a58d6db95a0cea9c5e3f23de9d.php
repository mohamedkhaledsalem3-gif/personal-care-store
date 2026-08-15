
<article class="overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-md">

    <a
        href="<?php echo e(route('storefront.products.show', $product->slug)); ?>"
        class="block"
    >

        <div class="relative aspect-square bg-gray-100">

            <?php
                $image = $product->images->first();
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>

                <img
                    src="<?php echo e(asset('storage/' . $image->image_path)); ?>"
                    alt="<?php echo e($image->alt_text ?: $product->name); ?>"
                    class="h-full w-full object-cover"
                >

            <?php else: ?>

                <div class="flex h-full items-center justify-center text-gray-400">
                    لا توجد صورة
                </div>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->sale_price): ?>

                <span class="absolute right-3 top-3 rounded-full bg-red-500 px-3 py-1 text-xs font-semibold text-white">
                    عرض
                </span>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->is_new): ?>

                <span class="absolute left-3 top-3 rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">
                    جديد
                </span>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>


        <div class="p-5">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->brand): ?>

                <p class="text-xs text-gray-500">
                    <?php echo e($product->brand->name); ?>

                </p>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


            <h3 class="mt-2 font-semibold">
                <?php echo e($product->name); ?>

            </h3>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->category): ?>

                <p class="mt-1 text-sm text-gray-500">
                    <?php echo e($product->category->name); ?>

                </p>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


            <div class="mt-4 flex items-center gap-2">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->sale_price): ?>

                    <span class="text-lg font-bold">
                        <?php echo e(number_format($product->sale_price, 2)); ?>

                        ج.م
                    </span>

                    <span class="text-sm text-gray-400 line-through">
                        <?php echo e(number_format($product->price, 2)); ?>

                        ج.م
                    </span>

                <?php else: ?>

                    <span class="text-lg font-bold">
                        <?php echo e(number_format($product->price, 2)); ?>

                        ج.م
                    </span>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->isInStock()): ?>

                <p class="mt-3 text-sm text-green-600">
                    متوفر في المخزون
                </p>

            <?php else: ?>

                <p class="mt-3 text-sm text-red-600">
                    غير متوفر
                </p>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>

    </a>

</article><?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/partials/product-card.blade.php ENDPATH**/ ?>