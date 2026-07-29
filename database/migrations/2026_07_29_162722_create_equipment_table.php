<?php
// File: database/migrations/2024_01_01_000009_create_equipment_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('equipment_code')->unique();
            $table->enum('type', [
                'excavator', 'bulldozer', 'crane', 'concrete_mixer', 
                'generator', 'compressor', 'drill', 'truck', 'other'
            ]);
            $table->string('model')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('serial_number')->unique()->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 15, 2)->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'repair', 'retired'])
                  ->default('available');
            $table->foreignId('current_site_id')->nullable()
                  ->constrained('sites')->onDelete('set null');
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('total_hours_used', 10, 2)->default(0);
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('specifications')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'current_site_id']);
        });

        // Create equipment_usage_logs table
        Schema::create('equipment_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('operator_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->decimal('hours_used', 8, 2)->nullable();
            $table->decimal('fuel_consumed', 8, 2)->nullable();
            $table->text('work_description')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->json('meter_readings')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['equipment_id', 'start_time']);
            $table->index(['site_id', 'status']);
        });

        // Create equipment_maintenance_logs table
        Schema::create('equipment_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['routine', 'repair', 'overhaul', 'inspection']);
            $table->text('description');
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('vendor')->nullable();
            $table->date('maintenance_date');
            $table->date('next_maintenance_date')->nullable();
            $table->json('parts_replaced')->nullable();
            $table->foreignId('performed_by')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['equipment_id', 'maintenance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_maintenance_logs');
        Schema::dropIfExists('equipment_usage_logs');
        Schema::dropIfExists('equipment');
    }
};