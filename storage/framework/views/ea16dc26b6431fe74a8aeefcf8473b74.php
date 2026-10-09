
<?php $__env->startSection('title', 'Dashboard Teknisi ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer')); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startPush('styles'); ?>
<style>
    .table-job { font-size: .85rem; }
    .table-job th { background: #f8f9fa; font-weight: 600; color: #495057; white-space: nowrap; padding: 10px 8px; }
    .table-job td { padding: 10px 8px; vertical-align: middle; }
    .table-job code { background: #f1f3f5; padding: 2px 6px; border-radius: 4px; color: #495057; font-size: .78rem; }

    tr.row-clickable { cursor: pointer; transition: background-color .2s; }
    tr.row-clickable:hover { background-color: #f0f7ff; box-shadow: inset 4px 0 0 0 #0d6efd; }

    .arrow-icon { color: #adb5bd; font-size: 1.2rem; transition: transform .2s, color .2s; }
    tr.row-clickable:hover .arrow-icon { color: #0d6efd; transform: translateX(5px); }

    @keyframes blinkRowBg { 0% { background-color: #ffeb3b; } 50% { background-color: #ffffff; } 100% { background-color: #ffeb3b; } }
    tr.row-belum-dikerjakan td { animation: blinkRowBg 1s infinite ease-in-out !important; font-weight: 600 !important; }
    tr.row-belum-dikerjakan { border-left: 5px solid #dc3545; }

    @keyframes blinkBadge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
    .badge-blinking { animation: blinkBadge 0.7s infinite ease-in-out !important; box-shadow: 0 0 10px rgba(220, 53, 69, 0.8); }

    .exclaim { display: inline-block; animation: blinkExclaim 0.6s infinite !important; color: #dc3545; font-weight: bold; }
    @keyframes blinkExclaim { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    .job-selesai-item { border-left: 4px solid #198754; background: #f8fff9; padding: 10px 12px; border-radius: 6px; margin-bottom: 8px; }

    .modal-content { border: none; border-radius: 12px; }
    .modal-header { border-radius: 12px 12px 0 0; padding: 12px 16px; }
    .modal-body { padding: 16px; }
    .modal-footer { padding: 12px 16px; border-top: 1px solid #e9ecef; }
    .modal-info-table { font-size: .85rem; }
    .modal-info-table th { width: 40%; color: #6c757d; font-weight: 500; padding: 4px 0; vertical-align: top; }
    .modal-info-table td { padding: 4px 0; vertical-align: top; }
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
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">📥 Total Tugas Hari Ini</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['total']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">✅ Sudah Dikerjakan</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['selesai']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">⏳ Belum Dikerjakan</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($hariIni['belum']); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">🏆 Statistik Saya</small>
                <h3 class="mb-0 fw-bold mt-1"><?php echo e($myStats['selesai_bulan']); ?></h3>
                <small style="opacity: 0.9; font-size: .65rem;">dari <?php echo e($myStats['total_bulan']); ?> tugas</small>
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
    <div class="card-header bg-success text-white py-2">
        <h6 class="mb-0"><i class="bi bi-check-circle-fill"></i> Riwayat Selesai (10 Terakhir)</h6>
    </div>
    <div class="card-body p-2">
        <?php $__currentLoopData = $jobsSelesai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="job-selesai-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold" style="font-size: .9rem;"><?php echo e($j->judul); ?></div>
                        <small class="text-muted">
                            <i class="bi bi-hash"></i><?php echo e($j->kode_job); ?> ·
                            <i class="bi bi-geo-alt"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?>

                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success">Selesai</span>
                        <br>
                        <small class="text-muted"><?php echo e($j->tanggal_selesai?->format('d/m H:i')); ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
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
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/dashboard.blade.php ENDPATH**/ ?>