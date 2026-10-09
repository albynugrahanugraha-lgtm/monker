{{-- WIDGET TIKET HARI INI --}}
<div class="row g-2 mb-2">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small style="opacity: 0.9; font-size: .7rem;">📥 Tiket Masuk Hari Ini</small>
                    <h4 class="mb-0 fw-bold">{{ $tiket['hari'] ?? 0 }}</h4>
                </div>
                <i class="bi bi-calendar-day" style="font-size: 1.5rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>