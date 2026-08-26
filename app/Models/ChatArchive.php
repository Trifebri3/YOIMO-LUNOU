<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatArchive extends Model
{
    protected $fillable = [
        'user_id',
        'archived_user_id',
        'archived_project_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archivedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_user_id');
    }

    public function archivedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'archived_project_id');
    }
}
