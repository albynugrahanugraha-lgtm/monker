@extends('layouts.app')
@section('title', 'Dashboard Teknisi')

@push('styles')
<style>
    .table-job { font-size: .85rem; }
    .table-job th {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        white-space: nowrap;
        padding: 10px 8px;
    }
    .table-job td { padding: 10px 8px; vertical-align: middle; }
    .table-job code {
        background: #f1f3f5;
        padding: 2px 6px;
        border-radius: 4px;
        color: #495057;
        font-size: .78rem;
    }

    tr.row-clickable {
        cursor: pointer;
        transition: background-color .2s;
    }
    tr.row-clickable:hover {
        background-color: #f0f7ff;
        box-shadow: inset 4px 0 0 0 #0d6efd;
    }

    .arrow-icon {
        color: #adb5bd;
        font-size: 1.2rem;
        transition: transform .2s, color .2s;
    }
    tr.row-clickable:hover .arrow-icon {
        color: #0d6efd;
        transform: translateX(5px);
    }

    @keyframes blinkRowBg {
        0%   { background-color: #ffeb3b; }
        50%  { background-color: #ffffff; }
        100% { background-color: #ffeb3b; }
    }
    tr.row-belum-dikerjakan td {
        animation: blinkRowBg 1s infinite ease-in-out !important;
        font-weight: 600 !important;
    }
    tr.row-belum-dikerjakan { border-left: 5px solid #dc3545; }

    @keyframes blinkBadge {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }
    .badge-blinking {
        animation: blinkBadge 0.7s infinite ease-in-out !important;
        box-shadow: 0 0 10px rgba(220, 53, 69, 0.8);
    }

    .exclaim {
        display: inline-block;
        animation: blinkExclaim 0.6s infinite !important;
        color: #dc3545;
        font-weight: bold;
    }
    @keyframes blinkExclaim {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .job-selesai-item {
        border-left: 4px solid #198754;
        background: #f8fff9;
        padding: 10px 12px;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .modal-content { border: none; border-radius: 12px; }
    .modal-header { border-radius: 12px 12px 0 0; padding: 12px 16px; }
    .modal-body { padding: 16px; }
    .modal-footer { padding: 12px 16px; border-top: 1px solid #e9ecef; }
    .modal-info-table { font-size: .85rem; }
    .modal-info-table th {
        width: 40%;
        color: #6c757d;
        font-weight: 500;
        padding: 4px 0;
        vertical-align: top;
    }
    .modal-info-table td { padding: 4px 0; vertical-align: top; }

    @media (max-width: 768px) {
        .table-job thead { display: none; }
        .table-job,
        .table-job tbody,
        .table-job tr,
        .table-job td { display: block; width: 100%; }

        .table-job tr {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            margin-bottom: 12px;
            padding: 12px 14px;
            border-left: 5px solid #ccc;
        }
        .table-job tr.row-belum-dikerjakan { border-left: 5px solid #dc3545; }
        .table-job tr.row-dikerjakan { border-left: 5px solid #ffc107; }

        .table-job td {
            border: none !important;
            padding: 4px 0 !important;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .table-job td:first-child {
            margin-bottom: 6px;
            padding-bottom: 6px !important;
            border-bottom: 1px dashed #e9ecef !important;
        }
        .table-job td:nth-child(2) { display: block !important; margin-bottom: 8px; }
        .table-job td:last-child {
            margin-top: 8px;
            padding-top: 8px !important;
            border-top: 1px dashed #e9ecef !important;
            justify-content: flex-end;
        }
        .table-job td:nth-child(4) { display: none; }
    }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 fw-bold">Dashboard Teknisi</h5>
        <small class="text-muted" style="font-size: .75rem;">Halo, {{ auth()->user()->display_name }} 👋</small>
    </div>
    <a href="{{ route('teknisi.buat') }}" class="btn btn-primary btn-sm py-1 px-2" style="font-size: .8rem;">
        <i class="bi bi-plus-circle"></i> Buat Tugas
    </a>
</div>

{{-- 4 KOTAK --}}
<div class="row g-2 g-md-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">📥 Total Tugas Hari Ini</small>
                <h3 class="mb-0 fw-bold mt-1">{{ $hariIni['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">✅ Sudah Dikerjakan</small>
                <h3 class="mb-0 fw-bold mt-1">{{ $hariIni['selesai'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">⏳ Belum Dikerjakan</small>
                <h3 class="mb-0 fw-bold mt-1">{{ $hariIni['belum'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white;">
            <div class="card-body p-2 p-md-3">
                <small style="opacity: 0.9; font-size: .7rem;">🏆 Statistik Saya</small>
                <h3 class="mb-0 fw-bold mt-1">{{ $myStats['selesai_bulan'] }}</h3>
                <small style="opacity: 0.9; font-size: .65rem;">dari {{ $myStats['total_bulan'] }} tugas</small>
            </div>
        </div>
    </div>
</div>

{{-- LIST PEKERJAAN --}}
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
                    'Tinggi' => 'danger',
                    'Sedang' => 'warning',
                    default  => 'secondary',
                };
                $isBelum = $j->status === 'DITUGASKAN';
                $isProses = $j->status === 'DIKERJAKAN';
                $isBebas = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $rowClass = 'row-clickable';
                if ($isBelum) $rowClass .= ' row-belum-dikerjakan';
                if ($isProses) $rowClass .= ' row-dikerjakan';
            @endphp

            <tr class="{{ $rowClass }} job-row" data-status="{{ $j->status }}" data-id="{{ $j->id }}">
                <td>
                    <code>{{ $j->kode_job }}</code>
                    @if($isBelum)
                        <span class="exclaim">!</span>
                    @endif
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
                <td class="text-center">
                    <i class="bi bi-chevron-right arrow-icon"></i>
                </td>
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

{{-- MODAL UNTUK SETIAP PEKERJAAN --}}
@foreach($jobs as $j)
    <div class="modal fade" id="modalAksi{{ $j->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header text-white
                    @if($j->status === 'DITUGASKAN') bg-primary
                    @elseif($j->status === 'DIKERJAKAN') bg-warning
                    @else bg-success @endif">
                    <h6 class="modal-title">
                        <i class="bi bi-clipboard-check"></i>
                        {{ $j->kode_job }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <h5 class="fw-bold mb-3">{{ $j->judul }}</h5>

                    <table class="table modal-info-table mb-3">
                        <tr>
                            <th><i class="bi bi-geo-alt-fill"></i> Lokasi</th>
                            <td>{{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-pc-display"></i> Perangkat</th>
                            <td>{{ $j->computer_name ?? '-' }} @if($j->merk) ({{ $j->merk }}) @endif</td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-tag-fill"></i> Kategori</th>
                            <td>{{ $j->kategori ?? 'Umum' }}</td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-flag-fill"></i> Prioritas</th>
                            <td>
                                @php $pc = match($j->prioritas) {
                                    'Tinggi' => 'danger',
                                    'Sedang' => 'warning',
                                    default  => 'secondary',
                                }; @endphp
                                <span class="badge bg-{{ $pc }}">{{ $j->prioritas }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-calendar-event"></i> Tanggal</th>
                            <td>{{ $j->tanggal_tugas?->format('d M Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-clock"></i> Dibuat</th>
                            <td>{{ $j->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @if($j->deskripsi)
                            <tr>
                                <th><i class="bi bi-card-text"></i> Deskripsi</th>
                                <td>{{ $j->deskripsi }}</td>
                            </tr>
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
                    @if($j->status === 'DITUGASKAN')
                        <a href="{{ route('teknisi.kerjakan', $j) }}"
                           class="btn btn-primary w-100 w-sm-auto">
                            <i class="bi bi-play-circle"></i> Kerjakan Sekarang
                        </a>
                    @elseif($j->status === 'DIKERJAKAN')
                        <a href="{{ route('teknisi.kerjakan', $j) }}"
                           class="btn btn-success w-100 w-sm-auto">
                            <i class="bi bi-check-circle"></i> Selesaikan Pekerjaan
                        </a>
                    @endif

                    <a href="{{ route('teknisi.cetak', $j) }}" target="_blank"
                       class="btn btn-outline-secondary w-100 w-sm-auto">
                        <i class="bi bi-printer"></i> Cetak
                    </a>
                    <button type="button" class="btn btn-light w-100 w-sm-auto"
                            data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
@endforeach

{{-- RIWAYAT SELESAI --}}
@if($jobsSelesai->count() > 0)
<div class="card shadow-sm">
    <div class="card-header bg-success text-white py-2">
        <h6 class="mb-0">
            <i class="bi bi-check-circle-fill"></i> Riwayat Selesai (10 Terakhir)
        </h6>
    </div>
    <div class="card-body p-2">
        @foreach($jobsSelesai as $j)
            <div class="job-selesai-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold" style="font-size: .9rem;">{{ $j->judul }}</div>
                        <small class="text-muted">
                            <i class="bi bi-hash"></i>{{ $j->kode_job }} ·
                            <i class="bi bi-geo-alt"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success">Selesai</span>
                        <br>
                        <small class="text-muted">{{ $j->tanggal_selesai?->format('d/m H:i') }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    // ============================================================
    // KLIK BARIS → BUKA MODAL (TANPA BUNYI)
    // ============================================================
    document.querySelectorAll('tr.row-clickable').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            // ===== LANGSUNG BUKA MODAL TANPA BUNYI =====
            const id = row.dataset.id;
            if (id) {
                const modalEl = document.getElementById('modalAksi' + id);
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            }
        });
    });

    // ============================================================
    // FALLBACK KELAP-KELIP
    // ============================================================
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

    // ============================================================
    // DEBUG STATUS AUDIO (TANPA BUNYI)
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            console.log('📊 Audio status (layout):');
            console.log('   - audioReady:', typeof audioReady !== 'undefined' ? audioReady : 'undefined');
            console.log('   - audioUnlocked:', typeof audioUnlocked !== 'undefined' ? audioUnlocked : 'undefined');
            console.log('   - audioCtx state:', typeof audioCtx !== 'undefined' && audioCtx ? audioCtx.state : 'no ctx');
        }, 1000);
    });
</script>
@endpush