<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ModulController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\RekomendasiController;
use App\Models\SusResponse;

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

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/sus/export/{format}', [AdminDashboardController::class, 'exportSus'])->name('sus.export');
        Route::get('/kuesioner/export/{format}', [AdminDashboardController::class, 'exportKuesioner'])->name('kuesioner.export');
    });

    // SUS Export
    Route::get('/sus/export', function () {
        $responses = SusResponse::with('user')->latest()->get();

        if ($responses->isEmpty()) {
            return back()->with('error', 'Belum ada data evaluasi SUS.');
        }

        $filename = 'SUS_Evaluasi_MASSIVE_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($responses) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, [
                'user_id', 'nama', 'email',
                'sus_1', 'sus_2', 'sus_3', 'sus_4', 'sus_5',
                'sus_6', 'sus_7', 'sus_8', 'sus_9', 'sus_10',
                'skor_sus', 'grade', 'keterangan', 'tanggal'
            ], ';');

            foreach ($responses as $response) {
                fputcsv($file, [
                    $response->user_id,
                    $response->user?->name ?? 'User #' . $response->user_id,
                    $response->user?->email ?? '-',
                    $response->sus_1,
                    $response->sus_2,
                    $response->sus_3,
                    $response->sus_4,
                    $response->sus_5,
                    $response->sus_6,
                    $response->sus_7,
                    $response->sus_8,
                    $response->sus_9,
                    $response->sus_10,
                    number_format((float) $response->skor_sus, 1, '.', ''),
                    $response->grade,
                    $response->grade_label,
                    $response->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
                ], ';');
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    })->name('sus.export');
});
