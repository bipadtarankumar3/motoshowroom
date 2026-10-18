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
        Schema::create('bikes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('sku');
            $table->string('brand');
            $table->unsignedSmallInteger('model_year');
            $table->string('category')->default('sports');
            $table->unsignedSmallInteger('engine_cc')->nullable();
            $table->decimal('power_bhp', 6, 2)->nullable();
            $table->decimal('torque_nm', 6, 2)->nullable();
            $table->string('fuel_type')->default('petrol');
            $table->string('transmission')->nullable();
            $table->decimal('mileage_kmpl', 5, 2)->nullable();
            $table->unsignedSmallInteger('top_speed_kmh')->nullable();
            $table->unsignedSmallInteger('curb_weight_kg')->nullable();
            $table->decimal('price', 12, 2);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->string('status')->default('in_stock');
            $table->longText('description')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->unique(['tenant_id', 'sku']);
            $table->index(['tenant_id', 'is_published', 'status']);
            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'brand']);
        });

        Schema::create('bike_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('bike_id')->constrained('bikes')->cascadeOnDelete();
            $table->string('image_url');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'bike_id']);
        });

        Schema::create('bike_variants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('bike_id')->constrained('bikes')->cascadeOnDelete();
            $table->string('name');
            $table->string('color_name');
            $table->string('color_hex', 20)->nullable();
            $table->decimal('price_difference', 12, 2)->default(0.00);
            $table->unsignedSmallInteger('stock_quantity')->default(1);
            $table->timestamps();

            $table->index(['tenant_id', 'bike_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bike_variants');
        Schema::dropIfExists('bike_images');
        Schema::dropIfExists('bikes');
    }
};
