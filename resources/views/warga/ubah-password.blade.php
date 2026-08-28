@extends('layouts.app')

@section('title', 'Ubah Kata Sandi - Portal Warga')

@push('styles')
<style>
    body {
        background: #f8fafc;
        padding-top: 85px;
        padding-bottom: 60px;
    }
    .card-password {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }
    .card-password-header {
        background: linear-gradient(135deg, #102c1e 0%, #198754 100%);
        color: white;
        padding: 28px;
    }
</style>
@endpush

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
                    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan:</strong>
                    <ul class="mb-0 small ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('warga.dashboard') }}" class="text-success text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ubah Kata Sandi</li>
                </ol>
            </nav>

            <div class="card-password">
                <div class="card-password-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-20 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-shield-lock-fill fs-3 text-warning"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Pengaturan Keamanan Akun</h4>
                            <p class="mb-0 text-white-50 small">Perbarui kata sandi Anda secara berkala untuk melindungi data pribadi</p>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <!-- Password Lama -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-bold small text-dark">
                                Kata Sandi Lama Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" name="current_password" id="current_password" class="form-control border-start-0 py-2 @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama Anda" required />
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small text-dark">
                                Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 py-2 @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required />
                            </div>
                            <small class="text-muted" style="font-size: 0.78rem;">Gunakan kombinasi huruf dan angka agar lebih aman.</small>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold small text-dark">
                                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 py-2" placeholder="Ketik ulang password baru Anda" required />
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('warga.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
