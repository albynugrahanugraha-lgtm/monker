
<?php $__env->startSection('title', 'Laporan Bulanan'); ?>

<?php $__env->startSection('content'); ?>


<?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
<?php
    $bidangAktif = request('bidang') ?? 'semua';
?>
<ul class="nav nav-tabs mb-3" id="laporanTabs">
    <li class="nav-item">
        <a class="nav-link <?php echo e($bidangAktif === 'komputer' ? 'active fw-bold' : ''); ?>"
           href="<?php echo e(route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'komputer'])); ?>">
            <i class="bi bi-pc-display"></i> Laporan Komputer
            <?php if($bidangAktif === 'komputer'): ?>
                <span class="badge bg-primary ms-1">aktif</span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e($bidangAktif === 'cctv' ? 'active fw-bold' : ''); ?>"
           href="<?php echo e(route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'cctv'])); ?>">
            <i class="bi bi-camera-video"></i> Laporan CCTV
            <?php if($bidangAktif === 'cctv'): ?>
                <span class="badge bg-info text-dark ms-1">aktif</span>
            <?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo e($bidangAktif === 'semua' ? 'active fw-bold' : ''); ?>"
           href="<?php echo e(route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun])); ?>">
            <i class="bi bi-files"></i> Semua Bidang
            <?php if($bidangAktif === 'semua'): ?>
                <span class="badge bg-secondary ms-1">aktif</span>
            <?php endif; ?>
        </a>
    </li>
</ul>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="mb-0 fw-bold">
        <?php if(request('bidang') === 'komputer'): ?>
            <i class="bi bi-pc-display text-primary"></i> Laporan Komputer
        <?php elseif(request('bidang') === 'cctv'): ?>
            <i class="bi bi-camera-video text-info"></i> Laporan CCTV
        <?php else: ?>
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Bulanan Pekerjaan
        <?php endif; ?>
    </h3>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => request('bidang')])); ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="<?php echo e(route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => request('bidang')])); ?>" target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>
    </div>
</div>


