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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('user')->after('password');
            $table->string('phone', 30)->nullable()->after('role');
            $table->string('status', 30)->default('active')->after('phone');
            $table->text('bio')->nullable()->after('status');
            $table->string('address')->nullable()->after('bio');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('avatar_url', 1024)->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'status', 'bio', 'address', 'city', 'avatar_url']);
        });
    }
};
