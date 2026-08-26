<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingReflection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'week_number',
        'what_went_well',
        'what_was_exhausting',
        'what_to_change',
        'what_proud_of',
        'summary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
