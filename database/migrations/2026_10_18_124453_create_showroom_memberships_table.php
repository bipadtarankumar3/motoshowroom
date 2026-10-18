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
        Schema::create('showroom_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showroom_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 50)->default('owner');
            $table->json('permissions')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();

            $table->unique(['showroom_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('showroom_memberships');
    }
};
