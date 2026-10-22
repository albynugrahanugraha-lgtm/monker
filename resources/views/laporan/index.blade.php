@extends('layouts.app')
@section('title', 'Laporan Pekerjaan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="mb-0 fw-bold">
        <i class="bi bi-file-earmark-bar-graph"></i>
        Laporan Pekerjaan
    </h3>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('laporan.excel', request()->query()) }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('laporan.cetak', request()->query()) }}"
           target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>
    </div>
</div>

{{-- FILTER --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">📅 Tahun</label>
                <select name="tahun" class="form-select">
                    @foreach($tahunList as $y)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">📆 Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="semua" @selected($bulan === 'semua' || !$bulan)>🗓 Semua Bulan (1 Tahun)</option>
                    @foreach($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" @selected($bulan == $num)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="col-md-3">
                <label class="form-label fw-semibold">📋 Bidang</label>
                <select name="bidang" class="form-select">
                    <option value="">Semua Bidang</option>
                    <option value="komputer" @selected(request('bidang')==='komputer')>🖥 Komputer</option>
                    <option value="cctv"     @selected(request('bidang')==='cctv')>📹 CCTV</option>
                </select>
            </div>
            @endif

            {{-- 🆕 Filter Tanggal Mulai --}}
            <div class="col-md-2">
                <label class="form-label fw-semibold">📅 Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="form-control" value="{{ $tanggalMulai ?? '' }}">
            </div>

            {{-- 🆕 Filter Tanggal Akhir --}}
            <div class="col-md-2">
                <label class="form-label fw-semibold">📅 Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir ?? '' }}">
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- INFO PERIODE --}}
<div class="alert alert-info d-flex align-items-center mb-3">
    <i class="bi bi-info-circle-fill me-2" style="font-size: 1.3rem;"></i>
    <div>
        <strong>Periode:</strong>
        @if($tanggalMulai || $tanggalAkhir)
            @if($tanggalMulai && $tanggalAkhir)
                {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
            @elseif($tanggalMulai)
                Sejak {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}
            @elseif($tanggalAkhir)
                Sampai {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}
            @endif
            <span class="badge bg-warning text-dark ms-1">Filter Tanggal Aktif</span>
        @else
            @if($bulan && $bulan !== 'semua')
                {{ $daftarBulan[$bulan] }} {{ $tahun }}
            @else
                Seluruh Tahun {{ $tahun }} (Januari - Desember)
            @endif
        @endif
        @if(request('bidang'))
            · Bidang: <strong>{{ ucfirst(request('bidang')) }}</strong>
        @endif
    </div>
</div>

{{-- STATISTIK --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card text-bg-primary shadow-sm p-3">
            <small>Total</small>
            <h3 class="mb-0">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-secondary shadow-sm p-3">
            <small>Ditugaskan</small>
            <h3 class="mb-0">{{ $stats['ditugaskan'] }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-warning shadow-sm p-3">
            <small>Dikerjakan</small>
            <h3 class="mb-0">{{ $stats['dikerjakan'] }}</h3>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card text-bg-success shadow-sm p-3">
            <small>Selesai</small>
            <h3 class="mb-0">{{ $stats['selesai'] }}</h3>
        </div>
    </div>
</div>

{{-- GRAFIK PER BULAN (12 Bulan) --}}
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-bar-chart-fill text-primary"></i>
            Grafik Pekerjaan per Bulan — Tahun {{ $tahun }}
        </h6>
        <div style="position: relative; height: 280px;">
            <canvas id="chartTahunan"></canvas>
        </div>
    </div>
</div>

{{-- GRAFIK PER HARI --}}
@if($bulan && $bulan !== 'semua')
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
@else
<div class="alert alert-secondary small">
    <i class="bi bi-info-circle"></i>
    Pilih <strong>bulan tertentu</strong> untuk melihat grafik per hari.
</div>
@endif

{{-- REKAP TEKNISI --}}
@if($rekapTeknisi->count() > 0)
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="bi bi-people-fill"></i> Rekap per Teknisi — Tahun {{ $tahun }}
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
            @foreach($rekapTeknisi as $i => $r)
                @php
                    $rasio = $r->total > 0 ? round(($r->selesai / $r->total) * 100) : 0;
                    $badgeColor = $rasio >= 80 ? 'success' : ($rasio >= 50 ? 'warning' : 'danger');
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <i class="bi bi-person-circle text-primary"></i>
                        {{ $r->teknisi->display_name ?? '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $r->total }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success">{{ $r->selesai }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark">{{ $r->pending }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $badgeColor }}">{{ $rasio }}%</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- DETAIL PEKERJAAN (dengan Foto) --}}
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-list-task"></i> Detail Pekerjaan ({{ $jobs->count() }})
        </h6>
    </div>
    <div class="card-body">
    @forelse($jobs as $j)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <div>
                    <h6 class="mb-1">
                        <code>{{ $j->kode_job }}</code> — {{ $j->judul }}
                        <span class="badge {{ $j->bidangBadgeClass() }} ms-1">
                            <i class="bi {{ $j->bidangIcon() }}"></i> {{ $j->bidangLabel() }}
                        </span>
                    </h6>
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
                        'SELESAI'    => 'bg-success',
                        default      => 'bg-light text-dark',
                    }; @endphp
                    <span class="badge {{ $badge }}">{{ $j->status }}</span>
                </div>
            </div>

            @if($j->hasil_pekerjaan)
                <p class="mb-2 small"><strong>Hasil:</strong> {{ $j->hasil_pekerjaan }}</p>
            @endif

            {{-- FOTO --}}
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>📷 Foto Sebelum:</strong></div>
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

                <div class="col-md-6">
                    <div class="small text-muted mb-1"><strong>📷 Foto Sesudah:</strong></div>
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
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: .3;"></i>
            <p class="mt-2 mb-0">Belum ada pekerjaan pada periode ini</p>
        </div>
    @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // GRAFIK TAHUNAN
    const ctxTahunan = document.getElementById('chartTahunan');
    if (ctxTahunan) {
        new Chart(ctxTahunan, {
            type: 'bar',
            data: {
                labels: @json($chartTahunan['labels']),
                datasets: [
                    {
                        label: 'Total',
                        data: @json($chartTahunan['data']),
                        backgroundColor: 'rgba(13, 110, 253, 0.8)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                        borderRadius: 5,
                    },
                    {
                        label: 'Selesai',
                        data: @json($chartTahunan['selesai']),
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
                labels: @json($chartHarian['labels']),
                datasets: [
                    {
                        label: 'Total',
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
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return 'Tanggal ' + context[0].label + ' {{ $bulan && $bulan !== "semua" ? $daftarBulan[$bulan] : "" }} {{ $tahun }}';
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
@endpush