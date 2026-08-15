

<?php $__env->startSection('title', 'المنتجات - Personal Care Store'); ?>

<?php $__env->startSection('content'); ?>

    <section class="py-10">
        <div class="mx-auto max-w-7xl px-4">

            
            <div class="mb-8">
                <h1 class="text-3xl font-bold">
                    جميع المنتجات
                </h1>

                <p class="mt-2 text-gray-500">
                    اكتشف مجموعة منتجات العناية الشخصية المتاحة لدينا.
                </p>
            </div>


            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

                
                <aside class="lg:col-span-1">

                    <div class="rounded-2xl bg-white p-5 shadow-sm">

                        <div class="mb-6 flex items-center justify-between">
                            <h2 class="text-lg font-bold">
                                تصفية المنتجات
                            </h2>

                            <a
                                href="<?php echo e(route('storefront.products.index')); ?>"
                                class="text-sm text-gray-500 hover:text-black"
                            >
                                مسح الكل
                            </a>
                        </div>


                        
                        <form
                            method="GET"
                            action="<?php echo e(route('storefront.products.index')); ?>"
                            class="mb-8"
                        >

                            <label
                                for="search"
                                class="mb-2 block text-sm font-semibold"
                            >
                                البحث
                            </label>

                            <div class="flex gap-2">

                                <input
                                    id="search"
                                    type="search"
                                    name="search"
                                    value="<?php echo e(request('search')); ?>"
                                    placeholder="ابحث عن منتج..."
                                    class="w-full rounded-xl border-gray-300 text-sm focus:border-black focus:ring-black"
                                >

                                <button
                                    type="submit"
                                    class="rounded-xl bg-black px-4 py-2 text-sm font-semibold text-white"
                                >
                                    بحث
                                </button>

                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('category')): ?>
                                <input
                                    type="hidden"
                                    name="category"
                                    value="<?php echo e(request('category')); ?>"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('brand')): ?>
                                <input
                                    type="hidden"
                                    name="brand"
                                    value="<?php echo e(request('brand')); ?>"
                                >
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </form>


                        
                        <div class="mb-8">

                            <h3 class="mb-4 font-semibold">
                                الأقسام
                            </h3>

                            <div class="space-y-3">

                                <a
                                    href="<?php echo e(route('storefront.products.index', request()->except('category', 'page'))); ?>"
                                    class="flex items-center justify-between text-sm
                                    <?php echo e(!request('category') ? 'font-bold text-black' : 'text-gray-600 hover:text-black'); ?>"
                                >
                                    <span>
                                        جميع الأقسام
                                    </span>
                                </a>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <a
                                        href="<?php echo e(route('storefront.products.index', array_merge(request()->except('page'), ['category' => $category->slug]))); ?>"
                                        class="flex items-center justify-between text-sm
                                        <?php echo e(request('category') === $category->slug ? 'font-bold text-black' : 'text-gray-600 hover:text-black'); ?>"
                                    >
                                        <span>
                                            <?php echo e($category->name); ?>

                                        </span>

                                        <span class="text-xs text-gray-400">
                                            <?php echo e($category->products_count); ?>

                                        </span>
                                    </a>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>

                        </div>


                        
                        <div>

                            <h3 class="mb-4 font-semibold">
                                العلامات التجارية
                            </h3>

                            <div class="space-y-3">

                                <a
                                    href="<?php echo e(route('storefront.products.index', request()->except('brand', 'page'))); ?>"
                                    class="flex items-center justify-between text-sm
                                    <?php echo e(!request('brand') ? 'font-bold text-black' : 'text-gray-600 hover:text-black'); ?>"
                                >
                                    <span>
                                        جميع العلامات
                                    </span>
                                </a>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brand): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                    <a
                                        href="<?php echo e(route('storefront.products.index', array_merge(request()->except('page'), ['brand' => $brand->slug]))); ?>"
                                        class="flex items-center justify-between text-sm
                                        <?php echo e(request('brand') === $brand->slug ? 'font-bold text-black' : 'text-gray-600 hover:text-black'); ?>"
                                    >
                                        <span>
                                            <?php echo e($brand->name); ?>

                                        </span>

                                        <span class="text-xs text-gray-400">
                                            <?php echo e($brand->products_count); ?>

                                        </span>
                                    </a>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>

                        </div>

                    </div>

                </aside>


                
                <div class="lg:col-span-3">

                    
                    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <p class="text-sm text-gray-500">
                            عرض
                            <span class="font-semibold text-gray-900">
                                <?php echo e($products->total()); ?>

                            </span>
                            منتج
                        </p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search') || request('category') || request('brand')): ?>

                            <div class="flex flex-wrap gap-2">

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('search')): ?>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        البحث: <?php echo e(request('search')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('category')): ?>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        القسم: <?php echo e(request('category')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request('brand')): ?>
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs">
                                        العلامة: <?php echo e(request('brand')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            </div>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    </div>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($products->isNotEmpty()): ?>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <?php echo $__env->make('storefront.partials.product-card', [
                                    'product' => $product,
                                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        </div>


                        
                        <div class="mt-10">
                            <?php echo e($products->links()); ?>

                        </div>

                    <?php else: ?>

                        <div class="rounded-2xl bg-white p-12 text-center shadow-sm">

                            <div class="text-5xl">
                                🔍
                            </div>

                            <h2 class="mt-4 text-xl font-bold">
                                لم يتم العثور على منتجات
                            </h2>

                            <p class="mt-2 text-gray-500">
                                حاول تغيير البحث أو الفلاتر المستخدمة.
                            </p>

                            <a
                                href="<?php echo e(route('storefront.products.index')); ?>"
                                class="mt-6 inline-flex rounded-xl bg-black px-6 py-3 font-semibold text-white"
                            >
                                عرض جميع المنتجات
                            </a>

                        </div>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                </div>

            </div>

        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('storefront.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/products/index.blade.php ENDPATH**/ ?>