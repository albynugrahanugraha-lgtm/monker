<style>
    .pwa-bottom-nav {
        display: none; position: fixed; bottom: 0; left: 0; right: 0; background: white;
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
        body { padding-bottom: 85px !important; }
    }
</style>

<div class="pwa-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('admin.buat') }}" class="nav-item {{ request()->routeIs('admin.buat') ? 'active' : '' }}">
        <i class="bi bi-plus-square"></i>
        <span>Buat</span>
    </a>
    
    {{-- 🆕 MENU IMPORT --}}
    <a href="{{ route('admin.import.index') }}" class="nav-item {{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-excel"></i>
        <span>Import</span>
    </a>

    <a href="{{ route('admin.belum-dikerjakan') }}" class="nav-item {{ request()->routeIs('admin.belum-dikerjakan') ? 'active' : '' }}">
        <i class="bi bi-hourglass-split"></i>
        <span>Belum</span>
    </a>
    <a href="{{ route('admin.absensi') }}" class="nav-item {{ request()->routeIs('admin.absensi*') ? 'active' : '' }}">
        <i class="bi bi-person-lines-fill"></i>
        <span>Rekap Absen</span>
    </a>
    <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.index') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Laporan</span>
    </a>
</div>