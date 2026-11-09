
<?php $__env->startSection('title', 'Laporan Pekerjaan'); ?>

<?php
    // Deteksi Role User
    $isTeknisi = auth()->user()->isAnyTeknisi();
    $isCctv = auth()->user()->isTeknisiCctv() ?? false;
    $rp = auth()->user()->isTeknisiCctv() ? 'teknisi_cctv' : (auth()->user()->isTeknisi() ? 'teknisi' : 'admin');
    
    // KONSISTENSI WARNA: Cyan hanya untuk CCTV, sisanya Biru
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
    $primaryDark  = $isCctv ? '#0aa2c0' : '#0a58ca';
    $rgbColor     = $isCctv ? '13, 202, 240' : '13, 110, 253';
?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background-color: #f2f4f8 !important; }
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* HEADER */
    .app-header {
        background: linear-gradient(135deg, <?php echo e($primaryColor); ?>, <?php echo e($primaryDark); ?>);
        border-radius: 24px; padding: 24px 20px; color: white;
        box-shadow: 0 10px 25px rgba(<?php echo e($rgbColor); ?>, 0.3);
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

    /* Menu Desktop (Hanya Teknisi di Laptop) */
    .desktop-nav { display: none; gap: 8px; margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 16px; }
    .desktop-nav a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; }
    .desktop-nav a:hover, .desktop-nav a.active { background: rgba(255,255,255,0.2); color: white; }

    /* FILTER CARD */
    .filter-card {
        background: white; border-radius: 16px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 24px;
    }
    .filter-group { margin-bottom: 12px; }
    .filter-group label { font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 6px; display: block; }
    .filter-control {
        width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e0e0;
        background: #f9f9f9; font-size: 0.85rem; color: #333;
    }
    .btn-filter {
        width: 100%; padding: 12px; border-radius: 12px; background: <?php echo e($primaryColor); ?>;
        color: white; border: none; font-weight: 700; font-size: 0.9rem; margin-top: 8px;
    }

    /* GRAFIK CARD */
    .chart-card {
        background: white; border-radius: 16px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 24px;
    }
    .chart-card h2 { font-size: 1rem; font-weight: 800; margin: 0 0 16px 0; color: #1a1a1a; }
    .chart-wrapper { position: relative; height: 250px; width: 100%; }

    /* ============================================
       LIST LAPORAN CARD (WARNA STATUS)
       ============================================ */
    .laporan-card {
        background: white; border-radius: 16px; padding: 16px;
        margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        cursor: pointer; transition: transform 0.15s;
        
        /* Default border */
        border-left: 6px solid #ccc;
    }
    .laporan-card:active { transform: scale(0.98); }

    /* Warna Khusus Selesai (Hijau) */
    .laporan-card.status-selesai {
        border-left-color: #198754;
        background-color: #f8fff9;
    }

    /* Warna Khusus Belum Selesai (Kuning/Oranye) */
    .laporan-card.status-pending {
        border-left-color: #ffc107;
        background-color: #fffdf5;
    }

    .lap-title { font-size: 0.95rem; font-weight: 800; color: #1a1a1a; margin-bottom: 8px; }
    .lap-meta { display: flex; flex-wrap: wrap; gap: 10px; font-size: 0.75rem; color: #666; margin-bottom: 12px; }
    .lap-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #eee; padding-top: 12px; }
    .btn-aksi { background: #f1f3f5; color: #333; padding: 5px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; text-decoration: none; }

    /* BOTTOM NAV (Hanya Teknisi) */
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

    /* LAPTOP SETTINGS */
    @media (min-width: 768px) {
        .app-container { max-width: 1000px; padding: 24px; padding-bottom: 24px; }
        .bottom-nav { display: none !important; }
        .desktop-nav { display: flex; }
        .filter-card form { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: end; }
        .btn-filter { grid-column: span 4; width: 200px; justify-self: start; }
        
        /* Grafik berjejer 2 kolom */
        .chart-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        
        /* DAFTAR LAPORAN JADI 1 KOLOM PENUH (LIST KE BAWAH) */
        .grid-laporan { 
            display: flex !important; 
            flex-direction: column !important; 
            gap: 12px; 
        }
        
        /* TATA LETAK CARD JADI MENYAMPING */
        .laporan-card {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            margin-bottom: 0;
        }
        .laporan-card > div:first-child {
            flex: 1;
        }
        .laporan-card .lap-meta {
            margin-bottom: 0;
        }
        .laporan-card .lap-footer {
            border-top: none;
            padding-top: 0;
            margin-left: 24px;
            padding-left: 24px;
            border-left: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 16px;
            width: auto;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="app-container">

    <div class="app-header">
        <div class="d-flex justify-content-between align-items-center" style="position:relative; z-index:1;">
            <div class="d-flex align-items-center gap-3">
                
                <?php if(!$isTeknisi): ?>
                    <a href="<?php echo e(route('admin.dashboard')); ?>" style="color: white; font-size: 1.5rem; text-decoration: none;">
                        <i class="bi bi-arrow-left-circle-fill"></i>
                    </a>
                <?php endif; ?>
                
                <div>
                    <h1>Laporan Pekerjaan</h1>
                    <p>Rekapitulasi data pekerjaan teknisi</p>
                </div>
            </div>
            
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('laporan.excel', request()->query())); ?>" class="btn-header-action" title="Export Excel"><i class="bi bi-file-earmark-excel"></i></a>
                <a href="<?php echo e(route('laporan.cetak', request()->query())); ?>" target="_blank" class="btn-header-action" title="Cetak PDF"><i class="bi bi-printer"></i></a>
            </div>
        </div>

        
        <?php if($isTeknisi): ?>
        <div class="desktop-nav">
            <a href="<?php echo e(route($rp . '.dashboard')); ?>"><i class="bi bi-house-door-fill"></i> Beranda</a>
            <a href="<?php echo e(route($rp . '.buat')); ?>"><i class="bi bi-plus-square"></i> Buat Tugas</a>
            <a href="<?php echo e(route('laporan.index')); ?>" class="active"><i class="bi bi-bar-chart-fill"></i> Laporan</a>
            <a href="<?php echo e(route('profile.edit')); ?>"><i class="bi bi-person-circle"></i> Profil</a>
        </div>
        <?php endif; ?>
    </div>

    <div class="filter-card">
        <form method="GET" action="<?php echo e(route('laporan.index')); ?>">
            <div class="filter-group">
                <label>Tahun</label>
                <select name="tahun" class="filter-control">
                    <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($y); ?>" <?php if($tahun == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select name="bulan" class="filter-control">
                    <option value="semua" <?php if($bulan === 'semua' || !$bulan): echo 'selected'; endif; ?>>Semua Bulan</option>
                    <?php $__currentLoopData = $daftarBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($num); ?>" <?php if($bulan == $num): echo 'selected'; endif; ?>><?php echo e($nama); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
            <div class="filter-group">
                <label>Bidang</label>
                <select name="bidang" class="filter-control">
                    <option value="">Semua Bidang</option>
                    <option value="komputer" <?php if(request('bidang')==='komputer'): echo 'selected'; endif; ?>>Komputer</option>
                    <option value="cctv" <?php if(request('bidang')==='cctv'): echo 'selected'; endif; ?>>CCTV</option>
                </select>
            </div>
            <?php endif; ?>
            <div class="filter-group"><label>Dari Tanggal</label><input type="date" name="tanggal_mulai" class="filter-control" value="<?php echo e($tanggalMulai ?? ''); ?>"></div>
            <div class="filter-group"><label>Sampai Tanggal</label><input type="date" name="tanggal_akhir" class="filter-control" value="<?php echo e($tanggalAkhir ?? ''); ?>"></div>
            <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Tampilkan</button>
        </form>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h2><i class="bi bi-bar-chart-fill text-primary"></i> Pekerjaan per Bulan (<?php echo e($tahun); ?>)</h2>
            <div class="chart-wrapper"><canvas id="chartTahunan"></canvas></div>
        </div>
        <?php if($bulan && $bulan !== 'semua'): ?>
        <div class="chart-card">
            <h2><i class="bi bi-calendar-range text-success"></i> Pekerjaan per Hari (<?php echo e($daftarBulan[$bulan]); ?>)</h2>
            <div class="chart-wrapper"><canvas id="chartHarian"></canvas></div>
        </div>
        <?php endif; ?>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:end; margin: 24px 0 12px 0; padding: 0 4px;">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin:0;">Daftar Pekerjaan</h2>
        <span style="background: <?php echo e($primaryColor); ?>; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;"><?php echo e($jobs->count()); ?> Data</span>
    </div>

    <div class="grid-laporan">
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                // Tentukan warna card berdasarkan status
                $statusClass = $j->status === 'SELESAI' ? 'status-selesai' : 'status-pending';
                
                // 🆕 Tentukan URL tujuan (Laporan -> Detail dengan parameter from=laporan)
                $urlDetail = $isTeknisi 
                    ? route($rp . '.show', $j) . '?from=laporan' 
                    : route('admin.show', $j);
            ?>
            
            
            <div class="laporan-card <?php echo e($statusClass); ?>" onclick="window.location='<?php echo e($urlDetail); ?>'">
                
                
                <div>
                    <div class="d-flex justify-content-between align-items-start">
                        <span style="font-family:monospace; font-size:0.7rem; color:#888;"><i class="bi bi-hash"></i><?php echo e($j->kode_job); ?></span>
                        
                        <span class="badge <?php echo e($j->statusBadgeClass()); ?> d-md-none"><?php echo e($j->status); ?></span>
                    </div>
                    <div class="lap-title"><?php echo e($j->judul); ?></div>
                    <div class="lap-meta">
                        <span><i class="bi bi-person-circle text-primary"></i> <?php echo e($j->teknisi->display_name ?? '-'); ?></span>
                        <span><i class="bi bi-calendar-check text-success"></i> <?php echo e($j->tanggal_selesai?->format('d M Y') ?? $j->tanggal_tugas?->format('d M Y')); ?></span>
                    </div>
                </div>

                
                <div class="lap-footer">
                    <span class="badge <?php echo e($j->bidangBadgeClass()); ?> d-none d-md-inline" style="font-size:0.65rem;"><i class="bi <?php echo e($j->bidangIcon()); ?>"></i> <?php echo e($j->bidangLabel()); ?></span>
                    <span class="badge <?php echo e($j->statusBadgeClass()); ?> d-none d-md-inline"><?php echo e($j->status); ?></span>
                    <span class="btn-aksi">Detail <i class="bi bi-chevron-right"></i></span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <h3 style="font-size: 1rem; font-weight: 800; margin-top: 12px; color: #333;">Tidak Ada Data</h3>
            </div>
        <?php endif; ?>
    </div>

</div>


<?php if($isTeknisi): ?>
<div class="bottom-nav">
    <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="bottom-nav-item"><i class="bi bi-house-door-fill"></i><span>Beranda</span></a>
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="bottom-nav-item"><i class="bi bi-plus-square"></i><span>Buat</span></a>
    <a href="<?php echo e(route('laporan.index')); ?>" class="bottom-nav-item active"><i class="bi bi-bar-chart"></i><span>Laporan</span></a>
    <a href="<?php echo e(route('profile.edit')); ?>" class="bottom-nav-item"><i class="bi bi-person-circle"></i><span>Profil</span></a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctxTahunan = document.getElementById('chartTahunan');
    if (ctxTahunan) {
        new Chart(ctxTahunan, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartTahunan['labels'], 15, 512) ?>,
                datasets: [
                    { label: 'Total', data: <?php echo json_encode($chartTahunan['data'], 15, 512) ?>, backgroundColor: 'rgba(13, 110, 253, 0.8)', borderRadius: 5 },
                    { label: 'Selesai', data: <?php echo json_encode($chartTahunan['selesai'], 15, 512) ?>, backgroundColor: 'rgba(25, 135, 84, 0.8)', borderRadius: 5 }
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
                labels: <?php echo json_encode($chartHarian['labels'], 15, 512) ?>,
                datasets: [
                    { label: 'Total', data: <?php echo json_encode($chartHarian['data'], 15, 512) ?>, backgroundColor: 'rgba(13, 110, 253, 0.8)', borderRadius: 4 },
                    { label: 'Selesai', data: <?php echo json_encode($chartHarian['selesai'], 15, 512) ?>, backgroundColor: 'rgba(25, 135, 84, 0.8)', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app_pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/index.blade.php ENDPATH**/ ?>