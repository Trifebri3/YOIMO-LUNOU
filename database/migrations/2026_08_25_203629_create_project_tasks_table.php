<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_roadmap_id')->nullable()->constrained('project_roadmaps')->nullOnDelete();
            $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete(); // 1 User PIC penanggung jawab
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // Detail Tugas
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->enum('status', ['Todo', 'In Progress', 'Review', 'Completed'])->default('Todo');
            $table->date('due_date')->nullable();

            // Keterikatan Target Output Linimasa (Opsional)
            $table->integer('linked_objective_index')->nullable(); // Index target di JSON objectives roadmap

            // Hasil Laporan & Bukti Kerja dari User
            $table->text('submission_notes')->nullable();
            $table->string('submission_file')->nullable();
            $table->string('submission_link')->nullable();
            $table->dateTime('submitted_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
