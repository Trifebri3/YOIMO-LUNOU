<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah field timer, evaluasi, revisi, dan status waktu pada project_tasks
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dateTime('started_at')->nullable()->after('due_date');
            $table->unsignedInteger('duration_minutes')->default(0)->after('started_at'); // Total durasi pengerjaan dalam menit
            $table->unsignedTinyInteger('progress_percentage')->default(0)->after('duration_minutes'); // 0 - 100%

            // Evaluasi kendala & hambatan
            $table->text('obstacles_faced')->nullable()->after('submission_notes'); // Kendala & Hambatan
            $table->text('self_evaluation')->nullable()->after('obstacles_faced');  // Evaluasi Diri
            $table->string('submission_timing_status')->nullable()->after('submitted_at'); // 'On Time' atau 'Overdue'

            // Catatan Revisi / Penolakan Management
            $table->text('revision_notes')->nullable()->after('submission_timing_status');
        });

        // 2. Buat tabel progress log bertahap (Partial Progress Logs)
        Schema::create('task_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_task_id')->constrained('project_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('progress_percentage'); // misal: 30%, 60%, 100%
            $table->text('notes'); // Catatan progress kecil hari ini
            $table->string('attachment_url')->nullable();
            $table->string('attachment_file')->nullable();
            $table->text('obstacles')->nullable(); // Kendala saat pengerjaan bertahap

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_progress_logs');
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn([
                'started_at', 'duration_minutes', 'progress_percentage',
                'obstacles_faced', 'self_evaluation', 'submission_timing_status', 'revision_notes',
            ]);
        });
    }
};
