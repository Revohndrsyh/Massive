<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\RekomendasiController;

// Landing Page — redirect to dashboard if logged in
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Auth Routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kuesioner
    Route::get('/kuesioner/{step?}', [KuesionerController::class, 'show'])->name('kuesioner');
    Route::post('/kuesioner/{step}', [KuesionerController::class, 'storeStep'])->name('kuesioner.store')->middleware('throttle:kuesioner');

    // Analisis
    Route::get('/analisis', [AnalisisController::class, 'index'])->name('analisis');

    // Rekomendasi
    Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
    Route::get('/solusi/{id}', [RekomendasiController::class, 'show'])->name('solusi');
    Route::get('/solusi-node', [RekomendasiController::class, 'showByIsu'])->name('solusi.node');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Modul (slug-based, no database)
    Route::get('/modul', [ModulController::class, 'index'])->name('modul');
    Route::get('/modul/{slug}', [ModulController::class, 'show'])->name('modul.show');

    // SUS Export — serve CSV file directly
    Route::get('/sus/export', function () {
        $csvPath = storage_path('app/sus_responses.csv');

        if (!file_exists($csvPath)) {
            return back()->with('error', 'Belum ada data evaluasi SUS.');
        }

        $filename = 'SUS_Evaluasi_MASSIVE_' . now()->format('Ymd_His') . '.csv';

        return response()->download($csvPath, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    })->name('sus.export');
});
