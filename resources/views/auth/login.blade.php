
@extends('storefront.layouts.app')

@section('title', 'تسجيل الدخول - Personal Care Store')

@section('content')

<section class="py-16">
    <div class="mx-auto max-w-md px-4">

        <div class="rounded-2xl bg-white p-8 shadow-sm">

            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold">
                    تسجيل الدخول
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    ادخل إلى حسابك للمتابعة
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium"
                    >
                        البريد الإلكتروني
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-black"
                    >
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium"
                    >
                        كلمة المرور
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-black"
                    >
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        class="rounded"
                    >

                    <span>
                        تذكرني
                    </span>
                </label>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-black px-6 py-3 font-semibold text-white transition hover:bg-gray-800"
                >
                    تسجيل الدخول
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                ليس لديك حساب؟

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-black hover:underline"
                >
                    إنشاء حساب
                </a>
            </div>

        </div>

    </div>
</section>

@endsection
