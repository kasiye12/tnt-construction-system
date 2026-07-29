<?php
// File: database/migrations/2024_01_01_000007_create_daily_reports_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('cascade');
            $table->date('report_date');
            $table->integer('workforce_count')->nullable();
            $table->integer('subcontractor_count')->nullable();
            $table->integer('absent_count')->nullable();
            $table->decimal('progress_percentage', 5, 2)->nullable();
            $table->json('equipment_hours')->nullable();
            $table->json('weather_conditions')->nullable();
            $table->text('summary_text')->nullable();
            $table->text('challenges_encountered')->nullable();
            $table->text('safety_incidents')->nullable();
            $table->text('material_deliveries')->nullable();
            $table->text('quality_inspections')->nullable();
            $table->text('visitors')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])
                  ->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->json('location_data')->nullable();
            $table->boolean('is_offline_submission')->default(false);
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['site_id', 'report_date']);
            $table->index(['project_id', 'report_date']);
            $table->index(['status', 'submitted_by']);
        });

        // Create report_attachments table
        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('original_name');
            $table->bigInteger('file_size');
            $table->string('mime_type');
            $table->json('metadata')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Create report_comments table
        Schema::create('report_comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_comments');
        Schema::dropIfExists('report_attachments');
        Schema::dropIfExists('daily_reports');
    }
};