<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;



Route::get('/', function () {

    return Inertia::render('Auth/Register');
});



Route::middleware('auth')->group(function () {



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('products', [ProductController::class, 'index'])->name("products.show");
    Route::post('products/dataFetch', [ProductController::class, 'dataFetch']);
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::post('/product/{product}/rate', [ProductController::class, 'rateProduct'])->name('product.rating');


    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/add/{itemId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/purchase/{id}', [CartController::class, 'purchase'])->name('cart.purchase');





});



Route::get('/api/exchange-rates', function () {
    $apiKey = config('services.api.exchange');

        $response = Http::get('https://api.freecurrencyapi.com/v1/latest', [
            'apikey' => $apiKey,
            'currencies' => 'EUR,USD,CAD,PLN,GBP,CZK',
            'base_currency' => 'CZK',
        ]);


    return $response->json();
});

require __DIR__.'/auth.php';
