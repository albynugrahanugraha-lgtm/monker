@extends('layouts.app')
@section('title', 'Kelola User')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

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

{{-- GRAFIK RATING TEKNISI --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-trophy-fill text-warning"></i>
            Rating Pengerjaan Tugas per Teknisi
        </h6>
        <small class="text-muted" style="font-size: .75rem;">
            <i class="bi bi-info-circle"></i> Berdasarkan jumlah tugas yang diselesaikan
        </small>
    </div>
    <div class="card-body">
        @if($rekapTeknisi->count() > 0)
            <div style="position: relative; height: {{ max(250, $rekapTeknisi->count() * 65) }}px;">
                <canvas id="chartRating"></canvas>
            </div>

            <div class="row g-2 mt-3 text-center small">
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
        @else
            <div class="text-center text-muted py-5">
                <i class="bi bi-people" style="font-size: 3rem; opacity: .3;"></i>
                <p class="mt-2 mb-0">Belum ada teknisi</p>
            </div>
        @endif
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
        <thead class="table-light"><tr><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->display_name }}</td>
                <td>{{ $u->username }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge bg-secondary">{{ $u->role }}</span></td>
                <td>@if($u->status === 'aktif')<span class="badge bg-success">Aktif</span>@else<span class="badge bg-danger">Nonaktif</span>@endif</td>
                <td>
                    @if($u->role !== 'superadmin')
                    <form action="{{ route('superadmin.user.toggle', $u) }}" method="POST" class="d-inline">@csrf
                        <button class="btn btn-sm btn-outline-warning py-0 px-2">{{ $u->status === 'aktif' ? 'Off' : 'On' }}</button>
                    </form>
                    <form action="{{ route('superadmin.user.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user?')">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger py-0 px-2"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('superadmin.user.store') }}" class="modal-content">
            @csrf
            <div class="modal-header py-2"><h6 class="modal-title">Tambah User</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body py-2">
                <div class="mb-2"><label class="form-label mb-1 small">Nama</label><input name="nama" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Username</label><input name="username" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Email</label><input type="email" name="email" class="form-control form-control-sm" required></div>
                <div class="mb-2"><label class="form-label mb-1 small">Password</label><input type="password" name="password" class="form-control form-control-sm" required minlength="6"></div>
                <div class="mb-2"><label class="form-label mb-1 small">Role</label><select name="role" class="form-select form-select-sm" required><option value="admin">Admin</option><option value="teknisi">Teknisi</option><option value="superadmin">Super Admin</option></select></div>
                <div class="mb-2"><label class="form-label mb-1 small">No HP</label><input name="no_hp" class="form-control form-control-sm"></div>
            </div>
            <div class="modal-footer py-1"><button class="btn btn-primary btn-sm">Simpan</button></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const ctx1 = document.getElementById('chartRating');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: @json($rekapTeknisi->pluck('display_name')),
                datasets: [
                    {
                        label: 'Jumlah Selesai',
                        data: @json($rekapTeknisi->pluck('selesai')),
                        backgroundColor: @json($rekapTeknisi->pluck('warna')),
                        borderColor: @json($rekapTeknisi->pluck('warna_border')),
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.65,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 15, right: 40 }
                },
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
                                const idx = context.dataIndex;
                                const data = @json($rekapTeknisi);
                                const t = data[idx];
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
                        title: {
                            display: true,
                            text: 'Jumlah Tugas Selesai',
                            font: { size: 11, weight: 'bold' }
                        }
                    },
                    y: { grid: { display: false } }
                },
                animation: {
                    onComplete: function() {
                        const chart = this;
                        const ctx = chart.ctx;
                        const data = @json($rekapTeknisi);

                        ctx.save();
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach(function(dataset, i) {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                const t = data[index];

                                ctx.font = 'bold 14px Arial';
                                ctx.fillStyle = '#212529';
                                ctx.fillText(t.selesai + ' tugas', bar.x + 8, bar.y - 8);

                                ctx.font = '11px Arial';
                                ctx.fillStyle = '#6c757d';
                                ctx.fillText('(' + t.belum + ' belum · ' + t.total + ' total)', bar.x + 8, bar.y + 10);
                            });
                        });
                        ctx.restore();
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection