<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\SubkriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\PerangkinganController;
use App\Http\Controllers\ProfileController;

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // SPK Logic (Accessible by Siswa & Guru BK)
    Route::get('penilaian/create/{id_alternatif}', [PenilaianController::class, 'create'])->name('penilaian.create');
    Route::delete('penilaian/destroyPerAlternatif', [PenilaianController::class, 'destroyPerAlternatif'])->name('penilaian.destroyPerAlternatif');
    Route::resource('penilaian', PenilaianController::class)->only(['index', 'store', 'edit']);
    Route::get('perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
    Route::get('perangkingan', [PerangkinganController::class, 'index'])->name('perangkingan.index');
    Route::post('perangkingan/catatan', [PerangkinganController::class, 'storeCatatan'])->name('perangkingan.catatan.store');

    // Master Data (ADMIN ONLY - Guru BK)
    // Master Data (ADMIN ONLY - Guru BK)
    Route::middleware(['can:access-master-data'])->group(function () {
        Route::resource('kriteria', KriteriaController::class);
        Route::resource('subkriteria', SubkriteriaController::class);
        Route::resource('alternatif', AlternatifController::class);
        Route::resource('pertanyaan', PertanyaanController::class);
        Route::resource('user', UserController::class);
    });
});
