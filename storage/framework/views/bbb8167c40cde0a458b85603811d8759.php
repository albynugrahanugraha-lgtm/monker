<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Pekerjaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            margin: auto;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <i class="bi bi-clipboard-check" style="font-size:3rem;color:#667eea;"></i>
            <h4 class="mt-2">Monitoring Pekerjaan</h4>
            <p class="text-muted small mb-0">Silakan login untuk melanjutkan</p>
        </div>
        <?php if($errors->any()): ?>
            <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo e(old('username')); ?>" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100 py-2">Login</button>
        </form>
        <hr class="my-3">
        <div class="small text-muted">
            <strong>Akun default:</strong><br>
            superadmin / super123<br>
            admin / admin123<br>
            teknisi / teknisi123<br>
            teknisi_cctv / cctv123
        </div>
    </div>
</div>
</body>
</html><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/auth/login.blade.php ENDPATH**/ ?>