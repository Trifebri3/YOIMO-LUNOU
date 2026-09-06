<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'client_name',
        'recipient_id',
        'project_id',
        'task_id',
        'message',
        'message_type',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'resolver_name',
        'resolution_note',
        'attachment_file',
        'attachment_name',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
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

    public function getIsImageAttribute(): bool
    {
        if (empty($this->attachment_file)) {
            return false;
        }

        $extension = strtolower(pathinfo($this->attachment_file, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    public function getSenderDisplayNameAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->name;
        }

        return $this->client_name ?: 'Klien';
    }

    public function getSenderDisplayRoleAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->role ?? 'Tim Proyek';
        }

        return 'Klien';
    }

    public function getSenderDisplayAvatarAttribute(): ?string
    {
        if ($this->sender && $this->sender->avatar) {
            return asset('storage/'.$this->sender->avatar);
        }

        return null;
    }
}
