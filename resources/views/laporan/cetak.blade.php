<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $daftarBulan[$bulan] }} {{ $tahun }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 15px; color: #000; }
        h1 { font-size: 16px; text-align: center; margin: 0 0 4px 0; }
        h2 { font-size: 13px; text-align: center; margin: 0 0 15px 0; font-weight: normal; }
        h3 { font-size: 12px; margin: 15px 0 6px 0; border-bottom: 1px solid #000; padding-bottom: 3px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background: #f0f0f0; }
        .text-center { text-align: center; }

        .stat-box { display: inline-block; border: 1px solid #333; padding: 6px 15px; margin: 3px; border-radius: 4px; font-size: 11px; }

        /* Grafik */
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
        .job-block th { background: #eaeaea; width: 22%; }

        /* ===== FOTO ===== */
        .foto-section { display: flex; gap: 15px; margin-top: 6px; }
        .foto-box { flex: 1; }
        .foto-label { font-size: 10px; font-weight: bold; margin-bottom: 4px; background: #eaeaea; padding: 3px 6px; border: 1px solid #999; }
        .foto-list { display: flex; flex-wrap: wrap; gap: 8px; }

        .foto-item {
            position: relative;
            border: 1px solid #333;
            border-radius: 4px;
            overflow: hidden;
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .foto-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .foto-item img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            display: block;
            cursor: pointer;
        }
        .foto-actions {
            display: flex;
            border-top: 1px solid #ccc;
            font-size: 9px;
        }
        .foto-actions a {
            flex: 1;
            text-align: center;
            padding: 3px 0;
            text-decoration: none;
            color: #0d6efd;
            font-weight: 600;
            background: #f8f9fa;
            transition: background 0.2s;
        }
        .foto-actions a:hover {
            background: #e9ecef;
        }
        .foto-actions a:first-child {
            border-right: 1px solid #ccc;
        }
        .foto-empty {
            font-size: 10px;
            font-style: italic;
            color: #888;
            border: 1px dashed #ccc;
            padding: 10px;
            text-align: center;
            width: 110px;
        }

        .ttd { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd div { width: 40%; text-align: center; }
        .ttd p { margin: 60px 0 0 0; border-top: 1px solid #000; padding-top: 5px; }

        .no-print { text-align: center; margin: 25px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .btn-print { padding: 12px 25px; background: #0d6efd; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; margin: 0 5px; }
        .btn-print:hover { background: #0b5ed7; }
        .btn-secondary { background: #6c757d; }

        /* ===== MODAL PREVIEW FOTO ===== */
        #fotoModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        #fotoModal img {
            max-width: 95%;
            max-height: 90%;
            border: 3px solid white;
            border-radius: 8px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }
        #fotoModal .close-btn {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }
        #fotoModal .download-btn {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            padding: 12px 24px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }
        #fotoModal .download-btn:hover {
            background: #0b5ed7;
        }

        @media print {
            .no-print { display: none !important; }
            .foto-actions { display: none !important; }
            #fotoModal { display: none !important; }
            body { margin: 0; }
            @page { size: A4; margin: 15mm 10mm; }
            .foto-item img {
                width: 130px;
                height: 130px;
            }
        }
    </style>
</head>
<body>

<h1>LAPORAN BULANAN PEKERJAAN TEKNISI</h1>
<h2>Periode: {{ $daftarBulan[$bulan] }} {{ $tahun }}</h2>

@if($user->isTeknisi())
    <p style="text-align: center; font-style: italic; margin-bottom: 15px;">
        Laporan untuk teknisi: <strong>{{ $user->display_name }}</strong>
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
        $padL = 30;
        $padR = 15;
        $padT = 15;
        $padB = 30;
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
            <line x1="{{ $padL }}" y1="{{ $y }}" x2="{{ $chartWidth - $padR }}" y2="{{ $y }}"
                  stroke="#ddd" stroke-width="0.5"/>
            <text x="{{ $padL - 5 }}" y="{{ $y + 3 }}" font-size="9" text-anchor="end" fill="#666">{{ $labelVal }}</text>
        @endfor

        @for($d = 0; $d < $jumlahHari; $d++)
            @php
                $val = $chartHarian['data'][$d] ?? 0;
                $valSelesai = $chartHarian['selesai'][$d] ?? 0;
                $barHeight = $maxData > 0 ? ($val / $maxData) * $plotH : 0;
                $x = $padL + $d * $barW;
                $y = $padT + $plotH - $barHeight;
                $color = $val > 0 ? '#0d6efd' : '#e9ecef';
            @endphp
            <rect x="{{ $x + 1 }}" y="{{ $y }}" width="{{ max($barW - 2, 3) }}" height="{{ max($barHeight, 0.5) }}"
                  fill="{{ $color }}" stroke="#0d6efd" stroke-width="0.3"/>

            @if($val > 0)
                <text x="{{ $x + $barW / 2 }}" y="{{ $y - 3 }}" font-size="8" text-anchor="middle"
                      fill="#0d6efd" font-weight="bold">{{ $val }}</text>
            @endif

            @if($jumlahHari <= 15 || $d % 2 == 0)
                <text x="{{ $x + $barW / 2 }}" y="{{ $chartHeight - 10 }}" font-size="8" text-anchor="middle" fill="#666">
                    {{ $chartHarian['labels'][$d] }}
                </text>
            @endif
        @endfor
    </svg>

    <div style="text-align: center; margin-top: 6px; font-size: 10px;">
        <span style="display:inline-block; width:12px; height:12px; background:#0d6efd; border-radius:2px; vertical-align:middle;"></span>
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
<h3>D. DETAIL PEKERJAAN</h3>

@forelse($jobs as $i => $j)
    <div class="job-block">
        <div class="job-header">
            #{{ $i + 1 }} — {{ $j->kode_job }} — {{ $j->judul }}
            <span style="float: right;">Status: <strong>{{ $j->status }}</strong></span>
        </div>

        <table>
            <tr><th>Teknisi</th><td>{{ $j->teknisi->display_name ?? '-' }}</td></tr>
            <tr><th>Tanggal</th><td>{{ $j->tanggal_tugas?->format('d-m-Y') }}</td></tr>
            <tr><th>Lokasi</th><td>{{ $j->lok_gedung ?? '-' }} / {{ $j->lokasi_ruang ?? '-' }}</td></tr>
            <tr><th>Perangkat</th><td>{{ $j->computer_name ?? '-' }} ({{ $j->merk ?? '-' }})</td></tr>
            <tr><th>Prioritas</th><td>{{ $j->prioritas }}</td></tr>
            <tr><th>Hasil Pekerjaan</th><td>{{ $j->hasil_pekerjaan ?? '-' }}</td></tr>
            <tr><th>Catatan</th><td>{{ $j->catatan ?? '-' }}</td></tr>
        </table>

        <div class="foto-section">
            {{-- FOTO SEBELUM --}}
            <div class="foto-box">
                <div class="foto-label">📷 Foto Sebelum</div>
                <div class="foto-list">
                    @if($j->fotoSebelum->count() > 0)
                        @foreach($j->fotoSebelum as $f)
                            <div class="foto-item">
                                <img src="{{ asset($f->nama_file) }}"
                                     alt="Foto Sebelum"
                                     onclick="openFoto('{{ asset($f->nama_file) }}')">
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

            {{-- FOTO SESUDAH --}}
            <div class="foto-box">
                <div class="foto-label">📷 Foto Sesudah</div>
                <div class="foto-list">
                    @if($j->fotoSesudah->count() > 0)
                        @foreach($j->fotoSesudah as $f)
                            <div class="foto-item">
                                <img src="{{ asset($f->nama_file) }}"
                                     alt="Foto Sesudah"
                                     onclick="openFoto('{{ asset($f->nama_file) }}')">
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
    </p>
    <button class="btn-print" onclick="window.print()">🖨️ Cetak / Save as PDF</button>
    <button class="btn-print btn-secondary" onclick="window.close()">❌ Tutup</button>
</div>

{{-- MODAL PREVIEW FOTO --}}
<div id="fotoModal" onclick="if(event.target.id === 'fotoModal') closeFoto()">
    <span class="close-btn" onclick="closeFoto()">&times;</span>
    <img id="fotoModalImg" src="" alt="Preview">
    <a id="fotoModalDownload" class="download-btn" href="" download>
        ⬇ Download Foto Ini
    </a>
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

    // ESC untuk tutup
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFoto();
        }
    });
</script>

</body>
</html>