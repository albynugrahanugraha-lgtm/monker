<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>
        Laporan
        @if(request('bidang') === 'cctv') CCTV
        @elseif(request('bidang') === 'komputer') Komputer
        @else Semua Bidang
        @endif
        — {{ $daftarBulan[$bulan] }} {{ $tahun }}
    </title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15px; color: #000; }
        h1 { font-size: 16px; text-align: center; margin: 0 0 4px 0; }
        h2 { font-size: 13px; text-align: center; margin: 0 0 6px 0; font-weight: normal; }
        h3 { font-size: 12px; margin: 15px 0 6px 0; border-bottom: 1px solid #000; padding-bottom: 3px; }

        /* 🆕 Bidang banner */
        .bidang-banner {
            text-align: center;
            padding: 6px 15px;
            margin: 0 auto 15px auto;
            display: inline-block;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .bidang-banner.komputer { background: #0d6efd; color: white; }
        .bidang-banner.cctv { background: #0dcaf0; color: #000; }
        .bidang-banner.semua { background: #6c757d; color: white; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        .text-center { text-align: center; }

        .stat-box { display: inline-block; border: 1px solid #333; padding: 6px 15px; margin: 3px; border-radius: 4px; font-size: 11px; }

        .chart-box {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 12px;
            background: #fafafa;
            page-break-inside: avoid;
        }

        .job-block { border: 1px solid #333; padding: 8px; margin-bottom: 12px; page-break-inside: avoid; }
        .job-block .job-header { font-weight: bold; font-size: 12px; margin-bottom: 6px; }
        .job-block table { margin: 0 0 8px 0; }
        .job-block th { background: #eaeaea; width: 25%; }

        /* Highlight tanggal */
        .tanggal-buat th { background: #fff3cd; }
        .tanggal-buat td { background: #fffde7; }
        .tanggal-selesai th { background: #d1e7dd; }
        .tanggal-selesai td { background: #e8f5e9; }

        .badge-cctv { background: #0dcaf0; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 6px; }
        .badge-komputer { background: #0d6efd; color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 10px; margin-left: 6px; }

        .foto-section { display: flex; gap: 15px; margin-top: 6px; }
        .foto-box { flex: 1; }
        .foto-label { font-size: 10px; font-weight: bold; margin-bottom: 4px; background: #eaeaea; padding: 3px 6px; border: 1px solid #999; }
        .foto-list { display: flex; flex-wrap: wrap; gap: 8px; }

        .foto-item { border: 1px solid #333; border-radius: 4px; overflow: hidden; background: white; }
        .foto-item img { width: 110px; height: 110px; object-fit: cover; display: block; cursor: pointer; }
        .foto-actions { display: flex; border-top: 1px solid #ccc; font-size: 9px; }
        .foto-actions a { flex: 1; text-align: center; padding: 3px 0; text-decoration: none; color: #0d6efd; font-weight: 600; background: #f8f9fa; }
        .foto-actions a:first-child { border-right: 1px solid #ccc; }
        .foto-empty { font-size: 10px; font-style: italic; color: #888; border: 1px dashed #ccc; padding: 10px; text-align: center; width: 110px; }

        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { width: 40%; text-align: center; }
        .ttd p { margin: 60px 0 0 0; border-top: 1px solid #000; padding-top: 5px; }

        .no-print { text-align: center; margin: 25px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .btn-print { padding: 12px 25px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; margin: 0 5px; }
        .btn-secondary { background: #6c757d; }

        #fotoModal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center; padding: 20px; }
        #fotoModal img { max-width: 95%; max-height: 90%; border: 3px solid white; border-radius: 8px; }
        #fotoModal .close-btn { position: absolute; top: 15px; right: 25px; color: white; font-size: 40px; font-weight: bold; cursor: pointer; background: rgba(0,0,0,0.5); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        #fotoModal .download-btn { position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); padding: 12px 24px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: bold; cursor: pointer; text-decoration: none; }

        @media print {
            .no-print { display: none !important; }
            .foto-actions { display: none !important; }
            #fotoModal { display: none !important; }
            body { margin: 0; }
            @page { size: A4; margin: 15mm 10mm; }
            .foto-item img { width: 130px; height: 130px; }

            /* Paksa background tercetak */
            .bidang-banner,
            .tanggal-buat th, .tanggal-buat td,
            .tanggal-selesai th, .tanggal-selesai td,
            .badge-cctv, .badge-komputer {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

@php
    $isCctvOnly     = request('bidang') === 'cctv';
    $isKomputerOnly = request('bidang') === 'komputer';
    $bidangTitle    = $isCctvOnly ? 'CCTV' : ($isKomputerOnly ? 'KOMPUTER' : 'SEMUA BIDANG');
@endphp

<h1>LAPORAN BULANAN PEKERJAAN TEKNISI</h1>
<h2>Periode: {{ $daftarBulan[$bulan] }} {{ $tahun }}</h2>

{{-- 🆕 BANNER BIDANG --}}
<div style="text-align:center;">
    <div class="bidang-banner {{ $isCctvOnly ? 'cctv' : ($isKomputerOnly ? 'komputer' : 'semua') }}">
        @if($isCctvOnly)
            <i>📹</i> BIDANG: CCTV
        @elseif($isKomputerOnly)
            <i>🖥</i> BIDANG: KOMPUTER
        @else
            <i>📋</i> SEMUA BIDANG (KOMPUTER + CCTV)
        @endif
    </div>
</div>

@if($user->isAnyTeknisi())
    <p style="text-align: center; font-style: italic; margin-bottom: 15px;">
        Laporan untuk teknisi: <strong>{{ $user->display_name }}</strong>
        (Bidang: {{ $user->bidangLabel() }})
    </p>
@endif

{{-- A. STATISTIK --}}
<h3>A. STATISTIK</h3>
<div>
    <span class="stat-box">Total: <strong>{{ $stats['total'] }}</strong></span>
    <span class="stat-box">Ditugaskan: <strong>{{ $stats['ditugaskan'] }}</strong></span>
    <span class="stat-box">Dikerjakan: <strong>{{ $stats['dikerjakan'] }}</strong></span>
    <span class="stat-box">Selesai: <strong>{{ $stats['selesai'] }}</strong></span>
</div>

{{-- B. GRAFIK PER HARI --}}
<h3>B. GRAFIK PEKERJAAN PER HARI — {{ strtoupper($daftarBulan[$bulan]) }} {{ $tahun }}</h3>
<div class="chart-box">
    @php
        $maxData = max($chartHarian['data']) ?: 1;
        $chartHeight = 180;
        $chartWidth = 750;
        $padL = 30; $padR = 15; $padT = 15; $padB = 30;
        $plotW = $chartWidth - $padL - $padR;
        $plotH = $chartHeight - $padT - $padB;
        $jumlahHari = count($chartHarian['labels']);
        $barW = $plotW / $jumlahHari;
    @endphp

    <svg width="100%" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" xmlns="http://www.w3.org/2000/svg">
        @for($g = 0; $g <= 4; $g++)
            @php
                $y = $padT + ($plotH / 4) * $g;
                $labelVal = round($maxData - ($maxData / 4) * $g);
            @endphp
            <line x1="{{ $padL }}" y1="{{ $y }}" x2="{{ $chartWidth - $padR }}" y2="{{ $y }}" stroke="#ddd" stroke-width="0.5"/>
            <text x="{{ $padL - 5 }}" y="{{ $y + 3 }}" font-size="9" text-anchor="end" fill="#666">{{ $labelVal }}</text>
        @endfor

        @for($d = 0; $d < $jumlahHari; $d++)
            @php
                $val = $chartHarian['data'][$d] ?? 0;
                $barHeight = $maxData > 0 ? ($val / $maxData) * $plotH : 0;
                $x = $padL + $d * $barW;
                $y = $padT + $plotH - $barHeight;
                $color = $val > 0 ? ($isCctvOnly ? '#0dcaf0' : '#0d6efd') : '#e9ecef';
            @endphp
            <rect x="{{ $x + 1 }}" y="{{ $y }}" width="{{ max($barW - 2, 3) }}" height="{{ max($barHeight, 0.5) }}" fill="{{ $color }}" stroke="{{ $isCctvOnly ? '#0dcaf0' : '#0d6efd' }}" stroke-width="0.3"/>

            @if($val > 0)
                <text x="{{ $x + $barW / 2 }}" y="{{ $y - 3 }}" font-size="8" text-anchor="middle" fill="{{ $isCctvOnly ? '#0aa2c0' : '#0d6efd' }}" font-weight="bold">{{ $val }}</text>
            @endif

            @if($jumlahHari <= 15 || $d % 2 == 0)
                <text x="{{ $x + $barW / 2 }}" y="{{ $chartHeight - 10 }}" font-size="8" text-anchor="middle" fill="#666">{{ $chartHarian['labels'][$d] }}</text>
            @endif
        @endfor
    </svg>

    <div style="text-align: center; margin-top: 6px; font-size: 10px;">
        <span style="display:inline-block; width:12px; height:12px; background:{{ $isCctvOnly ? '#0dcaf0' : '#0d6efd' }}; border-radius:2px; vertical-align:middle;"></span>
        Total Pekerjaan per Hari
    </div>
</div>

{{-- C. REKAP PER TEKNISI --}}
@if($rekapTeknisi->count() > 0)
<h3>C. REKAP PER TEKNISI</h3>
<table>
    <thead>
        <tr>
            <th style="width: 50px;" class="text-center">No</th>
            <th>Nama Teknisi</th>
            <th style="width: 120px;" class="text-center">Total Tugas</th>
            <th style="width: 120px;" class="text-center">Selesai</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rekapTeknisi as $i => $r)
        <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $r->teknisi->display_name ?? '-' }}</td>
            <td class="text-center">{{ $r->total }}</td>
            <td class="text-center">{{ $r->selesai }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif

{{-- D. DETAIL PEKERJAAN --}}
<h3>D. DETAIL PEKERJAAN @if(request('bidang')) ({{ strtoupper($bidangTitle) }}) @endif</h3>

@forelse($jobs as $i => $j)
    <div class="job-block">
        <div class="job-header">
            #{{ $i + 1 }} — {{ $j->kode_job }} — {{ $j->judul }}
            <span class="{{ $j->bidang === 'cctv' ? 'badge-cctv' : 'badge-komputer' }}">
                {{ $j->bidangLabel() }}
            </span>
            <span style="float: right;">Status: <strong>{{ $j->status }}</strong></span>
        </div>

        <table>
            <tr><th>Teknisi</th><td>{{ $j->teknisi->display_name ?? '-' }}</td></tr>

            <tr class="tanggal-buat">
                <th>📅 Tanggal Buat Tugas</th>
                <td>
                    <strong>{{ $j->created_at?->format('d-m-Y H:i') ?? '-' }}</strong>
                    <br>
                    <small style="color: #666;">
                        Dibuat oleh: {{ $j->admin->display_name ?? 'Teknisi sendiri' }}
                    </small>
                </td>
            </tr>

            <tr class="tanggal-selesai">
                <th>✅ Tanggal Selesai</th>
                <td>
                    @if($j->tanggal_selesai)
                        <strong>{{ $j->tanggal_selesai->format('d-m-Y H:i') }}</strong>
                        @php
                            $diff = $j->created_at->diff($j->tanggal_selesai);
                            $parts = [];
                            if ($diff->d > 0) $parts[] = $diff->d . ' hari';
                            if ($diff->h > 0) $parts[] = $diff->h . ' jam';
                            if ($diff->i > 0 && $diff->d == 0) $parts[] = $diff->i . ' menit';
                            $durasiText = implode(' ', $parts) ?: 'kurang dari 1 menit';
                        @endphp
                        <br>
                        <small style="color: #666;">⏱️ Durasi: {{ $durasiText }}</small>
                    @else
                        <em style="color: #999;">Belum selesai</em>
                    @endif
                </td>
            </tr>

            <tr><th>Tanggal Tugas</th><td>{{ $j->tanggal_tugas?->format('d-m-Y') }}</td></tr>
            <tr><th>Lokasi</th><td>{{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}</td></tr>
            <tr><th>Perangkat</th><td>{{ $j->computer_name ?? '-' }} ({{ $j->merk ?? '-' }})</td></tr>
            <tr><th>Prioritas</th><td>{{ $j->prioritas }}</td></tr>
            <tr><th>Hasil Pekerjaan</th><td>{{ $j->hasil_pekerjaan ?? '-' }}</td></tr>
            <tr><th>Catatan</th><td>{{ $j->catatan ?? '-' }}</td></tr>
        </table>

        <div class="foto-section">
            <div class="foto-box">
                <div class="foto-label">📷 Foto Sebelum</div>
                <div class="foto-list">
                    @if($j->fotoSebelum->count() > 0)
                        @foreach($j->fotoSebelum as $f)
                            <div class="foto-item">
                                <img src="{{ asset($f->nama_file) }}" onclick="openFoto('{{ asset($f->nama_file) }}')">
                                <div class="foto-actions">
                                    <a href="{{ asset($f->nama_file) }}" target="_blank">👁 Lihat</a>
                                    <a href="{{ asset($f->nama_file) }}" download>⬇ Download</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="foto-empty">Tidak ada foto</div>
                    @endif
                </div>
            </div>

            <div class="foto-box">
                <div class="foto-label">📷 Foto Sesudah</div>
                <div class="foto-list">
                    @if($j->fotoSesudah->count() > 0)
                        @foreach($j->fotoSesudah as $f)
                            <div class="foto-item">
                                <img src="{{ asset($f->nama_file) }}" onclick="openFoto('{{ asset($f->nama_file) }}')">
                                <div class="foto-actions">
                                    <a href="{{ asset($f->nama_file) }}" target="_blank">👁 Lihat</a>
                                    <a href="{{ asset($f->nama_file) }}" download>⬇ Download</a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="foto-empty">Tidak ada foto</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <p style="text-align: center; font-style: italic;">Tidak ada pekerjaan pada periode ini</p>
@endforelse

<div class="ttd">
    <div><p>Mengetahui,</p></div>
    <div><p>Petugas,</p></div>
</div>

<div class="no-print">
    <p style="margin-bottom: 10px;">
        <strong>💡 Tips:</strong> Klik tombol di bawah, lalu pilih <strong>"Save as PDF"</strong>.
        <br>
        <small>Centang <strong>"Background graphics"</strong> di print dialog supaya warna tanggal tercetak.</small>
    </p>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Save as PDF</button>
    <button class="btn-print btn-secondary" onclick="window.close()">❌ Tutup</button>
</div>

<div id="fotoModal" onclick="if(event.target.id === 'fotoModal') closeFoto()">
    <span class="close-btn" onclick="closeFoto()">&times;</span>
    <img id="fotoModalImg" src="" alt="Preview">
    <a id="fotoModalDownload" class="download-btn" href="" download>⬇ Download Foto Ini</a>
</div>

<script>
    function openFoto(src) {
        document.getElementById('fotoModalImg').src = src;
        document.getElementById('fotoModalDownload').href = src;
        document.getElementById('fotoModal').style.display = 'flex';
    }
    function closeFoto() {
        document.getElementById('fotoModal').style.display = 'none';
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeFoto();
    });
</script>

</body>
</html>