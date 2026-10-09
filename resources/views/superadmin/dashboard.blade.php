@extends('layouts.app')
@section('title', 'Kelola User')

@section('content')
<h5 class="mb-3 fw-bold">Kelola User</h5>

{{-- 3 KOTAK BESAR --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">📥 Total Tugas Hari Ini</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['total'] }}</h2>
                    </div>
                    <i class="bi bi-calendar-day" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">✅ Sudah Dikerjakan</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['selesai'] }}</h2>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">⏳ Belum Dikerjakan</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['belum'] }}</h2>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- GRAFIK RATING — 2 CHART TERPISAH --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-pc-display text-primary"></i>
                    Rating Teknisi Komputer
                </h6>
            </div>
            <div class="card-body">
                @if(count($rekapTeknisiKomputer) > 0)
                    <div style="position: relative; height: {{ max(250, count($rekapTeknisiKomputer) * 65) }}px;">
                        <canvas id="chartKomputer"></canvas>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-people" style="font-size: 3rem; opacity: .3;"></i>
                        <p class="mt-2 mb-0">Belum ada teknisi komputer</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-camera-video text-info"></i>
                    Rating Teknisi CCTV
                </h6>
            </div>
            <div class="card-body">
                @if(count($rekapTeknisiCctv) > 0)
                    <div style="position: relative; height: {{ max(250, count($rekapTeknisiCctv) * 65) }}px;">
                        <canvas id="chartCctv"></canvas>
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-people" style="font-size: 3rem; opacity: .3;"></i>
                        <p class="mt-2 mb-0">Belum ada teknisi CCTV</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- LEGENDA --}}
<div class="row g-2 mb-4 text-center small">
    <div class="col-4">
        <span class="badge" style="background: rgba(25, 135, 84, 0.9); padding: 6px 12px;">
            🥇 Top (≥ 80% dari yang tertinggi)
        </span>
    </div>
    <div class="col-4">
        <span class="badge" style="background: rgba(255, 193, 7, 0.9); color: #000; padding: 6px 12px;">
            🥈 Sedang (40-79%)
        </span>
    </div>
    <div class="col-4">
        <span class="badge" style="background: rgba(220, 53, 69, 0.9); padding: 6px 12px;">
            🥉 Rendah (< 40%)
        </span>
    </div>
</div>

{{-- DAFTAR USER --}}
<div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0 fw-bold">Daftar User</h6>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-plus-circle"></i> Tambah User
    </button>
</div>

<div class="card shadow-sm mb-3">
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" style="font-size: .85rem;">
        <thead class="table-light">
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th class="text-center" style="width: 140px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->display_name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    @php
                        $roleBadge = match($u->role) {
                            'superadmin'   => 'bg-danger',
                            'admin'        => 'bg-primary',
                            'teknisi_cctv' => 'bg-info text-dark',
                            default        => 'bg-secondary',
                        };
                        $roleLabel = match($u->role) {
                            'teknisi_cctv' => 'Teknisi CCTV',
                            'teknisi'      => 'Teknisi Komputer',
                            default        => ucfirst($u->role),
                        };
                    @endphp
                    <span class="badge {{ $roleBadge }}">{{ $roleLabel }}</span>
                </td>
                <td>
                    @if($u->status === 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($u->role !== 'superadmin')
                    <div class="d-flex gap-1 justify-content-center">
                        {{-- Reset Password --}}
                        <button type="button"
                                class="btn btn-sm btn-outline-danger py-0 px-2"
                                title="Reset Password"
                                data-bs-toggle="modal"
                                data-bs-target="#modalResetPassword{{ $u->id }}">
                            <i class="bi bi-key-fill"></i>
                        </button>

                        {{-- Toggle Status --}}
                        <form action="{{ route('superadmin.user.toggle', $u) }}" method="POST" class="d-inline m-0">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning py-0 px-2" title="Aktif/Nonaktif">
                                {{ $u->status === 'aktif' ? 'Off' : 'On' }}
                            </button>
                        </form>

                        {{-- Hapus --}}
                        <form action="{{ route('superadmin.user.destroy', $u) }}" method="POST"
                              class="d-inline m-0"
                              onsubmit="return confirm('Hapus user {{ $u->display_name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @else
                        <span class="text-muted small">—</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- MODAL: TAMBAH USER --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('superadmin.user.store') }}" class="modal-content">
            @csrf
            <div class="modal-header py-2">
                <h6 class="modal-title"><i class="bi bi-person-plus"></i> Tambah User</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-2">
                <div class="mb-2"><label class="form-label mb-1 small">Nama</label><input name="nama" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Username</label><input name="username" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Email</label><input type="email" name="email" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Password</label><input type="password" name="password" class="form-control form-control-sm" required minlength="6"></div>
                <div class="mb-2">
                    <label class="form-label mb-1 small">Role</label>
                    <select name="role" class="form-select form-select-sm" required>
                        <option value="admin">Admin</option>
                        <option value="teknisi">Teknisi Komputer</option>
                        <option value="teknisi_cctv">Teknisi CCTV</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <div class="mb-2"><label class="form-label mb-1 small">No HP</label><input name="no_hp" class="form-control form-control-sm"></div>
            </div>
            <div class="modal-footer py-1">
                <button class="btn btn-primary btn-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- 🆕 MODAL: RESET PASSWORD (satu per user) --}}
