@extends('layouts.app_pwa')
@section('title', 'Absensi')

@php
    $isCctv = auth()->user()->isTeknisiCctv();
    $primaryColor = $isCctv ? '#0dcaf0' : '#0d6efd';
@endphp

@push('styles')
<style>
    .app-container { max-width: 600px; margin: 0 auto; padding: 16px; padding-bottom: 100px; }
    
    .clock-card {
        background: linear-gradient(135deg, {{ $primaryColor }}, #0a58ca);
        border-radius: 20px; padding: 24px; color: white; text-align: center;
        margin-bottom: 20px; box-shadow: 0 8px 20px rgba(13, 110, 253, 0.25);
    }
    .clock-card .clock { font-size: 2.8rem; font-weight: 800; letter-spacing: 2px; }
    .clock-card .date  { font-size: 0.9rem; opacity: 0.9; margin-top: 4px; }
    .clock-card .greet { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }

    .btn-absen { width: 100%; padding: 16px; border-radius: 14px; border: none; color: white; font-size: 1rem; font-weight: 800; display: flex; justify-content: center; align-items: center; gap: 8px; margin-bottom: 10px; cursor: pointer; transition: transform 0.15s; }
    .btn-absen:active { transform: scale(0.97); }
    .btn-masuk { background: #198754; }
    .btn-pulang { background: #dc3545; }

    /* MODAL KAMERA */
    .camera-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 9999; display: none; flex-direction: column; }
    .camera-modal.show { display: flex; }
    .camera-preview { flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
    .camera-preview video { max-width: 100%; max-height: 100%; object-fit: cover; }
    .camera-controls { padding: 20px; display: flex; gap: 12px; justify-content: center; background: #000; }
    .btn-cam { padding: 14px 24px; border-radius: 50px; border: none; font-weight: 800; font-size: 0.9rem; cursor: pointer; }
    .btn-cam-cancel { background: #6c757d; color: white; }
    .btn-cam-submit { background: #198754; color: white; }
</style>
@endpush

@section('content')
<div class="app-container">
    <div class="clock-card">
        <div class="greet">Halo, {{ auth()->user()->display_name }} 👋</div>
        <div class="clock" id="liveClock">00:00:00</div>
        <div class="date">{{ now()->translatedFormat('l, d F Y') }}</div>
    </div>

    <!-- List Absen Hari Ini -->
    <div style="background:white; border-radius:16px; padding:16px; margin-bottom:20px; box-shadow: 0 4px 12px rgba(0,0,0,0.04);">
        <h6 style="font-weight:800; margin-bottom:12px;">Absen Hari Ini:</h6>
        @forelse($absenHariIni as $a)
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px dashed #eee; font-size:0.9rem;">
                <span style="font-weight:bold; color:{{ $a->tipe == 'masuk' ? '#198754' : '#dc3545' }}">
                    {{ strtoupper($a->tipe) }}
                </span>
                <span>{{ substr($a->jam, 0, 5) }} WIB</span>
            </div>
        @empty
            <div style="color:#888; font-size:0.85rem; text-align:center;">Belum ada absen hari ini.</div>
        @endforelse
    </div>

    <!-- Tombol -->
    <button class="btn-absen btn-masuk" onclick="mulaiAbsen('masuk')">
        <i class="bi bi-box-arrow-in-right"></i> ABSEN MASUK
    </button>
    <button class="btn-absen btn-pulang" onclick="mulaiAbsen('pulang')">
        <i class="bi bi-box-arrow-right"></i> ABSEN PULANG
    </button>
</div>

<!-- Modal Kamera -->
<div class="camera-modal" id="cameraModal">
    <div class="camera-preview">
        <video id="video" autoplay playsinline muted></video>
    </div>
    <div class="camera-controls">
        <button class="btn-cam btn-cam-cancel" onclick="tutupKamera()">Batal</button>
        <button class="btn-cam btn-cam-submit" id="btnJepret" onclick="jepretDanKirim()">📸 Jepret & Kirim</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. JAM DIGITAL
    setInterval(() => {
        const now = new Date();
        document.getElementById('liveClock').innerText = 
            String(now.getHours()).padStart(2, '0') + ':' +
            String(now.getMinutes()).padStart(2, '0') + ':' +
            String(now.getSeconds()).padStart(2, '0');
    }, 1000);

    let stream = null;
    let currentTipe = '';

    // 2. BUKA KAMERA
    async function mulaiAbsen(tipe) {
        currentTipe = tipe;
        const modal = document.getElementById('cameraModal');
        const video = document.getElementById('video');

        // Reset tombol
        const btnJepret = document.getElementById('btnJepret');
        btnJepret.disabled = false;
        btnJepret.innerText = '📸 Jepret & Kirim';

        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = stream;
            modal.classList.add('show');
        } catch (err) {
            alert('Gagal membuka kamera: ' + err.message);
        }
    }

    function tutupKamera() {
        if (stream) stream.getTracks().forEach(track => track.stop());
        document.getElementById('cameraModal').classList.remove('show');
    }

    // 3. JEPRET LANGSUNG KIRIM (1 KLIK)
    function jepretDanKirim() {
        const video = document.getElementById('video');
        
        if (video.videoWidth === 0) {
            alert('Kamera belum siap, tunggu sebentar lalu coba lagi.');
            return;
        }

        // Ambil gambar dari video
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        // Ubah ke gambar JPEG
        canvas.toBlob((blob) => {
            if (!blob) {
                alert('Gagal mengambil gambar.');
                return;
            }
            
            // Proses Kirim
            prosesKirim(blob);
            
        }, 'image/jpeg', 0.8);
    }

    // 4. PROSES KIRIM KE SERVER
    async function prosesKirim(imageBlob) {
        const btnJepret = document.getElementById('btnJepret');
        
        // Ubah tombol jadi loading
        btnJepret.disabled = true;
        btnJepret.innerText = 'Mengirim Data...';

        const formData = new FormData();
        formData.append('tipe', currentTipe);
        formData.append('foto', imageBlob, 'selfie.jpg');
        
        // Ambil CSRF Token
        const token = document.querySelector('meta[name="csrf-token"]');
        if(token) {
            formData.append('_token', token.getAttribute('content'));
        }

        // Deteksi URL route
        const routeUrl = '{{ auth()->user()->isTeknisiCctv() ? route("teknisi_cctv.absensi.store") : route("teknisi.absensi.store") }}';

        try {
            const res = await fetch(routeUrl, { 
                method: 'POST', 
                body: formData,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json' 
                }
            });
            
            const textResponse = await res.text();
            
            try {
                const data = JSON.parse(textResponse);
                if (data.success) {
                    alert('✅ ' + data.message);
                    tutupKamera();
                    window.location.reload(); // Refresh agar list absen muncul
                } else {
                    alert('❌ Gagal: ' + data.message);
                    btnJepret.disabled = false;
                    btnJepret.innerText = '📸 Jepret & Kirim';
                }
            } catch (e) {
                alert('ERROR SERVER:\n\n' + textResponse);
                btnJepret.disabled = false;
                btnJepret.innerText = '📸 Jepret & Kirim';
            }

        } catch (err) {
            alert('ERROR JARINGAN:\n' + err.message);
            btnJepret.disabled = false;
            btnJepret.innerText = '📸 Jepret & Kirim';
        }
    }
</script>
@endpush