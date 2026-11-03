@extends('layouts.app_admin')
@section('title', 'Dashboard Admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<style>
    /* ============================================
       PWA STYLES & ANTI-GAGAL
       ============================================ */
    body { background-color: #f2f4f8 !important; }
    
    /* Mematikan gaya link biru bawaan Bootstrap */
    .app-header a, .admin-panel a, .stat-card-modern a { text-decoration: none !important; }

    /* HEADER */
    .app-header {
        background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
        border-radius: 24px; padding: 24px; color: white !important;
        box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3);
        margin-bottom: 24px; position: relative; overflow: hidden;
    }
    .app-header h1 { font-size: 1.5rem; font-weight: 800; margin: 0; color: white !important; }
    .app-header p { font-size: 0.85rem; opacity: 0.9; margin: 4px 0 0 0; color: white !important; }

    .btn-header-action {
        background: rgba(255, 255, 255, 0.2) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: white !important; border-radius: 12px; padding: 8px 16px;
        font-size: 0.85rem; font-weight: 700; transition: all 0.2s;
    }
    .btn-header-action:hover { background: rgba(255, 255, 255, 0.3) !important; }

    /* FILTER CARD */
    .filter-card {
        background: white !important; border-radius: 20px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;
    }
    .filter-group label { font-size: 0.75rem; font-weight: 700; color: #666 !important; margin-bottom: 6px; display: block; }
    .filter-control {
        width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e0e0 !important;
        background: #f9f9f9 !important; font-size: 0.9rem; color: #1a1a1a !important; font-weight: 600;
    }
    .btn-filter {
        width: 100%; padding: 12px; border-radius: 10px; background: #0d6efd !important;
        color: white !important; border: none; font-weight: 700; font-size: 0.9rem;
    }

    /* STATISTIK CARD */
    .stat-card-modern {
        background: white !important; border-radius: 20px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px; height: 100%;
    }
    .stat-icon-box {
        width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;
    }
    .stat-card-modern .value { font-size: 1.8rem; font-weight: 800; line-height: 1; color: #1a1a1a !important; margin-bottom: 4px; }
    .stat-card-modern .label { font-size: 0.75rem; font-weight: 700; color: #888 !important; text-transform: uppercase; }

    /* ADMIN PANEL */
    .admin-panel {
        background: white !important; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-bottom: 24px; overflow: hidden; border: none !important;
    }
    .admin-panel .accordion-item { border: none; border-bottom: 1px solid #f0f0f0; background: white !important; }
    .admin-panel .accordion-button { background: white !important; font-weight: 800; color: #1a1a1a !important; box-shadow: none; padding: 16px 20px; }
    .admin-panel .accordion-button:not(.collapsed) { background: #f8f9fa !important; color: #0d6efd !important; }
    
    /* TABEL */
    .table-modern { margin-bottom: 0; font-size: 0.85rem; width: 100%; }
    .table-modern th {
        background: #f8f9fa !important; color: #666 !important; font-weight: 700; font-size: 0.75rem;
        text-transform: uppercase; border-bottom: 1px solid #eaeaea; padding: 12px 16px; white-space: nowrap;
    }
    .table-modern td { padding: 12px 16px; vertical-align: middle; border-bottom: 1px solid #f8f9fa; color: #333 !important; }
    
    /* Animasi Blink */
    @keyframes lampuBlink { 0%, 100% { background-color: #fff3cd !important; } 50% { background-color: #ffffff !important; } }
    @keyframes lampuBlinkMerah { 0%, 100% { background-color: #f8d7da !important; } 50% { background-color: #ffffff !important; } }
    tr.row-lampu, tr.row-lampu td { animation: lampuBlink 1.5s infinite ease-in-out !important; font-weight: 700 !important; }
    tr.row-lampu-merah, tr.row-lampu-merah td { animation: lampuBlinkMerah 1.5s infinite ease-in-out !important; font-weight: 700 !important; }
    
    .lampu-indicator { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #dc3545 !important; box-shadow: 0 0 8px #dc3545; margin-right: 6px; }

    /* HEADER ACCORDION */
    .header-komputer { background: #e7f1ff !important; color: #0d6efd !important; border-radius: 12px; padding: 12px 16px; font-weight: 800; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .header-cctv { background: #e0f7fa !important; color: #0aa2c0 !important; border-radius: 12px; padding: 12px 16px; font-weight: 800; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }

    /* TOP TEKNISI */
    .top-header-komputer { background: linear-gradient(135deg, #0d6efd, #0a58ca) !important; color: white !important; padding: 16px 20px; border-radius: 20px 20px 0 0; }
    .top-header-cctv { background: linear-gradient(135deg, #0dcaf0, #0aa2c0) !important; color: #000 !important; padding: 16px 20px; border-radius: 20px 20px 0 0; }

    .chart-wrapper { position: relative; width: 100%; min-height: 280px; }

    /* LAPTOP GRID */
    @media (min-width: 768px) {
        .filter-form { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; align-items: end; }
        .btn-filter { grid-column: span 3; width: 200px; justify-self: start; }
        .table-modern th, .table-modern td { white-space: normal; }
    }

    /* ============================================
       BOTTOM NAVIGATION (HANYA UNTUK HP)
       ============================================ */
    .pwa-bottom-nav {
        display: none; /* Disembunyikan di Laptop */
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1050;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
    }
    .pwa-bottom-nav .nav-item {
        flex: 1; text-align: center; padding: 6px 0; color: #adb5bd;
        text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
    }
    .pwa-bottom-nav .nav-item i { font-size: 1.4rem; }
    .pwa-bottom-nav .nav-item.active { color: #0d6efd; }

    @media (max-width: 767.98px) {
        .pwa-bottom-nav { display: flex; }
        body { padding-bottom: 85px !important; } /* Jarak agar konten tidak tertutup menu */
    }
</style>

<div class="container-fluid px-0">

    {{-- HEADER --}}
    <div class="app-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:1;">
            <div>
                <h1><i class="bi bi-speedometer2"></i> Monitoring Pekerjaan</h1>
                <p>Pantau aktivitas teknisi secara real-time</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.buat') }}" class="btn-header-action"><i class="bi bi-plus-circle"></i> Buat Tugas</a>
                <a href="{{ route('admin.import.index') }}" class="btn-header-action"><i class="bi bi-file-earmark-excel"></i> Import</a>
                <a href="{{ route('admin.belum-dikerjakan') }}" class="btn-header-action"><i class="bi bi-hourglass-split"></i> Belum</a>
            </div>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="filter-form">
            <div class="filter-group">
                <label><i class="bi bi-calendar-event"></i> Pilih Tanggal</label>
                <input type="date" name="tanggal" class="filter-control" value="{{ $tanggalObj->format('Y-m-d') }}" max="{{ now()->addYear()->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label><i class="bi bi-trophy-fill"></i> Periode Rating</label>
                <select name="periode" class="filter-control">
                    <option value="harian" @selected($periode === 'harian')>📅 Harian</option>
                    <option value="mingguan" @selected($periode === 'mingguan')>📆 Mingguan</option>
                    <option value="bulanan" @selected($periode === 'bulanan')>🗓 Bulanan</option>
                    <option value="semua" @selected($periode === 'semua')>🏆 Semua Waktu</option>
                </select>
            </div>
            <div class="filter-group d-flex gap-2">
                <button type="submit" class="btn-filter flex-fill"><i class="bi bi-search"></i> Tampilkan</button>
                <a href="{{ route('admin.dashboard', ['tanggal' => now()->format('Y-m-d'), 'periode' => 'harian']) }}" class="btn btn-light border fw-bold" style="border-radius:10px; display:flex; align-items:center; color: #333 !important;">Hari Ini</a>
            </div>
        </form>
    </div>

    {{-- STATISTIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-12">
            <div class="stat-card-modern">
                <div class="stat-icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar-day"></i></div>
                <div>
                    <div class="value">{{ $hariIni['total'] }}</div>
                    <div class="label">Total Tugas</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="stat-card-modern">
                <div class="stat-icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="value">{{ $hariIni['selesai'] }}</div>
                    <div class="label">Sudah Dikerjakan</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12">
            <div class="stat-card-modern">
                <div class="stat-icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="value">{{ $hariIni['belum'] }}</div>
                    <div class="label">Belum Dikerjakan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="header-komputer">
                <span><i class="bi bi-pc-display"></i> PEKERJAAN KOMPUTER</span>
                <span class="badge bg-primary text-white">{{ $kerjaanHariIniKomputer->count() + $kerjaanKemarinKomputer->count() }} total</span>
            </div>
            <div class="accordion" id="accordionKomputer">
                <div class="admin-panel">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingHariIniKmpt">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHariIniKmpt" aria-expanded="true">
                                <i class="bi bi-calendar-day text-primary me-2"></i> {{ $tanggalObj->translatedFormat('d F Y') }}
                                <span class="badge bg-primary ms-2">{{ $kerjaanHariIniKomputer->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapseHariIniKmpt" class="accordion-collapse collapse show" data-bs-parent="#accordionKomputer">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-modern">
                                        <thead><tr><th>Kode</th><th>Pekerjaan</th><th>Teknisi</th><th>Status</th></tr></thead>
                                        <tbody>
                                        @forelse($kerjaanHariIniKomputer as $j)
                                            @php
                                                $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                                                $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                                                $rowClass = $belumDiambil ? 'row-lampu-merah' : ($belumDikerjakan ? 'row-lampu' : '');
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>@if($belumDikerjakan) <span class="lampu-indicator"></span> @endif<code>{{ $j->kode_job }}</code></td>
                                                <td><div class="fw-bold">{{ $j->judul }}</div></td>
                                                <td>@if($j->teknisi)<span class="badge bg-primary">{{ $j->teknisi->display_name }}</span>@else<span class="badge bg-danger">Belum</span>@endif</td>
                                                <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->statusLabel() }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pekerjaan</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="header-cctv">
                <span><i class="bi bi-camera-video"></i> PEKERJAAN CCTV</span>
                <span class="badge bg-dark text-white">{{ $kerjaanHariIniCctv->count() + $kerjaanKemarinCctv->count() }} total</span>
            </div>
            <div class="accordion" id="accordionCctv">
                <div class="admin-panel">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingHariIniCctv">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHariIniCctv" aria-expanded="true">
                                <i class="bi bi-calendar-day text-info me-2"></i> {{ $tanggalObj->translatedFormat('d F Y') }}
                                <span class="badge bg-info text-dark ms-2">{{ $kerjaanHariIniCctv->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapseHariIniCctv" class="accordion-collapse collapse show" data-bs-parent="#accordionCctv">
                            <div class="accordion-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-modern">
                                        <thead><tr><th>Kode</th><th>Pekerjaan</th><th>Teknisi</th><th>Status</th></tr></thead>
                                        <tbody>
                                        @forelse($kerjaanHariIniCctv as $j)
                                            @php
                                                $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                                                $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                                                $rowClass = $belumDiambil ? 'row-lampu-merah' : ($belumDikerjakan ? 'row-lampu' : '');
                                            @endphp
                                            <tr class="{{ $rowClass }}">
                                                <td>@if($belumDikerjakan) <span class="lampu-indicator"></span> @endif<code>{{ $j->kode_job }}</code></td>
                                                <td><div class="fw-bold">{{ $j->judul }}</div></td>
                                                <td>@if($j->teknisi)<span class="badge bg-info text-dark">{{ $j->teknisi->display_name }}</span>@else<span class="badge bg-danger">Belum</span>@endif</td>
                                                <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->statusLabel() }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada pekerjaan</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOP TEKNISI --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="admin-panel">
                <div class="top-header-komputer">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill"></i> Top Teknisi Komputer ({{ ucfirst($periode) }})</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-modern">
                        <thead><tr><th class="text-center">#</th><th>Teknisi</th><th class="text-center">Selesai</th><th class="text-center">Belum</th></tr></thead>
                        <tbody>
                        @forelse($ratingHariIniKomputer as $i => $t)
                            <tr class="{{ $i == 0 ? 'table-warning' : '' }}">
                                <td class="text-center fw-bold">{{ $i == 0 ? '🥇' : ($i == 1 ? '🥈' : ($i == 2 ? '🥉' : $i+1)) }}</td>
                                <td><i class="bi bi-person-circle text-primary"></i> <strong>{{ $t->display_name }}</strong></td>
                                <td class="text-center"><span class="badge bg-success">{{ $t->hari_ini_selesai }}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark">{{ $t->hari_ini_belum }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-panel">
                <div class="top-header-cctv">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-trophy-fill"></i> Top Teknisi CCTV ({{ ucfirst($periode) }})</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-modern">
                        <thead><tr><th class="text-center">#</th><th>Teknisi</th><th class="text-center">Selesai</th><th class="text-center">Belum</th></tr></thead>
                        <tbody>
                        @forelse($ratingHariIniCctv as $i => $t)
                            <tr class="{{ $i == 0 ? 'table-warning' : '' }}">
                                <td class="text-center fw-bold">{{ $i == 0 ? '🥇' : ($i == 1 ? '🥈' : ($i == 2 ? '🥉' : $i+1)) }}</td>
                                <td><i class="bi bi-person-circle text-info"></i> <strong>{{ $t->display_name }}</strong></td>
                                <td class="text-center"><span class="badge bg-success">{{ $t->hari_ini_selesai }}</span></td>
                                <td class="text-center"><span class="badge bg-warning text-dark">{{ $t->hari_ini_belum }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="admin-panel h-100">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-pc-display text-primary"></i> Statistik Komputer</h6>
                </div>
                <div class="p-3">
                    @if(count($rekapTeknisiKomputer) > 0)
                        <div class="chart-wrapper" style="height: {{ max(280, count($rekapTeknisiKomputer) * 70) }}px;"><canvas id="chartKomputer"></canvas></div>
                    @else
                        <div class="text-center text-muted py-5"><i class="bi bi-bar-chart" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2 mb-0">Belum ada data</p></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="admin-panel h-100">
                <div class="p-3 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-camera-video text-info"></i> Statistik CCTV</h6>
                </div>
                <div class="p-3">
                    @if(count($rekapTeknisiCctv) > 0)
                        <div class="chart-wrapper" style="height: {{ max(280, count($rekapTeknisiCctv) * 70) }}px;"><canvas id="chartCctv"></canvas></div>
                    @else
                        <div class="text-center text-muted py-5"><i class="bi bi-bar-chart" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2 mb-0">Belum ada data</p></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- 🆕 BOTTOM NAVIGATION UNTUK HP --}}
<div class="pwa-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item active">
        <i class="bi bi-house-door-fill"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('admin.buat') }}" class="nav-item">
        <i class="bi bi-plus-square"></i>
        <span>Buat</span>
    </a>
    <a href="{{ route('admin.belum-dikerjakan') }}" class="nav-item">
        <i class="bi bi-hourglass-split"></i>
        <span>Belum</span>
    </a>
    <a href="{{ route('laporan.index') }}" class="nav-item">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Laporan</span>
    </a>
</div>

@push('scripts')
<script>
    function renderTopChart(canvasId, rekapData, colorTheme) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || !rekapData || rekapData.length === 0) return;

        const warnaBar = rekapData.map((t, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 0.95)';
            if (i === 1) return 'rgba(108, 117, 125, 0.9)';
            if (i === 2) return 'rgba(205, 127, 50, 0.9)';
            return colorTheme === 'cctv' ? 'rgba(13, 202, 240, 0.7)' : 'rgba(13, 110, 253, 0.7)';
        });

        const borderBar = rekapData.map((t, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 1)';
            if (i === 1) return 'rgba(108, 117, 125, 1)';
            if (i === 2) return 'rgba(205, 127, 50, 1)';
            return colorTheme === 'cctv' ? 'rgba(13, 202, 240, 1)' : 'rgba(13, 110, 253, 1)';
        });

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: rekapData.map(t => t.display_name),
                datasets: [{
                    label: 'Tugas Selesai',
                    data: rekapData.map(t => t.selesai),
                    backgroundColor: warnaBar,
                    borderColor: borderBar,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.7,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 15, right: 60, left: 5, bottom: 5 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 14,
                        displayColors: false,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(context) {
                                const t = rekapData[context[0].dataIndex];
                                const rank = context[0].dataIndex + 1;
                                let medal = '';
                                if (rank === 1) medal = '🥇 ';
                                else if (rank === 2) medal = '🥈 ';
                                else if (rank === 3) medal = '🥉 ';
                                return medal + t.display_name;
                            },
                            label: function(context) {
                                const t = rekapData[context.dataIndex];
                                return [
                                    '✅ Sudah selesai  : ' + t.selesai + ' tugas',
                                    '⏳ Belum selesai  : ' + t.belum + ' tugas',
                                    '📊 Total tugas    : ' + t.total + ' tugas',
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                    y: { grid: { display: false } }
                },
                animation: {
                    duration: 800,
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
                                c.font = 'bold 15px Arial';
                                c.fillStyle = '#212529';
                                c.fillText(t.selesai, bar.x + 10, bar.y - 6);
                                c.font = '10px Arial';
                                c.fillStyle = '#6c757d';
                                c.fillText('dari ' + t.total + ' tugas', bar.x + 10, bar.y + 9);
                            });
                        });
                        c.restore();
                    }
                }
            }
        });
    }

    const rekapKomputer = @json($rekapTeknisiKomputer);
    const rekapCctv     = @json($rekapTeknisiCctv);

    renderTopChart('chartKomputer', rekapKomputer, 'komputer');
    renderTopChart('chartCctv', rekapCctv, 'cctv');

    (function() {
        let state = false;
        setInterval(function() {
            state = !state;
            document.querySelectorAll('tr.row-lampu-merah').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#f8d7da' : '#ffffff';
                });
            });
            document.querySelectorAll('tr.row-lampu').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#fff3cd' : '#ffffff';
                });
            });
        }, 1500);
    })();
</script>
@endpush
@endsection