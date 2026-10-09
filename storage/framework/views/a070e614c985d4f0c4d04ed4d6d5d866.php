<table border="1">
    <thead>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN BULANAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="12" style="text-align: center; font-size: 12px;">
                Periode: <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>

                <?php if(request('bidang')): ?>
                    — Bidang: <?php echo e(request('bidang') === 'cctv' ? 'CCTV' : 'Komputer'); ?>

                <?php endif; ?>
                <?php if($user->isAnyTeknisi()): ?>
                    — Teknisi: <?php echo e($user->display_name); ?>

                <?php endif; ?>
            </th>
        </tr>
        <tr><td colspan="12"></td></tr>

        
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">A. STATISTIK</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">Total</th>
            <th style="background-color: #e0e0e0;">Ditugaskan</th>
            <th style="background-color: #e0e0e0;">Dikerjakan</th>
            <th style="background-color: #e0e0e0;">Selesai</th>
        </tr>
        <tr>
            <td style="text-align: center;"><?php echo e($stats['total']); ?></td>
            <td style="text-align: center;"><?php echo e($stats['ditugaskan']); ?></td>
            <td style="text-align: center;"><?php echo e($stats['dikerjakan']); ?></td>
            <td style="text-align: center;"><?php echo e($stats['selesai']); ?></td>
        </tr>
        <tr><td colspan="12"></td></tr>

        
        <?php if($rekapTeknisi->count() > 0): ?>
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">B. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">No</th>
            <th style="background-color: #e0e0e0;">Nama Teknisi</th>
            <th style="background-color: #e0e0e0;">Total Tugas</th>
            <th style="background-color: #e0e0e0;">Selesai</th>
        </tr>
        <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="text-align: center;"><?php echo e($i + 1); ?></td>
            <td><?php echo e($r->teknisi->display_name ?? '-'); ?></td>
            <td style="text-align: center;"><?php echo e($r->total); ?></td>
            <td style="text-align: center;"><?php echo e($r->selesai); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr><td colspan="12"></td></tr>
        <?php endif; ?>

        
        <tr style="background-color: #f0f0f0;">
            <th colspan="12" style="text-align: left; font-weight: bold;">C. DETAIL PEKERJAAN</th>
        </tr>
        <tr>
            <th style="background-color: #e0e0e0;">No</th>
            <th style="background-color: #e0e0e0;">Kode</th>
            <th style="background-color: #e0e0e0;">Bidang</th>
            <th style="background-color: #e0e0e0;">Tanggal</th>
            <th style="background-color: #e0e0e0;">Judul</th>
            <th style="background-color: #e0e0e0;">Teknisi</th>
            <th style="background-color: #e0e0e0;">Lokasi</th>
            <th style="background-color: #e0e0e0;">Perangkat</th>
            <th style="background-color: #e0e0e0;">Prioritas</th>
            <th style="background-color: #e0e0e0;">Status</th>
            <th style="background-color: #e0e0e0;">Hasil Pekerjaan</th>
            <th style="background-color: #e0e0e0;">Catatan</th>
        </tr>
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td style="text-align: center;"><?php echo e($i + 1); ?></td>
            <td><?php echo e($j->kode_job); ?></td>
            <td><?php echo e($j->bidangLabel()); ?></td>
            <td><?php echo e($j->tanggal_tugas?->format('d-m-Y')); ?></td>
            <td><?php echo e($j->judul); ?></td>
            <td><?php echo e($j->teknisi->display_name ?? '-'); ?></td>
            <td><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td>
            <td><?php echo e($j->computer_name ?? '-'); ?> (<?php echo e($j->merk ?? '-'); ?>)</td>
            <td><?php echo e($j->prioritas); ?></td>
            <td><?php echo e($j->status); ?></td>
            <td><?php echo e($j->hasil_pekerjaan ?? '-'); ?></td>
            <td><?php echo e($j->catatan ?? '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="12" style="text-align: center;">Tidak ada data</td></tr>
        <?php endif; ?>

        <tr><td colspan="12"></td></tr>
        <tr>
            <td colspan="12" style="text-align: center;">
                Dicetak pada: <?php echo e(now()->format('d-m-Y H:i')); ?>

            </td>
        </tr>
    </thead>
</table><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/excel.blade.php ENDPATH**/ ?>