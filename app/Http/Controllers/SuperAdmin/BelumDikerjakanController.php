<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Job, JobHistory, User};
use Illuminate\Http\Request;

class BelumDikerjakanController extends Controller
{
    /**
     * Halaman daftar pekerjaan belum dikerjakan (SuperAdmin)
     */
    public function index(Request $request)
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
            $query->whereBetween('tanggal_tugas', [now()->startOfWeek(), now()->endOfWeek()]);
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
            'terlambat'     => Job::whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->whereDate('tanggal_tugas', '<', today())->count(),
            'belum_diambil' => Job::where('status', 'DITUGASKAN')->whereNull('teknisi_id')->count(),
        ];

        $teknisiList = User::whereIn('role', ['teknisi', 'teknisi_cctv'])
            ->where('status', 'aktif')
            ->orderBy('role')
            ->orderBy('nama')
            ->get();

        return view('admin.belum_dikerjakan', compact(
            'jobs', 'stats', 'teknisiList',
            'bidang', 'status', 'tanggalFilter'
        ))->with('isSuperAdmin', true);
    }

    /**
     * Assign teknisi dari halaman superadmin
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
}