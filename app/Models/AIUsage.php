<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIUsage extends Model
{
    use HasFactory;

    protected $table = 'ai_usages';

    protected $fillable = [
        'provider',
        'model',
        'user_id',
        'request_type',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'response_time_ms',
        'status',
        'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
