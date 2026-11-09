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

    // Mengambil jam masuk paling pagi & pulang paling malam
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

    // Hitung status: Terlambat jika masuk > 08:00
    public static function hitungStatus(?string $jamMasuk): string
    {
        if (!$jamMasuk) return 'alpha';
        $batas = '08:00:00';
        return $jamMasuk <= $batas ? 'hadir' : 'terlambat';
    }

    // Hitung durasi kerja (dalam menit)
    public static function hitungDurasi(?string $jamMasuk, ?string $jamPulang): ?int
    {
        if (!$jamMasuk || !$jamPulang) return null;
        $masuk  = strtotime($jamMasuk);
        $pulang = strtotime($jamPulang);
        if ($pulang <= $masuk) return null;
        return (int) round(($pulang - $masuk) / 60);
    }
}