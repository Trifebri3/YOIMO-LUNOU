<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Metadata Aksi
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action'); // CREATE, UPDATE, DELETE, SUBMIT, CLAIM, READ, TOGGLE
            $table->string('module'); // Project, Task, Roadmap, Expense, Agenda, Document
            $table->text('description'); // Narasi log audit
            $table->json('properties')->nullable(); // Data lama/baru (payload audit)
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_activity_logs');
    }
};