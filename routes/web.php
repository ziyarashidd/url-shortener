<?php

// routes/web.php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Short URL redirect (public but requires auth)
Route::get('/s/{shortCode}', [ShortUrlController::class, 'redirect'])
    ->name('short-url.redirect');

// Invitations
Route::get('/invitations/{token}', [InvitationController::class, 'accept'])
    ->name('invitation.accept');
Route::post('/invitations/{token}', [InvitationController::class, 'acceptStore'])
    ->name('invitation.accept.store');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/urls', [DashboardController::class, 'urls'])->name('dashboard.urls');

    // Short URLs
    Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
    Route::get('/short-urls/create', [ShortUrlController::class, 'create'])->name('short-urls.create');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
    Route::get('/short-urls/{shortUrl}', [ShortUrlController::class, 'show'])->name('short-urls.show');
    Route::delete('/short-urls/{shortUrl}', [ShortUrlController::class, 'destroy'])->name('short-urls.destroy');

    // Invitations
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});