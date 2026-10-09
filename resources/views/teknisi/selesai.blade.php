@extends('layouts.app')
@section('title', 'Kerjakan Pekerjaan')

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">

    {{-- INFO PEKERJAAN --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-1">{{ $job->judul }}</h5>
            <div class="small text-muted">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-hash"></i>{{ $job->kode_job }}
                </span>
                <span class="ms-2"><i class="bi bi-geo-alt"></i>
                    {{ $job->lok_gedung ?? '-' }} / {{ $job->lokasi_ruang ?? '-' }}
                </span>
                <span class="ms-2"><i class="bi bi-calendar"></i>
                    {{ $job->tanggal_tugas?->format('d M Y') }}
                </span>
            </div>
        </div>
    </div>

    {{-- FORM KERJAKAN & SELESAIKAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-tools"></i> Kerjakan & Selesaikan Pekerjaan</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('teknisi.selesai', $job) }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hasil Pekerjaan <span class="text-danger">*</span></label>
                    <textarea name="hasil_pekerjaan" class="form-control" rows="3"
                              placeholder="Contoh: Printer sudah bisa digunakan kembali" required>{{ old('hasil_pekerjaan', $job->hasil_pekerjaan) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"
                              placeholder="Contoh: Penggantian kabel LAN">{{ old('catatan', $job->catatan) }}</textarea>
                </div>

                {{-- FOTO SEBELUM --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sebelum</label>
                    <input type="file" name="foto_sebelum[]" class="form-control" multiple accept="image/*">
                    @if($job->fotoSebelum->count() > 0)
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach($job->fotoSebelum as $f)
                                <img src="{{ asset($f->nama_file) }}" class="rounded border" width="70">
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- FOTO SESUDAH --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Foto Sesudah</label>
                    <input type="file" name="foto_sesudah[]" class="form-control" multiple accept="image/*">
                    @if($job->fotoSesudah->count() > 0)
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach($job->fotoSesudah as $f)
                                <img src="{{ asset($f->nama_file) }}" class="rounded border border-success" width="70">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-fill">
                        <i class="bi bi-check-circle"></i> Simpan & Selesaikan
                    </button>
                    <a href="{{ route('teknisi.dashboard') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection