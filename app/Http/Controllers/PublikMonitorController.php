<?php

namespace App\Http\Controllers;

use App\Models\{Job, User};
use Illuminate\Http\Request;

class PublikMonitorController extends Controller
{
    public function index()
    {
        return view('publik.monitor');
    }

    public function data(Request $request)
    {
        // ===== 3 KOTAK DASHBOARD =====
        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ===== TABEL PEKERJAAN HARI INI =====
        $kerjaanHariIni = Job::with('teknisi')
            ->whereDate('tanggal_tugas', today())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($j) {
                return [
                    'id' => $j->id,
                    'kode_job' => $j->kode_job,
                    'judul' => $j->judul,
                    'lok_gedung' => $j->lok_gedung,
                    'lokasi_ruang' => $j->lokasi_ruang,
                    'status' => $j->status,
                    'teknisi' => $j->teknisi ? $j->teknisi->display_name : null,
                    'belum_diambil' => is_null($j->teknisi_id) && $j->status === 'DITUGASKAN',
                ];
            });

        // ===== TABEL PEKERJAAN KEMARIN =====
        $kerjaanKemarin = Job::with('teknisi')
            ->whereDate('tanggal_tugas', today()->subDay())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($j) {
                return [
                    'id' => $j->id,
                    'kode_job' => $j->kode_job,
                    'judul' => $j->judul,
                    'lok_gedung' => $j->lok_gedung,
                    'lokasi_ruang' => $j->lokasi_ruang,
                    'status' => $j->status,
                    'teknisi' => $j->teknisi ? $j->teknisi->display_name : null,
                ];
            });

        // ===== RATING / TOP TEKNISI HARI INI =====
        $ratingHariIni = User::where('role', 'teknisi')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) {
                $total = Job::where('teknisi_id', $teknisi->id)
                    ->whereDate('tanggal_tugas', today())
                    ->count();

                $selesai = Job::where('teknisi_id', $teknisi->id)
                    ->whereDate('tanggal_tugas', today())
                    ->where('status', 'SELESAI')
                    ->count();

                $belum = $total - $selesai;
                $rating = $total > 0 ? round(($selesai / $total) * 100) : 0;

                if ($rating >= 80) {
                    $predikat = '🥇 Top';
                    $warna = 'success';
                } elseif ($rating >= 50) {
                    $predikat = '🥈 Sedang';
                    $warna = 'warning';
                } else {
                    $predikat = '🥉 Rendah';
                    $warna = 'danger';
                }

                return [
                    'nama' => $teknisi->display_name,
                    'total' => $total,
                    'selesai' => $selesai,
                    'belum' => $belum,
                    'rating' => $rating,
                    'predikat' => $predikat,
                    'warna' => $warna,
                ];
            })
            ->filter(fn($t) => $t['total'] > 0)
            ->sortByDesc('selesai')
            ->values();

        // ===== REKAP TEKNISI OVERALL (untuk grafik) =====
        $rekapTeknisi = User::where('role', 'teknisi')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) {
                $selesai = Job::where('teknisi_id', $teknisi->id)
                    ->where('status', 'SELESAI')
                    ->count();

                $belum = Job::where('teknisi_id', $teknisi->id)
                    ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
                    ->count();

                $total = $selesai + $belum;
                $rating = $total > 0 ? round(($selesai / $total) * 100) : 0;

                if ($rating >= 80) {
                    $warna = 'success';
                } elseif ($rating >= 50) {
                    $warna = 'warning';
                } else {
                    $warna = 'danger';
                }

                return [
                    'nama' => $teknisi->display_name,
                    'selesai' => $selesai,
                    'belum' => $belum,
                    'total' => $total,
                    'rating' => $rating,
                    'warna' => $warna,
                ];
            })
            ->sortByDesc('selesai')
            ->values();

        return response()->json([
            'hariIni' => $hariIni,
            'kerjaanHariIni' => $kerjaanHariIni,
            'kerjaanKemarin' => $kerjaanKemarin,
            'ratingHariIni' => $ratingHariIni,
            'rekapTeknisi' => $rekapTeknisi,
            'timestamp' => now()->format('d M Y, H:i:s'),
            'tanggalHariIni' => now()->translatedFormat('d F Y'),
            'tanggalKemarin' => now()->subDay()->translatedFormat('d F Y'),
        ]);
    }
}