@extends('layouts.app')
@section('title', 'Update Pekerjaan')
@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
    <div class="card-header bg-warning"><h5 class="mb-0">Update Pekerjaan #{{ $job->kode_job }}</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('teknisi.update', $job) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="DIKERJAKAN" @selected($job->status === 'DIKERJAKAN')>DIKERJAKAN</option>
                    <option value="SELESAI" @selected($job->status === 'SELESAI')>SELESAI</option>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Hasil Pekerjaan</label><textarea name="hasil_pekerjaan" class="form-control" rows="3">{{ $job->hasil_pekerjaan }}</textarea></div>
            <div class="mb-3">
                <label class="form-label">Foto Sebelum (bisa multiple)</label>
                <input type="file" name="foto_sebelum[]" class="form-control" multiple accept="image/*">
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @foreach($job->fotoSebelum as $f)
                        <img src="{{ asset($f->nama_file) }}" class="rounded" width="80">
                    @endforeach
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Sesudah (bisa multiple)</label>
                <input type="file" name="foto_sesudah[]" class="form-control" multiple accept="image/*">
                <div class="mt-2 d-flex flex-wrap gap-2">
                    @foreach($job->fotoSesudah as $f)
                        <img src="{{ asset($f->nama_file) }}" class="rounded" width="80">
                    @endforeach
                </div>
            </div>
            <div class="mb-3"><label class="form-label">Catatan</label><textarea name="catatan" class="form-control" rows="2">{{ $job->catatan }}</textarea></div>
            <button class="btn btn-primary">Simpan Laporan</button>
            <a href="{{ route('teknisi.dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</div>
</div>
@endsection