<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $__env->yieldContent('title', 'Teknisi PWA'); ?></title>
    
    <!-- 🆕 PWA META TAGS -->
    <meta name="theme-color" content="#0d6efd">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    
    <!-- HANYA MEMUAT ICON, TANPA BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* CSS RESET MURNI */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #f2f4f8; color: #333; padding-bottom: 90px; -webkit-tap-highlight-color: transparent; }
        
        /* UTILITY MURNI */
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-center { align-items: center; }
        .w-100 { width: 100%; }
        
        <?php echo $__env->yieldContent('content'); ?>
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

    <!-- KONTEN -->
    <?php echo $__env->yieldContent('content'); ?>

    
    <?php if(auth()->guard()->check()): ?>
        <?php if(auth()->user()->isAnyTeknisi()): ?>
            <?php echo $__env->make('layouts.partials.bottom_nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    <?php endif; ?>

    <!-- SCRIPTS (Hanya JS Bootstrap untuk modal jika diperlukan) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 🆕 DAFTARKAN SERVICE WORKER PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/layouts/app_pwa.blade.php ENDPATH**/ ?>