@extends('layouts.app')
@section('title', 'Buat Tugas')
@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white"><h5 class="mb-0">Tambah Pekerjaan</h5></div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Judul Pekerjaan</label>
                <input name="judul" class="form-control" value="{{ old('judul') }}" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teknisi</label>
                    <select name="teknisi_id" class="form-select">
                        <option value="">— Biarkan Kosong (Bebas Diambil Siapa Saja) —</option>
                        @foreach($teknisiList as $t)
                            <option value="{{ $t->id }}" @selected(old('teknisi_id') == $t->id)>{{ $t->display_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Kosongkan kalau ingin teknisi ambil sendiri.
                    </small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input name="kategori" class="form-control" value="{{ old('kategori') }}" placeholder="CCTV / Switch / AP / PC">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-select">
                        <option>Rendah</option>
                        <option selected>Sedang</option>
                        <option>Tinggi</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal_tugas" class="form-control" value="{{ old('tanggal_tugas', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Gedung</label>
                    <input name="lok_gedung" class="form-control" value="{{ old('lok_gedung') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Ruang</label>
                    <input name="lokasi_ruang" class="form-control" value="{{ old('lokasi_ruang') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Perangkat</label>
                    <input name="computer_name" class="form-control" value="{{ old('computer_name') }}" placeholder="Contoh: PC-IT-01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Merk</label>
                    <input name="merk" class="form-control" value="{{ old('merk') }}" placeholder="Contoh: Lenovo">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>
            <button class="btn btn-primary">Simpan Tugas</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</div>
</div>
@endsection