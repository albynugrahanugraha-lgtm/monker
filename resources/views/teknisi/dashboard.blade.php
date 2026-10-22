@extends('layouts.app')
@section('title', 'Dashboard Teknisi ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer'))

@php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
@endphp

@push('styles')
<style>
    .table-job { font-size: .85rem; }
    .table-job th { background: #f8f9fa; font-weight: 600; color: #495057; white-space: nowrap; padding: 10px 8px; }
    .table-job td { padding: 10px 8px; vertical-align: middle; }
    .table-job code { background: #f1f3f5; padding: 2px 6px; border-radius: 4px; color: #495057; font-size: .78rem; }

    tr.row-clickable { cursor: pointer; transition: background-color .2s; }
    tr.row-clickable:hover { background-color: #f0f7ff; box-shadow: inset 4px 0 0 0 #0d6efd; }

    .arrow-icon { color: #adb5bd; font-size: 1.2rem; transition: transform .2s, color .2s; }
    tr.row-clickable:hover .arrow-icon { color: #0d6efd; transform: translateX(5px); }

    @keyframes blinkRowBg { 0% { background-color: #ffeb3b; } 50% { background-color: #ffffff; } 100% { background-color: #ffeb3b; } }
    tr.row-belum-dikerjakan td { animation: blinkRowBg 1s infinite ease-in-out !important; font-weight: 600 !important; }
    tr.row-belum-dikerjakan { border-left: 5px solid #dc3545; }

    @keyframes blinkBadge { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
    .badge-blinking { animation: blinkBadge 0.7s infinite ease-in-out !important; box-shadow: 0 0 10px rgba(220, 53, 69, 0.8); }

    .exclaim { display: inline-block; animation: blinkExclaim 0.6s infinite !important; color: #dc3545; font-weight: bold; }
    @keyframes blinkExclaim { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* ============================================
       RIWAYAT SELESAI — TABEL
       ============================================ */
    .table-riwayat { font-size: .82rem; margin-bottom: 0; }
    .table-riwayat thead th { background: #f8f9fa; color: #495057; font-weight: 600; font-size: .72rem; text-transform: uppercase; letter-spacing: .3px; padding: 8px 10px; white-space: nowrap; border-bottom: 2px solid #dee2e6; }
    .table-riwayat tbody tr { cursor: pointer; transition: all 0.15s ease; border-left: 3px solid transparent; }
    .table-riwayat tbody tr:hover { background: #e8f5e9 !important; border-left-color: #198754; }
    .table-riwayat tbody tr:hover .arrow-riwayat { transform: translateX(4px); color: #0d6efd; }
    .table-riwayat tbody td { padding: 10px; vertical-align: middle; border-bottom: 1px solid #f0f0f0; }
    .table-riwayat .judul-riwayat { color: #212529; font-weight: 600; font-size: .85rem; margin-bottom: 2px; line-height: 1.3; }
    .table-riwayat .kode-riwayat { color: #6c757d; font-size: .7rem; font-family: 'Courier New', monospace; background: #f1f3f5; padding: 2px 6px; border-radius: 3px; display: inline-block; }
    .table-riwayat .meta-riwayat { color: #6c757d; font-size: .72rem; margin-top: 3px; }
    .table-riwayat .meta-riwayat i { color: #6c757d; margin-right: 2px; }
    .table-riwayat .badge-selesai-riwayat { background: #198754; color: white; font-size: .62rem; padding: 3px 8px; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 3px; }
    .table-riwayat .waktu-riwayat { color: #6c757d; font-size: .7rem; font-family: 'Courier New', monospace; white-space: nowrap; }
    .table-riwayat .arrow-riwayat { color: #adb5bd; font-size: 1.1rem; transition: transform 0.15s, color 0.15s; }

    .modal-content { border: none; border-radius: 12px; }
    .modal-header { border-radius: 12px 12px 0 0; padding: 12px 16px; }
    .modal-body { padding: 16px; }
    .modal-footer { padding: 12px 16px; border-top: 1px solid #e9ecef; }
    .modal-info-table { font-size: .85rem; }
    .modal-info-table th { width: 40%; color: #6c757d; font-weight: 500; padding: 4px 0; vertical-align: top; }
    .modal-info-table td { padding: 4px 0; vertical-align: top; }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold">
            <i class="bi {{ $isCctv ? 'bi-camera-video' : 'bi-pc-display' }}"></i>
            Dashboard Teknisi {{ $isCctv ? 'CCTV' : 'Komputer' }}
        </h5>
        <small class="text-muted" style="font-size: .75rem;">Halo, {{ auth()->user()->display_name }} 👋</small>
    </div>
    <a href="{{ route($rp . '.buat') }}" class="btn btn-primary btn-sm py-1 px-2" style="font-size: .8rem;">
        <i class="bi bi-plus-circle"></i> Buat Tugas
    </a>
</div>

{{-- 3 KOTAK STATISTIK (RINGKASAN BULAN INI) --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted d-block" style="font-size: .7rem;">📥 Total Tugas (Bulan Ini)</small>
                    <h5 class="mb-0 fw-bold">{{ $hariIni['total'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted d-block" style="font-size: .7rem;">✅ Sudah Dikerjakan</small>
                    <h5 class="mb-0 fw-bold text-success">{{ $hariIni['selesai'] }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                <div>
                    <small class="text-muted d-block" style="font-size: .7rem;">⏳ Belum Dikerjakan</small>
                    <h5 class="mb-0 fw-bold text-danger">{{ $hariIni['belum'] }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- PEKERJAAN YANG HARUS DIKERJAKAN --}}
{{-- ============================================ --}}
<div class="card shadow-sm mb-3">
    <div class="card-header bg-danger text-white py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-exclamation-circle-fill"></i>
            Pekerjaan yang Harus Dikerjakan
            <span class="badge bg-light text-dark ms-2">{{ $jobs->count() }}</span>
        </h6>
        <small class="d-none d-md-inline" style="opacity: .8;">
            <i class="bi bi-info-circle"></i> Klik baris untuk detail
        </small>
    </div>
    <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 table-job">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Judul & Lokasi</th>
                <th>Prioritas</th>
                <th>Waktu</th>
                <th>Status</th>
                <th class="text-center" style="width: 50px;"></th>
            </tr>
        </thead>
        <tbody>
        @forelse($jobs as $j)
            @php
                $badgeClass = match($j->status) {
                    'DITUGASKAN' => 'bg-secondary',
                    'DIKERJAKAN' => 'bg-warning text-dark',
                    default => 'bg-light text-dark',
                };
                $prioritasColor = match($j->prioritas) {
                    'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary',
                };
                $isBelum = $j->status === 'DITUGASKAN';
                $isProses = $j->status === 'DIKERJAKAN';
                $isBebas = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $rowClass = 'row-clickable';
                if ($isBelum) $rowClass .= ' row-belum-dikerjakan';
                if ($isProses) $rowClass .= ' row-dikerjakan';

                // LOGIKA KETERANGAN TANGGAL
                $tglTugas = $j->tanggal_tugas;
                $tglHariIni = \Carbon\Carbon::today();
                $keteranganTanggal = '';
                $badgeTanggalClass = '';

                if ($tglTugas) {
                    if ($tglTugas->isToday()) {
                        $keteranganTanggal = 'Hari Ini';
                        $badgeTanggalClass = 'bg-success';
                    } elseif ($tglTugas->isYesterday()) {
                        $keteranganTanggal = 'Kemarin';
                        $badgeTanggalClass = 'bg-warning text-dark';
                    } elseif ($tglTugas->isPast()) {
                        $selisih = (int) $tglTugas->diffInDays($tglHariIni);
                        $keteranganTanggal = $selisih . ' Hari Lalu';
                        $badgeTanggalClass = 'bg-danger';
                    } else {
                        $keteranganTanggal = $tglTugas->format('d M Y');
                        $badgeTanggalClass = 'bg-info text-dark';
                    }
                }
            @endphp

            <tr class="{{ $rowClass }} job-row" data-status="{{ $j->status }}" data-id="{{ $j->id }}">
                <td>
                    <code>{{ $j->kode_job }}</code>
                    @if($isBelum) <span class="exclaim">!</span> @endif
                    @if($isBebas)
                        <br>
                        <span class="badge bg-info text-dark mt-1" style="font-size: .65rem;">
                            <i class="bi bi-hand-index-thumb"></i> Bebas Diambil
                        </span>
                    @endif
                </td>
                <td>
                    <div class="fw-semibold">{{ $j->judul }}</div>
                    <small class="text-muted">
                        <i class="bi bi-geo-alt-fill"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                    </small>

                    {{-- KETERANGAN TANGGAL --}}
                    @if($keteranganTanggal)
                        <br>
                        <span class="badge {{ $badgeTanggalClass }} mt-1" style="font-size: .65rem;">
                            <i class="bi bi-calendar-event"></i> Tugas: {{ $keteranganTanggal }}
                        </span>
                    @endif

                    <br>
                    <small class="text-muted">
                        <i class="bi bi-pc-display"></i> {{ $j->computer_name ?? '-' }}
                        @if($j->merk) ({{ $j->merk }}) @endif
                        · <i class="bi bi-tag-fill"></i> {{ $j->kategori ?? 'Umum' }}
                    </small>
                </td>
                <td><span class="badge bg-{{ $prioritasColor }}">{{ $j->prioritas }}</span></td>
                <td>
                    <div style="font-size: .72rem; color: #6c757d;">
                        <i class="bi bi-clock text-primary"></i> <strong>{{ $j->created_at->format('d/m H:i') }}</strong>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $badgeClass }} {{ $isBelum ? 'badge-blinking' : '' }}">
                        {{ str_replace('_', ' ', $j->status) }}
                    </span>
                </td>
                <td class="text-center"><i class="bi bi-chevron-right arrow-icon"></i></td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-check-circle" style="font-size: 2.5rem; color: #198754; opacity: .5;"></i>
                    <div class="mt-2">Tidak ada pekerjaan yang harus dikerjakan. 👍</div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL AKSI --}}
{{-- ============================================ --}}
@foreach($jobs as $j)
    <div class="modal fade" id="modalAksi{{ $j->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header text-white
                    @if($j->status === 'DITUGASKAN') bg-primary
                    @elseif($j->status === 'DIKERJAKAN') bg-warning
                    @else bg-success @endif">
                    <h6 class="modal-title"><i class="bi bi-clipboard-check"></i> {{ $j->kode_job }}</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5 class="fw-bold mb-3">{{ $j->judul }}</h5>

                    <table class="table modal-info-table mb-3">
                        <tr><th><i class="bi bi-geo-alt-fill"></i> Lokasi</th><td>{{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}</td></tr>
                        <tr><th><i class="bi bi-pc-display"></i> Perangkat</th><td>{{ $j->computer_name ?? '-' }} @if($j->merk) ({{ $j->merk }}) @endif</td></tr>
                        <tr><th><i class="bi bi-tag-fill"></i> Kategori</th><td>{{ $j->kategori ?? 'Umum' }}</td></tr>
                        <tr>
                            <th><i class="bi bi-flag-fill"></i> Prioritas</th>
                            <td>
                                @php $pc = match($j->prioritas) {
                                    'Tinggi' => 'danger', 'Sedang' => 'warning', default => 'secondary',
                                }; @endphp
                                <span class="badge bg-{{ $pc }}">{{ $j->prioritas }}</span>
                            </td>
                        </tr>
                        <tr><th><i class="bi bi-calendar-event"></i> Tanggal</th><td>{{ $j->tanggal_tugas?->format('d M Y') ?? '-' }}</td></tr>
                        <tr><th><i class="bi bi-clock"></i> Dibuat</th><td>{{ $j->created_at->format('d M Y, H:i') }}</td></tr>
                        @if($j->deskripsi)
                            <tr><th><i class="bi bi-card-text"></i> Deskripsi</th><td>{{ $j->deskripsi }}</td></tr>
                        @endif
                    </table>

                    <div class="text-center mb-2">
                        <span class="badge
                            @if($j->status === 'DITUGASKAN') bg-secondary
                            @elseif($j->status === 'DIKERJAKAN') bg-warning text-dark
                            @else bg-success @endif
                            py-2 px-3" style="font-size: .85rem;">
                            <i class="bi bi-info-circle"></i> {{ str_replace('_', ' ', $j->status) }}
                        </span>
                    </div>
                </div>

                <div class="modal-footer flex-column flex-sm-row gap-2">
                    <a href="{{ route($rp . '.show', $j) }}" class="btn btn-outline-primary w-100 w-sm-auto">
                        <i class="bi bi-eye"></i> Detail
                    </a>

                    @if($j->status === 'DITUGASKAN')
                        <a href="{{ route($rp . '.kerjakan', $j) }}" class="btn btn-primary w-100 w-sm-auto">
                            <i class="bi bi-play-circle"></i> Kerjakan Sekarang
                        </a>
                    @elseif($j->status === 'DIKERJAKAN')
                        <a href="{{ route($rp . '.kerjakan', $j) }}" class="btn btn-success w-100 w-sm-auto">
                            <i class="bi bi-check-circle"></i> Selesaikan Pekerjaan
                        </a>
                    @endif

                    <a href="{{ route($rp . '.cetak', $j) }}" target="_blank" class="btn btn-outline-secondary w-100 w-sm-auto">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                    <button type="button" class="btn btn-light w-100 w-sm-auto" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- ============================================ --}}
{{-- RIWAYAT SELESAI -- FILTER PERIODE DI SINI --}}
{{-- ============================================ --}}
<div class="card shadow-sm">
    <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="mb-0">
            <i class="bi bi-check-circle-fill"></i> Riwayat Selesai
            <span class="badge bg-light text-success ms-1">{{ $jobsSelesai->count() }}</span>
        </h6>

        {{-- FILTER PERIODE KHUSUS RIWAYAT --}}
        <form method="GET" action="{{ route($rp . '.dashboard') }}" class="d-flex gap-1 align-items-center mb-0" style="font-size: .75rem;">
            <span class="me-1 text-white-50 d-none d-sm-inline">Filter:</span>
            <input type="radio" class="btn-check" name="periode" id="p_harian" value="harian" @checked($periode === 'harian') onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_harian" style="font-size: .7rem;">Harian</label>

            <input type="radio" class="btn-check" name="periode" id="p_mingguan" value="mingguan" @checked($periode === 'mingguan') onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_mingguan" style="font-size: .7rem;">Mingguan</label>

            <input type="radio" class="btn-check" name="periode" id="p_bulanan" value="bulanan" @checked($periode === 'bulanan') onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_bulanan" style="font-size: .7rem;">Bulanan</label>

            <input type="radio" class="btn-check" name="periode" id="p_semua" value="semua" @checked($periode === 'semua') onchange="this.form.submit()">
            <label class="btn btn-outline-light btn-sm py-0 px-2" for="p_semua" style="font-size: .7rem;">Semua</label>
        </form>
    </div>

    @if($jobsSelesai->count() > 0)
    <div class="table-responsive">
        <table class="table table-riwayat align-middle">
            <thead>
                <tr>
                    <th style="width: 40%;">Pekerjaan</th>
                    <th style="width: 22%;">Lokasi</th>
                    <th style="width: 15%;" class="text-center">Status</th>
                    <th style="width: 18%;">Waktu Selesai</th>
                    <th style="width: 5%;" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobsSelesai as $j)
                    <tr onclick="window.location='{{ route($rp . '.show', $j) }}'">
                        <td>
                            <div class="judul-riwayat">{{ $j->judul }}</div>
                            <span class="kode-riwayat">
                                <i class="bi bi-hash"></i>{{ $j->kode_job }}
                            </span>
                        </td>
                        <td>
                            <div class="meta-riwayat">
                                <i class="bi bi-geo-alt-fill"></i> {{ $j->lok_gedung ?? '-' }}
                            </div>
                            <div class="meta-riwayat">
                                <i class="bi bi-door-open"></i> {{ $j->lokasi_ruang ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge-selesai-riwayat">
                                <i class="bi bi-check-circle-fill"></i> Selesai
                            </span>
                        </td>
                        <td>
                            <div class="waktu-riwayat">
                                <i class="bi bi-calendar-event"></i>
                                {{ $j->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                            </div>
                            <div class="waktu-riwayat">
                                <i class="bi bi-clock"></i>
                                {{ $j->tanggal_selesai?->format('H:i') ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <i class="bi bi-chevron-right arrow-riwayat"></i>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="card-body text-center text-muted py-4">
        <i class="bi bi-inbox" style="font-size: 2.5rem; opacity: .3;"></i>
        <p class="mt-2 mb-0">Belum ada riwayat pekerjaan untuk periode ini.</p>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // Klik baris pekerjaan → buka modal
    document.querySelectorAll('tr.row-clickable').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) return;
            const id = row.dataset.id;
            if (id) {
                const modalEl = document.getElementById('modalAksi' + id);
                if (modalEl) new bootstrap.Modal(modalEl).show();
            }
        });
    });

    // JS Blinking untuk memastikan efek kelap-kelip pasti jalan
    setInterval(function() {
        document.querySelectorAll('tr.row-belum-dikerjakan').forEach(function(row, idx) {
            const offset = idx * 200;
            const phase = (Date.now() + offset) % 1000;
            const isYellow = phase < 500;
            row.querySelectorAll('td').forEach(function(td) {
                td.style.transition = 'background-color 0.3s ease';
                td.style.backgroundColor = isYellow ? '#ffeb3b' : '#ffffff';
            });
        });
    }, 500);
</script>
@endpush