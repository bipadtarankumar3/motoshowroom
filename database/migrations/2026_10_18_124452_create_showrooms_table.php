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
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->timestamp('domain_verified_at')->nullable();
            $table->string('email');
            $table->string('phone', 50);
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 100)->default('India');
            $table->string('logo_url', 1024)->nullable();
            $table->string('banner_url', 1024)->nullable();
            $table->string('brand_color_primary', 20)->default('#DC2626');
            $table->string('brand_color_accent', 20)->default('#0F172A');
            $table->string('status', 30)->default('active');
            $table->boolean('public_website_enabled')->default(true);
            $table->boolean('read_only_mode')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'public_website_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showrooms');
    }
};
