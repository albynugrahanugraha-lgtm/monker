<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\{Job, User, JobPhoto};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        $tahun  = $request->input('tahun', now()->year);
        $bulan  = $request->input('bulan');
        $bidang = $request->input('bidang');

        $query = Job::query()
            ->whereYear('tanggal_tugas', $tahun);

        if ($bulan && $bulan !== 'semua') {
            $query->whereMonth('tanggal_tugas', $bulan);
        }

        if ($user->isTeknisi()) {
            $query->where('bidang', 'komputer')->where('teknisi_id', $user->id);
        } elseif ($user->isTeknisiCctv()) {
            $query->where('bidang', 'cctv')->where('teknisi_id', $user->id);
        } elseif ($bidang && in_array($bidang, ['komputer', 'cctv'])) {
            $query->where('bidang', $bidang);
        }

        // URUT DESC (tanggal terbaru di atas)
        $jobs = $query->with(['teknisi', 'admin', 'fotoSebelum', 'fotoSesudah'])
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
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

    private function getRekapTeknisi($user, $tahun, $bulan = null, $bidangFilter = null)
    {
        if ($user->isAnyTeknisi()) {
            return collect();
        }

        $query = Job::select(
                'teknisi_id',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "SELESAI" THEN 1 ELSE 0 END) as selesai'),
                DB::raw('SUM(CASE WHEN status = "DITUGASKAN" THEN 1 ELSE 0 END) as pending')
            )
            ->whereYear('tanggal_tugas', $tahun);

        if ($bulan && $bulan !== 'semua') {
            $query->whereMonth('tanggal_tugas', $bulan);
        }

        if ($bidangFilter && in_array($bidangFilter, ['komputer', 'cctv'])) {
            $query->where('bidang', $bidangFilter);
            if ($bidangFilter === 'komputer') {
                $validIds = User::where('role', 'teknisi')->pluck('id')->toArray();
                $query->whereIn('teknisi_id', $validIds);
            } elseif ($bidangFilter === 'cctv') {
                $validIds = User::where('role', 'teknisi_cctv')->pluck('id')->toArray();
                $query->whereIn('teknisi_id', $validIds);
            }
        }

        return $query->groupBy('teknisi_id')
            ->with('teknisi')
            ->orderByDesc('selesai')
            ->get();
    }

    private function getChartTahunan($user, $tahun, $bidangFilter = null)
    {
        $labels  = [];
        $data    = [];
        $selesai = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = substr($this->getDaftarBulan()[$m], 0, 3);

            $q1 = Job::whereMonth('tanggal_tugas', $m)->whereYear('tanggal_tugas', $tahun);
            $q2 = Job::whereMonth('tanggal_tugas', $m)->whereYear('tanggal_tugas', $tahun)->where('status', 'SELESAI');

            if ($user->isAnyTeknisi()) {
                $q1->where('teknisi_id', $user->id)->where('bidang', $user->bidang());
                $q2->where('teknisi_id', $user->id)->where('bidang', $user->bidang());
            } elseif ($bidangFilter && in_array($bidangFilter, ['komputer', 'cctv'])) {
                $q1->where('bidang', $bidangFilter);
                $q2->where('bidang', $bidangFilter);
            }

            $data[]    = $q1->count();
            $selesai[] = $q2->count();
        }

        return [
            'labels'  => $labels,
            'data'    => $data,
            'selesai' => $selesai,
        ];
    }

    private function getChartHarian($jobs, $bulan, $tahun)
    {
        if (!$bulan || $bulan === 'semua') {
            $bulan = now()->month;
        }

        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $labels = []; $data = []; $selesai = [];

        for ($d = 1; $d <= $jumlahHari; $d++) {
            $labels[] = $d;

            $data[] = $jobs->filter(fn($j) => $j->tanggal_tugas && $j->tanggal_tugas->day == $d)->count();
            $selesai[] = $jobs->filter(fn($j) => $j->tanggal_tugas && $j->tanggal_tugas->day == $d && $j->status == 'SELESAI')->count();
        }

        return ['labels' => $labels, 'data' => $data, 'selesai' => $selesai];
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $tahun, $bulan, $request->input('bidang'));
        $daftarBulan  = $this->getDaftarBulan();
        $chartTahunan = $this->getChartTahunan($user, $tahun, $request->input('bidang'));
        $chartHarian  = $this->getChartHarian($jobs, $bulan, $tahun);

        $tahunList = Job::selectRaw('YEAR(tanggal_tugas) as tahun')
            ->distinct()
            ->whereNotNull('tanggal_tugas')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [now()->year];
        }

        return view('laporan.index', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun',
            'daftarBulan', 'chartTahunan', 'chartHarian', 'tahunList'
        ));
    }

    public function cetak(Request $request)
    {
        $user = auth()->user();

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $tahun, $bulan, $request->input('bidang'));
        $daftarBulan  = $this->getDaftarBulan();
        $chartTahunan = $this->getChartTahunan($user, $tahun, $request->input('bidang'));
        $chartHarian  = $this->getChartHarian($jobs, $bulan, $tahun);

        return view('laporan.cetak', compact(
            'jobs', 'stats', 'rekapTeknisi', 'bulan', 'tahun',
            'daftarBulan', 'user', 'chartTahunan', 'chartHarian'
        ));
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();

        [$jobs, $bulan, $tahun] = $this->getJobs($request, $user);
        $stats        = $this->getStats($jobs);
        $rekapTeknisi = $this->getRekapTeknisi($user, $tahun, $bulan, $request->input('bidang'));
        $daftarBulan  = $this->getDaftarBulan();
        $chartTahunan = $this->getChartTahunan($user, $tahun, $request->input('bidang'));

        $namaFile = "Laporan-Pekerjaan-{$tahun}";
        if ($bulan && $bulan !== 'semua') {
            $namaFile .= '-' . ($daftarBulan[$bulan] ?? $bulan);
        }
        if ($request->input('bidang')) {
            $namaFile .= '-' . ucfirst($request->input('bidang'));
        }
        if ($user->isAnyTeknisi()) {
            $namaFile .= '-' . $user->username;
        }
        $namaFile .= '-' . date('YmdHis') . '.xlsx';

        return Excel::download(
            new LaporanExport(
                $jobs,
                $stats,
                $rekapTeknisi,
                $chartTahunan,
                $tahun,
                $bulan,
                $request->input('bidang'),
                $user,
                $daftarBulan
            ),
            $namaFile
        );
    }

    /**
     * Hapus foto dari halaman laporan
     */
    public function hapusFoto(JobPhoto $photo)
    {
        $user = auth()->user();
        $job  = $photo->job;

        $bolehHapus = false;

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $bolehHapus = true;
        } elseif ($user->isAnyTeknisi() && $job->teknisi_id === $user->id) {
            $bolehHapus = true;
        }

        if (!$bolehHapus) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak menghapus foto ini.',
            ], 403);
        }

        $path = public_path($photo->nama_file);
        if (file_exists($path)) {
            @unlink($path);
        }

        $fotoId = $photo->id;
        $jenis  = $photo->jenis_foto;
        $photo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus.',
            'foto_id' => $fotoId,
            'jenis'   => $jenis,
        ]);
    }
}