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
    .stat-box .stat-label { font-size: .75rem; opacity: 0.9; }
    .stat-box .stat-value { font-size: 1.8rem; font-weight: 700; line-height: 1; margin-top: 5px; }
    .stat-box .stat-icon  { font-size: 2rem; opacity: 0.3; }

    .stat-total      { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-komputer   { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
    .stat-cctv       { background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); color: #000; }
    .stat-terlambat  { background: linear-gradient(135deg, #dc3545 0%, #b71c1c 100%); }

    .filter-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 10px;
    }

    .badge-group {
        font-size: .65rem;
        background: #6c757d;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
    }

    /* Pagination custom */
    .pagination .page-link {
        font-size: .8rem;
        padding: .35rem .7rem;
        border-radius: 6px;
        margin: 0 2px;
        border: 1px solid #dee2e6;
        color: #0d6efd;
    }
    .pagination .page-item.active .page-link {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        font-weight: 700;
    }
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
    }
    .pagination .page-item .page-link:hover {
        background: #e9ecef;
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
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('admin.buat') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Tugas
        </a>
    </div>
</div>

{{-- 4 KOTAK STATISTIK --}}
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

{{-- FILTER + SORT + PENCARIAN TANGGAL --}}
<div class="filter-card p-3 mb-3">
    <form method="GET" action="{{ route('admin.belum-dikerjakan') }}" class="row g-2 align-items-end">

        {{-- ============ ROW 1: Filter utama ============ --}}
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
                <i class="bi bi-lightning-charge"></i> Tanggal Cepat
            </label>
            <select name="tanggal_filter" class="form-select form-select-sm">
                <option value="">Semua Tanggal</option>
                <option value="terlambat" @selected($tanggalFilter === 'terlambat')>⚠️ Terlambat</option>
                <option value="hari_ini" @selected($tanggalFilter === 'hari_ini')>📅 Hari Ini</option>
                <option value="minggu_ini" @selected($tanggalFilter === 'minggu_ini')>📆 Minggu Ini</option>
                <option value="bulan_ini" @selected($tanggalFilter === 'bulan_ini')>🗓 Bulan Ini</option>
            </select>
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-sort-down"></i> Urutkan
            </label>
            <select name="sort" class="form-select form-select-sm">
                <option value="desc" @selected(($sort ?? 'desc') === 'desc')>📅 Tanggal Terbaru</option>
                <option value="asc"  @selected(($sort ?? '')     === 'asc')>📅 Tanggal Terlama</option>
            </select>
        </div>

        {{-- ============ ROW 2: Rentang tanggal custom ============ --}}
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-calendar-plus"></i> Dari Tanggal
            </label>
            <input type="date"
                   name="tanggal_dari"
                   value="{{ $tanggalDari ?? '' }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-3 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-calendar-check"></i> Sampai Tanggal
            </label>
            <input type="date"
                   name="tanggal_sampai"
                   value="{{ $tanggalSampai ?? '' }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-list-ol"></i> Per Halaman
            </label>
            <select name="per_page" class="form-select form-select-sm">
                @foreach([10, 25, 50, 100] as $pp)
                    <option value="{{ $pp }}" @selected(($perPage ?? 10) == $pp)>{{ $pp }} baris</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 col-12 d-flex gap-1">
            <button type="submit" class="btn btn-light btn-sm flex-fill fw-bold">
                <i class="bi bi-search"></i> Cari & Terapkan
            </button>
            <a href="{{ route('admin.belum-dikerjakan') }}"
               class="btn btn-outline-light btn-sm"
               title="Reset semua filter">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </form>

    {{-- Info filter tanggal aktif --}}
    @if($tanggalDari || $tanggalSampai)
        <div class="alert alert-light mt-2 mb-0 py-2 small d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <i class="bi bi-funnel-fill text-primary"></i>
                <strong>Rentang tanggal aktif:</strong>
                {{ $tanggalDari ? \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') : '...' }}
                <i class="bi bi-arrow-right"></i>
                {{ $tanggalSampai ? \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') : '...' }}
            </span>
            <a href="{{ route('admin.belum-dikerjakan', request()->except(['tanggal_dari', 'tanggal_sampai'])) }}"
               class="btn btn-sm btn-outline-danger py-0">
                <i class="bi bi-x-circle"></i> Hapus filter tanggal
            </a>
        </div>
    @endif

    <div class="mt-2 small d-flex justify-content-between flex-wrap gap-2">
        <span>
            <i class="bi bi-info-circle"></i>
            Menampilkan <strong>{{ $jobs->count() }}</strong>
            dari <strong>{{ $jobs->total() }}</strong> pekerjaan belum dikerjakan
        </span>
        @if($jobs->total() > 0)
            <span>
                <i class="bi bi-bookmark"></i>
                Halaman <strong>{{ $jobs->currentPage() }}</strong>
                dari <strong>{{ $jobs->lastPage() }}</strong>
            </span>
        @endif
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
                    <th style="width: 14%;">Kode</th>
                    <th style="width: 8%;">Bidang</th>
                    <th style="width: 22%;">Judul & Lokasi</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 16%;">Teknisi</th>
                    <th style="width: 15%;" class="text-center">Aksi</th>
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
                        @if($j->isGrouped())
                            <br>
                            <span class="badge-group">🔗 {{ $j->group_id }}</span>
                        @endif
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
                        @elseif($j->status === 'DIKERJAKAN')
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-tools"></i> Sedang dikerjakan
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

                            <a href="{{ route('admin.selesaikan.form', $j) }}"
                               class="btn btn-outline-success"
                               title="Selesaikan Pekerjaan">
                                <i class="bi bi-check-circle"></i>
                            </a>

                            <a href="{{ route('admin.show', $j) }}"
                               class="btn btn-outline-secondary"
                               title="Lihat Detail"
                               target="_blank">
                                <i class="bi bi-eye"></i>
                            </a>

                            <button type="button"
                                    class="btn btn-outline-danger"
                                    title="Hapus Tugas"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-job-id="{{ $j->id }}"
                                    data-job-kode="{{ $j->kode_job }}"
                                    data-job-judul="{{ $j->judul }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-search" style="font-size: 3rem; color: #0d6efd; opacity: .5;"></i>
                        <div class="mt-2">
                            <strong>Tidak ada pekerjaan yang cocok dengan filter.</strong>
                            <div class="small mt-1">Coba ubah atau reset filter tanggal.</div>
                        </div>
                        <a href="{{ route('admin.belum-dikerjakan') }}" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-arrow-clockwise"></i> Reset Filter
                        </a>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($jobs->hasPages())
        <div class="card-footer bg-white py-2">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Menampilkan <strong>{{ $jobs->firstItem() ?? 0 }}</strong>
                    – <strong>{{ $jobs->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ $jobs->total() }}</strong> data
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($jobs->onFirstPage())
                        <button class="btn btn-sm btn-outline-secondary" disabled>
                            <i class="bi bi-chevron-left"></i> Prev
                        </button>
                    @else
                        <a href="{{ $jobs->previousPageUrl() }}"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-chevron-left"></i> Prev
                        </a>
                    @endif

                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            @foreach($jobs->getUrlRange(
                                max(1, $jobs->currentPage() - 2),
                                min($jobs->lastPage(), $jobs->currentPage() + 2)
                            ) as $page => $url)
                                @if($page == $jobs->currentPage())
                                    <li class="page-item active">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>

                    @if($jobs->hasMorePages())
                        <a href="{{ $jobs->nextPageUrl() }}"
                           class="btn btn-sm btn-outline-primary">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <button class="btn btn-sm btn-outline-secondary" disabled>
                            Next <i class="bi bi-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

{{-- MODAL ASSIGN TEKNISI (MULTI) --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="assignForm" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title">
                    <i class="bi bi-people-fill"></i> Assign Teknisi
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
                        Pilih Teknisi <span class="text-muted">(bisa pilih lebih dari 1)</span>
                    </label>

                    <div class="border rounded p-2"
                         style="max-height: 250px; overflow-y: auto; background: #f8f9fa;">
                        <div id="assignTeknisiList"></div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAllAssign(true)">
                            <i class="bi bi-check-all"></i> Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="checkAllAssign(false)">
                            <i class="bi bi-x-circle"></i> Hapus
                        </button>
                        <span class="ms-auto small text-muted align-self-center">
                            Terpilih: <strong id="assignCount">0</strong>
                        </span>
                    </div>

                    <div class="alert alert-info mt-2 mb-0 small" id="assignInfoClone" style="display:none;">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Akan dibuat <span id="assignCloneCount">0</span> tiket terpisah.</strong>
                        Setiap teknisi menerima tiket sendiri.
                    </div>

                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle"></i>
                        Kosongkan semua kalau ingin tugas bebas diambil.
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

{{-- MODAL KONFIRMASI DELETE --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" id="deleteForm" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-2 small">Apakah Anda yakin ingin menghapus tugas ini?</p>
                <div class="bg-light p-2 rounded small">
                    <div><strong>Kode:</strong> <code id="deleteJobKode">-</code></div>
                    <div><strong>Judul:</strong> <span id="deleteJobJudul">-</span></div>
                </div>
                <div class="alert alert-warning mt-2 mb-0 small py-2">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Data tidak bisa dikembalikan.</strong>
                    Semua foto, history, dan laporan ikut terhapus.
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash"></i> Ya, Hapus
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
        // ===== Modal Assign =====
        const assignModal = document.getElementById('assignModal');

        assignModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const jobId      = button.getAttribute('data-job-id');
            const jobKode    = button.getAttribute('data-job-kode');
            const jobBidang  = button.getAttribute('data-job-bidang');
            const jobJudul   = button.getAttribute('data-job-judul');
            const teknisiId  = parseInt(button.getAttribute('data-teknisi-id')) || null;

            const form = document.getElementById('assignForm');
            form.action = `/admin/job/${jobId}/assign`;

            document.getElementById('assignJobKode').textContent  = jobKode;
            document.getElementById('assignJobJudul').textContent = jobJudul;

            const container = document.getElementById('assignTeknisiList');
            container.innerHTML = '';

            const expectedRole = jobBidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

            teknisiList.forEach(function(t) {
                if (t.role !== expectedRole) return;

                const isChecked = (teknisiId && t.id === teknisiId);

                const div = document.createElement('div');
                div.className = 'form-check';
                div.innerHTML = `
                    <input class="form-check-input assign-checkbox" type="checkbox"
                           name="teknisi_ids[]" value="${t.id}"
                           id="assign_${t.id}"
                           ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label" for="assign_${t.id}">
                        <i class="bi bi-person-circle"></i>
                        ${t.display_name || t.nama}
                    </label>
                `;
                container.appendChild(div);
            });

            updateAssignCount();
        });

        // ===== Modal Delete =====
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            const jobId    = button.getAttribute('data-job-id');
            const jobKode  = button.getAttribute('data-job-kode');
            const jobJudul = button.getAttribute('data-job-judul');

            const form = document.getElementById('deleteForm');
            form.action = "{{ route('admin.destroy', ':id') }}".replace(':id', jobId);

            document.getElementById('deleteJobKode').textContent  = jobKode;
            document.getElementById('deleteJobJudul').textContent = jobJudul;
        });
    });

    function updateAssignCount() {
        const checked = document.querySelectorAll('.assign-checkbox:checked').length;
        document.getElementById('assignCount').textContent = checked;

        const infoClone = document.getElementById('assignInfoClone');
        const cloneCount = document.getElementById('assignCloneCount');

        if (checked >= 2) {
            infoClone.style.display = 'block';
            cloneCount.textContent = checked;
        } else {
            infoClone.style.display = 'none';
        }
    }

    function checkAllAssign(state) {
        document.querySelectorAll('.assign-checkbox').forEach(cb => cb.checked = state);
        updateAssignCount();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('assign-checkbox')) {
            updateAssignCount();
        }
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