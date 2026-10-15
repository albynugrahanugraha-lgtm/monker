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
        // ============================================================
        // STATISTIK GABUNGAN (header)
        // ============================================================
        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ============================================================
        // STATISTIK PER BIDANG (hari ini)
        // ============================================================
        $statsKomputer = [
            'total'   => Job::where('bidang', 'komputer')->whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::where('bidang', 'komputer')->whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::where('bidang', 'komputer')->whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        $statsCctv = [
            'total'   => Job::where('bidang', 'cctv')->whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::where('bidang', 'cctv')->whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::where('bidang', 'cctv')->whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ============================================================
        // TABEL HARI INI & KEMARIN (split per bidang)
        // ============================================================
        $kerjaanHariIniKomputer = $this->mapJobs(
            Job::with('teknisi')
                ->where('bidang', 'komputer')
                ->whereDate('tanggal_tugas', today())
                ->orderBy('created_at', 'desc')
                ->get(),
            true
        );

        $kerjaanHariIniCctv = $this->mapJobs(
            Job::with('teknisi')
                ->where('bidang', 'cctv')
                ->whereDate('tanggal_tugas', today())
                ->orderBy('created_at', 'desc')
                ->get(),
            true
        );

        $kerjaanKemarinKomputer = $this->mapJobs(
            Job::with('teknisi')
                ->where('bidang', 'komputer')
                ->whereDate('tanggal_tugas', today()->subDay())
                ->orderBy('created_at', 'desc')
                ->get()
        );

        $kerjaanKemarinCctv = $this->mapJobs(
            Job::with('teknisi')
                ->where('bidang', 'cctv')
                ->whereDate('tanggal_tugas', today()->subDay())
                ->orderBy('created_at', 'desc')
                ->get()
        );

        // ============================================================
        // TOP TEKNISI HARI INI (split per bidang)
        // ============================================================
        $ratingHariIniKomputer = $this->getRatingPerBidang('komputer');
        $ratingHariIniCctv     = $this->getRatingPerBidang('cctv');

        // ============================================================
        // 🆕 REKAP TEKNISI BULAN INI (untuk chart top teknisi)
        // ============================================================
        $rekapTeknisiKomputer = $this->getRekapPerBidang('komputer');
        $rekapTeknisiCctv     = $this->getRekapPerBidang('cctv');

        return response()->json([
            'hariIni'       => $hariIni,
            'statsKomputer' => $statsKomputer,
            'statsCctv'     => $statsCctv,

            'kerjaanHariIniKomputer' => $kerjaanHariIniKomputer,
            'kerjaanHariIniCctv'     => $kerjaanHariIniCctv,
            'kerjaanKemarinKomputer' => $kerjaanKemarinKomputer,
            'kerjaanKemarinCctv'     => $kerjaanKemarinCctv,

            'ratingHariIniKomputer' => $ratingHariIniKomputer,
            'ratingHariIniCctv'     => $ratingHariIniCctv,

            'rekapTeknisiKomputer' => $rekapTeknisiKomputer,
            'rekapTeknisiCctv'     => $rekapTeknisiCctv,

            'timestamp'      => now()->format('d M Y, H:i:s'),
            'tanggalHariIni' => now()->translatedFormat('d F Y'),
            'tanggalKemarin' => now()->subDay()->translatedFormat('d F Y'),

            // 🆕 Periode rekap untuk label chart
            'periodeRekap'   => now()->translatedFormat('F Y'),
        ]);
    }

    /**
     * Map job collection ke array untuk JSON
     */
    private function mapJobs($jobs, bool $withBelumDiambil = false): array
    {
        return $jobs->map(function ($j) use ($withBelumDiambil) {
            $data = [
                'id'           => $j->id,
                'kode_job'     => $j->kode_job,
                'judul'        => $j->judul,
                'bidang'       => $j->bidang,
                'lok_gedung'   => $j->lok_gedung,
                'lokasi_ruang' => $j->lokasi_ruang,
                'status'       => $j->status,
                'teknisi'      => $j->teknisi ? $j->teknisi->display_name : null,
            ];

            if ($withBelumDiambil) {
                $data['belum_diambil'] = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
            }

            return $data;
        })->toArray();
    }

    /**
     * Rating Top Teknisi HARI INI berdasarkan bidang
     */
    private function getRatingPerBidang(string $bidang): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
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

                $belum  = $total - $selesai;
                $rating = $total > 0 ? round(($selesai / $total) * 100) : 0;

                return [
                    'nama'    => $teknisi->display_name,
                    'total'   => $total,
                    'selesai' => $selesai,
                    'belum'   => $belum,
                    'rating'  => $rating,
                ];
            })
            ->filter(fn($t) => $t['total'] > 0)
            ->sortByDesc('selesai')
            ->values()
            ->toArray();
    }

    /**
     * 🆕 Rekap BULAN INI per bidang (untuk chart top teknisi)
     */
    private function getRekapPerBidang(string $bidang): array
    {
        $role  = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
        $bulan = now()->month;
        $tahun = now()->year;

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) use ($bulan, $tahun) {
                // Selesai bulan ini
                $selesai = Job::where('teknisi_id', $teknisi->id)
                    ->where('status', 'SELESAI')
                    ->whereMonth('tanggal_tugas', $bulan)
                    ->whereYear('tanggal_tugas', $tahun)
                    ->count();

                // Belum selesai (ditugaskan + dikerjakan) bulan ini
                $belum = Job::where('teknisi_id', $teknisi->id)
                    ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
                    ->whereMonth('tanggal_tugas', $bulan)
                    ->whereYear('tanggal_tugas', $tahun)
                    ->count();

                return [
                    'nama'    => $teknisi->display_name,
                    'selesai' => $selesai,
                    'belum'   => $belum,
                    'total'   => $selesai + $belum,
                ];
            })
            ->filter(fn($t) => $t['total'] > 0)   // hanya yang ada tugas bulan ini
            ->sortByDesc('selesai')
            ->values()
            ->toArray();
    }
}