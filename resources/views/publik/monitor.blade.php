<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>📺 Monitor Pekerjaan Teknisi — Live</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        html, body {
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: .8rem;
        }

        .monitor-wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        .monitor-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 6px 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        .monitor-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1rem;
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
            font-size: .65rem;
            margin-left: 6px;
        }
        .live-dot {
            width: 7px;
            height: 7px;
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
            font-size: .85rem;
            font-weight: bold;
            background: rgba(0,0,0,0.2);
            padding: 3px 10px;
            border-radius: 6px;
        }

        .btn-login {
            background: white;
            color: #0d6efd;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: .75rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.3s;
        }
        .btn-login:hover {
            background: #43e97b;
            color: white;
            transform: scale(1.05);
        }

        #audioBtn {
            background: #ffc107;
            color: #000;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: .75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            animation: audioBtnPulse 1.5s infinite;
            transition: all 0.3s;
        }
        #audioBtn:hover { transform: scale(1.05); }
        #audioBtn.active { background: #43e97b; animation: none; }
        @keyframes audioBtnPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            50% { box-shadow: 0 0 0 8px rgba(255, 193, 7, 0); }
        }

        .monitor-content {
            flex: 1;
            overflow: hidden;
            padding: 6px 10px;
            display: grid;
            grid-template-rows: auto 1fr;
            gap: 6px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }
        .stat-card {
            border-radius: 8px;
            color: white;
            overflow: hidden;
            position: relative;
            padding: 6px 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 60px;
        }
        .stat-card .icon-bg {
            position: absolute;
            right: -5px;
            bottom: -12px;
            font-size: 2.8rem;
            opacity: 0.15;
        }
        .stat-card small {
            font-size: .68rem;
            opacity: 0.9;
            display: block;
        }
        .stat-card h3 {
            margin: 2px 0 0 0;
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 6px;
            min-height: 0;
            overflow: hidden;
        }

        .table-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,.08);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }
        .table-card-header {
            padding: 5px 10px;
            font-weight: 600;
            font-size: .75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }
        .table-card-body {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }
        .table-card-body::-webkit-scrollbar { width: 4px; }
        .table-card-body::-webkit-scrollbar-track { background: transparent; }
        .table-card-body::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 2px;
        }

        table { font-size: .72rem; margin: 0; }
        table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8f9fa;
            font-size: .68rem;
            padding: 4px 6px;
            white-space: nowrap;
            border-bottom: 2px solid #dee2e6;
        }
        table tbody td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        @keyframes blinkRowBg {
            0%, 100% { background-color: #ffeb3b; }
            50%      { background-color: #ffffff; }
        }
        tr.row-belum td {
            animation: blinkRowBg 1s infinite ease-in-out;
            font-weight: 600;
        }
        @keyframes blinkBadge {
            0%, 100% { transform: scale(1); }
            50%      { transform: scale(1.15); }
        }
        .badge-blinking {
            animation: blinkBadge 0.8s infinite;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.7);
        }

        .monitor-footer {
            text-align: center;
            font-size: .68rem;
            color: #6c757d;
            padding: 3px 0;
            flex-shrink: 0;
        }

        /* Ranking Medali */
        .rank-medal {
            font-size: 1.1rem;
            display: inline-block;
        }
        .row-top-1 {
            background: linear-gradient(90deg, rgba(255, 215, 0, 0.15) 0%, transparent 100%);
            border-left: 4px solid #ffd700;
        }
        .row-top-2 {
            background: linear-gradient(90deg, rgba(192, 192, 192, 0.15) 0%, transparent 100%);
            border-left: 4px solid #c0c0c0;
        }
        .row-top-3 {
            background: linear-gradient(90deg, rgba(205, 127, 50, 0.15) 0%, transparent 100%);
            border-left: 4px solid #cd7f32;
        }

        @media (max-width: 768px) {
            html, body { height: auto; overflow: auto; }
            .monitor-wrapper { height: auto; }
            .monitor-content { height: auto; }
            .main-grid { grid-template-columns: 1fr; grid-template-rows: auto; }
            .table-card-body { max-height: 300px; }
            .stat-card h3 { font-size: 1.1rem; }
            .monitor-header h4 { font-size: .85rem; }
        }
    </style>
</head>
<body>

<div class="monitor-wrapper">

    {{-- HEADER --}}
    <div class="monitor-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>
                <i class="bi bi-display"></i>
                Monitor Pekerjaan Teknisi
                <span class="live-indicator">
                    <span class="live-dot"></span> LIVE
                </span>
            </h4>
            <small style="opacity: 0.9; font-size: .65rem;">
                <i class="bi bi-clock-history"></i>
                Update: <span id="lastUpdate">-</span>
            </small>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button id="audioBtn" onclick="unlockAudio()">
                <i class="bi bi-volume-mute-fill"></i> Aktifkan Suara
            </button>
            <div class="clock" id="clock">--:--:--</div>
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </div>
    </div>

    {{-- KONTEN --}}
    <div class="monitor-content">

        {{-- 3 KOTAK STATISTIK --}}
        <div class="stats-row">
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

        {{-- GRID UTAMA 2x2 --}}
        <div class="main-grid">

            {{-- 1. HARI INI --}}
            <div class="table-card">
                <div class="table-card-header bg-primary text-white">
                    <span>
                        <i class="bi bi-calendar-day"></i>
                        Pekerjaan Hari Ini — <span id="labelHariIni">-</span>
                    </span>
                    <span class="badge bg-light text-dark" id="countHariIni">0</span>
                </div>
                <div class="table-card-body">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 42%;">Pekerjaan</th>
                                <th style="width: 22%;">Teknisi</th>
                                <th style="width: 18%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableHariIni">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. KEMARIN --}}
            <div class="table-card">
                <div class="table-card-header bg-secondary text-white">
                    <span>
                        <i class="bi bi-calendar-check"></i>
                        Pekerjaan Kemarin — <span id="labelKemarin">-</span>
                    </span>
                    <span class="badge bg-light text-dark" id="countKemarin">0</span>
                </div>
                <div class="table-card-body">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Kode</th>
                                <th style="width: 42%;">Pekerjaan</th>
                                <th style="width: 22%;">Teknisi</th>
                                <th style="width: 18%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tableKemarin">
                            <tr><td colspan="4" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. TOP TEKNISI HARI INI --}}
            <div class="table-card">
                <div class="table-card-header bg-warning text-dark">
                    <span>
                        <i class="bi bi-trophy-fill"></i>
                        Top Teknisi Hari Ini — <span id="labelRating">-</span>
                    </span>
                </div>
                <div class="table-card-body">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;" class="text-center">#</th>
                                <th style="width: 37%;">Teknisi</th>
                                <th style="width: 18%;" class="text-center">✅</th>
                                <th style="width: 18%;" class="text-center">⏳</th>
                                <th style="width: 19%;" class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody id="tableTopTeknisi">
                            <tr><td colspan="5" class="text-center text-muted py-3">Memuat...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. GRAFIK TOP TEKNISI --}}
            <div class="table-card">
                <div class="table-card-header bg-info text-white">
                    <span>
                        <i class="bi bi-bar-chart-fill"></i>
                        Top Teknisi Terbanyak
                    </span>
                </div>
                <div class="table-card-body" style="padding: 8px;">
                    <div style="position: relative; height: 100%; min-height: 160px;">
                        <canvas id="chartTopTeknisi"></canvas>
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
    // AUDIO ALARM
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
            console.log('✅ Audio monitor siap!');
        } catch (err) {
            console.log('❌ Audio error:', err);
        }
    }
    initAudio();

    function unlockAudio() {
        if (!audioCtx) { initAudio(); return; }
        audioCtx.resume().then(() => {
            audioUnlocked = true;
            localStorage.setItem('monitor_audio_unlocked', 'true');
            audioBtn.classList.add('active');
            audioBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i> Suara Aktif';
            console.log('🔓 Audio monitor unlocked!');
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
            console.log('🔊 Beep monitor!');
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
    // CHART TOP TEKNISI (berdasarkan jumlah selesai)
    // ============================================================
    let chartTop = null;

    function renderChartTop(ratings) {
        const ctx = document.getElementById('chartTopTeknisi');
        if (!ctx || ratings.length === 0) return;

        const labels = ratings.map(r => r.nama);
        const dataSelesai = ratings.map(r => r.selesai);

        // Warna berdasarkan peringkat
        const colors = ratings.map((r, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 0.95)';
            if (i === 1) return 'rgba(108, 117, 125, 0.9)';
            if (i === 2) return 'rgba(205, 127, 50, 0.9)';
            return 'rgba(13, 110, 253, 0.7)';
        });

        const borders = ratings.map((r, i) => {
            if (i === 0) return 'rgba(255, 193, 7, 1)';
            if (i === 1) return 'rgba(108, 117, 125, 1)';
            if (i === 2) return 'rgba(205, 127, 50, 1)';
            return 'rgba(13, 110, 253, 1)';
        });

        if (chartTop) {
            chartTop.data.labels = labels;
            chartTop.data.datasets[0].data = dataSelesai;
            chartTop.data.datasets[0].backgroundColor = colors;
            chartTop.data.datasets[0].borderColor = borders;
            chartTop.update();
            return;
        }

        chartTop = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tugas Selesai',
                    data: dataSelesai,
                    backgroundColor: colors,
                    borderColor: borders,
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.7,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { top: 10, right: 50, left: 5, bottom: 5 }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.9)',
                        padding: 10,
                        displayColors: false,
                        cornerRadius: 6,
                        callbacks: {
                            title: function(context) {
                                const rank = context[0].dataIndex + 1;
                                let medal = '';
                                if (rank === 1) medal = '🥇 ';
                                else if (rank === 2) medal = '🥈 ';
                                else if (rank === 3) medal = '🥉 ';
                                return medal + ratings[context[0].dataIndex].nama;
                            },
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
                            font: { size: 10, weight: 'bold' }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, weight: 'bold' },
                            color: '#212529'
                        }
                    }
                },
                animation: {
                    duration: 600,
                    onComplete: function() {
                        const chart = this;
                        const ctx = chart.ctx;

                        ctx.save();
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach(function(dataset, i) {
                            const meta = chart.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                const t = ratings[index];

                                // Angka jumlah selesai di ujung bar
                                ctx.font = 'bold 12px Arial';
                                ctx.fillStyle = '#212529';
                                ctx.fillText(t.selesai, bar.x + 6, bar.y);
                            });
                        });
                        ctx.restore();
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

            document.getElementById('labelHariIni').textContent = data.tanggalHariIni;
            document.getElementById('labelKemarin').textContent = data.tanggalKemarin;
            document.getElementById('labelRating').textContent = data.tanggalHariIni;

            document.getElementById('countHariIni').textContent = data.kerjaanHariIni.length;
            document.getElementById('countKemarin').textContent = data.kerjaanKemarin.length;

            // Deteksi tugas baru → bunyi
            const currentJobCount = data.hariIni.total;
            if (!isFirstLoad && currentJobCount > lastJobCount && audioUnlocked) {
                playBeep();
                setTimeout(playBeep, 500);
                setTimeout(playBeep, 1000);
                console.log('🚨 TUGAS BARU! Alarm monitor');
            }
            lastJobCount = currentJobCount;
            localStorage.setItem('monitor_last_job_count', currentJobCount);
            isFirstLoad = false;

            renderTableHariIni(data.kerjaanHariIni);
            renderTableKemarin(data.kerjaanKemarin);
            renderTableTopTeknisi(data.ratingHariIni);
            renderChartTop(data.rekapTeknisi || []);

            document.getElementById('lastUpdate').textContent = data.timestamp;
        } catch (err) {
            console.error('❌ Error:', err);
        }
    }

    function renderTableHariIni(jobs) {
        const tbody = document.getElementById('tableHariIni');
        if (jobs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada</td></tr>';
            return;
        }
        tbody.innerHTML = jobs.map(j => {
            const badgeClass = {
                'DITUGASKAN': 'bg-secondary',
                'DIKERJAKAN': 'bg-warning text-dark',
                'SELESAI': 'bg-success',
            }[j.status] || 'bg-light text-dark';
            const rowClass = j.belum_diambil ? 'row-belum' : (j.status === 'DIKERJAKAN' ? 'row-belum' : '');
            const teknisiBadge = j.teknisi
                ? `<span class="badge bg-primary" style="font-size:.65rem;"><i class="bi bi-person-circle"></i> ${j.teknisi}</span>`
                : `<span class="badge bg-danger badge-blinking" style="font-size:.65rem;"><i class="bi bi-exclamation-triangle"></i> Belum diambil</span>`;
            return `
                <tr class="${rowClass}">
                    <td><code style="font-size:.62rem;">${j.kode_job}</code></td>
                    <td>
                        <div class="fw-semibold" style="font-size:.72rem;">${j.judul}</div>
                        <small class="text-muted" style="font-size:.62rem;">
                            <i class="bi bi-geo-alt-fill"></i> ${j.lok_gedung || '-'} / ${j.lokasi_ruang || '-'}
                        </small>
                    </td>
                    <td>${teknisiBadge}</td>
                    <td><span class="badge ${badgeClass}" style="font-size:.6rem;">${j.status.replace('_', ' ')}</span></td>
                </tr>`;
        }).join('');
    }

    function renderTableKemarin(jobs) {
        const tbody = document.getElementById('tableKemarin');
        if (jobs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Belum ada</td></tr>';
            return;
        }
        tbody.innerHTML = jobs.map(j => {
            const badgeClass = {
                'DITUGASKAN': 'bg-secondary',
                'DIKERJAKAN': 'bg-warning text-dark',
                'SELESAI': 'bg-success',
            }[j.status] || 'bg-light text-dark';
            const teknisiBadge = j.teknisi
                ? `<span class="badge bg-primary" style="font-size:.65rem;"><i class="bi bi-person-circle"></i> ${j.teknisi}</span>`
                : `<span class="badge bg-secondary" style="font-size:.65rem;">Belum diambil</span>`;
            return `
                <tr>
                    <td><code style="font-size:.62rem;">${j.kode_job}</code></td>
                    <td>
                        <div class="fw-semibold" style="font-size:.72rem;">${j.judul}</div>
                        <small class="text-muted" style="font-size:.62rem;">
                            <i class="bi bi-geo-alt-fill"></i> ${j.lok_gedung || '-'} / ${j.lokasi_ruang || '-'}
                        </small>
                    </td>
                    <td>${teknisiBadge}</td>
                    <td><span class="badge ${badgeClass}" style="font-size:.6rem;">${j.status.replace('_', ' ')}</span></td>
                </tr>`;
        }).join('');
    }

    // ============================================================
    // RENDER TOP TEKNISI (tanpa persen)
    // ============================================================
    function renderTableTopTeknisi(ratings) {
        const tbody = document.getElementById('tableTopTeknisi');
        if (ratings.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Belum ada</td></tr>';
            return;
        }

        tbody.innerHTML = ratings.map((t, i) => {
            let medal = '';
            let rowClass = '';

            if (i === 0) {
                medal = '🥇';
                rowClass = 'row-top-1';
            } else if (i === 1) {
                medal = '🥈';
                rowClass = 'row-top-2';
            } else if (i === 2) {
                medal = '🥉';
                rowClass = 'row-top-3';
            } else {
                medal = String(i + 1);
            }

            return `
                <tr class="${rowClass}">
                    <td class="text-center fw-bold" style="font-size:.9rem;">${medal}</td>
                    <td>
                        <i class="bi bi-person-circle text-primary"></i>
                        <strong>${t.nama}</strong>
                        ${i === 0 ? '<span class="badge bg-warning text-dark ms-1" style="font-size:.6rem;">TOP</span>' : ''}
                    </td>
                    <td class="text-center">
                        <span class="badge bg-success" style="font-size:.72rem; padding:5px 10px;">
                            ${t.selesai}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark" style="font-size:.72rem; padding:5px 10px;">
                            ${t.belum}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-secondary" style="font-size:.72rem; padding:5px 10px;">
                            ${t.total}
                        </span>
                    </td>
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