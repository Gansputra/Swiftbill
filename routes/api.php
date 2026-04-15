<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// For basic Postman testing, we expose the main tables directly here.
// In a real production app, these should be secured with auth:sanctum middleware.

Route::get('/users', function () {
    return response()->json(\App\Models\User::all());
});

Route::get('/categories', function () {
    return response()->json(\App\Models\Category::all());
});

Route::get('/suppliers', function () {
    return response()->json(\App\Models\Supplier::all());
});

Route::get('/products', function () {
    // Include category and supplier relationships for better context
    return response()->json(\App\Models\Product::with(['category', 'supplier'])->get());
});

Route::get('/transactions', function () {
    return response()->json(\App\Models\Transaction::with(['items.product', 'user'])->get());
});

Route::get('/shifts', function () {
    return response()->json(\App\Models\CashShift::with('user')->get());
});

Route::get('/stock-movements', function () {
    return response()->json(\App\Models\StockMovement::with('product')->get());
});

Route::get('/ai-chats', function () {
    return response()->json(\App\Models\AiChatMessage::all());
});
