@extends('layouts.app_pwa')
@section('title', 'Detail Pekerjaan')

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

    .header-detail {
        background: linear-gradient(135deg, {{ $primaryColor }}, {{ $isCctv ? '#0aa2c0' : '#0a58ca' }});
        border-radius: 20px; padding: 20px; color: white; margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba({{ $isCctv ? '13, 202, 240' : '13, 110, 253' }}, 0.3);
    }
    .header-detail h5 { font-size: 1.2rem; font-weight: 800; margin: 0 0 8px 0; }
    
    .detail-card {
        background: white; border-radius: 20px; padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-bottom: 16px;
    }
    .detail-card h6 { font-size: 0.9rem; font-weight: 800; margin-bottom: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    
    .detail-row { display: flex; margin-bottom: 12px; font-size: 0.85rem; }
    .detail-row .lbl { width: 40%; color: #888; font-weight: 600; }
    .detail-row .val { width: 60%; font-weight: 700; color: #1a1a1a; word-break: break-word; }

    .foto-grid { display: flex; flex-wrap: wrap; gap: 10px; }
    .foto-item { position: relative; width: 80px; height: 80px; border-radius: 10px; overflow: hidden; border: 2px solid #ddd; }
    .foto-item img { width: 100%; height: 100%; object-fit: cover; }

    .btn-action {
        display: block; width: 100%; padding: 14px; border-radius: 14px; text-align: center;
        font-weight: 800; text-decoration: none; font-size: 0.95rem; margin-bottom: 10px;
    }

    .timeline { border-left: 2px solid {{ $primaryColor }}; padding-left: 16px; margin-left: 8px; }
    .timeline-item { position: relative; margin-bottom: 16px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -21px; top: 4px;
        width: 10px; height: 10px; border-radius: 50%; background: {{ $primaryColor }}; border: 2px solid white;
    }
    .timeline-date { font-size: 0.7rem; color: #888; font-weight: bold; margin-bottom: 2px; }
    .timeline-text { font-size: 0.85rem; color: #333; }

    /* Tombol Hapus Foto */
    .btn-hapus-foto {
        opacity: 0.85; transition: all 0.2s; border-radius: 50% !important;
        width: 24px; height: 24px; padding: 0 !important; line-height: 1 !important;
        display: flex; align-items: center; justify-content: center;
    }
    .btn-hapus-foto:hover { opacity: 1; transform: scale(1.15); }
</style>
@endpush

@section('content')
<div class="app-container">

    {{-- TOMBOL BACK KE DASHBOARD --}}
    <div class="header-back">
        <a href="{{ route($rp . '.dashboard') }}">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </a>
        <div>
            <div class="title">Detail Pekerjaan</div>
            <div class="subtitle">Kode: {{ $job->kode_job }}</div>
        </div>
    </div>

    {{-- HEADER TUGAS --}}
    <div class="header-detail">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span style="font-family: monospace; font-size: 0.8rem; background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 6px;"><i class="bi bi-hash"></i>{{ $job->kode_job }}</span>
            <span class="badge {{ $job->statusBadgeClass() }}">{{ $job->statusLabel() }}</span>
        </div>
        <h5>{{ $job->judul }}</h5>
        <div style="font-size: 0.8rem; opacity: 0.9;">
            <i class="bi bi-geo-alt-fill"></i> {{ $job->lok_gedung ?? '-' }} / {{ $job->lokasi_ruang ?? '-' }}
        </div>
    </div>

    {{-- TOMBOL AKSI --}}
    @if($job->bisaDiselesaikan())
        <a href="{{ route($rp . '.kerjakan', $job) }}" class="btn-action" style="background:#198754; color:white;">
            <i class="bi bi-play-circle"></i> Kerjakan Tugas Ini
        </a>
    @endif
    <a href="{{ route($rp . '.cetak', $job) }}" target="_blank" class="btn-action" style="background:#e9ecef; color:#333;">
        <i class="bi bi-printer"></i> Cetak Laporan
    </a>

    {{-- RINCIAN DATA --}}
    <div class="detail-card">
        <h6><i class="bi bi-info-circle"></i> Informasi Tugas</h6>
        <div class="detail-row"><div class="lbl">Bidang</div><div class="val">{{ $job->bidangLabel() }}</div></div>
        <div class="detail-row"><div class="lbl">Kategori</div><div class="val">{{ $job->kategori ?? '-' }}</div></div>
        <div class="detail-row"><div class="lbl">Perangkat</div><div class="val">{{ $job->computer_name ?? '-' }} ({{ $job->merk ?? '-' }})</div></div>
        <div class="detail-row"><div class="lbl">Teknisi</div><div class="val">{{ $job->teknisi->display_name ?? 'Belum di-assign' }}</div></div>
        <div class="detail-row"><div class="lbl">Tanggal</div><div class="val">{{ $job->tanggal_tugas?->format('d F Y') ?? '-' }}</div></div>
        @if($job->deskripsi)
            <div class="detail-row"><div class="lbl">Deskripsi</div><div class="val">{{ $job->deskripsi }}</div></div>
        @endif
    </div>

    {{-- HASIL PEKERJAAN --}}
    @if($job->hasil_pekerjaan)
        <div class="detail-card" style="border-left: 5px solid #198754;">
            <h6 style="color:#198754;"><i class="bi bi-check-circle-fill"></i> Hasil Pekerjaan</h6>
            <div style="font-size:0.9rem; font-weight:600; margin-bottom:8px;">{{ $job->hasil_pekerjaan }}</div>
            @if($job->catatan)
                <div style="font-size:0.8rem; color:#666;"><em>Catatan: {{ $job->catatan }}</em></div>
            @endif
        </div>
    @endif

    {{-- FOTO SEBELUM --}}
    <div class="detail-card">
        <h6><i class="bi bi-image" style="color:#ffc107;"></i> Foto Sebelum ({{ $job->fotoSebelum->count() }})</h6>
        <div class="foto-grid" id="wrapperFotoSebelum">
            @forelse($job->fotoSebelum as $f)
                <div class="foto-item" data-foto-id="{{ $f->id }}">
                    <a href="{{ asset($f->nama_file) }}" target="_blank"><img src="{{ asset($f->nama_file) }}"></a>
                    <button type="button"
                            class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                            data-foto-id="{{ $f->id }}"
                            data-foto-jenis="sebelum"
                            title="Hapus foto">
                        <i class="bi bi-x" style="font-size: .85rem;"></i>
                    </button>
                </div>
            @empty
                <div style="font-size:0.8rem; color:#888; padding:10px;">Belum ada foto</div>
            @endforelse
        </div>
    </div>

    {{-- FOTO SESUDAH --}}
    <div class="detail-card">
        <h6><i class="bi bi-image" style="color:#198754;"></i> Foto Sesudah ({{ $job->fotoSesudah->count() }})</h6>
        <div class="foto-grid" id="wrapperFotoSesudah">
            @forelse($job->fotoSesudah as $f)
                <div class="foto-item" data-foto-id="{{ $f->id }}">
                    <a href="{{ asset($f->nama_file) }}" target="_blank"><img src="{{ asset($f->nama_file) }}"></a>
                    <button type="button"
                            class="btn btn-danger btn-sm btn-hapus-foto position-absolute top-0 end-0 m-1"
                            data-foto-id="{{ $f->id }}"
                            data-foto-jenis="sesudah"
                            title="Hapus foto">
                        <i class="bi bi-x" style="font-size: .85rem;"></i>
                    </button>
                </div>
            @empty
                <div style="font-size:0.8rem; color:#888; padding:10px;">Belum ada foto</div>
            @endforelse
        </div>
    </div>

    {{-- RIWAYAT (TIMELINE) --}}
    @if($job->histories->count() > 0)
        <div class="detail-card">
            <h6><i class="bi bi-clock-history"></i> Riwayat Aktivitas</h6>
            <div class="timeline">
                @foreach($job->histories as $h)
                    <div class="timeline-item">
                        <div class="timeline-date">{{ $h->created_at->format('d M Y - H:i') }}</div>
                        <div class="timeline-text">
                            <strong>{{ $h->user->display_name ?? 'System' }}</strong><br>
                            {{ $h->keterangan ?? 'Update status' }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logika Hapus Foto via AJAX
        document.querySelectorAll('.btn-hapus-foto').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const fotoId    = this.dataset.fotoId;
                const fotoJenis = this.dataset.fotoJenis;

                if (!confirm('Yakin hapus foto ini?')) return;

                const originalHtml = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.8rem;height:.8rem;"></span>';

                const urlPrefix = '{{ $rp === "teknisi_cctv" ? "teknisi-cctv" : "teknisi" }}';
                const url = '/' + urlPrefix + '/photo/' + fotoId;

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const wrapper = document.querySelector(`.foto-item[data-foto-id="${fotoId}"]`);
                        if (wrapper) wrapper.remove();

                        const container = fotoJenis === 'sebelum'
                            ? document.getElementById('wrapperFotoSebelum')
                            : document.getElementById('wrapperFotoSesudah');

                        if (container && container.querySelectorAll('.foto-item').length === 0) {
                            container.innerHTML = '<div style="font-size:0.8rem; color:#888; padding:10px;">Semua foto sudah dihapus</div>';
                        }

                        tampilkanToast('✅ Foto berhasil dihapus', 'success');
                    } else {
                        tampilkanToast('❌ ' + (data.message || 'Gagal hapus foto'), 'danger');
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                })
                .catch(err => {
                    console.error(err);
                    tampilkanToast('❌ Gagal hapus foto', 'danger');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                });
            });
        });

        function tampilkanToast(msg, type) {
            const toast = document.createElement('div');
            // Menggunakan style PWA untuk toast
            toast.style.cssText = `
                position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
                background: ${type === 'success' ? '#198754' : '#dc3545'};
                color: white; padding: 10px 20px; border-radius: 30px;
                font-size: 0.85rem; font-weight: bold; z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15); text-align: center;
            `;
            toast.innerHTML = msg;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }
    });
</script>
@endpush