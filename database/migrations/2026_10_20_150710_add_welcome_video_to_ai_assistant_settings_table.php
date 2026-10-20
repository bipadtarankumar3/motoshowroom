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
            $table->string('welcome_video_path', 1000)->nullable()->after('voice_audio_path');
            $table->string('welcome_video_type', 50)->default('none')->after('welcome_video_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistant_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'welcome_video_path',
                'welcome_video_type',
            ]);
        });
    }
};
