@extends('layouts.app_pwa')
@section('title', 'Kerjakan Pekerjaan')

@php
    $rp = $routePrefix ?? 'teknisi';
    $isCctv = ($bidang ?? 'komputer') === 'cctv';
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
@endphp

@push('styles')
<style>
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }

    /* TOMBOL BACK */
    .header-back {
        display: flex; align-items: center; margin-bottom: 20px;
    }
    .header-back a {
        color: #333; font-size: 1.5rem; margin-right: 12px; text-decoration: none;
    }
    .header-back .title { font-size: 1.1rem; font-weight: 800; color: #1a1a1a; }
    .header-back .subtitle { font-size: 0.8rem; color: #888; }

    /* CARD INFO TUGAS */
    .job-info-card {
        background: linear-gradient(135deg, {{ $primaryColor }}, {{ $isCctv ? '#0aa2c0' : '#0a58ca' }});
        border-radius: 20px; padding: 20px; color: white; margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba({{ $isCctv ? '13, 202, 240' : '13, 110, 253' }}, 0.3);
    }
    .job-info-card h5 { font-size: 1.1rem; font-weight: 800; margin: 0 0 8px 0; line-height: 1.3; }
    .job-info-card .meta { font-size: 0.75rem; opacity: 0.9; display: flex; flex-wrap: wrap; gap: 10px; }

    /* CARD FORM */
    .form-card {
        background: white; border-radius: 20px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 20px;
    }

    .form-label { font-size: 0.85rem; font-weight: 700; color: #333; margin-bottom: 8px; display: block; }
    .form-control-pwa {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e0e0e0;
        background: #f9f9f9; font-size: 0.95rem; margin-bottom: 16px;
    }
    .form-control-pwa:focus { outline: none; border-color: {{ $primaryColor }}; background: white; }

    /* FOTO SLOT PWA */
    .foto-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
    .foto-slot {
        position: relative; aspect-ratio: 1; border-radius: 16px;
        border: 2px dashed #ccc; background: #f9f9f9;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        cursor: pointer; overflow: hidden; transition: all 0.2s;
    }
    .foto-slot:active { transform: scale(0.95); }
    .foto-slot.has-file { border: 2px solid #198754; background: #e8f5e9; }
    .foto-slot .icon { font-size: 1.8rem; color: #bbb; margin-bottom: 4px; }
    .foto-slot .label { font-size: 0.65rem; color: #888; font-weight: 700; }
    
    .foto-slot.has-file .icon, .foto-slot.has-file .label { display: none; }
    .foto-slot img { width: 100%; height: 100%; object-fit: cover; display: none; }
    .foto-slot.has-file img { display: block; }

    .foto-remove {
        position: absolute; top: 4px; right: 4px; width: 22px; height: 22px;
        background: #dc3545; color: white; border: none; border-radius: 50%;
        font-size: 0.7rem; display: flex; align-items: center; justify-content: center;
        z-index: 10;
    }
    .file-input-hidden { display: none; }

    /* FOTO LAMA */
    .foto-lama-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
    .foto-lama-item { position: relative; width: 70px; height: 70px; border-radius: 10px; overflow: hidden; border: 1px solid #ddd; }
    .foto-lama-item img { width: 100%; height: 100%; object-fit: cover; }

    .btn-submit-pwa {
        width: 100%; padding: 14px; border-radius: 14px; border: none;
        background: #198754; color: white; font-size: 1rem; font-weight: 800;
        display: flex; justify-content: center; align-items: center; gap: 8px;
        margin-top: 10px; cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="app-container">

    {{-- TOMBOL BACK --}}
    <div class="header-back">
        <a href="{{ route($rp . '.dashboard') }}">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
        <div>
            <div class="title">Kerjakan Tugas</div>
            <div class="subtitle">Pastikan foto & hasil sudah benar</div>
        </div>
    </div>

    {{-- INFO TUGAS --}}
    <div class="job-info-card">
        <div class="d-flex justify-content-between align-items-start">
            <h5>{{ $job->judul }}</h5>
            <span class="badge bg-light text-dark" style="font-size:0.65rem;"><i class="bi bi-hash"></i>{{ $job->kode_job }}</span>
        </div>
        <div class="meta">
            <span><i class="bi bi-geo-alt-fill"></i> {{ $job->lok_gedung ?? '-' }} / {{ $job->lokasi_ruang ?? '-' }}</span>
            <span><i class="bi bi-calendar3"></i> {{ $job->tanggal_tugas?->format('d M Y') }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route($rp . '.selesai', $job) }}" enctype="multipart/form-data">
        @csrf

        <div class="form-card">
            <label class="form-label">Hasil Pekerjaan <span style="color:red">*</span></label>
            <textarea name="hasil_pekerjaan" class="form-control-pwa" rows="3" required
                      placeholder="Tuliskan apa yang sudah diperbaiki...">{{ old('hasil_pekerjaan', $job->hasil_pekerjaan) }}</textarea>

            <label class="form-label">Catatan Tambahan (opsional)</label>
            <textarea name="catatan" class="form-control-pwa" rows="2"
                      placeholder="Contoh: Ganti kabel LAN baru">{{ old('catatan', $job->catatan) }}</textarea>
        </div>

        <div class="form-card">
            <label class="form-label"><i class="bi bi-image text-warning"></i> Foto Sebelum (Maks 3)</label>
            <div class="foto-grid">
                @for($i = 1; $i <= 3; $i++)
                    <input type="file" class="file-input-hidden" name="foto_sebelum_{{ $i }}" id="inputSebelum{{ $i }}" accept="image/*" onchange="handleFile(this, 'Sebelum', {{ $i }})">
                    <div class="foto-slot" id="slotSebelum{{ $i }}" onclick="document.getElementById('inputSebelum{{ $i }}').click()">
                        <i class="bi bi-camera icon"></i>
                        <div class="label">Foto {{ $i }}</div>
                        <img id="previewSebelum{{ $i }}" src="">
                        <button type="button" class="foto-remove" id="removeSebelum{{ $i }}" onclick="resetSlot(event, 'Sebelum', {{ $i }})" style="display:none;"><i class="bi bi-x"></i></button>
                    </div>
                @endfor
            </div>

            @if($job->fotoSebelum->count() > 0)
                <div style="font-size:0.75rem; color:#888; font-weight:bold; margin-top:10px;">Foto Tersimpan:</div>
                <div class="foto-lama-grid">
                    @foreach($job->fotoSebelum as $f)
                        <div class="foto-lama-item">
                            <img src="{{ asset($f->nama_file) }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="form-card">
            <label class="form-label"><i class="bi bi-image text-success"></i> Foto Sesudah (Maks 3)</label>
            <div class="foto-grid">
                @for($i = 1; $i <= 3; $i++)
                    <input type="file" class="file-input-hidden" name="foto_sesudah_{{ $i }}" id="inputSesudah{{ $i }}" accept="image/*" onchange="handleFile(this, 'Sesudah', {{ $i }})">
                    <div class="foto-slot" id="slotSesudah{{ $i }}" onclick="document.getElementById('inputSesudah{{ $i }}').click()">
                        <i class="bi bi-camera icon"></i>
                        <div class="label">Foto {{ $i }}</div>
                        <img id="previewSesudah{{ $i }}" src="">
                        <button type="button" class="foto-remove" id="removeSesudah{{ $i }}" onclick="resetSlot(event, 'Sesudah', {{ $i }})" style="display:none;"><i class="bi bi-x"></i></button>
                    </div>
                @endfor
            </div>

            @if($job->fotoSesudah->count() > 0)
                <div style="font-size:0.75rem; color:#888; font-weight:bold; margin-top:10px;">Foto Tersimpan:</div>
                <div class="foto-lama-grid">
                    @foreach($job->fotoSesudah as $f)
                        <div class="foto-lama-item">
                            <img src="{{ asset($f->nama_file) }}">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <button type="submit" class="btn-submit-pwa">
            <i class="bi bi-check-circle"></i> Simpan & Selesaikan
        </button>
        <a href="{{ route($rp . '.dashboard') }}" style="display:block; text-align:center; color:#888; text-decoration:none; margin-top:12px; font-size:0.9rem;">Batal</a>
    </form>

</div>
@endsection

@push('scripts')
<script>
    function handleFile(input, section, index) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (file.size > 10 * 1024 * 1024) {
                alert('Maksimal ukuran file 10MB');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const slot = document.getElementById('slot' + section + index);
                const img = document.getElementById('preview' + section + index);
                const removeBtn = document.getElementById('remove' + section + index);
                
                slot.classList.add('has-file');
                img.src = e.target.result;
                removeBtn.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    }

    function resetSlot(e, section, index) {
        e.stopPropagation();
        const input = document.getElementById('input' + section + index);
        const slot = document.getElementById('slot' + section + index);
        const img = document.getElementById('preview' + section + index);
        const removeBtn = document.getElementById('remove' + section + index);

        input.value = '';
        slot.classList.remove('has-file');
        img.src = '';
        removeBtn.style.display = 'none';
    }
</script>
@endpush