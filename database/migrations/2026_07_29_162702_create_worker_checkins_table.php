<?php
// File: database/migrations/2024_01_01_000008_create_worker_checkins_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_checkins', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->json('check_in_location')->nullable();
            $table->json('check_out_location')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->enum('check_in_method', ['telegram', 'mobile_app', 'manual', 'biometric'])
                  ->default('mobile_app');
            $table->enum('check_out_method', ['telegram', 'mobile_app', 'manual', 'biometric'])
                  ->nullable();
            $table->enum('status', ['checked_in', 'checked_out', 'absent', 'half_day', 'overtime'])
                  ->default('checked_in');
            $table->string('shift')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['site_id', 'check_in_time']);
            $table->index('status');
        });

        // Create attendance_summary table for monthly reports
        Schema::create('attendance_summaries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('overtime_days')->default(0);
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'site_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_summaries');
        Schema::dropIfExists('worker_checkins');
    }
};