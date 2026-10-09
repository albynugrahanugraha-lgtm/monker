<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() ?? 0 }}">
    <title>@yield('title', 'Monitoring Pekerjaan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar {
            padding-top: 4px !important;
            padding-bottom: 4px !important;
            min-height: 45px;
        }
        .navbar-brand {
            font-size: 1rem !important;
            padding: 0 !important;
        }
        .navbar .nav-link {
            padding: 4px 10px !important;
            font-size: .85rem;
        }
        .navbar .badge {
            font-size: .65rem;
            padding: 2px 6px;
        }
        .notif-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            font-size: 9px;
            padding: 2px 5px;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        .notif-pulse { animation: pulse 1.5s infinite; }

        /* ===== BANNER AKTIFKAN SUARA ===== */
        #audioBanner {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #000;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: .9rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            font-weight: 600;
            border: 2px solid #ff9800;
            display: none;
        }
        #audioBanner:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.4);
        }
        #audioBanner.unlocked {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border-color: #38f9d7;
        }

        /* ===== DROPDOWN MENU LAPORAN ===== */
        .dropdown-menu {
            min-width: 260px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-radius: 10px;
            padding: 6px;
        }
        .dropdown-menu .dropdown-header {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #6c757d;
            font-weight: 700;
            padding: 8px 12px 4px;
        }
        .dropdown-menu .dropdown-item {
            padding: 8px 12px;
            border-radius: 6px;
            transition: all .15s;
            font-size: .85rem;
        }
        .dropdown-menu .dropdown-item:hover {
            background: #f0f7ff;
            transform: translateX(3px);
        }
        .dropdown-menu .dropdown-item i {
            font-size: 1.1rem;
            margin-right: 8px;
            vertical-align: middle;
        }
        .dropdown-menu .dropdown-item small {
            display: block;
            padding-left: 30px;
            margin-top: -2px;
        }

        /* 🆕 User dropdown */
        .user-dropdown .dropdown-toggle {
            color: #fff !important;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .user-dropdown .dropdown-toggle::after {
            color: #fff;
        }
        .user-dropdown .dropdown-menu {
            min-width: 240px;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-clipboard-check"></i>
            <span class="d-none d-sm-inline">Monitoring Pekerjaan</span>
            <span class="d-sm-none">MP</span>
        </a>

        @auth
        @php
            $user = auth()->user();
            $notifCount = 0;
            $notifLink = '';

            if ($user->isTeknisi()) {
                $notifCount = \App\Models\Job::where('teknisi_id', $user->id)
                    ->where('status', 'DITUGASKAN')->count();
                $notifLink = route('teknisi.dashboard');
            } elseif ($user->isTeknisiCctv()) {
                $notifCount = \App\Models\Job::where('teknisi_id', $user->id)
                    ->where('status', 'DITUGASKAN')->count();
                $notifLink = route('teknisi_cctv.dashboard');
            } elseif ($user->isAdmin()) {
                $notifCount = \App\Models\Job::whereDate('tanggal_selesai', today())->count();
                $notifLink = route('admin.dashboard');
            } elseif ($user->isSuperAdmin()) {
                $notifCount = \App\Models\Job::whereDate('tanggal_selesai', today())->count();
                $notifLink = route('superadmin.dashboard');
            }
        @endphp

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="d-flex align-items-center text-white order-lg-3 ms-auto me-2">
            {{-- Notifikasi Bell --}}
            <a href="{{ $notifLink }}" class="text-white me-2 position-relative" style="text-decoration:none;">
                <i class="bi bi-bell-fill" style="font-size: 1.1rem;"></i>
                @if($notifCount > 0)
                    <span class="badge bg-danger rounded-pill notif-badge notif-pulse">
                        {{ $notifCount }}
                    </span>
                @endif
            </a>

            {{-- 🆕 DROPDOWN USER (Profile + Logout) --}}
            <div class="dropdown user-dropdown">
                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i>
                    <span class="d-none d-lg-inline small">{{ $user->display_name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <h6 class="dropdown-header">
                            <i class="bi bi-person-circle"></i>
                            {{ $user->display_name }}
                            <br>
                            <span class="badge bg-primary mt-1">{{ $user->role }}</span>
                        </h6>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear text-primary"></i>
                            <strong>Profile Saya</strong>
                            <small class="text-muted">Ganti password</small>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit">
                                <i class="bi bi-box-arrow-right"></i>
                                <strong>Logout</strong>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="collapse navbar-collapse order-lg-2" id="navMenu">
            <ul class="navbar-nav me-auto">

                {{-- ADMIN --}}
                @if($user->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.buat') }}">
                            <i class="bi bi-plus-circle"></i> Buat Tugas
                        </a>
                    </li>
                @endif

                {{-- TEKNISI KOMPUTER --}}
                @if($user->isTeknisi())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teknisi.dashboard') }}">
                            <i class="bi bi-pc-display"></i> Tugas Komputer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teknisi.buat') }}">
                            <i class="bi bi-plus-circle"></i> Buat Tugas
                        </a>
                    </li>
                @endif

                {{-- TEKNISI CCTV --}}
                @if($user->isTeknisiCctv())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teknisi_cctv.dashboard') }}">
                            <i class="bi bi-camera-video"></i> Tugas CCTV
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('teknisi_cctv.buat') }}">
                            <i class="bi bi-plus-circle"></i> Buat Tugas
                        </a>
                    </li>
                @endif

                {{-- SUPER ADMIN --}}
                @if($user->isSuperAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('superadmin.dashboard') }}">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                @endif

                {{-- LAPORAN --}}
                @if($user->isAdmin() || $user->isSuperAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                        </a>
                        <ul class="dropdown-menu shadow-sm">
                            <li>
                                <h6 class="dropdown-header">📋 Pilih Bidang</h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('laporan.index', ['bidang' => 'komputer']) }}">
                                    <i class="bi bi-pc-display text-primary"></i>
                                    <strong>Laporan Komputer</strong>
                                    <small class="text-muted">Pekerjaan PC / Jaringan</small>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('laporan.index', ['bidang' => 'cctv']) }}">
                                    <i class="bi bi-camera-video text-info"></i>
                                    <strong>Laporan CCTV</strong>
                                    <small class="text-muted">Pekerjaan Kamera / DVR</small>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('laporan.index') }}">
                                    <i class="bi bi-files text-secondary"></i>
                                    <strong>Semua Laporan</strong>
                                    <small class="text-muted">Komputer + CCTV digabung</small>
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('laporan.index') }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                        </a>
                    </li>
                @endif

            </ul>

            {{-- Mobile user info --}}
            <div class="d-lg-none border-top border-light pt-2 mt-2 text-white">
                <div class="d-flex justify-content-between align-items-center px-2">
                    <span class="small">
                        <i class="bi bi-person-circle"></i>
                        <strong>{{ $user->display_name }}</strong>
                        <small class="badge bg-light text-dark ms-1">{{ $user->role }}</small>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button class="btn btn-sm btn-light">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</nav>

