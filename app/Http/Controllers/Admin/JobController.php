<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobHistory, User};
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function dashboard()
    {
        // ===== 3 KOTAK DASHBOARD =====
        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ===== TABEL HARI INI (split per bidang) =====
        $kerjaanHariIniKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', today())
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanHariIniCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', today())
            ->orderBy('created_at', 'desc')
            ->get();

        // ===== TABEL KEMARIN (split per bidang) =====
        $kerjaanKemarinKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', today()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanKemarinCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', today()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        // ===== TOP TEKNISI HARI INI (split per bidang) =====
        // Return COLLECTION (bukan array) supaya view bisa pakai $t->display_name
        $ratingHariIniKomputer = $this->getRatingPerBidang('komputer');
        $ratingHariIniCctv     = $this->getRatingPerBidang('cctv');

        // ===== REKAP TEKNISI OVERALL (split per bidang) =====
        // Return ARRAY (untuk @json() di chart JS)
        $rekapTeknisiKomputer = $this->getRekapPerBidang('komputer');
        $rekapTeknisiCctv     = $this->getRekapPerBidang('cctv');

        return view('admin.dashboard', compact(
            'hariIni',
            'kerjaanHariIniKomputer', 'kerjaanHariIniCctv',
            'kerjaanKemarinKomputer', 'kerjaanKemarinCctv',
            'ratingHariIniKomputer', 'ratingHariIniCctv',
            'rekapTeknisiKomputer', 'rekapTeknisiCctv'
        ));
    }

    /**
     * Rating Top Teknisi Hari Ini — Return COLLECTION of User models
     * supaya view bisa akses $t->display_name, $t->hari_ini_selesai, dll
     */
    private function getRatingPerBidang(string $bidang)
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

                $teknisi->hari_ini_total   = $total;
                $teknisi->hari_ini_selesai = $selesai;
                $teknisi->hari_ini_belum   = $total - $selesai;
                $teknisi->rating_hari_ini  = $total > 0 ? round(($selesai / $total) * 100) : 0;

                return $teknisi;
            })
            ->filter(fn($t) => $t->hari_ini_total > 0)
            ->sortByDesc('hari_ini_selesai')
            ->values();
        // ⚠️ JANGAN pakai ->toArray() — biar tetep Collection of User objects
    }

    /**
     * Rekap Overall per bidang — Return ARRAY eksplisit
     * supaya @json() di view include semua field (termasuk dynamic property)
     */
    private function getRekapPerBidang(string $bidang): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) {
                $selesai = Job::where('teknisi_id', $teknisi->id)
                    ->where('status', 'SELESAI')->count();

                $belum = Job::where('teknisi_id', $teknisi->id)
                    ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count();

                return [
                    'display_name' => $teknisi->display_name,
                    'selesai'      => $selesai,
                    'belum'        => $belum,
                    'total'        => $selesai + $belum,
                ];
            })
            ->sortByDesc('selesai')
            ->values()
            ->toArray();
        // ✅ Array eksplisit — aman untuk @json() di JS
    }

    public function create()
    {
        $teknisiList = User::whereIn('role', ['teknisi', 'teknisi_cctv'])
            ->where('status', 'aktif')
            ->orderBy('role')
            ->orderBy('nama')
            ->get();

        return view('admin.buat_tugas', compact('teknisiList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'judul'         => 'required|string|max:200',
            'deskripsi'     => 'nullable|string',
            'lok_gedung'    => 'nullable|string|max:100',
            'lokasi_ruang'  => 'nullable|string|max:100',
            'computer_name' => 'nullable|string|max:100',
            'merk'          => 'nullable|string|max:100',
            'kategori'      => 'nullable|string|max:50',
            'bidang'        => 'required|in:komputer,cctv',
            'prioritas'     => 'required|in:Rendah,Sedang,Tinggi',
            'teknisi_id'    => 'nullable|exists:users,id',
            'tanggal_tugas' => 'required|date',
        ]);

        $data['teknisi_id'] = $data['teknisi_id'] ?: null;
        $data['kode_job']   = Job::generateKode($data['bidang']);
        $data['admin_id']   = auth()->id();
        $data['status']     = 'DITUGASKAN';

        $job = Job::create($data);

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => null,
            'status_baru' => 'DITUGASKAN',
            'keterangan'  => $data['teknisi_id']
                ? 'Tugas dibuat untuk teknisi tertentu'
                : 'Tugas dibuat, bebas diambil teknisi',
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Job $job)
    {
        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');
        return view('admin.detail_job', compact('job'));
    }

    public function cetak(Job $job)
    {
        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');
        return view('admin.cetak', compact('job'));
    }

    public function destroy(Job $job)
    {
        foreach ($job->photos as $foto) {
            $path = public_path($foto->nama_file);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $job->photos()->delete();
        $job->histories()->delete();
        if ($job->report) $job->report()->delete();

        $job->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Pekerjaan berhasil dihapus.');
    }
}