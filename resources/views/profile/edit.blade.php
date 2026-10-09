@extends('layouts.app')
@section('title', 'Profile Saya')

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">

    {{-- INFO USER --}}
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body d-flex align-items-center">
            <div class="me-3" style="font-size: 3rem; color: #0d6efd;">
                <i class="bi bi-person-circle"></i>
            </div>
            <div>
                <h5 class="mb-1 fw-bold">{{ auth()->user()->display_name }}</h5>
                <div class="text-muted small">
                    <i class="bi bi-person-badge"></i>
                    <span class="badge bg-secondary">{{ auth()->user()->role }}</span>
                    &nbsp;
                    <i class="bi bi-envelope"></i> {{ auth()->user()->email }}
                </div>
            </div>
        </div>
    </div>

    {{-- FORM GANTI PASSWORD --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="bi bi-key-fill"></i>
                Ganti Password
            </h5>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Password Lama <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="current_password"
                               id="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               required>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('current_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" name="password"
                               id="new_password"
                               class="form-control @error('password') is-invalid @enderror"
                               required minlength="6">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('new_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter.</small>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Konfirmasi Password Baru <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" name="password_confirmation"
                               id="confirm_password"
                               class="form-control"
                               required minlength="6">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePass('confirm_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="alert alert-info small">
                    <i class="bi bi-info-circle-fill"></i>
                    Setelah ganti password, kamu tetap login. Password baru berlaku untuk login berikutnya.
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-warning flex-fill">
                        <i class="bi bi-check-circle"></i> Simpan Password Baru
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endpush