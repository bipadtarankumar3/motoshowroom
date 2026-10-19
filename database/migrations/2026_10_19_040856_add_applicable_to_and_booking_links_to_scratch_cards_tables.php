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
        Schema::table('promotional_campaigns', function (Blueprint $table): void {
            if (! Schema::hasColumn('promotional_campaigns', 'applicable_to')) {
                $table->string('applicable_to', 30)->default('all')->after('status');
                $table->index(['tenant_id', 'applicable_to']);
            }
        });

        Schema::table('scratch_card_participations', function (Blueprint $table): void {
            if (! Schema::hasColumn('scratch_card_participations', 'enquiry_id')) {
                $table->foreignId('enquiry_id')->nullable()->after('service_booking_id')->constrained('enquiries')->nullOnDelete();
            }
            if (! Schema::hasColumn('scratch_card_participations', 'booking_type')) {
                $table->string('booking_type', 30)->nullable()->after('enquiry_id');
                $table->index(['tenant_id', 'booking_type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scratch_card_participations', function (Blueprint $table): void {
            if (Schema::hasColumn('scratch_card_participations', 'enquiry_id')) {
                $table->dropForeign(['enquiry_id']);
                $table->dropColumn('enquiry_id');
            }
            if (Schema::hasColumn('scratch_card_participations', 'booking_type')) {
                $table->dropIndex(['tenant_id', 'booking_type']);
                $table->dropColumn('booking_type');
            }
        });

        Schema::table('promotional_campaigns', function (Blueprint $table): void {
            if (Schema::hasColumn('promotional_campaigns', 'applicable_to')) {
                $table->dropIndex(['tenant_id', 'applicable_to']);
                $table->dropColumn('applicable_to');
            }
        });
    }
};
