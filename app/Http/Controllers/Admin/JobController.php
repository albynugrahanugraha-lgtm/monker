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

        // ===== TABEL PEKERJAAN HARI INI =====
        $kerjaanHariIni = Job::with('teknisi')
            ->whereDate('tanggal_tugas', today())
            ->orderBy('created_at', 'desc')
            ->get();

        // ===== TABEL PEKERJAAN KEMARIN =====
        $kerjaanKemarin = Job::with('teknisi')
            ->whereDate('tanggal_tugas', today()->subDay())
            ->orderBy('created_at', 'desc')
            ->get();

        // ===== TOP TEKNISI HARI INI =====
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

                $teknisi->hari_ini_total = $total;
                $teknisi->hari_ini_selesai = $selesai;
                $teknisi->hari_ini_belum = $belum;
                $teknisi->rating_hari_ini = $total > 0 ? round(($selesai / $total) * 100) : 0;

                return $teknisi;
            })
            ->filter(function ($t) {
                return $t->hari_ini_total > 0;
            })
            ->sortByDesc('hari_ini_selesai')
            ->values();

        // ===== REKAP TEKNISI OVERALL (UNTUK GRAFIK) =====
        // ⚠️ INI BAGIAN PENTING — field harus: display_name, selesai, belum, total
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

                return [
                    'display_name' => $teknisi->display_name,   // ← KUNCI untuk label grafik
                    'selesai'      => $selesai,                  // ← KUNCI untuk data grafik
                    'belum'        => $belum,
                    'total'        => $selesai + $belum,
                ];
            })
            ->sortByDesc('selesai')
            ->values();

        return view('admin.dashboard', compact(
            'hariIni',
            'kerjaanHariIni',
            'kerjaanKemarin',
            'ratingHariIni',
            'rekapTeknisi'
        ));
    }

    public function create()
    {
        $teknisiList = User::where('role', 'teknisi')
            ->where('status', 'aktif')
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
            'prioritas'     => 'required|in:Rendah,Sedang,Tinggi',
            'teknisi_id'    => 'nullable|exists:users,id',
            'tanggal_tugas' => 'required|date',
        ]);

        $data['teknisi_id'] = $data['teknisi_id'] ?: null;
        $data['kode_job'] = Job::generateKode();
        $data['admin_id'] = auth()->id();
        $data['status']   = 'DITUGASKAN';

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

        return redirect()->route('admin.dashboard')->with('success', 'Tugas berhasil dibuat.');
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
        if ($job->report) {
            $job->report()->delete();
        }

        $job->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Pekerjaan berhasil dihapus.');
    }
}