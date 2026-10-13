@extends('layouts.app')
@section('title', 'Selesaikan Tugas')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-check2-circle text-success"></i> Selesaikan Tugas
        </h5>
        <a href="{{ route('admin.show', $job) }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Alert --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong><i class="bi bi-exclamation-triangle"></i> Ada kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li><small>{{ $err }}</small></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info Job --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-primary text-white py-2">
            <h6 class="mb-0">
                <code>{{ $job->kode_job }}</code> — {{ $job->judul }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-2 small">
                <div class="col-md-6">
                    <strong>Bidang:</strong>
                    <span class="badge {{ $job->bidangBadgeClass() }}">
                        <i class="bi {{ $job->bidangIcon() }}"></i> {{ $job->bidangLabel() }}
                    </span>
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <span class="badge {{ $job->statusBadgeClass() }}">{{ $job->statusLabel() }}</span>
                </div>
                <div class="col-md-6">
                    <strong>Lokasi:</strong>
                    {{ $job->lok_gedung ?? '-' }} / {{ $job->lokasi_ruang ?? '-' }}
                </div>
                <div class="col-md-6">
                    <strong>Teknisi:</strong>
                    {{ $job->teknisi->display_name ?? 'Belum di-assign' }}
                </div>
                <div class="col-md-6">
                    <strong>Tanggal Tugas:</strong>
                    {{ $job->tanggal_tugas?->format('d-m-Y') ?? '-' }}
                </div>
                <div class="col-md-6">
                    <strong>Prioritas:</strong>
                    {{ $job->prioritas }}
                </div>
                @if($job->deskripsi)
                <div class="col-12">
                    <strong>Deskripsi:</strong>
                    <div class="text-muted">{{ $job->deskripsi }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Form Selesaikan --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white py-2">
            <h6 class="mb-0"><i class="bi bi-clipboard-check"></i> Form Penyelesaian</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.selesaikan', $job) }}" enctype="multipart/form-data">
                @csrf

                {{-- Teknisi (kalau belum di-assign) --}}
                @if(!$job->teknisi_id)
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Assign Teknisi <span class="text-muted">(opsional)</span>
                    </label>
                    <select name="teknisi_id" class="form-select">
                        <option value="">— Tidak assign teknisi —</option>
                        @foreach($teknisiList as $t)
                            <option value="{{ $t->id }}" @selected(old('teknisi_id') == $t->id)>
                                {{ $t->display_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Pilih teknisi kalau pekerjaan ini dikerjakan oleh teknisi tertentu.
                    </small>
                </div>
                @endif

                {{-- Hasil Pekerjaan --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Hasil Pekerjaan <span class="text-danger">*</span>
                    </label>
                    <textarea name="hasil_pekerjaan"
                              class="form-control @error('hasil_pekerjaan') is-invalid @enderror"
                              rows="4" required
                              placeholder="Jelaskan hasil pekerjaan yang sudah dilakukan...">{{ old('hasil_pekerjaan', $job->hasil_pekerjaan) }}</textarea>
                    @error('hasil_pekerjaan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Catatan Teknisi --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Teknisi</label>
                    <textarea name="catatan" class="form-control" rows="3"
                              placeholder="Catatan tambahan dari teknisi (opsional)...">{{ old('catatan', $job->catatan) }}</textarea>
                </div>

                {{-- Catatan Admin --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Admin</label>
                    <textarea name="catatan_admin" class="form-control" rows="3"
                              placeholder="Catatan dari admin (opsional)...">{{ old('catatan_admin', $job->catatan_admin) }}</textarea>
                </div>

                {{-- Tanggal Selesai --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Selesai</label>
                    <input type="datetime-local" name="tanggal_selesai" class="form-control"
                           value="{{ old('tanggal_selesai', now()->format('Y-m-d\TH:i')) }}">
                    <small class="text-muted">Kosongkan untuk menggunakan waktu sekarang.</small>
                </div>

                <hr>

                {{-- 🆕 Upload Foto SEBELUM --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-warning"></i> Foto Sebelum Pekerjaan
                    </label>
                    <input type="file" name="foto_sebelum[]" class="form-control"
                           accept="image/*" multiple>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Foto kondisi sebelum pekerjaan. Bisa pilih beberapa sekaligus (JPG, PNG, WEBP max 5MB).
                    </small>
                </div>

                {{-- Foto Sebelum yang sudah ada --}}
                @if($job->fotoSebelum->count() > 0)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sebelum yang Sudah Ada:</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($job->fotoSebelum as $f)
                            <a href="{{ asset($f->nama_file) }}" target="_blank">
                                <img src="{{ asset($f->nama_file) }}" class="rounded border" width="100">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Upload Foto SESUDAH --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-image text-success"></i> Foto Sesudah Pekerjaan
                    </label>
                    <input type="file" name="foto_sesudah[]" class="form-control"
                           accept="image/*" multiple>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Foto kondisi setelah pekerjaan. Bisa pilih beberapa sekaligus (JPG, PNG, WEBP max 5MB).
                    </small>
                </div>

                {{-- Foto Sesudah yang sudah ada --}}
                @if($job->fotoSesudah->count() > 0)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sesudah yang Sudah Ada:</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($job->fotoSesudah as $f)
                            <a href="{{ asset($f->nama_file) }}" target="_blank">
                                <img src="{{ asset($f->nama_file) }}" class="rounded border" width="100">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Perhatian:</strong> Setelah diselesaikan, status pekerjaan akan berubah menjadi
                    <span class="badge bg-success">SELESAI</span> dan tidak bisa dikembalikan.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill"
                            onclick="return confirm('Yakin selesaikan pekerjaan ini?')">
                        <i class="bi bi-check-circle-fill"></i> Selesaikan Pekerjaan
                    </button>
                    <a href="{{ route('admin.show', $job) }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection