
<?php $__env->startSection('title', 'Kerjakan Pekerjaan'); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-12 col-md-8">

    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-1"><?php echo e($job->judul); ?></h5>
            <div class="small text-muted">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-hash"></i><?php echo e($job->kode_job); ?>

                </span>
                <span class="badge <?php echo e($job->bidangBadgeClass()); ?> ms-1">
                    <i class="bi <?php echo e($job->bidangIcon()); ?>"></i> <?php echo e($job->bidangLabel()); ?>

                </span>
                <span class="ms-2"><i class="bi bi-geo-alt"></i>
                    <?php echo e($job->lok_gedung ?? '-'); ?> / <?php echo e($job->lokasi_ruang ?? '-'); ?>

                </span>
                <span class="ms-2"><i class="bi bi-calendar"></i>
                    <?php echo e($job->tanggal_tugas?->format('d M Y')); ?>

                </span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header <?php echo e($isCctv ? 'bg-info text-dark' : 'bg-primary text-white'); ?>">
            <h5 class="mb-0"><i class="bi bi-tools"></i> Kerjakan & Selesaikan Pekerjaan</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route($rp . '.selesai', $job)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hasil Pekerjaan <span class="text-danger">*</span></label>
                    <textarea name="hasil_pekerjaan" class="form-control" rows="3" required
                              placeholder="Contoh: <?php echo e($isCctv ? 'Kamera CCTV sudah normal kembali' : 'Printer sudah bisa digunakan kembali'); ?>"><?php echo e(old('hasil_pekerjaan', $job->hasil_pekerjaan)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"
                              placeholder="<?php echo e($isCctv ? 'Contoh: Penggantian kabel BNC' : 'Contoh: Penggantian kabel LAN'); ?>"><?php echo e(old('catatan', $job->catatan)); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sebelum</label>
                    <input type="file" name="foto_sebelum[]" class="form-control" multiple accept="image/*">
                    <?php if($job->fotoSebelum->count() > 0): ?>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <?php $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="70">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sesudah</label>
                    <input type="file" name="foto_sesudah[]" class="form-control" multiple accept="image/*">
                    <?php if($job->fotoSesudah->count() > 0): ?>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <?php $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border border-success" width="70">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-fill">
                        <i class="bi bi-check-circle"></i> Simpan & Selesaikan
                    </button>
                    <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/selesai.blade.php ENDPATH**/ ?>