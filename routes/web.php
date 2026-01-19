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

    // Master Data
    Route::resource('kriteria', KriteriaController::class);
    Route::resource('subkriteria', SubkriteriaController::class);
    Route::resource('alternatif', AlternatifController::class);
    Route::resource('pertanyaan', PertanyaanController::class);
    Route::resource('user', UserController::class);

    // SPK Logic
    Route::resource('penilaian', PenilaianController::class)->only(['index', 'create', 'store', 'edit']);
    Route::get('perhitungan', [PerhitunganController::class, 'index'])->name('perhitungan.index');
    Route::get('perangkingan', [PerangkinganController::class, 'index'])->name('perangkingan.index');
});
