<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📺 Monitor Pekerjaan Teknisi — Live</title>

    {{-- ⚡ SCRIPT ANTI-FLASH --}}
    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'light';
            let theme = savedTheme;
            if (savedTheme === 'auto') {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        /* ==========================================
           FIT TV SCREEN — NO SCROLL
           ========================================== */
        html, body {
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        body {
            background: #eef1f5;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: .8rem;
            transition: background-color 0.3s;
        }

        .monitor-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ==========================================
           HEADER
           ========================================== */
        .monitor-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 4px 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            flex-shrink: 0;
            height: 40px;
        }
        .monitor-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: .85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .monitor-header .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.2);
            padding: 1px 7px;
            border-radius: 20px;
            font-size: .55rem;
            margin-left: 5px;
        }
        .live-dot {
            width: 5px; height: 5px;
            background: #43e97b;
            border-radius: 50%;
            animation: livePulse 1.5s infinite;
            box-shadow: 0 0 8px #43e97b;
        }
        @keyframes livePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }
        .clock {
            font-family: 'Courier New', monospace;
            font-size: .72rem;
            font-weight: bold;
            background: rgba(0,0,0,0.2);
            padding: 2px 8px;
            border-radius: 5px;
        }
        .btn-login {
            background: white;
            color: #0d6efd;
            border: none;
            padding: 2px 9px;
            border-radius: 5px;
            font-weight: 600;
            font-size: .65rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            transition: all 0.3s;
        }
        .btn-login:hover { background: #43e97b; color: white; }

        #audioBtn {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 2px 9px;
            border-radius: 5px;
            font-weight: 600;
            font-size: .65rem;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            cursor: pointer;
        }
        #audioBtn.active { background: #43e97b; }

        .theme-toggle {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            color: white;
            padding: 2px 8px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            line-height: 1;
        }
        .theme-toggle:hover { background: rgba(255,255,255,0.3); }
        .theme-toggle .icon-sun,
        .theme-toggle .icon-moon { display: none; }
        html[data-bs-theme="light"] .theme-toggle .icon-moon { display: inline-block; }
        html[data-bs-theme="dark"] .theme-toggle .icon-sun { display: inline-block; }

        /* ==========================================
           MAIN CONTENT — 2 KOLOM
           ========================================== */
        .monitor-content {
            flex: 1;
            padding: 6px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            overflow: hidden;
            min-height: 0;
        }

        /* ==========================================
           KOLOM BIDANG
           ========================================== */
        .kolom-bidang {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            display: flex;
            flex-direction: column;
            min-height: 0;
            transition: background-color 0.3s;
        }

        .kolom-header {
            padding: 4px 12px;
            font-weight: 700;
            font-size: .73rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            flex-shrink: 0;
        }
        .kolom-header.komputer {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        .kolom-header.cctv {
            background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);
            color: #000;
        }
        .kolom-header .count-badge {
            background: rgba(255,255,255,.3);
            padding: 1px 8px;
            border-radius: 8px;
            font-size: .6rem;
            font-weight: 700;
        }
        .kolom-header.cctv .count-badge { background: rgba(0,0,0,.15); color: #000; }

        /* ==========================================
           MINI STATS
           ========================================== */
        .mini-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
            padding: 3px 6px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
        }
        .mini-stat {
            text-align: center;
            padding: 2px 2px;
            border-radius: 4px;
            background: white;
            border: 1px solid #dee2e6;
        }
        .mini-stat small {
            display: block;
            font-size: .5rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .2px;
            line-height: 1.1;
        }
        .mini-stat .val {
            font-size: .82rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .mini-stat.blue { border-left: 3px solid #0d6efd; }
        .mini-stat.blue .val { color: #0d6efd; }
        .mini-stat.green { border-left: 3px solid #198754; }
        .mini-stat.green .val { color: #198754; }
        .mini-stat.red { border-left: 3px solid #dc3545; }
        .mini-stat.red .val { color: #dc3545; }

        /* ==========================================
           SUB-SECTION: HARI INI / KEMARIN (DROPDOWN) / CHART
           ========================================== */
        .sub-section {
            display: flex;
            flex-direction: column;
            min-height: 0;
            transition: border-color 0.3s;
        }
        .sub-section.hari-ini {
            flex: 1; /* Akan memenuhi sisa ruang saat Kemarin tertutup */
            border-bottom: 1px solid #e9ecef;
        }
        .sub-section.chart-mini {
            flex: none; 
            height: 130px; /* Tinggi chart fix */
            border-top: 1px solid #e9ecef;
        }
        .sub-header {
            padding: 4px 10px;
            font-weight: 600;
            font-size: .65rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f3f5;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
        }
        
        /* ==========================================
           DROPDOWN KEMARIN
           ========================================== */
        .sub-section.kemarin {
            flex: none;
            display: flex;
            flex-direction: column;
            min-height: 0;
            transition: all 0.3s ease;
            border-bottom: 1px solid #e9ecef;
        }
        /* Area tabel disembunyikan secara default */
        .sub-section.kemarin .table-scroll {
            display: none;
        }
        /* Jika class 'is-open' ditambahkan via JS, tabel muncul */
        .sub-section.kemarin.is-open {
            flex: 2.5; /* Saat dibuka, ambil 25-30% layar */
            min-height: 120px;
            max-height: 30vh;
            border-bottom: 1px solid #e9ecef;
        }
        .sub-section.kemarin.is-open .table-scroll {
            display: block;
        }
        /* Header Kemarin bisa diklik */
        .sub-section.kemarin .sub-header {
            cursor: pointer;
            user-select: none;
            background: #e2e6ea; /* Warna abu-abu agar terlihat seperti tombol */
            transition: background 0.2s;
        }
        .sub-section.kemarin .sub-header:hover {
            background: #d1d7dd;
        }
        .sub-section.kemarin .toggle-icon {
            transition: transform 0.3s ease;
        }
        .sub-section.kemarin.is-open .toggle-icon {
            transform: rotate(180deg); /* Panah ke atas saat buka */
        }

        /* ==========================================
           TABLE
           ========================================== */
        .table-scroll {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }
        .table-scroll::-webkit-scrollbar { width: 4px; }
        .table-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 2px; }

        table.monitor-table { 
            font-size: .6rem; 
            margin: 0; 
            width: 100%;
        }
        table.monitor-table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8f9fa;
            font-size: .52rem;
            padding: 2px 5px;
            white-space: nowrap;
            border-bottom: 1px solid #dee2e6;
            text-transform: uppercase;
            letter-spacing: .2px;
            font-weight: 700;
            color: #495057;
        }
        table.monitor-table tbody td {
            padding: 3px 5px;
            vertical-align: middle;
            line-height: 1.2;
            border-bottom: 1px solid #f0f0f0;
        }
        table.monitor-table tbody tr:hover {
            background: #f0f7ff;
        }

        /* Kode job */
        table.monitor-table .kode-job {
            font-family: 'Courier New', monospace;
            font-size: .55rem;
            background: #f1f3f5;
            padding: 1px 4px;
            border-radius: 3px;
            color: #495057;
            white-space: nowrap;
        }

        /* Judul */
        table.monitor-table .judul-job {
            font-weight: 600;
            font-size: .62rem;
            color: #212529;
            line-height: 1.15;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        /* Lokasi */
        table.monitor-table .lokasi-job {
            font-size: .52rem;
            color: #6c757d;
            line-height: 1.15;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Badge teknisi compact */
        .teknisi-badge {
            font-size: .5rem;
            padding: 1px 4px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Badge status compact */
        .status-badge {
            font-size: .5rem;
            padding: 1px 4px;
            display: inline-block;
            white-space: nowrap;
        }

        /* ==========================================
           KELAP-KELIP
           ========================================== */
        @keyframes blinkRowBg {
            0%   { background-color: #fff3cd; }
            50%  { background-color: #ffffff; }
            100% { background-color: #fff3cd; }
        }
        tr.row-belum,
        tr.row-belum td {
            animation: blinkRowBg 1s infinite ease-in-out !important;
            font-weight: 600 !important;
        }
        tr.row-belum { 
            border-left: 3px solid #dc3545 !important; 
        }

        @keyframes lampuPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
                background: #dc3545;
                box-shadow: 0 0 6px #dc3545;
            }
            50% {
                transform: scale(1.3);
                opacity: 0.6;
                background: #ffc107;
                box-shadow: 0 0 12px #ffc107;
            }
        }
        .lampu-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #dc3545;
            box-shadow: 0 0 6px #dc3545;
            animation: lampuPulse 0.8s infinite;
            margin-right: 3px;
            vertical-align: middle;
        }

        @keyframes blinkBadge {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.1); }
        }
        .badge-blinking {
            animation: blinkBadge 0.8s infinite;
            box-shadow: 0 0 6px rgba(220, 53, 69, 0.6);
        }

        /* ==========================================
           CHART MINI
           ========================================== */
        .chart-mini-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 4px;
        }
        .chart-mini-title {
            font-size: .55rem;
            font-weight: 700;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .periode-btn-group {
            display: inline-flex;
            gap: 1px;
            background: #e9ecef;
            padding: 1px;
            border-radius: 4px;
        }
        .periode-btn {
            border: none;
            background: transparent;
            color: #6c757d;
            font-size: .48rem;
            font-weight: 600;
            padding: 1px 5px;
            border-radius: 2px;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .periode-btn:hover {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        .periode-btn.active {
            background: #0d6efd;
            color: white;
        }
        .periode-btn.active.cctv-active {
            background: #0dcaf0;
            color: #000;
        }
        
        /* Container Canvas Chart */
        .chart-mini-container {
            position: relative;
            flex: 1;
            min-height: 0;
            padding: 2px 4px;
            height: 100px; 
        }

        /* ==========================================
           FOOTER
           ========================================== */
        .monitor-footer {
            text-align: center;
            font-size: .55rem;
            color: #6c757d;
            padding: 2px 0;
            flex-shrink: 0;
            height: 16px;
        }

        /* ==========================================
           RESPONSIVE
           ========================================== */
        @media (max-width: 900px) {
            html, body { height: auto; overflow: auto; }
            .monitor-wrapper { height: auto; }
            .monitor-content { 
                grid-template-columns: 1fr; 
                overflow: visible;
            }
            .sub-section.hari-ini,
            .sub-section.kemarin.is-open {
                flex: none;
            }
            .table-scroll { max-height: 300px; }
            .sub-section.chart-mini { 
                min-height: 150px; 
                max-height: 170px;
            }
            .chart-mini-container {
                height: 120px;
            }
        }

        /* ==========================================
           DARK MODE
           ========================================== */
        html[data-bs-theme="dark"] body {
            background: #0d1117 !important;
            color: #e6edf3;
        }
        html[data-bs-theme="dark"] .monitor-header {
            background: linear-gradient(135deg, #0a58ca 0%, #0d3a8f 100%);
        }
        html[data-bs-theme="dark"] .kolom-bidang {
            background: #161b22;
            border: 1px solid #30363d;
            box-shadow: 0 2px 10px rgba(0,0,0,0.4);
        }
        html[data-bs-theme="dark"] .mini-stats {
            background: #21262d;
            border-color: #30363d;
        }
        html[data-bs-theme="dark"] .mini-stat {
            background: #161b22;
            border-color: #30363d;
        }
        html[data-bs-theme="dark"] .mini-stat small {
            color: #8b949e;
        }
        html[data-bs-theme="dark"] .sub-header {
            background: #21262d;
            border-color: #30363d;
            color: #e6edf3;
        }
        html[data-bs-theme="dark"] .sub-section.kemarin .sub-header {
            background: #1c2128;
        }
        html[data-bs-theme="dark"] .sub-section.kemarin .sub-header:hover {
            background: #2d333b;
        }
        html[data-bs-theme="dark"] .sub-header .sub-count {
            background: #30363d;
            color: #8b949e;
        }
        html[data-bs-theme="dark"] table.monitor-table thead th {
            background: #21262d !important;
            color: #e6edf3 !important;
            border-color: #30363d !important;
        }
        html[data-bs-theme="dark"] table.monitor-table tbody td {
            color: #e6edf3;
            border-color: #30363d;
        }
        html[data-bs-theme="dark"] table.monitor-table tbody tr:hover {
            background: #1c2128;
        }
        html[data-bs-theme="dark"] .kode-job {
            background: #21262d !important;
            color: #79c0ff !important;
        }
        html[data-bs-theme="dark"] .judul-job {
            color: #e6edf3 !important;
        }
        html[data-bs-theme="dark"] .lokasi-job {
            color: #8b949e !important;
        }
        html[data-bs-theme="dark"] .chart-mini-title {
            color: #e6edf3;
        }
        html[data-bs-theme="dark"] .periode-btn-group {
            background: #21262d;
        }
        html[data-bs-theme="dark"] .periode-btn {
            color: #8b949e;
        }
        html[data-bs-theme="dark"] .periode-btn:hover {
            background: rgba(13, 110, 253, 0.2);
            color: #79c0ff;
        }
        html[data-bs-theme="dark"] .periode-btn.active {
            background: #0d6efd;
            color: white;
        }
        html[data-bs-theme="dark"] .periode-btn.active.cctv-active {
            background: #0dcaf0;
            color: #000;
        }
        html[data-bs-theme="dark"] .monitor-footer {
            color: #8b949e;
        }

        @keyframes blinkRowBgDark {
            0%   { background-color: #9e6a00; }
            50%  { background-color: #161b22; }
            100% { background-color: #9e6a00; }
        }
        html[data-bs-theme="dark"] tr.row-belum,
        html[data-bs-theme="dark"] tr.row-belum td {
            animation: blinkRowBgDark 1s infinite ease-in-out !important;
            color: #fff !important;
        }
    </style>
</head>
<body>

<div class="monitor-wrapper">

    {{-- HEADER --}}
    <div class="monitor-header">
        <div>
            <h4>
                <i class="bi bi-display"></i>
                Monitor Pekerjaan Teknisi
                <span class="live-indicator">
                    <span class="live-dot"></span> LIVE
                </span>
            </h4>
            <small style="opacity: 0.9; font-size: .55rem;">
                <i class="bi bi-clock-history"></i>
                Update: <span id="lastUpdate">-</span>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema">
                <i class="bi bi-sun-fill icon-sun"></i>
                <i class="bi bi-moon-stars-fill icon-moon"></i>
            </button>

            <button id="audioBtn" onclick="unlockAudio()">
                <i class="bi bi-volume-mute-fill"></i> Aktifkan Suara
            </button>
            <div class="clock" id="clock">--:--:--</div>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </div>
    </div>

    {{-- CONTENT — 2 KOLOM --}}
    <div class="monitor-content">

        {{-- ==========================================
             KOLOM 1: KOMPUTER
             ========================================== --}}
        <div class="kolom-bidang">
            <div class="kolom-header komputer">
                <span><i class="bi bi-pc-display"></i> PEKERJAAN KOMPUTER</span>
                <span class="count-badge" id="countKomputer">0</span>
            </div>

            <div class="mini-stats">
                <div class="mini-stat blue">
                    <small>Total</small>
                    <div class="val" id="kmptTotal">0</div>
                </div>
                <div class="mini-stat green">
                    <small>Selesai</small>
                    <div class="val" id="kmptSelesai">0</div>
                </div>
                <div class="mini-stat red">
                    <small>Belum</small>
                    <div class="val" id="kmptBelum">0</div>
                </div>
            </div>

            {{-- HARI INI --}}
            <div class="sub-section hari-ini">
                <div class="sub-header">
                    <span>
                        <i class="bi bi-calendar-day text-primary"></i>
                        Hari Ini — <span id="labelHariIniKmpt">-</span>
                    </span>
                    <span class="sub-count" id="countHariIniKmpt">0</span>
                </div>
                <div class="table-scroll">
                    <table class="monitor-table">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 52%;">Pekerjaan</th>
                                <th style="width: 18%;">Teknisi</th>
                                <th style="width: 12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableHariIniKomputer">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- KEMARIN (DROPDOWN) --}}
            <div class="sub-section kemarin" id="kemarinKomputer">
                <div class="sub-header" onclick="toggleKemarin('kemarinKomputer')">
                    <span>
                        <i class="bi bi-calendar-check text-secondary"></i>
                        Kemarin — <span id="labelKemarinKmpt">-</span>
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="sub-count" id="countKemarinKmpt">0</span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="monitor-table">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 52%;">Pekerjaan</th>
                                <th style="width: 18%;">Teknisi</th>
                                <th style="width: 12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableKemarinKomputer">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CHART MINI --}}
            <div class="sub-section chart-mini">
                <div class="sub-header">
                    <div class="chart-mini-header">
                        <div class="chart-mini-title">
                            <i class="bi bi-trophy-fill text-warning"></i>
                            <span>Top Teknisi</span>
                        </div>
                        <div class="periode-btn-group" data-target="komputer">
                            <button class="periode-btn" data-periode="harian">H</button>
                            <button class="periode-btn" data-periode="mingguan">M</button>
                            <button class="periode-btn active" data-periode="bulanan">B</button>
                            <button class="periode-btn" data-periode="semua">S</button>
                        </div>
                    </div>
                </div>
                <div class="chart-mini-container">
                    <canvas id="chartKomputer"></canvas>
                </div>
            </div>
        </div>

        {{-- ==========================================
             KOLOM 2: CCTV
             ========================================== --}}
        <div class="kolom-bidang">
            <div class="kolom-header cctv">
                <span><i class="bi bi-camera-video"></i> PEKERJAAN CCTV</span>
                <span class="count-badge" id="countCctv">0</span>
            </div>

            <div class="mini-stats">
                <div class="mini-stat blue">
                    <small>Total</small>
                    <div class="val" id="cctvTotal">0</div>
                </div>
                <div class="mini-stat green">
                    <small>Selesai</small>
                    <div class="val" id="cctvSelesai">0</div>
                </div>
                <div class="mini-stat red">
                    <small>Belum</small>
                    <div class="val" id="cctvBelum">0</div>
                </div>
            </div>

            {{-- HARI INI --}}
            <div class="sub-section hari-ini">
                <div class="sub-header">
                    <span>
                        <i class="bi bi-calendar-day text-info"></i>
                        Hari Ini — <span id="labelHariIniCctv">-</span>
                    </span>
                    <span class="sub-count" id="countHariIniCctv">0</span>
                </div>
                <div class="table-scroll">
                    <table class="monitor-table">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 52%;">Pekerjaan</th>
                                <th style="width: 18%;">Teknisi</th>
                                <th style="width: 12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableHariIniCctv">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- KEMARIN (DROPDOWN) --}}
            <div class="sub-section kemarin" id="kemarinCctv">
                <div class="sub-header" onclick="toggleKemarin('kemarinCctv')">
                    <span>
                        <i class="bi bi-calendar-check text-secondary"></i>
                        Kemarin — <span id="labelKemarinCctv">-</span>
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="sub-count" id="countKemarinCctv">0</span>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </div>
                </div>
                <div class="table-scroll">
                    <table class="monitor-table">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 52%;">Pekerjaan</th>
                                <th style="width: 18%;">Teknisi</th>
                                <th style="width: 12%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableKemarinCctv">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CHART MINI --}}
            <div class="sub-section chart-mini">
                <div class="sub-header">
                    <div class="chart-mini-header">
                        <div class="chart-mini-title">
                            <i class="bi bi-trophy-fill text-warning"></i>
                            <span>Top Teknisi</span>
                        </div>
                        <div class="periode-btn-group" data-target="cctv">
                            <button class="periode-btn cctv-active" data-periode="harian">H</button>
                            <button class="periode-btn cctv-active" data-periode="mingguan">M</button>
                            <button class="periode-btn cctv-active active" data-periode="bulanan">B</button>
                            <button class="periode-btn cctv-active" data-periode="semua">S</button>
                        </div>
                    </div>
                </div>
                <div class="chart-mini-container">
                    <canvas id="chartCctv"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="monitor-footer">
        <i class="bi bi-arrow-clockwise"></i>
        Auto-refresh 10 detik · <span id="refreshCounter">10</span> detik lagi
    </div>

</div>

{{-- Chart.js --}}
<script src="{{ asset('js/chart.umd.min.js') }}"></script>

<script>
    // ============================================================
    // TOGGLE DROPDOWN KEMARIN
    // ============================================================
    function toggleKemarin(elementId) {
        const section = document.getElementById(elementId);
        if (section) {
            section.classList.toggle('is-open');
        }
    }

    // ============================================================
    // THEME
    // ============================================================
    function applyTheme(theme) {
        let finalTheme = theme;
        if (theme === 'auto') {
            finalTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-bs-theme', finalTheme);
        localStorage.setItem('app_theme', theme);
        updateChartsTheme();
    }

    function toggleTheme() {
        const current = document.documentElement.getAttribute('data-bs-theme');
        applyTheme(current === 'dark' ? 'light' : 'dark');
    }

    function isDarkMode() {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark';
    }

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (localStorage.getItem('app_theme') === 'auto') {
            applyTheme('auto');
        }
    });

    // ============================================================
    // AUDIO
    // ============================================================
    let audioCtx = null;
    let audioBuffer = null;
    let audioReady = false;
    let audioUnlocked = false;

    const audioBtn = document.getElementById('audioBtn');
    const isUnlockedInStorage = localStorage.getItem('monitor_audio_unlocked') === 'true';

    if (isUnlockedInStorage) {
        audioUnlocked = true;
        audioBtn.classList.add('active');
        audioBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i> Suara Aktif';
    }

    async function initAudio() {
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const response = await fetch('{{ asset("audio/beep1.mp3") }}');
            if (!response.ok) return;
            const arrayBuffer = await response.arrayBuffer();
            audioBuffer = await audioCtx.decodeAudioData(arrayBuffer);
            audioReady = true;
        } catch (err) {}
    }
    initAudio();

    function unlockAudio() {
        if (!audioCtx) { initAudio(); return; }
        audioCtx.resume().then(() => {
            audioUnlocked = true;
            localStorage.setItem('monitor_audio_unlocked', 'true');
            audioBtn.classList.add('active');
            audioBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i> Suara Aktif';
        });
    }

    document.addEventListener('click', function() {
        if (!audioUnlocked) unlockAudio();
    }, { once: true });

    function playBeep() {
        if (!audioReady || !audioCtx || !audioBuffer) return;
        if (audioCtx.state === 'suspended') audioCtx.resume();
        try {
            const source = audioCtx.createBufferSource();
            source.buffer = audioBuffer;
            source.connect(audioCtx.destination);
            source.start(0);
        } catch (err) {}
    }

    // ============================================================
    // JAM
    // ============================================================
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // ============================================================
    // PERIODE STATE
    // ============================================================
    let periodeKomputer = localStorage.getItem('monitor_periode_komputer') || 'bulanan';
    let periodeCctv     = localStorage.getItem('monitor_periode_cctv') || 'bulanan';

    const validPeriode = ['harian', 'mingguan', 'bulanan', 'semua'];
    if (!validPeriode.includes(periodeKomputer)) periodeKomputer = 'bulanan';
    if (!validPeriode.includes(periodeCctv)) periodeCctv = 'bulanan';

    function initPeriodeButtons() {
        document.querySelectorAll('.periode-btn-group').forEach(function(group) {
            const target = group.dataset.target;
            const activePeriode = (target === 'komputer') ? periodeKomputer : periodeCctv;

            group.querySelectorAll('.periode-btn').forEach(function(btn) {
                btn.classList.toggle('active', btn.dataset.periode === activePeriode);
            });
        });
    }

    document.querySelectorAll('.periode-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = this.closest('.periode-btn-group');
            const target = group.dataset.target;
            const periode = this.dataset.periode;

            if (target === 'komputer') {
                periodeKomputer = periode;
                localStorage.setItem('monitor_periode_komputer', periode);
            } else {
                periodeCctv = periode;
                localStorage.setItem('monitor_periode_cctv', periode);
            }

            group.querySelectorAll('.periode-btn').forEach(function(b) {
                b.classList.remove('active');
            });
            this.classList.add('active');

            loadData();
        });
    });

    initPeriodeButtons();

    // ============================================================
    // CHARTS
    // ============================================================
    let chartKomputer = null;
    let chartCctv = null;
    let lastRekapKomputer = null;
    let lastRekapCctv = null;

    function updateChartsTheme() {
        if (lastRekapKomputer) {
            chartKomputer = renderChart('chartKomputer', chartKomputer, lastRekapKomputer, 'komputer');
        }
        if (lastRekapCctv) {
            chartCctv = renderChart('chartCctv', chartCctv, lastRekapCctv, 'cctv');
        }
    }

    function renderChart(canvasId, chartRef, ratings, warnaHeader) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        if (!ratings || ratings.length === 0) {
            if (chartRef) chartRef.destroy();
            const ctx2d = ctx.getContext('2d');
            ctx2d.clearRect(0, 0, ctx.width, ctx.height);
            return null;
        }

        const labels = ratings.map(r => r.nama);
        const dataSelesai = ratings.map(r => r.selesai);

        const colors = ratings.map((r, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 0.95)';
            if (i === 1) return 'rgba(108, 117, 125, 0.9)';
            if (i === 2) return 'rgba(205, 127, 50, 0.9)';
            return warnaHeader === 'cctv' ? 'rgba(13, 202, 240, 0.7)' : 'rgba(13, 110, 253, 0.7)';
        });

        const borders = ratings.map((r, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 1)';
            if (i === 1) return 'rgba(108, 117, 125, 1)';
            if (i === 2) return 'rgba(205, 127, 50, 1)';
            return warnaHeader === 'cctv' ? 'rgba(13, 202, 240, 1)' : 'rgba(13, 110, 253, 1)';
        });

        const textColor = isDarkMode() ? '#e6edf3' : '#212529';
        const gridColor = isDarkMode() ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';

        if (chartRef) chartRef.destroy();

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tugas Selesai',
                    data: dataSelesai,
                    backgroundColor: colors,
                    borderColor: borders,
                    borderWidth: 1.5,
                    borderRadius: 3,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.65 
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 2, right: 25, left: 2, bottom: 2 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 6,
                        displayColors: false,
                        cornerRadius: 4,
                        bodyFont: { size: 10 },
                        callbacks: {
                            label: function(context) {
                                const t = ratings[context.dataIndex];
                                return [
                                    '✅ Selesai: ' + t.selesai,
                                    '⏳ Belum  : ' + t.belum,
                                    '📊 Total  : ' + t.total,
                                ];
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1, 
                            precision: 0, 
                            font: { size: 8, weight: 'bold' }, 
                            color: textColor 
                        },
                        grid: { color: gridColor }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 9, weight: 'bold' }, 
                            color: textColor 
                        }
                    }
                },
                animation: {
                    duration: 400,
                    onComplete: function() {
                        const chart = this;
                        const c = chart.ctx;
                        const currentTextColor = isDarkMode() ? '#e6edf3' : '#212529';

                        c.save();
                        c.textAlign = 'left';
                        c.textBaseline = 'middle';

                        chart.data.datasets.forEach(function(dataset, i) {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                const t = ratings[index];
                                c.font = 'bold 9px Arial'; 
                                c.fillStyle = currentTextColor;
                                c.fillText(t.selesai, bar.x + 4, bar.y);
                            });
                        });
                        c.restore();
                    }
                }
            }
        });
    }

    // ============================================================
    // LOAD DATA
    // ============================================================
    const API_BASE_URL = '{{ route("monitor.data") }}';
    let refreshCountdown = 10;
    let lastJobCount = parseInt(localStorage.getItem('monitor_last_job_count') || '0', 10);
    let isFirstLoad = true;

    async function loadData() {
        try {
            const url = API_BASE_URL
                + '?periode_komputer=' + encodeURIComponent(periodeKomputer)
                + '&periode_cctv=' + encodeURIComponent(periodeCctv);

            const response = await fetch(url);
            const data = await response.json();

            document.getElementById('kmptTotal').textContent = data.statsKomputer.total;
            document.getElementById('kmptSelesai').textContent = data.statsKomputer.selesai;
            document.getElementById('kmptBelum').textContent = data.statsKomputer.belum;

            document.getElementById('cctvTotal').textContent = data.statsCctv.total;
            document.getElementById('cctvSelesai').textContent = data.statsCctv.selesai;
            document.getElementById('cctvBelum').textContent = data.statsCctv.belum;

            document.getElementById('labelHariIniKmpt').textContent = data.tanggalHariIni;
            document.getElementById('labelKemarinKmpt').textContent = data.tanggalKemarin;
            document.getElementById('labelHariIniCctv').textContent = data.tanggalHariIni;
            document.getElementById('labelKemarinCctv').textContent = data.tanggalKemarin;

            document.getElementById('countKomputer').textContent = data.statsKomputer.total + ' tugas';
            document.getElementById('countCctv').textContent = data.statsCctv.total + ' tugas';

            document.getElementById('countHariIniKmpt').textContent = data.kerjaanHariIniKomputer.length;
            document.getElementById('countKemarinKmpt').textContent = data.kerjaanKemarinKomputer.length;
            document.getElementById('countHariIniCctv').textContent = data.kerjaanHariIniCctv.length;
            document.getElementById('countKemarinCctv').textContent = data.kerjaanKemarinCctv.length;

            const currentJobCount = data.hariIni ? data.hariIni.total : 0;
            if (!isFirstLoad && currentJobCount > lastJobCount && audioUnlocked) {
                playBeep();
                setTimeout(playBeep, 500);
                setTimeout(playBeep, 1000);
            }
            lastJobCount = currentJobCount;
            localStorage.setItem('monitor_last_job_count', currentJobCount);
            isFirstLoad = false;

            renderTable('tableHariIniKomputer', data.kerjaanHariIniKomputer);
            renderTable('tableKemarinKomputer', data.kerjaanKemarinKomputer);
            renderTable('tableHariIniCctv', data.kerjaanHariIniCctv);
            renderTable('tableKemarinCctv', data.kerjaanKemarinCctv);

            lastRekapKomputer = data.rekapTeknisiKomputer;
            lastRekapCctv = data.rekapTeknisiCctv;
            chartKomputer = renderChart('chartKomputer', chartKomputer, lastRekapKomputer, 'komputer');
            chartCctv = renderChart('chartCctv', chartCctv, lastRekapCctv, 'cctv');

            document.getElementById('lastUpdate').textContent = data.timestamp;
        } catch (err) {
            console.error('❌ Error:', err);
        }
    }

    // ============================================================
    // RENDER TABEL
    // ============================================================
    function renderTable(tbodyId, jobs) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        if (!jobs || jobs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-2">Belum ada</td></tr>';
            return;
        }

        // Sorting: tugas pending di atas
        const sortedJobs = [...jobs].sort((a, b) => {
            const aIsPending = (a.status === 'DITUGASKAN' || a.status === 'DIKERJAKAN');
            const bIsPending = (b.status === 'DITUGASKAN' || b.status === 'DIKERJAKAN');
            
            if (aIsPending && !bIsPending) return -1;
            if (!aIsPending && bIsPending) return 1;
            return 0;
        });

        tbody.innerHTML = sortedJobs.map(j => {
            const badgeClass = {
                'DITUGASKAN': 'bg-secondary',
                'DIKERJAKAN': 'bg-warning text-dark',
                'SELESAI':    'bg-success',
            }[j.status] || 'bg-light text-dark';

            const isBelumSelesai = (j.status === 'DITUGASKAN' || j.status === 'DIKERJAKAN');
            const rowClass = isBelumSelesai ? 'row-belum' : '';
            const lampu = isBelumSelesai ? '<span class="lampu-indicator"></span>' : '';

            const teknisiBadge = j.teknisi
                ? `<span class="badge bg-primary teknisi-badge"><i class="bi bi-person-circle"></i> ${j.teknisi}</span>`
                : `<span class="badge bg-danger badge-blinking teknisi-badge"><i class="bi bi-exclamation-triangle"></i> Belum</span>`;

            return `
                <tr class="${rowClass}">
                    <td>${lampu}<span class="kode-job">${j.kode_job}</span></td>
                    <td>
                        <div class="judul-job" title="${j.judul}">${j.judul}</div>
                        <div class="lokasi-job">
                            <i class="bi bi-geo-alt-fill"></i> ${j.lok_gedung || '-'} / ${j.lokasi_ruang || '-'}
                        </div>
                    </td>
                    <td>${teknisiBadge}</td>
                    <td><span class="badge ${badgeClass} status-badge">${j.status.replace('_', ' ')}</span></td>
                </tr>`;
        }).join('');
    }

    // ============================================================
    // AUTO REFRESH
    // ============================================================
    setInterval(() => {
        refreshCountdown--;
        if (refreshCountdown <= 0) {
            loadData();
            refreshCountdown = 10;
        }
        document.getElementById('refreshCounter').textContent = refreshCountdown;
    }, 1000);

    // Load pertama kali
    loadData();
</script>

</body>
</html>