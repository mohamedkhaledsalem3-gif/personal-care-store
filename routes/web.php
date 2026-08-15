<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])
    ->name('storefront.home');


/*
|--------------------------------------------------------------------------
| Products
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index'])
    ->name('storefront.products.index');

Route::get('/products/{product}', [ProductController::class, 'show'])
    ->name('storefront.products.show');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.store');
});


Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Cart
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/cart', [CartController::class, 'index'])
        ->name('storefront.cart.index');

    Route::post('/cart/items', [CartController::class, 'store'])
        ->name('storefront.cart.items.store');

    Route::patch('/cart/items/{item}', [CartController::class, 'update'])
        ->name('storefront.cart.items.update');

    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])
        ->name('storefront.cart.items.destroy');

    Route::delete('/cart', [CartController::class, 'clear'])
        ->name('storefront.cart.clear');
});


/*
|--------------------------------------------------------------------------
| Checkout
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('storefront.checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('storefront.checkout.store');
});


/*
|--------------------------------------------------------------------------
| Customer Orders
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/orders', [OrderController::class, 'index'])
        ->name('storefront.orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->can('view', 'order')
        ->name('storefront.orders.show');

    Route::delete('/orders/{order}/cancel', [OrderController::class, 'cancel'])
        ->can('cancel', 'order')
        ->name('storefront.orders.cancel');
});