@extends('layouts.app')

@section('title', 'Masuk Akun')

@push('styles')
<style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f1d10 0%, #1e3a20 40%, #2c5530 100%);
        padding-top: 90px;
        padding-bottom: 50px;
    }

    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 160px);
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
        position: relative;
    }

    .auth-header {
        background: linear-gradient(135deg, #1e6b3c 0%, #2c5530 100%);
        padding: 32px 28px 24px;
        text-align: center;
        position: relative;
        color: white;
    }

    .auth-header::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 18px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 18px 18px 0 0;
    }

    .logo-circle {
        width: 68px;
        height: 68px;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .auth-body {
        padding: 24px 32px 36px;
    }

    .form-label-custom {
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
        margin-bottom: 6px;
    }

    .input-wrap {
        position: relative;
    }

    .input-wrap i.icon-left {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1.1rem;
        pointer-events: none;
    }

    .input-custom {
        width: 100%;
        padding: 12px 14px 12px 42px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.92rem;
        background: #fafafa;
        transition: all 0.2s ease;
    }

    .input-custom:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
        background: white;
        outline: none;
    }

    .btn-submit-auth {
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
    }

    .btn-submit-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(25, 135, 84, 0.4);
        color: white;
    }

    .demo-badge-btn {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .demo-badge-btn:hover {
        transform: translateY(-1px);
        filter: brightness(0.92);
    }
</style>
@endpush

@section('content')
<div class="container auth-container">
    <div class="auth-card">
        <!-- Header -->
        <div class="auth-header">
            <div class="logo-circle">
                <img src="{{ asset('image/logo agam.png') }}" alt="Logo Agam" width="38" />
            </div>
            <h4 class="fw-bold mb-1">Masuk Akun</h4>
            <p class="small text-white-50 mb-0">Portal Pelayanan Nagari Koto Malintang</p>
        </div>

        <!-- Body Form -->
        <div class="auth-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show small" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small mb-3" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <!-- Email / Username / NIK -->
                <div class="mb-3">
                    <label for="login_id" class="form-label-custom">Email, Username, atau NIK</label>
                    <div class="input-wrap">
                        <i class="bi bi-person-fill icon-left"></i>
                        <input type="text" name="login_id" id="login_id" class="input-custom @error('login_id') is-invalid @enderror" placeholder="Contoh: admin, walinagari, atau warga" value="{{ old('login_id') }}" required autofocus />
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label-custom">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill icon-left"></i>
                        <input type="password" name="password" id="password" class="input-custom @error('password') is-invalid @enderror" placeholder="Masukkan password Anda" required />
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <!-- Bantuan Akun Cepat / Demo -->
                <div class="p-2 mb-3 bg-light rounded-3 border text-center">
                    <small class="text-muted d-block mb-1 fw-bold" style="font-size: 0.75rem;">⚡ Klik untuk Masuk Cepat Akun Demo (Password: <code>password</code>):</small>
                    <div class="d-flex justify-content-center gap-1 flex-wrap">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 demo-badge-btn px-2 py-1" onclick="fillLogin('admin', 'password')">
                            👑 Admin
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 demo-badge-btn px-2 py-1" onclick="fillLogin('walinagari', 'password')">
                            🏛️ Wali Nagari
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-dark border demo-badge-btn px-2 py-1" onclick="fillLogin('warga', 'password')">
                            👤 Warga
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit-auth mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </button>

                <div class="text-center mt-3 pt-3 border-top">
                    <p class="small text-muted mb-0">
                        Belum memiliki akun warga? 
                        <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Daftar di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fillLogin(username, password) {
    document.getElementById('login_id').value = username;
    document.getElementById('password').value = password;
}
</script>
@endpush
@endsection
