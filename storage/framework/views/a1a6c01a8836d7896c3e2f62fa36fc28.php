
<?php $__env->startSection('title', 'Detail Pekerjaan'); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startPush('styles'); ?>
<style>
    .foto-item {
        position: relative;
        display: inline-block;
    }
    .btn-hapus-foto {
        opacity: 0.75;
        transition: all 0.2s;
        border-radius: 50% !important;
        width: 24px;
        height: 24px;
        padding: 0 !important;
        line-height: 1 !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-hapus-foto:hover {
        opacity: 1;
        transform: scale(1.15);
    }
    .detail-row {
        padding: 8px 0;
        border-bottom: 1px dashed #e9ecef;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-row .label {
        color: #6c757d;
        font-weight: 500;
        font-size: .85rem;
    }
    .detail-row .value {
        font-weight: 600;
        font-size: .9rem;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-12 col-md-10">

    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi <?php echo e($isCctv ? 'bi-camera-video' : 'bi-pc-display'); ?>"></i>
                Detail Pekerjaan
            </h5>
            <small class="text-muted" style="font-size: .75rem;">
                Kode: <code><?php echo e($job->kode_job); ?></code>
            </small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="btn btn-sm btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="<?php echo e(route($rp . '.cetak', $job)); ?>" target="_blank" class="btn btn-sm btn-outline-dark">
                <i class="bi bi-printer"></i> Cetak
            </a>
            <?php if($job->bisaDiselesaikan()): ?>
                <a href="<?php echo e(route($rp . '.kerjakan', $job)); ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-play-circle"></i> Kerjakan
                </a>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header <?php echo e($isCctv ? 'bg-info text-dark' : 'bg-primary text-white'); ?> d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-info-circle"></i> <?php echo e($job->judul); ?>

            </h6>
            <span class="badge <?php echo e($job->statusBadgeClass()); ?>"><?php echo e($job->statusLabel()); ?></span>
        </div>
        <div class="card-body">

            <div class="detail-row row">
                <div class="col-md-3 label">Bidang</div>
                <div class="col-md-9 value">
                    <span class="badge <?php echo e($job->bidangBadgeClass()); ?>">
                        <i class="bi <?php echo e($job->bidangIcon()); ?>"></i> <?php echo e($job->bidangLabel()); ?>

                    </span>
                </div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Kategori</div>
                <div class="col-md-9 value"><?php echo e($job->kategori ?? '-'); ?></div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Prioritas</div>
                <div class="col-md-9 value">
                    <?php
                        $pc = match($job->prioritas) {
                            'Tinggi' => 'danger',
                            'Sedang' => 'warning',
                            default  => 'secondary',
                        };
                    ?>
                    <span class="badge bg-<?php echo e($pc); ?>"><?php echo e($job->prioritas); ?></span>
                </div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Teknisi</div>
                <div class="col-md-9 value">
                    <i class="bi bi-person-circle text-primary"></i>
                    <?php echo e($job->teknisi->display_name ?? 'Belum di-assign'); ?>

                </div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Lokasi</div>
                <div class="col-md-9 value">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <?php echo e($job->lok_gedung ?? '-'); ?> / <?php echo e($job->lokasi_ruang ?? '-'); ?>

                </div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Perangkat</div>
                <div class="col-md-9 value">
                    <?php echo e($job->computer_name ?? '-'); ?>

                    <?php if($job->merk): ?> (<?php echo e($job->merk); ?>) <?php endif; ?>
                </div>
            </div>

            <div class="detail-row row">
                <div class="col-md-3 label">Tanggal Tugas</div>
                <div class="col-md-9 value">
                    <i class="bi bi-calendar-event"></i>
                    <?php echo e($job->tanggal_tugas?->format('d F Y') ?? '-'); ?>

                </div>
            </div>

            <?php if($job->tanggal_selesai): ?>
            <div class="detail-row row">
                <div class="col-md-3 label">Tanggal Selesai</div>
                <div class="col-md-9 value">
                    <i class="bi bi-calendar-check text-success"></i>
                    <?php echo e($job->tanggal_selesai->format('d F Y, H:i')); ?>

                </div>
            </div>
            <?php endif; ?>

            <?php if($job->deskripsi): ?>
            <div class="detail-row row">
                <div class="col-md-3 label">Deskripsi</div>
                <div class="col-md-9 value"><?php echo e($job->deskripsi); ?></div>
            </div>
            <?php endif; ?>

            <?php if($job->hasil_pekerjaan): ?>
            <div class="detail-row row">
                <div class="col-md-3 label">Hasil Pekerjaan</div>
                <div class="col-md-9 value">
                    <div class="alert alert-success mb-0 py-2 px-3">
                        <i class="bi bi-check-circle-fill"></i> <?php echo e($job->hasil_pekerjaan); ?>

                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if($job->catatan): ?>
            <div class="detail-row row">
                <div class="col-md-3 label">Catatan</div>
                <div class="col-md-9 value"><?php echo e($job->catatan); ?></div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-image"></i> Foto Sebelum
                <span class="badge bg-dark text-white"><?php echo e($job->fotoSebelum->count()); ?></span>
            </h6>
            <small style="font-size: .7rem;">
                <i class="bi bi-info-circle"></i> Klik <span class="text-danger">×</span> di pojok foto untuk hapus
            </small>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2" id="wrapperFotoSebelum">
                <?php $__empty_1 = true; $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="130">
                        </a>
                        <button type="button"
                                class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                                data-foto-id="<?php echo e($f->id); ?>"
                                data-foto-jenis="sebelum"
                                title="Hapus foto">
                            <i class="bi bi-x" style="font-size: .85rem;"></i>
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-muted small p-3">
                        <i class="bi bi-image"></i> Belum ada foto sebelum
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-image"></i> Foto Sesudah
                <span class="badge bg-light text-dark"><?php echo e($job->fotoSesudah->count()); ?></span>
            </h6>
            <small style="font-size: .7rem;">
                <i class="bi bi-info-circle"></i> Klik <span class="text-danger">×</span> di pojok foto untuk hapus
            </small>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2" id="wrapperFotoSesudah">
                <?php $__empty_1 = true; $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border border-success" width="130">
                        </a>
                        <button type="button"
                                class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                                data-foto-id="<?php echo e($f->id); ?>"
                                data-foto-jenis="sesudah"
                                title="Hapus foto">
                            <i class="bi bi-x" style="font-size: .85rem;"></i>
                        </button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-muted small p-3">
                        <i class="bi bi-image"></i> Belum ada foto sesudah
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <?php if($job->histories->count() > 0): ?>
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clock-history"></i> Riwayat Pekerjaan
            </h6>
        </div>
        <div class="card-body p-2">
            <ul class="list-group list-group-flush mb-0">
                <?php $__currentLoopData = $job->histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item px-2 py-1" style="font-size: .85rem;">
                        <small class="text-muted"><?php echo e($h->created_at->format('d-m-Y H:i')); ?></small> —
                        <strong><?php echo e($h->user->display_name ?? 'System'); ?></strong>:
                        <?php echo e($h->status_lama ?? '-'); ?> → <?php echo e($h->status_baru); ?>

                        <?php if($h->keterangan): ?>
                            <br><small class="text-muted"><?php echo e($h->keterangan); ?></small>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const fotoId    = this.dataset.fotoId;
                const fotoJenis = this.dataset.fotoJenis;

                if (!confirm('Yakin hapus foto ini?')) return;

                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.8rem;height:.8rem;"></span>';

                const urlPrefix = '<?php echo e($rp === "teknisi_cctv" ? "teknisi-cctv" : "teknisi"); ?>';
                const url = '/' + urlPrefix + '/photo/' + fotoId;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const wrapper = document.querySelector(`.foto-item[data-foto-id="${fotoId}"]`);
                        if (wrapper) wrapper.remove();

                        const container = fotoJenis === 'sebelum'
                            ? document.getElementById('wrapperFotoSebelum')
                            : document.getElementById('wrapperFotoSesudah');

                        if (container && container.querySelectorAll('.foto-item').length === 0) {
                            container.innerHTML = '<div class="text-muted small p-3"><i class="bi bi-image"></i> Semua foto sudah dihapus</div>';
                        }

                        tampilkanToast('✅ Foto berhasil dihapus', 'success');
                    } else {
                        tampilkanToast('❌ ' + (data.message || 'Gagal hapus foto'), 'danger');
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                    tampilkanToast('❌ Gagal hapus foto', 'danger');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                });
            });
        });

        function tampilkanToast(msg, type) {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3 shadow`;
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi_cctv/detail_pekerjaan.blade.php ENDPATH**/ ?>