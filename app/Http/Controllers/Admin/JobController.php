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
     * Dashboard admin dengan filter tanggal & periode rating
     */
    public function dashboard(Request $request)
    {
        $tanggal = $request->input('tanggal');
        $periode = $request->input('periode', 'harian'); // 🆕 default harian

        // Validasi periode
        if (!in_array($periode, ['harian', 'mingguan', 'bulanan', 'semua'])) {
            $periode = 'harian';
        }

        try {
            $tanggalObj = $tanggal ? Carbon::parse($tanggal) : today();
        } catch (\Exception $e) {
            $tanggalObj = today();
        }

        $tanggalStr = $tanggalObj->format('Y-m-d');
        $tanggalSebelumnya = $tanggalObj->copy()->subDay();

        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', $tanggalStr)->count(),
            'selesai' => Job::whereDate('tanggal_tugas', $tanggalStr)->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', $tanggalStr)->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // 🆕 URUT DESC (tanggal terbaru di atas)
        $kerjaanHariIniKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', $tanggalStr)
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanHariIniCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', $tanggalStr)
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanKemarinKomputer = Job::with('teknisi')
            ->where('bidang', 'komputer')
            ->whereDate('tanggal_tugas', $tanggalSebelumnya)
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $kerjaanKemarinCctv = Job::with('teknisi')
            ->where('bidang', 'cctv')
            ->whereDate('tanggal_tugas', $tanggalSebelumnya)
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // 🆕 GUNAKAN PERIODE UNTUK RATING & REKAP
        $ratingHariIniKomputer = $this->getRatingPerBidang('komputer', $periode);
        $ratingHariIniCctv     = $this->getRatingPerBidang('cctv', $periode);

        $rekapTeknisiKomputer = $this->getRekapPerBidang('komputer', $periode);
        $rekapTeknisiCctv     = $this->getRekapPerBidang('cctv', $periode);

        return view('admin.dashboard', compact(
            'hariIni',
            'kerjaanHariIniKomputer', 'kerjaanHariIniCctv',
            'kerjaanKemarinKomputer', 'kerjaanKemarinCctv',
            'ratingHariIniKomputer', 'ratingHariIniCctv',
            'rekapTeknisiKomputer', 'rekapTeknisiCctv',
            'tanggalObj', 'periode' // 🆕 kirim periode ke view
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
            ->orderBy('tanggal_tugas', 'desc')
            ->orderBy('created_at', 'desc')
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
     * Assign teknisi (multi-clone)
     */
    public function assignTeknisi(Request $request, Job $job)
    {
        $request->validate([
            'teknisi_ids'   => 'nullable|array|max:10',
            'teknisi_ids.*' => 'exists:users,id',
        ]);

        $teknisiIds = $request->input('teknisi_ids', []);

        if (!empty($teknisiIds)) {
            $expectedRole = $job->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
            $validCount = User::whereIn('id', $teknisiIds)
                ->where('role', $expectedRole)
                ->where('status', 'aktif')
                ->count();

            if ($validCount !== count($teknisiIds)) {
                return back()->with('error', 'Ada teknisi yang tidak sesuai bidang.');
            }
        }

        if (empty($teknisiIds)) {
            if ($job->isGrouped()) {
                $groupJobs = $job->groupJobs()->get();
                foreach ($groupJobs as $gj) {
                    if ($gj->id !== $job->id) {
                        $gj->photos()->delete();
                        $gj->histories()->delete();
                        if ($gj->report) $gj->report()->delete();
                        $gj->delete();
                    }
                }
                $job->group_id = null;
            }

            $job->teknisi_id = null;
            $job->save();

            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => $job->status,
                'status_baru' => $job->status,
                'keterangan'  => 'Assign dibatalkan (bebas diambil)',
            ]);

            return back()->with('success', 'Assign dibatalkan.');
        }

        if (count($teknisiIds) === 1) {
            if ($job->isGrouped()) {
                $groupJobs = $job->groupJobs()->get();
                foreach ($groupJobs as $gj) {
                    if ($gj->id !== $job->id) {
                        $gj->photos()->delete();
                        $gj->histories()->delete();
                        if ($gj->report) $gj->report()->delete();
                        $gj->delete();
                    }
                }
                $job->group_id = null;
            }

            $oldTeknisiId = $job->teknisi_id;
            $job->teknisi_id = $teknisiIds[0];
            $job->save();

            if ($oldTeknisiId != $teknisiIds[0]) {
                JobHistory::create([
                    'job_id'      => $job->id,
                    'user_id'     => auth()->id(),
                    'status_lama' => $job->status,
                    'status_baru' => $job->status,
                    'keterangan'  => 'Teknisi di-assign: ' . ($job->teknisi->display_name ?? '-'),
                ]);
            }

            return back()->with('success', 'Teknisi berhasil di-assign.');
        }

        if ($job->isGrouped()) {
            $groupJobs = $job->groupJobs()->get();
            foreach ($groupJobs as $gj) {
                if ($gj->id !== $job->id) {
                    $gj->photos()->delete();
                    $gj->histories()->delete();
                    if ($gj->report) $gj->report()->delete();
                    $gj->delete();
                }
            }
        }

        $groupId = Job::generateGroupId();

        foreach ($teknisiIds as $index => $teknisiId) {
            if ($index === 0) {
                $job->teknisi_id = $teknisiId;
                $job->group_id   = $groupId;
                $job->kode_job   = Job::generateKodeWithIndex($job->bidang, 1);
                $job->save();

                JobHistory::create([
                    'job_id'      => $job->id,
                    'user_id'     => auth()->id(),
                    'status_lama' => $job->status,
                    'status_baru' => $job->status,
                    'keterangan'  => 'Clone grup dibuat: ' . $groupId,
                ]);
            } else {
                $cloneData = $job->replicate(['kode_job', 'group_id', 'teknisi_id']);
                $cloneData->kode_job        = Job::generateKodeWithIndex($job->bidang, $index + 1);
                $cloneData->group_id        = $groupId;
                $cloneData->teknisi_id      = $teknisiId;
                $cloneData->status          = 'DITUGASKAN';
                $cloneData->hasil_pekerjaan = null;
                $cloneData->catatan         = null;
                $cloneData->catatan_admin   = null;
                $cloneData->tanggal_selesai = null;
                $cloneData->save();

                JobHistory::create([
                    'job_id'      => $cloneData->id,
                    'user_id'     => auth()->id(),
                    'status_lama' => null,
                    'status_baru' => 'DITUGASKAN',
                    'keterangan'  => 'Clone grup ' . $groupId . ' untuk: ' . ($cloneData->teknisi->display_name ?? '-'),
                ]);
            }
        }

        return back()->with('success', sprintf(
            '✅ Berhasil assign %d teknisi (grup: %s)',
            count($teknisiIds),
            $groupId
        ));
    }

    /**
     * Rating per bidang dengan filter periode
     */
    private function getRatingPerBidang(string $bidang, string $periode)
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) use ($periode) {
                // Query dasar
                $queryTotal = Job::where('teknisi_id', $teknisi->id);
                $querySelesai = Job::where('teknisi_id', $teknisi->id)->where('status', 'SELESAI');

                // Filter Periode
                switch ($periode) {
                    case 'harian':
                        $queryTotal->whereDate('tanggal_tugas', today());
                        $querySelesai->whereDate('tanggal_tugas', today());
                        break;
                    case 'mingguan':
                        $queryTotal->whereBetween('tanggal_tugas', [now()->startOfWeek(), now()->endOfWeek()]);
                        $querySelesai->whereBetween('tanggal_tugas', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'bulanan':
                        $queryTotal->whereMonth('tanggal_tugas', now()->month)->whereYear('tanggal_tugas', now()->year);
                        $querySelesai->whereMonth('tanggal_tugas', now()->month)->whereYear('tanggal_tugas', now()->year);
                        break;
                    case 'semua':
                    default:
                        // Tanpa filter tanggal
                        break;
                }

                $total = $queryTotal->count();
                $selesai = $querySelesai->count();

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

    /**
     * Rekap per bidang dengan filter periode
     */
    private function getRekapPerBidang(string $bidang, string $periode): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        return User::where('role', $role)
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) use ($periode) {
                $querySelesai = Job::where('teknisi_id', $teknisi->id)->where('status', 'SELESAI');
                $queryBelum = Job::where('teknisi_id', $teknisi->id)->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN']);

                // Filter Periode
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
                    case 'semua':
                    default:
                        // Tanpa filter
                        break;
                }

                $selesai = $querySelesai->count();
                $belum = $queryBelum->count();

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
            'tanggal_tugas' => 'required|date',

            'teknisi_ids'   => 'nullable|array|max:10',
            'teknisi_ids.*' => 'exists:users,id',
        ]);

        $teknisiIds = $data['teknisi_ids'] ?? [];
        unset($data['teknisi_ids']);

        if (!empty($teknisiIds)) {
            $expectedRole = $data['bidang'] === 'cctv' ? 'teknisi_cctv' : 'teknisi';
            $validCount = User::whereIn('id', $teknisiIds)
                ->where('role', $expectedRole)
                ->where('status', 'aktif')
                ->count();

            if ($validCount !== count($teknisiIds)) {
                return back()
                    ->withErrors(['teknisi_ids' => 'Ada teknisi yang tidak sesuai bidang atau tidak aktif.'])
                    ->withInput();
            }
        }

        if (empty($teknisiIds)) {
            $data['kode_job']   = Job::generateKode($data['bidang']);
            $data['teknisi_id'] = null;
            $data['admin_id']   = auth()->id();
            $data['status']     = 'DITUGASKAN';
            $data['group_id']   = null;

            $job = Job::create($data);

            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => null,
                'status_baru' => 'DITUGASKAN',
                'keterangan'  => 'Tugas dibuat, bebas diambil teknisi',
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', '✅ Tugas berhasil dibuat.');
        }

        if (count($teknisiIds) === 1) {
            $data['kode_job']   = Job::generateKode($data['bidang']);
            $data['teknisi_id'] = $teknisiIds[0];
            $data['admin_id']   = auth()->id();
            $data['status']     = 'DITUGASKAN';
            $data['group_id']   = null;

            $job = Job::create($data);

            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => null,
                'status_baru' => 'DITUGASKAN',
                'keterangan'  => 'Tugas dibuat untuk 1 teknisi: ' . ($job->teknisi->display_name ?? '-'),
            ]);

            return redirect()->route('admin.dashboard')
                ->with('success', '✅ Tugas berhasil dibuat untuk ' . ($job->teknisi->display_name ?? '1 teknisi'));
        }

        $groupId     = Job::generateGroupId();
        $createdJobs = [];

        foreach ($teknisiIds as $index => $teknisiId) {
            $jobData = $data;

            $jobData['kode_job']   = Job::generateKodeWithIndex($data['bidang'], $index + 1);
            $jobData['teknisi_id'] = $teknisiId;
            $jobData['admin_id']   = auth()->id();
            $jobData['status']     = 'DITUGASKAN';
            $jobData['group_id']   = $groupId;

            $job = Job::create($jobData);
            $createdJobs[] = $job;

            JobHistory::create([
                'job_id'      => $job->id,
                'user_id'     => auth()->id(),
                'status_lama' => null,
                'status_baru' => 'DITUGASKAN',
                'keterangan'  => sprintf(
                    'Tugas dibuat dalam grup %s (%d/%d) untuk: %s',
                    $groupId,
                    $index + 1,
                    count($teknisiIds),
                    $job->teknisi->display_name ?? '-'
                ),
            ]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', sprintf(
                '✅ Berhasil membuat %d tiket dalam grup %s',
                count($createdJobs),
                $groupId
            ));
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

            'foto_sebelum'    => 'nullable|array',
            'foto_sebelum.*'  => 'image|mimes:jpg,jpeg,png,webp|max:5120',
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

        $job->hasil_pekerjaan = $data['hasil_pekerjaan'];
        $job->catatan         = $data['catatan'] ?? $job->catatan;
        $job->catatan_admin   = $data['catatan_admin'] ?? $job->catatan_admin;
        $job->tanggal_selesai = !empty($data['tanggal_selesai'])
            ? Carbon::parse($data['tanggal_selesai'])
            : now();
        $job->status = 'SELESAI';

        if (!empty($data['teknisi_id'])) {
            $teknisi = User::find($data['teknisi_id']);
            $expectedRole = $job->bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';
            if ($teknisi && $teknisi->role === $expectedRole) {
                $job->teknisi_id = $data['teknisi_id'];
            }
        }

        if (empty($job->teknisi_id)) {
            $job->teknisi_id = auth()->id();
        }

        $job->save();

        $uploadDir = public_path('uploads/jobs');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }
        @chmod($uploadDir, 0775);

        if (!is_writable($uploadDir)) {
            return redirect()->route('admin.show', $job)
                ->with('error', '❌ Folder uploads/jobs tidak bisa ditulis.');
        }

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

        JobReport::updateOrCreate(
            ['job_id' => $job->id],
            [
                'hasil'         => $data['hasil_pekerjaan'],
                'catatan'       => $data['catatan'] ?? null,
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'dibuat_oleh'   => auth()->id(),
            ]
        );

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

    /**
     * Hapus foto (admin)
     */
    public function hapusFoto(JobPhoto $photo)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && !$user->isSuperAdmin()) {
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