@extends('layouts.app_pwa')
@section('title', 'Buat Tugas ' . (($bidang ?? 'komputer') === 'cctv' ? 'CCTV' : 'Komputer'))

@php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
@endphp

@push('styles')
<style>
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }
    
    .pwa-card {
        background: white; border-radius: 20px; padding: 24px 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    .pwa-header-title {
        font-size: 1.2rem; font-weight: 800; color: #1a1a1a; margin-bottom: 4px;
    }
    .pwa-subtitle {
        font-size: 0.8rem; color: #888; margin-bottom: 24px; display: block;
    }

    .form-group { margin-bottom: 18px; }
    .form-label { font-size: 0.85rem; font-weight: 700; color: #333; margin-bottom: 8px; display: block; }
    
    .form-control-pwa {
        width: 100%; padding: 12px 16px; border-radius: 12px;
        border: 1px solid #e0e0e0; background: #f9f9f9;
        font-size: 0.95rem; color: #1a1a1a; transition: all 0.2s;
    }
    .form-control-pwa:focus {
        outline: none; border-color: {{ $primaryColor }}; background: white;
        box-shadow: 0 0 0 3px {{ $isCctv ? 'rgba(13, 202, 240, 0.2)' : 'rgba(13, 110, 253, 0.2)' }};
    }
    
    .btn-submit-pwa {
        width: 100%; padding: 14px; border-radius: 12px; border: none;
        background: {{ $primaryColor }}; color: white; font-size: 1rem; font-weight: 800;
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-top: 10px; cursor: pointer; transition: transform 0.15s;
    }
    .btn-submit-pwa:active { transform: scale(0.98); }
    
    .btn-cancel-pwa {
        display: block; text-align: center; width: 100%; padding: 12px;
        border-radius: 12px; color: #888; font-weight: 700; text-decoration: none;
        margin-top: 10px; font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="app-container">

    <div class="d-flex align-items-center mb-3">
        <a href="{{ route($rp . '.dashboard') }}" style="color: #333; font-size: 1.5rem; margin-right: 12px; text-decoration: none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div class="pwa-header-title">Buat Tugas Baru</div>
            <div class="pwa-subtitle">Tugas {{ $isCctv ? 'CCTV' : 'Komputer' }}</div>
        </div>
    </div>

    <div class="pwa-card">
        <form method="POST" action="{{ route($rp . '.store') }}" id="formBuatTugas">
            @csrf

            <div class="form-group">
                <label class="form-label">Judul Pekerjaan <span style="color:red">*</span></label>
                <input name="judul" class="form-control-pwa" value="{{ old('judul') }}" required placeholder="Contoh: Perbaikan printer lantai 2">
            </div>

            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Kategori</label>
                    <input name="kategori" class="form-control-pwa" value="{{ old('kategori') }}"
                           placeholder="{{ $isCctv ? 'CCTV / DVR' : 'PC / Printer' }}">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Prioritas</label>
                    <select name="prioritas" class="form-control-pwa">
                        <option>Rendah</option>
                        <option selected>Sedang</option>
                        <option>Tinggi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Tugas</label>
                <input type="date" name="tanggal_tugas" class="form-control-pwa"
                       value="{{ old('tanggal_tugas', now()->format('Y-m-d')) }}" required>
            </div>

            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Lokasi Gedung</label>
                    <input name="lok_gedung" class="form-control-pwa" value="{{ old('lok_gedung') }}" placeholder="Gedung A">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Lokasi Ruang</label>
                    <input name="lokasi_ruang" class="form-control-pwa" value="{{ old('lokasi_ruang') }}" placeholder="Ruang IT">
                </div>
            </div>

            <div class="row" style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Nama Perangkat</label>
                    <input name="computer_name" class="form-control-pwa" value="{{ old('computer_name') }}"
                           placeholder="{{ $isCctv ? 'CAM-01' : 'PC-IT-01' }}">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Merk</label>
                    <input name="merk" class="form-control-pwa" value="{{ old('merk') }}"
                           placeholder="{{ $isCctv ? 'Hikvision' : 'Lenovo' }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi / Keluhan</label>
                <textarea name="deskripsi" class="form-control-pwa" rows="3" placeholder="Jelaskan detail masalahnya...">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit" class="btn-submit-pwa" id="btnSubmit">
                <i class="bi bi-save"></i> Simpan Tugas
            </button>
            <a href="{{ route($rp . '.dashboard') }}" class="btn-cancel-pwa">Batal</a>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formBuatTugas');
        const btnSubmit = document.getElementById('btnSubmit');

        if (form && btnSubmit) {
            form.addEventListener('submit', function() {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            });
        }
    });
</script>
@endpush