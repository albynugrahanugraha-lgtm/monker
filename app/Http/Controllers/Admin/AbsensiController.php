<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiExport;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $start = now()->setYear($tahun)->setMonth($bulan)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $rekap = Attendance::rekapQuery()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get()
            ->map(function($r) {
                $r->user = User::find($r->user_id);
                $r->status = Attendance::hitungStatus($r->jam_masuk);
                $r->durasi = Attendance::hitungDurasi($r->jam_masuk, $r->jam_pulang);
                
                // 🆕 AMBIL FOTO MASUK
                $fotoMasuk = DB::table('attendances')
                    ->where('user_id', $r->user_id)
                    ->where('tanggal', $r->tanggal)
                    ->where('tipe', 'masuk')
                    ->whereNotNull('foto')
                    ->orderBy('jam', 'asc')
                    ->first();
                $r->foto_masuk = $fotoMasuk ? $fotoMasuk->foto : null;

                // 🆕 AMBIL FOTO PULANG
                $fotoPulang = DB::table('attendances')
                    ->where('user_id', $r->user_id)
                    ->where('tanggal', $r->tanggal)
                    ->where('tipe', 'pulang')
                    ->whereNotNull('foto')
                    ->orderBy('jam', 'desc')
                    ->first();
                $r->foto_pulang = $fotoPulang ? $fotoPulang->foto : null;

                return $r;
            });

        return view('admin.absensi', compact('rekap', 'bulan', 'tahun'));
    }

    public function export(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        
        $filename = 'Rekap_Absensi_' . $bulan . '_' . $tahun . '.xlsx';
        return Excel::download(new AbsensiExport($bulan, $tahun), $filename);
    }
}