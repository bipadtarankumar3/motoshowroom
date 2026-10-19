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
        Schema::table('enquiries', function (Blueprint $table) {
            $table->decimal('booking_amount', 12, 2)->nullable()->after('status');
            $table->string('payment_method', 30)->nullable()->default('offline')->after('booking_amount');
            $table->string('payment_status', 30)->nullable()->default('unpaid')->after('payment_method');
            $table->boolean('terms_accepted')->default(false)->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['booking_amount', 'payment_method', 'payment_status', 'terms_accepted']);
        });
    }
};
