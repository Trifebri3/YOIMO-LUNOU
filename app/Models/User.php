<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'company_profile_id',
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'phone',
        'position',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Helper Role
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isManagement(): bool
    {
        return $this->role === 'management';
    }

    public function isFinance(): bool
    {
        return $this->role === 'finance';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    // Mutator Format Nomor WhatsApp Standar Fonnte (628xxx)
    public function setPhoneAttribute($value): void
    {
        if (! empty($value)) {
            $cleanNumber = preg_replace('/[^0-9]/', '', $value);
            if (str_starts_with($cleanNumber, '0')) {
                $cleanNumber = '62'.substr($cleanNumber, 1);
            } elseif (str_starts_with($cleanNumber, '8')) {
                $cleanNumber = '62'.$cleanNumber;
            }
            $this->attributes['phone'] = $cleanNumber;
        } else {
            $this->attributes['phone'] = null;
        }
    }

    // Tambahkan di dalam class User
    public function managedCompanies()
    {
        return $this->hasMany(CompanyProfile::class, 'manager_id');
    }

    public function company()
    {
        return $this->belongsTo(CompanyProfile::class, 'company_profile_id');
    }
}
