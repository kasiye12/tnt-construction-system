<?php
// File: database/migrations/2024_01_01_000012_create_safety_incidents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('safety_incidents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('incident_number')->unique();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('incident_datetime');
            $table->enum('severity', ['minor', 'moderate', 'major', 'fatal'])->default('minor');
            $table->enum('type', [
                'injury', 'near_miss', 'property_damage', 'environmental', 
                'equipment_failure', 'fire', 'chemical', 'other'
            ]);
            $table->string('location')->nullable();
            $table->text('description');
            $table->text('immediate_actions')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->json('affected_persons')->nullable();
            $table->json('injuries_sustained')->nullable();
            $table->boolean('medical_treatment_required')->default(false);
            $table->boolean('work_stoppage')->default(false);
            $table->decimal('estimated_damage_cost', 15, 2)->nullable();
            $table->enum('status', [
                'reported', 'investigating', 'resolved', 'closed'
            ])->default('reported');
            $table->foreignId('investigated_by')->nullable()
                  ->constrained('users')->onDelete('set null');
            $table->dateTime('resolved_at')->nullable();
            $table->json('attachments')->nullable();
            $table->json('witnesses')->nullable();
            $table->text('preventive_measures')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index(['site_id', 'incident_datetime']);
        });

        // Create safety_inspections table
        Schema::create('safety_inspections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained('users')->onDelete('cascade');
            $table->date('inspection_date');
            $table->enum('type', ['routine', 'special', 'follow_up'])->default('routine');
            $table->json('checklist_items')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->integer('score')->nullable();
            $table->enum('result', ['pass', 'fail', 'conditional'])->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'inspection_date']);
        });

        // Create safety_violations table
        Schema::create('safety_violations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('violator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->date('violation_date');
            $table->string('violation_type');
            $table->text('description');
            $table->enum('severity', ['minor', 'major', 'critical'])->default('minor');
            $table->string('action_taken')->nullable();
            $table->boolean('warning_issued')->default(false);
            $table->boolean('fine_imposed')->default(false);
            $table->decimal('fine_amount', 10, 2)->nullable();
            $table->json('evidence')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'violation_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_violations');
        Schema::dropIfExists('safety_inspections');
        Schema::dropIfExists('safety_incidents');
    }
};