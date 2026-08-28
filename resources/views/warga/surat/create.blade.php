@extends('layouts.app')

@section('title', 'Ajukan Permohonan Surat')

@push('styles')
<style>
    body {
        background: #f8f9fa;
        padding-top: 80px;
        padding-bottom: 60px;
    }

    .wizard-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #e9ecef;
        box-shadow: 0 10px 40px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .wizard-header {
        background: linear-gradient(135deg, #102c1e 0%, #1e3b2c 100%);
        color: white;
        padding: 28px 32px;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #198754;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e8f5e9;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #374151;
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.92rem;
        border: 1.5px solid #e5e7eb;
    }

    .form-control:focus, .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.15);
    }

    .upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        padding: 14px 16px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .upload-box:hover {
        border-color: #198754;
        background: #f0fdf4;
    }
</style>
@endpush

@section('content')
<div class="container" style="max-width: 900px;">
    <div class="wizard-card">
        <div class="wizard-header">
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Formulir Permohonan Online</span>
            <h3 class="fw-bold mb-1 text-white">Pengajuan Surat Nagari Koto Malintang</h3>
            <p class="text-white-50 mb-0 small">Lengkapi data dan berkas persyaratan di bawah ini untuk diterbitkan surat keterangan resmi berstempel</p>
        </div>

        <div class="p-4 p-md-5">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                    <strong>Mohon periksa kembali form pengajuan:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('warga.surat.store') }}" method="POST" enctype="multipart/form-data" id="formSurat">
                @csrf

                <!-- 1. PILIH JENIS SURAT -->
                <div class="form-section-title">
                    <i class="bi bi-file-earmark-check"></i> 1. Jenis Surat yang Diajukan
                </div>

                <div class="mb-4">
                    <label for="jenis_surat" class="form-label">Pilih Jenis Surat <span class="text-danger">*</span></label>
                    <select name="jenis_surat" id="jenis_surat" class="form-select @error('jenis_surat') is-invalid @enderror" onchange="toggleSpesifikFields()" required>
                        @foreach ($daftarSurat as $key => $surat)
                            <option value="{{ $key }}" {{ (old('jenis_surat', $selectedJenis) == $key) ? 'selected' : '' }}>
                                {{ $surat['nama'] }} ({{ $surat['singkatan'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 2. DATA IDENTITAS PEMOHON -->
                <div class="form-section-title">
                    <i class="bi bi-person-vcard"></i> 2. Data Identitas Pemohon
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="nama_pemohon" class="form-label">Nama Lengkap Pemohon <span class="text-danger">*</span></label>
                        <input type="text" name="nama_pemohon" id="nama_pemohon" class="form-control" value="{{ old('nama_pemohon', $user->name) }}" required />
                    </div>
                    <div class="col-md-6">
                        <label for="nik_pemohon" class="form-label">NIK Pemohon (Opsional)</label>
                        <input type="text" name="nik_pemohon" id="nik_pemohon" maxlength="20" class="form-control" placeholder="16 digit NIK jika ada" value="{{ old('nik_pemohon', $user->nik) }}" />
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir" class="form-control" placeholder="Contoh: Koto Malintang" value="{{ old('tempat_lahir') }}" required />
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required />
                    </div>
                    <div class="col-md-4">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="agama" class="form-label">Agama <span class="text-danger">*</span></label>
                        <select name="agama" id="agama" class="form-select" required>
                            <option value="Islam" {{ old('agama', 'Islam') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pekerjaan" class="form-label">Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" placeholder="Contoh: Petani / Wiraswasta / PNS" value="{{ old('pekerjaan') }}" required />
                    </div>
                </div>

                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat Lengkap / Jorong <span class="text-danger">*</span></label>
                    <textarea name="alamat" id="alamat" rows="2" class="form-control" required>{{ old('alamat', $user->address) }}</textarea>
                </div>

                <!-- 3. DATA SPESIFIK SESUAI JENIS SURAT -->
                <div class="form-section-title">
                    <i class="bi bi-list-stars"></i> 3. Keterangan Spesifik Surat
                </div>

                <!-- Field Khusus: Suket Usaha -->
                <div id="section_suket_usaha" class="spesifik-section mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Usaha / Toko</label>
                            <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Warung Kopi Nagari" value="{{ old('nama_usaha') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bidang / Jenis Usaha</label>
                            <input type="text" name="bidang_usaha" class="form-control" placeholder="Contoh: Kuliner & Minuman" value="{{ old('bidang_usaha') }}" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat / Lokasi Usaha</label>
                            <input type="text" name="alamat_usaha" class="form-control" placeholder="Contoh: Jl. Danau Maninjau No. 12, Jorong Muko-Muko" value="{{ old('alamat_usaha') }}" />
                        </div>
                    </div>
                </div>

                <!-- Field Khusus: Suket Meninggal -->
                <div id="section_suket_meninggal" class="spesifik-section mb-3" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap Almarhum / Almarhumah</label>
                            <input type="text" name="nama_almarhum" class="form-control" placeholder="Nama almarhum" value="{{ old('nama_almarhum') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Meninggal</label>
                            <input type="date" name="tanggal_meninggal" class="form-control" value="{{ old('tanggal_meninggal') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Meninggal</label>
                            <input type="text" name="tempat_meninggal" class="form-control" placeholder="Contoh: Rumah Duka / RSUD Lubuk Basung" value="{{ old('tempat_meninggal') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sebab Kematian</label>
                            <input type="text" name="sebab_meninggal" class="form-control" placeholder="Contoh: Sakit Menua" value="{{ old('sebab_meninggal') }}" />
                        </div>
                    </div>
                </div>

                <!-- Field Khusus: Surat Nikah NA -->
                <div id="section_surat_nikah_na" class="spesifik-section mb-3" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Calon Pasangan</label>
                            <input type="text" name="nama_calon_pasangan" class="form-control" placeholder="Nama calon suami / istri" value="{{ old('nama_calon_pasangan') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bin / Binti Pasangan</label>
                            <input type="text" name="bin_binti_pasangan" class="form-control" placeholder="Nama orang tua calon pasangan" value="{{ old('bin_binti_pasangan') }}" />
                        </div>
                    </div>
                </div>

                <!-- Field Khusus: Sket Penghasilan / SKTM -->
                <div id="section_sket_penghasilan" class="spesifik-section mb-3" style="display: none;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Estimasi Rata-rata Penghasilan per Bulan (Rp)</label>
                            <input type="number" name="nominal_penghasilan" class="form-control" placeholder="Contoh: 1500000" value="{{ old('nominal_penghasilan') }}" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sumber Penghasilan</label>
                            <input type="text" name="sumber_penghasilan" class="form-control" placeholder="Contoh: Hasil Bertani / Buruh Harian" value="{{ old('sumber_penghasilan') }}" />
                        </div>
                    </div>
                </div>

                <!-- 4. UPLOAD BERKAS PERSYARATAN -->
                <div class="form-section-title mt-4">
                    <i class="bi bi-cloud-arrow-up-fill"></i> 4. Upload Berkas Persyaratan (Google Drive / Storage)
                </div>
                <p class="small text-muted mb-3">Unggah foto atau dokumen persyaratan (Format: PDF, JPG, PNG, Maksimal 5MB per file).</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-person-badge text-success me-1"></i> Foto / Scan KTP Pemohon</label>
                            <input type="file" name="berkas_ktp" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Kartu Tanda Penduduk yang masih berlaku.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-people text-primary me-1"></i> Foto / Scan Kartu Keluarga (KK)</label>
                            <input type="file" name="berkas_kk" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Kartu Keluarga domisili Nagari Koto Malintang.</small>
                        </div>
                    </div>

                    <!-- Upload Berkas Spesifik -->
                    <div class="col-md-6 berkas-box" id="box_berkas_pengantar">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-file-earmark-text text-warning me-1"></i> Surat Pengantar Kepala Jorong</label>
                            <input type="file" name="berkas_pengantar" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Surat pengantar atau rekomendasi dari Kepala Jorong.</small>
                        </div>
                    </div>

                    <div class="col-md-6 berkas-box" id="box_berkas_foto_usaha">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-shop text-success me-1"></i> Foto Tempat / Aktivitas Usaha</label>
                            <input type="file" name="berkas_foto_usaha" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Foto toko / tempat jualan / produk usaha Anda.</small>
                        </div>
                    </div>

                    <div class="col-md-6 berkas-box" id="box_berkas_foto_rumah" style="display: none;">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-house-door text-danger me-1"></i> Foto Rumah / Tempat Tinggal</label>
                            <input type="file" name="berkas_foto_rumah" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Foto kondisi tempat tinggal tampak depan.</small>
                        </div>
                    </div>

                    <div class="col-md-6 berkas-box" id="box_berkas_kematian" style="display: none;">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-heartbreak text-danger me-1"></i> Surat Keterangan Kematian RS/Bidan</label>
                            <input type="file" name="berkas_kematian" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Surat keterangan kematian dari tenaga medis atau jorong.</small>
                        </div>
                    </div>

                    <div class="col-md-6 berkas-box" id="box_berkas_ktp_pasangan" style="display: none;">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-gem text-info me-1"></i> KTP Calon Pasangan</label>
                            <input type="file" name="berkas_ktp_pasangan" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Foto / scan KTP calon suami atau istri.</small>
                        </div>
                    </div>

                    <div class="col-md-6 berkas-box" id="box_berkas_slip" style="display: none;">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-cash-coin text-success me-1"></i> Slip Gaji / Surat Pernyataan</label>
                            <input type="file" name="berkas_slip" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Bukti slip penghasilan atau surat pernyataan pendapatan.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="upload-box">
                            <label class="form-label mb-1"><i class="bi bi-paperclip text-secondary me-1"></i> Dokumen Pendukung Tambahan (Opsional)</label>
                            <input type="file" name="berkas_lainnya" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png" />
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Sertifikat, surat pernyataan, atau lampiran pendukung lainnya.</small>
                        </div>
                    </div>
                </div>

                <!-- 5. KEPERLUAN -->
                <div class="form-section-title mt-4">
                    <i class="bi bi-question-circle"></i> 5. Keperluan Permohonan Surat
                </div>

                <div class="mb-4">
                    <label for="keperluan" class="form-label">Jelaskan Keperluan / Tujuan Surat Ini <span class="text-danger">*</span></label>
                    <textarea name="keperluan" id="keperluan" rows="3" class="form-control" placeholder="Contoh: Pengajuan pinjaman modal usaha KUR Bank Nagari / Pendaftaran Beasiswa Kuliah / Administrasi Pembuatan KTP" required>{{ old('keperluan') }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('warga.dashboard') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-semibold shadow">
                        <i class="bi bi-send-fill me-1"></i> Kirim Permohonan & Berkas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleSpesifikFields() {
        const jenis = document.getElementById('jenis_surat').value;
        
        // Hide all specific sections
        document.querySelectorAll('.spesifik-section').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.berkas-box').forEach(el => {
            if (el.id !== 'box_berkas_pengantar') el.style.display = 'none';
        });

        if (jenis === 'suket_usaha') {
            document.getElementById('section_suket_usaha').style.display = 'block';
            document.getElementById('box_berkas_foto_usaha').style.display = 'block';
        } else if (jenis === 'suket_meninggal') {
            document.getElementById('section_suket_meninggal').style.display = 'block';
            document.getElementById('box_berkas_kematian').style.display = 'block';
        } else if (jenis === 'surat_nikah_na') {
            document.getElementById('section_surat_nikah_na').style.display = 'block';
            document.getElementById('box_berkas_ktp_pasangan').style.display = 'block';
        } else if (jenis === 'sket_penghasilan') {
            document.getElementById('section_sket_penghasilan').style.display = 'block';
            document.getElementById('box_berkas_slip').style.display = 'block';
        } else if (jenis === 'suket_tidak_mampu') {
            document.getElementById('section_sket_penghasilan').style.display = 'block';
            document.getElementById('box_berkas_foto_rumah').style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleSpesifikFields();
    });
</script>
@endpush
@endsection
