<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'kode_job',
        'judul', 'deskripsi',
        'lok_gedung', 'lokasi_ruang', 'computer_name', 'merk',
        'kategori', 'prioritas',
        'technician_id', 'teknisi_id', 'admin_id',
        'tanggal_tugas', 'tanggal_selesai',
        'status', 'hasil_pekerjaan', 'catatan', 'catatan_admin',
    ];

    protected $casts = [
        'tanggal_tugas'   => 'date',
        'tanggal_selesai' => 'datetime',
    ];

    public function technician() { return $this->belongsTo(Technician::class); }
    public function teknisi()    { return $this->belongsTo(User::class, 'teknisi_id'); }
    public function admin()      { return $this->belongsTo(User::class, 'admin_id'); }

    public function photos()      { return $this->hasMany(JobPhoto::class); }
    public function fotoSebelum() { return $this->hasMany(JobPhoto::class)->where('jenis_foto', 'sebelum'); }
    public function fotoSesudah() { return $this->hasMany(JobPhoto::class)->where('jenis_foto', 'sesudah'); }

    public function report()    { return $this->hasOne(JobReport::class); }
    public function histories() { return $this->hasMany(JobHistory::class); }

    public static function generateKode(): string
    {
        $last = self::orderByDesc('id')->first();
        $next = $last ? $last->id + 1 : 1;
        return 'JOB-' . now()->format('Ym') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}