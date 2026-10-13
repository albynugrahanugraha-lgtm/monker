<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pekerjaan — <?php echo e($tahun); ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15px; color: #000; }
        h1 { font-size: 16px; text-align: center; margin: 0 0 4px 0; }
        h2 { font-size: 13px; text-align: center; margin: 0 0 6px 0; font-weight: normal; }
        h3 { font-size: 12px; margin: 15px 0 6px 0; border-bottom: 2px solid #000; padding-bottom: 3px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        .text-center { text-align: center; }

        .stat-box { display: inline-block; border: 1px solid #333; padding: 8px 20px; margin: 4px; border-radius: 4px; font-size: 12px; }
        .stat-box strong { font-size: 14px; }

        .chart-box {
            border: 1px solid #333;
            padding: 12px;
            margin-bottom: 12px;
            background: #fafafa;
            page-break-inside: avoid;
        }

        .job-block { border: 1px solid #333; padding: 8px; margin-bottom: 12px; page-break-inside: avoid; }
        .job-block .job-header { font-weight: bold; font-size: 12px; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #ccc; }
        .job-block table { margin: 0 0 8px 0; }
        .job-block th { background: #eaeaea; width: 25%; }

        .badge-cctv { background: #0dcaf0; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 6px; }
        .badge-komputer { background: #0d6efd; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 6px; }
        .badge-status { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
        .status-selesai { background: #198754; color: white; }
        .status-dikerjakan { background: #ffc107; color: #000; }
        .status-ditugaskan { background: #6c757d; color: white; }

        /* FOTO */
        .foto-section { display: flex; gap: 15px; margin-top: 6px; }
        .foto-box { flex: 1; }
        .foto-label { font-size: 10px; font-weight: bold; margin-bottom: 4px; background: #eaeaea; padding: 3px 6px; border: 1px solid #999; }
        .foto-list { display: flex; flex-wrap: wrap; gap: 8px; }

        .foto-item { border: 1px solid #333; border-radius: 4px; overflow: hidden; background: white; }
        .foto-item img { width: 110px; height: 110px; object-fit: cover; display: block; cursor: pointer; }
        .foto-actions { display: flex; border-top: 1px solid #ccc; font-size: 9px; }
        .foto-actions a { flex: 1; text-align: center; padding: 3px 0; text-decoration: none; color: #0d6efd; font-weight: 600; background: #f8f9fa; }
        .foto-actions a:first-child { border-right: 1px solid #ccc; }
        .foto-empty { font-size: 10px; font-style: italic; color: #888; border: 1px dashed #ccc; padding: 10px; text-align: center; width: 110px; }

        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { width: 40%; text-align: center; }
        .ttd p { margin: 60px 0 0 0; border-top: 1px solid #000; padding-top: 5px; }

        .no-print { text-align: center; margin: 25px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .btn-print { padding: 12px 25px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; margin: 0 5px; }
        .btn-secondary { background: #6c757d; }

        #fotoModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center; padding: 20px; }
        #fotoModal img { max-width: 95%; max-height: 90%; border: 3px solid white; border-radius: 8px; }
        #fotoModal .close-btn { position: absolute; top: 15px; right: 25px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; background: rgba(0,0,0,0.5); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }

        @media print {
            .no-print { display: none !important; }
            .foto-actions { display: none !important; }
            #fotoModal { display: none !important; }
            body { margin: 0; }
            @page { size: A4; margin: 15mm 10mm; }
            .foto-item img { width: 130px; height: 130px; }
            .badge-cctv, .badge-komputer, .badge-status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<h1>LAPORAN PEKERJAAN TEKNISI</h1>
<h2>
    Periode:
    <?php if($bulan && $bulan !== 'semua'): ?>
        <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>

    <?php else: ?>
        Tahun <?php echo e($tahun); ?> (Januari - Desember)
    <?php endif; ?>
    <?php if(request('bidang')): ?>
        — Bidang: <?php echo e(request('bidang') === 'cctv' ? 'CCTV' : 'Komputer'); ?>

    <?php endif; ?>
</h2>


<h3>A. STATISTIK</h3>
<div>
    <span class="stat-box">Total: <strong><?php echo e($stats['total']); ?></strong></span>
    <span class="stat-box">Ditugaskan: <strong><?php echo e($stats['ditugaskan']); ?></strong></span>
    <span class="stat-box">Dikerjakan: <strong><?php echo e($stats['dikerjakan']); ?></strong></span>
    <span class="stat-box">Selesai: <strong><?php echo e($stats['selesai']); ?></strong></span>
</div>


<h3>B. GRAFIK PEKERJAAN PER BULAN — <?php echo e(strtoupper($tahun)); ?></h3>
<div class="chart-box">
    <?php
        $maxData = max($chartTahunan['data']) ?: 1;
        $chartHeight = 200;
        $chartWidth = 750;
        $padL = 35; $padR = 15; $padT = 15; $padB = 30;
        $plotW = $chartWidth - $padL - $padR;
        $plotH = $chartHeight - $padT - $padB;
        $jumlahBulan = 12;
        $barW = $plotW / $jumlahBulan;
    ?>

    <svg width="100%" viewBox="0 0 <?php echo e($chartWidth); ?> <?php echo e($chartHeight); ?>" xmlns="http://www.w3.org/2000/svg">
        <?php for($g = 0; $g <= 4; $g++): ?>
            <?php
                $y = $padT + ($plotH / 4) * $g;
                $labelVal = round($maxData - ($maxData / 4) * $g);
            ?>
            <line x1="<?php echo e($padL); ?>" y1="<?php echo e($y); ?>" x2="<?php echo e($chartWidth - $padR); ?>" y2="<?php echo e($y); ?>" stroke="#ddd" stroke-width="0.5"/>
            <text x="<?php echo e($padL - 5); ?>" y="<?php echo e($y + 3); ?>" font-size="9" text-anchor="end" fill="#666"><?php echo e($labelVal); ?></text>
        <?php endfor; ?>

        <?php for($d = 0; $d < $jumlahBulan; $d++): ?>
            <?php
                $val = $chartTahunan['data'][$d] ?? 0;
                $valSelesai = $chartTahunan['selesai'][$d] ?? 0;
                $barHeight = $maxData > 0 ? ($val / $maxData) * $plotH : 0;
                $barHeightSelesai = $maxData > 0 ? ($valSelesai / $maxData) * $plotH : 0;
                $x = $padL + $d * $barW;
                $y = $padT + $plotH - $barHeight;
                $ySelesai = $padT + $plotH - $barHeightSelesai;
            ?>

            
            <rect x="<?php echo e($x + 1); ?>" y="<?php echo e($y); ?>" width="<?php echo e(max($barW / 2 - 1, 3)); ?>" height="<?php echo e(max($barHeight, 0.5)); ?>"
                  fill="#0d6efd" stroke="#0d6efd" stroke-width="0.3"/>

            
            <rect x="<?php echo e($x + $barW / 2); ?>" y="<?php echo e($ySelesai); ?>" width="<?php echo e(max($barW / 2 - 1, 3)); ?>" height="<?php echo e(max($barHeightSelesai, 0.5)); ?>"
                  fill="#198754" stroke="#198754" stroke-width="0.3"/>

            
            <?php if($val > 0): ?>
                <text x="<?php echo e($x + $barW / 4); ?>" y="<?php echo e($y - 3); ?>" font-size="8" text-anchor="middle" fill="#0d6efd" font-weight="bold"><?php echo e($val); ?></text>
            <?php endif; ?>

            
            <text x="<?php echo e($x + $barW / 2); ?>" y="<?php echo e($chartHeight - 10); ?>" font-size="9" text-anchor="middle" fill="#333">
                <?php echo e($chartTahunan['labels'][$d]); ?>

            </text>
        <?php endfor; ?>
    </svg>

    <div style="text-align: center; margin-top: 6px; font-size: 10px;">
        <span style="display:inline-block; width:12px; height:12px; background:#0d6efd; border-radius:2px; vertical-align:middle;"></span> Total
        &nbsp;&nbsp;
        <span style="display:inline-block; width:12px; height:12px; background:#198754; border-radius:2px; vertical-align:middle;"></span> Selesai
    </div>
</div>


<?php if($bulan && $bulan !== 'semua'): ?>
<h3>C. GRAFIK PEKERJAAN PER HARI — <?php echo e(strtoupper($daftarBulan[$bulan])); ?> <?php echo e($tahun); ?></h3>
<div class="chart-box">
    <?php
        $maxData = max($chartHarian['data']) ?: 1;
        $jumlahHari = count($chartHarian['labels']);
        $barW = $plotW / $jumlahHari;
    ?>

    <svg width="100%" viewBox="0 0 <?php echo e($chartWidth); ?> <?php echo e($chartHeight); ?>" xmlns="http://www.w3.org/2000/svg">
        <?php for($g = 0; $g <= 4; $g++): ?>
            <?php
                $y = $padT + ($plotH / 4) * $g;
                $labelVal = round($maxData - ($maxData / 4) * $g);
            ?>
            <line x1="<?php echo e($padL); ?>" y1="<?php echo e($y); ?>" x2="<?php echo e($chartWidth - $padR); ?>" y2="<?php echo e($y); ?>" stroke="#ddd" stroke-width="0.5"/>
            <text x="<?php echo e($padL - 5); ?>" y="<?php echo e($y + 3); ?>" font-size="9" text-anchor="end" fill="#666"><?php echo e($labelVal); ?></text>
        <?php endfor; ?>

        <?php for($d = 0; $d < $jumlahHari; $d++): ?>
            <?php
                $val = $chartHarian['data'][$d] ?? 0;
                $barHeight = $maxData > 0 ? ($val / $maxData) * $plotH : 0;
                $x = $padL + $d * $barW;
                $y = $padT + $plotH - $barHeight;
            ?>
            <rect x="<?php echo e($x + 1); ?>" y="<?php echo e($y); ?>" width="<?php echo e(max($barW - 2, 3)); ?>" height="<?php echo e(max($barHeight, 0.5)); ?>"
                  fill="#0d6efd" stroke="#0d6efd" stroke-width="0.3"/>

            <?php if($val > 0): ?>
                <text x="<?php echo e($x + $barW / 2); ?>" y="<?php echo e($y - 3); ?>" font-size="7" text-anchor="middle" fill="#0d6efd" font-weight="bold"><?php echo e($val); ?></text>
            <?php endif; ?>

            <?php if($jumlahHari <= 15 || $d % 2 == 0): ?>
                <text x="<?php echo e($x + $barW / 2); ?>" y="<?php echo e($chartHeight - 10); ?>" font-size="8" text-anchor="middle" fill="#666">
                    <?php echo e($chartHarian['labels'][$d]); ?>

                </text>
            <?php endif; ?>
        <?php endfor; ?>
    </svg>
</div>
<?php endif; ?>


<?php if($rekapTeknisi->count() > 0): ?>
<h3><?php echo e($bulan && $bulan !== 'semua' ? 'D' : 'C'); ?>. REKAP PER TEKNISI</h3>
<table>
    <thead>
        <tr>
            <th style="width: 50px;" class="text-center">No</th>
            <th>Nama Teknisi</th>
            <th style="width: 100px;" class="text-center">Total</th>
            <th style="width: 100px;" class="text-center">Selesai</th>
            <th style="width: 100px;" class="text-center">Pending</th>
            <th style="width: 80px;" class="text-center">Rasio</th>
        </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0; ?>
        <tr>
            <td class="text-center"><?php echo e($i + 1); ?></td>
            <td><?php echo e($r->teknisi->display_name ?? '-'); ?></td>
            <td class="text-center"><?php echo e($r->total); ?></td>
            <td class="text-center"><?php echo e($r->selesai); ?></td>
            <td class="text-center"><?php echo e($r->pending); ?></td>
            <td class="text-center"><?php echo e($rasio); ?>%</td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php endif; ?>


<h3><?php echo e($bulan && $bulan !== 'semua' ? 'E' : 'D'); ?>. DETAIL PEKERJAAN (<?php echo e($jobs->count()); ?> pekerjaan)</h3>

<?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $statusClass = match($j->status) {
            'SELESAI' => 'status-selesai',
            'DIKERJAKAN' => 'status-dikerjakan',
            default => 'status-ditugaskan',
        };
    ?>
    <div class="job-block">
        <div class="job-header">
            #<?php echo e($i + 1); ?> — <?php echo e($j->kode_job); ?> — <?php echo e($j->judul); ?>

            <span class="<?php echo e($j->bidang === 'cctv' ? 'badge-cctv' : 'badge-komputer'); ?>">
                <?php echo e($j->bidangLabel()); ?>

            </span>
            <span style="float: right;">
                Status: <span class="badge-status <?php echo e($statusClass); ?>"><?php echo e($j->status); ?></span>
            </span>
        </div>

        <table>
            <tr><th>Teknisi</th><td><?php echo e($j->teknisi->display_name ?? '-'); ?></td></tr>
            <tr><th>Tanggal Tugas</th><td><?php echo e($j->tanggal_tugas?->format('d-m-Y')); ?></td></tr>
            <tr><th>Tanggal Selesai</th><td><?php echo e($j->tanggal_selesai?->format('d-m-Y H:i') ?? '-'); ?></td></tr>
            <tr><th>Lokasi</th><td><?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td></tr>
            <tr><th>Perangkat</th><td><?php echo e($j->computer_name ?? '-'); ?> (<?php echo e($j->merk ?? '-'); ?>)</td></tr>
            <tr><th>Kategori</th><td><?php echo e($j->kategori ?? '-'); ?></td></tr>
            <tr><th>Prioritas</th><td><?php echo e($j->prioritas); ?></td></tr>
            <tr><th>Hasil Pekerjaan</th><td><?php echo e($j->hasil_pekerjaan ?? '-'); ?></td></tr>
            <tr><th>Catatan</th><td><?php echo e($j->catatan ?? '-'); ?></td></tr>
        </table>

        <div class="foto-section">
            <div class="foto-box">
                <div class="foto-label">📷 Foto Sebelum</div>
                <div class="foto-list">
                    <?php if($j->fotoSebelum->count() > 0): ?>
                        <?php $__currentLoopData = $j->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="foto-item">
                                <img src="<?php echo e(asset($f->nama_file)); ?>" onclick="openFoto('<?php echo e(asset($f->nama_file)); ?>')">
                                <div class="foto-actions">
                                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">👁 Lihat</a>
                                    <a href="<?php echo e(asset($f->nama_file)); ?>" download>⬇ Save</a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="foto-empty">Tidak ada foto</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="foto-box">
                <div class="foto-label">📷 Foto Sesudah</div>
                <div class="foto-list">
                    <?php if($j->fotoSesudah->count() > 0): ?>
                        <?php $__currentLoopData = $j->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="foto-item">
                                <img src="<?php echo e(asset($f->nama_file)); ?>" onclick="openFoto('<?php echo e(asset($f->nama_file)); ?>')">
                                <div class="foto-actions">
                                    <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">👁 Lihat</a>
                                    <a href="<?php echo e(asset($f->nama_file)); ?>" download>⬇ Save</a>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="foto-empty">Tidak ada foto</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p style="text-align: center; font-style: italic;">Tidak ada pekerjaan pada periode ini</p>
<?php endif; ?>

<div class="ttd">
    <div><p>Mengetahui,</p></div>
    <div><p>Petugas,</p></div>
</div>

<div class="no-print">
    <p style="margin-bottom: 10px;">
        <strong>💡 Tips:</strong> Klik tombol di bawah, pilih <strong>"Save as PDF"</strong>.
        <br>
        <small>Centang <strong>"Background graphics"</strong> di print dialog supaya warna tetap tercetak.</small>
    </p>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Save as PDF</button>
    <button class="btn-print btn-secondary" onclick="window.close()">❌ Tutup</button>
</div>

<div id="fotoModal" onclick="if(event.target.id === 'fotoModal') closeFoto()">
    <span class="close-btn" onclick="closeFoto()">&times;</span>
    <img id="fotoModalImg" src="" alt="Preview">
</div>

<script>
    function openFoto(src) {
        document.getElementById('fotoModalImg').src = src;
        document.getElementById('fotoModal').style.display = 'flex';
    }
    function closeFoto() {
        document.getElementById('fotoModal').style.display = 'none';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFoto();
    });
</script>

</body>
</html><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/cetak.blade.php ENDPATH**/ ?>