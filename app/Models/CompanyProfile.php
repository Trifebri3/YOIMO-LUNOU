<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'manager_id',
        'company_name',
        'slug',
        'is_published',
        'tagline',
        'email',
        'phone',
        'address',
        'logo',
        'banner',
        'about',
        'vision',
        'mission',
        'social_media',
        'dynamic_sections',
    ];

    protected $casts = [
        'social_media'     => 'array',
        'dynamic_sections' => 'array',
        'is_published'     => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Otomatis buat slug jika belum ada saat create atau update nama
        static::saving(function ($company) {
            if (empty($company->slug) && !empty($company->company_name)) {
                $company->slug = Str::slug($company->company_name);
            }
        });
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}