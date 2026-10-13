@extends('layouts.app')
@section('title', 'Pekerjaan Belum Dikerjakan')

@push('styles')
<style>
    @keyframes lampuBlinkMerah {
        0%, 100% { background-color: #ffcdd2; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuBlink {
        0%, 100% { background-color: #ffeb3b; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuPulse {
        0%, 100% { transform: scale(1); opacity: 1; background: #dc3545; box-shadow: 0 0 10px #dc3545; }
        50% { transform: scale(1.4); opacity: 0.6; background: #ffc107; box-shadow: 0 0 20px #ffc107; }
    }

    tr.row-urgent, tr.row-urgent td {
        animation: lampuBlinkMerah 0.9s infinite ease-in-out !important;
        font-weight: 700 !important;
    }
    tr.row-urgent { border-left: 5px solid #b71c1c !important; }

    tr.row-warning, tr.row-warning td {
        animation: lampuBlink 1s infinite ease-in-out !important;
        font-weight: 600 !important;
    }
    tr.row-warning { border-left: 5px solid #dc3545 !important; }

    tr.row-normal { border-left: 5px solid #198754 !important; }

    .lampu-indicator {
        display: inline-block;
        width: 12px; height: 12px;
        border-radius: 50%;
        background: #dc3545;
        box-shadow: 0 0 10px #dc3545;
        animation: lampuPulse 0.8s infinite;
        margin-right: 6px;
        vertical-align: middle;
    }

    .stat-box {
        padding: 15px;
        border-radius: 10px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .stat-box .stat-label {
        font-size: .75rem;
        opacity: 0.9;
    }
    .stat-box .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1;
        margin-top: 5px;
    }
    .stat-box .stat-icon {
        font-size: 2rem;
        opacity: 0.3;
    }

    .stat-total      { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-komputer   { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
    .stat-cctv       { background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); color: #000; }
    .stat-terlambat  { background: linear-gradient(135deg, #dc3545 0%, #b71c1c 100%); }
    .stat-belum      { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #000; }

    .filter-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-bold">
        <i class="bi bi-hourglass-split"></i> Pekerjaan Belum Dikerjakan
    </h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
        <a href="{{ route('admin.buat') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Tugas
        </a>
    </div>
</div>

{{-- 5 KOTAK STATISTIK --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-box stat-total">
            <div>
                <div class="stat-label">📋 Total Belum Selesai</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
            <i class="bi bi-clipboard-x stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box stat-komputer">
            <div>
                <div class="stat-label">🖥 Komputer</div>
                <div class="stat-value">{{ $stats['komputer'] }}</div>
            </div>
            <i class="bi bi-pc-display stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box stat-cctv">
            <div>
                <div class="stat-label">📹 CCTV</div>
                <div class="stat-value">{{ $stats['cctv'] }}</div>
            </div>
            <i class="bi bi-camera-video stat-icon"></i>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box stat-terlambat">
            <div>
                <div class="stat-label">⚠️ Terlambat</div>
                <div class="stat-value">{{ $stats['terlambat'] }}</div>
            </div>
            <i class="bi bi-exclamation-triangle stat-icon"></i>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="filter-card p-3 mb-3">
    <form method="GET" action="{{ route('admin.belum-dikerjakan') }}" class="row g-2 align-items-end">
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-layers"></i> Bidang
            </label>
            <select name="bidang" class="form-select form-select-sm">
                <option value="">Semua Bidang</option>
                <option value="komputer" @selected($bidang === 'komputer')>🖥 Komputer</option>
                <option value="cctv" @selected($bidang === 'cctv')>📹 CCTV</option>
            </select>
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-info-circle"></i> Status
            </label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua Status</option>
                <option value="DITUGASKAN" @selected($status === 'DITUGASKAN')>Ditugaskan</option>
                <option value="DIKERJAKAN" @selected($status === 'DIKERJAKAN')>Dikerjakan</option>
            </select>
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-calendar-event"></i> Tanggal
            </label>
            <select name="tanggal_filter" class="form-select form-select-sm">
                <option value="">Semua Tanggal</option>
                <option value="terlambat" @selected($tanggalFilter === 'terlambat')>⚠️ Terlambat</option>
                <option value="hari_ini" @selected($tanggalFilter === 'hari_ini')>📅 Hari Ini</option>
                <option value="minggu_ini" @selected($tanggalFilter === 'minggu_ini')>📆 Minggu Ini</option>
                <option value="bulan_ini" @selected($tanggalFilter === 'bulan_ini')>🗓 Bulan Ini</option>
            </select>
        </div>
        <div class="col-md-3 col-6 d-flex gap-1">
            <button type="submit" class="btn btn-light btn-sm flex-fill fw-bold">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('admin.belum-dikerjakan') }}" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </form>

    <div class="mt-2 small">
        <i class="bi bi-info-circle"></i>
        Menampilkan <strong>{{ $jobs->total() }}</strong> pekerjaan belum dikerjakan
        @if($bidang) · Bidang: <strong>{{ ucfirst($bidang) }}</strong> @endif
        @if($status) · Status: <strong>{{ $status }}</strong> @endif
        @if($tanggalFilter) · Tanggal: <strong>{{ str_replace('_', ' ', ucfirst($tanggalFilter)) }}</strong> @endif
    </div>
</div>

{{-- TABEL --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-list-task"></i> Daftar Pekerjaan
        </h6>
        <span class="badge bg-primary">{{ $jobs->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: .85rem;">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 12%;">Kode</th>
                    <th style="width: 8%;">Bidang</th>
                    <th style="width: 25%;">Judul & Lokasi</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 18%;">Teknisi</th>
                    <th style="width: 10%;" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $i => $j)
                @php
                    $isTerlambat = $j->tanggal_tugas && $j->tanggal_tugas->isPast() && !$j->tanggal_tugas->isToday();
                    $isHariIni = $j->tanggal_tugas && $j->tanggal_tugas->isToday();
                    $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                    if ($isTerlambat || $belumDiambil) {
                        $rowClass = 'row-urgent';
                    } elseif ($isHariIni) {
                        $rowClass = 'row-warning';
                    } else {
                        $rowClass = 'row-normal';
                    }

                    $badgeStatus = match($j->status) {
                        'DITUGASKAN' => 'bg-secondary',
                        'DIKERJAKAN' => 'bg-warning text-dark',
                        default      => 'bg-light text-dark',
                    };
                @endphp
                <tr class="{{ $rowClass }}">
                    <td class="text-center">{{ $jobs->firstItem() + $i }}</td>
                    <td>
                        <code style="font-size: .75rem;">{{ $j->kode_job }}</code>
                    </td>
                    <td>
                        @if($j->bidang === 'cctv')
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-camera-video"></i> CCTV
                            </span>
                        @else
                            <span class="badge bg-primary">
                                <i class="bi bi-pc-display"></i> Komputer
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $j->judul }}</div>
                        <small class="text-muted" style="font-size: .7rem;">
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                        </small>
                        @if($j->kategori)
                            <br>
                            <small class="text-muted" style="font-size: .68rem;">
                                <i class="bi bi-tag-fill"></i> {{ $j->kategori }}
                            </small>
                        @endif
                    </td>
                    <td>
                        @if($j->tanggal_tugas)
                            <div style="font-size: .8rem;">
                                <i class="bi bi-calendar-event"></i>
                                <strong>{{ $j->tanggal_tugas->format('d/m/Y') }}</strong>
                            </div>
                            @if($isTerlambat)
                                <small class="text-danger fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                    {{ $j->tanggal_tugas->diffForHumans() }}
                                </small>
                            @elseif($isHariIni)
                                <small class="text-primary fw-bold">
                                    <i class="bi bi-clock-fill"></i> Hari Ini
                                </small>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $badgeStatus }}">
                            {{ str_replace('_', ' ', $j->status) }}
                        </span>
                    </td>
                    <td>
                        @if($j->teknisi)
                            <span class="badge bg-primary">
                                <i class="bi bi-person-circle"></i>
                                {{ $j->teknisi->display_name }}
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                Belum diambil
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button"
                                    class="btn btn-outline-primary"
                                    title="Assign Teknisi"
                                    data-bs-toggle="modal"
                                    data-bs-target="#assignModal"
                                    data-job-id="{{ $j->id }}"
                                    data-job-kode="{{ $j->kode_job }}"
                                    data-job-bidang="{{ $j->bidang }}"
                                    data-job-judul="{{ $j->judul }}"
                                    data-teknisi-id="{{ $j->teknisi_id }}">
                                <i class="bi bi-person-plus"></i>
                            </button>

                            <a href="{{ route('admin.show', $j) }}"
                               class="btn btn-outline-secondary"
                               title="Lihat Detail"
                               target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-check-circle" style="font-size: 3rem; color: #198754; opacity: .5;"></i>
                        <div class="mt-2">
                            <strong>Semua pekerjaan sudah dikerjakan!</strong>
                            <br>
                            <small>Tidak ada pekerjaan yang pending saat ini.</small>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($jobs->hasPages())
        <div class="card-footer bg-white py-2">
            {{ $jobs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- MODAL ASSIGN TEKNISI --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="assignForm" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title">
                    <i class="bi bi-person-plus"></i> Assign Teknisi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pekerjaan</label>
                    <div class="form-control form-control-sm bg-light" readonly>
                        <code id="assignJobKode">-</code>
                        <br>
                        <span id="assignJobJudul">-</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">
                        Pilih Teknisi <span class="text-danger">*</span>
                    </label>
                    <select name="teknisi_id" id="assignTeknisiSelect" class="form-select form-select-sm" required>
                        <option value="">— Biarkan Kosong (Bebas Diambil) —</option>
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Hanya teknisi sesuai bidang yang akan muncul.
                    </small>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-check-circle"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const teknisiList = @json($teknisiList);

    document.addEventListener('DOMContentLoaded', function() {
        const assignModal = document.getElementById('assignModal');

        assignModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const jobId     = button.getAttribute('data-job-id');
            const jobKode   = button.getAttribute('data-job-kode');
            const jobBidang = button.getAttribute('data-job-bidang');
            const jobJudul  = button.getAttribute('data-job-judul');
            const teknisiId = button.getAttribute('data-teknisi-id');

            const form = document.getElementById('assignForm');
            form.action = `/admin/job/${jobId}/assign`;

            document.getElementById('assignJobKode').textContent  = jobKode;
            document.getElementById('assignJobJudul').textContent = jobJudul;

            const select = document.getElementById('assignTeknisiSelect');
            select.innerHTML = '<option value="">— Biarkan Kosong (Bebas Diambil) —</option>';

            const expectedRole = jobBidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

            teknisiList.forEach(function(t) {
                if (t.role === expectedRole) {
                    const opt = document.createElement('option');
                    opt.value = t.id;

                    let label = t.display_name || t.nama;
                    if (t.role === 'teknisi_cctv') label += ' (CCTV)';

                    opt.textContent = label;
                    if (teknisiId && parseInt(teknisiId) === t.id) {
                        opt.selected = true;
                    }
                    select.appendChild(opt);
                }
            });
        });
    });

    (function() {
        let state = false;
        setInterval(function() {
            state = !state;

            document.querySelectorAll('tr.row-urgent').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#ffcdd2' : '#ffffff';
                });
            });

            document.querySelectorAll('tr.row-warning').forEach(function(row) {
                row.querySelectorAll('td').forEach(function(td) {
                    td.style.transition = 'background-color 0.3s ease';
                    td.style.backgroundColor = state ? '#ffeb3b' : '#ffffff';
                });
            });
        }, 500);
    })();
</script>
@endpush