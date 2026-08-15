@extends('storefront.layouts.app')

@section('title', 'إنشاء حساب - Personal Care Store')

@section('content')

<section class="py-16">
    <div class="mx-auto max-w-md px-4">

        <div class="rounded-2xl bg-white p-8 shadow-sm">

            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold">
                    إنشاء حساب
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    أنشئ حسابك وابدأ التسوق
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
                action="{{ route('register.store') }}"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="name"
                        class="mb-2 block text-sm font-medium"
                    >
                        الاسم
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-black"
                    >
                </div>

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
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-black"
                    >
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="mb-2 block text-sm font-medium"
                    >
                        تأكيد كلمة المرور
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 outline-none focus:border-black"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full rounded-xl bg-black px-6 py-3 font-semibold text-white transition hover:bg-gray-800"
                >
                    إنشاء الحساب
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                لديك حساب بالفعل؟

                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-black hover:underline"
                >
                    تسجيل الدخول
                </a>
            </div>

        </div>

    </div>
</section>

@endsection