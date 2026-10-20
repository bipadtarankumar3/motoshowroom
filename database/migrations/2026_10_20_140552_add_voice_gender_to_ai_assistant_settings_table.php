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
            $table->string('voice_gender', 20)->default('male')->after('voice_language');
            $table->decimal('voice_pitch', 3, 2)->nullable()->default(1.00)->after('voice_gender');
            $table->string('voice_name', 100)->nullable()->after('voice_pitch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistant_settings', function (Blueprint $table): void {
            $table->dropColumn(['voice_gender', 'voice_pitch', 'voice_name']);
        });
    }
};
