<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo $__env->yieldContent('title', 'Personal Care Store'); ?>
    </title>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/app.css',
        'resources/js/app.js'
    ]); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>

</head>

<body class="bg-gray-50 text-gray-900">

    
    <?php echo $__env->make('storefront.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main>

        <?php echo $__env->yieldContent('content'); ?>

    </main>

    
    <?php echo $__env->make('storefront.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>

</html><?php /**PATH C:\laragon\www\personal-care-store\resources\views/storefront/layouts/app.blade.php ENDPATH**/ ?>