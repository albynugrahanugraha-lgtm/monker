<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nama', 'username', 'email', 'password',
        'role', 'no_hp', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function technician()
    {
        return $this->hasOne(Technician::class);
    }

    public function jobsAsTeknisi()
    {
        return $this->hasMany(Job::class, 'teknisi_id');
    }

    public function jobsAsAdmin()
    {
        return $this->hasMany(Job::class, 'admin_id');
    }

    public function isSuperAdmin(): bool { return $this->role === 'superadmin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isTeknisi(): bool    { return $this->role === 'teknisi'; }

    public function getDisplayNameAttribute(): string
    {
        return $this->attributes['nama']
            ?? $this->attributes['name']
            ?? $this->attributes['username']
            ?? 'User';
    }
}