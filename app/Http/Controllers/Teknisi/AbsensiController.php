<?php

namespace App\Http\Controllers\Teknisi;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AbsensiController extends Controller
{
    // 1. Tampilkan Halaman Absen
    public function index()
    {
        $user = auth()->user();
        $absenHariIni = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->get();
        return view('teknisi.absensi', compact('user', 'absenHariIni'));
    }

    // 2. Proses Simpan Absen (Masuk / Pulang)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tipe' => 'required|in:masuk,pulang',
                'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            ]);

            $user = auth()->user();
            $now  = now();

            $folder = 'uploads/absensi';
            $dir    = public_path($folder);
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $filename = 'absen-' . $user->id . '-' . $request->tipe . '-' . $now->format('YmdHis') . '.jpg';
            $request->file('foto')->move($dir, $filename);
            
            $fotoPath = $folder . '/' . $filename;

            Attendance::create([
                'user_id' => $user->id,
                'tanggal' => today()->toDateString(),
                'tipe'    => $request->tipe,
                'jam'     => $now->format('H:i:s'),
                'foto'    => $fotoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => '✅ Absen ' . $request->tipe . ' berhasil pukul ' . $now->format('H:i')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SERVER ERROR: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Halaman Laporan Absensi Teknisi
    public function laporan(Request $request)
    {
        $user  = auth()->user();
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $start = now()->setYear($tahun)->setMonth($bulan)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $rekap = Attendance::rekapQuery()
            ->where('user_id', $user->id)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->orderBy('tanggal')
            ->get()
            ->map(function($r) use ($user) {
                $r->status = Attendance::hitungStatus($r->jam_masuk);
                $r->durasi = Attendance::hitungDurasi($r->jam_masuk, $r->jam_pulang);
                
                // 🆕 AMBIL FOTO MASUK
                $fotoMasuk = DB::table('attendances')
                    ->where('user_id', $user->id)
                    ->where('tanggal', $r->tanggal)
                    ->where('tipe', 'masuk')
                    ->whereNotNull('foto')
                    ->orderBy('jam', 'asc')
                    ->first();
                $r->foto_masuk = $fotoMasuk ? $fotoMasuk->foto : null;

                // 🆕 AMBIL FOTO PULANG
                $fotoPulang = DB::table('attendances')
                    ->where('user_id', $user->id)
                    ->where('tanggal', $r->tanggal)
                    ->where('tipe', 'pulang')
                    ->whereNotNull('foto')
                    ->orderBy('jam', 'desc')
                    ->first();
                $r->foto_pulang = $fotoPulang ? $fotoPulang->foto : null;

                return $r;
            });

        $statistik = [
            'hadir' => $rekap->where('status', 'hadir')->count(),
            'terlambat' => $rekap->where('status', 'terlambat')->count(),
            'total_menit' => $rekap->sum('durasi'),
        ];

        return view('teknisi.laporan_absensi', compact('rekap', 'statistik', 'bulan', 'tahun'));
    }
}