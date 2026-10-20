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
        Schema::table('showroom_memberships', function (Blueprint $table) {
            $table->string('availability_status', 30)->default('available')->after('status')->index();
            $table->string('department', 100)->nullable()->after('availability_status');
            $table->string('designation', 100)->nullable()->after('department');
            $table->string('shift', 100)->nullable()->after('designation');
            $table->text('notes')->nullable()->after('shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showroom_memberships', function (Blueprint $table) {
            $table->dropIndex(['availability_status']);
            $table->dropColumn([
                'availability_status',
                'department',
                'designation',
                'shift',
                'notes',
            ]);
        });
    }
};
