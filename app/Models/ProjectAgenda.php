<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAgenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'description',
        'category',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'recurrence',
        'recurrence_days',
        'location_type',
        'meeting_url',
        'location_address',
        'attendee_ids',
        'agenda_notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'attendee_ids' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
