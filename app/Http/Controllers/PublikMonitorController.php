<?php

namespace App\Http\Controllers;

use App\Models\{Job, User, Attendance};
use Illuminate\Http\Request;

class PublikMonitorController extends Controller
{
    public function index()
    {
        return view('publik.monitor');
    }

    public function data(Request $request)
    {
        $periodeKomputer = $request->input('periode_komputer', 'bulanan');
        $periodeCctv     = $request->input('periode_cctv', 'bulanan');

        $validPeriode = ['harian', 'mingguan', 'bulanan', 'semua'];

        if (!in_array($periodeKomputer, $validPeriode)) $periodeKomputer = 'bulanan';
        if (!in_array($periodeCctv, $validPeriode)) $periodeCctv = 'bulanan';

        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

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

        $kerjaanHariIniKomputer = $this->mapJobs(Job::with('teknisi')->where('bidang', 'komputer')->whereDate('tanggal_tugas', today())->orderBy('created_at', 'desc')->get(), true);
        $kerjaanHariIniCctv = $this->mapJobs(Job::with('teknisi')->where('bidang', 'cctv')->whereDate('tanggal_tugas', today())->orderBy('created_at', 'desc')->get(), true);
        $kerjaanKemarinKomputer = $this->mapJobs(Job::with('teknisi')->where('bidang', 'komputer')->whereDate('tanggal_tugas', today()->subDay())->orderBy('created_at', 'desc')->get());
        $kerjaanKemarinCctv = $this->mapJobs(Job::with('teknisi')->where('bidang', 'cctv')->whereDate('tanggal_tugas', today()->subDay())->orderBy('created_at', 'desc')->get());

        $rekapTeknisiKomputer = $this->getRekapPerBidangByPeriode('komputer', $periodeKomputer);
        $rekapTeknisiCctv     = $this->getRekapPerBidangByPeriode('cctv', $periodeCctv);

        // 🆕 ABSENSI HARI INI
        $absensiHariIni = Attendance::rekapQuery()
            ->where('tanggal', today()->toDateString())
            ->get()
            ->map(function($r) {
                $user = User::find($r->user_id);
                $status = Attendance::hitungStatus($r->jam_masuk);
                return [
                    'nama' => $user ? $user->display_name : '-',
                    'bidang' => $user ? $user->bidangLabel() : '-',
                    'is_cctv' => $user && $user->isTeknisiCctv(),
                    'jam_masuk' => $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : null,
                    'jam_pulang' => $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : null,
                    'status' => $status,
                ];
            });

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
            'absensiHariIni'       => $absensiHariIni, // 🆕 Data Absensi
            'periodeKomputer' => $periodeKomputer,
            'periodeCctv'     => $periodeCctv,
            'timestamp'      => now()->format('d M Y, H:i:s'),
            'tanggalHariIni' => now()->translatedFormat('d F Y'),
            'tanggalKemarin' => now()->subDay()->translatedFormat('d F Y'),
        ]);
    }

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
            if ($withBelumDiambil) $data['belum_diambil'] = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
            return $data;
        })->toArray();
    }

    private function getRekapPerBidangByPeriode(string $bidang, string $periode): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
        return User::where('role', $role)->where('status', 'aktif')->get()->map(function ($teknisi) use ($periode) {
            $querySelesai = Job::where('teknisi_id', $teknisi->id)->where('status', 'SELESAI');
            $queryBelum = Job::where('teknisi_id', $teknisi->id)->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);
            switch ($periode) {
                case 'harian':
                    $querySelesai->whereDate('tanggal_tugas', today());
                    $queryBelum->whereDate('tanggal_tugas', today());
                    break;
                case 'mingguan':
                    $querySelesai->whereBetween('tanggal_tugas', [now()->startOfWeek(), now()->endOfWeek()]);
                    $queryBelum->whereBetween('tanggal_tugas', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulanan':
                    $querySelesai->whereMonth('tanggal_tugas', now()->month)->whereYear('tanggal_tugas', now()->year);
                    $queryBelum->whereMonth('tanggal_tugas', now()->month)->whereYear('tanggal_tugas', now()->year);
                    break;
            }
            $selesai = $querySelesai->count();
            $belum   = $queryBelum->count();
            return ['nama' => $teknisi->display_name, 'selesai' => $selesai, 'belum' => $belum, 'total' => $selesai + $belum];
        })->filter(fn($t) => $t['total'] > 0)->sortByDesc('selesai')->take(10)->values()->toArray();
    }
}