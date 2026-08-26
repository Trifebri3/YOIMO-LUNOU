<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AISetting extends Model
{
    use HasFactory;

    protected $table = 'ai_settings';

    protected $fillable = [
        'user_id',
        'provider',
        'name',
        'api_key',
        'model',
        'base_url',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];
}
