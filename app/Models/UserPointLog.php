<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'source_type',
        'source_id',
        'company_profile_id',
        'project_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
