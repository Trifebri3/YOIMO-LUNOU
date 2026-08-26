<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WellbeingLeftThought extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thought',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
