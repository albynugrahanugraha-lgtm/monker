@extends('layouts.app')
@section('title', 'Laporan Bulanan')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="mb-0 fw-bold">Laporan Bulanan Pekerjaan</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}" target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" @selected($bulan == $num)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Statistik -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6"><div class="card text-bg-primary shadow-sm p-3"><small>Total</small><h3 class="mb-0">{{ $stats['total'] }}</h3></div></div>
    <div class="col-md-3 col-6"><div class="card text-bg-secondary shadow-sm p-3"><small>Ditugaskan</small><h3 class="mb-0">{{ $stats['ditugaskan'] }}</h3></div></div>
    <div class="col-md-3 col-6"><div class="card text-bg-warning shadow-sm p-3"><small>Dikerjakan</small><h3 class="mb-0">{{ $stats['dikerjakan'] }}</h3></div></div>
    <div class="col-md-3 col-6"><div class="card text-bg-success shadow-sm p-3"><small>Selesai</small><h3 class="mb-0">{{ $stats['selesai'] }}</h3></div></div>
</div>

<!-- GRAFIK BULANAN (12 Bulan Terakhir) -->
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

<!-- GRAFIK PER HARI -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-calendar-range text-success"></i>
            Grafik Pekerjaan per Hari — {{ $daftarBulan[$bulan] }} {{ $tahun }}
        </h6>
        <div style="position: relative; height: 250px;">
            <canvas id="chartHarian"></canvas>
        </div>
    </div>
</div>

<!-- Rekap per Teknisi -->
@if($rekapTeknisi->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light"><h6 class="mb-0">Rekap per Teknisi</h6></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Teknisi</th><th class="text-center">Total Tugas</th><th class="text-center">Selesai</th></tr>
            </thead>
            <tbody>
            @foreach($rekapTeknisi as $r)
                <tr>
                    <td>{{ $r->teknisi->display_name ?? '-' }}</td>
                    <td class="text-center">{{ $r->total }}</td>
                    <td class="text-center"><span class="badge bg-success">{{ $r->selesai }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Detail Pekerjaan -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">Detail Pekerjaan — {{ $daftarBulan[$bulan] }} {{ $tahun }}</h6>
    </div>
    <div class="card-body">
    @forelse($jobs as $j)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1"><code>{{ $j->kode_job }}</code> — {{ $j->judul }}</h6>
                    <p class="mb-1 small text-muted">
                        <i class="bi bi-person"></i> {{ $j->teknisi->display_name ?? '-' }} ·
                        <i class="bi bi-calendar"></i> {{ $j->tanggal_tugas?->format('d-m-Y') }} ·
                        <i class="bi bi-geo-alt"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                    </p>
                </div>
                <div>
                    @php $badge = match($j->status) {
                        'DITUGASKAN' => 'bg-secondary',
                        'DIKERJAKAN' => 'bg-warning text-dark',
                        'SELESAI' => 'bg-success',
                        default => 'bg-light text-dark',
                    }; @endphp
                    <span class="badge {{ $badge }}">{{ $j->status }}</span>
                </div>
            </div>

            @if($j->hasil_pekerjaan)
                <p class="mb-2 small"><strong>Hasil:</strong> {{ $j->hasil_pekerjaan }}</p>
            @endif

            <div class="row g-3 mt-2">
                {{-- FOTO SEBELUM --}}
                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>Foto Sebelum:</strong></div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($j->fotoSebelum->count() > 0)
                            @foreach($j->fotoSebelum as $f)
                                <a href="{{ asset($f->nama_file) }}" target="_blank">
                                    <img src="{{ asset($f->nama_file) }}" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                                </a>
                            @endforeach
                        @else
                            <span class="small text-muted fst-italic">Tidak ada foto</span>
                        @endif
                    </div>
                </div>

                {{-- FOTO SESUDAH --}}
                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>Foto Sesudah:</strong></div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($j->fotoSesudah->count() > 0)
                            @foreach($j->fotoSesudah as $f)
                                <a href="{{ asset($f->nama_file) }}" target="_blank">
                                    <img src="{{ asset($f->nama_file) }}" class="rounded border" style="width: 90px; height: 90px; object-fit: cover;">
                                </a>
                            @endforeach
                        @else
                            <span class="small text-muted fst-italic">Tidak ada foto</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5">Belum ada pekerjaan di bulan ini</div>
    @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ===== GRAFIK 12 BULAN TERAKHIR =====
    const ctx = document.getElementById('chartLaporan');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: 'Total',
                        data: @json($chartData['data']),
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                    {
                        label: 'Selesai',
                        data: @json($chartData['selesai']),
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
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } }
                }
            }
        });
    }

    // ===== GRAFIK PER HARI =====
    const ctxHarian = document.getElementById('chartHarian');
    if (ctxHarian) {
        new Chart(ctxHarian, {
            type: 'bar',
            data: {
                labels: @json($chartHarian['labels']),
                datasets: [
                    {
                        label: 'Total Pekerjaan',
                        data: @json($chartHarian['data']),
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Selesai',
                        data: @json($chartHarian['selesai']),
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
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.85)',
                        padding: 10,
                        callbacks: {
                            title: function(context) {
                                return 'Tanggal ' + context[0].label + ' {{ $daftarBulan[$bulan] }} {{ $tahun }}';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, font: { size: 10 } },
                        title: {
                            display: true,
                            text: 'Jumlah Pekerjaan',
                            font: { size: 11, weight: 'bold' }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } },
                        title: {
                            display: true,
                            text: 'Tanggal',
                            font: { size: 11, weight: 'bold' }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush