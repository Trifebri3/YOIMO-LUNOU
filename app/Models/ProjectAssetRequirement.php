<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssetRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'category',
        'is_mandatory',
        'status',
        'submitted_by_name',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'client_notes',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'review_feedback',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isImage(): bool
    {
        if (! $this->file_name) {
            return false;
        }

        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }
}
