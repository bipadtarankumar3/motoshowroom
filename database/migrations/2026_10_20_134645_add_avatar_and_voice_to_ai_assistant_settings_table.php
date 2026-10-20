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
            $table->string('avatar_type', 50)->default('male_tvs')->after('tone');
            $table->string('avatar_custom_url')->nullable()->after('avatar_type');
            $table->boolean('voice_enabled')->default(true)->after('avatar_custom_url');
            $table->string('voice_language', 20)->default('hi-IN')->after('voice_enabled');
            $table->text('voice_welcome_text')->nullable()->after('voice_language');
            $table->string('voice_audio_path')->nullable()->after('voice_welcome_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_assistant_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'avatar_type',
                'avatar_custom_url',
                'voice_enabled',
                'voice_language',
                'voice_welcome_text',
                'voice_audio_path',
            ]);
        });
    }
};
