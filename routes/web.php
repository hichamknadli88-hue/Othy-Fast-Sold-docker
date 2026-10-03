<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\AnnonceController;
use App\Http\Controllers\RechargeController;
use App\Models\Annonce;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'throttle:20,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});
Route::get('/login/check', [AuthController::class, 'checkAdmin'])
    ->middleware(['guest', 'throttle:60,1'])
    ->name('auth.check-admin');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
});


Route::get('/portal', [RechargeController::class, 'index'])
    ->middleware('throttle:30,1')->name('recharge.form');

Route::post('/portal', [RechargeController::class, 'store'])
    ->middleware('throttle:recharge')->name('recharge.store');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AnnonceController::class, 'index'])->name('admin.dashboard');

    Route::post('/admin/annonces', [AnnonceController::class, 'store'])->name('admin.annonces.store');
    Route::post('/admin/annonces/{annonce}/activate', [AnnonceController::class, 'activate'])->name('admin.annonces.activate');
    Route::post('/admin/annonces/{annonce}/pause', [AnnonceController::class, 'pause'])->name('admin.annonces.pause');
    Route::post('/admin/annonces/{annonce}/resume', [AnnonceController::class, 'resume'])->name('admin.annonces.resume');
    Route::delete('/admin/annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('admin.annonces.destroy');
});

Route::get('/', function () {
    return view('home', ['annonce' => Annonce::current()]);
})->name('home');