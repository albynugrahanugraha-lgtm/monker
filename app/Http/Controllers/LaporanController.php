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

    private function getJobs(Request $request, $user)
    {
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        $query = Job::query()
            ->whereMonth('tanggal_tugas', $bulan)
            ->whereYear('tanggal_tugas', $tahun);

        if ($user->isTeknisi()) {
            $query->where('teknisi_id', $user->id);
        }

        $jobs = $query->with(['teknisi', 'fotoSebelum', 'fotoSesudah'])
            ->orderBy('tanggal_tugas')
            ->get();

        return [$jobs, $bulan, $tahun];
    }

    private function getStats($jobs)
    {
        return [
            'total'      => $jobs->count(),
            'ditugaskan' => $jobs->where('status', 'DITUGASKAN')->count(),
            'dikerjakan' => $jobs->where('status', 'DIKERJAKAN')->count(),
            'selesai'    => $jobs->where('status', 'SELESAI')->count(),
        ];
    }

    private function getRekapTeknisi($user, $bulan, $tahun)
    {
        if ($user->isTeknisi()) {
            return collect();
        }

        return Job::select(
                'teknisi_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "SELESAI" THEN 1 ELSE 0 END) as selesai')
            )
            ->whereMonth('tanggal_tugas', $bulan)
            ->whereYear('tanggal_tugas', $tahun)
            ->groupBy('teknisi_id')
            ->with('teknisi')
            ->get();
    }

    /**
     * Data grafik per hari dalam bulan terpilih
     */
    private function getChartHarian($jobs, $bulan, $tahun)
    {
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        $labels = [];
        $data = [];
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

    /**
     * Data grafik 12 bulan terakhir
     */
    private function getChartBulanan($user)
    {
        $chartData = ['labels' => [], 'data' => [], 'selesai' => []];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $bulan = $date->month;
            $tahun = $date->year;

            $chartData['labels'][] = $date->format('M Y');

            $q1 = Job::whereMonth('tanggal_tugas', $bulan)->whereYear('tanggal_tugas', $tahun);
            $q2 = Job::whereMonth('tanggal_tugas', $bulan)->whereYear('tanggal_tugas', $tahun)->where('status', 'SELESAI');

            if ($user->isTeknisi()) {
                $q1->where('teknisi_id', $user->id);
                $q2->where('teknisi_id', $user->id);
            }

            $chartData['data'][] = $q1->count();
            $chartData['selesai'][] = $q2->count();
        }

        return $chartData;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun);
        $daftarBulan = $this->getDaftarBulan();
        $chartData = $this->getChartBulanan($user);
        $chartHarian = $this->getChartHarian($jobs, $bulan, $tahun);

        return view('laporan.index', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun', 'daftarBulan', 'chartData', 'chartHarian'
        ));
    }

    public function cetak(Request $request)
    {
        $user = auth()->user();
        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun);
        $daftarBulan = $this->getDaftarBulan();
        $chartHarian = $this->getChartHarian($jobs, $bulan, $tahun);

        return view('laporan.cetak', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun', 'daftarBulan', 'user', 'chartHarian'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $bulan, $tahun);
        $daftarBulan = $this->getDaftarBulan();
        $chartHarian = $this->getChartHarian($jobs, $bulan, $tahun);

        $namaBulan = $daftarBulan[$bulan];
        $filename = "Laporan-Pekerjaan-{$namaBulan}-{$tahun}.xls";

        return response()
            ->view('laporan.excel', compact(
                'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun', 'daftarBulan', 'user', 'chartHarian'
            ))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}