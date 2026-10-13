<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        <tr>
            <th colspan="14" style="text-align: center; font-size: 18px; font-weight: bold; background: #4a90e2; color: white;">
                LAPORAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="14" style="text-align: center; font-size: 13px; background: #e8f0fe;">
                Periode:
                <?php if($bulan && $bulan !== 'semua'): ?>
                    <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>

                <?php else: ?>
                    Tahun <?php echo e($tahun); ?> (Januari - Desember)
                <?php endif; ?>
                <?php if(request('bidang')): ?>
                    — Bidang: <?php echo e(request('bidang') === 'cctv' ? 'CCTV' : 'Komputer'); ?>

                <?php endif; ?>
            </th>
        </tr>
        <tr><td colspan="14"></td></tr>

        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">A. STATISTIK</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">Total</th>
            <th style="background: #d0e0ff;">Ditugaskan</th>
            <th style="background: #d0e0ff;">Dikerjakan</th>
            <th style="background: #d0e0ff;">Selesai</th>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold;"><?php echo e($stats['total']); ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo e($stats['ditugaskan']); ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo e($stats['dikerjakan']); ?></td>
            <td style="text-align: center; font-weight: bold;"><?php echo e($stats['selesai']); ?></td>
        </tr>
        <tr><td colspan="14"></td></tr>

        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">B. REKAP PER BULAN</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 150px;">Bulan</th>
            <th style="background: #d0e0ff; width: 100px;">Total</th>
            <th style="background: #d0e0ff; width: 100px;">Selesai</th>
        </tr>
        <?php $__currentLoopData = $chartTahunan['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td><?php echo e($label); ?> <?php echo e($tahun); ?></td>
            <td style="text-align: center;"><?php echo e($chartTahunan['data'][$i] ?? 0); ?></td>
            <td style="text-align: center;"><?php echo e($chartTahunan['selesai'][$i] ?? 0); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr><td colspan="14"></td></tr>

        
        <?php if($rekapTeknisi->count() > 0): ?>
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">C. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Nama Teknisi</th>
            <th style="background: #d0e0ff;">Total</th>
            <th style="background: #d0e0ff;">Selesai</th>
            <th style="background: #d0e0ff;">Pending</th>
            <th style="background: #d0e0ff;">Rasio</th>
        </tr>
        <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="text-align: center;"><?php echo e($i + 1); ?></td>
            <td><?php echo e($r->teknisi->display_name ?? '-'); ?></td>
            <td style="text-align: center;"><?php echo e($r->total); ?></td>
            <td style="text-align: center;"><?php echo e($r->selesai); ?></td>
            <td style="text-align: center;"><?php echo e($r->pending); ?></td>
            <td style="text-align: center;">
                <?php echo e($r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0); ?>%
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr><td colspan="14"></td></tr>
        <?php endif; ?>

        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">
                D. DETAIL PEKERJAAN (<?php echo e($jobs->count()); ?> pekerjaan)
            </th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Kode</th>
            <th style="background: #d0e0ff;">Bidang</th>
            <th style="background: #d0e0ff;">Tanggal</th>
            <th style="background: #d0e0ff;">Judul</th>
            <th style="background: #d0e0ff;">Teknisi</th>
            <th style="background: #d0e0ff;">Gedung</th>
            <th style="background: #d0e0ff;">Ruang</th>
            <th style="background: #d0e0ff;">Perangkat</th>
            <th style="background: #d0e0ff;">Kategori</th>
            <th style="background: #d0e0ff;">Prioritas</th>
            <th style="background: #d0e0ff;">Status</th>
            <th style="background: #d0e0ff;">Hasil</th>
            <th style="background: #d0e0ff;">Catatan</th>
        </tr>
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td style="text-align: center;"><?php echo e($i + 1); ?></td>
            <td><?php echo e($j->kode_job); ?></td>
            <td><?php echo e($j->bidangLabel()); ?></td>
            <td style="text-align: center;"><?php echo e($j->tanggal_tugas?->format('d-m-Y')); ?></td>
            <td><?php echo e($j->judul); ?></td>
            <td><?php echo e($j->teknisi->display_name ?? '-'); ?></td>
            <td><?php echo e($j->lok_gedung ?? '-'); ?></td>
            <td><?php echo e($j->lokasi_ruang ?? '-'); ?></td>
            <td><?php echo e($j->computer_name ?? '-'); ?> (<?php echo e($j->merk ?? '-'); ?>)</td>
            <td><?php echo e($j->kategori ?? '-'); ?></td>
            <td><?php echo e($j->prioritas); ?></td>
            <td><?php echo e($j->status); ?></td>
            <td><?php echo e($j->hasil_pekerjaan ?? '-'); ?></td>
            <td><?php echo e($j->catatan ?? '-'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="14" style="text-align: center;">Tidak ada data</td></tr>
        <?php endif; ?>

        <tr><td colspan="14"></td></tr>

        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="14" style="text-align: left; font-weight: bold;">E. LINK FOTO PER PEKERJAAN</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff;">No</th>
            <th style="background: #d0e0ff;">Kode</th>
            <th style="background: #d0e0ff;" colspan="5">Foto Sebelum</th>
            <th style="background: #d0e0ff;" colspan="5">Foto Sesudah</th>
        </tr>
        <?php $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="text-align: center;"><?php echo e($i + 1); ?></td>
            <td><?php echo e($j->kode_job); ?></td>
            <td colspan="5" style="font-size: 9px;">
                <?php $__empty_1 = true; $__currentLoopData = $j->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo e(asset($f->nama_file)); ?>

                    <?php if(!$loop->last): ?> &nbsp;|&nbsp; <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    -
                <?php endif; ?>
            </td>
            <td colspan="5" style="font-size: 9px;">
                <?php $__empty_1 = true; $__currentLoopData = $j->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo e(asset($f->nama_file)); ?>

                    <?php if(!$loop->last): ?> &nbsp;|&nbsp; <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr><td colspan="14"></td></tr>
        <tr>
            <td colspan="14" style="text-align: center; font-style: italic; font-size: 11px;">
                Dicetak pada: <?php echo e(now()->format('d-m-Y H:i')); ?>

            </td>
        </tr>
    </thead>
</table><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/excel.blade.php ENDPATH**/ ?>