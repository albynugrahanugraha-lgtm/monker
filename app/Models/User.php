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
        'password'          => 'hashed',
    ];

    // ============================================
    // RELATIONS
    // ============================================
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

    // ============================================
    // ROLE HELPERS
    // ============================================
    public function isSuperAdmin(): bool  { return $this->role === 'superadmin'; }
    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isTeknisi(): bool     { return $this->role === 'teknisi'; }
    public function isTeknisiCctv(): bool { return $this->role === 'teknisi_cctv'; }

    /** Semua role teknisi (komputer + cctv) */
    public function isAnyTeknisi(): bool
    {
        return in_array($this->role, ['teknisi', 'teknisi_cctv']);
    }

    /** Bidang yang dia kerjakan */
    public function bidang(): string
    {
        return $this->isTeknisiCctv() ? 'cctv' : 'komputer';
    }

    /** Label bidang untuk tampilan */
    public function bidangLabel(): string
    {
        return $this->isTeknisiCctv() ? 'CCTV' : 'Komputer';
    }

    // ============================================
    // ACCESSOR
    // ============================================
    public function getDisplayNameAttribute(): string
    {
        $nama = $this->attributes['nama']
            ?? $this->attributes['name']
            ?? $this->attributes['username']
            ?? 'User';

        // Tambah label (CCTV) kalau teknisi_cctv
        if ($this->isTeknisiCctv()) {
            return $nama . ' (CCTV)';
        }

        return $nama;
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeTeknisiKomputer($q)
    {
        return $q->where('role', 'teknisi');
    }

    public function scopeTeknisiCctv($q)
    {
        return $q->where('role', 'teknisi_cctv');
    }

    public function scopeTeknisiAktif($q)
    {
        return $q->whereIn('role', ['teknisi', 'teknisi_cctv'])
                 ->where('status', 'aktif');
    }
}