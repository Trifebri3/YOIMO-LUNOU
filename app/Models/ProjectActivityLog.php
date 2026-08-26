<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ProjectActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Helper statis untuk mencatat log audit dengan satu baris kode
     */
    public static function record(int $projectId, string $module, string $action, string $description, ?array $properties = null): self
    {
        $user = Auth::user();

        return self::create([
            'project_id'  => $projectId,
            'user_id'     => $user ? $user->id : null,
            'user_name'   => $user ? $user->name : 'System',
            'user_role'   => $user ? $user->role : 'system',
            'module'      => $module,
            'action'      => strtoupper($action),
            'description' => $description,
            'properties'  => $properties,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}