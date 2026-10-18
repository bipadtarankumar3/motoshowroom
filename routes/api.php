<?php

use App\Http\Controllers\Api\RazorpayWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - MotoShowroom SaaS
|--------------------------------------------------------------------------
*/

// Razorpay Asynchronous Webhooks (Idempotent + HMAC-SHA256 verified)
Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])->name('api.webhooks.razorpay');
