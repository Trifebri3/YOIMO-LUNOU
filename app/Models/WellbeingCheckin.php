<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingCheckin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'checkin_date',
        'mood',
        'energy',
        'mental_load',
        'rest_condition',
        'thoughts',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
