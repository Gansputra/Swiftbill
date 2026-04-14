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
    Route::view('inventory/stock-movements', 'inventory.stock-movements')->name('stock-movements.index');
    Route::view('pos', 'pos.index')->name('pos.index');
    Route::get('pos/receipt/{invoice}', [\App\Http\Controllers\Pos\ReceiptController::class, 'show'])->name('pos.receipt');
    Route::get('ai-dashboard', \App\Livewire\AiDashboard::class)->name('ai-dashboard');
    Route::get('reports/sales', \App\Livewire\Reports\SalesReport::class)->name('reports.sales');
    Route::get('reports/export/excel', [\App\Http\Controllers\Reports\ReportExportController::class, 'excel'])->name('reports.export.excel');
    Route::get('reports/export/pdf', [\App\Http\Controllers\Reports\ReportExportController::class, 'pdf'])->name('reports.export.pdf');
    Route::get('/users', \App\Livewire\Users\UserManager::class)->name('users.index');
});

Route::middleware(['auth'])->group(function () {
    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
