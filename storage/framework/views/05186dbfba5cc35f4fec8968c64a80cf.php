
<?php $__env->startSection('title', 'Buat Tugas'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Pekerjaan</h5></div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('admin.store')); ?>">
            <?php echo csrf_field(); ?>

            
            <div class="mb-3">
                <label class="form-label fw-bold">Bidang Pekerjaan <span class="text-danger">*</span></label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bidang"
                               id="bidangKomputer" value="komputer"
                               <?php if(old('bidang', 'komputer') === 'komputer'): echo 'checked'; endif; ?>>
                        <label class="form-check-label" for="bidangKomputer">
                            <i class="bi bi-pc-display text-primary"></i> Komputer / Jaringan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bidang"
                               id="bidangCctv" value="cctv"
                               <?php if(old('bidang') === 'cctv'): echo 'checked'; endif; ?>>
                        <label class="form-check-label" for="bidangCctv">
                            <i class="bi bi-camera-video text-info"></i> CCTV
                        </label>
                    </div>
                </div>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Judul Pekerjaan</label>
                <input name="judul" class="form-control" value="<?php echo e(old('judul')); ?>" required>
            </div>

            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teknisi</label>
                    <select name="teknisi_id" class="form-select" id="teknisiSelect">
                        <option value="">— Biarkan Kosong (Bebas Diambil Siapa Saja) —</option>
                        <?php $__currentLoopData = $teknisiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>" data-bidang="<?php echo e($t->bidang()); ?>"
                                <?php if(old('teknisi_id') == $t->id): echo 'selected'; endif; ?>><?php echo e($t->display_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Kosongkan kalau ingin teknisi ambil sendiri.
                    </small>
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input name="kategori" class="form-control" value="<?php echo e(old('kategori')); ?>"
                           placeholder="CCTV / Switch / AP / PC">
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select">
                        <option>Rendah</option>
                        <option selected>Sedang</option>
                        <option>Tinggi</option>
                    </select>
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_tugas" class="form-control"
                           value="<?php echo e(old('tanggal_tugas', now()->format('Y-m-d'))); ?>" required>
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Gedung</label>
                    <input name="lok_gedung" class="form-control" value="<?php echo e(old('lok_gedung')); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Ruang</label>
                    <input name="lokasi_ruang" class="form-control" value="<?php echo e(old('lokasi_ruang')); ?>">
                </div>

                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Perangkat</label>
                    <input name="computer_name" class="form-control" value="<?php echo e(old('computer_name')); ?>"
                           placeholder="Contoh: PC-IT-01 / CAM-01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Merk</label>
                    <input name="merk" class="form-control" value="<?php echo e(old('merk')); ?>"
                           placeholder="Contoh: Lenovo / Hikvision">
                </div>
            </div>

            
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3"><?php echo e(old('deskripsi')); ?></textarea>
            </div>

            <button class="btn btn-primary">Simpan Tugas</button>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const sel = document.getElementById('teknisiSelect');
        const radios = document.querySelectorAll('input[name="bidang"]');

        function filter() {
            const picked = document.querySelector('input[name="bidang"]:checked');
            if (!picked) return;
            const bidang = picked.value;

            [...sel.options].forEach(o => {
                if (!o.value) return;
                const ok = o.dataset.bidang === bidang;
                o.hidden = !ok;
                o.disabled = !ok;
                if (!ok && o.selected) o.selected = false;
            });
        }

        radios.forEach(r => r.addEventListener('change', filter));
        filter();
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/admin/buat_tugas.blade.php ENDPATH**/ ?>