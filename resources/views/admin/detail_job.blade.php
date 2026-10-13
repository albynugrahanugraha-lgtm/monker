@extends('layouts.app')
@section('title', 'Detail Pekerjaan')
@section('content')
<div class="row justify-content-center">
<div class="col-md-10">
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Pekerjaan #{{ $job->kode_job }}</h5>
        <div>
            <span class="badge {{ $job->bidangBadgeClass() }} me-1">
                <i class="bi {{ $job->bidangIcon() }}"></i> {{ $job->bidangLabel() }}
            </span>
            <span class="badge {{ $job->statusBadgeClass() }}">{{ $job->statusLabel() }}</span>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr><th width="200">Judul</th><td>{{ $job->judul }}</td></tr>
            <tr><th>Deskripsi</th><td>{{ $job->deskripsi ?? '-' }}</td></tr>
            <tr><th>Teknisi</th><td>{{ $job->teknisi->display_name ?? '-' }}</td></tr>
            <tr><th>Lokasi</th><td>{{ $job->lok_gedung ?? '-' }} / {{ $job->lokasi_ruang ?? '-' }}</td></tr>
            <tr><th>Perangkat</th><td>{{ $job->computer_name ?? '-' }} ({{ $job->merk ?? '-' }})</td></tr>
            <tr><th>Kategori</th><td>{{ $job->kategori ?? '-' }}</td></tr>
            <tr><th>Prioritas</th><td>{{ $job->prioritas }}</td></tr>
            <tr><th>Tanggal Tugas</th><td>{{ $job->tanggal_tugas?->format('d-m-Y') }}</td></tr>
            <tr><th>Tanggal Selesai</th><td>{{ $job->tanggal_selesai?->format('d-m-Y H:i') ?? '-' }}</td></tr>
            <tr><th>Hasil Pekerjaan</th><td>{{ $job->hasil_pekerjaan ?? '-' }}</td></tr>
            <tr><th>Catatan Teknisi</th><td>{{ $job->catatan ?? '-' }}</td></tr>
            <tr><th>Catatan Admin</th><td>{{ $job->catatan_admin ?? '-' }}</td></tr>
        </table>

        <h6 class="mt-4">
            <i class="bi bi-image text-warning"></i>
            Foto Sebelum
            <span class="badge bg-warning text-dark">{{ $job->fotoSebelum->count() }}</span>
        </h6>
        <div class="d-flex flex-wrap gap-2 mb-3">
            @forelse($job->fotoSebelum as $f)
                <a href="{{ asset($f->nama_file) }}" target="_blank">
                    <img src="{{ asset($f->nama_file) }}" class="rounded border" width="120">
                </a>
            @empty
                <span class="text-muted">Belum ada foto sebelum</span>
            @endforelse
        </div>

        <h6>
            <i class="bi bi-image text-success"></i>
            Foto Sesudah
            <span class="badge bg-success">{{ $job->fotoSesudah->count() }}</span>
        </h6>
        <div class="d-flex flex-wrap gap-2">
            @forelse($job->fotoSesudah as $f)
                <a href="{{ asset($f->nama_file) }}" target="_blank">
                    <img src="{{ asset($f->nama_file) }}" class="rounded border" width="120">
                </a>
            @empty
                <span class="text-muted">Belum ada foto sesudah</span>
            @endforelse
        </div>

        <hr class="mt-4">

        @if($job->status === 'SELESAI')
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Pekerjaan sudah diselesaikan.</strong>
                    @if($job->tanggal_selesai)
                        <br><small>Selesai pada: {{ $job->tanggal_selesai->format('d-m-Y H:i') }}</small>
                    @endif
                </div>
            </div>
        @elseif($job->status === 'DIKERJAKAN')
            <div class="alert alert-warning">
                <i class="bi bi-tools"></i> Pekerjaan sedang dikerjakan oleh teknisi.
            </div>
        @else
            <div class="alert alert-secondary">
                <i class="bi bi-hourglass"></i> Pekerjaan belum dimulai.
            </div>
        @endif

        <div class="mt-3 d-flex gap-2 flex-wrap">
            @if($job->bisaDiselesaikan())
                <a href="{{ route('admin.selesaikan.form', $job) }}" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Selesaikan Pekerjaan
                </a>
            @endif

            <a href="{{ route('admin.cetak', $job) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Kembali</a>

            <form method="POST" action="{{ route('admin.destroy', $job) }}"
                  onsubmit="return confirm('Yakin hapus pekerjaan ini? Tindakan tidak bisa dibatalkan.')"
                  class="ms-auto">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">
                    <i class="bi bi-trash"></i> Hapus Pekerjaan
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><h6 class="mb-0">Riwayat Pekerjaan</h6></div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @forelse($job->histories as $h)
                <li class="list-group-item">
                    <small class="text-muted">{{ $h->created_at->format('d-m-Y H:i') }}</small> —
                    <strong>{{ $h->user->display_name ?? 'System' }}</strong>:
                    {{ $h->status_lama ?? '-' }} → {{ $h->status_baru }}
                    @if($h->keterangan)<br><small class="text-muted">{{ $h->keterangan }}</small>@endif
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada riwayat</li>
            @endforelse
        </ul>
    </div>
</div>
</div>
</div>
@endsection