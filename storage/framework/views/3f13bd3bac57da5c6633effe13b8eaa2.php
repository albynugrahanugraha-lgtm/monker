
<?php $__env->startSection('title', 'Laporan Pekerjaan'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="mb-0 fw-bold">
        <i class="bi bi-file-earmark-bar-graph"></i>
        Laporan Pekerjaan
    </h3>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?php echo e(route('laporan.excel', request()->query())); ?>"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="<?php echo e(route('laporan.cetak', request()->query())); ?>"
           target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>
    </div>
</div>


<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">📅 Tahun</label>
                <select name="tahun" class="form-select">
                    <?php $__currentLoopData = $tahunList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($y); ?>" <?php if($tahun == $y): echo 'selected'; endif; ?>><?php echo e($y); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">📆 Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="semua" <?php if($bulan === 'semua' || !$bulan): echo 'selected'; endif; ?>>🗓 Semua Bulan (1 Tahun)</option>
                    <?php $__currentLoopData = $daftarBulan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $nama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php if($bulan == $num): echo 'selected'; endif; ?>><?php echo e($nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <?php if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()): ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">📋 Bidang</label>
                <select name="bidang" class="form-select">
                    <option value="">Semua Bidang</option>
                    <option value="komputer" <?php if(request('bidang')==='komputer'): echo 'selected'; endif; ?>>🖥 Komputer</option>
                    <option value="cctv"     <?php if(request('bidang')==='cctv'): echo 'selected'; endif; ?>>📹 CCTV</option>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-2">
                <label class="form-label fw-semibold">📅 Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="<?php echo e($tanggalMulai ?? ''); ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label fw-semibold">📅 Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="<?php echo e($tanggalAkhir ?? ''); ?>">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('laporan.index')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>


<div class="alert alert-info d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill me-2" style="font-size: 1.3rem;"></i>
    <div>
        <strong>Periode:</strong>
        <?php if($tanggalMulai || $tanggalAkhir): ?>
            <?php if($tanggalMulai && $tanggalAkhir): ?>
                <?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d M Y')); ?> s/d <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')); ?>

            <?php elseif($tanggalMulai): ?>
                Sejak <?php echo e(\Carbon\Carbon::parse($tanggalMulai)->format('d M Y')); ?>

            <?php elseif($tanggalAkhir): ?>
                Sampai <?php echo e(\Carbon\Carbon::parse($tanggalAkhir)->format('d M Y')); ?>

            <?php endif; ?>
            <span class="badge bg-warning text-dark ms-1">Filter Tanggal Aktif</span>
        <?php else: ?>
            <?php if($bulan && $bulan !== 'semua'): ?>
                <?php echo e($daftarBulan[$bulan]); ?> <?php echo e($tahun); ?>

            <?php else: ?>
                Seluruh Tahun <?php echo e($tahun); ?> (Januari - Desember)
            <?php endif; ?>
        <?php endif; ?>
        <?php if(request('bidang')): ?>
            · Bidang: <strong><?php echo e(ucfirst(request('bidang'))); ?></strong>
        <?php endif; ?>
    </div>
</div>


<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-bar-chart-fill text-primary"></i>
            Grafik Pekerjaan per Bulan — Tahun <?php echo e($tahun); ?>

        </h6>
        <div style="position: relative; height: 280px;">
            <canvas id="chartTahunan"></canvas>
        </div>
    </div>
</div>


<?php if($bulan && $bulan !== 'semua'): ?>
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
<?php else: ?>
<div class="alert alert-secondary small">
    <i class="bi bi-info-circle"></i>
    Pilih <strong>bulan tertentu</strong> untuk melihat grafik per hari.
</div>
<?php endif; ?>


<?php if($rekapTeknisi->count() > 0): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-people-fill"></i> Rekap per Teknisi — Tahun <?php echo e($tahun); ?>

        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Nama Teknisi</th>
                    <th class="text-center" style="width: 120px;">Total Tugas</th>
                    <th class="text-center" style="width: 120px;">Selesai</th>
                    <th class="text-center" style="width: 120px;">Pending</th>
                    <th class="text-center" style="width: 100px;">Rasio</th>
                </tr>
            </thead>
            <tbody>
            <?php $__currentLoopData = $rekapTeknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0;
                    $badgeColor = $rasio >= 80 ? 'success' : ($rasio >= 50 ? 'warning' : 'danger');
                ?>
                <tr>
                    <td class="text-center"><?php echo e($i + 1); ?></td>
                    <td>
                        <i class="bi bi-person-circle text-primary"></i>
                        <?php echo e($r->teknisi->display_name ?? '-'); ?>

                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary"><?php echo e($r->total); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success"><?php echo e($r->selesai); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark"><?php echo e($r->pending); ?></span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?php echo e($badgeColor); ?>"><?php echo e($rasio); ?>%</span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>


