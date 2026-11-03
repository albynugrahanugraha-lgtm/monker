
<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php
    $isCctv = auth()->user()->isTeknisiCctv() ?? false;
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
    $primaryDark = $isCctv ? '#0aa2c0' : '#0a58ca';
    $rp = auth()->user()->isTeknisiCctv() ? 'teknisi_cctv' : (auth()->user()->isTeknisi() ? 'teknisi' : 'admin');
?>

<?php $__env->startPush('styles'); ?>
<style>
    body { background-color: #f2f4f8 !important; }
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* HEADER PROFIL */
    .profile-header {
        background: linear-gradient(135deg, <?php echo e($primaryColor); ?>, <?php echo e($primaryDark); ?>);
        border-radius: 24px; padding: 30px 20px; color: white;
        text-align: center; position: relative; overflow: hidden;
        box-shadow: 0 10px 25px rgba(<?php echo e($isCctv ? '13, 202, 240' : '13, 110, 253'); ?>, 0.3);
        margin-bottom: 24px;
    }
    .profile-avatar {
        width: 80px; height: 80px; border-radius: 50%;
        background: rgba(255,255,255,0.2); border: 3px solid white;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; font-weight: 800; margin: 0 auto 12px auto;
    }
    .profile-header h1 { font-size: 1.3rem; font-weight: 800; margin: 0; }
    .profile-header p { font-size: 0.85rem; opacity: 0.9; margin: 4px 0 0 0; text-transform: uppercase; }

    /* MENU DESKTOP (Hanya Laptop) */
    .desktop-nav { display: none; gap: 8px; margin-top: 16px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 16px; }
    .desktop-nav a { color: rgba(255,255,255,0.8); text-decoration: none; font-size: 0.85rem; font-weight: 600; padding: 6px 12px; border-radius: 8px; }
    .desktop-nav a:hover, .desktop-nav a.active { background: rgba(255,255,255,0.2); color: white; }

    /* FORM CARD */
    .form-card {
        background: white; border-radius: 20px; padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 20px;
    }
    .form-card h2 { font-size: 1rem; font-weight: 800; color: #1a1a1a; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 12px; }
    
    .form-group { margin-bottom: 16px; }
    .form-label { font-size: 0.8rem; font-weight: 700; color: #555; margin-bottom: 6px; display: block; }
    .form-control-pwa {
        width: 100%; padding: 12px 16px; border-radius: 12px;
        border: 1px solid #e0e0e0; background: #f9f9f9;
        font-size: 0.95rem; color: #1a1a1a; transition: all 0.2s;
    }
    .form-control-pwa:focus {
        outline: none; border-color: <?php echo e($primaryColor); ?>; background: white;
        box-shadow: 0 0 0 3px <?php echo e($isCctv ? 'rgba(13, 202, 240, 0.2)' : 'rgba(13, 110, 253, 0.2)'); ?>;
    }

    .btn-save {
        width: 100%; padding: 14px; border-radius: 12px; border: none;
        background: <?php echo e($primaryColor); ?>; color: white; font-size: 1rem; font-weight: 800;
        margin-top: 10px; cursor: pointer; transition: transform 0.15s;
    }
    .btn-save:active { transform: scale(0.98); }
    
    /* BOTTOM NAV */
    .bottom-nav {
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; display: flex; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1000;
    }
    .bottom-nav-item {
        display: flex; flex-direction: column; align-items: center; gap: 4px;
        color: #adb5bd; text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
    }
    .bottom-nav-item i { font-size: 1.4rem; }
    .bottom-nav-item.active { color: <?php echo e($primaryColor); ?>; }

    @media (min-width: 768px) {
        .app-container { max-width: 800px; padding: 24px; padding-bottom: 24px; }
        .bottom-nav { display: none !important; }
        .desktop-nav { display: flex; justify-content: center; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="app-container">

    
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo e(strtoupper(substr(auth()->user()->nama ?? 'T', 0, 1))); ?>

        </div>
        <h1><?php echo e(auth()->user()->display_name); ?></h1>
        <p><?php echo e(str_replace('_', ' ', auth()->user()->role)); ?></p>

        <?php if(auth()->user()->isAnyTeknisi()): ?>
        <div class="desktop-nav">
            <a href="<?php echo e(route($rp . '.dashboard')); ?>"><i class="bi bi-house-door-fill"></i> Beranda</a>
            <a href="<?php echo e(route($rp . '.buat')); ?>"><i class="bi bi-plus-square"></i> Buat Tugas</a>
            <a href="<?php echo e(route('laporan.index')); ?>"><i class="bi bi-bar-chart-fill"></i> Laporan</a>
            <a href="<?php echo e(route('profile.edit')); ?>" class="active"><i class="bi bi-person-circle"></i> Profil</a>
        </div>
        <?php endif; ?>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success" style="border-radius: 12px; border: none; font-size: 0.85rem;">
            <i class="bi bi-check-circle-fill"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger" style="border-radius: 12px; border: none; font-size: 0.85rem;">
            <i class="bi bi-exclamation-triangle-fill"></i> <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    
    <div class="form-card">
        <h2><i class="bi bi-shield-lock-fill"></i> Ubah Password</h2>
        <form method="POST" action="<?php echo e(route('profile.password')); ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="password_lama" class="form-control-pwa" required placeholder="Masukkan password lama">
            </div>

            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control-pwa" required placeholder="Minimal 6 karakter">
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_baru_confirmation" class="form-control-pwa" required placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn-save">
                <i class="bi bi-save"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    
    <div class="d-md-none mt-4">
        <form action="<?php echo e(route('logout')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" style="width: 100%; padding: 14px; border-radius: 12px; background: #ffebee; color: #dc3545; border: 1px solid #ffcdd2; font-weight: 800; font-size: 0.95rem;">
                <i class="bi bi-box-arrow-right"></i> Logout Akun
            </button>
        </form>
    </div>

</div>


<?php if(auth()->user()->isAnyTeknisi()): ?>
<div class="bottom-nav">
    <a href="<?php echo e(route($rp . '.dashboard')); ?>" class="bottom-nav-item"><i class="bi bi-house-door-fill"></i><span>Beranda</span></a>
    <a href="<?php echo e(route($rp . '.buat')); ?>" class="bottom-nav-item"><i class="bi bi-plus-square"></i><span>Buat</span></a>
    <a href="<?php echo e(route('laporan.index')); ?>" class="bottom-nav-item"><i class="bi bi-bar-chart"></i><span>Laporan</span></a>
    <a href="<?php echo e(route('profile.edit')); ?>" class="bottom-nav-item active"><i class="bi bi-person-circle"></i><span>Profil</span></a>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app_pwa', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/profile/edit.blade.php ENDPATH**/ ?>