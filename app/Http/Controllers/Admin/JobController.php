<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobHistory, User, JobPhoto, JobReport};
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class JobController extends Controller
{
    /**
     * Dashboard admin dengan filter tanggal
     */
    public function dashboard(Request $request)
    {
        $tanggal = $request->input('tanggal');

        try {
            $tanggalObj = $tanggal ? Carbon::parse($tanggal) : today();
        } catch (\Exception $e) {
            $tanggalObj = today();
        }

        $tanggalStr = $tanggalObj->format('Y-m-d');
        $tanggalSebelumnya = $tanggalObj->copy()->subDay();

        // ===== 3 KOTAK STATISTIK =====
        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', $tanggalStr)->count(),
            'selesai' => Job::whereDate('tanggal_tugas', $tanggalStr)->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', $tanggalStr)->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ===== TABEL PEKERJAAN =====
        $kerjaanHariIniKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', $tanggalStr)
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanHariIniCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', $tanggalStr)
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanKemarinKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', $tanggalSebelumnya)
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanKemarinCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', $tanggalSebelumnya)
            ->orderBy('created_at', 'desc')
            ->get();

        // ===== TOP TEKNISI =====
        $ratingHariIniKomputer = $this->getRatingPerBidang('komputer', $tanggalStr);
        $ratingHariIniCctv     = $this->getRatingPerBidang('cctv', $tanggalStr);

        // ===== REKAP TEKNISI =====
        $rekapTeknisiKomputer = $this->getRekapPerBidang('komputer');
        $rekapTeknisiCctv     = $this->getRekapPerBidang('cctv');

        return view('admin.dashboard', compact(
            'hariIni',
            'kerjaanHariIniKomputer', 'kerjaanHariIniCctv',
            'kerjaanKemarinKomputer', 'kerjaanKemarinCctv',
            'ratingHariIniKomputer', 'ratingHariIniCctv',
            'rekapTeknisiKomputer', 'rekapTeknisiCctv',
            'tanggalObj'
        ));
    }

    /**
     * Halaman daftar pekerjaan belum dikerjakan
     */
    public function belumDikerjakan(Request $request)
    {
        $bidang        = $request->input('bidang');
        $status        = $request->input('status');
        $tanggalFilter = $request->input('tanggal_filter');

        $query = Job::with('teknisi')
            ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);

        if ($bidang && in_array($bidang, ['komputer', 'cctv'])) {
            $query->where('bidang', $bidang);
        }

        if ($status && in_array($status, ['DITUGASKAN', 'DIKERJAKAN'])) {
            $query->where('status', $status);
        }

        if ($tanggalFilter === 'terlambat') {
            $query->whereDate('tanggal_tugas', '<', today());
        } elseif ($tanggalFilter === 'hari_ini') {
            $query->whereDate('tanggal_tugas', today());
        } elseif ($tanggalFilter === 'minggu_ini') {
            $query->whereBetween('tanggal_tugas', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);
        } elseif ($tanggalFilter === 'bulan_ini') {
            $query->whereMonth('tanggal_tugas', now()->month)
                  ->whereYear('tanggal_tugas', now()->year);
        }

        $jobs = $query
            ->orderByRaw("CASE
                WHEN tanggal_tugas < CURDATE() THEN 0
                WHEN tanggal_tugas = CURDATE() THEN 1
                ELSE 2
            END")
            ->orderBy('tanggal_tugas', 'asc')
            ->orderByRaw("CASE WHEN teknisi_id IS NULL THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'asc')
            ->paginate(50);

        $stats = [
            'total'         => Job::whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
            'komputer'      => Job::whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->where('bidang', 'komputer')->count(),
            'cctv'          => Job::whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->where('bidang', 'cctv')->count(),
            'terlambat'     => Job::whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
                ->whereDate('tanggal_tugas', '<', today())->count(),
            'belum_diambil' => Job::where('status', 'DITUGASKAN')
                ->whereNull('teknisi_id')->count(),
        ];

        $teknisiList = User::whereIn('role', ['teknisi', 'teknisi_cctv'])
            ->where('status', 'aktif')
            ->orderBy('role')
            ->orderBy('nama')
            ->get();

        return view('admin.belum_dikerjakan', compact(
            'jobs', 'stats', 'teknisiList',
            'bidang', 'status', 'tanggalFilter'
        ));
    }

    /**
     * Assign teknisi cepat
     */
    public function assignTeknisi(Request $request, Job $job)
    {
        $request->validate([
            'teknisi_id' => 'nullable|exists:users,id',
        ]);

        $teknisiId = $request->input('teknisi_id') ?: null;

        if ($teknisiId) {
            $teknisi = User::find($teknisiId);
            $expectedRole = $job->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

            if ($teknisi->role !== $expectedRole) {
                return back()->with('error', 'Teknisi tidak sesuai dengan bidang tugas ini.');
            }
        }

        $oldTeknisi = $job->teknisi_id;
        $job->teknisi_id = $teknisiId;
        $job->save();

        if ($oldTeknisi != $teknisiId) {
            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => $job->status,
                'status_baru' => $job->status,
                'keterangan'  => $teknisiId
                    ? 'Teknisi di-assign: ' . ($job->teknisi->display_name ?? '-')
                    : 'Assign teknisi dibatalkan (bebas diambil)',
            ]);
        }

        return back()->with('success', 'Assign teknisi berhasil diupdate.');
    }

    private function getRatingPerBidang(string $bidang, string $tanggal)
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) use ($tanggal) {
                $total = Job::where('teknisi_id', $teknisi->id)
                    ->whereDate('tanggal_tugas', $tanggal)
                    ->count();

                $selesai = Job::where('teknisi_id', $teknisi->id)
                    ->whereDate('tanggal_tugas', $tanggal)
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
    }

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

    // ============================================================
    // ADMIN MENYELESAIKAN TUGAS
    // ============================================================

    /**
     * Form admin menyelesaikan tugas
     */
    public function formSelesai(Job $job)
    {
        if (!$job->bisaDiselesaikan()) {
            return redirect()->route('admin.show', $job)
                ->with('error', 'Pekerjaan ini sudah selesai atau tidak bisa diselesaikan lagi.');
        }

        $job->load('teknisi', 'admin', 'fotoSebelum', 'fotoSesudah', 'report');

        $role = $job->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
        $teknisiList = User::where('role', $role)
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        return view('admin.selesaikan_tugas', compact('job', 'teknisiList'));
    }

    /**
     * Proses admin menyelesaikan tugas
     */
    public function selesaikan(Request $request, Job $job)
    {
        if (!$job->bisaDiselesaikan()) {
            return redirect()->route('admin.show', $job)
                ->with('error', 'Pekerjaan ini sudah selesai atau tidak bisa diselesaikan lagi.');
        }

        $data = $request->validate([
            'hasil_pekerjaan' => 'required|string',
            'catatan'         => 'nullable|string',
            'catatan_admin'   => 'nullable|string',
            'tanggal_selesai' => 'nullable|date',
            'teknisi_id'      => 'nullable|exists:users,id',

            // 🆕 Validasi foto SEBELUM
            'foto_sebelum'    => 'nullable|array',
            'foto_sebelum.*'  => 'image|mimes:jpg,jpeg,png,webp|max:5120',

            // Validasi foto SESUDAH
            'foto_sesudah'    => 'nullable|array',
            'foto_sesudah.*'  => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'hasil_pekerjaan.required' => 'Hasil pekerjaan wajib diisi.',
            'foto_sebelum.*.image'     => 'File foto sebelum harus berupa gambar.',
            'foto_sebelum.*.mimes'     => 'Format foto sebelum harus JPG, JPEG, PNG, atau WEBP.',
            'foto_sebelum.*.max'       => 'Ukuran foto sebelum maksimal 5MB.',
            'foto_sesudah.*.image'     => 'File foto sesudah harus berupa gambar.',
            'foto_sesudah.*.mimes'     => 'Format foto sesudah harus JPG, JPEG, PNG, atau WEBP.',
            'foto_sesudah.*.max'       => 'Ukuran foto sesudah maksimal 5MB.',
        ]);

        $statusLama = $job->status;

        // Update data job
        $job->hasil_pekerjaan = $data['hasil_pekerjaan'];
        $job->catatan         = $data['catatan'] ?? $job->catatan;
        $job->catatan_admin   = $data['catatan_admin'] ?? $job->catatan_admin;
        $job->tanggal_selesai = !empty($data['tanggal_selesai'])
            ? Carbon::parse($data['tanggal_selesai'])
            : now();
        $job->status = 'SELESAI';

        // Kalau admin pilih teknisi
        if (!empty($data['teknisi_id'])) {
            $teknisi = User::find($data['teknisi_id']);
            $expectedRole = $job->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
            if ($teknisi && $teknisi->role === $expectedRole) {
                $job->teknisi_id = $data['teknisi_id'];
            }
        }

        // Kalau tetap belum ada teknisi, assign ke admin yang menyelesaikan
        if (empty($job->teknisi_id)) {
            $job->teknisi_id = auth()->id();
        }

        $job->save();

        // =====================================================
        // UPLOAD FOTO
        // =====================================================
        $uploadDir = public_path('uploads/jobs');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        // 🆕 Upload FOTO SEBELUM
        if ($request->hasFile('foto_sebelum')) {
            foreach ($request->file('foto_sebelum') as $file) {
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

        // Upload FOTO SESUDAH
        if ($request->hasFile('foto_sesudah')) {
            foreach ($request->file('foto_sesudah') as $file) {
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

        // Update / buat report
        JobReport::updateOrCreate(
            ['job_id' => $job->id],
            [
                'hasil'         => $data['hasil_pekerjaan'],
                'catatan'       => $data['catatan'] ?? null,
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'dibuat_oleh'   => auth()->id(),
            ]
        );

        // Catat history
        JobHistory::create([
            'job_id'      => $job->id,
            'user_id'     => auth()->id(),
            'status_lama' => $statusLama,
            'status_baru' => 'SELESAI',
            'keterangan'  => 'Diselesaikan oleh admin: ' . auth()->user()->display_name,
        ]);

        return redirect()->route('admin.show', $job)
            ->with('success', '✅ Pekerjaan berhasil diselesaikan oleh admin.');
    }
}