
<?php $__env->startSection('title', 'Detail Pekerjaan'); ?>
<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-md-10">
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Pekerjaan #<?php echo e($job->kode_job); ?></h5>
        <div>
            <span class="badge <?php echo e($job->bidangBadgeClass()); ?> me-1">
                <i class="bi <?php echo e($job->bidangIcon()); ?>"></i> <?php echo e($job->bidangLabel()); ?>

            </span>
            <span class="badge <?php echo e($job->statusBadgeClass()); ?>"><?php echo e($job->statusLabel()); ?></span>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="200">Judul</th><td><?php echo e($job->judul); ?></td></tr>
            <tr><th>Deskripsi</th><td><?php echo e($job->deskripsi ?? '-'); ?></td></tr>
            <tr><th>Teknisi</th><td><?php echo e($job->teknisi->display_name ?? '-'); ?></td></tr>
            <tr><th>Lokasi</th><td><?php echo e($job->lok_gedung ?? '-'); ?> / <?php echo e($job->lokasi_ruang ?? '-'); ?></td></tr>
            <tr><th>Perangkat</th><td><?php echo e($job->computer_name ?? '-'); ?> (<?php echo e($job->merk ?? '-'); ?>)</td></tr>
            <tr><th>Kategori</th><td><?php echo e($job->kategori ?? '-'); ?></td></tr>
            <tr><th>Prioritas</th><td><?php echo e($job->prioritas); ?></td></tr>
            <tr><th>Tanggal Tugas</th><td><?php echo e($job->tanggal_tugas?->format('d-m-Y')); ?></td></tr>
            <tr><th>Tanggal Selesai</th><td><?php echo e($job->tanggal_selesai?->format('d-m-Y H:i') ?? '-'); ?></td></tr>
            <tr><th>Hasil Pekerjaan</th><td><?php echo e($job->hasil_pekerjaan ?? '-'); ?></td></tr>
            <tr><th>Catatan Teknisi</th><td><?php echo e($job->catatan ?? '-'); ?></td></tr>
            <tr><th>Catatan Admin</th><td><?php echo e($job->catatan_admin ?? '-'); ?></td></tr>
        </table>

        <h6 class="mt-4">
            <i class="bi bi-image text-warning"></i>
            Foto Sebelum
            <span class="badge bg-warning text-dark"><?php echo e($job->fotoSebelum->count()); ?></span>
        </h6>
        <div class="d-flex flex-wrap gap-2 mb-3">
            <?php $__empty_1 = true; $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                    <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="120">
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="text-muted">Belum ada foto sebelum</span>
            <?php endif; ?>
        </div>

        <h6>
            <i class="bi bi-image text-success"></i>
            Foto Sesudah
            <span class="badge bg-success"><?php echo e($job->fotoSesudah->count()); ?></span>
        </h6>
        <div class="d-flex flex-wrap gap-2">
            <?php $__empty_1 = true; $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                    <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="120">
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <span class="text-muted">Belum ada foto sesudah</span>
            <?php endif; ?>
        </div>

        <hr class="mt-4">

        <?php if($job->status === 'SELESAI'): ?>
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Pekerjaan sudah diselesaikan.</strong>
                    <?php if($job->tanggal_selesai): ?>
                        <br><small>Selesai pada: <?php echo e($job->tanggal_selesai->format('d-m-Y H:i')); ?></small>
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif($job->status === 'DIKERJAKAN'): ?>
            <div class="alert alert-warning">
                <i class="bi bi-tools"></i> Pekerjaan sedang dikerjakan oleh teknisi.
            </div>
        <?php else: ?>
            <div class="alert alert-secondary">
                <i class="bi bi-hourglass"></i> Pekerjaan belum dimulai.
            </div>
        <?php endif; ?>

        <div class="mt-3 d-flex gap-2 flex-wrap">
            <?php if($job->bisaDiselesaikan()): ?>
                <a href="<?php echo e(route('admin.selesaikan.form', $job)); ?>" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Selesaikan Pekerjaan
                </a>
            <?php endif; ?>

            <a href="<?php echo e(route('admin.cetak', $job)); ?>" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary">Kembali</a>

            <form method="POST" action="<?php echo e(route('admin.destroy', $job)); ?>"
                  onsubmit="return confirm('Yakin hapus pekerjaan ini? Tindakan tidak bisa dibatalkan.')"
                  class="ms-auto">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-danger">
                    <i class="bi bi-trash"></i> Hapus Pekerjaan
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><h6 class="mb-0">Riwayat Pekerjaan</h6></div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <?php $__empty_1 = true; $__currentLoopData = $job->histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <li class="list-group-item">
                    <small class="text-muted"><?php echo e($h->created_at->format('d-m-Y H:i')); ?></small> —
                    <strong><?php echo e($h->user->display_name ?? 'System'); ?></strong>:
                    <?php echo e($h->status_lama ?? '-'); ?> → <?php echo e($h->status_baru); ?>

                    <?php if($h->keterangan): ?><br><small class="text-muted"><?php echo e($h->keterangan); ?></small><?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <li class="list-group-item text-muted">Belum ada riwayat</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/admin/detail_job.blade.php ENDPATH**/ ?>