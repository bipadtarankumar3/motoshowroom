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
        Schema::create('service_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bike_id')->nullable()->constrained('bikes')->nullOnDelete();
            $table->string('booking_number', 40);
            $table->string('customer_name', 150);
            $table->string('customer_email', 150);
            $table->string('customer_phone', 30);
            $table->string('bike_make_model', 150);
            $table->string('registration_number', 50);
            $table->unsignedInteger('odometer_reading')->nullable();
            $table->string('service_type', 50);
            $table->date('booking_date');
            $table->string('time_slot', 50);
            $table->string('bay_number', 30)->nullable();
            $table->string('status', 40)->default('pending');
            $table->decimal('estimated_cost', 10, 2)->default(0.00);
            $table->decimal('final_cost', 10, 2)->nullable();
            $table->string('payment_status', 40)->default('pending');
            $table->text('customer_notes')->nullable();
            $table->text('technician_notes')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('in_service_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'booking_number']);
            $table->index(['tenant_id', 'booking_date', 'time_slot']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'registration_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_bookings');
    }
};
