<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'category',
        'is_mandatory',
        'doc_type',
        'file_path',
        'file_name_original',
        'file_size',
        'external_url',
        'content',
        'readers_log',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'readers_log'  => 'array',
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