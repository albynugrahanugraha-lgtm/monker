<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobHistory, JobPhoto, JobReport};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class TugasCctvController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $hariIni = [
            'total'   => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::where('teknisi_id', $user->id)->whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        $jobs = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->where(function ($q) use ($user) {
                $q->where('teknisi_id', $user->id)
                  ->orWhereNull('teknisi_id');
            })
            ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $jobsSelesai = Job::where('teknisi_id', $user->id)
            ->where('status', 'SELESAI')
            ->orderBy('tanggal_selesai', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $myStats = [
            'selesai_bulan' => Job::where('teknisi_id', $user->id)
                ->where('status', 'SELESAI')
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->count(),
            'total_bulan' => Job::where('teknisi_id', $user->id)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->count(),
        ];

        $routePrefix = 'teknisi_cctv';
        $bidang      = 'cctv';

        return view('teknisi_cctv.dashboard', compact('hariIni', 'jobs', 'jobsSelesai', 'myStats', 'routePrefix', 'bidang'));
    }

    public function create()
    {
        $routePrefix = 'teknisi_cctv';
        $bidang      = 'cctv';

        return view('teknisi_cctv.buat_tugas', compact('routePrefix', 'bidang'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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

        $data['bidang']     = 'cctv';
        $data['kode_job']   = Job::generateKode($data['bidang']);
        $data['teknisi_id'] = $user->id;
        $data['status']     = 'DITUGASKAN';

        $job = Job::create($data);

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => null,
            'status_baru' => 'DITUGASKAN',
            'keterangan'  => 'Tugas dibuat oleh teknisi sendiri',
        ]);

        return redirect()->route('teknisi_cctv.dashboard')
            ->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Job $job)
    {
        $user = auth()->user();

        if ($job->bidang !== 'cctv') {
            abort(403);
        }

        if ($job->teknisi_id && $job->teknisi_id !== $user->id) {
            abort(403);
        }

        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');

        $routePrefix = 'teknisi_cctv';
        $bidang      = 'cctv';

        return view('teknisi_cctv.detail_pekerjaan', compact('job', 'routePrefix', 'bidang'));
    }

    public function kerjakan(Job $job)
    {
        $user = auth()->user();

        if ($job->bidang !== 'cctv') {
            abort(403);
        }

        if ($job->teknisi_id && $job->teknisi_id !== $user->id) {
            abort(403);
        }

        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report');

        $routePrefix = 'teknisi_cctv';
        $bidang      = 'cctv';

        return view('teknisi_cctv.selesai', compact('job', 'routePrefix', 'bidang'));
    }

    public function selesai(Request $request, Job $job)
    {
        $user = auth()->user();

        if ($job->bidang !== 'cctv') {
            abort(403);
        }

        if ($job->teknisi_id && $job->teknisi_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan'         => 'nullable|string',

            'foto_sebelum_1'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sebelum_2'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sebelum_3'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'foto_sesudah_1'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sesudah_2'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sesudah_3'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'hasil_pekerjaan.required' => 'Hasil pekerjaan wajib diisi.',
            'foto_sebelum_*.image'     => 'File foto sebelum harus berupa gambar.',
            'foto_sebelum_*.mimes'     => 'Format foto sebelum harus JPG, JPEG, PNG, atau WEBP.',
            'foto_sebelum_*.max'       => 'Ukuran foto sebelum maksimal 5MB per file.',
            'foto_sesudah_*.image'     => 'File foto sesudah harus berupa gambar.',
            'foto_sesudah_*.mimes'     => 'Format foto sesudah harus JPG, JPEG, PNG, atau WEBP.',
            'foto_sesudah_*.max'       => 'Ukuran foto sesudah maksimal 5MB per file.',
        ]);

        $statusLama = $job->status;

        if (!$job->teknisi_id) {
            $job->teknisi_id = $user->id;
        }

        $job->hasil_pekerjaan = $data['hasil_pekerjaan'];
        $job->catatan         = $data['catatan'] ?? $job->catatan;
        $job->tanggal_selesai = now();
        $job->status          = 'SELESAI';
        $job->save();

        $uploadDir = public_path('uploads/jobs');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }
        @chmod($uploadDir, 0775);

        // Upload 3 slot foto SEBELUM
        for ($i = 1; $i <= 3; $i++) {
            $field = 'foto_sebelum_' . $i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if (!$file->isValid()) continue;

                $filename = 'job-' . $job->id . '-sebelum-' . time() . '-' . uniqid() . '.'
                    . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);

                JobPhoto::create([
                    'job_id'     => $job->id,
                    'nama_file'  => 'uploads/jobs/' . $filename,
                    'jenis_foto' => 'sebelum',
                ]);
            }
        }

        // Upload 3 slot foto SESUDAH
        for ($i = 1; $i <= 3; $i++) {
            $field = 'foto_sesudah_' . $i;
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                if (!$file->isValid()) continue;

                $filename = 'job-' . $job->id . '-sesudah-' . time() . '-' . uniqid() . '.'
                    . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);

                JobPhoto::create([
                    'job_id'     => $job->id,
                    'nama_file'  => 'uploads/jobs/' . $filename,
                    'jenis_foto' => 'sesudah',
                ]);
            }
        }

        JobReport::updateOrCreate(
            ['job_id' => $job->id],
            [
                'hasil'       => $data['hasil_pekerjaan'],
                'catatan'     => $data['catatan'] ?? null,
                'dibuat_oleh' => auth()->id(),
            ]
        );

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => $statusLama,
            'status_baru' => 'SELESAI',
            'keterangan'  => 'Diselesaikan oleh teknisi: ' . $user->display_name,
        ]);

        return redirect()->route('teknisi_cctv.show', $job)
            ->with('success', '✅ Pekerjaan berhasil diselesaikan.');
    }

    public function cetak(Job $job)
    {
        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');

        return view('teknisi_cctv.cetak', compact('job'));
    }

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