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
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url', 1024)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'is_active', 'sort_order']);
        });

        Schema::create('banners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('badge')->nullable();
            $table->string('title_prefix')->nullable();
            $table->string('title_highlight')->nullable();
            $table->string('title_suffix')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('btn_primary_text')->nullable()->default('Explore Bikes');
            $table->string('btn_primary_url', 1024)->nullable();
            $table->string('btn_secondary_text')->nullable()->default('Book a Test Ride');
            $table->string('btn_secondary_url', 1024)->nullable();
            $table->string('feature_1_title')->nullable();
            $table->string('feature_1_sub')->nullable();
            $table->string('feature_2_title')->nullable();
            $table->string('feature_2_sub')->nullable();
            $table->string('feature_3_title')->nullable();
            $table->string('feature_3_sub')->nullable();
            $table->string('image_path')->nullable();
            $table->string('image_url', 1024)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'is_active', 'sort_order']);
        });

        Schema::table('bikes', function (Blueprint $table): void {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bikes', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('banners');
        Schema::dropIfExists('categories');
    }
};
