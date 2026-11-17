<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attendance extends Model
{
    protected $table = 'attendances';
    protected $fillable = ['user_id', 'tanggal', 'tipe', 'jam', 'foto'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function rekapQuery()
    {
        return DB::table('attendances')
            ->select(
                'user_id',
                'tanggal',
                DB::raw("MIN(CASE WHEN tipe = 'masuk' THEN jam END) as jam_masuk"),
                DB::raw("MAX(CASE WHEN tipe = 'pulang' THEN jam END) as jam_pulang")
            )
            ->groupBy('user_id', 'tanggal');
    }

    public static function hitungStatus(?string $jamMasuk): string
    {
        if (!$jamMasuk) return 'alpha';
        $batas = '08:00:00';
        return $jamMasuk <= $batas ? 'hadir' : 'terlambat';
    }

    public static function hitungDurasi(?string $jamMasuk, ?string $jamPulang): ?int
    {
        if (!$jamMasuk || !$jamPulang) return null;
        $masuk  = strtotime($jamMasuk);
        $pulang = strtotime($jamPulang);
        if ($pulang <= $masuk) return null;
        return (int) round(($pulang - $masuk) / 60);
    }

    // 🆕 Hitung Keterangan Jam Kerja (Flexi Time)
    public static function hitungKeterangan(?string $jamMasuk, ?string $jamPulang): array
    {
        if (!$jamMasuk && !$jamPulang) {
            return ['status' => 'alpha', 'label' => 'Tidak Absen', 'warna' => 'danger'];
        }

        if (!$jamMasuk || !$jamPulang) {
            return ['status' => 'tidak_lengkap', 'label' => 'Absen Tidak Lengkap', 'warna' => 'warning text-dark'];
        }

        $masuk  = strtotime($jamMasuk);
        $pulang = strtotime($jamPulang);
        $durasiMenit = ($pulang - $masuk) / 60;

        $standarMenit = 9 * 60; // 9 Jam

        if ($durasiMenit < $standarMenit) {
            $kurang = $standarMenit - $durasiMenit;
            $jamKurang = floor($kurang / 60);
            $menitKurang = $kurang % 60;
            return [
                'status' => 'kurang', 
                'label' => "Kurang {$jamKurang}j {$menitKurang}m", 
                'warna' => 'danger'
            ];
        }

        return ['status' => 'cukup', 'label' => 'Jam Cukup', 'warna' => 'success'];
    }
}