<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'project_id',
        'task_id',
        'message',
        'attachment_file',
        'attachment_name',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function task()
    {
        return $this->belongsTo(ProjectTask::class, 'task_id');
    }

    protected function getMessageAttribute($value)
    {
        if (session()->has('demo_track_id')) {
            return '🔒 [Disensor untuk Akun Demo]';
        }

        return $value;
    }

    protected function getAttachmentNameAttribute($value)
    {
        if (session()->has('demo_track_id') && ! empty($value)) {
            return '🔒 file_disensor.pdf';
        }

        return $value;
    }

    protected function getAttachmentFileAttribute($value)
    {
        if (session()->has('demo_track_id')) {
            return null;
        }

        return $value;
    }
}
