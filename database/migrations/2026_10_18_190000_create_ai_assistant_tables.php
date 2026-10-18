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
        Schema::create('ai_assistant_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('bot_name', 100)->default('Showroom AI Concierge');
            $table->text('welcome_message')->nullable();
            $table->string('tone', 30)->default('friendly'); // 'friendly', 'professional', 'enthusiastic'
            $table->boolean('is_enabled')->default(true);
            $table->json('suggested_prompts')->nullable();
            $table->json('custom_faqs')->nullable();
            $table->timestamps();

            $table->unique('tenant_id');
        });

        Schema::create('ai_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('session_id', 100)->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 50)->nullable();
            $table->string('detected_intent', 50)->nullable(); // 'bike_recommendation', 'test_ride', 'service_inquiry', 'pricing', 'general'
            $table->boolean('lead_converted')->default(false);
            $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'session_id']);
            $table->index(['tenant_id', 'lead_converted']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('ai_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('ai_conversations')->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('sender', 20)->default('user'); // 'user', 'assistant', 'system'
            $table->text('message');
            $table->json('structured_payload')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_assistant_settings');
    }
};
