@extends('admin.layouts.admin')

@section('title', 'Verifikasi Surat: ' . $surat->nama_pemohon)
@section('page-title', 'Pemeriksaan, Penomoran & Verifikasi Surat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">{{ $surat->nama_jenis_surat }}</h4>
        <span class="badge bg-{{ $surat->status_badge_color }} rounded-pill px-3 py-1">
            Status: {{ $surat->status_label }}
        </span>
    </div>
    <a href="{{ route('admin.surat.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Detail Pemohon & Berkas Persyaratan -->
    <div class="col-lg-4">
        <!-- Identitas Pemohon -->
        <div class="card-custom mb-4">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-success me-2"></i>Data Pemohon</h5>

            <div class="mb-3">
                <small class="text-muted d-block">Nama Lengkap:</small>
                <strong class="text-dark">{{ $surat->nama_pemohon }}</strong>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">NIK:</small>
                <strong class="text-dark">{{ $surat->nik_pemohon }}</strong>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Tempat, Tgl Lahir:</small>
                <span>{{ $surat->tempat_lahir }}, {{ $surat->tanggal_lahir ? $surat->tanggal_lahir->format('d/m/Y') : '-' }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Jenis Kelamin / Agama:</small>
                <span>{{ $surat->jenis_kelamin }} / {{ $surat->agama }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Pekerjaan:</small>
                <span>{{ $surat->pekerjaan }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Alamat Jorong:</small>
                <span>{{ $surat->alamat }}</span>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Keperluan:</small>
                <div class="p-2 bg-light rounded small text-dark mt-1">{{ $surat->keperluan }}</div>
            </div>

            @if ($surat->data_spesifik && count($surat->data_spesifik) > 0)
                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-bold text-success mb-2 small text-uppercase">Data Khusus Permohonan:</h6>
                    <ul class="list-unstyled small mb-0">
                        @foreach ($surat->data_spesifik as $k => $v)
                            <li class="mb-1">
                                <span class="text-muted">{{ ucwords(str_replace('_', ' ', $k)) }}:</span>
                                <strong>{{ is_array($v) ? json_encode($v) : $v }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Berkas Persyaratan Terlampir -->
        <div class="card-custom">
            <h5 class="fw-bold text-dark mb-3">
                <i class="bi bi-paperclip text-primary me-2"></i>Berkas Persyaratan ({{ $surat->berkasPersyaratans->count() }})
            </h5>

            @if ($surat->berkasPersyaratans->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach ($surat->berkasPersyaratans as $berkas)
                        <div class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <div class="text-truncate me-2">
                                <strong class="d-block small text-dark">{{ $berkas->label_jenis }}</strong>
                                <small class="text-muted text-truncate d-block" style="font-size: 0.75rem;">
                                    {{ $berkas->nama_file_asli }} ({{ $berkas->formatted_size }})
                                </small>
                            </div>
                            <a href="{{ route('warga.berkas.download', $berkas->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-download me-1"></i> Unduh
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-file-earmark-x fs-2 d-block mb-1 text-secondary"></i>
                    Tidak ada berkas yang diunggah.
                </div>
            @endif
        </div>
    </div>

    <!-- Kolom Kanan: Penomoran, AI Assistant & Editor Redaksi Surat -->
    <div class="col-lg-8">
        <div class="card-custom">
            <!-- Header Card -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-text text-success me-2"></i>Naskah Surat Resmi</h5>
                    <p class="text-muted small mb-0">Periksa isi surat, tentukan nomor surat resmi, dan teruskan ke meja Wali Nagari.</p>
                </div>
            </div>

            <!-- FITUR AI ASSISTANT BOX -->
            <div class="p-3 mb-4 rounded-3 border bg-gradient" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border-color: #86efac !important;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-75 rounded-circle p-2">
                            <i class="bi bi-robot text-white fs-6"></i>
                        </span>
                        <span class="fw-bold text-dark">AI Surat Assistant (Transformer / Hugging Face)</span>
                    </div>
                    <span id="aiLoadingBadge" class="badge bg-primary text-white d-none">
                        <span class="spinner-border spinner-border-sm me-1"></span> Sedang Menyusun Naskah...
                    </span>
                </div>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" id="aiCustomPrompt" class="form-control" placeholder="Instruksi khusus ke AI (Opsional, cth: 'Buat lebih formal dan sebutkan masa berlaku')...">
                    <button type="button" class="btn btn-success fw-semibold" id="btnAiGenerate">
                        <i class="bi bi-magic me-1"></i> Generate Ulang
                    </button>
                    <button type="button" class="btn btn-outline-primary fw-semibold" id="btnAiRefine">
                        <i class="bi bi-pencil-square me-1"></i> Rapikan Redaksi
                    </button>
                </div>
                <small class="text-muted" style="font-size: 0.8rem;">
                    💡 <em>Tip: Klik tombol di atas untuk meminta AI menyusun ulang atau merapikan bahasa naskah surat secara otomatis.</em>
                </small>
            </div>

            <!-- FORM VERIFIKASI & PENOMORAN -->
            <form action="{{ route('admin.surat.verify', $surat->id) }}" method="POST">
                @csrf

                <!-- NOMOR SURAT RESMI DIISI OLEH ADMIN -->
                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <label for="nomor_surat" class="form-label fw-bold text-dark d-flex justify-content-between align-items-center mb-1">
                        <span><i class="bi bi-123 text-danger me-1"></i> Nomor Surat Resmi Nagari: <span class="text-danger">*</span></span>
                        <small class="text-muted fw-normal">Format baku: 470 / [Nomor] / WN-KM / [Bulan] / [Tahun]</small>
                    </label>
                    <input type="text" name="nomor_surat" id="nomor_surat" class="form-control form-control-lg fw-bold text-primary font-monospace" 
                           value="{{ old('nomor_surat', $surat->nomor_surat ?: $saranNomorSurat) }}" required />
                    <small class="text-muted d-block mt-1">Admin menentukan nomor surat resmi ini sebelum surat dikirimkan ke meja Wali Nagari.</small>
                </div>

                <!-- REDAKSI ISI SURAT -->
                <div class="mb-3">
                    <label for="isi_surat" class="form-label fw-bold small text-dark d-flex justify-content-between">
                        <span><i class="bi bi-file-text me-1"></i> Isi Redaksi Naskah Surat (Dapat Diedit Manual):</span>
                        <span class="text-muted fw-normal">Otomatis terisi dari AI / Template</span>
                    </label>
                    <textarea name="isi_surat" id="isi_surat" rows="13" class="form-control font-monospace" style="font-size: 0.92rem; line-height: 1.65;" required>{{ old('isi_surat', $surat->isi_surat) }}</textarea>
                </div>

                <!-- CATATAN ADMIN -->
                <div class="mb-4">
                    <label for="catatan_admin" class="form-label fw-bold small text-dark">Catatan Pemeriksaan Admin (Opsional):</label>
                    <input type="text" name="catatan_admin" id="catatan_admin" class="form-control" placeholder="Contoh: Berkas KTP/KK sudah diperiksa valid dan sesuai" value="{{ old('catatan_admin', $surat->catatan_admin) }}" />
                </div>

                <!-- TOMBOL AKSI -->
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-3 border-top">
                    <div>
                        <button type="submit" class="btn btn-success btn-lg rounded-pill px-4 fw-bold shadow">
                            <i class="bi bi-check-circle-fill me-1"></i> Verifikasi & Teruskan ke Wali Nagari
                        </button>
                    </div>
                </div>
            </form>

            <!-- Form Tolak -->
            <div class="mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#collapseTolak">
                    <i class="bi bi-x-circle me-1"></i> Tolak Permohonan Surat Ini
                </button>

                <div class="collapse mt-3" id="collapseTolak">
                    <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger border-opacity-25">
                        <form action="{{ route('admin.surat.reject', $surat->id) }}" method="POST">
                            @csrf
                            <label class="form-label small fw-bold text-danger">Alasan Penolakan Permohonan:</label>
                            <textarea name="catatan_admin" rows="2" class="form-control form-control-sm mb-2" placeholder="Tuliskan alasan mengapa surat ditolak agar pemohon mengetahui..." required></textarea>
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                Konfirmasi Tolak Surat
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnGenerate = document.getElementById('btnAiGenerate');
    const btnRefine = document.getElementById('btnAiRefine');
    const promptInput = document.getElementById('aiCustomPrompt');
    const textArea = document.getElementById('isi_surat');
    const loadingBadge = document.getElementById('aiLoadingBadge');

    function callAi(mode) {
        const instruksi = promptInput.value.trim();
        const currentText = textArea.value;

        loadingBadge.classList.remove('d-none');
        btnGenerate.disabled = true;
        btnRefine.disabled = true;

        fetch('{{ route("admin.surat.ai-generate", $surat->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mode: mode,
                instruksi: instruksi,
                isi_surat: currentText
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.isi_surat) {
                textArea.value = data.isi_surat;
                textArea.classList.add('bg-success', 'bg-opacity-10');
                setTimeout(() => {
                    textArea.classList.remove('bg-success', 'bg-opacity-10');
                }, 1000);
            } else {
                alert('Gagal memproses dengan AI: ' + (data.message || 'Terjadi kesalahan.'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi saat memanggil AI.');
        })
        .finally(() => {
            loadingBadge.classList.add('d-none');
            btnGenerate.disabled = false;
            btnRefine.disabled = false;
        });
    }

    btnGenerate.addEventListener('click', () => callAi('generate'));
    btnRefine.addEventListener('click', () => callAi('refine'));
});
</script>
@endpush
@endsection
