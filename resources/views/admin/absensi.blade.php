@extends('layouts.app') {{-- Sesuaikan dengan layout admin Anda --}}
@section('title', 'Rekap Absensi Teknisi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Rekap Absensi Teknisi</h3>
        <a href="{{ route('admin.absensi.export', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="bulan" class="form-select">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="tahun" class="form-select">
                        @for($i = now()->year; $i >= now()->year - 2; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4"><button type="submit" class="btn btn-primary w-100">Tampilkan</button></div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                    <tr><th>No</th><th>Tanggal</th><th>Nama Teknisi</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($rekap as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                            <td class="fw-bold">{{ $r->user->display_name ?? '-' }}</td>
                            <td>{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '-' }}</td>
                            <td>{{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '-' }}</td>
                            <td><span class="badge bg-{{ $r->status === 'hadir' ? 'success' : 'warning text-dark' }}">{{ strtoupper($r->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection