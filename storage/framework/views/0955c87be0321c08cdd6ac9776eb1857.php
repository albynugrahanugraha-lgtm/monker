
<?php $__env->startSection('title', 'Selesaikan Tugas'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-md-8">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-check2-circle text-success"></i> Selesaikan Tugas
        </h5>
        <a href="<?php echo e(route('admin.show', $job)); ?>" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill"></i> <?php echo e(session('error')); ?>

            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle"></i> Ada kesalahan:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><small><?php echo e($err); ?></small></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0">
                <code><?php echo e($job->kode_job); ?></code> — <?php echo e($job->judul); ?>

            </h6>
        </div>
        <div class="card-body">
            <div class="row g-2 small">
                <div class="col-md-6">
                    <strong>Bidang:</strong>
                    <span class="badge <?php echo e($job->bidangBadgeClass()); ?>">
                        <i class="bi <?php echo e($job->bidangIcon()); ?>"></i> <?php echo e($job->bidangLabel()); ?>

                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <span class="badge <?php echo e($job->statusBadgeClass()); ?>"><?php echo e($job->statusLabel()); ?></span>
                </div>
                <div class="col-md-6">
                    <strong>Lokasi:</strong>
                    <?php echo e($job->lok_gedung ?? '-'); ?> / <?php echo e($job->lokasi_ruang ?? '-'); ?>

                </div>
                <div class="col-md-6">
                    <strong>Teknisi:</strong>
                    <?php echo e($job->teknisi->display_name ?? 'Belum di-assign'); ?>

                </div>
                <div class="col-md-6">
                    <strong>Tanggal Tugas:</strong>
                    <?php echo e($job->tanggal_tugas?->format('d-m-Y') ?? '-'); ?>

                </div>
                <div class="col-md-6">
                    <strong>Prioritas:</strong>
                    <?php echo e($job->prioritas); ?>

                </div>
                <?php if($job->deskripsi): ?>
                <div class="col-12">
                    <strong>Deskripsi:</strong>
                    <div class="text-muted"><?php echo e($job->deskripsi); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Form Penyelesaian</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('admin.selesaikan', $job)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <?php if(!$job->teknisi_id): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Assign Teknisi <span class="text-muted">(opsional)</span>
                    </label>
                    <select name="teknisi_id" class="form-select">
                        <option value="">— Tidak assign teknisi —</option>
                        <?php $__currentLoopData = $teknisiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>" <?php if(old('teknisi_id') == $t->id): echo 'selected'; endif; ?>>
                                <?php echo e($t->display_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Pilih teknisi kalau pekerjaan ini dikerjakan oleh teknisi tertentu.
                    </small>
                </div>
                <?php endif; ?>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Hasil Pekerjaan <span class="text-danger">*</span>
                    </label>
                    <textarea name="hasil_pekerjaan"
                              class="form-control <?php $__errorArgs = ['hasil_pekerjaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              rows="4" required
                              placeholder="Jelaskan hasil pekerjaan yang sudah dilakukan..."><?php echo e(old('hasil_pekerjaan', $job->hasil_pekerjaan)); ?></textarea>
                    <?php $__errorArgs = ['hasil_pekerjaan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Teknisi</label>
                    <textarea name="catatan" class="form-control" rows="3"
                              placeholder="Catatan tambahan dari teknisi (opsional)..."><?php echo e(old('catatan', $job->catatan)); ?></textarea>
                </div>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Admin</label>
                    <textarea name="catatan_admin" class="form-control" rows="3"
                              placeholder="Catatan dari admin (opsional)..."><?php echo e(old('catatan_admin', $job->catatan_admin)); ?></textarea>
                </div>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" class="form-control"
                           value="<?php echo e(old('tanggal_selesai', now()->format('Y-m-d\TH:i'))); ?>">
                    <small class="text-muted">Kosongkan untuk menggunakan waktu sekarang.</small>
                </div>

                <hr>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-warning"></i> Foto Sebelum Pekerjaan
                    </label>
                    <input type="file" name="foto_sebelum[]" class="form-control"
                           accept="image/*" multiple>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Foto kondisi sebelum pekerjaan. Bisa pilih beberapa sekaligus (JPG, PNG, WEBP max 5MB).
                    </small>
                </div>

                
                <?php if($job->fotoSebelum->count() > 0): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sebelum yang Sudah Ada:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="100">
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-success"></i> Foto Sesudah Pekerjaan
                    </label>
                    <input type="file" name="foto_sesudah[]" class="form-control"
                           accept="image/*" multiple>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Foto kondisi setelah pekerjaan. Bisa pilih beberapa sekaligus (JPG, PNG, WEBP max 5MB).
                    </small>
                </div>

                
                <?php if($job->fotoSesudah->count() > 0): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sesudah yang Sudah Ada:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="100">
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Perhatian:</strong> Setelah diselesaikan, status pekerjaan akan berubah menjadi
                    <span class="badge bg-success">SELESAI</span> dan tidak bisa dikembalikan.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill"
                            onclick="return confirm('Yakin selesaikan pekerjaan ini?')">
                        <i class="bi bi-check-circle-fill"></i> Selesaikan Pekerjaan
                    </button>
                    <a href="<?php echo e(route('admin.show', $job)); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/admin/selesaikan_tugas.blade.php ENDPATH**/ ?>