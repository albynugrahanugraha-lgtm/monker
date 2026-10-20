<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan #<?php echo e($job->kode_job); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1 { font-size: 16px; text-align: center; margin-bottom: 5px; }
        h2 { font-size: 13px; text-align: center; margin-top: 0; margin-bottom: 20px; font-weight: normal; }
        h3 { font-size: 13px; margin-top: 20px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; width: 30%; }
        .foto-box { display: inline-block; margin: 5px; text-align: center; }
        .foto-box img { max-width: 200px; max-height: 200px; border: 1px solid #ccc; }
        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { width: 40%; text-align: center; }
        .ttd p { margin: 60px 0 0 0; border-top: 1px solid #000; padding-top: 5px; }
        .no-print { text-align: center; margin: 20px 0; }

        /* 🆕 Badge bidang */
        .badge-cctv { background: #0dcaf0; color: #000; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-komputer { background: #0d6efd; color: #fff; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

<h1>LAPORAN PEKERJAAN TEKNISI <?php echo e(strtoupper($job->bidangLabel())); ?></h1>
<h2>Kode: <?php echo e($job->kode_job); ?></h2>

<table>
    <tr><th>Judul Pekerjaan</th><td><?php echo e($job->judul); ?></td></tr>
    <tr><th>Deskripsi</th><td><?php echo e($job->deskripsi ?? '-'); ?></td></tr>
    <tr>
        <th>Bidang</th>
        <td>
            <span class="<?php echo e($job->bidang === 'cctv' ? 'badge-cctv' : 'badge-komputer'); ?>">
                <?php echo e($job->bidangLabel()); ?>

            </span>
        </td>
    </tr>
    <tr><th>Kategori</th><td><?php echo e($job->kategori ?? '-'); ?></td></tr>
    <tr><th>Prioritas</th><td><?php echo e($job->prioritas); ?></td></tr>
    <tr><th>Status</th><td><?php echo e($job->status); ?></td></tr>
    <tr><th>Lokasi Gedung</th><td><?php echo e($job->lok_gedung ?? '-'); ?></td></tr>
    <tr><th>Lokasi Ruang</th><td><?php echo e($job->lokasi_ruang ?? '-'); ?></td></tr>
    <tr><th>Perangkat</th><td><?php echo e($job->computer_name ?? '-'); ?></td></tr>
    <tr><th>Merk</th><td><?php echo e($job->merk ?? '-'); ?></td></tr>
    <tr><th>Teknisi</th><td><?php echo e($job->teknisi->display_name ?? '-'); ?></td></tr>
    <tr><th>Admin Pembuat</th><td><?php echo e($job->admin->display_name ?? 'Dibuat sendiri oleh teknisi'); ?></td></tr>
    <tr><th>Tanggal Tugas</th><td><?php echo e($job->tanggal_tugas?->format('d-m-Y')); ?></td></tr>
    <tr><th>Tanggal Selesai</th><td><?php echo e($job->tanggal_selesai?->format('d-m-Y H:i') ?? '-'); ?></td></tr>
    <tr><th>Hasil Pekerjaan</th><td><?php echo e($job->hasil_pekerjaan ?? '-'); ?></td></tr>
    <tr><th>Catatan Teknisi</th><td><?php echo e($job->catatan ?? '-'); ?></td></tr>
</table>

<h3>Foto Sebelum Pekerjaan</h3>
<div>
    <?php $__empty_1 = true; $__currentLoopData = $job->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="foto-box"><img src="<?php echo e(asset($f->nama_file)); ?>"></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>- Tidak ada foto -</p>
    <?php endif; ?>
</div>

<h3>Foto Sesudah Pekerjaan</h3>
<div>
    <?php $__empty_1 = true; $__currentLoopData = $job->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="foto-box"><img src="<?php echo e(asset($f->nama_file)); ?>"></div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p>- Tidak ada foto -</p>
    <?php endif; ?>
</div>

<div class="ttd">
    <div><p>Teknisi <?php echo e($job->bidangLabel()); ?></p></div>
    <div><p>Admin</p></div>
</div>

<div class="no-print">
    <button onclick="window.print()" style="padding:10px 20px; background:#0d6efd; color:white; border:none; border-radius:5px; cursor:pointer;">
        🖨️ Cetak Sekarang
    </button>
</div>

</body>
</html><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/teknisi/cetak.blade.php ENDPATH**/ ?>