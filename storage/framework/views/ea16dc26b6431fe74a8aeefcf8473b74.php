
<?php $__env->startSection('title', 'Dashboard Teknisi ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer')); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
    $primaryDark = $isCctv ? '#0aa2c0' : '#0a58ca';
    $bgLight = $isCctv ? '#e0f7fa' : '#e7f1ff';
?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ============================================
       LAYOUT UTAMA (MOBILE FIRST)
       ============================================ */
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* 1. HEADER */
    .app-header {
        background: linear-gradient(135deg, <?php echo e($primaryColor); ?>, <?php echo e($primaryDark); ?>);
        border-radius: 24px; padding: 24px 20px; color: white;
        position: relative; overflow: hidden;
        box-shadow: 0 10px 25px rgba(<?php echo e($isCctv ? '13, 202, 240' : '13, 110, 253'); ?>, 0.3);
        margin-bottom: 24px;
    }
    .app-header::before {
        content: ''; position: absolute; top: -50px; right: -20px;
        width: 150px; height: 150px; border-radius: 50%; background: rgba(255,255,255,0.1);
    }
    .app-header h1 { font-size: 1.3rem; font-weight: 800; margin: 4px 0 0 0; }
    .app-header p { font-size: 0.8rem; opacity: 0.9; margin: 0; }
    
    .btn-header {
        background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.4);
        color: white; border-radius: 12px; padding: 8px 16px;
        font-size: 0.85rem; font-weight: 700; text-decoration: none;
        display: inline-flex; align-items: center; gap: 6px; position: relative; z-index: 1;
    }
    .btn-logout {
        background: rgba(220, 53, 69, 0.4); border: 1px solid rgba(220, 53, 69, 0.6);
        color: white; border-radius: 12px; padding: 8px 12px;
        font-size: 0.85rem; font-weight: 700; text-decoration: none;
        display: inline-flex; align-items: center; justify-content: center; position: relative; z-index: 1;
    }

    /* 🆕 DESKTOP MENU (Tersembunyi di HP) */
    .desktop-nav {
        display: none;
        gap: 8px;
        margin-top: 16px;
        position: relative;
        z-index: 1;
        border-top: 1px solid rgba(255,255,255,0.2);
        padding-top: 16px;
    }
    .desktop-nav a {
        color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem;
        font-weight: 600; padding: 6px 12px; border-radius: 8px; transition: all 0.2s;
    }
    .desktop-nav a:hover, .desktop-nav a.active {
        background: rgba(255,255,255,0.2); color: white;
    }

    /* 2. STATISTIK */
    .stat-row { display: flex; gap: 10px; margin-bottom: 24px; }
    .stat-card {
        flex: 1; background: white; border-radius: 16px; padding: 14px 8px;
        text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
    .stat-card .icon { font-size: 1.4rem; margin-bottom: 6px; display: block; }
    .stat-card .value { font-size: 1.4rem; font-weight: 800; color: #1a1a1a; margin-bottom: 4px; }
    .stat-card .label { font-size: 0.65rem; font-weight: 700; color: #888; text-transform: uppercase; }

    /* 3. TASK CARD */
    .task-card {
        background: white; border-radius: 16px; padding: 16px;
        margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        cursor: pointer; transition: transform 0.15s ease; position: relative;
        height: 100%;
    }
    .task-card:active { transform: scale(0.98); background: #fafafa; }
    .task-card.urgent { border-left: 5px solid #dc3545; }
    .task-card.proses { border-left: 5px solid #ffc107; }
    .task-card.normal { border-left: 5px solid #198754; }

    @keyframes softBlink { 
        0%, 100% { background-color: white; } 
        50% { background-color: #fff5f5; } 
    }
    .task-card.blink { animation: softBlink 1.5s infinite ease-in-out; }

    .badge-prio { font-size: 0.6rem; font-weight: 800; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; }
    .prio-tinggi { background: #ffebee; color: #c62828; }
    .prio-sedang { background: #fff8e1; color: #f57f17; }
    .prio-rendah { background: #e8f5e9; color: #2e7d32; }

    .task-title { font-size: 1rem; font-weight: 800; color: #1a1a1a; margin: 10px 0 12px 0; line-height: 1.3; }
    .task-meta { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; font-size: 0.8rem; color: #555; font-weight: 500; }
    
    .task-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e0e0e0; padding-top: 12px; }
    .status-badge { font-size: 0.65rem; font-weight: 700; padding: 5px 10px; border-radius: 6px; color: white; }
    .status-ditugaskan { background: #6c757d; }
    .status-dikerjakan { background: #ffc107; color: #000; }
    .status-selesai { background: #198754; }
    
    .btn-detail { background: <?php echo e($bgLight); ?>; color: <?php echo e($primaryDark); ?>; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: none; }

    /* 4. BOTTOM NAV & FAB */
    .bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; display: flex; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1000;
    }
    .bottom-nav-item {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        color: #adb5bd; text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
    }
    .bottom-nav-item i { font-size: 1.4rem; }
    .bottom-nav-item.active { color: <?php echo e($primaryColor); ?>; }

    .fab {
        position: fixed; bottom: 80px; right: 20px; width: 56px; height: 56px;
        border-radius: 50%; background: <?php echo e($primaryColor); ?>; color: white !important;
        display: flex; justify-content: center; align-items: center;
        font-size: 1.6rem; box-shadow: 0 6px 20px rgba(<?php echo e($isCctv ? '13, 202, 240' : '13, 110, 253'); ?>, 0.4);
        z-index: 1000; text-decoration: none !important;
    }

    /* MODAL CUSTOM */
    .custom-modal {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1050;
        display: none; align-items: center; justify-content: center; padding: 20px;
    }
    .custom-modal.show { display: flex; }
    .modal-box {
        background: white; width: 100%; max-width: 400px; border-radius: 20px;
        overflow: hidden; animation: slideUp 0.3s ease;
    }
    @keyframes slideUp { from { transform: translateY(50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .modal-header { padding: 16px 20px; color: white; display: flex; justify-content: space-between; align-items: center; font-weight: 800; }
    .modal-body { padding: 20px; }
    .modal-body table { width: 100%; font-size: 0.9rem; border-collapse: collapse; }
    .modal-body td { padding: 6px 0; border-bottom: 1px solid #f0f0f0; }
    .modal-body td:first-child { color: #888; width: 35%; }
    .modal-footer { padding: 0 20px 20px 20px; display: flex; flex-direction: column; gap: 10px; }
    
    .btn-modal { width: 100%; padding: 12px; border-radius: 12px; font-weight: 700; border: none; text-align: center; text-decoration: none; display: block; }

    /* ============================================
       🆕 PENGATURAN LAPTOP / TABLET (DESKTOP)
       ============================================ */
    @media (min-width: 768px) {
        .app-container { 
            max-width: 1000px;
            padding: 24px; 
            padding-bottom: 24px;
        }
        
        .bottom-nav, .fab { display: none !important; }
        .btn-logout { display: none !important; } /* Sembunyikan logout di pojok, karena pindah ke menu */

        /* 🆕 Tampilkan Menu Desktop */
        .desktop-nav { display: flex; }

        .app-header { padding: 32px; margin-bottom: 32px; }
        .app-header h1 { font-size: 1.8rem; }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }
        .stat-card { padding: 24px; }
        .stat-card .value { font-size: 2rem; }

        .task-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="app-container">

    
    <div class="app-header">
        <div class="d-flex justify-content-between align-items-center" style="position:relative; z-index:1;">
            <div>
                <p><i class="bi bi-calendar3"></i> <?php echo e(now()->format('l, d F Y')); ?></p>
                <h1>Halo, <?php echo e(auth()->user()->display_name); ?> 👋</h1>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <form action="<?php echo e(route('logout')); ?>" method="POST" style="margin:0;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-logout" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
                <a href="<?php echo e(route($rp . '.buat')); ?>" class="btn-header">
                    <i class="bi bi-plus-lg"></i> Buat
                </a>
            </div>
        </div>

        
        <div class="desktop-nav">
            <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="active"><i class="bi bi-house-door-fill"></i> Beranda</a>
            <a href="<?php echo e(route($rp . '.buat')); ?>"><i class="bi bi-plus-square"></i> Buat Tugas</a>
            <a href="<?php echo e(route('laporan.index')); ?>"><i class="bi bi-bar-chart-fill"></i> Laporan</a>
            <a href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-person-circle"></i> Profil</a>
            <form action="<?php echo e(route('logout')); ?>" method="POST" style="margin:0;">
                <?php echo csrf_field(); ?>
                <button type="submit" style="background:none; border:none; color:rgba(255,255,255,0.8); font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border-radius: 8px;" onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white';" onmouseout="this.style.background='none'; this.style.color='rgba(255,255,255,0.8)';"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
        </div>
    </div>

    
    <div class="stat-row">
        <div class="stat-card">
            <span class="icon">📥</span>
            <div class="value"><?php echo e($hariIni['total']); ?></div>
            <div class="label">Total</div>
        </div>
        <div class="stat-card">
            <span class="icon">✅</span>
            <div class="value"><?php echo e($hariIni['selesai']); ?></div>
            <div class="label">Selesai</div>
        </div>
        <div class="stat-card">
            <span class="icon">⏳</span>
            <div class="value"><?php echo e($hariIni['belum']); ?></div>
            <div class="label">Belum</div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 12px; padding: 0 4px;">
        <h2 style="font-size: 1rem; font-weight: 800; margin:0;">Tugas Anda</h2>
        <span style="background: <?php echo e($primaryColor); ?>; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;"><?php echo e($jobs->count()); ?> Tugas</span>
    </div>

    
    <div class="task-grid">
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $isBelum = $j->status === 'DITUGASKAN';
                $isProses = $j->status === 'DIKERJAKAN';
                
                $cardClass = 'normal';
                if ($isBelum) $cardClass = 'urgent blink';
                elseif ($isProses) $cardClass = 'proses';

                $prioClass = match($j->prioritas) {
                    'Tinggi' => 'prio-tinggi', 'Sedang' => 'prio-sedang', default => 'prio-rendah',
                };

                $statusClass = match($j->status) {
                    'DITUGASKAN' => 'status-ditugaskan', 'DIKERJAKAN' => 'status-dikerjakan', default => 'status-selesai',
                };

                $tglTugas = $j->tanggal_tugas;
                $ketTgl = '-'; $warnaTgl = 'color: #0d6efd;';
                if ($tglTugas) {
                    if ($tglTugas->isToday()) { $ketTgl = 'Hari Ini'; $warnaTgl = 'color: #0d6efd;'; }
                    elseif ($tglTugas->isYesterday()) { $ketTgl = 'Kemarin'; $warnaTgl = 'color: #ffc107;'; }
                    elseif ($tglTugas->isPast()) { $ketTgl = (int) $tglTugas->diffInDays(today()) . ' Hari Lalu'; $warnaTgl = 'color: #dc3545;'; }
                    else { $ketTgl = $tglTugas->format('d M'); }
                }
            ?>
            
            <div class="task-card <?php echo e($cardClass); ?>" onclick="openModal('<?php echo e($j->id); ?>')">
                <div class="d-flex justify-content-between align-items-center">
                    <span style="font-size:0.7rem; font-family:monospace; color:#888;"><i class="bi bi-hash"></i><?php echo e($j->kode_job); ?></span>
                    <span class="badge-prio <?php echo e($prioClass); ?>"><?php echo e($j->prioritas); ?></span>
                </div>
                
                <div class="task-title"><?php echo e($j->judul); ?></div>
                
                <div class="task-meta">
                    <div style="display:flex; gap:6px; align-items:flex-start;">
                        <i class="bi bi-geo-alt-fill" style="color:#dc3545;"></i> 
                        <span><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></span>
                    </div>
                    <div style="display:flex; gap:6px;">
                        <i class="bi bi-clock-history" style="<?php echo e($warnaTgl); ?>"></i> 
                        <span style="<?php echo e($warnaTgl); ?> font-weight:bold;"><?php echo e($ketTgl); ?></span>
                    </div>
                </div>
                
                <div class="task-footer">
                    <span class="status-badge <?php echo e($statusClass); ?>">
                        <?php echo e(str_replace('_', ' ', $j->status)); ?>

                    </span>
                    <span class="btn-detail">Detail <i class="bi bi-chevron-right"></i></span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 30px; background: white; border-radius: 16px;">
                <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; margin: 12px 0 4px 0;">Tidak Ada Tugas!</h3>
                <p style="font-size: 0.8rem; color: #888; margin:0;">Semua pekerjaan sudah selesai. 🎉</p>
            </div>
        <?php endif; ?>
    </div>

    
    <?php if($jobsSelesai->count() > 0): ?>
        <div style="margin: 24px 0 12px 0; padding: 0 4px;">
            <h2 style="font-size: 1rem; font-weight: 800; margin:0;">Riwayat Selesai</h2>
        </div>
        
        <div class="task-grid">
            <?php $__currentLoopData = $jobsSelesai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="task-card normal" onclick="window.location='<?php echo e(route($rp . '.show', $j)); ?>'" style="padding: 14px;">
                    <div style="font-size: 0.9rem; font-weight: 800; color: #1a1a1a;"><?php echo e($j->judul); ?></div>
                    <div style="font-size: 0.75rem; color: #888; margin-top: 4px;">
                        <i class="bi bi-check2-circle" style="color: #198754;"></i> 
                        Selesai: <?php echo e($j->tanggal_selesai?->format('d/m/Y H:i') ?? '-'); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

</div>


<a href="<?php echo e(route($rp . '.buat')); ?>" class="fab">
    <i class="bi bi-plus-lg"></i>
</a>


<div class="bottom-nav">
    <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="bottom-nav-item active">
        <i class="bi bi-house-door-fill"></i><span>Beranda</span>
    </a>
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="bottom-nav-item">
        <i class="bi bi-plus-square"></i><span>Buat</span>
    </a>
    <a href="<?php echo e(route('laporan.index')); ?>" class="bottom-nav-item">
        <i class="bi bi-bar-chart"></i><span>Laporan</span>
    </a>
    <a href="<?php echo e(route('profile.edit')); ?>" class="bottom-nav-item">
        <i class="bi bi-person-circle"></i><span>Profil</span>
    </a>
</div>


<?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="custom-modal" id="modalAksi<?php echo e($j->id); ?>">
        <div class="modal-box">
            <div class="modal-header" style="background: <?php echo e($j->status === 'DITUGASKAN' ? '#0d6efd' : ($j->status === 'DIKERJAKAN' ? '#ffc107; color:#000' : '#198754')); ?>;">
                <span>Detail Tugas</span>
                <button onclick="closeModal('<?php echo e($j->id); ?>')" style="background:none; border:none; color:inherit; font-size:1.2rem; cursor:pointer;"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-body">
                <h4 style="font-size:1rem; font-weight:800; margin-bottom:12px;"><?php echo e($j->judul); ?></h4>
                <table>
                    <tr><td>Kode</td><td style="font-weight:bold;"><?php echo e($j->kode_job); ?></td></tr>
                    <tr><td>Lokasi</td><td style="font-weight:bold;"><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td></tr>
                    <tr><td>Perangkat</td><td style="font-weight:bold;"><?php echo e($j->computer_name ?? '-'); ?></td></tr>
                    <tr><td>Prioritas</td><td><span class="badge-prio <?php echo e($prioClass); ?>" style="font-size:0.7rem;"><?php echo e($j->prioritas); ?></span></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <?php if($j->status === 'DITUGASKAN'): ?>
                    <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn-modal" style="background:#0d6efd; color:white;">Kerjakan Sekarang</a>
                <?php elseif($j->status === 'DIKERJAKAN'): ?>
                    <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn-modal" style="background:#198754; color:white;">Selesaikan Pekerjaan</a>
                <?php endif; ?>
                <a href="<?php echo e(route($rp . '.show', $j)); ?>" class="btn-modal" style="background:#e9ecef; color:#333;">Lihat Detail Lengkap</a>
                <button onclick="closeModal('<?php echo e($j->id); ?>')" class="btn-modal" style="background:#f8f9fa; color:#888;">Tutup</button>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function openModal(id) { document.getElementById('modalAksi' + id).classList.add('show'); }
    function closeModal(id) { document.getElementById('modalAksi' + id).classList.remove('show'); }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app_pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/dashboard.blade.php ENDPATH**/ ?>