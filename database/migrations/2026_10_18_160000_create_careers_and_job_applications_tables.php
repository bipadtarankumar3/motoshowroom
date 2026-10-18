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
        Schema::create('job_openings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('slug', 160);
            $table->string('department', 60)->default('sales');
            $table->string('job_type', 40)->default('full_time');
            $table->string('experience_level', 50)->default('mid');
            $table->string('location', 150)->nullable();
            $table->string('salary_range', 100)->nullable();
            $table->longText('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('deadline')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'department']);
        });

        Schema::create('job_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('showrooms')->cascadeOnDelete();
            $table->foreignId('job_opening_id')->constrained('job_openings')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('applicant_number', 40);
            $table->string('applicant_name', 150);
            $table->string('applicant_email', 150);
            $table->string('applicant_phone', 30);
            $table->string('current_company', 150)->nullable();
            $table->decimal('experience_years', 4, 1)->default(0.0);
            $table->string('resume_path', 255)->nullable();
            $table->string('portfolio_url', 255)->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('status', 40)->default('new');
            $table->text('internal_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'applicant_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['job_opening_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_openings');
    }
};
