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
        Schema::create('enquiries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bike_id')->nullable()->constrained('bikes')->nullOnDelete();
            $table->string('enquiry_number');
            $table->string('type')->default('purchase'); // purchase, test_ride, general
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time_slot')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->string('status')->default('new');
            $table->text('customer_notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('contacted_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'enquiry_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'bike_id']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
