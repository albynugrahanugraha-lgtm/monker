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
    .list-item { background: white; border-radius: 12px; padding: 12px 16px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.03); }
    .list-item .tgl { font-weight: 800; font-size: 0.9rem; }
    .list-item .jam { font-size: 0.8rem; color: #555; margin-top: 4px; }
    .badge { padding: 4px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .bg-hadir { background: #d4edda; color: #155724; }
    .bg-terlambat { background: #fff3cd; color: #856404; }
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
        <div class="list-item">
            <div>
                <div class="tgl">{{ \Carbon\Carbon::parse($r->tanggal)->translatedFormat('d M Y') }}</div>
                <div class="jam">Masuk: {{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '-' }} | Pulang: {{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '-' }}</div>
            </div>
            <div><span class="badge bg-{{ $r->status }}">{{ strtoupper($r->status) }}</span></div>
        </div>
    @empty
        <div style="text-align:center; padding:20px; color:#888; background:white; border-radius:12px;">Tidak ada data absensi bulan ini.</div>
    @endforelse
</div>
@endsection