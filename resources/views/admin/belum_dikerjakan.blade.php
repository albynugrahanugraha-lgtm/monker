@extends('layouts.app_admin')
@section('title', 'Pekerjaan Belum Dikerjakan')

@section('content')
<style>
    /* ============================================
       PWA STYLES & ANTI-GAGAL
       ============================================ */
    body { background-color: #f2f4f8 !important; }
    
    .app-header-pwa {
        background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
        border-radius: 24px; padding: 24px; color: white !important;
        box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3); margin-bottom: 24px;
    }
    .app-header-pwa h1 { font-size: 1.4rem; font-weight: 800; margin: 0; color: white !important; }
    .app-header-pwa p { font-size: 0.85rem; opacity: 0.9; margin: 4px 0 0 0; color: white !important; }
    
    .btn-header-pwa {
        background: rgba(255, 255, 255, 0.2) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: white !important; border-radius: 12px; padding: 8px 16px;
        font-size: 0.85rem; font-weight: 700; text-decoration: none !important;
    }

    .stat-card-pwa {
        background: white !important; border-radius: 16px; padding: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 16px; height: 100%;
    }
    .stat-icon-pwa { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .stat-card-pwa .value { font-size: 1.5rem; font-weight: 800; line-height: 1; color: #1a1a1a !important; margin-bottom: 4px; }
    .stat-card-pwa .label { font-size: 0.7rem; font-weight: 700; color: #888 !important; text-transform: uppercase; }

    .filter-card-pwa { background: white !important; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px; }
    .filter-group-pwa { margin-bottom: 12px; }
    .filter-group-pwa label { font-size: 0.75rem; font-weight: 700; color: #666 !important; margin-bottom: 6px; display: block; }
    .filter-control-pwa { width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid #e0e0e0 !important; background: #f9f9f9 !important; font-size: 0.85rem; color: #1a1a1a !important; font-weight: 600; }
    .btn-filter-pwa { width: 100%; padding: 12px; border-radius: 10px; background: #0d6efd !important; color: white !important; border: none; font-weight: 700; font-size: 0.9rem; }

    .task-card-pwa {
        background: white !important; border-radius: 16px; padding: 16px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        border-left: 6px solid #0d6efd !important; position: relative; text-decoration: none !important; display: block;
    }
    .task-card-pwa.urgent { border-left-color: #dc3545 !important; }
    .task-card-pwa.today { border-left-color: #ffc107 !important; }

    @keyframes softBlinkRed { 0%, 100% { background-color: #fff5f5 !important; } 50% { background-color: white !important; } }
    .task-card-pwa.blink-red { animation: softBlinkRed 1.5s infinite ease-in-out !important; }

    .task-code-pwa { font-family: monospace; font-size: 0.75rem; color: #888 !important; font-weight: 700; }
    .task-title-pwa { font-size: 1rem; font-weight: 800; color: #1a1a1a !important; margin: 8px 0; line-height: 1.3; }
    .task-meta-pwa { font-size: 0.8rem; color: #555 !important; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
    .task-footer-pwa { border-top: 1px dashed #eee; padding-top: 12px; margin-top: 12px; display: flex; justify-content: space-between; align-items: center; }

    .btn-action-group-pwa { display: flex; gap: 6px; }
    .btn-action-pwa { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd !important; background: white !important; color: #555 !important; font-size: 1rem; text-decoration: none !important; }
    .btn-action-pwa.assign { color: #0d6efd !important; border-color: #0d6efd !important; }
    .btn-action-pwa.finish { color: #198754 !important; border-color: #198754 !important; }
    .btn-action-pwa.delete { color: #dc3545 !important; border-color: #dc3545 !important; }

    /* ============================================
       LAPTOP SETTINGS (LIST KE BAWAH 1 KOLOM)
       ============================================ */
    @media (min-width: 768px) {
        .filter-form-pwa { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: end; }
        .btn-filter-pwa { grid-column: span 4; width: 200px; justify-self: start; }
        
        /* Grid Tugas jadi 1 Kolom Penuh */
        .grid-task-pwa { 
            display: flex !important; 
            flex-direction: column !important; 
            gap: 12px; 
        }
        
        /* Tata Letak Card Menyamping */
        .task-card-pwa {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            margin-bottom: 0;
        }
        .task-card-pwa > div:first-child {
            flex: 1;
        }
        .task-card-pwa .task-title-pwa {
            margin: 4px 0 8px 0;
        }
        /* Tata letak Footer menyamping */
        .task-card-pwa .task-footer-pwa {
            border-top: none;
            padding-top: 0;
            margin-top: 0;
            margin-left: 24px;
            padding-left: 24px;
            border-left: 1px dashed #eee;
            display: flex;
            align-items: center;
            gap: 16px;
            width: auto;
        }
    }

    /* ============================================
       BOTTOM NAVIGATION
       ============================================ */
    @media (max-width: 767.98px) {
        .pwa-bottom-nav {
            display: flex !important; position: fixed !important; bottom: 0 !important; left: 0 !important; right: 0 !important;
            background: white !important; border-top: 1px solid #f0f0f0 !important; justify-content: space-around !important;
            padding: 10px 0 max(10px, env(safe-area-inset-bottom)) !important; z-index: 9999 !important; box-shadow: 0 -4px 15px rgba(0,0,0,0.1) !important;
        }
        .pwa-bottom-nav .nav-item {
            flex: 1 !important; text-align: center !important; padding: 6px 0 !important; color: #adb5bd !important;
            text-decoration: none !important; font-size: 0.65rem !important; font-weight: 700 !important;
            display: flex !important; flex-direction: column !important; align-items: center !important; gap: 4px !important;
            position: relative !important; z-index: 10000 !important; pointer-events: auto !important; cursor: pointer !important;
        }
        .pwa-bottom-nav .nav-item i { font-size: 1.4rem !important; }
        .pwa-bottom-nav .nav-item.active { color: #0d6efd !important; }
        body { padding-bottom: 85px !important; }
    }
</style>

<div class="container-fluid px-0">
    <div class="app-header-pwa">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-hourglass-split"></i> Belum Dikerjakan</h1>
                <p>Daftar pekerjaan yang menunggu tindakan</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.dashboard') }}" class="btn-header-pwa"><i class="bi bi-arrow-left"></i> Kembali</a>
                <a href="{{ route('admin.buat') }}" class="btn-header-pwa"><i class="bi bi-plus-circle"></i> Buat Tugas</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card-pwa">
                <div class="stat-icon-pwa bg-primary bg-opacity-10 text-primary"><i class="bi bi-clipboard-x"></i></div>
                <div><div class="value">{{ $stats['total'] }}</div><div class="label">Total</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pwa">
                <div class="stat-icon-pwa bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-pc-display"></i></div>
                <div><div class="value">{{ $stats['komputer'] }}</div><div class="label">Komputer</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pwa">
                <div class="stat-icon-pwa bg-info bg-opacity-10 text-info"><i class="bi bi-camera-video"></i></div>
                <div><div class="value">{{ $stats['cctv'] }}</div><div class="label">CCTV</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card-pwa">
                <div class="stat-icon-pwa bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                <div><div class="value">{{ $stats['terlambat'] }}</div><div class="label">Terlambat</div></div>
            </div>
        </div>
    </div>

    <div class="filter-card-pwa">
        <form method="GET" action="{{ route('admin.belum-dikerjakan') }}" class="filter-form-pwa">
            <div class="filter-group-pwa">
                <label>Bidang</label>
                <select name="bidang" class="filter-control-pwa">
                    <option value="">Semua Bidang</option>
                    <option value="komputer" @selected($bidang === 'komputer')>Komputer</option>
                    <option value="cctv" @selected($bidang === 'cctv')>CCTV</option>
                </select>
            </div>
            <div class="filter-group-pwa">
                <label>Status</label>
                <select name="status" class="filter-control-pwa">
                    <option value="">Semua Status</option>
                    <option value="DITUGASKAN" @selected($status === 'DITUGASKAN')>Ditugaskan</option>
                    <option value="DIKERJAKAN" @selected($status === 'DIKERJAKAN')>Dikerjakan</option>
                </select>
            </div>
            <div class="filter-group-pwa">
                <label>Rentang Tanggal</label>
                <div class="d-flex gap-1">
                    <input type="date" name="tanggal_dari" value="{{ $tanggalDari ?? '' }}" class="filter-control-pwa" style="padding:8px;">
                    <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai ?? '' }}" class="filter-control-pwa" style="padding:8px;">
                </div>
            </div>
            <div class="filter-group-pwa d-flex gap-2">
                <button type="submit" class="btn-filter-pwa flex-fill"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('admin.belum-dikerjakan') }}" class="btn btn-light border fw-bold" style="border-radius:10px; display:flex; align-items:center; color: #333 !important;"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:end; margin-bottom: 12px; padding: 0 4px;">
        <h2 style="font-size: 1.1rem; font-weight: 800; margin:0; color: #1a1a1a !important;">Daftar Pekerjaan</h2>
        <span style="background: #0d6efd; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">{{ $jobs->total() }} Data</span>
    </div>

    <div class="grid-task-pwa">
        @forelse($jobs as $i => $j)
            @php
                $isTerlambat = $j->tanggal_tugas && $j->tanggal_tugas->isPast() && !$j->tanggal_tugas->isToday();
                $isHariIni = $j->tanggal_tugas && $j->tanggal_tugas->isToday();
                $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';

                $cardClass = 'normal';
                if ($isTerlambat || $belumDiambil) $cardClass = 'urgent blink-red';
                elseif ($isHariIni) $cardClass = 'today';

                $badgeStatus = match($j->status) {
                    'DITUGASKAN' => 'bg-secondary',
                    'DIKERJAKAN' => 'bg-warning text-dark',
                    default      => 'bg-light text-dark',
                };
            @endphp
            
            <div class="task-card-pwa {{ $cardClass }}">
                {{-- Bagian Kiri (Info) --}}
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="task-code-pwa"><i class="bi bi-hash"></i>{{ $j->kode_job }}</span>
                            @if($j->isGrouped())
                                <span class="badge bg-secondary ms-1" style="font-size:0.6rem;">🔗 {{ $j->group_id }}</span>
                            @endif
                        </div>
                        {{-- Badge Status Hanya Muncul di HP --}}
                        <span class="badge {{ $badgeStatus }} d-md-none">{{ str_replace('_', ' ', $j->status) }}</span>
                    </div>

                    <div class="task-title-pwa">{{ $j->judul }}</div>

                    <div class="task-meta-pwa">
                        <i class="bi bi-geo-alt-fill text-danger"></i> {{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}
                    </div>
                    <div class="task-meta-pwa">
                        <i class="bi bi-calendar-event text-primary"></i> {{ $j->tanggal_tugas?->format('d/m/Y') }}
                        @if($isTerlambat)
                            <span class="text-danger fw-bold ms-1"><i class="bi bi-exclamation-triangle-fill"></i> Terlambat</span>
                        @elseif($isHariIni)
                            <span class="text-warning fw-bold ms-1"><i class="bi bi-clock-fill"></i> Hari Ini</span>
                        @endif
                    </div>
                </div>

                {{-- Bagian Kanan (Status & Tombol) --}}
                <div class="task-footer-pwa">
                    <div>
                        @if($j->teknisi)
                            <span class="badge bg-primary"><i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}</span>
                        @elseif($j->status === 'DIKERJAKAN')
                            <span class="badge bg-warning text-dark"><i class="bi bi-tools"></i> Dikerjakan</span>
                        @else
                            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Belum diambil</span>
                        @endif
                        {{-- Badge Status di Laptop --}}
                        <span class="badge {{ $badgeStatus }} d-none d-md-inline ms-2">{{ str_replace('_', ' ', $j->status) }}</span>
                    </div>

                    <div class="btn-action-group-pwa">
                        <button type="button" class="btn-action-pwa assign" title="Assign"
                                data-bs-toggle="modal" data-bs-target="#assignModal"
                                data-job-id="{{ $j->id }}" data-job-kode="{{ $j->kode_job }}"
                                data-job-bidang="{{ $j->bidang }}" data-job-judul="{{ $j->judul }}"
                                data-teknisi-id="{{ $j->teknisi_id }}">
                            <i class="bi bi-person-plus"></i>
                        </button>
                        <a href="{{ route('admin.selesaikan.form', $j) }}" class="btn-action-pwa finish" title="Selesaikan">
                            <i class="bi bi-check-circle"></i>
                        </a>
                        <a href="{{ route('admin.show', $j) }}" class="btn-action-pwa" target="_blank" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        <button type="button" class="btn-action-pwa delete" title="Hapus"
                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                data-job-id="{{ $j->id }}" data-job-kode="{{ $j->kode_job }}" data-job-judul="{{ $j->judul }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 20px;">
                <i class="bi bi-check-circle-fill" style="font-size: 3rem; color: #198754; opacity: 0.8;"></i>
                <h3 style="font-size: 1.1rem; font-weight: 800; margin: 12px 0 4px 0; color: #198754;">Semua Tugas Selesai!</h3>
                <p style="font-size: 0.85rem; color: #888;">Tidak ada pekerjaan yang cocok dengan filter saat ini.</p>
                <a href="{{ route('admin.belum-dikerjakan') }}" class="btn btn-outline-primary mt-2" style="border-radius: 10px;">Reset Filter</a>
            </div>
        @endforelse
    </div>

    @if($jobs->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-2">
            <ul class="pagination pagination-sm mb-0" style="gap: 4px;">
                @if ($jobs->onFirstPage())
                    <li class="page-item disabled"><span class="page-link" style="border-radius: 8px; border: none; background: #e9ecef; color: #adb5bd; padding: 8px 12px;">&laquo;</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $jobs->previousPageUrl() }}" style="border-radius: 8px; border: none; background: white; color: #0d6efd; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 8px 12px;">&laquo;</a></li>
                @endif

                @foreach ($jobs->getUrlRange(max(1, $jobs->currentPage() - 2), min($jobs->lastPage(), $jobs->currentPage() + 2)) as $page => $url)
                    @if ($page == $jobs->currentPage())
                        <li class="page-item active"><span class="page-link" style="border-radius: 8px; border: none; background: #0d6efd; color: white; font-weight: 700; padding: 8px 12px;">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $url }}" style="border-radius: 8px; border: none; background: white; color: #333; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 8px 12px;">{{ $page }}</a></li>
                    @endif
                @endforeach

                @if ($jobs->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $jobs->nextPageUrl() }}" style="border-radius: 8px; border: none; background: white; color: #0d6efd; box-shadow: 0 2px 6px rgba(0,0,0,0.05); padding: 8px 12px;">&raquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link" style="border-radius: 8px; border: none; background: #e9ecef; color: #adb5bd; padding: 8px 12px;">&raquo;</span></li>
                @endif
            </ul>
        </div>
    @endif
</div>

{{-- BOTTOM NAVIGATION UNTUK HP --}}
<div class="pwa-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item" style="text-decoration: none !important;">
        <i class="bi bi-house-door-fill"></i><span>Beranda</span>
    </a>
    <a href="{{ route('admin.buat') }}" class="nav-item" style="text-decoration: none !important;">
        <i class="bi bi-plus-square"></i><span>Buat</span>
    </a>
    <a href="{{ route('admin.belum-dikerjakan') }}" class="nav-item active" style="text-decoration: none !important;">
        <i class="bi bi-hourglass-split"></i><span>Belum</span>
    </a>
    <a href="{{ route('laporan.index') }}" class="nav-item" style="text-decoration: none !important;">
        <i class="bi bi-bar-chart-fill"></i><span>Laporan</span>
    </a>
</div>

{{-- MODAL ASSIGN --}}
<div class="modal fade" id="assignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="assignForm" class="modal-content" style="border-radius: 20px; border: none;">
            @csrf
            <div class="modal-header bg-primary text-white py-3" style="border-radius: 20px 20px 0 0; border-bottom: none;">
                <h6 class="modal-title fw-bold"><i class="bi bi-people-fill"></i> Assign Teknisi</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Pekerjaan</label>
                    <div class="form-control bg-light" readonly style="border-radius: 10px;">
                        <code id="assignJobKode">-</code> <br>
                        <span id="assignJobJudul">-</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Pilih Teknisi (bisa > 1)</label>
                    <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto; background: #f8f9fa;">
                        <div id="assignTeknisiList"></div>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="checkAllAssign(true)">Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="checkAllAssign(false)">Hapus</button>
                        <span class="ms-auto small text-muted align-self-center fw-bold">Terpilih: <span id="assignCount">0</span></span>
                    </div>
                    <div class="alert alert-info mt-2 mb-0 small py-2" id="assignInfoClone" style="display:none; border-radius:10px;">
                        <i class="bi bi-info-circle-fill"></i> Akan dibuat <span id="assignCloneCount">0</span> tiket terpisah.
                    </div>
                </div>
            </div>
            <div class="modal-footer py-3 border-top-0">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-check-circle"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DELETE --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form method="POST" id="deleteForm" class="modal-content" style="border-radius: 20px; border: none;">
            @csrf
            @method('DELETE')
            <div class="modal-header bg-danger text-white py-3" style="border-radius: 20px 20px 0 0; border-bottom: none;">
                <h6 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Hapus Tugas</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3 text-center">
                <i class="bi bi-trash text-danger mb-2" style="font-size: 3rem;"></i>
                <p class="mb-2 fw-bold">Yakin ingin menghapus tugas ini?</p>
                <div class="bg-light p-2 rounded small text-start">
                    <div><strong>Kode:</strong> <code id="deleteJobKode">-</code></div>
                    <div><strong>Judul:</strong> <span id="deleteJobJudul">-</span></div>
                </div>
            </div>
            <div class="modal-footer py-3 border-top-0 justify-content-center">
                <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const teknisiList = @json($teknisiList);

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Matikan Timer Reload (jika ada bentrok)
        let highestIntervalId = setInterval(function() {}, 0);
        for (let i = 0; i <= highestIntervalId; i++) {
            clearInterval(i);
        }
        
        // 2. Bekukan Reload Browser
        const originalReload = location.reload;
        location.reload = function() {
            console.log('Reload dibatalkan oleh proteksi halaman.');
            return;
        };

        // 3. Paksa Dropdown Navbar Bisa Diklik
        document.querySelectorAll('.navbar .dropdown-toggle').forEach(function(el) {
            el.addEventListener('click', function(e) {
                e.stopPropagation(); 
                var dropdown = bootstrap.Dropdown.getInstance(el) || new bootstrap.Dropdown(el);
                dropdown.show();
            });
        });

        // --- Logika Modal Assign & Delete ---
        const assignModal = document.getElementById('assignModal');
        if (assignModal) {
            assignModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const jobId = button.getAttribute('data-job-id');
                const jobKode = button.getAttribute('data-job-kode');
                const jobBidang = button.getAttribute('data-job-bidang');
                const jobJudul = button.getAttribute('data-job-judul');
                const teknisiId = parseInt(button.getAttribute('data-teknisi-id')) || null;

                document.getElementById('assignForm').action = `/admin/job/${jobId}/assign`;
                document.getElementById('assignJobKode').textContent = jobKode;
                document.getElementById('assignJobJudul').textContent = jobJudul;

                const container = document.getElementById('assignTeknisiList');
                container.innerHTML = '';
                const expectedRole = jobBidang === 'cctv' ? 'teknisi_cctv' : 'teknisi';

                teknisiList.forEach(function(t) {
                    if (t.role !== expectedRole) return;
                    const isChecked = (teknisiId && t.id === teknisiId);
                    const div = document.createElement('div');
                    div.className = 'form-check mb-2';
                    div.innerHTML = `
                        <input class="form-check-input assign-checkbox" type="checkbox" name="teknisi_ids[]" value="${t.id}" id="assign_${t.id}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label fw-bold" for="assign_${t.id}"><i class="bi bi-person-circle text-primary"></i> ${t.display_name || t.nama}</label>
                    `;
                    container.appendChild(div);
                });
                updateAssignCount();
            });
        }

        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const jobId = button.getAttribute('data-job-id');
                const jobKode = button.getAttribute('data-job-kode');
                const jobJudul = button.getAttribute('data-job-judul');

                document.getElementById('deleteForm').action = "{{ route('admin.destroy', ':id') }}".replace(':id', jobId);
                document.getElementById('deleteJobKode').textContent = jobKode;
                document.getElementById('deleteJobJudul').textContent = jobJudul;
            });
        }
    });

    function updateAssignCount() {
        const checked = document.querySelectorAll('.assign-checkbox:checked').length;
        document.getElementById('assignCount').textContent = checked;
        const infoClone = document.getElementById('assignInfoClone');
        if (checked >= 2) {
            infoClone.style.display = 'block';
            document.getElementById('assignCloneCount').textContent = checked;
        } else {
            infoClone.style.display = 'none';
        }
    }

    function checkAllAssign(state) {
        document.querySelectorAll('.assign-checkbox').forEach(cb => cb.checked = state);
        updateAssignCount();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('assign-checkbox')) updateAssignCount();
    });
</script>
@endpush