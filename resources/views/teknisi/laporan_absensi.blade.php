@extends('layouts.app_pwa')
@section('title', 'Laporan Absensi')

@php
    $isCctv = auth()->user()->isTeknisiCctv();
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
@endphp

@push('styles')
<style>
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }
    .header-back { display: flex; align-items: center; margin-bottom: 20px; }
    .header-back a { color: #333; font-size: 1.5rem; margin-right: 12px; text-decoration: none; }
    .filter-box { background: white; border-radius: 16px; padding: 16px; margin-bottom: 16px; display: flex; gap: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
    .filter-box select { padding: 8px; border-radius: 8px; border: 1px solid #ddd; flex: 1; font-weight: bold; }
    .filter-box button { background: {{ $primaryColor }}; color: white; border: none; border-radius: 8px; padding: 0 16px; font-weight: bold; }
    
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
    .stat-card { background: white; border-radius: 12px; padding: 12px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
    .stat-card .val { font-size: 1.2rem; font-weight: 800; color: {{ $primaryColor }}; }
    .stat-card .lbl { font-size: 0.7rem; color: #888; font-weight: bold; }

    /* 🆕 TAMPILAN KARTU */
    .card-absen { background: white; border-radius: 16px; padding: 16px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); }
    .card-header-absen { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #eee; padding-bottom: 12px; margin-bottom: 12px; }
    .card-tanggal { font-weight: 800; font-size: 1rem; color: #1a1a1a; }
    
    .badge { padding: 5px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
    .bg-hadir { background: #d4edda; color: #155724; }
    .bg-terlambat { background: #fff3cd; color: #856404; }
    
    /* 🆕 AREA FOTO & JAM */
    .foto-wrapper { display: flex; justify-content: space-between; gap: 10px; }
    .foto-box { flex: 1; text-align: center; }
    .foto-img { width: 100%; height: 100px; object-fit: cover; border-radius: 10px; border: 2px solid #eee; cursor: pointer; }
    .foto-label { font-size: 0.7rem; font-weight: bold; margin-bottom: 4px; display: block; }
    .jam-text { font-size: 1.1rem; font-weight: 900; font-variant-numeric: tabular-nums; margin-top: 6px; }
    
    .warna-masuk { color: #198754; }
    .warna-pulang { color: #dc3545; }
</style>
@endpush

@section('content')
<div class="app-container">
    <div class="header-back">
        <a href="{{ route(auth()->user()->isTeknisiCctv() ? 'teknisi_cctv.dashboard' : 'teknisi.dashboard') }}"><i class="bi bi-arrow-left-circle-fill"></i></a>
        <div><div class="title" style="font-size:1.1rem; font-weight:800;">Laporan Absensi</div><div class="subtitle" style="font-size:0.8rem; color:#888;">Rekap Kehadiran Anda</div></div>
    </div>

    <form method="GET" class="filter-box">
        <select name="bulan">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
            @endfor
        </select>
        <select name="tahun">
            @for($i = now()->year; $i >= now()->year - 2; $i--)
                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit">Cari</button>
    </form>

    <div class="stat-grid">
        <div class="stat-card"><div class="val">{{ $statistik['hadir'] }}</div><div class="lbl">Hadir</div></div>
        <div class="stat-card"><div class="val" style="color:#ffc107;">{{ $statistik['terlambat'] }}</div><div class="lbl">Terlambat</div></div>
        <div class="stat-card"><div class="val">{{ floor($statistik['total_menit'] / 60) }}j</div><div class="lbl">Total Jam</div></div>
    </div>

    @forelse($rekap as $r)
        <div class="card-absen">
            <div class="card-header-absen">
                <div class="card-tanggal">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('l, d M Y') }}</div>
                <div><span class="badge bg-{{ $r->status }}">{{ strtoupper($r->status) }}</span></div>
            </div>

            <div class="foto-wrapper">
                {{-- Sisi Masuk --}}
                <div class="foto-box">
                    <span class="foto-label warna-masuk"><i class="bi bi-box-arrow-in-right"></i> MASUK</span>
                    @if(isset($r->foto_masuk) && $r->foto_masuk)
                        <img src="{{ asset($r->foto_masuk) }}" class="foto-img" onclick="window.open(this.src, '_blank')" alt="Foto Masuk">
                    @else
                        <div class="foto-img" style="display:flex; align-items:center; justify-content:center; background:#f8f9fa; font-size:0.7rem; color:#aaa;">Tanpa Foto</div>
                    @endif
                    <div class="jam-text warna-masuk">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '--:--' }}</div>
                </div>

                {{-- Sisi Pulang --}}
                <div class="foto-box">
                    <span class="foto-label warna-pulang"><i class="bi bi-box-arrow-right"></i> PULANG</span>
                    @if(isset($r->foto_pulang) && $r->foto_pulang)
                        <img src="{{ asset($r->foto_pulang) }}" class="foto-img" onclick="window.open(this.src, '_blank')" alt="Foto Pulang">
                    @else
                        <div class="foto-img" style="display:flex; align-items:center; justify-content:center; background:#f8f9fa; font-size:0.7rem; color:#aaa;">Tanpa Foto</div>
                    @endif
                    <div class="jam-text warna-pulang">{{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '--:--' }}</div>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:20px; color:#888; background:white; border-radius:12px;">Tidak ada data absensi bulan ini.</div>
    @endforelse
</div>
@endsection