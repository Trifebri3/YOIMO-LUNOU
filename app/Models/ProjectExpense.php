<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'category',
        'amount',
        'expense_date',
        'receipt_file',
        'notes',
        'status',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
