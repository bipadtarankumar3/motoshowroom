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
        Schema::create('showroom_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('estimated_duration_minutes')->default(60);
            $table->boolean('is_payment_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('badge', 50)->nullable();
            $table->json('features')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active', 'sort_order']);
            $table->index(['tenant_id', 'slug']);
        });

        Schema::table('service_bookings', function (Blueprint $table) {
            $table->foreignId('showroom_service_id')->nullable()->after('bike_id')->constrained('showroom_services')->nullOnDelete();
            $table->boolean('terms_accepted')->default(false)->after('customer_notes');
            $table->boolean('payment_required')->default(false)->after('terms_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_bookings', function (Blueprint $table) {
            $table->dropForeign(['showroom_service_id']);
            $table->dropColumn(['showroom_service_id', 'terms_accepted', 'payment_required']);
        });

        Schema::dropIfExists('showroom_services');
    }
};
