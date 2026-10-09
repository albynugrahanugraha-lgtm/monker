@extends('layouts.app')
@section('title', 'Laporan Bulanan')

@section('content')

{{-- TABS NAVIGASI (Admin & SuperAdmin saja) --}}
@if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
@php
    $bidangAktif = request('bidang') ?? 'semua';
@endphp
<ul class="nav nav-tabs mb-3" id="laporanTabs">
    <li class="nav-item">
        <a class="nav-link {{ $bidangAktif === 'komputer' ? 'active fw-bold' : '' }}"
           href="{{ route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'komputer']) }}">
            <i class="bi bi-pc-display"></i> Laporan Komputer
            @if($bidangAktif === 'komputer')
                <span class="badge bg-primary ms-1">aktif</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $bidangAktif === 'cctv' ? 'active fw-bold' : '' }}"
           href="{{ route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'cctv']) }}">
            <i class="bi bi-camera-video"></i> Laporan CCTV
            @if($bidangAktif === 'cctv')
                <span class="badge bg-info text-dark ms-1">aktif</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $bidangAktif === 'semua' ? 'active fw-bold' : '' }}"
           href="{{ route('laporan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}">
            <i class="bi bi-files"></i> Semua Bidang
            @if($bidangAktif === 'semua')
                <span class="badge bg-secondary ms-1">aktif</span>
            @endif
        </a>
    </li>
</ul>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3 class="mb-0 fw-bold">
        @if(request('bidang') === 'komputer')
            <i class="bi bi-pc-display text-primary"></i> Laporan Komputer
        @elseif(request('bidang') === 'cctv')
            <i class="bi bi-camera-video text-info"></i> Laporan CCTV
        @else
            <i class="bi bi-file-earmark-bar-graph"></i> Laporan Bulanan Pekerjaan
        @endif
    </h3>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('laporan.excel', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => request('bidang')]) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
        <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => request('bidang')]) }}" target="_blank" class="btn btn-primary">
            <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
        </a>
    </div>
</div>

{{-- SHORTCUT CETAK TERPISAH --}}
@if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
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
                <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'komputer']) }}"
                   target="_blank"
                   class="btn btn-outline-primary w-100 py-2">
                    <i class="bi bi-pc-display"></i>
                    <strong>Cetak Laporan Komputer</strong>
                    <br>
                    <small class="text-muted">Hanya pekerjaan bidang komputer</small>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'bidang' => 'cctv']) }}"
                   target="_blank"
                   class="btn btn-outline-info w-100 py-2">
                    <i class="bi bi-camera-video"></i>
                    <strong>Cetak Laporan CCTV</strong>
                    <br>
                    <small class="text-muted">Hanya pekerjaan bidang CCTV</small>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('laporan.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
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
@endif

{{-- FILTER --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    @foreach($daftarBulan as $num => $nama)
                        <option value="{{ $num }}" @selected($bulan == $num)>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                <div class="col-md-3">
                    <label class="form-label">Bidang</label>
                    <select name="bidang" class="form-select">
                        <option value="">Semua Bidang</option>
                        <option value="komputer" @selected(request('bidang')==='komputer')>Komputer</option>
                        <option value="cctv"     @selected(request('bidang')==='cctv')>CCTV</option>
                    </select>
                </div>
            @endif

            <div class="col-md-3">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
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

{{-- GRAFIK 12 BULAN --}}
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

{{-- GRAFIK PER HARI --}}
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

{{-- REKAP PER TEKNISI --}}
@if($rekapTeknisi->count() > 0)
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

{{-- DETAIL PEKERJAAN --}}
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h6 class="mb-0">Detail Pekerjaan — {{ $daftarBulan[$bulan] }} {{ $tahun }}</h6>
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

            {{-- Tanggal Buat & Selesai --}}
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100" style="background:#fff8e1; border-left:4px solid #ffc107 !important;">
                        <div class="small">
                            <i class="bi bi-calendar-plus text-warning"></i>
                            <strong>Tanggal Buat Tugas:</strong>
                        </div>
                        <div class="fw-bold" style="font-size:.9rem;">
                            {{ $j->created_at?->format('d-m-Y H:i') ?? '-' }}
                        </div>
                        <small class="text-muted" style="font-size:.72rem;">
                            <i class="bi bi-person-badge"></i>
                            Dibuat oleh: {{ $j->admin->display_name ?? 'Teknisi sendiri' }}
                        </small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-2 h-100" style="background:#e8f5e9; border-left:4px solid #198754 !important;">
                        <div class="small">
                            <i class="bi bi-calendar-check text-success"></i>
                            <strong>Tanggal Selesai:</strong>
                        </div>
                        @if($j->tanggal_selesai)
                            <div class="fw-bold" style="font-size:.9rem;">
                                {{ $j->tanggal_selesai->format('d-m-Y H:i') }}
                            </div>
                            @php
                                $diff = $j->created_at->diff($j->tanggal_selesai);
                                $parts = [];
                                if ($diff->d > 0) $parts[] = $diff->d . ' hari';
                                if ($diff->h > 0) $parts[] = $diff->h . ' jam';
                                if ($diff->i > 0 && $diff->d == 0) $parts[] = $diff->i . ' menit';
                                $durasiText = implode(' ', $parts) ?: 'kurang dari 1 menit';
                            @endphp
                            <small class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-stopwatch"></i>
                                Durasi: {{ $durasiText }}
                            </small>
                        @else
                            <div class="fw-bold text-muted" style="font-size:.9rem;">
                                <em>Belum selesai</em>
                            </div>
                            <small class="text-muted" style="font-size:.72rem;">
                                <i class="bi bi-hourglass-split"></i>
                                Masih dalam proses
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            <div class="small text-muted mb-2">
                <i class="bi bi-calendar-event"></i>
                <strong>Tanggal Tugas:</strong> {{ $j->tanggal_tugas?->format('d-m-Y') ?? '-' }}
            </div>

            @if($j->hasil_pekerjaan)
                <p class="mb-2 small"><strong>Hasil:</strong> {{ $j->hasil_pekerjaan }}</p>
            @endif

            <div class="row g-3 mt-2">
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
        <div class="text-center text-muted py-5">
            @if(request('bidang') === 'komputer')
                Belum ada pekerjaan <strong>komputer</strong> di bulan ini
            @elseif(request('bidang') === 'cctv')
                Belum ada pekerjaan <strong>CCTV</strong> di bulan ini
            @else
                Belum ada pekerjaan di bulan ini
            @endif
        </div>
    @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
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
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } } }
            }
        });
    }

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
                    legend: { position: 'top', labels: { font: { size: 11 } } },
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
@endpush