{{-- WIDGET TUGAS HARI INI --}}
<div class="row g-2 mb-2">
    <div class="col-6 col-md-6">
        <div class="card border-0 shadow-sm p-2" style="background: #e7e7e7; border-left: 5px solid #6c757d;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted" style="font-size: .75rem;">
                        <i class="bi bi-hourglass-split"></i> Belum Dikerjakan Hari Ini
                    </small>
                    <h3 class="mb-0 fw-bold text-secondary">{{ $hariIni['belum'] ?? 0 }}</h3>
                </div>
                <i class="bi bi-hourglass" style="font-size: 2rem; color: #6c757d; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6">
        <div class="card border-0 shadow-sm p-2" style="background: #d1e7dd; border-left: 5px solid #198754;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted" style="font-size: .75rem;">
                        <i class="bi bi-check-circle"></i> Sudah Dikerjakan Hari Ini
                    </small>
                    <h3 class="mb-0 fw-bold text-success">{{ $hariIni['selesai'] ?? 0 }}</h3>
                </div>
                <i class="bi bi-check-circle" style="font-size: 2rem; color: #198754; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>