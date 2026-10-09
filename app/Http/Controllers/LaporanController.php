<?php

namespace App\Http\Controllers;

use App\Models\{Job, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    private function getDaftarBulan()
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    // ============================================================
    // GET JOBS — dengan filter bidang
    // ============================================================
    private function getJobs(Request $request, $user)
    {
        $bulan  = $request->input('bulan', now()->month);
        $tahun  = $request->input('tahun', now()->year);
        $bidang = $request->input('bidang');   // untuk admin/superadmin

        $query = Job::query()
            ->whereMonth('tanggal_tugas', $bulan)
            ->whereYear('tanggal_tugas', $tahun);

        // Teknisi KOMPUTER → auto filter bidang + teknisi_id
        if ($user->isTeknisi()) {
            $query->where('bidang', 'komputer')
                  ->where('teknisi_id', $user->id);
        }
        // Teknisi CCTV → auto filter bidang + teknisi_id
        elseif ($user->isTeknisiCctv()) {
            $query->where('bidang', 'cctv')
                  ->where('teknisi_id', $user->id);
        }
        // Admin/Superadmin → kalau pilih bidang, filter
        elseif ($bidang && in_array($bidang, ['komputer', 'cctv'])) {
            $query->where('bidang', $bidang);
        }

        $jobs = $query->with(['teknisi', 'fotoSebelum', 'fotoSesudah'])
            ->orderBy('tanggal_tugas')
            ->get();

        return [$jobs, $bulan, $tahun];
    }

    // ============================================================
    // STATISTIK
    // ============================================================
    private function getStats($jobs)
    {
        return [
            'total'      => $jobs->count(),
            'ditugaskan' => $jobs->where('status', 'DITUGASKAN')->count(),
            'dikerjakan' => $jobs->where('status', 'DIKERJAKAN')->count(),
            'selesai'    => $jobs->where('status', 'SELESAI')->count(),
        ];
    }

    // ============================================================
    // REKAP PER TEKNISI — untuk admin/superadmin saja
    // ============================================================
    private function getRekapTeknisi($user, $bulan, $tahun, $bidangFilter = null)
    {
        // Teknisi tidak perlu rekap (cuma lihat tugas sendiri)
        if ($user->isAnyTeknisi()) {
            return collect();
        }

        $query = Job::select(
                'teknisi_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "SELESAI" THEN 1 ELSE 0 END) as selesai')
            )
            ->whereMonth('tanggal_tugas', $bulan)
            ->whereYear('tanggal_tugas', $tahun);

        // Filter bidang kalau admin pilih bidang tertentu
        if ($bidangFilter && in_array($bidangFilter, ['komputer', 'cctv'])) {
            $query->where('bidang', $bidangFilter);
        }

        return $query->groupBy('teknisi_id')
            ->with('teknisi')
            ->get();
    }

    // ============================================================
    // GRAFIK PER HARI
    // ============================================================
    private function getChartHarian($jobs, $bulan, $tahun)
    {
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $labels  = [];
        $data    = [];
        $selesai = [];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $labels[] = $d;

            $data[] = $jobs->filter(function ($j) use ($d) {
                return $j->tanggal_tugas && $j->tanggal_tugas->day == $d;
            })->count();

            $selesai[] = $jobs->filter(function ($j) use ($d) {
                return $j->tanggal_tugas
                    && $j->tanggal_tugas->day == $d
                    && $j->status == 'SELESAI';
            })->count();
        }

        return [
            'labels'  => $labels,
            'data'    => $data,
            'selesai' => $selesai,
        ];
    }

    // ============================================================
    // GRAFIK 12 BULAN TERAKHIR
    // ============================================================
    private function getChartBulanan($user, $bidangFilter = null)
    {
        $chartData = ['labels' => [], 'data' => [], 'selesai' => []];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $bulan = $date->month;
            $tahun = $date->year;

            $chartData['labels'][] = $date->format('M Y');

            $q1 = Job::whereMonth('tanggal_tugas', $bulan)->whereYear('tanggal_tugas', $tahun);
            $q2 = Job::whereMonth('tanggal_tugas', $bulan)->whereYear('tanggal_tugas', $tahun)->where('status', 'SELESAI');

            // Teknisi → filter teknisi_id + bidang
            if ($user->isAnyTeknisi()) {
                $q1->where('teknisi_id', $user->id)->where('bidang', $user->bidang());
                $q2->where('teknisi_id', $user->id)->where('bidang', $user->bidang());
            }
            // Admin pilih bidang
            elseif ($bidangFilter && in_array($bidangFilter, ['komputer', 'cctv'])) {
                $q1->where('bidang', $bidangFilter);
                $q2->where('bidang', $bidangFilter);
            }

            $chartData['data'][]    = $q1->count();
            $chartData['selesai'][] = $q2->count();
        }

        return $chartData;
    }

    // ============================================================
    // INDEX — halaman laporan
    // ============================================================
    public function index(Request $request)
    {
        $user         = auth()->user();
        $bidangFilter = $request->input('bidang');

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun, $bidangFilter);
        $daftarBulan  = $this->getDaftarBulan();
        $chartData    = $this->getChartBulanan($user, $bidangFilter);
        $chartHarian  = $this->getChartHarian($jobs, $bulan, $tahun);

        return view('laporan.index', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun',
            'daftarBulan', 'chartData', 'chartHarian', 'bidangFilter'
        ));
    }

    // ============================================================
    // CETAK PDF
    // ============================================================
    public function cetak(Request $request)
    {
        $user = auth()->user();

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun);
        $daftarBulan  = $this->getDaftarBulan();
        $chartHarian  = $this->getChartHarian($jobs, $bulan, $tahun);

        return view('laporan.cetak', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun',
            'daftarBulan', 'user', 'chartHarian'
        ));
    }

    // ============================================================
    // EXPORT EXCEL
    // ============================================================
    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun);
        $daftarBulan  = $this->getDaftarBulan();
        $chartHarian  = $this->getChartHarian($jobs, $bulan, $tahun);

        $namaBulan = $daftarBulan[$bulan];
        $filename  = "Laporan-Pekerjaan-{$namaBulan}-{$tahun}.xls";

        return response()
            ->view('laporan.excel', compact(
                'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun',
                'daftarBulan', 'user', 'chartHarian'
            ))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}