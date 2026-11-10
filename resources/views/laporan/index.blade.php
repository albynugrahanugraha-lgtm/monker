@extends('layouts.app_pwa')
@section('title', 'Laporan Pekerjaan')

@php
    $isTeknisi = auth()->user()->isAnyTeknisi();
    $isAdmin = auth()->user()->isAdmin() || auth()->user()->isSuperAdmin();
    $isCctv = auth()->user()->isTeknisiCctv() ?? false;
    $rp = auth()->user()->isTeknisiCctv() ? 'teknisi_cctv' : (auth()->user()->isTeknisi() ? 'teknisi' : 'admin');
    
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
    $primaryDark  = $isCctv ? '#0aa2c0' : '#0a58ca';
    $rgbColor     = $isCctv ? '13, 202, 240' : '13, 110, 253';
@endphp

@push('styles')
<style>
    body { background-color: #f2f4f8 !important; }
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* =========================================
       🆕 NAVBAR ATAS KHUSUS LAPTOP (DESKTOP)
       ========================================= */
    .desktop-top-navbar {
        display: none; /* Tersembunyi di HP */
        background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }});
        padding: 16px 24px; color: white;
        box-shadow: 0 4px 15px rgba({{ $rgbColor }}, 0.3);
        position: sticky; top: 0; z-index: 1020;
    }
    .desktop-top-navbar .nav-wrapper {
        max-width: 1000px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;
    }
    .desktop-top-navbar .brand { font-size: 1.2rem; font-weight: 800; text-decoration: none; color: white; }
    .desktop-top-navbar .nav-links { display: flex; gap: 8px; align-items: center; }
    .desktop-top-navbar .nav-links a {
        color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; font-weight: 600;
        padding: 6px 12px; border-radius: 8px; transition: all 0.2s;
    }
    .desktop-top-navbar .nav-links a:hover, .desktop-top-navbar .nav-links a.active {
        background: rgba(255,255,255,0.2); color: white;
    }
    .desktop-top-navbar .dropdown-menu { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 10px; padding: 8px; }
    .desktop-top-navbar .dropdown-item { font-size: 0.85rem; font-weight: 600; border-radius: 6px; padding: 8px 12px; }
    .desktop-top-navbar .dropdown-item:hover { background: #e7f1ff; color: #0d6efd; }

    @media (min-width: 768px) {
        .desktop-top-navbar { display: block; }
    }

    /* HEADER HALAMAN LAPORAN */
    .app-header {
        background: linear-gradient(135deg, {{ $primaryColor }}, {{ $primaryDark }});
        border-radius: 24px; padding: 24px 20px; color: white;
        box-shadow: 0 10px 25px rgba({{ $rgbColor }}, 0.3);
        margin-bottom: 24px;
    }
    .app-header h1 { font-size: 1.4rem; font-weight: 800; margin: 0; }
    .app-header p { font-size: 0.8rem; opacity: 0.9; margin: 4px 0 0 0; }
    
    .btn-header-action {
        background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4);
        color: white; border-radius: 12px; padding: 8px 14px;
        font-size: 0.8rem; font-weight: 700; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px;
    }

    /* FILTER CARD */
    .filter-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 24px; }
    .filter-group { margin-bottom: 12px; }
    .filter-group label { font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 6px; display: block; }
    .filter-control { width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e0e0; background: #f9f9f9; font-size: 0.85rem; color: #333; }
    .btn-filter { width: 100%; padding: 12px; border-radius: 12px; background: {{ $primaryColor }}; color: white; border: none; font-weight: 700; font-size: 0.9rem; margin-top: 8px; }

    /* GRAFIK CARD */
    .chart-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 24px; }
    .chart-card h2 { font-size: 1rem; font-weight: 800; margin: 0 0 16px 0; color: #1a1a1a; }
    .chart-wrapper { position: relative; height: 250px; width: 100%; }

    /* LIST LAPORAN CARD */
    .laporan-card {
        background: white; border-radius: 16px; padding: 16px; margin-bottom: 12px; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.03); cursor: pointer; transition: transform 0.15s;
        border-left: 6px solid #ccc;
    }
    .laporan-card:active { transform: scale(0.98); }
    .laporan-card.status-selesai { border-left-color: #198754; background-color: #f8fff9; }
    .laporan-card.status-pending { border-left-color: #ffc107; background-color: #fffdf5; }

    .lap-title { font-size: 0.95rem; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
    .lap-meta { display: flex; flex-wrap: wrap; gap: 10px; font-size: 0.75rem; color: #666; margin-bottom: 12px; }
    .lap-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #eee; padding-top: 12px; }
    .btn-aksi { background: #f1f3f5; color: #333; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; }

    /* NAVBAR BAWAH (KUSTOM UNTUK HALAMAN INI) */
    .pwa-bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1050;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05); display: none;
    }
    .pwa-bottom-nav .nav-item {
        flex: 1; text-align: center; padding: 6px 0; color: #adb5bd;
        text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
    }
    .pwa-bottom-nav .nav-item i { font-size: 1.4rem; }
    .pwa-bottom-nav .nav-item.active { color: {{ $primaryColor }}; }

    @media (min-width: 768px) {
        .app-container { max-width: 1000px; padding: 24px; padding-bottom: 24px; }
        .filter-card form { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: end; }
        .btn-filter { grid-column: span 4; width: 200px; justify-self: start; }
        .chart-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .grid-laporan { display: flex !important; flex-direction: column !important; gap: 12px; }
        .laporan-card { display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 16px 24px; margin-bottom: 0; }
        .laporan-card > div:first-child { flex: 1; }
        .laporan-card .lap-meta { margin-bottom: 0; }
        .laporan-card .lap-footer { border-top: none; padding-top: 0; margin-left: 24px; padding-left: 24px; border-left: 1px solid #eee; display: flex; align-items: center; gap: 16px; width: auto; }
    }

    @media (max-width: 767.98px) {
        .pwa-bottom-nav { display: flex; }
        body { padding-bottom: 85px !important; }
    }
</style>
@endpush

@section('content')

{{-- 🆕 NAVBAR ATAS KHUSUS LAPTOP --}}
<div class="desktop-top-navbar">
    <div class="nav-wrapper">
        <a href="{{ route('dashboard') }}" class="brand">
            <i class="bi bi-clipboard-check"></i> Monitoring Pekerjaan
        </a>
        
        <div class="nav-links">
            @if($isTeknisi)
                <a href="{{ route($rp . '.dashboard') }}"><i class="bi bi-house-door-fill"></i> Beranda</a>
                <a href="{{ route($rp . '.absensi') }}"><i class="bi bi-fingerprint"></i> Absensi</a>
                <a href="{{ route($rp . '.buat') }}"><i class="bi bi-plus-square"></i> Buat Tugas</a>
                <a href="{{ route('laporan.index') }}" class="active"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
            @endif

            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a href="{{ route('admin.buat') }}"><i class="bi bi-plus-circle"></i> Buat Tugas</a>
                <a href="{{ route('admin.import.index') }}"><i class="bi bi-file-earmark-excel"></i> Import</a>
                <a href="{{ route('admin.belum-dikerjakan') }}"><i class="bi bi-hourglass-split"></i> Belum</a>
                
                {{-- Dropdown Laporan di Laptop --}}
                <div class="dropdown d-inline-block">
                    <a href="#" class="dropdown-toggle active" data-bs-toggle="dropdown" style="text-decoration: none;">
                        <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.absensi') }}"><i class="bi bi-person-lines-fill text-primary"></i> Rekap Absensi</a></li>
                        <li><a class="dropdown-item" href="{{ route('laporan.index') }}"><i class="bi bi-briefcase-fill text-success"></i> Laporan Pekerjaan</a></li>
                    </ul>
                </div>
            @endif

            <a href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> Profil</a>
            
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" style="background:none; border:none; color:rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border-radius: 8px;" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white';" onmouseout="this.style.background='none'; this.style.color='rgba(255,255,255,0.8)';"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>
</div>

<div class="app-container">

    <div class="app-header">
        <div class="d-flex justify-content-between align-items-center" style="position:relative; z-index:1;">
            <div class="d-flex align-items-center gap-3">
                @if(!$isTeknisi)
                    <a href="{{ route('admin.dashboard') }}" class="d-md-none" style="color: white; font-size: 1.5rem; text-decoration: none;">
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                @endif
                
                <div>
                    <h1>Laporan Pekerjaan</h1>
                    <p>Rekapitulasi data pekerjaan teknisi</p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <a href="{{ route('laporan.excel', request()->query()) }}" class="btn-header-action" title="Export Excel"><i class="bi bi-file-earmark-excel"></i></a>
                <a href="{{ route('laporan.cetak', request()->query()) }}" target="_blank" class="btn-header-action" title="Cetak PDF"><i class="bi bi-printer"></i></a>
            </div>
        </div>
    </div>

    <div class="filter-card">
        <form method="GET" action="{{ route('laporan.index') }}">
            <div class="filter-group">
                <label>Tahun</label>
                <select name="tahun" class="filter-control">
                    @foreach($tahunList as $y)<option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>@endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select name="bulan" class="filter-control">
                    <option value="semua" @selected($bulan === 'semua' || !$bulan)>Semua Bulan</option>
                    @foreach($daftarBulan as $num => $nama)<option value="{{ $num }}" @selected($bulan == $num)>{{ $nama }}</option>@endforeach
                </select>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="filter-group">
                <label>Bidang</label>
                <select name="bidang" class="filter-control">
                    <option value="">Semua Bidang</option>
                    <option value="komputer" @selected(request('bidang')==='komputer')>Komputer</option>
                    <option value="cctv" @selected(request('bidang')==='cctv')>CCTV</option>
                </select>
            </div>
            @endif
            <div class="filter-group"><label>Dari Tanggal</label><input type="date" name="tanggal_mulai" class="filter-control" value="{{ $tanggalMulai ?? '' }}"></div>
            <div class="filter-group"><label>Sampai Tanggal</label><input type="date" name="tanggal_akhir" class="filter-control" value="{{ $tanggalAkhir ?? '' }}"></div>
            <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h2><i class="bi bi-bar-chart-fill text-primary"></i> Pekerjaan per Bulan ({{ $tahun }})</h2>
            <div class="chart-wrapper"><canvas id="chartTahunan"></canvas></div>
        </div>
        @if($bulan && $bulan !== 'semua')
        <div class="chart-card">
            <h2><i class="bi bi-calendar-range text-success"></i> Pekerjaan per Hari ({{ $daftarBulan[$bulan] }})</h2>
            <div class="chart-wrapper"><canvas id="chartHarian"></canvas></div>
        </div>
        @endif
    </div>

    <div style="display:flex; justify-content:space-between; align-items:end; margin: 24px 0 12px 0; padding: 0 4px;">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin:0;">Daftar Pekerjaan</h2>
        <span style="background: {{ $primaryColor }}; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">{{ $jobs->count() }} Data</span>
    </div>

    <div class="grid-laporan">
        @forelse($jobs as $j)
            @php
                $statusClass = $j->status === 'SELESAI' ? 'status-selesai' : 'status-pending';
                $urlDetail = $isTeknisi 
                    ? route($rp . '.show', $j) . '?from=laporan' 
                    : route('admin.show', $j);
            @endphp
            
            <div class="laporan-card {{ $statusClass }}" onclick="window.location='{{ $urlDetail }}'">
                <div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span style="font-family:monospace; font-size:0.7rem; color:#888;"><i class="bi bi-hash"></i>{{ $j->kode_job }}</span>
                        <span class="badge {{ $j->statusBadgeClass() }} d-md-none">{{ $j->status }}</span>
                    </div>
                    <div class="lap-title">{{ $j->judul }}</div>
                    <div class="lap-meta">
                        <span><i class="bi bi-person-circle text-primary"></i> {{ $j->teknisi->display_name ?? '-' }}</span>
                        <span><i class="bi bi-calendar-check text-success"></i> {{ $j->tanggal_selesai?->format('d M Y') ?? $j->tanggal_tugas?->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="lap-footer">
                    <span class="badge {{ $j->bidangBadgeClass() }} d-none d-md-inline" style="font-size:0.65rem;"><i class="bi {{ $j->bidangIcon() }}"></i> {{ $j->bidangLabel() }}</span>
                    <span class="badge {{ $j->statusBadgeClass() }} d-none d-md-inline">{{ $j->status }}</span>
                    <span class="btn-aksi">Detail <i class="bi bi-chevron-right"></i></span>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; margin-top: 12px; color: #333;">Tidak Ada Data</h3>
            </div>
        @endforelse
    </div>

</div>

{{-- BOTTOM NAV (TAMPILAN HP) --}}
@if(auth()->user()->isAnyTeknisi())
    <div class="pwa-bottom-nav">
        <a href="{{ route($rp . '.dashboard') }}" class="nav-item"><i class="bi bi-house-door-fill"></i><span>Beranda</span></a>
        <a href="{{ route($rp . '.absensi') }}" class="nav-item"><i class="bi bi-fingerprint"></i><span>Absen</span></a>
        <a href="{{ route($rp . '.buat') }}" class="nav-item"><i class="bi bi-plus-square"></i><span>Buat</span></a>
        <a href="{{ route('laporan.index') }}" class="nav-item active"><i class="bi bi-file-earmark-bar-graph"></i><span>Laporan</span></a>
    </div>
@elseif(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
    <div class="pwa-bottom-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="bi bi-house-door-fill"></i><span>Beranda</span></a>
        <a href="{{ route('admin.buat') }}" class="nav-item"><i class="bi bi-plus-square"></i><span>Buat</span></a>
        <a href="{{ route('admin.import.index') }}" class="nav-item"><i class="bi bi-file-earmark-excel"></i><span>Import</span></a>
        <a href="{{ route('admin.belum-dikerjakan') }}" class="nav-item"><i class="bi bi-hourglass-split"></i><span>Belum</span></a>
        <a href="{{ route('laporan.index') }}" class="nav-item active"><i class="bi bi-file-earmark-bar-graph"></i><span>Laporan</span></a>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctxTahunan = document.getElementById('chartTahunan');
    if (ctxTahunan) {
        new Chart(ctxTahunan, {
            type: 'bar',
            data: {
                labels: @json($chartTahunan['labels']),
                datasets: [
                    { label: 'Total', data: @json($chartTahunan['data']), backgroundColor: 'rgba(13, 110, 253, 0.8)', borderRadius: 5 },
                    { label: 'Selesai', data: @json($chartTahunan['selesai']), backgroundColor: 'rgba(25, 135, 84, 0.8)', borderRadius: 5 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    const ctxHarian = document.getElementById('chartHarian');
    if (ctxHarian) {
        new Chart(ctxHarian, {
            type: 'bar',
            data: {
                labels: @json($chartHarian['labels']),
                datasets: [
                    { label: 'Total', data: @json($chartHarian['data']), backgroundColor: 'rgba(13, 110, 253, 0.8)', borderRadius: 4 },
                    { label: 'Selesai', data: @json($chartHarian['selesai']), backgroundColor: 'rgba(25, 135, 84, 0.8)', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }
</script>
@endpush