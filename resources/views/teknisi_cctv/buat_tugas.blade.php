@extends('layouts.app')
@section('title', 'Buat Tugas ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer'))

@php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
@endphp

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card shadow-sm">
    <div class="card-header {{ $isCctv ? 'bg-info text-dark' : 'bg-primary text-white' }}">
        <h5 class="mb-0">
            <i class="bi {{ $isCctv ? 'bi-camera-video' : 'bi-pc-display' }}"></i>
            Buat Tugas {{ $isCctv ? 'CCTV' : 'Komputer' }} Sendiri
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route($rp . '.store') }}">
            @csrf

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

            <div class="mb-3">
                <label class="form-label">Judul Pekerjaan <span class="text-danger">*</span></label>
                <input name="judul" class="form-control" value="{{ old('judul') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori</label>
                    <input name="kategori" class="form-control" value="{{ old('kategori') }}"
                           placeholder="{{ $isCctv ? 'CCTV / DVR / NVR / Kabel' : 'PC / Switch / AP / Printer' }}">
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
                    <input type="date" name="tanggal_tugas" class="form-control"
                           value="{{ old('tanggal_tugas', now()->format('Y-m-d')) }}" required>
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
                    <input name="computer_name" class="form-control" value="{{ old('computer_name') }}"
                           placeholder="{{ $isCctv ? 'Contoh: CAM-01 / DVR-01' : 'Contoh: PC-IT-01' }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Merk</label>
                    <input name="merk" class="form-control" value="{{ old('merk') }}"
                           placeholder="{{ $isCctv ? 'Contoh: Hikvision' : 'Contoh: Lenovo' }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
            </div>

            <button class="btn {{ $isCctv ? 'btn-info' : 'btn-primary' }}">
                <i class="bi bi-save"></i> Simpan Tugas
            </button>
            <a href="{{ route($rp . '.dashboard') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</div>
</div>
@endsection