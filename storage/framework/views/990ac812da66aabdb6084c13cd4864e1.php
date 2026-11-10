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
        background: none; border: none;
    }
    .pwa-bottom-nav .nav-item i { font-size: 1.4rem; }
    .pwa-bottom-nav .nav-item.active { color: #0d6efd; }

    @media (max-width: 767.98px) {
        .pwa-bottom-nav { display: flex; }
        body { padding-bottom: 85px !important; }
    }

    /* STYLE BOTTOM SHEET (ADMIN) */
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
    .sheet-item:hover, .sheet-item:active { background: #f8f9fa; color: #0d6efd; }
    .sheet-item .icon-box {
        width: 48px; height: 48px; border-radius: 12px; display: flex; 
        align-items: center; justify-content: center; font-size: 1.5rem;
    }
</style>

<div class="pwa-bottom-nav">
    <a href="<?php echo e(route('admin.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-house-door-fill"></i>
        <span>Beranda</span>
    </a>
    
    <a href="<?php echo e(route('admin.buat')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.buat') ? 'active' : ''); ?>">
        <i class="bi bi-plus-square"></i>
        <span>Buat</span>
    </a>
    
    <a href="<?php echo e(route('admin.import.index')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.import.*') ? 'active' : ''); ?>">
        <i class="bi bi-file-earmark-excel"></i>
        <span>Import</span>
    </a>

    <a href="<?php echo e(route('admin.belum-dikerjakan')); ?>" class="nav-item <?php echo e(request()->routeIs('admin.belum-dikerjakan') ? 'active' : ''); ?>">
        <i class="bi bi-hourglass-split"></i>
        <span>Belum</span>
    </a>
    
    
    <button type="button" onclick="toggleSheet('adminSheet')" class="nav-item <?php echo e(request()->routeIs('admin.absensi*') || request()->routeIs('laporan.index') ? 'active' : ''); ?>">
        <i class="bi bi-file-earmark-bar-graph"></i>
        <span>Laporan</span>
    </button>
</div>


<div class="custom-bottom-sheet-backdrop" id="adminBackdrop" onclick="toggleSheet('adminSheet')"></div>
<div class="custom-bottom-sheet" id="adminSheet">
    <div class="sheet-handle"></div>
    <h6 class="fw-bold mb-3 px-2">Pusat Laporan</h6>
    
    <a href="<?php echo e(route('admin.absensi')); ?>" class="sheet-item">
        <div class="icon-box" style="background: #e7f1ff; color: #0d6efd;"><i class="bi bi-person-lines-fill"></i></div>
        <div>
            <div style="font-size: 0.95rem;">Rekap Absensi Teknisi</div>
            <div style="font-size: 0.75rem; color: #888; font-weight: normal;">Lihat kehadiran seluruh teknisi</div>
        </div>
    </a>
    
    <a href="<?php echo e(route('laporan.index')); ?>" class="sheet-item">
        <div class="icon-box" style="background: #e8f5e9; color: #198754;"><i class="bi bi-briefcase-fill"></i></div>
        <div>
            <div style="font-size: 0.95rem;">Laporan Pekerjaan</div>
            <div style="font-size: 0.75rem; color: #888; font-weight: normal;">Lihat rekapitulasi tugas selesai</div>
        </div>
    </a>
    
    <button type="button" onclick="toggleSheet('adminSheet')" class="btn btn-light w-100 mt-2 fw-bold py-2 rounded-3 text-muted">Tutup</button>
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
</script><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/layouts/partials/admin_bottom_nav.blade.php ENDPATH**/ ?>