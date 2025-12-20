<?php

use App\Http\Controllers\Merchant\DashboardController;
use App\Http\Controllers\Merchant\OrderController;
use App\Http\Controllers\Merchant\TrackingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('merchant.dashboard');
});

// Merchant routes (protected by auth middleware)
Route::middleware(['auth', 'verified'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
    Route::get('/tracking', [TrackingController::class, 'search'])->name('tracking.search');
    Route::post('/tracking/lookup', [TrackingController::class, 'lookup'])->name('tracking.lookup');
});

// Payment routes
Route::middleware(['auth', 'verified'])->prefix('payment')->name('payment.')->group(function () {
    Route::post('/initiate/{order}', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('/cancel/{order}', [PaymentController::class, 'cancel'])->name('cancel');
});

// Payment callback (no auth required - comes from Plutu)
Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Profile routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
