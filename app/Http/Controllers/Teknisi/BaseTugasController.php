<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobPhoto, JobReport, JobHistory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

abstract class BaseTugasController extends Controller
{
    /** @var string 'komputer' atau 'cctv' */
    protected string $bidang;

    /** @var string 'teknisi' atau 'teknisi_cctv' */
    protected string $routePrefix;

    // ============================================
    // AUTHORIZE — cek job milik bidang yang benar
    // ============================================
    protected function authorizeBidang(Job $job): void
    {
        abort_if($job->bidang !== $this->bidang, 403, 'Tugas ini bukan bidang Anda.');
    }

    // ============================================
    // HELPER — Filter query berdasarkan periode (Untuk Riwayat)
    // ============================================
    private function applyPeriode($query, string $periode)
    {
        return match($periode) {
            'harian'   => $query->whereDate('tanggal_tugas', today()),
            'mingguan' => $query->whereBetween('tanggal_tugas', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                          ]),
            'bulanan'  => $query->whereMonth('tanggal_tugas', now()->month)
                                ->whereYear('tanggal_tugas', now()->year),
            'semua'    => $query,
            default    => $query->whereDate('tanggal_tugas', today()),
        };
    }

    // ============================================
    // DASHBOARD
    // ============================================
    public function dashboard(Request $request)
    {
        $user    = auth()->user();
        $periode = $request->input('periode', 'harian'); // Filter untuk Riwayat Selesai

        // Validasi periode
        if (!in_array($periode, ['harian', 'mingguan', 'bulanan', 'semua'])) {
            $periode = 'harian';
        }

        // ============================================================
        // 3 KOTAK STATISTIK (SELALU BULAN INI)
        // ============================================================
        $hariIni = [
            'total'   => Job::where('teknisi_id', $user->id)
                ->where('bidang', $this->bidang)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->count(),
            'selesai' => Job::where('teknisi_id', $user->id)
                ->where('bidang', $this->bidang)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->where('status', 'SELESAI')
                ->count(),
            'belum'   => Job::where('teknisi_id', $user->id)
                ->where('bidang', $this->bidang)
                ->whereMonth('tanggal_tugas', now()->month)
                ->whereYear('tanggal_tugas', now()->year)
                ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
                ->count(),
        ];

        // ============================================================
        // DAFTAR PEKERJAAN AKTIF (TIDAK TERPENGARUH FILTER PERIODE)
        // ============================================================
        $jobs = Job::with(['photos', 'fotoSebelum', 'fotoSesudah'])
            ->where('bidang', $this->bidang)
            ->where(function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('teknisi_id', $user->id)
                        ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);
                })
                ->orWhere(function ($sub) {
                    $sub->whereNull('teknisi_id')
                        ->where('status', 'DITUGASKAN');
                });
            })
            ->orderBy('tanggal_tugas', 'desc')
            ->latest('created_at')
            ->get();

        // ============================================================
        // RIWAYAT SELESAI (TERPENGARUH FILTER PERIODE)
        // ============================================================
        $jobsSelesaiQuery = Job::with(['fotoSebelum', 'fotoSesudah'])
            ->where('teknisi_id', $user->id)
            ->where('bidang', $this->bidang)
            ->where('status', 'SELESAI');

        $jobsSelesai = $this->applyPeriode($jobsSelesaiQuery, $periode)
            ->latest('tanggal_selesai')
            ->limit(10)
            ->get();

        $routePrefix = $this->routePrefix;
        $bidang      = $this->bidang;

        return view('teknisi.dashboard', compact(
            'jobs', 'jobsSelesai', 'hariIni',
            'routePrefix', 'bidang', 'periode'
        ));
    }

    // ============================================
    // CREATE — form buat tugas
    // ============================================
    public function create()
    {
        $routePrefix = $this->routePrefix;
        $bidang      = $this->bidang;

        return view('teknisi.buat_tugas', compact('routePrefix', 'bidang'));
    }

    // ============================================
    // STORE — simpan tugas baru
    // ============================================
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

        $data['kode_job']   = Job::generateKode($this->bidang);
        $data['bidang']     = $this->bidang;
        $data['teknisi_id'] = auth()->id();
        $data['admin_id']   = null;
        $data['status']     = 'DIKERJAKAN';

        $job = Job::create($data);

        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => null,
            'status_baru' => 'DIKERJAKAN',
            'keterangan'  => "Tugas {$this->bidang} dibuat sendiri oleh teknisi",
        ]);

        return redirect()->route("{$this->routePrefix}.dashboard")
            ->with('success', 'Tugas berhasil dibuat.');
    }

    // ============================================
    // SHOW — detail pekerjaan
    // ============================================
    public function show(Job $job)
    {
        $this->authorizeBidang($job);
        abort_if($job->teknisi_id && $job->teknisi_id !== auth()->id(), 403);

        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');

        $routePrefix = $this->routePrefix;
        $bidang      = $this->bidang;

        return view('teknisi.detail_pekerjaan', compact('job', 'routePrefix', 'bidang'));
    }

    // ============================================
    // KERJAKAN — form selesai pekerjaan
    // ============================================
    public function kerjakan(Job $job)
    {
        $this->authorizeBidang($job);

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

        $routePrefix = $this->routePrefix;
        $bidang      = $this->bidang;

        return view('teknisi.selesai', compact('job', 'routePrefix', 'bidang'));
    }

    // ============================================
    // SELESAI — simpan hasil pekerjaan (3 SLOT FOTO)
    // ============================================
    public function selesai(Request $request, Job $job)
    {
        $this->authorizeBidang($job);
        abort_if($job->teknisi_id && $job->teknisi_id !== auth()->id(), 403);

        $data = $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan'         => 'nullable|string',

            'foto_sebelum_1'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sebelum_2'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sebelum_3'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'foto_sesudah_1'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sesudah_2'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_sesudah_3'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $statusLama = $job->status;

        if (!$job->teknisi_id) {
            $job->teknisi_id = auth()->id();
        }

        $job->hasil_pekerjaan = $data['hasil_pekerjaan'];
        $job->catatan         = $data['catatan'] ?? $job->catatan;
        $job->tanggal_selesai = now();
        $job->status          = 'SELESAI';
        $job->save();

        // Siapkan folder upload
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
            'keterangan'  => 'Diselesaikan oleh teknisi: ' . auth()->user()->display_name,
        ]);

        return redirect()->route("{$this->routePrefix}.show", $job)
            ->with('success', '✅ Pekerjaan berhasil diselesaikan.');
    }

    // ============================================
    // CETAK — cetak laporan per job
    // ============================================
    public function cetak(Job $job)
    {
        $this->authorizeBidang($job);
        abort_if($job->teknisi_id && $job->teknisi_id !== auth()->id(), 403);

        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report', 'histories.user');

        $bidang = $this->bidang;

        return view('teknisi.cetak', compact('job', 'bidang'));
    }

    // ============================================
    // HAPUS FOTO
    // ============================================
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