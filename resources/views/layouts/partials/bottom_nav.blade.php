@php
    $rp = $routePrefix ?? (auth()->user()->isTeknisiCctv() ? 'teknisi_cctv' : 'teknisi');
    $isCctv = auth()->user()->isTeknisiCctv();
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
@endphp

<style>
    .bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; display: flex; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1000;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
    }
    .bottom-nav-item {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        color: #adb5bd; text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
        background: none; border: none; padding: 0;
    }
    .bottom-nav-item i { font-size: 1.4rem; }
    .bottom-nav-item.active { color: {{ $primaryColor }}; }

    /* 🆕 STYLE BOTTOM SHEET (POP-UP MENU LAPORAN) */
    .custom-bottom-sheet {
        position: fixed; bottom: -100%; left: 0; right: 0; background: white;
        border-radius: 24px 24px 0 0; z-index: 2000; padding: 24px 16px;
        transition: bottom 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 -10px 40px rgba(0,0,0,0.2);
    }
    .custom-bottom-sheet.show { bottom: 0; }
    .custom-bottom-sheet-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1999;
        display: none; opacity: 0; transition: opacity 0.3s;
    }
    .custom-bottom-sheet-backdrop.show { display: block; opacity: 1; }
    
    .sheet-handle { width: 40px; height: 5px; background: #ddd; border-radius: 5px; margin: 0 auto 20px auto; }
    .sheet-item {
        display: flex; align-items: center; gap: 16px; padding: 16px; 
        border-radius: 12px; text-decoration: none; color: #333; font-weight: 700;
        margin-bottom: 8px; transition: background 0.2s;
    }
    .sheet-item:hover, .sheet-item:active { background: #f8f9fa; color: {{ $primaryColor }}; }
    .sheet-item .icon-box {
        width: 48px; height: 48px; border-radius: 12px; background: #e7f1ff; color: {{ $primaryColor }};
        display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
    }

    @media (min-width: 768px) {
        .bottom-nav { display: none !important; }
    }
</style>

<div class="bottom-nav">
    <a href="{{ route($rp . '.dashboard') }}" class="bottom-nav-item {{ request()->routeIs($rp . '.dashboard') ? 'active' : '' }}">
        <i class="bi bi-house-door-fill"></i><span>Beranda</span>
    </a>
    
    <a href="{{ route($rp . '.absensi') }}" class="bottom-nav-item {{ request()->routeIs($rp . '.absensi') ? 'active' : '' }}">
        <i class="bi bi-fingerprint"></i><span>Absen</span>
    </a>

    <a href="{{ route($rp . '.buat') }}" class="bottom-nav-item {{ request()->routeIs($rp . '.buat') ? 'active' : '' }}">
        <i class="bi bi-plus-square"></i><span>Buat</span>
    </a>
    
    {{-- Tombol Laporan (Pemicu Bottom Sheet) --}}
    <button type="button" onclick="toggleSheet('teknisiSheet')" class="bottom-nav-item {{ request()->routeIs($rp . '.absensi.laporan') || request()->routeIs('laporan.index') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-bar-graph"></i><span>Laporan</span>
    </button>
</div>

{{-- POP-UP MENU LAPORAN (TEKNISI) --}}
<div class="custom-bottom-sheet-backdrop" id="teknisiBackdrop" onclick="toggleSheet('teknisiSheet')"></div>
<div class="custom-bottom-sheet" id="teknisiSheet">
    <div class="sheet-handle"></div>
    <h6 class="fw-bold mb-3 px-2">Pilih Laporan</h6>
    
    <a href="{{ auth()->user()->isTeknisiCctv() ? route('teknisi_cctv.absensi.laporan') : route('teknisi.absensi.laporan') }}" class="sheet-item">
        <div class="icon-box"><i class="bi bi-fingerprint"></i></div>
        <div>
            <div style="font-size: 0.95rem;">Laporan Absensi</div>
            <div style="font-size: 0.75rem; color: #888; font-weight: normal;">Lihat rekap kehadiran Anda</div>
        </div>
    </a>
    
    <a href="{{ route('laporan.index') }}" class="sheet-item">
        <div class="icon-box" style="background: #e8f5e9; color: #198754;"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <div style="font-size: 0.95rem;">Laporan Pekerjaan</div>
            <div style="font-size: 0.75rem; color: #888; font-weight: normal;">Lihat histori tugas yang selesai</div>
        </div>
    </a>
    
    <button type="button" onclick="toggleSheet('teknisiSheet')" class="btn btn-light w-100 mt-2 fw-bold py-2 rounded-3 text-muted">Tutup</button>
</div>

<script>
    function toggleSheet(id) {
        const sheet = document.getElementById(id);
        const backdrop = document.getElementById(id.replace('Sheet', 'Backdrop'));
        if (sheet.classList.contains('show')) {
            sheet.classList.remove('show');
            backdrop.classList.remove('show');
        } else {
            sheet.classList.add('show');
            backdrop.classList.add('show');
        }
    }
</script>