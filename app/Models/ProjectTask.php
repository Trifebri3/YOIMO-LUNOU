<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'project_roadmap_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'attachment_file',
        'attachment_link',
        'priority',
        'status',
        'due_date',
        'started_at',
        'duration_minutes',
        'progress_percentage',
        'linked_objective_index',
        'submission_notes',
        'submission_file',
        'submission_link',
        'obstacles_faced',
        'self_evaluation',
        'submitted_at',
        'submission_timing_status',
        'revision_notes',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function roadmap()
    {
        return $this->belongsTo(ProjectRoadmap::class, 'project_roadmap_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progressLogs()
    {
        return $this->hasMany(TaskProgressLog::class, 'project_task_id')->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($task) {
            if ($task->isDirty('status') && $task->status === 'Completed' && $task->assigned_to) {
                $dueDate = $task->due_date;
                $companyId = $task->project ? $task->project->company_profile_id : null;
                $projectId = $task->project_id;
                
                if ($dueDate && \Carbon\Carbon::parse($dueDate)->isPast()) {
                    // Terlambat -> Minus 30 Poin
                    \App\Services\GamificationService::addPoints(
                        $task->assigned_to,
                        -30,
                        'task_overdue',
                        $task->id,
                        $companyId,
                        $projectId,
                        "Terlambat menyelesaikan tugas: " . $task->title
                    );
                } else {
                    // Tepat Waktu -> Tambah 50 Poin
                    \App\Services\GamificationService::addPoints(
                        $task->assigned_to,
                        50,
                        'task_ontime',
                        $task->id,
                        $companyId,
                        $projectId,
                        "Menyelesaikan tugas tepat waktu: " . $task->title
                    );
                }
                
                // Cek Milestone Badge Baru
                \App\Services\GamificationService::checkTaskMilestones($task->assigned_to);
            }
        });

        static::saved(function ($task) {
            if ($task->project) {
                $task->project->recalculateProgressAndStage();
            }
        });

        static::deleted(function ($task) {
            if ($task->project) {
                $task->project->recalculateProgressAndStage();
            }
        });
    }
}