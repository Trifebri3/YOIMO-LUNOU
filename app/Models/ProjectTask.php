<?php

namespace App\Models;

use App\Services\GamificationService;
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
        'due_date' => 'date',
        'started_at' => 'datetime',
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
                GamificationService::awardTaskCompletion($task);
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
