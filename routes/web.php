<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\Auth;

// user
Route::get('/', [Controllers\HomeController::class, 'index'])->name('home');
Route::get('/kategori/{slug}', [Controllers\CategoryNewsController::class, 'show'])->name('kategori.show');

// filament
Route::middleware(['admin'])->group(function () {
    Route::get('/dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');
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