<?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-dark text-white py-2">
        <h6 class="mb-0">
            <i class="bi bi-printer-fill"></i> Cetak Laporan Terpisah
            <small class="text-white-50 ms-2">— pilih bidang yang mau dicetak</small>
        </h6>
    </div>
    <div class="card-body py-3">
        <div class="row g-2">
            <div class="col-md-4">
                <a href="<?php echo e(route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'komputer'])); ?>"
                   target="_blank"
                   class="btn btn-outline-primary w-100 py-2">
                    <i class="bi bi-pc-display"></i>
                    <strong>Cetak Laporan Komputer</strong>
                    <br>
                    <small class="text-muted">Hanya pekerjaan bidang komputer</small>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo e(route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'cctv'])); ?>"
                   target="_blank"
                   class="btn btn-outline-info w-100 py-2">
                    <i class="bi bi-camera-video"></i>
                    <strong>Cetak Laporan CCTV</strong>
                    <br>
                    <small class="text-muted">Hanya pekerjaan bidang CCTV</small>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo e(route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun])); ?>"
                   target="_blank"
                   class="btn btn-outline-secondary w-100 py-2">
                    <i class="bi bi-files"></i>
                    <strong>Cetak Semua Bidang</strong>
                    <br>
                    <small class="text-muted">Komputer + CCTV digabung</small>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    <?php $__currentLoopData = $daftarBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php if($bulan == $num): echo 'selected'; endif; ?>><?php echo e($nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    <?php for($y = now()->year; $y >= now()->year - 5; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php if($tahun == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
                <div class="col-md-3">
                    <label class="form-label">Bidang</label>
                    <select name="bidang" class="form-select">
                        <option value="">Semua Bidang</option>
                        <option value="komputer" <?php if(request('bidang')==='komputer'): echo 'selected'; endif; ?>>Komputer</option>
                        <option value="cctv"     <?php if(request('bidang')==='cctv'): echo 'selected'; endif; ?>>CCTV</option>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-3">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card text-bg-primary shadow-sm p-3">
            <small>Total</small>
            <h3 class="mb-0"><?php echo e($stats['total']); ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-secondary shadow-sm p-3">
            <small>Ditugaskan</small>
            <h3 class="mb-0"><?php echo e($stats['ditugaskan']); ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-warning shadow-sm p-3">
            <small>Dikerjakan</small>
            <h3 class="mb-0"><?php echo e($stats['dikerjakan']); ?></h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-success shadow-sm p-3">
            <small>Selesai</small>
            <h3 class="mb-0"><?php echo e($stats['selesai']); ?></h3>
        </div>
    </div>
</div>


<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-bar-chart-fill text-primary"></i>
            Statistik 12 Bulan Terakhir
        </h6>
        <div style="position: relative; height: 250px;">
            <canvas id="chartLaporan"></canvas>
        </div>
    </div>
</div>


<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-calendar-range text-success"></i>
            Grafik Pekerjaan per Hari — <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>

        </h6>
        <div style="position: relative; height: 250px;">
            <canvas id="chartHarian"></canvas>
        </div>
    </div>
</div>


<?php if($rekapTeknisi->count() > 0): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light"><h6 class="mb-0">Rekap per Teknisi</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Teknisi</th>
                    <th class="text-center">Total Tugas</th>
                    <th class="text-center">Selesai</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($r->teknisi->display_name ?? '-'); ?></td>
                    <td class="text-center"><?php echo e($r->total); ?></td>
                    <td class="text-center"><span class="badge bg-success"><?php echo e($r->selesai); ?></span></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">Detail Pekerjaan — <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?></h6>
    </div>
    <div class="card-body">
    <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <div>
                    <h6 class="mb-1">
                        <code><?php echo e($j->kode_job); ?></code> — <?php echo e($j->judul); ?>

                        <span class="badge <?php echo e($j->bidangBadgeClass()); ?> ms-1">
                            <i class="bi <?php echo e($j->bidangIcon()); ?>"></i> <?php echo e($j->bidangLabel()); ?>

                        </span>
                    </h6>
                    <p class="mb-1 small text-muted">
                        <i class="bi bi-person"></i> <?php echo e($j->teknisi->display_name ?? '-'); ?> ·
                        <i class="bi bi-geo-alt"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?>

                    </p>
                </div>
                <div>
                    <?php $badge = match($j->status) {
                        'DITUGASKAN' => 'bg-secondary',
                        'DIKERJAKAN' => 'bg-warning text-dark',
                        'SELESAI'    => 'bg-success',
                        default      => 'bg-light text-dark',
                    }; ?>
                    <span class="badge <?php echo e($badge); ?>"><?php echo e($j->status); ?></span>
                </div>
            </div>

            
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100" style="background:#fff8e1; border-left:4px solid #ffc107 !important;">
                        <div class="small">
                            <i class="bi bi-calendar-plus text-warning"></i>
                            <strong>Tanggal Buat Tugas:</strong>
                        </div>
                        <div class="fw-bold" style="font-size:.9rem;">
                            <?php echo e($j->created_at?->format('d-m-Y H:i') ?? '-'); ?>

                        </div>
                        <small class="text-muted" style="font-size:.72rem;">
                            <i class="bi bi-person-badge"></i>
                            Dibuat oleh: <?php echo e($j->admin->display_name ?? 'Teknisi sendiri'); ?>

                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100" style="background:#e8f5e9; border-left:4px solid #198754 !important;">
                        <div class="small">
                            <i class="bi bi-calendar-check text-success"></i>
                            <strong>Tanggal Selesai:</strong>
                        </div>
                        <?php if($j->tanggal_selesai): ?>
                            <div class="fw-bold" style="font-size:.9rem;">
                                <?php echo e($j->tanggal_selesai->format('d-m-Y H:i')); ?>

                            </div>
                            <?php
                                $diff = $j->created_at->diff($j->tanggal_selesai);
                                $parts = [];
                                if ($diff->d > 0) $parts[] = $diff->d . ' hari';
                                if ($diff->h > 0) $parts[] = $diff->h . ' jam';
                                if ($diff->i > 0 && $diff->d == 0) $parts[] = $diff->i . ' menit';
                                $durasiText = implode(' ', $parts) ?: 'kurang dari 1 menit';
                            ?>
                            <small class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-stopwatch"></i>
                                Durasi: <?php echo e($durasiText); ?>

                            </small>
                        <?php else: ?>
                            <div class="fw-bold text-muted" style="font-size:.9rem;">
                                <em>Belum selesai</em>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-hourglass-split"></i>
                                Masih dalam proses
                            </small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="small text-muted mb-2">
                <i class="bi bi-calendar-event"></i>
                <strong>Tanggal Tugas:</strong> <?php echo e($j->tanggal_tugas?->format('d-m-Y') ?? '-'); ?>

            </div>

            <?php if($j->hasil_pekerjaan): ?>
                <p class="mb-2 small"><strong>Hasil:</strong> <?php echo e($j->hasil_pekerjaan); ?></p>
            <?php endif; ?>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>Foto Sebelum:</strong></div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if($j->fotoSebelum->count() > 0): ?>
                            <?php $__currentLoopData = $j->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                    <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="small text-muted fst-italic">Tidak ada foto</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>Foto Sesudah:</strong></div>
                    <div class="d-flex flex-wrap gap-2">
                        <?php if($j->fotoSesudah->count() > 0): ?>
                            <?php $__currentLoopData = $j->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                    <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span class="small text-muted fst-italic">Tidak ada foto</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="text-center text-muted py-5">
            <?php if(request('bidang') === 'komputer'): ?>
                Belum ada pekerjaan <strong>komputer</strong> di bulan ini
            <?php elseif(request('bidang') === 'cctv'): ?>
                Belum ada pekerjaan <strong>CCTV</strong> di bulan ini
            <?php else: ?>
                Belum ada pekerjaan di bulan ini
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartLaporan');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartData['labels'], 15, 512) ?>,
                datasets: [
                    {
                        label: 'Total',
                        data: <?php echo json_encode($chartData['data'], 15, 512) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                    {
                        label: 'Selesai',
                        data: <?php echo json_encode($chartData['selesai'], 15, 512) ?>,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
            }
        });
    }

    const ctxHarian = document.getElementById('chartHarian');
    if (ctxHarian) {
        new Chart(ctxHarian, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartHarian['labels'], 15, 512) ?>,
                datasets: [
                    {
                        label: 'Total Pekerjaan',
                        data: <?php echo json_encode($chartHarian['data'], 15, 512) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Selesai',
                        data: <?php echo json_encode($chartHarian['selesai'], 15, 512) ?>,
                        backgroundColor: 'rgba(25, 135, 84, 0.8)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 11 } } },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.85)',
                        padding: 10,
                        callbacks: {
                            title: function(context) {
                                return 'Tanggal ' + context[0].label + ' <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, font: { size: 10 } },
                        title: { display: true, text: 'Jumlah Pekerjaan', font: { size: 11, weight: 'bold' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } },
                        title: { display: true, text: 'Tanggal', font: { size: 11, weight: 'bold' } }
                    }
                }
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/index.blade.php ENDPATH**/ ?>