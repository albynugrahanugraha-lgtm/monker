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
        // PERIODE untuk rekap top teknisi (dipisah komputer & cctv)
        // ============================================================
        $periodeKomputer = $request->input('periode_komputer', 'bulanan');
        $periodeCctv     = $request->input('periode_cctv', 'bulanan');

        $validPeriode = ['harian', 'mingguan', 'bulanan', 'semua'];

        if (!in_array($periodeKomputer, $validPeriode)) {
            $periodeKomputer = 'bulanan';
        }
        if (!in_array($periodeCctv, $validPeriode)) {
            $periodeCctv = 'bulanan';
        }

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
        // TABEL HARI INI & KEMARIN
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
        // TOP TEKNISI dengan FILTER PERIODE (dipisah per bidang)
        // ============================================================
        $rekapTeknisiKomputer = $this->getRekapPerBidangByPeriode('komputer', $periodeKomputer);
        $rekapTeknisiCctv     = $this->getRekapPerBidangByPeriode('cctv', $periodeCctv);

        return response()->json([
            'hariIni'       => $hariIni,
            'statsKomputer' => $statsKomputer,
            'statsCctv'     => $statsCctv,

            'kerjaanHariIniKomputer' => $kerjaanHariIniKomputer,
            'kerjaanHariIniCctv'     => $kerjaanHariIniCctv,
            'kerjaanKemarinKomputer' => $kerjaanKemarinKomputer,
            'kerjaanKemarinCctv'     => $kerjaanKemarinCctv,

            'rekapTeknisiKomputer' => $rekapTeknisiKomputer,
            'rekapTeknisiCctv'     => $rekapTeknisiCctv,

            'periodeKomputer' => $periodeKomputer,
            'periodeCctv'     => $periodeCctv,
            'labelKomputer'   => $this->getPeriodeLabel($periodeKomputer),
            'labelCctv'       => $this->getPeriodeLabel($periodeCctv),

            'timestamp'      => now()->format('d M Y, H:i:s'),
            'tanggalHariIni' => now()->translatedFormat('d F Y'),
            'tanggalKemarin' => now()->subDay()->translatedFormat('d F Y'),
        ]);
    }

    /**
     * Label periode untuk ditampilkan di chart
     */
    private function getPeriodeLabel(string $periode): string
    {
        return match($periode) {
            'harian'   => 'Hari Ini (' . now()->translatedFormat('d M Y') . ')',
            'mingguan' => 'Minggu Ini (' . now()->startOfWeek()->translatedFormat('d M') . ' - ' . now()->endOfWeek()->translatedFormat('d M Y') . ')',
            'bulanan'  => 'Bulan Ini (' . now()->translatedFormat('F Y') . ')',
            'semua'    => 'Sepanjang Masa',
            default    => 'Bulan Ini',
        };
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
     * Rekap per bidang dengan filter periode
     */
    private function getRekapPerBidangByPeriode(string $bidang, string $periode): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) use ($periode) {
                // Query dasar — filter per bidang teknisi
                $querySelesai = Job::where('teknisi_id', $teknisi->id)
                    ->where('status', 'SELESAI');

                $queryBelum = Job::where('teknisi_id', $teknisi->id)
                    ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);

                // Apply filter periode
                switch ($periode) {
                    case 'harian':
                        $querySelesai->whereDate('tanggal_tugas', today());
                        $queryBelum->whereDate('tanggal_tugas', today());
                        break;

                    case 'mingguan':
                        $querySelesai->whereBetween('tanggal_tugas', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        $queryBelum->whereBetween('tanggal_tugas', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                        break;

                    case 'bulanan':
                        $querySelesai->whereMonth('tanggal_tugas', now()->month)
                                     ->whereYear('tanggal_tugas', now()->year);
                        $queryBelum->whereMonth('tanggal_tugas', now()->month)
                                   ->whereYear('tanggal_tugas', now()->year);
                        break;

                    case 'semua':
                    default:
                        // Tidak ada filter → semua waktu
                        break;
                }

                $selesai = $querySelesai->count();
                $belum   = $queryBelum->count();

                return [
                    'nama'    => $teknisi->display_name,
                    'selesai' => $selesai,
                    'belum'   => $belum,
                    'total'   => $selesai + $belum,
                ];
            })
            ->filter(fn($t) => $t['total'] > 0)
            ->sortByDesc('selesai')
            ->take(10)  // 🆕 Top 10 saja
            ->values()
            ->toArray();
    }
}