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

        $rekapTeknisiKomputer = $this->getRekapPerBidang('komputer');
        $rekapTeknisiCctv     = $this->getRekapPerBidang('cctv');

        $users = User::orderBy('role')->orderBy('nama')->get();

        return view('superadmin.dashboard', compact(
            'hariIni',
            'rekapTeknisiKomputer',
            'rekapTeknisiCctv',
            'users'
        ));
    }

    private function getRekapPerBidang(string $bidang): array
    {
        $role = $bidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

        $data = User::where('role', $role)
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
            ->values();

        $maxSelesai = $data->max('selesai') ?: 1;

        return $data->map(function ($t) use ($maxSelesai) {
            $persen = $maxSelesai > 0 ? round(($t['selesai'] / $maxSelesai) * 100) : 0;

            if ($t['selesai'] >= ($maxSelesai * 0.8)) {
                $warna    = 'rgba(25, 135, 84, 0.9)';
                $border   = 'rgba(25, 135, 84, 1)';
                $predikat = '🥇 Top';
            } elseif ($t['selesai'] >= ($maxSelesai * 0.4)) {
                $warna    = 'rgba(255, 193, 7, 0.9)';
                $border   = 'rgba(255, 193, 7, 1)';
                $predikat = '🥈 Sedang';
            } else {
                $warna    = 'rgba(220, 53, 69, 0.9)';
                $border   = 'rgba(220, 53, 69, 1)';
                $predikat = '🥉 Rendah';
            }

            return [
                'display_name' => $t['display_name'],
                'selesai'      => $t['selesai'],
                'belum'        => $t['belum'],
                'total'        => $t['total'],
                'persen'       => $persen,
                'warna'        => $warna,
                'warna_border' => $border,
                'predikat'     => $predikat,
            ];
        })->toArray();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,teknisi,teknisi_cctv,superadmin',
            'no_hp'    => 'nullable|string|max:20',
        ]);

        DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            $data['status']   = 'aktif';

            $user = User::create($data);

            if (in_array($user->role, ['teknisi', 'teknisi_cctv'])) {
                Technician::create([
                    'user_id' => $user->id,
                    'no_hp'   => $user->no_hp,
                    'status'  => 'aktif',
                ]);
            }
        });

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Reset password user (dari SuperAdmin)
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.required'  => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'new_password.min'       => 'Password minimal 6 karakter.',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Password user "' . $user->display_name . '" berhasil direset.');
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