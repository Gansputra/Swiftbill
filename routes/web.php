<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('inventory/categories', 'inventory.categories')->name('categories.index');
    Route::view('inventory/suppliers', 'inventory.suppliers')->name('suppliers.index');
    Route::view('inventory/products', 'inventory.products')->name('products.index');
    Route::view('pos', 'pos.index')->name('pos.index');
});

Route::middleware(['auth'])->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
