<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\Auth;

// user
Route::get('/', [Controllers\HomeController::class, 'index'])->name('home');

Route::get('/news', [Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/kategori/{slug}', [Controllers\CategoryNewsController::class, 'show'])->name('kategori.show');
Route::get('/news/{news}', [Controllers\NewsController::class, 'show'])->name('news.show');

Route::get('/struktur-organisasi-kecamatan-binong', [Controllers\StrukturOrganisasiController::class, 'index'])->name('struktur-organisasi');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/struktur-organisasi/upload', [Controllers\StrukturOrganisasiController::class, 'store'])->name('struktur-organisasi.store');
    Route::delete('/struktur-organisasi/delete', [Controllers\StrukturOrganisasiController::class, 'destroy'])->name('struktur-organisasi.destroy');
});

// Login dengan Google
Route::get('/auth/google', [Auth\GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [Auth\GoogleController::class, 'callback'])->name('google.callback');

// Middleware auth
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
