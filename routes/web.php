<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Models\Showroom;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MotoShowroom SaaS
|--------------------------------------------------------------------------
*/

// Public SaaS Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    // Standard Registration & Login
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.update');
});

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Profile shortcuts
    Route::get('/dashboard', [UserController::class, 'profile'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // User details page & edit
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');

    // Admin Exclusive Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
});

// Public Showroom Storefront Route (Tenant-resolved via EnsureTenantContext)
Route::middleware('tenant')->prefix('showrooms/{showroom}')->group(function () {
    Route::get('/', function (Showroom $showroom) {
        return view('showrooms.public', compact('showroom'));
    })->name('showrooms.show');

    // Authenticated Showroom Back-Office (Tenant isolation enforced via VerifyShowroomMembership)
    Route::middleware(['auth', 'tenant.member'])->get('/manage', function (Showroom $showroom) {
        return view('showrooms.manage', compact('showroom'));
    })->name('showrooms.manage');
});
