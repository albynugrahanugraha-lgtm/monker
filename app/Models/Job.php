<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'kode_job',
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
    // GENERATE KODE (per bidang)
    // ============================================
    public static function generateKode(string $bidang = 'komputer'): string
    {
        $prefix  = $bidang === 'cctv' ? 'CCTV' : 'PC';
        $tanggal = now()->format('Ymd');

        $last = static::where('bidang', $bidang)
            ->whereDate('created_at', today())
            ->count() + 1;

        return sprintf('%s-%s-%03d', $prefix, $tanggal, $last);
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