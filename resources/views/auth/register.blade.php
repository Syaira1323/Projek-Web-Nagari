@extends('layouts.app')

@section('title', 'Daftar Akun Warga')

@push('styles')
<style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f1d10 0%, #1e3a20 40%, #2c5530 100%);
        padding-top: 90px;
        padding-bottom: 60px;
    }

    .auth-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
        width: 100%;
        max-width: 520px;
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
            <h4 class="fw-bold mb-1">Pendaftaran Akun Warga</h4>
            <p class="small text-white-50 mb-0">Daftar mudah dalam 1 menit & nikmati layanan nagari</p>
        </div>

        <!-- Body Form -->
        <div class="auth-body">
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

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <!-- Nama Lengkap -->
                <div class="mb-3">
                    <label for="name" class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <i class="bi bi-person-fill icon-left"></i>
                        <input type="text" name="name" id="name" class="input-custom @error('name') is-invalid @enderror" placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required autofocus />
                    </div>
                </div>

                <!-- Username & WhatsApp -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label-custom">Username <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="bi bi-at icon-left"></i>
                            <input type="text" name="username" id="username" class="input-custom @error('username') is-invalid @enderror" placeholder="username_anda" value="{{ old('username') }}" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label-custom">No. WhatsApp / HP <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="bi bi-whatsapp icon-left text-success"></i>
                            <input type="text" name="phone" id="phone" class="input-custom @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('phone') }}" required />
                        </div>
                    </div>
                </div>

                <!-- Alamat Jorong (Sederhana) -->
                <div class="mb-3">
                    <label for="address" class="form-label-custom">Alamat / Jorong Asal (Opsional)</label>
                    <div class="input-wrap">
                        <i class="bi bi-geo-alt-fill icon-left text-danger"></i>
                        <input type="text" name="address" id="address" class="input-custom" placeholder="Contoh: Jorong Muko-Muko" value="{{ old('address') }}" />
                    </div>
                </div>

                <!-- Password & Konfirmasi -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="password" class="form-label-custom">Password <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="bi bi-lock-fill icon-left"></i>
                            <input type="password" name="password" id="password" class="input-custom @error('password') is-invalid @enderror" placeholder="Min. 6 karakter" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label-custom">Ulangi Password <span class="text-danger">*</span></label>
                        <div class="input-wrap">
                            <i class="bi bi-shield-lock-fill icon-left"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input-custom" placeholder="Ketik ulang" required />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit-auth mb-3">
                    <i class="bi bi-person-check-fill me-1"></i> Buat Akun Sekarang
                </button>

                <div class="text-center mt-3 pt-3 border-top">
                    <p class="small text-muted mb-0">
                        Sudah memiliki akun? 
                        <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Masuk di sini</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
