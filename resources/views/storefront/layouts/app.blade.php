<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Personal Care Store')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('styles')

</head>

<body class="bg-gray-50 text-gray-900">

    {{-- Header --}}
    @include('storefront.partials.header')

    {{-- Main --}}
    <main>

        @yield('content')

    </main>

    {{-- Footer --}}
    @include('storefront.partials.footer')

    @stack('scripts')

</body>

</html>