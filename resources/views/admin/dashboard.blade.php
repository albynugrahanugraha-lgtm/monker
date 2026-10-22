@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@push('styles')
<style>
    @keyframes lampuBlink {
        0%, 100% { background-color: #ffeb3b; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuBlinkMerah {
        0%, 100% { background-color: #ffcdd2; }
        50%      { background-color: #ffffff; }
    }
    @keyframes lampuPulse {
        0%, 100% { transform: scale(1); opacity: 1; background: #dc3545; box-shadow: 0 0 10px #dc3545; }
        50% { transform: scale(1.4); opacity: 0.6; background: #ffc107; box-shadow: 0 0 20px #ffc107; }
    }

    tr.row-lampu, tr.row-lampu td {
        animation: lampuBlink 1s infinite ease-in-out !important;
        font-weight: 600 !important;
    }
    tr.row-lampu { border-left: 5px solid #dc3545 !important; }

    tr.row-lampu-merah, tr.row-lampu-merah td {
        animation: lampuBlinkMerah 0.9s infinite ease-in-out !important;
        font-weight: 700 !important;
    }
    tr.row-lampu-merah { border-left: 5px solid #b71c1c !important; }

    tr.tugas-sudah-kerja { border-left: 5px solid #198754 !important; }

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

    .badge-belum-diambil { animation: lampuBlinkMerah 0.9s infinite ease-in-out !important; }

    .chart-wrapper { position: relative; width: 100%; min-height: 280px; }
    .chart-empty { text-align: center; padding: 50px 20px; color: #adb5bd; }
    .chart-empty i { font-size: 3rem; opacity: 0.4; display: block; margin-bottom: 10px; }

    .section-header {
        padding: 10px 15px;
        border-radius: 8px;
        color: white;
        font-weight: 700;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: .9rem;
    }
    .section-header.komputer { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); }
    .section-header.cctv { background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%); color: #000; }

    .card-header.header-komputer {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        color: white !important;
    }
    .card-header.header-cctv {
        background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%) !important;
        color: #000 !important;
    }

    .table-scroll { max-height: 300px; overflow-y: auto; }
    .table-scroll::-webkit-scrollbar { width: 6px; }
    .table-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .table-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }

    .date-filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
    }
    .date-filter-card input[type="date"] {
        background: rgba(255,255,255,.95);
        border: 2px solid rgba(255,255,255,.5);
        color: #212529;
        font-weight: 600;
    }
    .date-filter-card input[type="date"]:focus {
        background: white;
        border-color: white;
        box-shadow: 0 0 0 3px rgba(255,255,255,.3);
    }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-bold">
        <i class="bi bi-speedometer2"></i> Monitoring Pekerjaan Teknisi
    </h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.buat') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Buat Tugas
        </a>
        <a href="{{ route('admin.import.index') }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </a>
        <a href="{{ route('admin.belum-dikerjakan') }}" class="btn btn-warning btn-sm">
            <i class="bi bi-hourglass-split"></i> Belum Dikerjakan
        </a>
    </div>
</div>

{{-- FILTER TANGGAL & PERIODE RATING --}}
<div class="date-filter-card p-3 mb-4">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-end">
        <div class="col-md-3 col-12">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-calendar-event"></i> Pilih Tanggal Monitoring
            </label>
            <input type="date"
                   name="tanggal"
                   class="form-control"
                   value="{{ $tanggalObj->format('Y-m-d') }}"
                   max="{{ now()->addYear()->format('Y-m-d') }}">
        </div>

        <div class="col-md-3 col-12">
            <label class="form-label mb-1 small fw-bold">
                <i class="bi bi-trophy-fill"></i> Periode Rating
            </label>
            <select name="periode" class="form-select fw-bold text-dark">
                <option value="harian" @selected($periode === 'harian')>📅 Harian</option>
                <option value="mingguan" @selected($periode === 'mingguan')>📆 Mingguan</option>
                <option value="bulanan" @selected($periode === 'bulanan')>🗓 Bulanan</option>
                <option value="semua" @selected($periode === 'semua')>🏆 Semua Waktu</option>
            </select>
        </div>

        <div class="col-md-6 col-12 d-flex gap-2 flex-wrap align-items-end">
            <button type="submit" class="btn btn-light fw-bold">
                <i class="bi bi-search"></i> Tampilkan
            </button>
            <a href="{{ route('admin.dashboard', ['tanggal' => now()->format('Y-m-d'), 'periode' => 'harian']) }}"
               class="btn btn-outline-light btn-sm">
                📅 Hari Ini
            </a>
            <a href="{{ route('admin.dashboard', ['tanggal' => now()->subDay()->format('Y-m-d'), 'periode' => 'harian']) }}"
               class="btn btn-outline-light btn-sm">
                ⏮️ Kemarin
            </a>
            <a href="{{ route('admin.dashboard', ['tanggal' => now()->addDay()->format('Y-m-d'), 'periode' => 'harian']) }}"
               class="btn btn-outline-light btn-sm">
                ⏭️ Besok
            </a>
        </div>
    </form>

    <div class="mt-2 small">
        <i class="bi bi-info-circle"></i>
        Menampilkan data untuk tanggal:
        <strong>{{ $tanggalObj->translatedFormat('l, d F Y') }}</strong>
        @if($tanggalObj->isToday())
            <span class="badge bg-success ms-1">Hari Ini</span>
        @elseif($tanggalObj->isYesterday())
            <span class="badge bg-secondary ms-1">Kemarin</span>
        @elseif($tanggalObj->isTomorrow())
            <span class="badge bg-info text-dark ms-1">Besok</span>
        @elseif($tanggalObj->isFuture())
            <span class="badge bg-warning text-dark ms-1">Masa Depan</span>
        @else
            <span class="badge bg-dark ms-1">Masa Lalu</span>
        @endif
    </div>
</div>

{{-- 3 KOTAK STATISTIK --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small style="opacity: 0.9; font-size: .75rem;">📥 Total Tugas</small>
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

{{-- 2 KOLOM BESAR: KOMPUTER vs CCTV --}}
<div class="row g-3 mb-4">

    {{-- KOLOM KIRI: KOMPUTER --}}
    <div class="col-md-6">
        <div class="section-header komputer">
            <span><i class="bi bi-pc-display"></i> PEKERJAAN KOMPUTER</span>
            <span class="badge bg-light text-dark">
                {{ $kerjaanHariIniKomputer->count() + $kerjaanKemarinKomputer->count() }} total
            </span>
        </div>

        {{-- HARI INI --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-day"></i>
                    {{ $tanggalObj->translatedFormat('d F Y') }}
                </h6>
                <span class="badge bg-primary">{{ $kerjaanHariIniKomputer->count() }}</span>
            </div>
            <div class="table-responsive table-scroll">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 22%;">Kode</th>
                            <th style="width: 43%;">Pekerjaan</th>
                            <th style="width: 18%;">Teknisi</th>
                            <th style="width: 17%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanHariIniKomputer as $j)
                        @php
                            $badgeClass = $j->statusBadgeClass();
                            $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                            $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                            if ($belumDiambil) {
                                $rowClass = 'row-lampu-merah';
                            } elseif ($belumDikerjakan) {
                                $rowClass = 'row-lampu';
                            } else {
                                $rowClass = 'tugas-sudah-kerja';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
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
                                @php $penyelesai = $j->penyelesai(); @endphp
                                @if($j->status === 'SELESAI')
                                    @if($penyelesai)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> {{ $penyelesai->display_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Selesai
                                        </span>
                                    @endif
                                @elseif($j->teknisi)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}
                                    </span>
                                @elseif($j->status === 'DIKERJAKAN')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-tools"></i> Sedang dikerjakan
                                    </span>
                                @else
                                    <span class="badge bg-danger badge-belum-diambil">
                                        <i class="bi bi-exclamation-triangle"></i> Belum diambil
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge {{ $badgeClass }}">{{ $j->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">
                            Tidak ada pekerjaan komputer pada {{ $tanggalObj->translatedFormat('d F Y') }}
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KEMARIN --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-check"></i>
                    {{ $tanggalObj->copy()->subDay()->translatedFormat('d F Y') }}
                </h6>
                <span class="badge bg-secondary">{{ $kerjaanKemarinKomputer->count() }}</span>
            </div>
            <div class="table-responsive table-scroll">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 22%;">Kode</th>
                            <th style="width: 43%;">Pekerjaan</th>
                            <th style="width: 18%;">Teknisi</th>
                            <th style="width: 17%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanKemarinKomputer as $j)
                        @php
                            $badgeClass = $j->statusBadgeClass();
                            $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                            $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                            
                            // LOGIKA BARU: Terapkan class kelap-kelip jika belum selesai
                            if ($belumDiambil) {
                                $rowClass = 'row-lampu-merah';
                            } elseif ($belumDikerjakan) {
                                $rowClass = 'row-lampu';
                            } else {
                                $rowClass = 'tugas-sudah-kerja';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
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
                                @php $penyelesai = $j->penyelesai(); @endphp
                                @if($j->status === 'SELESAI' && $penyelesai)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> {{ $penyelesai->display_name }}
                                    </span>
                                @elseif($j->teknisi)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}
                                    </span>
                                @elseif($j->status === 'DIKERJAKAN')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-tools"></i> Sedang dikerjakan
                                    </span>
                                @else
                                    <span class="badge bg-danger badge-belum-diambil">
                                        <i class="bi bi-exclamation-triangle"></i> Belum diambil
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada pekerjaan komputer</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: CCTV --}}
    <div class="col-md-6">
        <div class="section-header cctv">
            <span><i class="bi bi-camera-video"></i> PEKERJAAN CCTV</span>
            <span class="badge bg-dark text-white">
                {{ $kerjaanHariIniCctv->count() + $kerjaanKemarinCctv->count() }} total
            </span>
        </div>

        {{-- HARI INI --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-day"></i>
                    {{ $tanggalObj->translatedFormat('d F Y') }}
                </h6>
                <span class="badge bg-info text-dark">{{ $kerjaanHariIniCctv->count() }}</span>
            </div>
            <div class="table-responsive table-scroll">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 22%;">Kode</th>
                            <th style="width: 43%;">Pekerjaan</th>
                            <th style="width: 18%;">Teknisi</th>
                            <th style="width: 17%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanHariIniCctv as $j)
                        @php
                            $badgeClass = $j->statusBadgeClass();
                            $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                            $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                            if ($belumDiambil) {
                                $rowClass = 'row-lampu-merah';
                            } elseif ($belumDikerjakan) {
                                $rowClass = 'row-lampu';
                            } else {
                                $rowClass = 'tugas-sudah-kerja';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
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
                                @php $penyelesai = $j->penyelesai(); @endphp
                                @if($j->status === 'SELESAI')
                                    @if($penyelesai)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> {{ $penyelesai->display_name }}
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Selesai
                                        </span>
                                    @endif
                                @elseif($j->teknisi)
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}
                                    </span>
                                @elseif($j->status === 'DIKERJAKAN')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-tools"></i> Sedang dikerjakan
                                    </span>
                                @else
                                    <span class="badge bg-danger badge-belum-diambil">
                                        <i class="bi bi-exclamation-triangle"></i> Belum diambil
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge {{ $badgeClass }}">{{ $j->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">
                            Tidak ada pekerjaan CCTV pada {{ $tanggalObj->translatedFormat('d F Y') }}
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- KEMARIN --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-calendar-check"></i>
                    {{ $tanggalObj->copy()->subDay()->translatedFormat('d F Y') }}
                </h6>
                <span class="badge bg-secondary">{{ $kerjaanKemarinCctv->count() }}</span>
            </div>
            <div class="table-responsive table-scroll">
                <table class="table table-hover align-middle mb-0" style="font-size: .8rem;">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 5;">
                        <tr>
                            <th style="width: 22%;">Kode</th>
                            <th style="width: 43%;">Pekerjaan</th>
                            <th style="width: 18%;">Teknisi</th>
                            <th style="width: 17%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($kerjaanKemarinCctv as $j)
                        @php
                            $badgeClass = $j->statusBadgeClass();
                            $belumDikerjakan = in_array($j->status, ['DITUGASKAN', 'DIKERJAKAN']);
                            $belumDiambil = is_null($j->teknisi_id) && $j->status === 'DITUGASKAN';
                            
                            // LOGIKA BARU: Terapkan class kelap-kelip jika belum selesai
                            if ($belumDiambil) {
                                $rowClass = 'row-lampu-merah';
                            } elseif ($belumDikerjakan) {
                                $rowClass = 'row-lampu';
                            } else {
                                $rowClass = 'tugas-sudah-kerja';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
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
                                @php $penyelesai = $j->penyelesai(); @endphp
                                @if($j->status === 'SELESAI' && $penyelesai)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> {{ $penyelesai->display_name }}
                                    </span>
                                @elseif($j->teknisi)
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-person-circle"></i> {{ $j->teknisi->display_name }}
                                    </span>
                                @elseif($j->status === 'DIKERJAKAN')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-tools"></i> Sedang dikerjakan
                                    </span>
                                @else
                                    <span class="badge bg-danger badge-belum-diambil">
                                        <i class="bi bi-exclamation-triangle"></i> Belum diambil
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge {{ $j->statusBadgeClass() }}">{{ $j->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada pekerjaan CCTV</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- TOP TEKNISI — 2 TABEL --}}
<div class="row g-3 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header header-komputer py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-trophy-fill"></i>
                    Top Teknisi Komputer — {{ ucfirst($periode) }}
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: .85rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%;" class="text-center">#</th>
                            <th style="width: 42%;">Teknisi</th>
                            <th style="width: 16%;" class="text-center">✅</th>
                            <th style="width: 16%;" class="text-center">⏳</th>
                            <th style="width: 18%;" class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($ratingHariIniKomputer as $i => $t)
                        <tr class="{{ $i == 0 ? 'table-warning' : '' }}">
                            <td class="text-center fw-bold" style="font-size: 1.1rem;">
                                @if($i == 0) 🥇
                                @elseif($i == 1) 🥈
                                @elseif($i == 2) 🥉
                                @else {{ $i + 1 }}
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-person-circle text-primary"></i>
                                <strong>{{ $t->display_name }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_selesai }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_belum }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_total }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: .3;"></i>
                                <div class="mt-2 small">Belum ada teknisi komputer yang aktif</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header header-cctv py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-trophy-fill"></i>
                    Top Teknisi CCTV — {{ ucfirst($periode) }}
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: .85rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%;" class="text-center">#</th>
                            <th style="width: 42%;">Teknisi</th>
                            <th style="width: 16%;" class="text-center">✅</th>
                            <th style="width: 16%;" class="text-center">⏳</th>
                            <th style="width: 18%;" class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($ratingHariIniCctv as $i => $t)
                        <tr class="{{ $i == 0 ? 'table-warning' : '' }}">
                            <td class="text-center fw-bold" style="font-size: 1.1rem;">
                                @if($i == 0) 🥇
                                @elseif($i == 1) 🥈
                                @elseif($i == 2) 🥉
                                @else {{ $i + 1 }}
                                @endif
                            </td>
                            <td>
                                <i class="bi bi-person-circle text-info"></i>
                                <strong>{{ $t->display_name }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_selesai }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_belum }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary" style="font-size: .85rem; padding: 6px 12px;">
                                    {{ $t->hari_ini_total }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem; opacity: .3;"></i>
                                <div class="mt-2 small">Belum ada teknisi CCTV yang aktif</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- GRAFIK TOP TEKNISI — 2 CHART --}}
<div class="row g-3 mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-pc-display text-primary"></i>
                    Top Teknisi Komputer (Keseluruhan) — {{ ucfirst($periode) }}
                </h6>
            </div>
            <div class="card-body">
                @if(count($rekapTeknisiKomputer) > 0)
                    <div class="chart-wrapper" style="height: {{ max(280, count($rekapTeknisiKomputer) * 70) }}px;">
                        <canvas id="chartKomputer"></canvas>
                    </div>
                @else
                    <div class="chart-empty">
                        <i class="bi bi-bar-chart"></i>
                        <h5>Belum Ada Data</h5>
                        <p class="mb-0">Belum ada teknisi komputer yang menyelesaikan tugas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-2">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-camera-video text-info"></i>
                    Top Teknisi CCTV (Keseluruhan) — {{ ucfirst($periode) }}
                </h6>
            </div>
            <div class="card-body">
                @if(count($rekapTeknisiCctv) > 0)
                    <div class="chart-wrapper" style="height: {{ max(280, count($rekapTeknisiCctv) * 70) }}px;">
                        <canvas id="chartCctv"></canvas>
                    </div>
                @else
                    <div class="chart-empty">
                        <i class="bi bi-bar-chart"></i>
                        <h5>Belum Ada Data</h5>
                        <p class="mb-0">Belum ada teknisi CCTV yang menyelesaikan tugas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function renderTopChart(canvasId, rekapData, colorTheme) {
        const ctx = document.getElementById(canvasId);
        if (!ctx || !rekapData || rekapData.length === 0) return;

        const warnaBar = rekapData.map((t, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 0.95)';
            if (i === 1) return 'rgba(108, 117, 125, 0.9)';
            if (i === 2) return 'rgba(205, 127, 50, 0.9)';
            return colorTheme === 'cctv' ? 'rgba(13, 202, 240, 0.7)' : 'rgba(13, 110, 253, 0.7)';
        });

        const borderBar = rekapData.map((t, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 1)';
            if (i === 1) return 'rgba(108, 117, 125, 1)';
            if (i === 2) return 'rgba(205, 127, 50, 1)';
            return colorTheme === 'cctv' ? 'rgba(13, 202, 240, 1)' : 'rgba(13, 110, 253, 1)';
        });

        new Chart(ctx, {
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
                layout: { padding: { top: 15, right: 60, left: 5, bottom: 5 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 14,
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
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        title: {
                            display: true,
                            text: '📊 Jumlah Tugas Selesai',
                            font: { size: 11, weight: 'bold' }
                        }
                    },
                    y: { grid: { display: false } }
                },
                animation: {
                    duration: 800,
                    onComplete: function() {
                        const chart = this;
                        const c = chart.ctx;

                        c.save();
                        c.textAlign = 'left';
                        c.textBaseline = 'middle';

                        chart.data.datasets.forEach(function(dataset, i) {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                const t = rekapData[index];

                                c.font = 'bold 15px Arial';
                                c.fillStyle = '#212529';
                                c.fillText(t.selesai, bar.x + 10, bar.y - 6);

                                c.font = '10px Arial';
                                c.fillStyle = '#6c757d';
                                c.fillText('dari ' + t.total + ' tugas', bar.x + 10, bar.y + 9);
                            });
                        });
                        c.restore();
                    }
                }
            }
        });
    }

    const rekapKomputer = @json($rekapTeknisiKomputer);
    const rekapCctv     = @json($rekapTeknisiCctv);

    renderTopChart('chartKomputer', rekapKomputer, 'komputer');
    renderTopChart('chartCctv', rekapCctv, 'cctv');

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
</script>
@endpush
@endsection