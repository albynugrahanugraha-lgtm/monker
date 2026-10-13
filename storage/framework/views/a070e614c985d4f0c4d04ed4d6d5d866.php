<table border="1" cellpadding="4" cellspacing="0">
    <thead>
        
        <tr>
            <th colspan="20" style="text-align: center; font-size: 18px; font-weight: bold; background: #4a90e2; color: white;">
                LAPORAN PEKERJAAN TEKNISI
            </th>
        </tr>
        <tr>
            <th colspan="20" style="text-align: center; font-size: 13px; background: #e8f0fe;">
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
        <tr><td colspan="20"></td></tr>

        
        
        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">A. REKAP PER BULAN — <?php echo e($tahun); ?></th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 120px;">Keterangan</th>
            <?php $__currentLoopData = $chartTahunan['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th style="background: #d0e0ff; text-align: center; width: 60px;"><?php echo e($label); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th style="background: #b3d1ff; text-align: center; width: 80px; font-weight: bold;">TOTAL</th>
            <th colspan="7" style="background: #d0e0ff;"></th>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Total Pekerjaan</td>
            <?php $__currentLoopData = $chartTahunan['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="text-align: center;"><?php echo e($val); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                <?php echo e(array_sum($chartTahunan['data'])); ?>

            </td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Selesai</td>
            <?php $__currentLoopData = $chartTahunan['selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="text-align: center;"><?php echo e($val); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                <?php echo e(array_sum($chartTahunan['selesai'])); ?>

            </td>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="background: #f0f7ff; font-weight: bold;">Pending</td>
            <?php $__currentLoopData = $chartTahunan['data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="text-align: center;">
                    <?php echo e($val - ($chartTahunan['selesai'][$i] ?? 0)); ?>

                </td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td style="text-align: center; font-weight: bold; background: #e8f0fe;">
                <?php echo e(array_sum($chartTahunan['data']) - array_sum($chartTahunan['selesai'])); ?>

            </td>
            <td colspan="7"></td>
        </tr>
        <tr><td colspan="20"></td></tr>

        
        
        
        <?php if($rekapTeknisi->count() > 0): ?>
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">B. REKAP PER TEKNISI</th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 40px; text-align: center;">No</th>
            <th style="background: #d0e0ff; width: 200px;">Nama Teknisi</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Total</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Selesai</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Pending</th>
            <th style="background: #d0e0ff; width: 80px; text-align: center;">Rasio</th>
            <?php $__currentLoopData = $chartTahunan['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th style="background: #d0e0ff; text-align: center; width: 50px; font-size: 10px;"><?php echo e($label); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th colspan="6" style="background: #d0e0ff;"></th>
        </tr>
        <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0;
                $bulanDetail = [];
                for ($m = 1; $m <= 12; $m++) {
                    $count = \App\Models\Job::where('teknisi_id', $r->teknisi_id)
                        ->whereMonth('tanggal_tugas', $m)
                        ->whereYear('tanggal_tugas', $tahun)
                        ->where('status', 'SELESAI')
                        ->count();
                    $bulanDetail[] = $count;
                }
            ?>
            <tr>
                <td style="text-align: center;"><?php echo e($i + 1); ?></td>
                <td><?php echo e($r->teknisi->display_name ?? '-'); ?></td>
                <td style="text-align: center;"><?php echo e($r->total); ?></td>
                <td style="text-align: center;"><?php echo e($r->selesai); ?></td>
                <td style="text-align: center;"><?php echo e($r->pending); ?></td>
                <td style="text-align: center;"><?php echo e($rasio); ?>%</td>
                <?php $__currentLoopData = $bulanDetail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <td style="text-align: center;">
                        <?php if($val > 0): ?> <?php echo e($val); ?> <?php else: ?> - <?php endif; ?>
                    </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td colspan="6"></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr style="background: #e8f0fe; font-weight: bold;">
            <td colspan="2" style="text-align: right;">TOTAL</td>
            <td style="text-align: center;"><?php echo e($rekapTeknisi->sum('total')); ?></td>
            <td style="text-align: center;"><?php echo e($rekapTeknisi->sum('selesai')); ?></td>
            <td style="text-align: center;"><?php echo e($rekapTeknisi->sum('pending')); ?></td>
            <td style="text-align: center;">
                <?php
                    $totalAll = $rekapTeknisi->sum('total');
                    $selesaiAll = $rekapTeknisi->sum('selesai');
                ?>
                <?php echo e($totalAll > 0 ? round(($selesaiAll / $totalAll) * 100) : 0); ?>%
            </td>
            <?php $__currentLoopData = $chartTahunan['labels']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <td style="text-align: center;"><?php echo e($chartTahunan['selesai'][$i] ?? 0); ?></td>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <td colspan="6"></td>
        </tr>
        <tr><td colspan="20"></td></tr>
        <?php endif; ?>

        
        
        
        <tr style="background: #4a90e2; color: white;">
            <th colspan="20" style="text-align: left; font-weight: bold;">
                C. DETAIL PEKERJAAN (<?php echo e($jobs->count()); ?> pekerjaan)
            </th>
        </tr>
        <tr>
            <th style="background: #d0e0ff; width: 40px; text-align: center;">No</th>
            <th style="background: #d0e0ff; width: 100px;">Kode</th>
            <th style="background: #d0e0ff; width: 70px;">Bidang</th>
            <th style="background: #d0e0ff; width: 80px;">Tanggal</th>
            <th style="background: #d0e0ff;">Judul</th>
            <th style="background: #d0e0ff; width: 130px;">Teknisi</th>
            <th style="background: #d0e0ff; width: 100px;">Gedung</th>
            <th style="background: #d0e0ff; width: 100px;">Ruang</th>
            <th style="background: #d0e0ff; width: 120px;">Perangkat</th>
            <th style="background: #d0e0ff; width: 80px;">Kategori</th>
            <th style="background: #d0e0ff; width: 70px;">Prioritas</th>
            <th style="background: #d0e0ff; width: 70px;">Status</th>
            <th style="background: #d0e0ff;">Hasil</th>
            <th style="background: #d0e0ff;">Catatan</th>
            <th style="background: #d0e0ff; width: 200px;">📷 Foto Sebelum</th>
            <th style="background: #d0e0ff; width: 200px;">📷 Foto Sesudah</th>
            <th colspan="4" style="background: #d0e0ff;"></th>
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
            
            <td style="font-size: 9px;">
                <?php $__empty_2 = true; $__currentLoopData = $j->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank"
                       style="color: #0d6efd; text-decoration: underline;">
                        📷 Foto <?php echo e($loop->iteration); ?>

                    </a>
                    <?php if(!$loop->last): ?> <br> <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    -
                <?php endif; ?>
            </td>
            
            <td style="font-size: 9px;">
                <?php $__empty_2 = true; $__currentLoopData = $j->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank"
                       style="color: #0d6efd; text-decoration: underline;">
                        📷 Foto <?php echo e($loop->iteration); ?>

                    </a>
                    <?php if(!$loop->last): ?> <br> <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                    -
                <?php endif; ?>
            </td>
            <td colspan="4"></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="20" style="text-align: center;">Tidak ada data</td></tr>
        <?php endif; ?>

        <tr><td colspan="20"></td></tr>
        <tr>
            <td colspan="20" style="text-align: center; font-style: italic; font-size: 11px;">
                Dicetak pada: <?php echo e(now()->format('d-m-Y H:i')); ?>

            </td>
        </tr>
    </thead>
</table><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/excel.blade.php ENDPATH**/ ?>