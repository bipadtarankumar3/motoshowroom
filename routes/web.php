<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlatformAnalyticsController;
use App\Http\Controllers\Admin\PlatformSettingController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Showroom\AiAssistantManagementController;
use App\Http\Controllers\Showroom\ApplicantManagementController;
use App\Http\Controllers\Showroom\BannerManagementController;
use App\Http\Controllers\Showroom\Bike360ViewerController;
use App\Http\Controllers\Showroom\BikeController;
use App\Http\Controllers\Showroom\BillingController;
use App\Http\Controllers\Showroom\CampaignManagementController;
use App\Http\Controllers\Showroom\CareerManagementController;
use App\Http\Controllers\Showroom\CategoryManagementController;
use App\Http\Controllers\Showroom\EmailPreviewController;
use App\Http\Controllers\Showroom\EnquiryController;
use App\Http\Controllers\Showroom\OnboardingController;
use App\Http\Controllers\Showroom\PaymentCheckoutController;
use App\Http\Controllers\Showroom\PaymentHistoryController;
use App\Http\Controllers\Showroom\PublicAiAssistantController;
use App\Http\Controllers\Showroom\PublicBike360Controller;
use App\Http\Controllers\Showroom\PublicBikeController;
use App\Http\Controllers\Showroom\PublicCareerController;
use App\Http\Controllers\Showroom\PublicEnquiryController;
use App\Http\Controllers\Showroom\PublicScratchCardController;
use App\Http\Controllers\Showroom\PublicServiceController;
use App\Http\Controllers\Showroom\ServiceBookingController;
use App\Http\Controllers\Showroom\SettingController;
use App\Http\Controllers\Showroom\ShowroomAnalyticsController;
use App\Http\Controllers\Showroom\ShowroomServicePackageController;
use App\Http\Controllers\Showroom\SupportController;
use App\Http\Controllers\Showroom\WebsiteBuilderController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use App\Models\Showroom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MotoShowroom SaaS
|--------------------------------------------------------------------------
*/

// Root: guests see the sign-in page, authenticated users go to their console
Route::get('/', function () {
    $user = Auth::user();

    if (! $user) {
        return view('auth.login');
    }

    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    $showroom = $user->showrooms()->first();
    if ($showroom) {
        return redirect()->route('showrooms.manage', $showroom);
    }

    return redirect()->route('users.show', $user);
})->name('home');

// Public SaaS Marketing Landing Page
Route::get('/landing', function () {
    return view('welcome');
})->name('landing');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    // Standard Registration & Login
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Two-Factor OTP Verification
    Route::get('/login/otp', [AuthController::class, 'showOtp'])->name('login.otp');
    Route::post('/login/otp', [AuthController::class, 'verifyOtp'])->name('login.otp.verify');
    Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])->name('login.otp.resend');

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
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // Super Admin Impersonation Exit
    Route::post('/impersonate/leave', [UserController::class, 'leaveImpersonate'])->name('impersonate.leave');

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
        Route::post('/showrooms/bulk-delete', [ShowroomController::class, 'bulkDestroy'])->name('showrooms.bulk-destroy');
        Route::post('/showrooms/bulk-destroy-alias', [ShowroomController::class, 'bulkDestroy'])->name('showrooms.bulk-delete');
        Route::get('/showrooms/export', [ShowroomController::class, 'export'])->name('showrooms.export');
        Route::get('/showrooms/{showroom}', [ShowroomController::class, 'show'])->name('showrooms.show');
        Route::get('/showrooms/{showroom}/edit', [ShowroomController::class, 'edit'])->name('showrooms.edit');
        Route::put('/showrooms/{showroom}', [ShowroomController::class, 'update'])->name('showrooms.update');
        Route::delete('/showrooms/{showroom}', [ShowroomController::class, 'destroy'])->name('showrooms.destroy');
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
        Route::match(['get', 'post'], '/subscriptions/export', [SubscriptionController::class, 'export'])->name('subscriptions.export');
        Route::post('/subscriptions/{subscription}/extend', [SubscriptionController::class, 'extend'])->name('subscriptions.extend');
        Route::post('/subscriptions/{subscription}/change-plan', [SubscriptionController::class, 'changePlan'])->name('subscriptions.change-plan');
        Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

        // Audit Trail Logs
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // Platform Settings
        Route::get('/settings', [PlatformSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [PlatformSettingController::class, 'update'])->name('settings.update');

        // Platform Analytics & Intelligence Oversight
        Route::get('/analytics', [PlatformAnalyticsController::class, 'index'])->name('analytics.index');

        // Platform Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
        Route::post('/users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
        Route::post('/users/bulk-destroy-alias', [UserController::class, 'bulkDestroy'])->name('users.bulk-delete');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
    });
});

