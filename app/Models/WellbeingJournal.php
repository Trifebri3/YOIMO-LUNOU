<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'raw_content',
        'feeling',
        'today_event',
        'gratitude',
        'let_go',
        'improvement',
        'analysis',
        'appreciation',
        'categories',
    ];

    protected $casts = [
        'categories' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
