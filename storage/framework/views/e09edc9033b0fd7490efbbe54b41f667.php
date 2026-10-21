
<?php $__env->startSection('title', 'Kerjakan Pekerjaan'); ?>

<?php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Hide input file asli */
    .foto-slot input[type="file"] {
        display: none !important;
    }

    /* Container slot */
    .foto-slot {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 90px;
        padding: 10px 8px;
        border: 2px dashed #ced4da;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        box-sizing: border-box;
        overflow: hidden;
    }

    .foto-slot:hover {
        border-color: #0d6efd;
        background: #e7f1ff;
    }

    /* State sudah ada file */
    .foto-slot.has-file {
        border-style: solid;
        border-color: #198754;
        background: #f0fff4;
    }

    .foto-slot.has-file:hover {
        border-color: #dc3545;
        background: #fff5f5;
    }

    /* Icon default */
    .foto-slot .foto-slot-icon {
        display: block;
        font-size: 1.6rem;
        color: #adb5bd;
        margin-bottom: 4px;
        line-height: 1;
    }

    /* Label default */
    .foto-slot .foto-slot-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 1px;
    }

    /* Hint default */
    .foto-slot .foto-slot-hint {
        display: block;
        font-size: 0.62rem;
        color: #adb5bd;
    }

    /* 🆕 Saat sudah ada file — SEMBUNYIKAN icon/label/hint */
    .foto-slot.has-file .foto-slot-icon,
    .foto-slot.has-file .foto-slot-label,
    .foto-slot.has-file .foto-slot-hint {
        display: none !important;
    }

    /* 🆕 Tampilkan nama file saja */
    .foto-slot .file-name-display {
        display: none;
        padding: 6px 4px;
        text-align: center;
        width: 100%;
    }

    .foto-slot.has-file .file-name-display {
        display: block;
    }

    .file-name-display .file-name-icon {
        display: block;
        font-size: 1.5rem;
        color: #198754;
        margin-bottom: 4px;
    }

    .file-name-display .file-name-text {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        color: #198754;
        word-break: break-all;
        line-height: 1.3;
    }

    .file-name-display .file-name-size {
        display: block;
        font-size: 0.6rem;
        color: #6c757d;
        margin-top: 2px;
    }

    /* Tombol hapus (❌) */
    .foto-slot .foto-slot-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #dc3545;
        color: #fff;
        border: none;
        font-size: 0.7rem;
        line-height: 1;
        padding: 0;
        cursor: pointer;
        z-index: 4;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }

    .foto-slot .foto-slot-remove:hover {
        background: #b02a37;
        transform: scale(1.1);
    }

    /* Foto lama */
    .foto-item {
        position: relative;
        display: inline-block;
    }
    .btn-hapus-foto {
        opacity: 0.75;
        transition: all 0.2s;
        border-radius: 50% !important;
        width: 22px;
        height: 22px;
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
</style>
<?php $__env->stopPush(); ?>

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
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-warning"></i> Foto Sebelum
                        <span class="text-muted small">(max 3 foto — klik slot untuk pilih)</span>
                    </label>

                    <div class="row g-2">
                        <?php for($i = 1; $i <= 3; $i++): ?>
                            <div class="col-4">
                                <input type="file"
                                       name="foto_sebelum_<?php echo e($i); ?>"
                                       id="inputSebelum<?php echo e($i); ?>"
                                       accept="image/*"
                                       onchange="handleFile(this, 'Sebelum', <?php echo e($i); ?>)">

                                <div class="foto-slot" id="slotSebelum<?php echo e($i); ?>" onclick="triggerFile('Sebelum', <?php echo e($i); ?>)">
                                    <i class="bi bi-camera foto-slot-icon" id="iconSebelum<?php echo e($i); ?>"></i>
                                    <div class="foto-slot-label" id="labelSebelum<?php echo e($i); ?>">Foto <?php echo e($i); ?></div>
                                    <div class="foto-slot-hint" id="hintSebelum<?php echo e($i); ?>">Klik untuk pilih</div>

                                    
                                    <div class="file-name-display" id="fileNameSebelum<?php echo e($i); ?>">
                                        <i class="bi bi-file-image file-name-icon"></i>
                                        <span class="file-name-text" id="fileNameTextSebelum<?php echo e($i); ?>">-</span>
                                        <span class="file-name-size" id="fileNameSizeSebelum<?php echo e($i); ?>">-</span>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <?php if($job->fotoSebelum->count() > 0): ?>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Foto sebelum yang sudah ada (<?php echo e($job->fotoSebelum->count()); ?>):
                            </small>
                            <div class="d-flex flex-wrap gap-2" id="wrapperFotoSebelum">
                                <?php $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" width="80">
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                                                data-foto-id="<?php echo e($f->id); ?>"
                                                data-foto-jenis="sebelum"
                                                title="Hapus foto">
                                            <i class="bi bi-x" style="font-size: .75rem;"></i>
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                
                
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-success"></i> Foto Sesudah
                        <span class="text-muted small">(max 3 foto — klik slot untuk pilih)</span>
                    </label>

                    <div class="row g-2">
                        <?php for($i = 1; $i <= 3; $i++): ?>
                            <div class="col-4">
                                <input type="file"
                                       name="foto_sesudah_<?php echo e($i); ?>"
                                       id="inputSesudah<?php echo e($i); ?>"
                                       accept="image/*"
                                       onchange="handleFile(this, 'Sesudah', <?php echo e($i); ?>)">

                                <div class="foto-slot" id="slotSesudah<?php echo e($i); ?>" onclick="triggerFile('Sesudah', <?php echo e($i); ?>)">
                                    <i class="bi bi-camera foto-slot-icon" id="iconSesudah<?php echo e($i); ?>"></i>
                                    <div class="foto-slot-label" id="labelSesudah<?php echo e($i); ?>">Foto <?php echo e($i); ?></div>
                                    <div class="foto-slot-hint" id="hintSesudah<?php echo e($i); ?>">Klik untuk pilih</div>

                                    
                                    <div class="file-name-display" id="fileNameSesudah<?php echo e($i); ?>">
                                        <i class="bi bi-file-image file-name-icon"></i>
                                        <span class="file-name-text" id="fileNameTextSesudah<?php echo e($i); ?>">-</span>
                                        <span class="file-name-size" id="fileNameSizeSesudah<?php echo e($i); ?>">-</span>
                                    </div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>

                    <?php if($job->fotoSesudah->count() > 0): ?>
                        <div class="mt-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-check-circle-fill text-success"></i>
                                Foto sesudah yang sudah ada (<?php echo e($job->fotoSesudah->count()); ?>):
                            </small>
                            <div class="d-flex flex-wrap gap-2" id="wrapperFotoSesudah">
                                <?php $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="foto-item" data-foto-id="<?php echo e($f->id); ?>">
                                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border border-success" width="80">
                                        </a>
                                        <button type="button"
                                                class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                                                data-foto-id="<?php echo e($f->id); ?>"
                                                data-foto-jenis="sesudah"
                                                title="Hapus foto">
                                            <i class="bi bi-x" style="font-size: .75rem;"></i>
                                        </button>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
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

<?php $__env->startPush('scripts'); ?>
<script>
    function triggerFile(section, index) {
        const input = document.getElementById('input' + section + index);
        if (input) input.click();
    }

    // 🆕 Handle file — validasi + tampilkan nama file
    function handleFile(input, section, index) {
        const slot = document.getElementById('slot' + section + index);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validasi size max 5 MB
            if (file.size > 5 * 1024 * 1024) {
                alert('❌ Ukuran foto max 5 MB. File: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB');
                input.value = '';
                return;
            }

            // Validasi format
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('❌ Format harus JPG, JPEG, PNG, atau WEBP.');
                input.value = '';
                return;
            }

            // Update UI — tampilkan nama file
            slot.classList.add('has-file');

            const fileNameText = document.getElementById('fileNameText' + section + index);
            const fileNameSize = document.getElementById('fileNameSize' + section + index);

            if (fileNameText) fileNameText.textContent = file.name;

            if (fileNameSize) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                fileNameSize.textContent = sizeMB + ' MB';
            }

            // Hapus tombol lama
            const existingRemove = slot.querySelector('.foto-slot-remove');
            if (existingRemove) existingRemove.remove();

            // Tombol hapus (❌)
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'foto-slot-remove';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                resetSlot(section, index);
            };
            slot.appendChild(removeBtn);
        }
    }

    // Reset slot
    function resetSlot(section, index) {
        const input = document.getElementById('input' + section + index);
        const slot  = document.getElementById('slot' + section + index);

        input.value = '';
        slot.classList.remove('has-file');

        const fileNameText = document.getElementById('fileNameText' + section + index);
        const fileNameSize = document.getElementById('fileNameSize' + section + index);
        if (fileNameText) fileNameText.textContent = '-';
        if (fileNameSize) fileNameSize.textContent = '-';

        const removeBtn = slot.querySelector('.foto-slot-remove');
        if (removeBtn) removeBtn.remove();
    }

    // Hapus foto lama
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const fotoId    = this.dataset.fotoId;
                const fotoJenis = this.dataset.fotoJenis;

                if (!confirm('Yakin hapus foto ini?')) return;

                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.7rem;height:.7rem;"></span>';

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
                            container.innerHTML = '<span class="text-muted small">Semua foto sudah dihapus</span>';
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
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi_cctv/selesai.blade.php ENDPATH**/ ?>