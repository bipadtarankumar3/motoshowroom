<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Showroom\BikeController;
use App\Http\Controllers\Showroom\BillingController;
use App\Http\Controllers\Showroom\EnquiryController;
use App\Http\Controllers\Showroom\PublicBikeController;
use App\Http\Controllers\Showroom\PublicEnquiryController;
use App\Http\Controllers\Showroom\PublicServiceController;
use App\Http\Controllers\Showroom\ServiceBookingController;
use App\Http\Controllers\Showroom\SettingController;
use App\Http\Controllers\Showroom\WebsiteBuilderController;
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
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Showroom Tenants Management
        Route::get('/showrooms', [ShowroomController::class, 'index'])->name('showrooms.index');
        Route::get('/showrooms/create', [ShowroomController::class, 'create'])->name('showrooms.create');
        Route::post('/showrooms', [ShowroomController::class, 'store'])->name('showrooms.store');
        Route::get('/showrooms/{showroom}', [ShowroomController::class, 'show'])->name('showrooms.show');
        Route::get('/showrooms/{showroom}/edit', [ShowroomController::class, 'edit'])->name('showrooms.edit');
        Route::put('/showrooms/{showroom}', [ShowroomController::class, 'update'])->name('showrooms.update');
        Route::post('/showrooms/{showroom}/status', [ShowroomController::class, 'toggleStatus'])->name('showrooms.status');
        Route::post('/showrooms/{showroom}/members', [ShowroomController::class, 'addMember'])->name('showrooms.members.store');
        Route::delete('/showrooms/{showroom}/members/{membership}', [ShowroomController::class, 'removeMember'])->name('showrooms.members.destroy');

        // Subscription Plans Management
        Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [SubscriptionPlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('plans.update');
        Route::post('/plans/{plan}/toggle', [SubscriptionPlanController::class, 'toggle'])->name('plans.toggle');

        // Subscriptions Platform Oversight
        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
        Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        // Audit Trail Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // Platform Settings
        Route::get('/settings', [PlatformSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [PlatformSettingController::class, 'update'])->name('settings.update');

        // Platform Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
});

// Public Showroom Storefront Route (Tenant-resolved via EnsureTenantContext)
Route::middleware('tenant')->prefix('showrooms/{showroom}')->group(function () {
    Route::get('/', function (Showroom $showroom) {
        $showroom->load(['featuredBikes.images']);

        return view('showrooms.public', compact('showroom'));
    })->name('showrooms.show');

    // Public Motorcycle Catalog
    Route::get('/bikes', [PublicBikeController::class, 'index'])->name('showrooms.bikes.index');
    Route::get('/bikes/{bike}', [PublicBikeController::class, 'show'])->name('showrooms.bikes.show');

    // Customer Enquiries & Test-Rides
    Route::get('/enquiries/create', [PublicEnquiryController::class, 'create'])->name('showrooms.enquiries.create');
    Route::post('/enquiries', [PublicEnquiryController::class, 'store'])->name('showrooms.enquiries.store');

    // Workshop & Service Appointment Booking
    Route::get('/services/book', [PublicServiceController::class, 'create'])->name('showrooms.services.create');
    Route::get('/services/slots', [PublicServiceController::class, 'slots'])->name('showrooms.services.slots');
    Route::post('/services/book', [PublicServiceController::class, 'store'])->name('showrooms.services.store');
    Route::get('/services/track/{booking?}', [PublicServiceController::class, 'track'])->name('showrooms.services.track');

    // Showroom Billing & Subscription Hub
    Route::middleware(['auth', 'tenant.member'])->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('showrooms.billing.index');
        Route::post('/billing/select-plan', [BillingController::class, 'selectPlan'])->name('showrooms.billing.select-plan');
    });

    // Authenticated Showroom Back-Office (Tenant isolation + Active Subscription check)
    Route::middleware(['auth', 'tenant.member', 'subscription.active'])->group(function () {
        Route::get('/manage', [App\Http\Controllers\Showroom\DashboardController::class, 'index'])->name('showrooms.manage');

        // Customer Purchase Enquiries & Test Rides
        Route::get('/manage/enquiries', [EnquiryController::class, 'index'])->name('showrooms.manage.enquiries.index');
        Route::get('/manage/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('showrooms.manage.enquiries.show');
        Route::put('/manage/enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('showrooms.manage.enquiries.update');
        Route::delete('/manage/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('showrooms.manage.enquiries.destroy');

        // Service Booking & Workshop Bay Management
        Route::get('/manage/services', [ServiceBookingController::class, 'index'])->name('showrooms.manage.services.index');
        Route::get('/manage/services/{booking}', [ServiceBookingController::class, 'show'])->name('showrooms.manage.services.show');
        Route::put('/manage/services/{booking}', [ServiceBookingController::class, 'update'])->name('showrooms.manage.services.update');
        Route::delete('/manage/services/{booking}', [ServiceBookingController::class, 'destroy'])->name('showrooms.manage.services.destroy');

        // Motorcycle Inventory Management
        Route::get('/manage/bikes', [BikeController::class, 'index'])->name('showrooms.manage.bikes.index');
        Route::get('/manage/bikes/create', [BikeController::class, 'create'])->name('showrooms.manage.bikes.create');
        Route::post('/manage/bikes', [BikeController::class, 'store'])->name('showrooms.manage.bikes.store');
        Route::get('/manage/bikes/{bike}/edit', [BikeController::class, 'edit'])->name('showrooms.manage.bikes.edit');
        Route::put('/manage/bikes/{bike}', [BikeController::class, 'update'])->name('showrooms.manage.bikes.update');
        Route::delete('/manage/bikes/{bike}', [BikeController::class, 'destroy'])->name('showrooms.manage.bikes.destroy');
        Route::post('/manage/bikes/{bike}/toggle-featured', [BikeController::class, 'toggleFeatured'])->name('showrooms.manage.bikes.toggle-featured');
        Route::post('/manage/bikes/{bike}/toggle-published', [BikeController::class, 'togglePublished'])->name('showrooms.manage.bikes.toggle-published');

        // Website Builder & Storefront Customizer
        Route::get('/manage/website', [WebsiteBuilderController::class, 'edit'])->name('showrooms.manage.website');
        Route::put('/manage/website', [WebsiteBuilderController::class, 'update'])->name('showrooms.manage.website.update');

        // Showroom Settings & Profile
        Route::get('/manage/settings', [SettingController::class, 'edit'])->name('showrooms.manage.settings');
        Route::put('/manage/settings', [SettingController::class, 'update'])->name('showrooms.manage.settings.update');
    });
});
