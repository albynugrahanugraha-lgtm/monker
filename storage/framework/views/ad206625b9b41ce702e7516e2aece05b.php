
<?php $__env->startSection('title', 'Profile Saya'); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
<div class="col-md-8">

    
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body d-flex align-items-center">
            <div class="me-3" style="font-size: 3rem; color: #0d6efd;">
                <i class="bi bi-person-circle"></i>
            </div>
            <div>
                <h5 class="mb-1 fw-bold"><?php echo e(auth()->user()->display_name); ?></h5>
                <div class="text-muted small">
                    <i class="bi bi-person-badge"></i>
                    <span class="badge bg-secondary"><?php echo e(auth()->user()->role); ?></span>
                    &nbsp;
                    <i class="bi bi-envelope"></i> <?php echo e(auth()->user()->email); ?>

                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="bi bi-key-fill"></i>
                Ganti Password
            </h5>
        </div>
        <div class="card-body">

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($e); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('profile.password')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Password Lama <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="current_password"
                               id="current_password"
                               class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" name="password"
                               id="new_password"
                               class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               required minlength="6">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('new_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter.</small>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-danger small mt-1"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Konfirmasi Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" name="password_confirmation"
                               id="confirm_password"
                               class="form-control"
                               required minlength="6">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('confirm_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill"></i>
                    Setelah ganti password, kamu tetap login. Password baru berlaku untuk login berikutnya.
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning flex-fill">
                        <i class="bi bi-check-circle"></i> Simpan Password Baru
                    </button>
                    <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/profile/edit.blade.php ENDPATH**/ ?>