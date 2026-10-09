<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Teknisi\TugasController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\PublikMonitorController;

// ============================================================
// TAMPILAN UTAMA → MONITOR (TANPA LOGIN)
// ============================================================
Route::get('/', fn() => redirect()->route('monitor'));

// ============================================================
// PUBLIK MONITOR (TANPA LOGIN)
// ============================================================
Route::get('/monitor', [PublikMonitorController::class, 'index'])->name('monitor');
Route::get('/monitor/data', [PublikMonitorController::class, 'data'])->name('monitor.data');

// ===== AUTH =====
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['username' => 'Username atau password salah.'])
            ->onlyInput('username');
    });
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('monitor');
})->middleware('auth')->name('logout');

// ===== DASHBOARD =====
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')->name('dashboard');

// ===== NOTIFIKASI CHECK =====
// TEKNISI: bunyi saat ada tugas BARU (DITUGASKAN)
// ADMIN & SUPERADMIN: TIDAK BUNYI (beep = 0)
Route::middleware('auth')->get('/notif-check', function () {
    $user = auth()->user();

    $badgeCount = 0;
    $beepCount  = 0;

    if ($user->isTeknisi()) {
        $badgeCount = \App\Models\Job::where('teknisi_id', $user->id)
            ->where('status', 'DITUGASKAN')
            ->count();
        $beepCount = $badgeCount;
    } elseif ($user->isAdmin() || $user->isSuperAdmin()) {
        $badgeCount = \App\Models\Job::whereDate('tanggal_selesai', today())->count();
        $beepCount = 0;
    }

    return response()->json([
        'count' => $badgeCount,
        'beep'  => $beepCount,
    ]);
})->name('notif.check');

// ===== LAPORAN =====
Route::middleware('auth')->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/cetak', [LaporanController::class, 'cetak'])->name('cetak');
    Route::get('/excel', [LaporanController::class, 'exportExcel'])->name('excel');
});

// ===== ADMIN =====
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [JobController::class, 'dashboard'])->name('dashboard');
    Route::get('/buat-tugas', [JobController::class, 'create'])->name('buat');
    Route::post('/buat-tugas', [JobController::class, 'store'])->name('store');
    Route::get('/job/{job}', [JobController::class, 'show'])->name('show');
    Route::get('/job/{job}/cetak', [JobController::class, 'cetak'])->name('cetak');
    Route::delete('/job/{job}', [JobController::class, 'destroy'])->name('destroy');
});

// ===== TEKNISI =====
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [TugasController::class, 'dashboard'])->name('dashboard');
    Route::get('/buat-tugas', [TugasController::class, 'create'])->name('buat');
    Route::post('/buat-tugas', [TugasController::class, 'store'])->name('store');
    Route::get('/job/{job}/kerjakan', [TugasController::class, 'kerjakan'])->name('kerjakan');
    Route::post('/job/{job}/selesai', [TugasController::class, 'selesai'])->name('selesai');
    Route::get('/job/{job}/cetak', [TugasController::class, 'cetak'])->name('cetak');
});

// ===== SUPER ADMIN =====
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/user/tambah', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/{user}/toggle', [UserController::class, 'toggle'])->name('user.toggle');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
});