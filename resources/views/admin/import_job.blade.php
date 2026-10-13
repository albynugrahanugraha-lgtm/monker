@extends('layouts.app')
@section('title', 'Import Pekerjaan dari Excel')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-file-earmark-excel text-success"></i>
            Import Pekerjaan dari Excel
        </h5>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert warning --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Alert error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Detail error dari import --}}
    @if(session('import_errors'))
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle"></i> Detail Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $err)
                    <li><small>{{ $err }}</small></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- STEP 1: Download Template --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-1-circle-fill"></i> Download Template</h6>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-2">
                Download template Excel, isi sesuai kolomnya, lalu upload di langkah 2.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.import.template', ['bidang' => 'cctv']) }}"
                   class="btn btn-success">
                    <i class="bi bi-download"></i> Template CCTV
                </a>
                <a href="{{ route('admin.import.template', ['bidang' => 'komputer']) }}"
                   class="btn btn-primary">
                    <i class="bi bi-download"></i> Template Komputer
                </a>
            </div>
        </div>
    </div>

    {{-- STEP 2: Upload --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0"><i class="bi bi-2-circle-fill"></i> Upload File Excel</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Bidang <span class="text-danger">*</span>
                    </label>
                    <select name="bidang" class="form-select" required>
                        <option value="cctv" @selected(old('bidang', 'cctv') === 'cctv')>
                            📹 CCTV
                        </option>
                        <option value="komputer" @selected(old('bidang') === 'komputer')>
                            🖥️ Komputer
                        </option>
                    </select>
                    <small class="text-muted">
                        Pilih bidang sesuai isi file Excel yang akan diimport.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        File Excel <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="file"
                           class="form-control @error('file') is-invalid @enderror"
                           accept=".xlsx,.xls,.csv" required>
                    <small class="text-muted">
                        Format: <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code> (max 10MB)
                    </small>
                    @error('file')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Catatan:</strong>
                    <ul class="mb-0 mt-1">
                        <li>Kolom <strong>judul</strong> & <strong>tanggal_tugas</strong> wajib diisi</li>
                        <li>Kolom <strong>teknisi_username</strong> opsional — kosongkan kalau ingin tugas bebas diambil</li>
                        <li>Format tanggal: <code>YYYY-MM-DD</code> atau <code>DD/MM/YYYY</code></li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-cloud-upload"></i> Import Sekarang
                </button>
            </form>
        </div>
    </div>

    {{-- STEP 3: Info Kolom --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-2">
            <h6 class="mb-0"><i class="bi bi-3-circle-fill"></i> Daftar Kolom Template CCTV</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib?</th>
                            <th>Contoh</th>
                        </tr>
                    </thead>
                    <tbody style="font-size:.85rem;">
                        <tr><td>judul</td><td><span class="badge bg-danger">Wajib</span></td><td>Perbaikan Kamera Lobby</td></tr>
                        <tr><td>deskripsi</td><td>—</td><td>Kamera mati sejak pagi</td></tr>
                        <tr><td>kategori</td><td>—</td><td>CCTV</td></tr>
                        <tr><td>prioritas</td><td>—</td><td>Rendah / Sedang / Tinggi</td></tr>
                        <tr><td>lokasi_gedung</td><td>—</td><td>Gedung A</td></tr>
                        <tr><td>lokasi_ruang</td><td>—</td><td>Lobby</td></tr>
                        <tr><td>titik_kamera</td><td>—</td><td>Pintu Masuk</td></tr>
                        <tr><td>channel_dvr</td><td>—</td><td>CH-01</td></tr>
                        <tr><td>jenis_kamera</td><td>—</td><td>Dome Indoor</td></tr>
                        <tr><td>nama_perangkat</td><td>—</td><td>CAM-01</td></tr>
                        <tr><td>merk</td><td>—</td><td>Hikvision</td></tr>
                        <tr>
                            <td>teknisi_username</td>
                            <td>—</td>
                            <td>
                                <code>teknisi_cctv</code>
                                <br>
                                <small class="text-muted">Kosong = bebas diambil</small>
                            </td>
                        </tr>
                        <tr><td>tanggal_tugas</td><td><span class="badge bg-danger">Wajib</span></td><td>2026-10-09</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
@endsection