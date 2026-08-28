@extends('admin.layouts.admin')

@section('title', 'Ubah Kata Sandi Admin - Nagari Koto Malintang')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="bi bi-shield-lock-fill text-success me-2"></i>Ubah Kata Sandi Administrator</h3>
            <p class="text-muted mb-0">Kelola kredensial keamanan akses panel kontrol nagari</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4 shadow-sm" role="alert">
            <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal Menyimpan Password:</strong>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7 col-md-10">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <!-- Password Lama -->
                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-bold small text-dark">
                                Kata Sandi Lama Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-key"></i></span>
                                <input type="password" name="current_password" id="current_password" class="form-control border-start-0 py-2" placeholder="Masukkan password admin saat ini" required />
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small text-dark">
                                Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 py-2" placeholder="Minimal 6 karakter" required />
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold small text-dark">
                                Konfirmasi Kata Sandi Baru <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-check2-circle"></i></span>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-start-0 py-2" placeholder="Ulangi password baru" required />
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
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
