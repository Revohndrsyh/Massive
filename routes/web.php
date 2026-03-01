<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kuisioner', function () {
    return view('kuisioner');
})->name('kuisioner.index');

// authentication pages
Route::get('login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);

// password reset
Route::get('forgot-password', [\App\Http\Controllers\AuthController::class, 'showForgot'])->name('password.request');
Route::post('forgot-password', [\App\Http\Controllers\AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('reset-password/{token}', [\App\Http\Controllers\AuthController::class, 'showReset'])->name('password.reset');
Route::post('reset-password', [\App\Http\Controllers\AuthController::class, 'reset'])->name('password.update');
// profile (requires auth)
Route::middleware('auth')->group(function () {
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::post('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
