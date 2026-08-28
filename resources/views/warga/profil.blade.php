@extends('layouts.app')

@section('title', 'Profil Saya')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .profile-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(135deg, #102c1e 0%, #1e3b2c 100%);
        color: white;
        padding: 30px;
    }

    .avatar-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #198754, #20c997);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .form-label {
        font-weight: 600;
        font-size: 0.88rem;
        color: #374151;
    }

    .form-control {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.92rem;
        border: 1.5px solid #e5e7eb;
    }

    .form-control:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 800px;">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-header">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-large">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1">Data Kependudukan Warga</span>
                    <h3 class="fw-bold mb-0 text-white">{{ $user->name }}</h3>
                    <small class="text-white-50">Username: {{ $user->username }} &bull; Terdaftar sejak {{ $user->created_at->format('d M Y') }}</small>
                </div>
            </div>
        </div>

        <div class="p-4 p-md-5">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                    <strong>Mohon periksa data yang dimasukkan:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('warga.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required />
                    </div>
                    <div class="col-md-6">
                        <label for="nik" class="form-label">Nomor Induk Kependudukan (NIK)</label>
                        <input type="text" name="nik" id="nik" maxlength="20" class="form-control" placeholder="16 digit NIK" value="{{ old('nik', $user->nik) }}" />
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Nomor WhatsApp / HP Aktif <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone', $user->phone) }}" required />
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required />
                    </div>
                </div>

                <div class="mb-4">
                    <label for="address" class="form-label">Alamat Jorong / Domisili di Koto Malintang <span class="text-danger">*</span></label>
                    <textarea name="address" id="address" rows="3" class="form-control" placeholder="Contoh: Jorong Muko-Muko, RT 02 / RW 01" required>{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <button type="button" class="btn btn-outline-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalUbahPasswordUniversal">
                        <i class="bi bi-key-fill text-warning me-1"></i> Ubah Kata Sandi
                    </button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-semibold shadow">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
