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
        Schema::table('job_applications', function (Blueprint $table): void {
            $table->string('video_path', 255)->nullable()->after('resume_path');
            $table->unsignedSmallInteger('video_duration_seconds')->nullable()->after('video_path');
            $table->string('desired_role', 150)->nullable()->after('current_company');
            $table->foreignId('job_opening_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropColumn(['video_path', 'video_duration_seconds', 'desired_role']);
            $table->foreignId('job_opening_id')->nullable(false)->change();
        });
    }
};
