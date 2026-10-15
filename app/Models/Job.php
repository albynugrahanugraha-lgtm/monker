<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'kode_job', 'group_id',
        'judul', 'deskripsi',
        'lok_gedung', 'lokasi_ruang', 'computer_name', 'merk',
        'kategori', 'bidang', 'prioritas',
        'technician_id', 'teknisi_id', 'admin_id',
        'tanggal_tugas', 'tanggal_selesai',
        'status', 'hasil_pekerjaan', 'catatan', 'catatan_admin',
    ];

    protected $casts = [
        'tanggal_tugas'   => 'date',
        'tanggal_selesai' => 'datetime',
    ];

    // ============================================
    // RELATIONS
    // ============================================
    public function technician() { return $this->belongsTo(Technician::class); }
    public function teknisi()    { return $this->belongsTo(User::class, 'teknisi_id'); }
    public function admin()      { return $this->belongsTo(User::class, 'admin_id'); }

    public function photos()      { return $this->hasMany(JobPhoto::class); }
    public function fotoSebelum() { return $this->hasMany(JobPhoto::class)->where('jenis_foto', 'sebelum'); }
    public function fotoSesudah() { return $this->hasMany(JobPhoto::class)->where('jenis_foto', 'sesudah'); }

    public function report()    { return $this->hasOne(JobReport::class); }
    public function histories() { return $this->hasMany(JobHistory::class); }

    // ============================================
    // GROUP (Multi-Teknisi Clone)
    // ============================================
    /**
     * Ambil semua job dalam grup yang sama (termasuk dirinya sendiri).
     */
    public function groupJobs()
    {
        if (!$this->group_id) {
            return static::where('id', $this->id);
        }
        return static::where('group_id', $this->group_id);
    }

    /**
     * Apakah job ini bagian dari grup multi-teknisi?
     */
    public function isGrouped(): bool
    {
        return !empty($this->group_id);
    }

    /**
     * Label grup untuk tampilan.
     */
    public function groupLabel(): string
    {
        return $this->group_id ? '🔗 ' . $this->group_id : '';
    }

    /**
     * Generate group_id unik.
     * Format: GRP-XXXXXX (6 karakter random uppercase)
     */
    public static function generateGroupId(): string
    {
        return 'GRP-' . strtoupper(Str::random(6));
    }

    // ============================================
    // GENERATE KODE JOB
    // ============================================
    /**
     * Generate kode job biasa (single teknisi).
     */
    public static function generateKode(string $bidang = 'komputer'): string
    {
        $prefix  = $bidang === 'cctv' ? 'CCTV' : 'PC';
        $tanggal = now()->format('Ymd');

        $last = static::where('bidang', $bidang)
            ->whereDate('created_at', today())
            ->count() + 1;

        return sprintf('%s-%s-%03d', $prefix, $tanggal, $last);
    }

    /**
     * Generate kode job dengan index (untuk multi-teknisi clone).
     * Contoh: PC-20261014-001-01, PC-20261014-001-02
     */
    public static function generateKodeWithIndex(string $bidang, int $index): string
    {
        $base = static::generateKode($bidang);
        return $base . '-' . str_pad($index, 2, '0', STR_PAD_LEFT);
    }

    // ============================================
    // HELPERS BIDANG
    // ============================================
    public function isCctv(): bool
    {
        return $this->bidang === 'cctv';
    }

    public function bidangLabel(): string
    {
        return $this->bidang === 'cctv' ? 'CCTV' : 'Komputer';
    }

    public function bidangBadgeClass(): string
    {
        return $this->bidang === 'cctv' ? 'bg-info text-dark' : 'bg-primary';
    }

    public function bidangIcon(): string
    {
        return $this->bidang === 'cctv' ? 'bi-camera-video' : 'bi-pc-display';
    }

    // ============================================
    // HELPERS STATUS
    // ============================================
    public function bisaDiselesaikan(): bool
    {
        return in_array($this->status, ['DITUGASKAN', 'DIKERJAKAN']);
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'DITUGASKAN' => 'bg-secondary',
            'DIKERJAKAN' => 'bg-warning text-dark',
            'SELESAI'    => 'bg-success',
            default      => 'bg-light text-dark',
        };
    }

    public function statusLabel(): string
    {
        return str_replace('_', ' ', $this->status);
    }

    // ============================================
    // PENYELESAI — siapa yang menyelesaikan
    // ============================================
    public function penyelesai()
    {
        if ($this->teknisi_id && $this->teknisi) {
            return $this->teknisi;
        }

        $history = $this->histories()
            ->where('status_baru', 'SELESAI')
            ->latest()
            ->first();

        return $history?->user;
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeBidang($q, string $bidang)
    {
        return $q->where('bidang', $bidang);
    }

    public function scopeForTeknisi($q, User $user)
    {
        return $q->where('bidang', $user->bidang())
            ->where(function ($sub) use ($user) {
                $sub->whereNull('teknisi_id')
                    ->orWhere('teknisi_id', $user->id);
            });
    }
}