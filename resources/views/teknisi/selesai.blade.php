@extends('layouts.app')
@section('title', 'Kerjakan Pekerjaan')

@php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
@endphp

@section('content')
<div class="row justify-content-center">
<div class="col-12 col-md-8">

    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-1">{{ $job->judul }}</h5>
            <div class="small text-muted">
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-hash"></i>{{ $job->kode_job }}
                </span>
                <span class="badge {{ $job->bidangBadgeClass() }} ms-1">
                    <i class="bi {{ $job->bidangIcon() }}"></i> {{ $job->bidangLabel() }}
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

    <div class="card shadow-sm border-0">
        <div class="card-header {{ $isCctv ? 'bg-info text-dark' : 'bg-primary text-white' }}">
            <h5 class="mb-0"><i class="bi bi-tools"></i> Kerjakan & Selesaikan Pekerjaan</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route($rp . '.selesai', $job) }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hasil Pekerjaan <span class="text-danger">*</span></label>
                    <textarea name="hasil_pekerjaan" class="form-control" rows="3" required
                              placeholder="Contoh: {{ $isCctv ? 'Kamera CCTV sudah normal kembali' : 'Printer sudah bisa digunakan kembali' }}">{{ old('hasil_pekerjaan', $job->hasil_pekerjaan) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan (opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2"
                              placeholder="{{ $isCctv ? 'Contoh: Penggantian kabel BNC' : 'Contoh: Penggantian kabel LAN' }}">{{ old('catatan', $job->catatan) }}</textarea>
                </div>

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
                    <a href="{{ route($rp . '.dashboard') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection