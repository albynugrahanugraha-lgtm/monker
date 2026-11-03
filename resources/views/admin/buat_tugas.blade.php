@extends('layouts.app')
@section('title', 'Buat Tugas')

@section('content')
<style>
    /* ============================================
       PWA STYLES & ANTI-GAGAL
       ============================================ */
    body { background-color: #f2f4f8 !important; }
    .app-header-pwa {
        background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
        border-radius: 24px; padding: 24px; color: white !important;
        box-shadow: 0 10px 25px rgba(13, 110, 253, 0.3);
        margin-bottom: 24px;
    }
    .app-header-pwa h1 { font-size: 1.4rem; font-weight: 800; margin: 0; color: white !important; }
    .app-header-pwa p { font-size: 0.85rem; opacity: 0.9; margin: 4px 0 0 0; color: white !important; }
    
    .btn-header-pwa {
        background: rgba(255, 255, 255, 0.2) !important; border: 1px solid rgba(255, 255, 255, 0.4) !important;
        color: white !important; border-radius: 12px; padding: 8px 16px;
        font-size: 0.85rem; font-weight: 700; text-decoration: none !important;
    }

    .form-card-pwa {
        background: white !important; border-radius: 20px; padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;
    }
    .form-label-pwa { font-size: 0.8rem; font-weight: 700; color: #333 !important; margin-bottom: 8px; display: block; }
    .form-control-pwa {
        width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e0e0e0 !important;
        background: #f9f9f9 !important; font-size: 0.95rem; color: #1a1a1a !important;
    }
    .form-control-pwa:focus { border-color: #0d6efd !important; background: white !important; outline: none; }

    .teknisi-wrapper {
        max-height: 250px; overflow-y: auto; background: #f8f9fa; border-radius: 12px; padding: 10px; border: 1px solid #e0e0e0;
    }
    .form-check-label { font-size: 0.9rem; }

    .btn-submit-pwa {
        width: 100%; padding: 14px; border-radius: 12px; border: none;
        background: #0d6efd !important; color: white !important; font-size: 1rem; font-weight: 800;
        margin-top: 10px; cursor: pointer; transition: transform 0.15s;
    }
    .btn-submit-pwa:active { transform: scale(0.98); }

    /* ============================================
       BOTTOM NAVIGATION (HANYA UNTUK HP)
       ============================================ */
    .pwa-bottom-nav {
        display: none;
        position: fixed; bottom: 0; left: 0; right: 0; background: white;
        border-top: 1px solid #f0f0f0; justify-content: space-around;
        padding: 10px 0 max(10px, env(safe-area-inset-bottom)); z-index: 1050;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
    }
    .pwa-bottom-nav .nav-item {
        flex: 1; text-align: center; padding: 6px 0; color: #adb5bd;
        text-decoration: none !important; font-size: 0.65rem; font-weight: 700;
        display: flex; flex-direction: column; align-items: center; gap: 4px;
    }
    .pwa-bottom-nav .nav-item i { font-size: 1.4rem; }
    .pwa-bottom-nav .nav-item.active { color: #0d6efd; }

    @media (max-width: 767.98px) {
        .pwa-bottom-nav { display: flex; }
        body { padding-bottom: 85px !important; }
    }
</style>

<div class="container-fluid px-0">

    {{-- HEADER --}}
    <div class="app-header-pwa">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-plus-circle"></i> Buat Tugas</h1>
                <p>Tambahkan pekerjaan baru untuk teknisi</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="btn-header-pwa"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>

    {{-- FORM --}}
    <div class="form-card-pwa">
        <form method="POST" action="{{ route('admin.store') }}">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger" style="border-radius: 12px; font-size: 0.85rem;">
                    <strong><i class="bi bi-exclamation-triangle"></i> Ada kesalahan:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- BIDANG --}}
            <div class="mb-4">
                <label class="form-label-pwa">Bidang Pekerjaan <span class="text-danger">*</span></label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bidang" id="bidangKomputer" value="komputer" @checked(old('bidang', 'komputer') === 'komputer')>
                        <label class="form-check-label fw-bold" for="bidangKomputer">
                            <i class="bi bi-pc-display text-primary"></i> Komputer / Jaringan
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="bidang" id="bidangCctv" value="cctv" @checked(old('bidang') === 'cctv')>
                        <label class="form-check-label fw-bold" for="bidangCctv">
                            <i class="bi bi-camera-video text-info"></i> CCTV
                        </label>
                    </div>
                </div>
            </div>

            {{-- JUDUL --}}
            <div class="mb-3">
                <label class="form-label-pwa">Judul Pekerjaan <span class="text-danger">*</span></label>
                <input name="judul" class="form-control-pwa" value="{{ old('judul') }}" required placeholder="Contoh: Perbaikan Jaringan Lantai 2">
            </div>

            {{-- MULTI TEKNISI --}}
            <div class="mb-3">
                <label class="form-label-pwa">
                    <i class="bi bi-people-fill"></i> Pilih Teknisi <span class="text-muted" style="font-weight:normal;">(bisa > 1)</span>
                </label>
                <div class="teknisi-wrapper" id="teknisiWrapper">
                    <div class="text-muted small p-2" id="teknisiEmpty">Pilih bidang dulu untuk menampilkan teknisi.</div>
                    @foreach($teknisiList as $t)
                        <div class="form-check teknisi-item mb-2" data-bidang="{{ $t->bidang() }}" style="display:none;">
                            <input class="form-check-input" type="checkbox" name="teknisi_ids[]" value="{{ $t->id }}" id="teknisi_{{ $t->id }}" @checked(in_array($t->id, old('teknisi_ids', [])))>
                            <label class="form-check-label" for="teknisi_{{ $t->id }}">
                                <i class="bi bi-person-circle text-primary"></i> <strong>{{ $t->display_name }}</strong>
                                <small class="text-muted">({{ $t->role === 'teknisi_cctv' ? 'CCTV' : 'Komputer' }})</small>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" class="btn btn-sm btn-outline-primary fw-bold" onclick="checkAllTeknisi(true)">Pilih Semua</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-bold" onclick="checkAllTeknisi(false)">Hapus</button>
                    <span class="ms-auto small text-muted align-self-center fw-bold">Terpilih: <span id="teknisiCount">0</span></span>
                </div>

                <div class="alert alert-info mt-3 mb-0 small" id="infoClone" style="display:none; border-radius:10px;">
                    <i class="bi bi-info-circle-fill"></i> <strong>Akan dibuat <span id="cloneCount">0</span> tiket terpisah.</strong>
                </div>
                <small class="text-muted d-block mt-2">Kosongkan kalau ingin teknisi ambil sendiri.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Kategori</label>
                    <input name="kategori" class="form-control-pwa" value="{{ old('kategori') }}" placeholder="CCTV / Switch / AP / PC">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Prioritas</label>
                    <select name="prioritas" class="form-control-pwa">
                        <option>Rendah</option>
                        <option selected>Sedang</option>
                        <option>Tinggi</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Tanggal Tugas</label>
                    <input type="date" name="tanggal_tugas" class="form-control-pwa" value="{{ old('tanggal_tugas', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Lokasi Gedung</label>
                    <input name="lok_gedung" class="form-control-pwa" value="{{ old('lok_gedung') }}" placeholder="Gedung A">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Lokasi Ruang</label>
                    <input name="lokasi_ruang" class="form-control-pwa" value="{{ old('lokasi_ruang') }}" placeholder="Ruang IT">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Nama Perangkat</label>
                    <input name="computer_name" class="form-control-pwa" value="{{ old('computer_name') }}" placeholder="PC-IT-01 / CAM-01">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-pwa">Merk</label>
                    <input name="merk" class="form-control-pwa" value="{{ old('merk') }}" placeholder="Lenovo / Hikvision">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label-pwa">Deskripsi / Keluhan</label>
                <textarea name="deskripsi" class="form-control-pwa" rows="3" placeholder="Jelaskan detail masalahnya...">{{ old('deskripsi') }}</textarea>
            </div>

            <button type="submit" class="btn-submit-pwa"><i class="bi bi-save"></i> Simpan Tugas</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light w-100 mt-2 fw-bold" style="border-radius: 12px; padding: 12px; color: #666 !important;">Batal</a>
        </form>
    </div>

</div>

{{-- BOTTOM NAVIGATION UNTUK HP --}}
<div class="pwa-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Beranda</span>
    </a>
    <a href="{{ route('admin.buat') }}" class="nav-item active">
        <i class="bi bi-plus-square"></i>
        <span>Buat</span>
    </a>
    <a href="{{ route('admin.belum-dikerjakan') }}" class="nav-item">
        <i class="bi bi-hourglass-split"></i>
        <span>Belum</span>
    </a>
    <a href="{{ route('laporan.index') }}" class="nav-item">
        <i class="bi bi-bar-chart-fill"></i>
        <span>Laporan</span>
    </a>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        const radios = document.querySelectorAll('input[name="bidang"]');
        const items = document.querySelectorAll('.teknisi-item');
        const emptyMsg = document.getElementById('teknisiEmpty');
        const countEl = document.getElementById('teknisiCount');
        const infoClone = document.getElementById('infoClone');
        const cloneCount = document.getElementById('cloneCount');

        function filter() {
            const picked = document.querySelector('input[name="bidang"]:checked');
            if (!picked) return;
            const bidang = picked.value;

            let visible = 0;
            items.forEach(function(el) {
                const isMatch = el.dataset.bidang === bidang;
                el.style.display = isMatch ? 'block' : 'none';

                if (!isMatch) {
                    const cb = el.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = false;
                } else {
                    visible++;
                }
            });

            emptyMsg.style.display = visible > 0 ? 'none' : 'block';
            updateCount();
        }

        function updateCount() {
            const checked = document.querySelectorAll('.teknisi-item input:checked').length;
            countEl.textContent = checked;

            if (checked >= 2) {
                infoClone.style.display = 'block';
                cloneCount.textContent = checked;
            } else {
                infoClone.style.display = 'none';
            }
        }

        window.checkAllTeknisi = function(state) {
            items.forEach(function(el) {
                if (el.style.display !== 'none') {
                    const cb = el.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = state;
                }
            });
            updateCount();
        };

        items.forEach(function(el) {
            const cb = el.querySelector('input[type="checkbox"]');
            if (cb) cb.addEventListener('change', updateCount);
        });

        radios.forEach(r => r.addEventListener('change', filter));
        filter();
    })();
</script>
@endpush