@foreach($users as $u)
    @if($u->role !== 'superadmin')
    <div class="modal fade" id="modalResetPassword{{ $u->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('superadmin.user.reset-password', $u) }}" class="modal-content">
                @csrf
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title">
                        <i class="bi bi-key-fill"></i> Reset Password
                    </h6>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="alert alert-warning small mb-3">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Kamu akan mereset password user:
                        <strong>{{ $u->display_name }}</strong>
                        <span class="badge bg-secondary">{{ $u->role }}</span>
                        <br>
                        <small>Password lama tidak akan berlaku lagi. Beritahu password baru ke user.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password"
                               class="form-control form-control-sm"
                               required minlength="6"
                               placeholder="Minimal 6 karakter">
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold">
                            Konfirmasi Password Baru <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="password_confirmation"
                               class="form-control form-control-sm"
                               required minlength="6"
                               placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="modal-footer py-1">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-key-fill"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rekapKomputer = @json($rekapTeknisiKomputer);
        const rekapCctv     = @json($rekapTeknisiCctv);

        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js tidak ter-load!');
            return;
        }

        function renderRatingChart(canvasId, rekapData) {
            const canvas = document.getElementById(canvasId);
            if (!canvas || !rekapData || rekapData.length === 0) return;

            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: rekapData.map(t => t.display_name),
                    datasets: [{
                        label: 'Jumlah Selesai',
                        data: rekapData.map(t => t.selesai),
                        backgroundColor: rekapData.map(t => t.warna),
                        borderColor: rekapData.map(t => t.warna_border),
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 15, right: 40 } },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            padding: 14,
                            displayColors: false,
                            callbacks: {
                                title: function(context) {
                                    return '👤 ' + context[0].label;
                                },
                                label: function(context) {
                                    const t = rekapData[context.dataIndex];
                                    return [
                                        '✅ Sudah selesai  : ' + t.selesai + ' tugas',
                                        '⏳ Belum selesai  : ' + t.belum + ' tugas',
                                        '📊 Total tugas    : ' + t.total + ' tugas',
                                        '🏆 Predikat       : ' + t.predikat,
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, precision: 0 },
                            title: { display: true, text: 'Jumlah Tugas Selesai', font: { size: 11, weight: 'bold' } }
                        },
                        y: { grid: { display: false } }
                    },
                    animation: {
                        onComplete: function() {
                            const chart = this;
                            const c = chart.ctx;
                            c.save();
                            c.textAlign = 'left';
                            c.textBaseline = 'middle';
                            chart.data.datasets.forEach(function(dataset, i) {
                                const meta = chart.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    const t = rekapData[index];
                                    c.font = 'bold 13px Arial';
                                    c.fillStyle = '#212529';
                                    c.fillText(t.selesai + ' tugas', bar.x + 8, bar.y - 8);
                                    c.font = '10px Arial';
                                    c.fillStyle = '#6c757d';
                                    c.fillText('(' + t.belum + ' belum · ' + t.total + ' total)', bar.x + 8, bar.y + 10);
                                });
                            });
                            c.restore();
                        }
                    }
                }
            });
        }

        renderRatingChart('chartKomputer', rekapKomputer);
        renderRatingChart('chartCctv', rekapCctv);
    });
</script>
@endpush