// Public Showroom Storefront Route (Tenant-resolved via EnsureTenantContext)
Route::middleware('tenant')->prefix('showrooms/{showroom}')->group(function () {
    Route::get('/', function (Showroom $showroom) {
        if (! $showroom->isWebsiteAccessible()) {
            if (auth()->check() && (auth()->user()->isAdmin() || auth()->user()->canAccessTenant($showroom))) {
                session()->flash('offline_preview', true);
            } else {
                return response()->view('showrooms.offline', compact('showroom'), 503);
            }
        }

        $showroom->load([
            'featuredBikes.images',
            'publishedBikes.images',
            'activePromotionalCampaigns',
            'activeServices',
        ]);

        return view('showrooms.public', compact('showroom'));
    })->name('showrooms.show');

    // Public Motorcycle Catalog & 360 Studio
    Route::get('/bikes', [PublicBikeController::class, 'index'])->name('showrooms.bikes.index');
    Route::get('/bikes/{bike}', [PublicBikeController::class, 'show'])->name('showrooms.bikes.show');
    Route::get('/bikes/{bike}/checkout', [PublicBikeController::class, 'checkout'])->name('showrooms.bikes.checkout');
    Route::get('/bikes/{bike}/360', [PublicBike360Controller::class, 'viewer'])->name('showrooms.bikes.360');
    Route::get('/bikes/{bike}/360/data', [PublicBike360Controller::class, 'data'])->name('showrooms.bikes.360.data');

    // Customer Enquiries & Test-Rides
    Route::get('/enquiries/create', [PublicEnquiryController::class, 'create'])->name('showrooms.enquiries.create');
    Route::post('/enquiries', [PublicEnquiryController::class, 'store'])->name('showrooms.enquiries.store');

    // Workshop & Service Appointment Booking
    Route::get('/services/book', [PublicServiceController::class, 'create'])->name('showrooms.services.create');
    Route::get('/services/slots', [PublicServiceController::class, 'slots'])->name('showrooms.services.slots');
    Route::post('/services/book', [PublicServiceController::class, 'store'])->name('showrooms.services.store');
    Route::get('/services/track/{booking?}', [PublicServiceController::class, 'track'])->name('showrooms.services.track');
    Route::get('/services/{booking}/checkout', [PaymentCheckoutController::class, 'serviceCheckout'])->name('showrooms.services.checkout');

    // Motorcycle Booking Checkout, Success & Error Resolution
    Route::get('/bikes/booking/{enquiry}/checkout', [PaymentCheckoutController::class, 'bikeBookingCheckout'])->name('showrooms.bikes.booking.checkout');
    Route::get('/bikes/booking/{enquiry}/success', [PaymentCheckoutController::class, 'bikeBookingSuccess'])->name('showrooms.booking.success');
    Route::get('/bikes/booking/{enquiry}/failed', [PaymentCheckoutController::class, 'bikeBookingFailed'])->name('showrooms.booking.failed');
    Route::post('/bikes/booking/{enquiry}/switch-offline', [PaymentCheckoutController::class, 'switchBookingToOffline'])->name('showrooms.booking.switch-offline');

    // Payment Signature Verification
    Route::post('/payments/verify', [PaymentCheckoutController::class, 'verifyPayment'])->name('showrooms.payments.verify');

    // Careers & Job Openings (Public Dealership Portal)
    Route::get('/careers', [PublicCareerController::class, 'index'])->name('showrooms.careers.index');
    Route::get('/careers/apply', [PublicCareerController::class, 'generalApply'])->name('showrooms.careers.general-apply');
    Route::post('/careers/apply', [PublicCareerController::class, 'storeGeneralApplication'])->name('showrooms.careers.store-general');
    Route::get('/careers/{jobOpening}', [PublicCareerController::class, 'show'])->name('showrooms.careers.show');
    Route::post('/careers/{jobOpening}/apply', [PublicCareerController::class, 'apply'])->name('showrooms.careers.apply');

    // Promotional Marketing & Scratch Cards (Public Portal)
    Route::get('/promotions', [PublicScratchCardController::class, 'index'])->name('showrooms.promotions.index');
    Route::get('/promotions/{campaign:slug}', [PublicScratchCardController::class, 'show'])->name('showrooms.promotions.show');
    Route::post('/promotions/{campaign:slug}/claim', [PublicScratchCardController::class, 'claim'])->name('showrooms.promotions.claim');
    Route::get('/scratch/{participation:participation_token}', [PublicScratchCardController::class, 'play'])->name('showrooms.scratch.play');
    Route::post('/scratch/{participation:participation_token}/reveal', [PublicScratchCardController::class, 'reveal'])->name('showrooms.scratch.reveal');

    // AI Virtual Showroom Concierge (Public Chat)
    Route::post('/ai/chat', [PublicAiAssistantController::class, 'chat'])->name('showrooms.ai.chat');
    Route::get('/ai/history', [PublicAiAssistantController::class, 'history'])->name('showrooms.ai.history');
    Route::get('/ai/settings', [PublicAiAssistantController::class, 'settings'])->name('showrooms.ai.settings');

    // Showroom Billing & Subscription Hub
    Route::middleware(['auth', 'tenant.member'])->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('showrooms.billing.index');
        Route::post('/billing/select-plan', [BillingController::class, 'selectPlan'])->name('showrooms.billing.select-plan');
        Route::get('/billing/checkout', [PaymentCheckoutController::class, 'subscriptionCheckout'])->name('showrooms.billing.checkout');
    });

    // Authenticated Showroom Back-Office (Tenant isolation + Active Subscription check)
    Route::middleware(['auth', 'tenant.member', 'subscription.active'])->group(function () {
        Route::get('/manage', [App\Http\Controllers\Showroom\DashboardController::class, 'index'])->name('showrooms.manage');
        Route::post('/onboarding', [OnboardingController::class, 'save'])->name('showrooms.onboarding.save');

        // Financial Ledger & Payment Transactions
        Route::get('/manage/payments', [PaymentHistoryController::class, 'index'])->name('showrooms.manage.payments.index');
        Route::get('/manage/payments/{payment}', [PaymentHistoryController::class, 'show'])->name('showrooms.manage.payments.show');

        // Transactional Email Templates & Previews
        Route::get('/manage/email-previews', [EmailPreviewController::class, 'index'])->name('showrooms.manage.email-previews.index');
        Route::get('/manage/email-previews/{type}', [EmailPreviewController::class, 'show'])->name('showrooms.manage.email-previews.show');

        // Customer Purchase Enquiries & Test Rides
        Route::get('/manage/enquiries', [EnquiryController::class, 'index'])->name('showrooms.manage.enquiries.index');
        Route::post('/manage/enquiries/bulk-delete', [EnquiryController::class, 'bulkDestroy'])->name('showrooms.manage.enquiries.bulk-destroy');
        Route::post('/manage/enquiries/bulk-destroy-alias', [EnquiryController::class, 'bulkDestroy'])->name('showrooms.manage.enquiries.bulk-delete');
        Route::get('/manage/enquiries/export', [EnquiryController::class, 'export'])->name('showrooms.manage.enquiries.export');
        Route::get('/manage/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('showrooms.manage.enquiries.show');
        Route::put('/manage/enquiries/{enquiry}', [EnquiryController::class, 'update'])->name('showrooms.manage.enquiries.update');
        Route::delete('/manage/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('showrooms.manage.enquiries.destroy');

        // Service Booking & Workshop Bay Management
        Route::get('/manage/services', [ServiceBookingController::class, 'index'])->name('showrooms.manage.services.index');
        Route::post('/manage/services/packages', [ShowroomServicePackageController::class, 'store'])->name('showrooms.manage.services.packages.store');
        Route::put('/manage/services/packages/{service}', [ShowroomServicePackageController::class, 'update'])->name('showrooms.manage.services.packages.update');
        Route::delete('/manage/services/packages/{service}', [ShowroomServicePackageController::class, 'destroy'])->name('showrooms.manage.services.packages.destroy');
        Route::post('/manage/services/packages/{service}/toggle-payment', [ShowroomServicePackageController::class, 'togglePayment'])->name('showrooms.manage.services.packages.toggle-payment');
        Route::post('/manage/services/packages/{service}/toggle-active', [ShowroomServicePackageController::class, 'toggleActive'])->name('showrooms.manage.services.packages.toggle-active');
        Route::post('/manage/services/bulk-delete', [ServiceBookingController::class, 'bulkDestroy'])->name('showrooms.manage.services.bulk-destroy');
        Route::post('/manage/services/bulk-destroy-alias', [ServiceBookingController::class, 'bulkDestroy'])->name('showrooms.manage.services.bulk-delete');
        Route::get('/manage/services/export', [ServiceBookingController::class, 'export'])->name('showrooms.manage.services.export');
        Route::get('/manage/services/{booking}', [ServiceBookingController::class, 'show'])->name('showrooms.manage.services.show');
        Route::put('/manage/services/{booking}', [ServiceBookingController::class, 'update'])->name('showrooms.manage.services.update');
        Route::delete('/manage/services/{booking}', [ServiceBookingController::class, 'destroy'])->name('showrooms.manage.services.destroy');

        // Motorcycle Inventory Management
        Route::get('/manage/bikes', [BikeController::class, 'index'])->name('showrooms.manage.bikes.index');
        Route::get('/manage/bikes/create', [BikeController::class, 'create'])->name('showrooms.manage.bikes.create');
        Route::post('/manage/bikes', [BikeController::class, 'store'])->name('showrooms.manage.bikes.store');
        Route::post('/manage/bikes/bulk-delete', [BikeController::class, 'bulkDestroy'])->name('showrooms.manage.bikes.bulk-destroy');
        Route::post('/manage/bikes/bulk-destroy-alias', [BikeController::class, 'bulkDestroy'])->name('showrooms.manage.bikes.bulk-delete');
        Route::get('/manage/bikes/export', [BikeController::class, 'export'])->name('showrooms.manage.bikes.export');
        Route::get('/manage/bikes/{bike}/edit', [BikeController::class, 'edit'])->name('showrooms.manage.bikes.edit');
        Route::put('/manage/bikes/{bike}', [BikeController::class, 'update'])->name('showrooms.manage.bikes.update');
        Route::delete('/manage/bikes/{bike}', [BikeController::class, 'destroy'])->name('showrooms.manage.bikes.destroy');
        Route::delete('/manage/bikes/{bike}/images/{image}', [BikeController::class, 'destroyImage'])->name('showrooms.manage.bikes.images.destroy');
        Route::post('/manage/bikes/{bike}/toggle-featured', [BikeController::class, 'toggleFeatured'])->name('showrooms.manage.bikes.toggle-featured');
        Route::post('/manage/bikes/{bike}/toggle-published', [BikeController::class, 'togglePublished'])->name('showrooms.manage.bikes.toggle-published');

        // Motorcycle 360-Degree Studio Management
        Route::get('/manage/bikes/{bike}/360', [Bike360ViewerController::class, 'index'])->name('showrooms.manage.bikes.360');
        Route::put('/manage/bikes/{bike}/360/settings', [Bike360ViewerController::class, 'updateSettings'])->name('showrooms.manage.bikes.360.settings');
        Route::post('/manage/bikes/{bike}/360/frames', [Bike360ViewerController::class, 'uploadFrames'])->name('showrooms.manage.bikes.360.frames.store');
        Route::delete('/manage/bikes/{bike}/360/frames/{image}', [Bike360ViewerController::class, 'deleteFrame'])->name('showrooms.manage.bikes.360.frames.destroy');
        Route::post('/manage/bikes/{bike}/360/frames/clear', [Bike360ViewerController::class, 'clearAllFrames'])->name('showrooms.manage.bikes.360.frames.clear');
        Route::post('/manage/bikes/{bike}/360/hotspots', [Bike360ViewerController::class, 'updateHotspots'])->name('showrooms.manage.bikes.360.hotspots');

        // Careers & Job Openings Management
        Route::get('/manage/careers', [CareerManagementController::class, 'index'])->name('showrooms.manage.careers.index');
        Route::get('/manage/careers/create', [CareerManagementController::class, 'create'])->name('showrooms.manage.careers.create');
        Route::post('/manage/careers', [CareerManagementController::class, 'store'])->name('showrooms.manage.careers.store');
        Route::get('/manage/careers/{jobOpening}/edit', [CareerManagementController::class, 'edit'])->name('showrooms.manage.careers.edit');
        Route::put('/manage/careers/{jobOpening}', [CareerManagementController::class, 'update'])->name('showrooms.manage.careers.update');
        Route::delete('/manage/careers/{jobOpening}', [CareerManagementController::class, 'destroy'])->name('showrooms.manage.careers.destroy');
        Route::post('/manage/careers/{jobOpening}/toggle-active', [CareerManagementController::class, 'toggleActive'])->name('showrooms.manage.careers.toggle-active');

        // Job Applicants & ATS Workflow Ledger
        Route::get('/manage/applicants', [ApplicantManagementController::class, 'index'])->name('showrooms.manage.applicants.index');
        Route::get('/manage/applicants/{applicant}', [ApplicantManagementController::class, 'show'])->name('showrooms.manage.applicants.show');
        Route::put('/manage/applicants/{applicant}', [ApplicantManagementController::class, 'update'])->name('showrooms.manage.applicants.update');
        Route::delete('/manage/applicants/{applicant}', [ApplicantManagementController::class, 'destroy'])->name('showrooms.manage.applicants.destroy');

        // Promotional Campaigns & Scratch Card Marketing Management
        Route::get('/manage/campaigns', [CampaignManagementController::class, 'index'])->name('showrooms.manage.campaigns.index');
        Route::get('/manage/campaigns/create', [CampaignManagementController::class, 'create'])->name('showrooms.manage.campaigns.create');
        Route::post('/manage/campaigns', [CampaignManagementController::class, 'store'])->name('showrooms.manage.campaigns.store');
        Route::get('/manage/campaigns/{campaign}/edit', [CampaignManagementController::class, 'edit'])->name('showrooms.manage.campaigns.edit');
        Route::put('/manage/campaigns/{campaign}', [CampaignManagementController::class, 'update'])->name('showrooms.manage.campaigns.update');
        Route::delete('/manage/campaigns/{campaign}', [CampaignManagementController::class, 'destroy'])->name('showrooms.manage.campaigns.destroy');
        Route::post('/manage/campaigns/{campaign}/toggle-status', [CampaignManagementController::class, 'toggleStatus'])->name('showrooms.manage.campaigns.toggle-status');
        Route::get('/manage/campaigns/{campaign}/participations', [CampaignManagementController::class, 'participations'])->name('showrooms.manage.campaigns.participations');
        Route::match(['get', 'post'], '/manage/campaigns/{campaign}/participations/export', [CampaignManagementController::class, 'exportParticipations'])->name('showrooms.manage.campaigns.participations.export');
        Route::post('/manage/coupons/redeem', [CampaignManagementController::class, 'redeemCoupon'])->name('showrooms.manage.coupons.redeem');

        // Website Builder & Storefront Customizer
        Route::get('/manage/website', [WebsiteBuilderController::class, 'edit'])->name('showrooms.manage.website');
        Route::put('/manage/website', [WebsiteBuilderController::class, 'update'])->name('showrooms.manage.website.update');

        // Motorcycle Category Master Management
        Route::get('/manage/categories', [CategoryManagementController::class, 'index'])->name('showrooms.manage.categories.index');
        Route::post('/manage/categories', [CategoryManagementController::class, 'store'])->name('showrooms.manage.categories.store');
        Route::put('/manage/categories/{category}', [CategoryManagementController::class, 'update'])->name('showrooms.manage.categories.update');
        Route::delete('/manage/categories/{category}', [CategoryManagementController::class, 'destroy'])->name('showrooms.manage.categories.destroy');
        Route::post('/manage/categories/{category}/toggle-active', [CategoryManagementController::class, 'toggleActive'])->name('showrooms.manage.categories.toggle-active');

        // Hero & Promotional Banners Management
        Route::get('/manage/banners', [BannerManagementController::class, 'index'])->name('showrooms.manage.banners.index');
        Route::post('/manage/banners', [BannerManagementController::class, 'store'])->name('showrooms.manage.banners.store');
        Route::put('/manage/banners/{banner}', [BannerManagementController::class, 'update'])->name('showrooms.manage.banners.update');
        Route::delete('/manage/banners/{banner}', [BannerManagementController::class, 'destroy'])->name('showrooms.manage.banners.destroy');
        Route::post('/manage/banners/{banner}/toggle-active', [BannerManagementController::class, 'toggleActive'])->name('showrooms.manage.banners.toggle-active');

        // AI Virtual Assistant Management & Conversations Ledger
        Route::get('/manage/ai', [AiAssistantManagementController::class, 'index'])->name('showrooms.manage.ai.index');
        Route::put('/manage/ai/settings', [AiAssistantManagementController::class, 'updateSettings'])->name('showrooms.manage.ai.settings');
        Route::get('/manage/ai/conversations/{conversation}', [AiAssistantManagementController::class, 'showConversation'])->name('showrooms.manage.ai.conversations.show');
        Route::delete('/manage/ai/conversations/{conversation}', [AiAssistantManagementController::class, 'destroyConversation'])->name('showrooms.manage.ai.conversations.destroy');

        // Analytics & Business Intelligence Reports
        Route::get('/manage/analytics', [ShowroomAnalyticsController::class, 'index'])->name('showrooms.manage.analytics.index');
        Route::get('/manage/analytics/export', [ShowroomAnalyticsController::class, 'export'])->name('showrooms.manage.analytics.export');

        // Showroom Settings & Profile
        Route::get('/manage/settings', [SettingController::class, 'edit'])->name('showrooms.manage.settings');
        Route::put('/manage/settings', [SettingController::class, 'update'])->name('showrooms.manage.settings.update');

        // Showroom Technical Support & Help Center
        Route::get('/manage/support', [SupportController::class, 'index'])->name('showrooms.manage.support');
    });
});
