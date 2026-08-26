<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_profile_id',
        'created_by',
        'demo_track_id',
        'name',
        'slug',
        'client_name',
        'client_logo',
        'project_cover',
        'demo_url',
        'category',
        'status',
        'priority',
        'start_date',
        'deadline',
        'budget',
        'budget_spent',
        'is_financial_transparent',
        'progress_percentage',
        'current_stage',
        'is_showcased',
        'problem_statement',
        'solution_statement',
        'result_statement',
        'project_goals',
        'expected_outputs',
        'short_description',
        'services_rendered',
        'tech_stacks',
        'gallery_images',
        'client_testimonial',
        'scope_included',
        'scope_excluded',
        'milestones',
        'deliverables',
        'team_matrix',
        'documents',
        'meeting_notes',
    ];

    protected $casts = [
        'is_financial_transparent' => 'boolean',
        'is_showcased' => 'boolean',
        'services_rendered' => 'array',
        'tech_stacks' => 'array',
        'gallery_images' => 'array',
        'client_testimonial' => 'array',
        'scope_included' => 'array',
        'scope_excluded' => 'array',
        'milestones' => 'array',
        'deliverables' => 'array',
        'team_matrix' => 'array',
        'documents' => 'array',
        'meeting_notes' => 'array',
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    protected static function booted()
    {
        if (session()->has('demo_track_id')) {
            $trackId = session()->get('demo_track_id');
            static::addGlobalScope('demo_isolation', function ($builder) use ($trackId) {
                $builder->where('demo_track_id', $trackId);
            });
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($project) {
            if (empty($project->slug) && ! empty($project->name)) {
                $project->slug = Str::slug($project->name).'-'.Str::random(5);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }

    public function roadmaps()
    {
        return $this->hasMany(ProjectRoadmap::class, 'project_id')->orderBy('start_date', 'asc');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_id')->latest();
    }

    public function agendas()
    {
        return $this->hasMany(ProjectAgenda::class, 'project_id')->orderBy('start_date', 'asc');
    }

    public function expenses()
    {
        return $this->hasMany(ProjectExpense::class, 'project_id')->orderBy('expense_date', 'desc');
    }

    public function repositoryDocuments()
    {
        return $this->hasMany(ProjectDocument::class, 'project_id')->latest();
    }

    public function activityLogs()
    {
        return $this->hasMany(ProjectActivityLog::class, 'project_id')->latest();
    }

    public function recalculateProgressAndStage()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            $progress = 0;
            $stage = 'Planning';
        } else {
            $sumProgress = 0;
            foreach ($this->tasks as $task) {
                if ($task->status === 'Completed') {
                    $sumProgress += 100;
                } elseif ($task->status === 'Todo') {
                    $sumProgress += 0;
                } else {
                    $sumProgress += max(10, intval($task->progress_percentage));
                }
            }
            $progress = min(100, max(0, round($sumProgress / $totalTasks)));

            if ($progress == 0) {
                $stage = 'Planning';
            } elseif ($progress < 70) {
                $stage = 'Development';
            } elseif ($progress < 90) {
                $stage = 'Review';
            } elseif ($progress < 100) {
                $stage = 'Revision';
            } else {
                $stage = 'Launch';
            }
        }

        $this->update([
            'progress_percentage' => $progress,
            'current_stage' => $stage,
        ]);
    }
}
