@extends('layouts.app_admin')
@section('title', 'Rekap Absensi Teknisi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h3 class="fw-bold m-0"><i class="bi bi-person-lines-fill text-primary"></i> Rekap Absensi Teknisi</h3>
        <a href="{{ route('admin.absensi.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a>
    </div>

    {{-- Filter Bulan, Tahun, & Tanggal --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body bg-light rounded">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Bulan</label>
                    <select name="bulan" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Tahun</label>
                    <select name="tahun" class="form-select">
                        @for($i = now()->year; $i >= now()->year - 2; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                {{-- 🆕 INPUT TANGGAL SPESIFIK --}}
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-primary">Atau Tanggal Spesifik (Opsional)</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggalCari ?? '' }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Rekap Detail --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Tanggal</th>
                        <th>Nama Teknisi</th>
                        <th class="text-center" style="background-color: #d1e7dd;">Foto Masuk</th>
                        <th class="text-center" style="background-color: #d1e7dd;">Jam Masuk</th>
                        <th class="text-center" style="background-color: #f8d7da;">Foto Pulang</th>
                        <th class="text-center" style="background-color: #f8d7da;">Jam Pulang</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekap as $index => $r)
                        @php
                            $user = \App\Models\User::find($r->user_id);
                            $status = \App\Models\Attendance::hitungStatus($r->jam_masuk);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                            
                            <td>
                                <div class="fw-bold">{{ $user->display_name ?? '-' }}</div>
                                <span class="badge bg-{{ $user && $user->isTeknisiCctv() ? 'info text-dark' : 'primary' }} small">
                                    {{ $user ? $user->bidangLabel() : '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if(isset($r->foto_masuk) && $r->foto_masuk)
                                    <img src="{{ asset($r->foto_masuk) }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #198754;"
                                         onclick="window.open(this.src, '_blank')" alt="Foto Masuk">
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>

                            <td class="text-center fw-bold text-success fs-6">
                                {{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '-' }}
                            </td>

                            <td class="text-center">
                                @if(isset($r->foto_pulang) && $r->foto_pulang)
                                    <img src="{{ asset($r->foto_pulang) }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #dc3545;"
                                         onclick="window.open(this.src, '_blank')" alt="Foto Pulang">
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>

                            <td class="text-center fw-bold text-danger fs-6">
                                {{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '-' }}
                            </td>

                            <td class="text-center">
                                <span class="badge bg-{{ $status === 'hadir' ? 'success' : 'warning text-dark' }}">
                                    {{ strtoupper($status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i><br>
                                Belum ada data absensi pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection