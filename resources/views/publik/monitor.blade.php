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

        /* ============================================================ */
        /* 🆕 FIT TV SCREEN — NO SCROLL                                 */
        /* ============================================================ */
        html, body {
            height: 100vh;
            overflow: hidden;           /* ⛔ no scroll */
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

        /* ============ HEADER (compact) ============ */
        .monitor-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 5px 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            flex-shrink: 0;             /* tidak mengecil */
        }
        .monitor-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: .92rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .monitor-header .live-indicator {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: .6rem;
            margin-left: 6px;
        }
        .live-dot {
            width: 6px; height: 6px;
            background: #43e97b;
            border-radius: 50%;
            animation: livePulse 1.5s infinite;
            box-shadow: 0 0 10px #43e97b;
        }
        @keyframes livePulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.4); opacity: 0.6; }
        }
        .clock {
            font-family: 'Courier New', monospace;
            font-size: .78rem;
            font-weight: bold;
            background: rgba(0,0,0,0.2);
            padding: 2px 8px;
            border-radius: 5px;
        }
        .btn-login {
            background: white;
            color: #0d6efd;
            border: none;
            padding: 3px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: .7rem;
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
            padding: 3px 10px;
            border-radius: 5px;
            font-weight: 600;
            font-size: .7rem;
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
            padding: 3px 9px;
            border-radius: 5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            line-height: 1;
        }
        .theme-toggle:hover {
            background: rgba(255,255,255,0.3);
            color: #fff;
        }
        .theme-toggle .icon-sun,
        .theme-toggle .icon-moon {
            display: none;
        }
        html[data-bs-theme="light"] .theme-toggle .icon-moon { display: inline-block; }
        html[data-bs-theme="dark"] .theme-toggle .icon-sun { display: inline-block; }

        /* ============ CONTENT — FILL REMAINING ============ */
        .monitor-content {
            flex: 1;
            padding: 6px 8px;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 6px;
            overflow: hidden;
            min-height: 0;              /* penting supaya grid bisa shrink */
        }

        /* ============ STATS GABUNGAN (compact) ============ */
        .stats-total {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
        .stat-card {
            border-radius: 6px;
            color: white;
            padding: 6px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 48px;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            position: relative;
            overflow: hidden;
        }
        .stat-card .icon-bg {
            position: absolute;
            right: -5px;
            bottom: -10px;
            font-size: 2rem;
            opacity: 0.15;
        }
        .stat-card small {
            font-size: .62rem;
            opacity: 0.9;
            display: block;
        }
        .stat-card h3 {
            margin: 1px 0 0 0;
            font-size: 1.2rem;
            font-weight: 700;
            line-height: 1;
        }

        /* ============ 2 KOLOM — FILL HEIGHT ============ */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            min-height: 0;
            overflow: hidden;
        }

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
            padding: 5px 12px;
            font-weight: 700;
            font-size: .78rem;
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
            padding: 1px 7px;
            border-radius: 8px;
            font-size: .65rem;
        }
        .kolom-header.cctv .count-badge { background: rgba(0,0,0,.15); color: #000; }

        /* Mini Stats (compact) */
        .mini-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3px;
            padding: 4px 6px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .mini-stat {
            text-align: center;
            padding: 3px 2px;
            border-radius: 4px;
            background: white;
            border: 1px solid #dee2e6;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .mini-stat small {
            display: block;
            font-size: .55rem;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .3px;
        }
        .mini-stat .val {
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.1;
            margin-top: 1px;
        }
        .mini-stat.blue { border-left: 3px solid #0d6efd; }
        .mini-stat.blue .val { color: #0d6efd; }
        .mini-stat.green { border-left: 3px solid #198754; }
        .mini-stat.green .val { color: #198754; }
        .mini-stat.red { border-left: 3px solid #dc3545; }
        .mini-stat.red .val { color: #dc3545; }

        /* Sub-section — split evenly */
        .sub-section {
            border-bottom: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            min-height: 0;
            flex: 1;                    /* bagi rata */
            transition: border-color 0.3s;
        }
        .sub-section:last-of-type {
            border-bottom: none;
        }
        .sub-header {
            padding: 4px 10px;
            font-weight: 600;
            font-size: .68rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f1f3f5;
            border-bottom: 1px solid #dee2e6;
            flex-shrink: 0;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .sub-header .sub-count {
            background: #dee2e6;
            padding: 1px 6px;
            border-radius: 6px;
            font-size: .6rem;
            color: #495057;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Table scroll — fill remaining height */
        .table-scroll {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }
        .table-scroll::-webkit-scrollbar { width: 4px; }
        .table-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 2px; }

        table { font-size: .68rem; margin: 0; }
        table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8f9fa;
            font-size: .6rem;
            padding: 3px 5px;
            white-space: nowrap;
            border-bottom: 2px solid #dee2e6;
            text-transform: uppercase;
            letter-spacing: .3px;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        }
        table tbody td {
            padding: 3px 5px;
            vertical-align: middle;
            transition: background-color 0.3s, color 0.3s;
        }

        /* ============ KELAP-KELIP ============ */
        @keyframes blinkRowBg {
            0%   { background-color: #ffeb3b; }
            50%  { background-color: #ffffff; }
            100% { background-color: #ffeb3b; }
        }
        tr.row-belum,
        tr.row-belum td {
            animation: blinkRowBg 1s infinite ease-in-out !important;
            font-weight: 700 !important;
        }
        tr.row-belum { border-left: 4px solid #dc3545 !important; }

        @keyframes lampuPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
                background: #dc3545;
                box-shadow: 0 0 10px #dc3545;
            }
            50% {
                transform: scale(1.4);
                opacity: 0.6;
                background: #ffc107;
                box-shadow: 0 0 20px #ffc107;
            }
        }
        .lampu-indicator {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #dc3545;
            box-shadow: 0 0 10px #dc3545;
            animation: lampuPulse 0.8s infinite;
            margin-right: 4px;
            vertical-align: middle;
        }

        @keyframes blinkBadge {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.15); }
        }
        .badge-blinking {
            animation: blinkBadge 0.8s infinite;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.7);
        }

        /* Chart section — compact, fixed height */
        .chart-section {
            padding: 4px 8px;
            background: #fafbfc;
            flex-shrink: 0;
            transition: background-color 0.3s;
        }
        .chart-section h6 {
            font-size: .65rem;
            font-weight: 700;
            margin-bottom: 3px;
            color: #495057;
            transition: color 0.3s;
        }
        .chart-container {
            position: relative;
            height: 130px;              /* fix tinggi chart */
        }

        /* Footer — compact */
        .monitor-footer {
            text-align: center;
            font-size: .62rem;
            color: #6c757d;
            padding: 3px 0;
            flex-shrink: 0;
            transition: color 0.3s;
        }

        /* Responsive */
        @media (max-width: 900px) {
            html, body { height: auto; overflow: auto; }
            .monitor-wrapper { height: auto; }
            .monitor-content { overflow: visible; }
            .two-columns { grid-template-columns: 1fr; }
            .stats-total { grid-template-columns: 1fr; }
            .table-scroll { max-height: 300px; flex: none; }
            .chart-container { height: 180px; }
        }

        /* ============================================================ */
        /* 🆕 DARK MODE STYLES                                           */
        /* ============================================================ */

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

        html[data-bs-theme="dark"] .sub-section {
            border-color: #30363d;
        }

        html[data-bs-theme="dark"] .sub-header {
            background: #21262d;
            border-color: #30363d;
            color: #e6edf3;
        }

        html[data-bs-theme="dark"] .sub-header .sub-count {
            background: #30363d;
            color: #8b949e;
        }

        html[data-bs-theme="dark"] table thead th {
            background: #21262d !important;
            color: #e6edf3 !important;
            border-color: #30363d !important;
        }

        html[data-bs-theme="dark"] table tbody td {
            color: #e6edf3;
            border-color: #30363d;
        }

        html[data-bs-theme="dark"] .chart-section {
            background: #0d1117;
        }

        html[data-bs-theme="dark"] .chart-section h6 {
            color: #e6edf3;
        }

        html[data-bs-theme="dark"] code {
            background: #21262d;
            color: #79c0ff;
            padding: 1px 4px;
            border-radius: 3px;
        }

        html[data-bs-theme="dark"] .text-muted {
            color: #8b949e !important;
        }

        html[data-bs-theme="dark"] .monitor-footer {
            color: #8b949e;
        }

        html[data-bs-theme="dark"] .badge.bg-light {
            background-color: #30363d !important;
            color: #e6edf3 !important;
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
            <small style="opacity: 0.9; font-size: .6rem;">
                <i class="bi bi-clock-history"></i>
                Update: <span id="lastUpdate">-</span>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="theme-toggle" onclick="toggleTheme()" title="Ganti Tema Light/Dark">
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

    {{-- CONTENT --}}
    <div class="monitor-content">

        {{-- STATISTIK GABUNGAN --}}
        <div class="stats-total">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <i class="bi bi-calendar-day icon-bg"></i>
                <div>
                    <small>📥 Total Tugas Hari Ini</small>
                    <h3 id="totalTugas">0</h3>
                </div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="bi bi-check-circle icon-bg"></i>
                <div>
                    <small>✅ Sudah Dikerjakan</small>
                    <h3 id="tugasSelesai">0</h3>
                </div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <i class="bi bi-hourglass-split icon-bg"></i>
                <div>
                    <small>⏳ Belum Dikerjakan</small>
                    <h3 id="tugasBelum">0</h3>
                </div>
            </div>
        </div>

        {{-- 2 KOLOM — FILL HEIGHT --}}
        <div class="two-columns">

            {{-- ==================== KOMPUTER ==================== --}}
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

                <div class="sub-section">
                    <div class="sub-header">
                        <span>
                            <i class="bi bi-calendar-day text-primary"></i>
                            Hari Ini — <span id="labelHariIniKmpt">-</span>
                        </span>
                        <span class="sub-count" id="countHariIniKmpt">0</span>
                    </div>
                    <div class="table-scroll">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 22%;">Kode</th>
                                    <th style="width: 43%;">Pekerjaan</th>
                                    <th style="width: 20%;">Teknisi</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableHariIniKomputer">
                                <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sub-section">
                    <div class="sub-header">
                        <span>
                            <i class="bi bi-calendar-check text-secondary"></i>
                            Kemarin — <span id="labelKemarinKmpt">-</span>
                        </span>
                        <span class="sub-count" id="countKemarinKmpt">0</span>
                    </div>
                    <div class="table-scroll">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 22%;">Kode</th>
                                    <th style="width: 43%;">Pekerjaan</th>
                                    <th style="width: 20%;">Teknisi</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableKemarinKomputer">
                                <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="chart-section">
                    <h6><i class="bi bi-trophy-fill text-warning"></i> Top Teknisi Komputer</h6>
                    <div class="chart-container">
                        <canvas id="chartKomputer"></canvas>
                    </div>
                </div>
            </div>

            {{-- ==================== CCTV ==================== --}}
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

                <div class="sub-section">
                    <div class="sub-header">
                        <span>
                            <i class="bi bi-calendar-day text-info"></i>
                            Hari Ini — <span id="labelHariIniCctv">-</span>
                        </span>
                        <span class="sub-count" id="countHariIniCctv">0</span>
                    </div>
                    <div class="table-scroll">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 22%;">Kode</th>
                                    <th style="width: 43%;">Pekerjaan</th>
                                    <th style="width: 20%;">Teknisi</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableHariIniCctv">
                                <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sub-section">
                    <div class="sub-header">
                        <span>
                            <i class="bi bi-calendar-check text-secondary"></i>
                            Kemarin — <span id="labelKemarinCctv">-</span>
                        </span>
                        <span class="sub-count" id="countKemarinCctv">0</span>
                    </div>
                    <div class="table-scroll">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 22%;">Kode</th>
                                    <th style="width: 43%;">Pekerjaan</th>
                                    <th style="width: 20%;">Teknisi</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableKemarinCctv">
                                <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="chart-section">
                    <h6><i class="bi bi-trophy-fill text-warning"></i> Top Teknisi CCTV</h6>
                    <div class="chart-container">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ============================================================
    // THEME TOGGLE
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
            const response = await fetch('{{ asset("audio/beep.wav") }}');
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
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.75,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 3, right: 28, left: 3, bottom: 3 } },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 8,
                        displayColors: false,
                        cornerRadius: 5,
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
                        ticks: { stepSize: 1, precision: 0, font: { size: 8, weight: 'bold' }, color: textColor },
                        grid: { color: gridColor }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 9, weight: 'bold' }, color: textColor }
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
                                c.font = 'bold 10px Arial';
                                c.fillStyle = currentTextColor;
                                c.fillText(t.selesai, bar.x + 5, bar.y);
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
    const API_URL = '{{ route("monitor.data") }}';
    let refreshCountdown = 10;
    let lastJobCount = parseInt(localStorage.getItem('monitor_last_job_count') || '0', 10);
    let isFirstLoad = true;

    async function loadData() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();

            document.getElementById('totalTugas').textContent = data.hariIni.total;
            document.getElementById('tugasSelesai').textContent = data.hariIni.selesai;
            document.getElementById('tugasBelum').textContent = data.hariIni.belum;

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

            const currentJobCount = data.hariIni.total;
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

    function renderTable(tbodyId, jobs) {
        const tbody = document.getElementById(tbodyId);
        if (!tbody) return;

        if (!jobs || jobs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-2">Belum ada</td></tr>';
            return;
        }

        tbody.innerHTML = jobs.map(j => {
            const badgeClass = {
                'DITUGASKAN': 'bg-secondary',
                'DIKERJAKAN': 'bg-warning text-dark',
                'SELESAI':    'bg-success',
            }[j.status] || 'bg-light text-dark';

            const isBelumSelesai = (j.status === 'DITUGASKAN' || j.status === 'DIKERJAKAN');
            const rowClass = isBelumSelesai ? 'row-belum' : '';
            const lampu = isBelumSelesai ? '<span class="lampu-indicator"></span>' : '';

            const teknisiBadge = j.teknisi
                ? `<span class="badge bg-primary" style="font-size:.58rem;"><i class="bi bi-person-circle"></i> ${j.teknisi}</span>`
                : `<span class="badge bg-danger badge-blinking" style="font-size:.58rem;"><i class="bi bi-exclamation-triangle"></i> Belum diambil</span>`;

            return `
                <tr class="${rowClass}">
                    <td>${lampu}<code style="font-size:.58rem;">${j.kode_job}</code></td>
                    <td>
                        <div class="fw-semibold" style="font-size:.65rem;">${j.judul}</div>
                        <small class="text-muted" style="font-size:.55rem;">
                            <i class="bi bi-geo-alt-fill"></i> ${j.lok_gedung || '-'} / ${j.lokasi_ruang || '-'}
                        </small>
                    </td>
                    <td>${teknisiBadge}</td>
                    <td><span class="badge ${badgeClass}" style="font-size:.55rem;">${j.status.replace('_', ' ')}</span></td>
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

    loadData();
</script>

</body>
</html>