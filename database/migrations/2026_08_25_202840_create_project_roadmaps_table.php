<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_roadmaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('title'); // Contoh: "Fase 1: Research & UI/UX Design"
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
            $table->unsignedTinyInteger('progress_percentage')->default(0);

            // Target Ketercapaian / Deliverable Milestones dalam Fase Ini
            $table->json('objectives')->nullable(); // Array: [{"target": "Design System Figma Selesai", "is_achieved": false}]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_roadmaps');
    }
};
