@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('styles')
<style>
    /* ============================================ */
    /* KELAP-KELIP                                  */
    /* ============================================ */
    @keyframes lampuBlink {
        0%, 100% { background-color: #ffeb3b; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuBlinkMerah {
        0%, 100% { background-color: #ffcdd2; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuPulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
            background: #dc3545;
            box-shadow: 0 0 10px #dc3545;
        }
        50% {
            transform: scale(1.4);
            opacity: 0.6;
            background: #ffc107;
            box-shadow: 0 0 20px #ffc107;
        }
    }

    tr.row-lampu,
    tr.row-lampu td {
        animation: lampuBlink 1s infinite ease-in-out !important;
        font-weight: 600 !important;
    }
    tr.row-lampu { border-left: 5px solid #dc3545 !important; }

    tr.row-lampu-merah,
    tr.row-lampu-merah td {
        animation: lampuBlinkMerah 0.9s infinite ease-in-out !important;
        font-weight: 700 !important;
    }
    tr.row-lampu-merah { border-left: 5px solid #b71c1c !important; }

    tr.tugas-sudah-kerja { border-left: 5px solid #198754 !important; }

    .lampu-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #dc3545;
        box-shadow: 0 0 10px #dc3545;
        animation: lampuPulse 0.8s infinite;
        margin-right: 6px;
        vertical-align: middle;
    }

    .badge-belum-diambil {
        animation: lampuBlinkMerah 0.9s infinite ease-in-out !important;
    }

    /* ============================================ */
    /* TABLE RANKING                                */
    /* ============================================ */
    .rank-medal {
        font-size: 1.2rem;
        display: inline-block;
    }
    .row-top-1 {
        background: linear-gradient(90deg, rgba(255, 215, 0, 0.15) 0%, transparent 100%);
        border-left: 4px solid #ffd700;
    }
    .row-top-2 {
        background: linear-gradient(90deg, rgba(192, 192, 192, 0.15) 0%, transparent 100%);
        border-left: 4px solid #c0c0c0;
    }
    .row-top-3 {
        background: linear-gradient(90deg, rgba(205, 127, 50, 0.15) 0%, transparent 100%);
        border-left: 4px solid #cd7f32;
    }

    /* ============================================ */
    /* CHART WRAPPER                                */
    /* ============================================ */
    .chart-wrapper {
        position: relative;
        width: 100%;
        min-height: 280px;
    }

    .chart-empty {
        text-align: center;
        padding: 50px 20px;
        color: #adb5bd;
    }
    .chart-empty i {
        font-size: 3rem;
        opacity: 0.4;
        display: block;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<h5 class="mb-3 fw-bold">Monitoring Pekerjaan Teknisi</h5>

{{-- 3 KOTAK BESAR --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">📥 Total Tugas Hari Ini</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['total'] }}</h2>
                    </div>
                    <i class="bi bi-calendar-day" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">✅ Sudah Dikerjakan</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['selesai'] }}</h2>
                    </div>
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">⏳ Belum Dikerjakan</small>
                        <h2 class="mb-0 fw-bold mt-2">{{ $hariIni['belum'] }}</h2>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 2.5rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL: HARI INI & KEMARIN --}}
<div class="row g-3 mb-4">

    {{-- HARI INI --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-2">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-day"></i>
                    Pekerjaan Hari Ini — {{ now()->translatedFormat('d F Y') }}
                    <span class="badge bg-light text-dark ms-2">{{ $kerjaanHariIni->count() }}</span>
                </h6>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 18%;">Kode</th>
                            <th style="width: 42%;">Pekerjaan</th>
                            <th style="width: 20%;">Teknisi</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanHariIni as $j)
                        @php
                            $badgeClass = match($j->status) {
                                'DITUGASKAN' => 'bg-secondary',
                                'DIKERJAKAN' => 'bg-warning text-dark',
                                'SELESAI'    => 'bg-success',
                                default => 'bg-light text-dark',
                            };
                            $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                            $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                            if ($belumDiambil) {
                                $rowClass = 'row-lampu-merah tugas-belum-diambil';
                            } elseif ($belumDikerjakan) {
                                $rowClass = 'row-lampu tugas-belum-kerja';
                            } else {
                                $rowClass = 'tugas-sudah-kerja';
                            }
                        @endphp
                        <tr class="{{ $rowClass }} job-row-admin" data-status="{{ $j->status }}">
                            <td>
                                @if($belumDikerjakan || $belumDiambil)
                                    <span class="lampu-indicator"></span>
                                @endif
                                <code style="font-size: .7rem;">{{ $j->kode_job }}</code>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $j->judul }}</div>
                                <small class="text-muted" style="font-size: .7rem;">
                                    <i class="bi bi-geo-alt-fill"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                                </small>
                            </td>
                            <td>
                                @if($j->teknisi)
                                    <span class="badge bg-primary"><i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}</span>
                                @else
                                    <span class="badge bg-danger badge-belum-diambil"><i class="bi bi-exclamation-triangle"></i> Belum diambil</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $j->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pekerjaan hari ini</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KEMARIN --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-secondary text-white py-2">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-check"></i>
                    Pekerjaan Kemarin — {{ now()->subDay()->translatedFormat('d F Y') }}
                    <span class="badge bg-light text-dark ms-2">{{ $kerjaanKemarin->count() }}</span>
                </h6>
            </div>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 18%;">Kode</th>
                            <th style="width: 42%;">Pekerjaan</th>
                            <th style="width: 20%;">Teknisi</th>
                            <th style="width: 20%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanKemarin as $j)
                        @php
                            $badgeClass = match($j->status) {
                                'DITUGASKAN' => 'bg-secondary',
                                'DIKERJAKAN' => 'bg-warning text-dark',
                                'SELESAI'    => 'bg-success',
                                default => 'bg-light text-dark',
                            };
                        @endphp
                        <tr>
                            <td><code style="font-size: .7rem;">{{ $j->kode_job }}</code></td>
                            <td>
                                <div class="fw-semibold">{{ $j->judul }}</div>
                                <small class="text-muted" style="font-size: .7rem;">
                                    <i class="bi bi-geo-alt-fill"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                                </small>
                            </td>
                            <td>
                                @if($j->teknisi)
                                    <span class="badge bg-primary"><i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}</span>
                                @else
                                    <span class="badge bg-secondary">Belum diambil</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $j->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pekerjaan kemarin</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- TOP TEKNISI HARI INI --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-warning text-dark py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-trophy-fill"></i>
            Top Teknisi Hari Ini — {{ now()->translatedFormat('d F Y') }}
        </h6>
        <small class="text-dark" style="font-size: .75rem;">
            <i class="bi bi-info-circle"></i> Berdasarkan jumlah tugas selesai terbanyak
        </small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: .9rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th style="width: 30%;">Teknisi</th>
                    <th style="width: 15%;" class="text-center">✅ Selesai</th>
                    <th style="width: 15%;" class="text-center">⏳ Belum</th>
                    <th style="width: 15%;" class="text-center">📊 Total</th>
                    <th style="width: 19%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse($ratingHariIni as $i => $t)
                <tr class="{{ $i == 0 ? 'table-warning' : '' }}">
                    <td class="text-center fw-bold" style="font-size: 1.2rem;">
                        @if($i == 0) 🥇
                        @elseif($i == 1) 🥈
                        @elseif($i == 2) 🥉
                        @else {{ $i + 1 }}
                        @endif
                    </td>
                    <td>
                        <i class="bi bi-person-circle text-primary"></i>
                        <strong>{{ $t->display_name }}</strong>
                        @if($i == 0)
                            <span class="badge bg-warning text-dark ms-2">🏆 TOP</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success" style="font-size: 1rem; padding: 8px 16px;">
                            {{ $t->hari_ini_selesai }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark" style="font-size: 1rem; padding: 8px 16px;">
                            {{ $t->hari_ini_belum }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary" style="font-size: 1rem; padding: 8px 16px;">
                            {{ $t->hari_ini_total }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($i == 0)
                            <span class="badge bg-success" style="font-size: .85rem; padding: 6px 12px;">⭐ Terbaik</span>
                        @else
                            <span class="badge bg-light text-dark border">Peringkat {{ $i + 1 }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem; opacity: .3;"></i>
                        <div class="mt-2">Belum ada teknisi yang mengerjakan tugas hari ini</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- GRAFIK TOP TEKNISI (OVERALL) --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-bar-chart-fill text-primary"></i>
            Top Teknisi Terbanyak Menyelesaikan Tugas (Keseluruhan)
        </h6>
        <small class="text-muted" style="font-size: .75rem;">
            <i class="bi bi-info-circle"></i> Berdasarkan jumlah tugas selesai terbanyak
        </small>
    </div>
    <div class="card-body">

        @if($rekapTeknisi->count() > 0)
            <div class="chart-wrapper" style="height: {{ max(280, $rekapTeknisi->count() * 70) }}px;">
                <canvas id="chartTopTeknisi"></canvas>
            </div>
        @else
            <div class="chart-empty">
                <i class="bi bi-bar-chart"></i>
                <h5>Belum Ada Data</h5>
                <p class="mb-0">Belum ada teknisi yang menyelesaikan tugas</p>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    // ============================================================
    // GRAFIK TOP TEKNISI (berdasarkan jumlah tugas selesai)
    // ============================================================
    const ctx1 = document.getElementById('chartTopTeknisi');

    if (ctx1) {
        const rekapData = @json($rekapTeknisi);
        console.log('📊 Rekap data grafik:', rekapData);

        if (rekapData.length === 0) {
            console.warn('⚠️ Tidak ada data untuk grafik');
        } else {
            // Warna berdasarkan peringkat
            const warnaBar = rekapData.map((t, i) => {
                if (i === 0) return 'rgba(255, 193, 7, 0.95)';   // 🥇 Emas
                if (i === 1) return 'rgba(108, 117, 125, 0.9)';   // 🥈 Perak
                if (i === 2) return 'rgba(205, 127, 50, 0.9)';    // 🥉 Perunggu
                return 'rgba(13, 110, 253, 0.7)';                 // Biru
            });

            const borderBar = rekapData.map((t, i) => {
                if (i === 0) return 'rgba(255, 193, 7, 1)';
                if (i === 1) return 'rgba(108, 117, 125, 1)';
                if (i === 2) return 'rgba(205, 127, 50, 1)';
                return 'rgba(13, 110, 253, 1)';
            });

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: rekapData.map(t => t.display_name),
                    datasets: [{
                        label: 'Tugas Selesai',
                        data: rekapData.map(t => t.selesai),
                        backgroundColor: warnaBar,
                        borderColor: borderBar,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        barPercentage: 0.7,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: { top: 15, right: 60, left: 5, bottom: 5 }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.9)',
                            padding: 14,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            displayColors: false,
                            cornerRadius: 8,
                            callbacks: {
                                title: function(context) {
                                    const t = rekapData[context[0].dataIndex];
                                    const rank = context[0].dataIndex + 1;
                                    let medal = '';
                                    if (rank === 1) medal = '🥇 ';
                                    else if (rank === 2) medal = '🥈 ';
                                    else if (rank === 3) medal = '🥉 ';
                                    return medal + t.display_name;
                                },
                                label: function(context) {
                                    const t = rekapData[context.dataIndex];
                                    return [
                                        '✅ Sudah selesai  : ' + t.selesai + ' tugas',
                                        '⏳ Belum selesai  : ' + t.belum + ' tugas',
                                        '📊 Total tugas    : ' + t.total + ' tugas',
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: { size: 11, weight: 'bold' }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            title: {
                                display: true,
                                text: '📊 Jumlah Tugas Selesai',
                                font: { size: 11, weight: 'bold' }
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 12, weight: 'bold' },
                                color: '#212529'
                            }
                        }
                    },
                    animation: {
                        duration: 800,
                        onComplete: function() {
                            const chart = this;
                            const ctx = chart.ctx;

                            ctx.save();
                            ctx.textAlign = 'left';
                            ctx.textBaseline = 'middle';

                            chart.data.datasets.forEach(function(dataset, i) {
                                const meta = chart.getDatasetMeta(i);
                                meta.data.forEach(function(bar, index) {
                                    const t = rekapData[index];

                                    // Angka jumlah selesai di ujung bar
                                    ctx.font = 'bold 15px Arial';
                                    ctx.fillStyle = '#212529';
                                    ctx.fillText(t.selesai, bar.x + 10, bar.y - 6);

                                    // Info (dari X total) di bawah
                                    ctx.font = '10px Arial';
                                    ctx.fillStyle = '#6c757d';
                                    ctx.fillText('dari ' + t.total + ' tugas', bar.x + 10, bar.y + 9);
                                });
                            });
                            ctx.restore();
                        }
                    }
                }
            });
        }
    }

    // ============================================================
    // JS FALLBACK: KELAP-KELIP
    // ============================================================
    (function() {
        let state = false;
        setInterval(function() {
            state = !state;

            document.querySelectorAll('tr.row-lampu-merah').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#ffcdd2' : '#ffffff';
                });
            });

            document.querySelectorAll('tr.row-lampu').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#ffeb3b' : '#ffffff';
                });
            });
        }, 500);
    })();

    console.log('🔇 Alarm dimatikan di dashboard admin');
</script>
@endpush
@endsection