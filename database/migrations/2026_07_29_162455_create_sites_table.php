<?php
// File: database/migrations/2024_01_01_000004_create_sites_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('site_name');
            $table->string('site_code')->unique();
            $table->json('location_coordinates')->nullable();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'active', 'inactive', 'completed', 'suspended'])
                  ->default('pending');
            $table->enum('type', ['main_site', 'sub_site', 'temporary'])->default('main_site');
            $table->text('address')->nullable();
            $table->string('landmark')->nullable();
            $table->decimal('area_sqm', 10, 2)->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->integer('max_workers')->nullable();
            $table->json('facilities')->nullable();
            $table->json('settings')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('site_code');
        });

        // Create site_workers pivot table
        Schema::create('site_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('worker');
            $table->string('shift')->nullable();
            $table->date('assigned_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->unique(['site_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_workers');
        Schema::dropIfExists('sites');
    }
};