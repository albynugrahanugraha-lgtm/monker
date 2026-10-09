<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobPhoto, JobReport, JobHistory};
use Illuminate\Http\Request;

class TugasController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        // ===== 4 KOTAK DASHBOARD TEKNISI =====
        $hariIni = [
            'total'   => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->where('status', 'DITUGASKAN')->count(),
        ];

        // Pekerjaan yang harus dikerjakan (belum selesai)
        $query = Job::with(['photos', 'fotoSebelum', 'fotoSesudah'])
            ->where(function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('teknisi_id', $user->id)
                        ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);
                })
                ->orWhere(function ($sub) {
                    $sub->whereNull('teknisi_id')
                        ->where('status', 'DITUGASKAN');
                });
            });

        $jobs = (clone $query)->latest()->get();

        // Riwayat selesai
        $jobsSelesai = Job::with(['fotoSebelum', 'fotoSesudah'])
            ->where('teknisi_id', $user->id)
            ->where('status', 'SELESAI')
            ->latest('tanggal_selesai')
            ->limit(10)
            ->get();

        // Info teknisi ini sendiri (untuk kotak ke-4)
        $myStats = [
            'total_bulan' => Job::where('teknisi_id', $user->id)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->count(),
            'selesai_bulan' => Job::where('teknisi_id', $user->id)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->where('status', 'SELESAI')
                ->count(),
        ];

        return view('teknisi.dashboard', compact('jobs', 'jobsSelesai', 'hariIni', 'myStats'));
    }

    public function create()
    {
        return view('teknisi.buat_tugas');
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
            'tanggal_tugas' => 'required|date',
        ]);

        $data['kode_job']   = Job::generateKode();
        $data['teknisi_id'] = auth()->id();
        $data['admin_id']   = null;
        $data['status']     = 'DIKERJAKAN';

        $job = Job::create($data);

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => null,
            'status_baru' => 'DIKERJAKAN',
            'keterangan'  => 'Tugas dibuat sendiri oleh teknisi',
        ]);

        return redirect()->route('teknisi.dashboard')->with('success', 'Tugas berhasil dibuat.');
    }

    public function kerjakan(Job $job)
    {
        if ($job->teknisi_id !== null && $job->teknisi_id !== auth()->id()) {
            abort(403, 'Tugas ini sudah diambil teknisi lain.');
        }

        if ($job->teknisi_id === null) {
            $job->teknisi_id = auth()->id();
        }

        if ($job->status === 'DITUGASKAN') {
            $old = $job->status;
            $job->status = 'DIKERJAKAN';
            $job->save();

            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => $old,
                'status_baru' => 'DIKERJAKAN',
                'keterangan'  => 'Teknisi mengambil & memulai tugas ini',
            ]);
        } else {
            $job->save();
        }

        $job->load('fotoSebelum', 'fotoSesudah');
        return view('teknisi.selesai', compact('job'));
    }

    public function selesai(Request $request, Job $job)
    {
        abort_if($job->teknisi_id !== auth()->id(), 403);

        $data = $request->validate([
            'hasil_pekerjaan' => 'nullable|string',
            'catatan'         => 'nullable|string',
            'foto_sebelum.*'  => 'nullable|image|max:4096',
            'foto_sesudah.*'  => 'nullable|image|max:4096',
        ]);

        foreach (['sebelum', 'sesudah'] as $jenis) {
            if ($request->hasFile("foto_$jenis")) {
                foreach ($request->file("foto_$jenis") as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads'), $filename);

                    JobPhoto::create([
                        'job_id'     => $job->id,
                        'nama_file'  => 'uploads/' . $filename,
                        'jenis_foto' => $jenis,
                    ]);
                }
            }
        }

        $old = $job->status;
        $job->status          = 'SELESAI';
        $job->hasil_pekerjaan = $data['hasil_pekerjaan'] ?? $job->hasil_pekerjaan;
        $job->catatan         = $data['catatan'] ?? $job->catatan;
        $job->tanggal_selesai = now();
        $job->save();

        JobReport::updateOrCreate(
            ['job_id' => $job->id],
            [
                'hasil'       => $job->hasil_pekerjaan,
                'catatan'     => $job->catatan,
                'dibuat_oleh' => auth()->id(),
            ]
        );

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => $old,
            'status_baru' => 'SELESAI',
            'keterangan'  => 'Pekerjaan diselesaikan teknisi',
        ]);

        return redirect()->route('teknisi.dashboard')->with('success', 'Pekerjaan selesai.');
    }

    public function cetak(Job $job)
    {
        abort_if($job->teknisi_id !== auth()->id(), 403);
        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');
        return view('teknisi.cetak', compact('job'));
    }
}