<?php
    $rp = $routePrefix ?? (auth()->user()->isTeknisiCctv() ? 'teknisi_cctv' : 'teknisi');
    $isCctv = auth()->user()->isTeknisiCctv();
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
?>

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
    }
    .bottom-nav-item i { font-size: 1.4rem; }
    .bottom-nav-item.active { color: <?php echo e($primaryColor); ?>; }
    
    @media (min-width: 768px) {
        .bottom-nav { display: none !important; }
    }
</style>

<div class="bottom-nav">
    
    <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs($rp . '.dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-house-door-fill"></i><span>Beranda</span>
    </a>
    
    
    <a href="<?php echo e(route($rp . '.absensi')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs($rp . '.absensi') ? 'active' : ''); ?>">
        <i class="bi bi-fingerprint"></i><span>Absen</span>
    </a>

    
    <a href="<?php echo e(auth()->user()->isTeknisiCctv() ? route('teknisi_cctv.absensi.laporan') : route('teknisi.absensi.laporan')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs($rp . '.absensi.laporan') ? 'active' : ''); ?>">
        <i class="bi bi-file-earmark-bar-graph"></i><span>Lap. Absen</span>
    </a>

    
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs($rp . '.buat') ? 'active' : ''); ?>">
        <i class="bi bi-plus-square"></i><span>Buat</span>
    </a>
    
    
    <a href="<?php echo e(route('laporan.index')); ?>" class="bottom-nav-item <?php echo e(request()->routeIs('laporan.index') ? 'active' : ''); ?>">
        <i class="bi bi-bar-chart"></i><span>Lap. Tugas</span>
    </a>
</div><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/layouts/partials/bottom_nav.blade.php ENDPATH**/ ?>