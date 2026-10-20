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
        Schema::table('ai_assistant_settings', function (Blueprint $table): void {
            $table->string('presenter_mode', 20)->default('avatar')->after('is_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistant_settings', function (Blueprint $table): void {
            $table->dropColumn('presenter_mode');
        });
    }
};
