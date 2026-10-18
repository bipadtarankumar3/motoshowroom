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
        // 1. Subscription Plans Table
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 12, 2)->default(0.00);
            $table->decimal('annual_price', 12, 2)->default(0.00);
            $table->string('currency', 3)->default('INR');
            $table->unsignedInteger('trial_days')->default(14);

            // Entitlements & Numeric Limits
            $table->unsignedInteger('max_staff_accounts')->default(5);
            $table->unsignedInteger('max_bike_listings')->default(25);
            $table->unsignedInteger('max_monthly_bookings')->default(100);
            $table->unsignedInteger('media_storage_mb')->default(1024);

            // Feature Flags
            $table->boolean('has_careers_module')->default(true);
            $table->boolean('has_scratch_cards')->default(true);
            $table->boolean('has_360_viewer')->default(true);
            $table->boolean('has_ai_assistant')->default(false);
            $table->boolean('has_custom_domain')->default(false);
            $table->boolean('has_advanced_analytics')->default(false);

            $table->json('features_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // 2. Subscriptions Table
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showroom_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('subscription_plans')->restrictOnDelete();
            $table->string('status', 30)->default('trial');
            $table->string('billing_interval', 20)->default('monthly');
            $table->timestamp('current_period_start')->useCurrent();
            $table->timestamp('current_period_end')->useCurrent();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->string('last_payment_reference')->nullable();
            $table->timestamps();

            $table->index(['showroom_id', 'status', 'current_period_end']);
        });

        // 3. Subscription Histories Table
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('showroom_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('previous_status', 30)->nullable();
            $table->string('new_status', 30)->nullable();
            $table->foreignId('previous_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->foreignId('new_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['showroom_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
