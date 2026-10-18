<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Promotional Scratch Cards and Prize Allocation.
     */
    public function up(): void
    {
        Schema::create('promotional_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 160);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 30)->default('draft');
            $table->decimal('min_booking_amount', 12, 2)->default(0.00);
            $table->unsignedInteger('max_participations_per_customer')->default(1);
            $table->string('banner_image', 255)->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'start_date', 'end_date']);
        });

        Schema::create('campaign_prizes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('promotional_campaigns')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('prize_type', 50);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->unsignedInteger('total_quantity')->default(0);
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->unsignedInteger('probability_weight')->default(10);
            $table->string('icon_or_image', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['campaign_id', 'remaining_quantity']);
            $table->index(['tenant_id', 'campaign_id']);
        });

        Schema::create('scratch_card_participations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('promotional_campaigns')->cascadeOnDelete();
            $table->foreignId('prize_id')->nullable()->constrained('campaign_prizes')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('service_booking_id')->nullable()->constrained('service_bookings')->nullOnDelete();
            $table->string('customer_name', 150);
            $table->string('customer_email', 150);
            $table->string('customer_phone', 30);
            $table->string('participation_token', 100)->unique();
            $table->boolean('is_scratched')->default(false);
            $table->timestamp('scratched_at')->nullable();
            $table->string('coupon_code', 50)->nullable()->unique();
            $table->boolean('is_redeemed')->default(false);
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('redeemed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'campaign_id']);
            $table->index(['tenant_id', 'customer_email']);
            $table->index(['tenant_id', 'customer_phone']);
            $table->index(['tenant_id', 'coupon_code']);
            $table->index(['tenant_id', 'is_redeemed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scratch_card_participations');
        Schema::dropIfExists('campaign_prizes');
        Schema::dropIfExists('promotional_campaigns');
    }
};
