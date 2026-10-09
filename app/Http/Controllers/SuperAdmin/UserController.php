<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\{User, Job, Technician};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function dashboard()
    {
        $hariIni = [
            'total'   => Job::whereDate('tanggal_tugas', today())->count(),
            'selesai' => Job::whereDate('tanggal_tugas', today())->where('status', 'SELESAI')->count(),
            'belum'   => Job::whereDate('tanggal_tugas', today())->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])->count(),
        ];

        // ===== GRAFIK RATING TEKNISI (berdasarkan JUMLAH) =====
        $rekapTeknisi = User::where('role', 'teknisi')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($teknisi) {
                $teknisi->selesai = Job::where('teknisi_id', $teknisi->id)
                    ->where('status', 'SELESAI')
                    ->count();

                $teknisi->belum = Job::where('teknisi_id', $teknisi->id)
                    ->whereIn('status', ['DITUGASKAN', 'DIKERJAKAN'])
                    ->count();

                $teknisi->total = $teknisi->selesai + $teknisi->belum;
                $teknisi->rating = $teknisi->selesai;

                return $teknisi;
            })
            ->sortByDesc('selesai')
            ->values();

        $maxSelesai = $rekapTeknisi->max('selesai') ?: 1;

        $rekapTeknisi = $rekapTeknisi->map(function ($t) use ($maxSelesai) {
            $t->persen = $maxSelesai > 0 ? round(($t->selesai / $maxSelesai) * 100) : 0;

            if ($t->selesai >= ($maxSelesai * 0.8)) {
                $t->warna = 'rgba(25, 135, 84, 0.9)';
                $t->warna_border = 'rgba(25, 135, 84, 1)';
                $t->predikat = '🥇 Top';
            } elseif ($t->selesai >= ($maxSelesai * 0.4)) {
                $t->warna = 'rgba(255, 193, 7, 0.9)';
                $t->warna_border = 'rgba(255, 193, 7, 1)';
                $t->predikat = '🥈 Sedang';
            } else {
                $t->warna = 'rgba(220, 53, 69, 0.9)';
                $t->warna_border = 'rgba(220, 53, 69, 1)';
                $t->predikat = '🥉 Rendah';
            }

            return $t;
        });

        $users = User::orderBy('role')->orderBy('nama')->get();

        return view('superadmin.dashboard', compact('hariIni', 'rekapTeknisi', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,teknisi,superadmin',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['status']   = 'aktif';

            $user = User::create($data);

            if ($user->role === 'teknisi') {
                Technician::create([
                    'user_id' => $user->id,
                    'no_hp'   => $user->no_hp,
                    'status'  => 'aktif',
                ]);
            }
        });

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function toggle(User $user)
    {
        abort_if($user->role === 'superadmin', 403, 'Tidak bisa mengubah superadmin.');
        $user->update(['status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif']);
        return back()->with('success', 'Status user diubah.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role === 'superadmin', 403, 'Tidak bisa menghapus superadmin.');
        $user->delete();
        return back()->with('success', 'User dihapus.');
    }
}