<div class="container my-3 my-md-4">

    {{-- BANNER AKTIFKAN SUARA --}}
    @auth
    <div id="audioBanner" onclick="unlockAudio()">
        🔊 <strong>Klik di sini untuk mengaktifkan suara notifikasi</strong>
    </div>
    @endauth

    @auth
        @if($notifCount > 0)
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center">
                <i class="bi bi-bell-fill me-2" style="font-size: 1.2rem;"></i>
                <div class="small">
                    <strong>Pemberitahuan:</strong>
                    @if(auth()->user()->isAnyTeknisi())
                        Anda memiliki <strong>{{ $notifCount }} tugas baru</strong>.
                    @else
                        Ada <strong>{{ $notifCount }} pekerjaan</strong> selesai hari ini.
                    @endif
                </div>
                <button class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endauth

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@auth
<script>
    // ============================================================
    // SETUP AUDIO
    // ============================================================
    const userId = document.querySelector('meta[name="user-id"]').content;
    const userKey = 'lastNotifCount_' + userId;
    const serverCount = {{ $notifCount }};

    let audioCtx = null;
    let audioBuffer = null;
    let audioReady = false;
    let audioUnlocked = false;

    const banner = document.getElementById('audioBanner');
    const isUnlockedInStorage = localStorage.getItem('audioUnlocked_' + userId) === 'true';

    if (!isUnlockedInStorage && banner) {
        banner.style.display = 'block';
    }

    async function initAudio() {
        try {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const response = await fetch('{{ asset("audio/beep.wav") }}');
            if (!response.ok) {
                console.log('❌ File beep.wav tidak ditemukan. Status:', response.status);
                return;
            }
            const arrayBuffer = await response.arrayBuffer();
            audioBuffer = await audioCtx.decodeAudioData(arrayBuffer);
            audioReady = true;
            console.log('✅ Audio siap!');
        } catch (err) {
            console.log('❌ Audio error:', err);
        }
    }

    initAudio();

    function unlockAudio() {
        if (!audioCtx) {
            console.log('⚠️ Audio Context belum dibuat, coba lagi...');
            initAudio();
            return;
        }

        audioCtx.resume().then(() => {
            audioUnlocked = true;
            localStorage.setItem('audioUnlocked_' + userId, 'true');

            if (banner) {
                banner.classList.add('unlocked');
                banner.innerHTML = '✅ <strong>Suara notifikasi sudah aktif!</strong>';
                setTimeout(() => {
                    banner.style.display = 'none';
                }, 3000);
            }

            console.log('🔓 Audio unlocked!');
        }).catch(err => {
            console.log('❌ Gagal unlock:', err);
        });
    }

    document.addEventListener('click', function() {
        if (!audioUnlocked) unlockAudio();
    }, { once: true });

    document.addEventListener('keydown', function() {
        if (!audioUnlocked) unlockAudio();
    }, { once: true });

    document.addEventListener('touchstart', function() {
        if (!audioUnlocked) unlockAudio();
    }, { once: true });

    function playBeep() {
        if (!audioReady || !audioCtx || !audioBuffer) {
            console.log('⚠️ Audio belum siap');
            return;
        }

        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }

        try {
            const source = audioCtx.createBufferSource();
            source.buffer = audioBuffer;
            source.connect(audioCtx.destination);
            source.start(0);
            console.log('🔊 Beep!');
        } catch (err) {
            console.log('❌ Gagal play beep:', err);
        }
    }

    const savedCount = localStorage.getItem(userKey);
    let lastNotifCount;

    if (savedCount === null) {
        lastNotifCount = 0;
    } else {
        lastNotifCount = parseInt(savedCount, 10);
    }

    let isFirstCheck = true;

    setInterval(function() {
        fetch('{{ route("notif.check") }}')
            .then(res => res.json())
            .then(data => {
                const badge = document.querySelector('.notif-badge');
                const beepValue = typeof data.beep !== 'undefined' ? data.beep : data.count;

                if (badge && data.count > 0) {
                    badge.textContent = data.count;
                }

                if (beepValue > lastNotifCount && !isFirstCheck) {
                    playBeep();
                    setTimeout(playBeep, 500);
                    setTimeout(playBeep, 1000);

                    localStorage.setItem(userKey, beepValue);
                    lastNotifCount = beepValue;

                    setTimeout(() => location.reload(), 3000);
                } else {
                    lastNotifCount = beepValue;
                    localStorage.setItem(userKey, beepValue);
                }

                isFirstCheck = false;
            })
            .catch(err => console.log('❌ Notif error:', err));
    }, 5000);

    window.playBeep = playBeep;
    window.unlockAudio = unlockAudio;
</script>
@endauth

@stack('scripts')
</body>
</html>