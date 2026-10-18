<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Financial Transactions & Payments Ledger
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payable_type', 150);
            $table->unsignedBigInteger('payable_id');
            $table->string('purpose', 50); // subscription_purchase, subscription_renewal, service_invoice, bike_booking_deposit
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->string('razorpay_order_id', 100)->nullable()->unique();
            $table->string('razorpay_payment_id', 100)->nullable()->unique();
            $table->string('razorpay_signature', 255)->nullable();
            $table->string('status', 30)->default('pending'); // pending, authorized, captured, failed, refunded, partially_refunded
            $table->string('method', 50)->nullable(); // upi, card, netbanking, wallet
            $table->boolean('signature_verified')->default(false);
            $table->string('error_code', 100)->nullable();
            $table->text('error_description')->nullable();
            $table->json('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status', 'created_at']);
            $table->index(['payable_type', 'payable_id']);
        });

        // 2. Payment Refunds Ledger
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('showrooms')->cascadeOnDelete();
            $table->string('razorpay_refund_id', 100)->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('INR');
            $table->text('reason')->nullable();
            $table->string('status', 30)->default('pending'); // pending, processed, failed
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        // 3. Idempotent Webhook Events Ledger
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id', 100)->unique();
            $table->string('event_type', 100);
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'processed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payments');
    }
};
