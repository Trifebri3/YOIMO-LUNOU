<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_profile_id')->nullable()->constrained('company_profiles')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // 1. Basic Info
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('client_name')->nullable();
            $table->string('category')->default('Software Development');
            $table->enum('status', ['Planning', 'Active', 'On Hold', 'Completed'])->default('Planning');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Urgent'])->default('Medium');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->decimal('budget', 15, 2)->default(0);
            $table->decimal('budget_spent', 15, 2)->default(0);
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->enum('current_stage', ['Planning', 'Development', 'Review', 'Revision', 'Launch'])->default('Planning');

            // 2. Project Brief
            $table->text('problem_statement')->nullable();
            $table->text('project_goals')->nullable();
            $table->text('expected_outputs')->nullable();

            // 3. Scope & Milestones (JSON Modular)
            $table->json('scope_included')->nullable();   // Array: ["Fitur Login", "Dashboard Admin"]
            $table->json('scope_excluded')->nullable();   // Array: ["Aplikasi iOS Native"]
            $table->json('milestones')->nullable();       // Array: [{"title": "MVP Launch", "date": "2026-09-01", "status": "Pending"}]
            $table->json('deliverables')->nullable();     // Array: ["Source Code Laravel", "Figma Design UI/UX"]

            // 4. Team Matrix & Roles (JSON)
            $table->json('team_matrix')->nullable();      // Array: [{"user_id": 1, "role": "PM", "name": "Budi"}]

            // 5. Assets & Documents
            $table->json('documents')->nullable();        // Array: [{"title": "Kontrak Kerja", "file_url": "..."}]

            // 6. Notes & Feedback Log
            $table->json('meeting_notes')->nullable();    // Array: [{"date": "2026-08-26", "note": "..."}]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
