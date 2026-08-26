<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'award_type',
        'title',
        'issued_date',
        'share_token',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
