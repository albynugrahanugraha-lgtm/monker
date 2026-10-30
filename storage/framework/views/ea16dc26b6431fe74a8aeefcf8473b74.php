
<?php $__env->startSection('title', 'Dashboard Teknisi ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer')); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ============================================
       MEMAKSA LAYOUT FULL WIDTH (KANAN-KIRI)
       ============================================ */
    /* Menimpa class .container dari layouts.app agar full 100% */
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    /* ============================================
       TABEL PEKERJAAN — SUPER TIPIS (DESKTOP)
       ============================================ */
    .table-job { 
        font-size: .65rem; 
        margin-bottom: 0;
        width: 100%;
    }
    .table-job th { 
        background: #f8f9fa; 
        font-weight: 600; 
        color: #495057; 
        white-space: nowrap; 
        padding: 3px 5px; 
        font-size: .55rem;
        text-transform: uppercase;
        border-bottom: 1px solid #dee2e6;
    }
    .table-job td { 
        padding: 3px 5px; /* 👈 Spasi sel tipis */
        vertical-align: middle; 
        line-height: 1.1;
    }
    .table-job code { 
        background: #f1f3f5; 
        padding: 0px 3px; 
        border-radius: 2px; 
        color: #495057; 
        font-size: .55rem;
        white-space: nowrap; 
    }

    /* 👈 TEKS JUDUL & LOKASI DIBUAT SANGAT KECIL */
    .table-job .job-title {
        font-weight: 600;
        font-size: .65rem; 
        color: #212529;
        margin-bottom: 0px;
        line-height: 1.05;
    }
    .table-job .job-meta {
        font-size: .55rem; 
        color: #6c757d;
        line-height: 1.05;
    }
    .table-job .job-meta i {
        font-size: .5rem;
    }

    tr.row-clickable { cursor: pointer; transition: background-color .15s; }
    tr.row-clickable:hover { background-color: #f0f7ff; box-shadow: inset 2px 0 0 0 #0d6efd; }
    
    .arrow-icon { color: #adb5bd; font-size: .7rem; transition: transform .15s, color .15s; }
    tr.row-clickable:hover .arrow-icon { color: #0d6efd; transform: translateX(2px); }

    /* Kelap-kelip */
    @keyframes blinkRowBg { 0% { background-color: #ffeb3b; } 50% { background-color: #ffffff; } 100% { background-color: #ffeb3b; } }
    tr.row-belum-dikerjakan td { animation: blinkRowBg 1s infinite ease-in-out !important; font-weight: 600 !important; }
    tr.row-belum-dikerjakan { border-left: 2px solid #dc3545; }

    @keyframes blinkBadge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.05); } }
    .badge-blinking { animation: blinkBadge 0.8s infinite ease-in-out !important; box-shadow: 0 0 4px rgba(220, 53, 69, 0.5); }

    .exclaim { display: inline-block; animation: blinkExclaim 0.6s infinite !important; color: #dc3545; font-weight: bold; }
    @keyframes blinkExclaim { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    .badge-tanggal-sm { font-size: .45rem; padding: 0px 2px; font-weight: 600; }

    /* ============================================
       TAMPILAN MOBILE (HP) — CARD MODERN
       ============================================ */
    .job-card-mobile { display: none; }

    @media (max-width: 767.98px) {
        .table-job-desktop { display: none !important; }
        .job-card-mobile { display: block; padding: 2px; }

        .job-card {
            border: 1px solid #e2e6ea;
            border-radius: 6px;
            padding: 6px 8px;
            margin-bottom: 6px;
            background: #fff;
            cursor: pointer;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            transition: transform 0.1s, box-shadow 0.1s;
        }
        .job-card:active { transform: scale(0.98); box-shadow: none; background: #f8f9fa; }
        .job-card.row-belum-dikerjakan { border-left: 3px solid #dc3545; background: #fffdf5; }

        .job-card .jc-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
        .job-card .jc-kode { font-size: .55rem; font-family: 'Courier New', monospace; background: #eef1f5; padding: 1px 4px; border-radius: 3px; color: #495057; font-weight: 700; }
        .job-card .jc-badge-status { font-size: .5rem; padding: 2px 5px; border-radius: 4px; font-weight: 700; letter-spacing: 0.2px; }

        .job-card .jc-title { font-weight: 700; font-size: .75rem; line-height: 1.15; color: #212529; margin-bottom: 2px; }
        .job-card .jc-meta { font-size: .6rem; color: #6c757d; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .job-card .jc-meta i { color: #adb5bd; margin-right: 2px; }

        .job-card .jc-badges { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 5px; padding-top: 5px; border-top: 1px dashed #e9ecef; }
        .job-card .jc-badge { font-size: .5rem; padding: 1px 5px; border-radius: 4px; font-weight: 600; display: inline-flex; align-items: center; gap: 2px; }

        .btn-sm-mobile { padding: 3px 8px !important; font-size: .65rem !important; }
        .header-title-mobile { font-size: .9rem !important; }
        
        /* Card Riwayat di Mobile */
        .table-riwayat thead { display: none; }
        .table-riwayat tbody tr { display: block; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 6px; padding: 4px; }
        .table-riwayat tbody td { display: block; border: none; padding: 2px 4px !important; text-align: left; }
        .table-riwayat tbody td:last-child { display: none; }
    }

    /* ============================================
       RIWAYAT SELESAI — TABEL (UKURAN NORMAL/AGAK BESAR)
       ============================================ */
    .table-riwayat { font-size: .8rem; margin-bottom: 0; }
    .table-riwayat thead th { background: #f8f9fa; color: #495057; font-weight: 600; font-size: .68rem; text-transform: uppercase; padding: 6px 8px; white-space: nowrap; border-bottom: 2px solid #dee2e6; }
    .table-riwayat tbody tr { cursor: pointer; transition: all 0.15s ease; border-left: 3px solid transparent; }
    .table-riwayat tbody tr:hover { background: #e8f5e9 !important; border-left-color: #198754; }
    .table-riwayat tbody td { padding: 8px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
    .table-riwayat .judul-riwayat { color: #212529; font-weight: 600; font-size: .82rem; margin-bottom: 1px; line-height: 1.3; }
    .table-riwayat .kode-riwayat { color: #6c757d; font-size: .68rem; font-family: 'Courier New', monospace; background: #f1f3f5; padding: 1px 5px; border-radius: 3px; display: inline-block; }
    .table-riwayat .meta-riwayat { color: #6c757d; font-size: .68rem; margin-top: 1px; }
    .table-riwayat .badge-selesai-riwayat { background: #198754; color: white; font-size: .6rem; padding: 2px 7px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px; }
    .table-riwayat .waktu-riwayat { color: #6c757d; font-size: .68rem; font-family: 'Courier New', monospace; white-space: nowrap; }

    /* ============================================
       MODAL (POP-UP) RESPONSIVE
       ============================================ */
    .modal-content { border: none; border-radius: 10px; }
    .modal-header { border-radius: 10px 10px 0 0; padding: 8px 12px; }
    .modal-body { padding: 12px; }
    .modal-footer { padding: 8px 12px; border-top: 1px solid #e9ecef; }
    .modal-info-table { font-size: .75rem; }
    .modal-info-table th { width: 40%; color: #6c757d; font-weight: 500; padding: 2px 0; vertical-align: top; }
    .modal-info-table td { padding: 2px 0; vertical-align: top; }
    
    @media (max-width: 575.98px) {
        .modal-info-table { font-size: .7rem; }
        .modal-body { padding: 10px; }
        .modal-header { padding: 8px 10px; }
        .modal-title { font-size: .85rem !important; }
        .modal-footer { padding: 10px; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold header-title-mobile" style="font-size: 1.1rem;">
            <i class="bi <?php echo e($isCctv ? 'bi-camera-video' : 'bi-pc-display'); ?>"></i>
            Dashboard <?php echo e($isCctv ? 'CCTV' : 'Komputer'); ?>

        </h5>
        <small class="text-muted d-block" style="font-size: .75rem;">Halo, <?php echo e(auth()->user()->display_name); ?> 👋</small>
    </div>
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="btn btn-primary btn-sm py-1 px-2 btn-sm-mobile" style="font-size: .8rem;">
        <i class="bi bi-plus-circle"></i> Buat Tugas
    </a>
</div>


<div class="row g-2 mb-3">
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-2 d-flex flex-column align-items-center justify-content-center text-center">
                <small class="text-muted" style="font-size: .65rem;">📥 Total</small>
                <h5 class="mb-0 fw-bold" style="font-size: 1.1rem;"><?php echo e($hariIni['total']); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-2 d-flex flex-column align-items-center justify-content-center text-center">
                <small class="text-muted" style="font-size: .65rem;">✅ Selesai</small>
                <h5 class="mb-0 fw-bold text-success" style="font-size: 1.1rem;"><?php echo e($hariIni['selesai']); ?></h5>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-2 d-flex flex-column align-items-center justify-content-center text-center">
                <small class="text-muted" style="font-size: .65rem;">⏳ Belum</small>
                <h5 class="mb-0 fw-bold text-danger" style="font-size: 1.1rem;"><?php echo e($hariIni['belum']); ?></h5>
            </div>
        </div>
    </div>
</div>




<div class="card shadow-sm mb-3 border-0">
    <div class="card-header bg-danger text-white py-2 px-3 d-flex justify-content-between align-items-center">
        
        <h6 class="mb-0 fw-bold" style="font-size: .9rem;">
            <i class="bi bi-exclamation-circle-fill"></i>
            Pekerjaan yang Harus Dikerjakan
            <span class="badge bg-light text-dark ms-1" style="font-size: .7rem; padding: 2px 6px;"><?php echo e($jobs->count()); ?></span>
        </h6>
        <small class="d-none d-md-inline" style="opacity: .8; font-size: .7rem;">
            <i class="bi bi-info-circle"></i> Klik untuk detail
        </small>
    </div>

    
    <div class="table-responsive table-job-desktop">
    <table class="table table-hover align-middle mb-0 table-job">
        <thead>
            <tr>
                <th style="width: 75px;">Kode</th>
                <th>Judul & Lokasi</th>
                <th style="width: 60px;">Prioritas</th>
                <th style="width: 70px;">Waktu</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 15px;"></th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $badgeClass = match($j->status) {
                    'DITUGASKAN' => 'bg-secondary',
                    'DIKERJAKAN' => 'bg-warning text-dark',
                    default => 'bg-light text-dark',
                };
                $prioritasColor = match($j->prioritas) {
                    'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary',
                };
                $isBelum = $j->status === 'DITUGASKAN';
                $isBebas = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $rowClass = 'row-clickable';
                if ($isBelum) $rowClass .= ' row-belum-dikerjakan';

                $tglTugas = $j->tanggal_tugas;
                $tglHariIni = \Carbon\Carbon::today();
                $keteranganTanggal = '';
                $badgeTanggalClass = '';

                if ($tglTugas) {
                    if ($tglTugas->isToday()) { $keteranganTanggal = 'Hari Ini'; $badgeTanggalClass = 'bg-success'; }
                    elseif ($tglTugas->isYesterday()) { $keteranganTanggal = 'Kemarin'; $badgeTanggalClass = 'bg-warning text-dark'; }
                    elseif ($tglTugas->isPast()) { $selisih = (int) $tglTugas->diffInDays($tglHariIni); $keteranganTanggal = $selisih . ' Hari Lalu'; $badgeTanggalClass = 'bg-danger'; }
                    else { $keteranganTanggal = $tglTugas->format('d M'); $badgeTanggalClass = 'bg-info text-dark'; }
                }
            ?>

            <tr class="<?php echo e($rowClass); ?> job-row" data-status="<?php echo e($j->status); ?>" data-id="<?php echo e($j->id); ?>">
                <td>
                    <code><?php echo e($j->kode_job); ?></code>
                    <?php if($isBelum): ?> <span class="exclaim">!</span> <?php endif; ?>
                </td>
                <td>
                    <div class="job-title"><?php echo e($j->judul); ?></div>
                    <div class="job-meta">
                        <i class="bi bi-geo-alt-fill"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?>

                        <?php if($keteranganTanggal): ?>
                            <span class="badge <?php echo e($badgeTanggalClass); ?> badge-tanggal-sm ms-1"><?php echo e($keteranganTanggal); ?></span>
                        <?php endif; ?>
                    </div>
                </td>
                <td><span class="badge bg-<?php echo e($prioritasColor); ?>" style="font-size: .5rem;"><?php echo e($j->prioritas); ?></span></td>
                <td>
                    <div style="font-size: .55rem; color: #6c757d; white-space: nowrap;">
                        <i class="bi bi-clock text-primary"></i> <?php echo e($j->created_at->format('d/m H:i')); ?>

                    </div>
                </td>
                <td>
                    <span class="badge <?php echo e($badgeClass); ?> <?php echo e($isBelum ? 'badge-blinking' : ''); ?>" style="font-size: .5rem;">
                        <?php echo e(str_replace('_', ' ', $j->status)); ?>

                    </span>
                </td>
                <td class="text-center"><i class="bi bi-chevron-right arrow-icon"></i></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-1">
                    <div class="mt-1" style="font-size: .65rem;">Tidak ada pekerjaan. 👍</div>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    
    <div class="job-card-mobile">
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $badgeClass = match($j->status) {
                    'DITUGASKAN' => 'bg-secondary text-white',
                    'DIKERJAKAN' => 'bg-warning text-dark',
                    default => 'bg-light text-dark',
                };
                $prioritasColor = match($j->prioritas) {
                    'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary',
                };
                $isBelum = $j->status === 'DITUGASKAN';
                $isBebas = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $cardClass = 'job-card';
                if ($isBelum) $cardClass .= ' row-belum-dikerjakan';

                $tglTugas = $j->tanggal_tugas;
                $tglHariIni = \Carbon\Carbon::today();
                $keteranganTanggal = '';
                $badgeTanggalClass = '';
                if ($tglTugas) {
                    if ($tglTugas->isToday()) { $keteranganTanggal = 'Hari Ini'; $badgeTanggalClass = 'bg-success text-white'; }
                    elseif ($tglTugas->isYesterday()) { $keteranganTanggal = 'Kemarin'; $badgeTanggalClass = 'bg-warning text-dark'; }
                    elseif ($tglTugas->isPast()) { $selisih = (int) $tglTugas->diffInDays($tglHariIni); $keteranganTanggal = $selisih . ' Hari Lalu'; $badgeTanggalClass = 'bg-danger text-white'; }
                    else { $keteranganTanggal = $tglTugas->format('d M'); $badgeTanggalClass = 'bg-info text-dark'; }
                }
            ?>
            <div class="<?php echo e($cardClass); ?>" data-id="<?php echo e($j->id); ?>" onclick="openModal(<?php echo e($j->id); ?>)">
                <div class="jc-header">
                    <div>
                        <span class="jc-kode"><?php echo e($j->kode_job); ?></span>
                        <?php if($isBelum): ?> <span class="exclaim" style="font-size:.7rem;">!</span> <?php endif; ?>
                    </div>
                    <span class="badge <?php echo e($badgeClass); ?> jc-badge-status <?php echo e($isBelum ? 'badge-blinking' : ''); ?>">
                        <?php echo e(str_replace('_', ' ', $j->status)); ?>

                    </span>
                </div>
                
                <div class="jc-title"><?php echo e($j->judul); ?></div>
                
                <div class="jc-meta">
                    <i class="bi bi-geo-alt-fill"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?>

                </div>
                
                <div class="jc-badges">
                    <span class="jc-badge bg-<?php echo e($prioritasColor); ?> text-white"><i class="bi bi-flag-fill"></i> <?php echo e($j->prioritas); ?></span>
                    
                    <?php if($keteranganTanggal): ?>
                        <span class="jc-badge <?php echo e($badgeTanggalClass); ?>"><i class="bi bi-calendar-event"></i> <?php echo e($keteranganTanggal); ?></span>
                    <?php endif; ?>
                    
                    <?php if($isBebas): ?>
                        <span class="jc-badge bg-info text-dark"><i class="bi bi-hand-index-thumb"></i> Bebas</span>
                    <?php endif; ?>
                    
                    <span class="jc-badge bg-light text-secondary border">
                        <i class="bi bi-clock"></i> <?php echo e($j->created_at->format('d/m H:i')); ?>

                    </span>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-muted py-3">
                <i class="bi bi-check-circle" style="font-size: 1.5rem; color: #198754; opacity: .5;"></i>
                <div class="mt-1" style="font-size: .75rem;">Tidak ada pekerjaan. 👍</div>
            </div>
        <?php endif; ?>
    </div>
</div>




<?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="modal fade" id="modalAksi<?php echo e($j->id); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white
                    <?php if($j->status === 'DITUGASKAN'): ?> bg-primary
                    <?php elseif($j->status === 'DIKERJAKAN'): ?> bg-warning
                    <?php else: ?> bg-success <?php endif; ?>">
                    <h6 class="modal-title"><i class="bi bi-clipboard-check"></i> <?php echo e($j->kode_job); ?></h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-2" style="font-size: .85rem;"><?php echo e($j->judul); ?></h6>

                    <table class="table modal-info-table mb-2">
                        <tr><th>Lokasi</th><td><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td></tr>
                        <tr><th>Perangkat</th><td><?php echo e($j->computer_name ?? '-'); ?></td></tr>
                        <tr><th>Kategori</th><td><?php echo e($j->kategori ?? 'Umum'); ?></td></tr>
                        <tr>
                            <th>Prioritas</th>
                            <td>
                                <?php $pc = match($j->prioritas) { 'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary' }; ?>
                                <span class="badge bg-<?php echo e($pc); ?>"><?php echo e($j->prioritas); ?></span>
                            </td>
                        </tr>
                        <tr><th>Tanggal</th><td><?php echo e($j->tanggal_tugas?->format('d M Y') ?? '-'); ?></td></tr>
                    </table>
                </div>

                <div class="modal-footer flex-column gap-1">
                    <?php if($j->status === 'DITUGASKAN'): ?>
                        <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-play-circle"></i> Kerjakan
                        </a>
                    <?php elseif($j->status === 'DIKERJAKAN'): ?>
                        <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-check-circle"></i> Selesaikan
                        </a>
                    <?php endif; ?>
                    
                    <div class="d-flex w-100 gap-1 mt-1">
                        <a href="<?php echo e(route($rp . '.show', $j)); ?>" class="btn btn-sm btn-outline-primary w-50">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <a href="<?php echo e(route($rp . '.cetak', $j)); ?>" target="_blank" class="btn btn-sm btn-outline-secondary w-50">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-light w-100 mt-1" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>




<div class="card shadow-sm border-0">
    <div class="card-header bg-success text-white py-2 px-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0" style="font-size: .85rem;">
            <i class="bi bi-check-circle-fill"></i> Riwayat Selesai
            <span class="badge bg-light text-success ms-1" style="font-size: .65rem; padding: 1px 5px;"><?php echo e($jobsSelesai->count()); ?></span>
        </h6>

        <form method="GET" action="<?php echo e(route($rp . '.dashboard')); ?>" class="d-flex gap-1 align-items-center mb-0" style="font-size: .7rem;">
            <input type="radio" class="btn-check" name="periode" id="p_harian" value="harian" <?php if($periode === 'harian'): echo 'checked'; endif; ?> onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_harian" style="font-size: .65rem;">Harian</label>

            <input type="radio" class="btn-check" name="periode" id="p_mingguan" value="mingguan" <?php if($periode === 'mingguan'): echo 'checked'; endif; ?> onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_mingguan" style="font-size: .65rem;">Minggu</label>

            <input type="radio" class="btn-check" name="periode" id="p_bulanan" value="bulanan" <?php if($periode === 'bulanan'): echo 'checked'; endif; ?> onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_bulanan" style="font-size: .65rem;">Bulan</label>

            <input type="radio" class="btn-check" name="periode" id="p_semua" value="semua" <?php if($periode === 'semua'): echo 'checked'; endif; ?> onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_semua" style="font-size: .65rem;">Semua</label>
        </form>
    </div>

    <?php if($jobsSelesai->count() > 0): ?>
    <div class="table-responsive">
        <table class="table table-riwayat align-middle">
            <thead>
                <tr>
                    <th style="width: 40%;">Pekerjaan</th>
                    <th style="width: 22%;">Lokasi</th>
                    <th style="width: 15%;" class="text-center">Status</th>
                    <th style="width: 18%;">Waktu Selesai</th>
                    <th style="width: 5%;" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $jobsSelesai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr onclick="window.location='<?php echo e(route($rp . '.show', $j)); ?>'">
                        <td>
                            <div class="judul-riwayat"><?php echo e($j->judul); ?></div>
                            <span class="kode-riwayat"><?php echo e($j->kode_job); ?></span>
                        </td>
                        <td>
                            <div class="meta-riwayat"><?php echo e($j->lok_gedung ?? '-'); ?></div>
                            <div class="meta-riwayat"><?php echo e($j->lokasi_ruang ?? '-'); ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge-selesai-riwayat"><i class="bi bi-check-circle-fill"></i> Selesai</span>
                        </td>
                        <td>
                            <div class="waktu-riwayat"><?php echo e($j->tanggal_selesai?->format('d/m/y') ?? '-'); ?></div>
                        </td>
                        <td class="text-center">
                            <i class="bi bi-chevron-right arrow-riwayat"></i>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="card-body text-center text-muted py-3">
        <p class="mt-1 mb-0" style="font-size: .75rem;">Belum ada riwayat.</p>
    </div>
    <?php endif; ?>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function openModal(id) {
        const modalEl = document.getElementById('modalAksi' + id);
        if (modalEl) new bootstrap.Modal(modalEl).show();
    }

    document.querySelectorAll('tr.row-clickable').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) return;
            const id = row.dataset.id;
            if (id) {
                const modalEl = document.getElementById('modalAksi' + id);
                if (modalEl) new bootstrap.Modal(modalEl).show();
            }
        });
    });

    setInterval(function() {
        document.querySelectorAll('tr.row-belum-dikerjakan').forEach(function(row, idx) {
            const offset = idx * 200;
            const phase = (Date.now() + offset) % 1000;
            const isYellow = phase < 500;
            row.querySelectorAll('td').forEach(function(td) {
                td.style.transition = 'background-color 0.3s ease';
                td.style.backgroundColor = isYellow ? '#ffeb3b' : '#ffffff';
            });
        });
    }, 500);

    setInterval(function() {
        document.querySelectorAll('.job-card.row-belum-dikerjakan').forEach(function(card, idx) {
            const phase = (Date.now() + idx * 200) % 1000;
            const isYellow = phase < 500;
            card.style.transition = 'background-color 0.3s ease';
            card.style.backgroundColor = isYellow ? '#fff8c4' : '#fffef5';
        });
    }, 500);
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/dashboard.blade.php ENDPATH**/ ?>