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
        Schema::table('bikes', function (Blueprint $table): void {
            $table->boolean('has_360_view')->default(false)->after('status');
            $table->string('viewer_type', 30)->default('image_sequence')->after('has_360_view');
            $table->string('model_3d_path', 1024)->nullable()->after('viewer_type');
            $table->json('viewer_settings')->nullable()->after('model_3d_path');
            $table->json('hotspots')->nullable()->after('viewer_settings');
        });

        Schema::table('bike_images', function (Blueprint $table): void {
            $table->boolean('is_360_frame')->default(false)->after('is_primary');
            $table->unsignedSmallInteger('frame_angle')->nullable()->after('is_360_frame');

            $table->index(['bike_id', 'is_360_frame', 'frame_angle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bike_images', function (Blueprint $table): void {
            $table->dropIndex(['bike_id', 'is_360_frame', 'frame_angle']);
            $table->dropColumn(['frame_angle', 'is_360_frame']);
        });

        Schema::table('bikes', function (Blueprint $table): void {
            $table->dropColumn([
                'has_360_view',
                'viewer_type',
                'model_3d_path',
                'viewer_settings',
                'hotspots',
            ]);
        });
    }
};
