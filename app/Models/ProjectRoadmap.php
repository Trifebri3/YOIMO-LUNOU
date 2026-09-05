<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRoadmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'progress_percentage',
        'objectives',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'objectives' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    // Di dalam class ProjectRoadmap
    public function tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_roadmap_id');
    }
}
