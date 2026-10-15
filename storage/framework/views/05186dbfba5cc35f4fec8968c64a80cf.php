
<?php $__env->startSection('title', 'Buat Tugas'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Pekerjaan</h5></div>
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('admin.store')); ?>">
            <?php echo csrf_field(); ?>

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
                
                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        <i class="bi bi-people-fill"></i> Pilih Teknisi
                        <span class="text-muted small">(bisa pilih lebih dari 1)</span>
                    </label>

                    <div class="border rounded p-2" id="teknisiWrapper"
                         style="max-height: 250px; overflow-y: auto; background: #f8f9fa;">

                        <div class="text-muted small p-2" id="teknisiEmpty">
                            Pilih bidang dulu untuk menampilkan teknisi.
                        </div>

                        <?php $__currentLoopData = $teknisiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check teknisi-item"
                                 data-bidang="<?php echo e($t->bidang()); ?>"
                                 style="display:none;">
                                <input class="form-check-input" type="checkbox"
                                       name="teknisi_ids[]"
                                       value="<?php echo e($t->id); ?>"
                                       id="teknisi_<?php echo e($t->id); ?>"
                                       <?php if(in_array($t->id, old('teknisi_ids', []))): echo 'checked'; endif; ?>>
                                <label class="form-check-label" for="teknisi_<?php echo e($t->id); ?>">
                                    <i class="bi bi-person-circle"></i>
                                    <strong><?php echo e($t->display_name); ?></strong>
                                    <small class="text-muted">
                                        (<?php echo e($t->role === 'teknisi_cctv' ? 'CCTV' : 'Komputer'); ?>)
                                    </small>
                                </label>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllTeknisi(true)">
                            <i class="bi bi-check-all"></i> Pilih Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllTeknisi(false)">
                            <i class="bi bi-x-circle"></i> Hapus Pilihan
                        </button>
                        <span class="ms-auto small text-muted align-self-center">
                            Terpilih: <strong id="teknisiCount">0</strong>
                        </span>
                    </div>

                    <div class="alert alert-info mt-2 mb-0 small" id="infoClone" style="display:none;">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Akan dibuat <span id="cloneCount">0</span> tiket terpisah.</strong>
                        Setiap teknisi akan menerima tiket sendiri dan menyelesaikan secara independen.
                    </div>

                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle"></i>
                        Kosongkan kalau ingin teknisi ambil sendiri.
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
        const radios = document.querySelectorAll('input[name="bidang"]');
        const items = document.querySelectorAll('.teknisi-item');
        const emptyMsg = document.getElementById('teknisiEmpty');
        const countEl = document.getElementById('teknisiCount');
        const infoClone = document.getElementById('infoClone');
        const cloneCount = document.getElementById('cloneCount');

        function filter() {
            const picked = document.querySelector('input[name="bidang"]:checked');
            if (!picked) return;
            const bidang = picked.value;

            let visible = 0;
            items.forEach(function(el) {
                const isMatch = el.dataset.bidang === bidang;
                el.style.display = isMatch ? 'block' : 'none';

                if (!isMatch) {
                    const cb = el.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = false;
                } else {
                    visible++;
                }
            });

            emptyMsg.style.display = visible > 0 ? 'none' : 'block';
            updateCount();
        }

        function updateCount() {
            const checked = document.querySelectorAll('.teknisi-item input:checked').length;
            countEl.textContent = checked;

            if (checked >= 2) {
                infoClone.style.display = 'block';
                cloneCount.textContent = checked;
            } else {
                infoClone.style.display = 'none';
            }
        }

        window.checkAllTeknisi = function(state) {
            items.forEach(function(el) {
                if (el.style.display !== 'none') {
                    const cb = el.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = state;
                }
            });
            updateCount();
        };

        items.forEach(function(el) {
            const cb = el.querySelector('input[type="checkbox"]');
            if (cb) cb.addEventListener('change', updateCount);
        });

        radios.forEach(r => r.addEventListener('change', filter));
        filter();
    })();
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/admin/buat_tugas.blade.php ENDPATH**/ ?>