<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-list-task"></i> Detail Pekerjaan (<?php echo e($jobs->count()); ?>)
        </h6>
        <small class="text-muted">Klik untuk melihat detail</small>
    </div>
    <div class="card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="accordion accordion-flush" id="accordionJob<?php echo e($j->id); ?>">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header" id="headingJob<?php echo e($j->id); ?>">
                        <button class="accordion-button collapsed" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseJob<?php echo e($j->id); ?>" 
                                aria-expanded="false" 
                                aria-controls="collapseJob<?php echo e($j->id); ?>">
                            <div class="d-flex w-100 justify-content-between align-items-center me-3 flex-wrap gap-2">
                                <div>
                                    <code class="me-2"><?php echo e($j->kode_job); ?></code>
                                    <strong><?php echo e($j->judul); ?></strong>
                                    <span class="badge <?php echo e($j->bidangBadgeClass()); ?> ms-1">
                                        <i class="bi <?php echo e($j->bidangIcon()); ?>"></i> <?php echo e($j->bidangLabel()); ?>

                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted d-none d-md-inline">
                                        <i class="bi bi-person"></i> <?php echo e($j->teknisi->display_name ?? '-'); ?>

                                    </small>
                                    <?php $badge = match($j->status) {
                                        'DITUGASKAN' => 'bg-secondary',
                                        'DIKERJAKAN' => 'bg-warning text-dark',
                                        'SELESAI'    => 'bg-success',
                                        default      => 'bg-light text-dark',
                                    }; ?>
                                    <span class="badge <?php echo e($badge); ?>"><?php echo e($j->status); ?></span>
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseJob<?php echo e($j->id); ?>" class="accordion-collapse collapse" 
                         aria-labelledby="headingJob<?php echo e($j->id); ?>" 
                         data-bs-parent="#accordionJob<?php echo e($j->id); ?>">
                        <div class="accordion-body bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <th width="120">Tanggal</th>
                                            <td>: <i class="bi bi-calendar"></i> <?php echo e($j->tanggal_tugas?->format('d-m-Y')); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi</th>
                                            <td>: <i class="bi bi-geo-alt"></i> <?php echo e($j->lok_gedung ?? '-'); ?> / <?php echo e($j->lokasi_ruang ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Perangkat</th>
                                            <td>: <?php echo e($j->computer_name ?? '-'); ?> (<?php echo e($j->merk ?? '-'); ?>)</td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>: <?php echo e($j->kategori ?? '-'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Prioritas</th>
                                            <td>: <?php echo e($j->prioritas); ?></td>
                                        </tr>
                                        <?php if($j->hasil_pekerjaan): ?>
                                        <tr>
                                            <th>Hasil</th>
                                            <td>: <?php echo e($j->hasil_pekerjaan); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if($j->catatan): ?>
                                        <tr>
                                            <th>Catatan</th>
                                            <td>: <?php echo e($j->catatan); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="small fw-bold text-muted mb-1">📷 Foto Sebelum:</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php if($j->fotoSebelum->count() > 0): ?>
                                                    <?php $__currentLoopData = $j->fotoSebelum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                                        </a>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <span class="small text-muted fst-italic">Tidak ada foto</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="small fw-bold text-muted mb-1">📷 Foto Sesudah:</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php if($j->fotoSesudah->count() > 0): ?>
                                                    <?php $__currentLoopData = $j->fotoSesudah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <a href="<?php echo e(asset($f->nama_file)); ?>" target="_blank">
                                                            <img src="<?php echo e(asset($f->nama_file)); ?>" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                                        </a>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <span class="small text-muted fst-italic">Tidak ada foto</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; opacity: .3;"></i>
                <p class="mt-2 mb-0">Belum ada pekerjaan pada periode ini</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // GRAFIK TAHUNAN
    const ctxTahunan = document.getElementById('chartTahunan');
    if (ctxTahunan) {
        new Chart(ctxTahunan, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartTahunan['labels'], 15, 512) ?>,
                datasets: [
                    {
                        label: 'Total',
                        data: <?php echo json_encode($chartTahunan['data'], 15, 512) ?>,
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                    {
                        label: 'Selesai',
                        data: <?php echo json_encode($chartTahunan['selesai'], 15, 512) ?>,
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
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        title: { display: true, text: 'Jumlah Pekerjaan' }
                    }
                }
            }
        });
    }

    // GRAFIK HARIAN (hanya kalau bulan dipilih)
    const ctxHarian = document.getElementById('chartHarian');
    if (ctxHarian) {
        new Chart(ctxHarian, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartHarian['labels'], 15, 512) ?>,
                datasets: [
                    {
                        label: 'Total',
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
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return 'Tanggal ' + context[0].label + ' <?php echo e($bulan && $bulan !== "semua" ? $daftarBulan[$bulan] : ""); ?> <?php echo e($tahun); ?>';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 }
                    }
                }
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\CHATBOT LARAVEL\monitoring-teknisi\resources\views/laporan/index.blade.php ENDPATH**/ ?>