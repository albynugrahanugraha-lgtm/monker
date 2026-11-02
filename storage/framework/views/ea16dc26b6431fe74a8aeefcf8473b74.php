
<?php $__env->startSection('title', 'Dashboard Teknisi ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer')); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ============================================
       TABEL PEKERJAAN — FULL WIDTH & RAPIH
       ============================================ */
    .table-job { 
        width: 100% !important;
        font-size: .85rem !important; 
        margin-bottom: 0 !important;
        border-color: #dee2e6 !important;
    }
    
    /* Header Tabel */
    .table-job th { 
        background-color: #f8f9fa !important; 
        font-weight: 600 !important; 
        color: #495057 !important; 
        white-space: nowrap !important; 
        padding: 10px 8px !important; 
        font-size: .75rem !important;
        text-transform: uppercase !important;
        vertical-align: middle !important;
        border-bottom: 2px solid #dee2e6 !important;
    }
    
    /* Sel Tabel (td) — Dipaksa Rapi */
    .table-job td { 
        padding: 12px 10px !important; 
        vertical-align: middle !important; 
        border-bottom: 1px solid #e9ecef !important;
        font-size: .85rem !important; 
        color: #212529 !important;
    }
    
    .table-job code { 
        background: #f1f3f5 !important; 
        padding: 3px 6px !important; 
        border-radius: 4px !important; 
        color: #495057 !important; 
        font-size: .78rem !important; 
        border: 1px solid #e9ecef !important;
    }

    /* Efek Hover Baris */
    tr.row-clickable { 
        cursor: pointer !important; 
        transition: background-color .2s !important; 
    }
    tr.row-clickable:hover { 
        background-color: #f0f7ff !important; 
        box-shadow: inset 4px 0 0 0 #0d6efd !important; 
    }

    /* Icon Panah */
    .arrow-icon { 
        color: #adb5bd !important; 
        font-size: 1.2rem !important; 
        transition: transform .2s, color .2s !important; 
    }
    tr.row-clickable:hover .arrow-icon { 
        color: #0d6efd !important; 
        transform: translateX(5px) !important; 
    }

    /* Animasi Kelap-kelip (Blinking) */
    @keyframes blinkRowBg { 
        0% { background-color: #ffeb3b; } 
        50% { background-color: #ffffff; } 
        100% { background-color: #ffeb3b; } 
    }
    tr.row-belum-dikerjakan td { 
        animation: blinkRowBg 1s infinite ease-in-out !important; 
        font-weight: 600 !important; 
    }
    tr.row-belum-dikerjakan { 
        border-left: 5px solid #dc3545 !important; 
    }

    @keyframes blinkBadge { 
        0%, 100% { transform: scale(1); } 
        50% { transform: scale(1.15); } 
    }
    .badge-blinking { 
        animation: blinkBadge 0.7s infinite ease-in-out !important; 
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.8) !important; 
    }

    .exclaim { 
        display: inline-block !important; 
        animation: blinkExclaim 0.6s infinite !important; 
        color: #dc3545 !important; 
        font-weight: bold !important; 
    }
    @keyframes blinkExclaim { 
        0%, 100% { opacity: 1; } 
        50% { opacity: 0.3; } 
    }

    .badge-tanggal-sm { 
        font-size: 0.65rem !important; 
        padding: 2px 6px !important; 
        font-weight: 600 !important; 
        vertical-align: middle !important;
    }

    /* ============================================
       TABEL RIWAYAT SELESAI
       ============================================ */
    .table-riwayat {
        font-size: .82rem !important;
        margin-bottom: 0 !important;
    }
    .table-riwayat thead th {
        background: #f8f9fa !important;
        color: #495057 !important;
        font-weight: 600 !important;
        font-size: .72rem !important;
        text-transform: uppercase !important;
        letter-spacing: .3px !important;
        padding: 8px 10px !important;
        white-space: nowrap !important;
        border-bottom: 2px solid #dee2e6 !important;
    }
    .table-riwayat tbody tr {
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        border-left: 3px solid transparent !important;
    }
    .table-riwayat tbody tr:hover {
        background: #e8f5e9 !important;
        border-left-color: #198754 !important;
    }
    .table-riwayat tbody tr:hover .arrow-riwayat {
        transform: translateX(4px) !important;
        color: #0d6efd !important;
    }
    .table-riwayat tbody td {
        padding: 10px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #f0f0f0 !important;
    }
    .table-riwayat .judul-riwayat {
        color: #212529 !important;
        font-weight: 600 !important;
        font-size: .85rem !important;
        margin-bottom: 2px !important;
        line-height: 1.3 !important;
    }
    .table-riwayat .kode-riwayat {
        color: #6c757d !important;
        font-size: .7rem !important;
        font-family: 'Courier New', monospace !important;
        background: #f1f3f5 !important;
        padding: 2px 6px !important;
        border-radius: 3px !important;
        display: inline-block !important;
    }
    .table-riwayat .meta-riwayat {
        color: #6c757d !important;
        font-size: .72rem !important;
        margin-top: 3px !important;
    }
    .table-riwayat .meta-riwayat i {
        color: #6c757d !important;
        margin-right: 2px !important;
    }
    .table-riwayat .badge-selesai-riwayat {
        background: #198754 !important;
        color: white !important;
        font-size: .62rem !important;
        padding: 3px 8px !important;
        border-radius: 10px !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 3px !important;
    }
    .table-riwayat .waktu-riwayat {
        color: #6c757d !important;
        font-size: .7rem !important;
        font-family: 'Courier New', monospace !important;
        white-space: nowrap !important;
    }
    .table-riwayat .arrow-riwayat {
        color: #adb5bd !important;
        font-size: 1.1rem !important;
        transition: transform 0.15s, color 0.15s !important;
    }

    /* ============================================
       MODAL
       ============================================ */
    .modal-content { 
        border: none !important; 
        border-radius: 12px !important; 
    }
    .modal-header { 
        border-radius: 12px 12px 0 0 !important; 
        padding: 12px 16px !important; 
    }
    .modal-body { 
        padding: 16px !important; 
    }
    .modal-footer { 
        padding: 12px 16px !important; 
        border-top: 1px solid #e9ecef !important; 
    }
    .modal-info-table { 
        font-size: .85rem !important; 
    }
    .modal-info-table th { 
        width: 40% !important; 
        color: #6c757d !important; 
        font-weight: 500 !important; 
        padding: 4px 0 !important; 
        vertical-align: top !important; 
    }
    .modal-info-table td { 
        padding: 4px 0 !important; 
        vertical-align: top !important; 
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold">
            <i class="bi <?php echo e($isCctv ? 'bi-camera-video' : 'bi-pc-display'); ?>"></i>
            Dashboard Teknisi <?php echo e($isCctv ? 'CCTV' : 'Komputer'); ?>

        </h5>
        <small class="text-muted" style="font-size: .75rem;">Halo, <?php echo e(auth()->user()->display_name); ?> 👋</small>
    </div>
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="btn btn-primary btn-sm py-1 px-2" style="font-size: .8rem;">
        <i class="bi bi-plus-circle"></i> Buat Tugas
    </a>
</div>


<div class="row g-2 g-md-3 mb-3">
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">📥 Total</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['total']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">✅ Selesai</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['selesai']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">⏳ Belum</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['belum']); ?></h3>
            </div>
        </div>
    </div>
</div>




<div class="card shadow-sm mb-3">
    <div class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-exclamation-circle-fill"></i>
            Pekerjaan yang Harus Dikerjakan
            <span class="badge bg-light text-dark ms-2"><?php echo e($jobs->count()); ?></span>
        </h6>
        <small class="d-none d-md-inline" style="opacity: .8;">
            <i class="bi bi-info-circle"></i> Klik baris untuk detail
        </small>
    </div>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 table-job">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul & Lokasi</th>
                <th>Prioritas</th>
                <th>Waktu</th>
                <th>Status</th>
                <th class="text-center" style="width: 50px;"></th>
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
                $isProses = $j->status === 'DIKERJAKAN';
                $isBebas = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $rowClass = 'row-clickable';
                if ($isBelum) $rowClass .= ' row-belum-dikerjakan';
                if ($isProses) $rowClass .= ' row-dikerjakan';

                // LOGIKA KETERANGAN TANGGAL (HARI INI / KEMARIN)
                $tglTugas = $j->tanggal_tugas;
                $tglHariIni = \Carbon\Carbon::today();
                $keteranganTanggal = '';
                $badgeTanggalClass = '';

                if ($tglTugas) {
                    if ($tglTugas->isToday()) { 
                        $keteranganTanggal = 'Hari Ini'; 
                        $badgeTanggalClass = 'bg-success text-white'; 
                    } elseif ($tglTugas->isYesterday()) { 
                        $keteranganTanggal = 'Kemarin'; 
                        $badgeTanggalClass = 'bg-warning text-dark'; 
                    } elseif ($tglTugas->isPast()) { 
                        $selisih = (int) $tglTugas->diffInDays($tglHariIni); 
                        $keteranganTanggal = $selisih . ' Hari Lalu'; 
                        $badgeTanggalClass = 'bg-danger text-white'; 
                    } else { 
                        $keteranganTanggal = $tglTugas->format('d M'); 
                        $badgeTanggalClass = 'bg-info text-dark'; 
                    }
                }
            ?>

            <tr class="<?php echo e($rowClass); ?> job-row" data-status="<?php echo e($j->status); ?>" data-id="<?php echo e($j->id); ?>">
                <td>
                    <code><?php echo e($j->kode_job); ?></code>
                    <?php if($isBelum): ?> <span class="exclaim">!</span> <?php endif; ?>
                    <?php if($isBebas): ?>
                        <br>
                        <span class="badge bg-info text-dark mt-1" style="font-size: .65rem;">
                            <i class="bi bi-hand-index-thumb"></i> Bebas Diambil
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="fw-semibold"><?php echo e($j->judul); ?></div>
                    <small class="text-muted">
                        <i class="bi bi-geo-alt-fill"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?>

                        
                        
                        <?php if($keteranganTanggal): ?>
                            <span class="badge <?php echo e($badgeTanggalClass); ?> badge-tanggal-sm ms-2"><?php echo e($keteranganTanggal); ?></span>
                        <?php endif; ?>
                    </small>
                    <br>
                    <small class="text-muted">
                        <i class="bi bi-pc-display"></i> <?php echo e($j->computer_name ?? '-'); ?>

                        <?php if($j->merk): ?> (<?php echo e($j->merk); ?>) <?php endif; ?>
                        · <i class="bi bi-tag-fill"></i> <?php echo e($j->kategori ?? 'Umum'); ?>

                    </small>
                </td>
                <td><span class="badge bg-<?php echo e($prioritasColor); ?>"><?php echo e($j->prioritas); ?></span></td>
                <td>
                    <div style="font-size: .72rem; color: #6c757d;">
                        <i class="bi bi-clock text-primary"></i> <strong><?php echo e($j->created_at->format('d/m H:i')); ?></strong>
                    </div>
                </td>
                <td>
                    <span class="badge <?php echo e($badgeClass); ?> <?php echo e($isBelum ? 'badge-blinking' : ''); ?>">
                        <?php echo e(str_replace('_', ' ', $j->status)); ?>

                    </span>
                </td>
                <td class="text-center"><i class="bi bi-chevron-right arrow-icon"></i></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; color: #198754; opacity: .5;"></i>
                    <div class="mt-2">Tidak ada pekerjaan yang harus dikerjakan. 👍</div>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
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
                    <h5 class="fw-bold mb-3"><?php echo e($j->judul); ?></h5>

                    <table class="table modal-info-table mb-3">
                        <tr><th><i class="bi bi-geo-alt-fill"></i> Lokasi</th><td><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td></tr>
                        <tr><th><i class="bi bi-pc-display"></i> Perangkat</th><td><?php echo e($j->computer_name ?? '-'); ?> <?php if($j->merk): ?> (<?php echo e($j->merk); ?>) <?php endif; ?></td></tr>
                        <tr><th><i class="bi bi-tag-fill"></i> Kategori</th><td><?php echo e($j->kategori ?? 'Umum'); ?></td></tr>
                        <tr>
                            <th><i class="bi bi-flag-fill"></i> Prioritas</th>
                            <td>
                                <?php $pc = match($j->prioritas) {
                                    'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary',
                                }; ?>
                                <span class="badge bg-<?php echo e($pc); ?>"><?php echo e($j->prioritas); ?></span>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-calendar-event"></i> Tanggal</th><td><?php echo e($j->tanggal_tugas?->format('d M Y') ?? '-'); ?></td></tr>
                        <tr><th><i class="bi bi-clock"></i> Dibuat</th><td><?php echo e($j->created_at->format('d M Y, H:i')); ?></td></tr>
                        <?php if($j->deskripsi): ?>
                            <tr><th><i class="bi bi-card-text"></i> Deskripsi</th><td><?php echo e($j->deskripsi); ?></td></tr>
                        <?php endif; ?>
                    </table>

                    <div class="text-center mb-2">
                        <span class="badge
                            <?php if($j->status === 'DITUGASKAN'): ?> bg-secondary
                            <?php elseif($j->status === 'DIKERJAKAN'): ?> bg-warning text-dark
                            <?php else: ?> bg-success <?php endif; ?>
                            py-2 px-3" style="font-size: .85rem;">
                            <i class="bi bi-info-circle"></i> <?php echo e(str_replace('_', ' ', $j->status)); ?>

                        </span>
                    </div>
                </div>

                <div class="modal-footer flex-column flex-sm-row gap-2">
                    <a href="<?php echo e(route($rp . '.show', $j)); ?>" class="btn btn-outline-primary w-100 w-sm-auto">
                        <i class="bi bi-eye"></i> Detail
                    </a>

                    <?php if($j->status === 'DITUGASKAN'): ?>
                        <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn btn-primary w-100 w-sm-auto">
                            <i class="bi bi-play-circle"></i> Kerjakan Sekarang
                        </a>
                    <?php elseif($j->status === 'DIKERJAKAN'): ?>
                        <a href="<?php echo e(route($rp . '.kerjakan', $j)); ?>" class="btn btn-success w-100 w-sm-auto">
                            <i class="bi bi-check-circle"></i> Selesaikan Pekerjaan
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route($rp . '.cetak', $j)); ?>" target="_blank" class="btn btn-outline-secondary w-100 w-sm-auto">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                    <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>




<?php if($jobsSelesai->count() > 0): ?>
<div class="card shadow-sm">
    <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-check-circle-fill"></i> Riwayat Selesai
            <span class="badge bg-light text-success ms-1"><?php echo e($jobsSelesai->count()); ?></span>
        </h6>
        <small style="opacity: .85;">
            <i class="bi bi-info-circle"></i> Klik baris untuk lihat detail
        </small>
    </div>
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
                            <div class="judul-riwayat">
                                <?php echo e($j->judul); ?>

                            </div>
                            <span class="kode-riwayat">
                                <i class="bi bi-hash"></i><?php echo e($j->kode_job); ?>

                            </span>
                        </td>

                        
                        <td>
                            <div class="meta-riwayat">
                                <i class="bi bi-geo-alt-fill"></i>
                                <?php echo e($j->lok_gedung ?? '-'); ?>

                            </div>
                            <div class="meta-riwayat">
                                <i class="bi bi-door-open"></i>
                                <?php echo e($j->lokasi_ruang ?? '-'); ?>

                            </div>
                        </td>

                        
                        <td class="text-center">
                            <span class="badge-selesai-riwayat">
                                <i class="bi bi-check-circle-fill"></i> Selesai
                            </span>
                        </td>

                        
                        <td>
                            <div class="waktu-riwayat">
                                <i class="bi bi-calendar-event"></i>
                                <?php echo e($j->tanggal_selesai?->format('d/m/Y') ?? '-'); ?>

                            </div>
                            <div class="waktu-riwayat">
                                <i class="bi bi-clock"></i>
                                <?php echo e($j->tanggal_selesai?->format('H:i') ?? '-'); ?>

                            </div>
                        </td>

                        
                        <td class="text-center">
                            <i class="bi bi-chevron-right arrow-riwayat"></i>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Klik baris pekerjaan → buka modal
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

    // Efek blinking untuk row belum dikerjakan
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
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/dashboard.blade.php ENDPATH**/ ?>