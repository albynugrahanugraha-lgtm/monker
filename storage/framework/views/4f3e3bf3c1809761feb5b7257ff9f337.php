
<?php $__env->startSection('title', 'Detail Pekerjaan'); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
?>

<?php $__env->startPush('styles'); ?>
<style>
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* TOMBOL BACK */
    .header-back {
        display: flex; align-items: center; margin-bottom: 20px;
    }
    .header-back a {
        color: #333; font-size: 1.5rem; margin-right: 12px; text-decoration: none;
    }
    .header-back .title { font-size: 1.1rem; font-weight: 800; color: #1a1a1a; }
    .header-back .subtitle { font-size: 0.8rem; color: #888; }

    .header-detail {
        background: linear-gradient(135deg, <?php echo e($primaryColor); ?>, <?php echo e($isCctv ? '#0aa2c0' : '#0a58ca'); ?>);
        border-radius: 20px; padding: 20px; color: white; margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(<?php echo e($isCctv ? '13, 202, 240' : '13, 110, 253'); ?>, 0.3);
    }
    .header-detail h5 { font-size: 1.2rem; font-weight: 800; margin: 0 0 8px 0; }
    
    .detail-card {
        background: white; border-radius: 20px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 16px;
    }
    .detail-card h6 { font-size: 0.9rem; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    
    .detail-row { display: flex; margin-bottom: 12px; font-size: 0.85rem; }
    .detail-row .lbl { width: 40%; color: #888; font-weight: 600; }
    .detail-row .val { width: 60%; font-weight: 700; color: #1a1a1a; word-break: break-word; }

    .foto-grid { display: flex; flex-wrap: wrap; gap: 10px; }
    .foto-item { position: relative; width: 80px; height: 80px; border-radius: 10px; overflow: hidden; border: 2px solid #ddd; }
    .foto-item img { width: 100%; height: 100%; object-fit: cover; }

    .btn-action {
        display: block; width: 100%; padding: 14px; border-radius: 14px; text-align: center;
        font-weight: 800; text-decoration: none; font-size: 0.95rem; margin-bottom: 10px;
    }

    .timeline { border-left: 2px solid <?php echo e($primaryColor); ?>; padding-left: 16px; margin-left: 8px; }
    .timeline-item { position: relative; margin-bottom: 16px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -21px; top: 4px;
        width: 10px; height: 10px; border-radius: 50%; background: <?php echo e($primaryColor); ?>; border: 2px solid white;
    }
    .timeline-date { font-size: 0.7rem; color: #888; font-weight: bold; margin-bottom: 2px; }
    .timeline-text { font-size: 0.85rem; color: #333; }

    /* Tombol Hapus Foto */
    .btn-hapus-foto {
        opacity: 0.85; transition: all 0.2s; border-radius: 50% !important;
        width: 24px; height: 24px; padding: 0 !important; line-height: 1 !important;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-hapus-foto:hover { opacity: 1; transform: scale(1.15); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="app-container">

    
    <div class="header-back">
        <a href="<?php echo e(route($rp . '.dashboard')); ?>">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
        <div>
            <div class="title">Detail Pekerjaan</div>
            <div class="subtitle">Kode: <?php echo e($job->kode_job); ?></div>
        </div>
    </div>

    
    <div class="header-detail">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span style="font-family: monospace; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 6px;"><i class="bi bi-hash"></i><?php echo e($job->kode_job); ?></span>
            <span class="badge <?php echo e($job->statusBadgeClass()); ?>"><?php echo e($job->statusLabel()); ?></span>
        </div>
        <h5><?php echo e($job->judul); ?></h5>
        <div style="font-size: 0.8rem; opacity: 0.9;">
            <i class="bi bi-geo-alt-fill"></i> <?php echo e($job->lok_gedung ?? '-'); ?> / <?php echo e($job->lokasi_ruang ?? '-'); ?>

        </div>
    </div>

    
    <?php if($job->bisaDiselesaikan()): ?>
        <a href="<?php echo e(route($rp . '.kerjakan', $job)); ?>" class="btn-action" style="background:#198754; color:white;">
            <i class="bi bi-play-circle"></i> Kerjakan Tugas Ini
        </a>
    <?php endif; ?>
    <a href="<?php echo e(route($rp . '.cetak', $job)); ?>" target="_blank" class="btn-action" style="background:#e9ecef; color:#333;">
        <i class="bi bi-printer"></i> Cetak Laporan
    </a>

    
    <div class="detail-card">
        <h6><i class="bi bi-info-circle"></i> Informasi Tugas</h6>
        <div class="detail-row"><div class="lbl">Bidang</div><div class="val"><?php echo e($job->bidangLabel()); ?></div></div>
        <div class="detail-row"><div class="lbl">Kategori</div><div class="val"><?php echo e($job->kategori ?? '-'); ?></div></div>
        <div class="detail-row"><div class="lbl">Perangkat</div><div class="val"><?php echo e($job->computer_name ?? '-'); ?> (<?php echo e($job->merk ?? '-'); ?>)</div></div>
        <div class="detail-row"><div class="lbl">Teknisi</div><div class="val"><?php echo e($job->teknisi->display_name ?? 'Belum di-assign'); ?></div></div>
        <div class="detail-row"><div class="lbl">Tanggal</div><div class="val"><?php echo e($job->tanggal_tugas?->format('d F Y') ?? '-'); ?></div></div>
        <?php if($job->deskripsi): ?>
            <div class="detail-row"><div class="lbl">Deskripsi</div><div class="val"><?php echo e($job->deskripsi); ?></div></div>
        <?php endif; ?>
    </div>

    
    <?php if($job->hasil_pekerjaan): ?>
        <div class="detail-card" style="border-left: 5px solid #198754;">
            <h6 style="color:#198754;"><i class="bi bi-check-circle-fill"></i> Hasil Pekerjaan</h6>
            <div style="font-size:0.9rem; font-weight:600; margin-bottom:8px;"><?php echo e($job->hasil_pekerjaan); ?></div>
            <?php if($job->catatan): ?>
                <div style="font-size:0.8rem; color:#666;"><em>Catatan: <?php echo e($job->catatan); ?></em></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <div class="detail-card">
        <h6><i class="bi bi-image" style="color:#ffc107;"></i> Foto Sebelum (<?php echo e($job->fotoSebelum->count()); ?>)</h6>
        <div class="foto-grid" id="wrapperFotoSebelum">
            <?php $__empty_1 = true; $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank"><img src="<?php echo e(asset($f->nama_file)); ?>"></a>
                    <button type="button"
                            class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                            data-foto-id="<?php echo e($f->id); ?>"
                            data-foto-jenis="sebelum"
                            title="Hapus foto">
                        <i class="bi bi-x" style="font-size: .85rem;"></i>
                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="font-size:0.8rem; color:#888; padding:10px;">Belum ada foto</div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="detail-card">
        <h6><i class="bi bi-image" style="color:#198754;"></i> Foto Sesudah (<?php echo e($job->fotoSesudah->count()); ?>)</h6>
        <div class="foto-grid" id="wrapperFotoSesudah">
            <?php $__empty_1 = true; $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank"><img src="<?php echo e(asset($f->nama_file)); ?>"></a>
                    <button type="button"
                            class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                            data-foto-id="<?php echo e($f->id); ?>"
                            data-foto-jenis="sesudah"
                            title="Hapus foto">
                        <i class="bi bi-x" style="font-size: .85rem;"></i>
                    </button>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div style="font-size:0.8rem; color:#888; padding:10px;">Belum ada foto</div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($job->histories->count() > 0): ?>
        <div class="detail-card">
            <h6><i class="bi bi-clock-history"></i> Riwayat Aktivitas</h6>
            <div class="timeline">
                <?php $__currentLoopData = $job->histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="timeline-item">
                        <div class="timeline-date"><?php echo e($h->created_at->format('d M Y - H:i')); ?></div>
                        <div class="timeline-text">
                            <strong><?php echo e($h->user->display_name ?? 'System'); ?></strong><br>
                            <?php echo e($h->keterangan ?? 'Update status'); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika Hapus Foto via AJAX
        document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
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
                            container.innerHTML = '<div style="font-size:0.8rem; color:#888; padding:10px;">Semua foto sudah dihapus</div>';
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
            // Menggunakan style PWA untuk toast
            toast.style.cssText = `
                position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
                background: ${type === 'success' ? '#198754' : '#dc3545'};
                color: white; padding: 10px 20px; border-radius: 30px;
                font-size: 0.85rem; font-weight: bold; z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15); text-align: center;
            `;
            toast.innerHTML = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app_pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/detail_pekerjaan.blade.php ENDPATH**/ ?>