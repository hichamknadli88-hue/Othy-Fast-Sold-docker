<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RechargeController;

Route::get('/', function () {
    return view('home');
})->name('home');


Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/portal', [RechargeController::class, 'index'])->name('recharge.form');
    Route::post('/portal', [RechargeController::class, 'store'])->name('recharge.store');
});