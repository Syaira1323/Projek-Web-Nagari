@extends('admin.layouts.admin')

@section('title', 'Galeri & Media Publik')
@section('page-title', 'Kelola Galeri & Media Nagari')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-images text-success me-2"></i>Kelola Galeri & Media Publik
        </h4>
        <p class="text-muted small mb-0">
            Kelola dokumentasi foto kegiatan, pesona nagari, dan publikasi media resmi Nagari Koto Malintang
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahGaleri">
        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload Foto Baru
    </button>
</div>

<!-- FORM QUICK UPLOAD CARD (Sesuai Desain Admin.html) -->
<div class="card-custom mb-4">
    <h6 class="fw-bold text-dark mb-3">
        <i class="bi bi-plus-circle-fill text-success me-2"></i>Unggah Cepat Foto Dokumentasi
    </h6>
    <form action="{{ route('admin.konten.galeri.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 align-items-center">
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold text-secondary mb-1">Pilih Berkas Foto <span class="text-danger">*</span></label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required onchange="previewQuickUpload(this)" />
                <small class="text-muted" style="font-size: 0.72rem;">Format: JPG, PNG, WEBP (Maks. 4MB)</small>
            </div>
            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold text-secondary mb-1">Keterangan / Caption Foto</label>
                <input type="text" name="caption" class="form-control" placeholder="Contoh: Gotong Royong Warga di Balai Adat" />
                <small class="text-muted" style="font-size: 0.72rem;">Deskripsi singkat dokumentasi kegiatan</small>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-secondary mb-1">Terkait Kaba (Opsional)</label>
                <select name="kaba_nagari_id" class="form-select">
                    <option value="">-- Galeri Umum --</option>
                    @foreach ($kabaList as $k)
                        <option value="{{ $k->id }}">{{ Str::limit($k->judul, 25) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 pt-lg-3">
                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-semibold">
                    <i class="bi bi-upload me-1"></i> Unggah
                </button>
            </div>
        </div>
        <div id="quickUploadPreviewContainer" class="mt-3 d-none">
            <div class="p-2 border rounded-3 d-inline-flex align-items-center gap-3 bg-light">
                <img id="quickUploadPreview" src="#" alt="Preview" style="height: 60px; width: 90px; object-fit: cover; border-radius: 8px;" />
                <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Foto siap diunggah</small>
            </div>
        </div>
    </form>
</div>

<!-- GRID DAFTAR FOTO GALERI -->
<div class="card-custom">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-grid-fill text-success me-2"></i>Daftar Foto Dokumentasi Publik ({{ $galeriList->total() }} Foto)
        </h6>
    </div>

    @if ($galeriList->count() > 0)
        <div class="row g-4" id="galeriGrid">
            @foreach ($galeriList as $g)
                @php
                    $imgUrl = asset('image/bg.jpg');
                    if ($g->gambar) {
                        if (file_exists(public_path($g->gambar))) {
                            $imgUrl = asset($g->gambar);
                        } elseif (file_exists(public_path('image/' . $g->gambar))) {
                            $imgUrl = asset('image/' . $g->gambar);
                        } else {
                            $imgUrl = asset($g->gambar);
                        }
                    }
                @endphp
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative" style="background: #ffffff; border: 1px solid #e9ecef !important;">
                        <div style="height: 180px; overflow: hidden; position: relative; background: #0f1d10;">
                            <img src="{{ $imgUrl }}" alt="{{ $g->caption ?? 'Galeri Foto' }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" class="galeri-thumb" />
                            @if ($g->kabaNagari)
                                <span class="position-absolute top-0 start-0 m-2 badge bg-success bg-opacity-90 shadow-sm text-truncate" style="max-width: 85%;">
                                    <i class="bi bi-newspaper me-1"></i> {{ Str::limit($g->kabaNagari->judul, 20) }}
                                </span>
                            @else
                                <span class="position-absolute top-0 start-0 m-2 badge bg-secondary bg-opacity-90 shadow-sm">
                                    <i class="bi bi-image me-1"></i> Umum
                                </span>
                            @endif
                        </div>

                        <div class="card-body p-3 d-flex flex-direction-column flex-grow-1 flex-column justify-content-between">
                            <div>
                                <p class="fw-semibold text-dark mb-1 small text-truncate-2" style="min-height: 38px; line-height: 1.4;">
                                    {{ $g->caption ?: 'Dokumentasi Nagari Koto Malintang' }}
                                </p>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock me-1"></i>{{ $g->created_at ? $g->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                </small>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill flex-fill" onclick="openPreviewLightbox('{{ $imgUrl }}', '{{ addslashes($g->caption ?: 'Dokumentasi Nagari') }}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill flex-fill" data-bs-toggle="modal" data-bs-target="#modalEditGaleri{{ $g->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill flex-fill" data-bs-toggle="modal" data-bs-target="#modalHapusGaleri{{ $g->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT FOTO GALERI -->
                <div class="modal fade" id="modalEditGaleri{{ $g->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="{{ route('admin.konten.galeri.update', $g->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header bg-dark text-white p-4">
                                    <h5 class="modal-title fw-bold">
                                        <i class="bi bi-pencil-square text-warning me-2"></i>Edit Foto Galeri
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="mb-3 text-center">
                                        <label class="form-label small fw-bold text-secondary d-block">Foto Saat Ini</label>
                                        <img id="currentEditImg{{ $g->id }}" src="{{ $imgUrl }}" alt="Foto" class="rounded-3 shadow-sm border mb-2" style="max-height: 150px; max-width: 100%; object-fit: cover;" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Ganti Foto (Opsional)</label>
                                        <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewEditImage(this, 'currentEditImg{{ $g->id }}')" />
                                        <small class="text-muted" style="font-size: 0.75rem;">Biarkan kosong jika tidak ingin mengganti file gambar</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Keterangan / Caption Foto</label>
                                        <input type="text" name="caption" class="form-control" value="{{ $g->caption }}" placeholder="Keterangan foto kegiatan" />
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Terkait Kaba / Berita (Opsional)</label>
                                        <select name="kaba_nagari_id" class="form-select">
                                            <option value="">-- Galeri Umum --</option>
                                            @foreach ($kabaList as $k)
                                                <option value="{{ $k->id }}" {{ $g->kaba_nagari_id == $k->id ? 'selected' : '' }}>
                                                    {{ Str::limit($k->judul, 40) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer p-4 pt-0 border-0">
                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- MODAL HAPUS FOTO GALERI -->
                <div class="modal fade" id="modalHapusGaleri{{ $g->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <form action="{{ route('admin.konten.galeri.destroy', $g->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body p-4 text-center">
                                    <i class="bi bi-exclamation-circle text-danger fs-1 mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-2">Hapus Foto Ini?</h6>
                                    <p class="text-muted small mb-3">Foto akan dihapus permanen dari sistem dan galeri publik.</p>
                                    <div class="rounded-3 overflow-hidden mb-3 border" style="height: 100px;">
                                        <img src="{{ $imgUrl }}" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold">Hapus</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $galeriList->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="p-4 rounded-circle bg-light d-inline-block mb-3">
                <i class="bi bi-images fs-1 text-secondary"></i>
            </div>
            <h6 class="fw-bold text-dark">Belum ada foto di Galeri</h6>
            <p class="text-muted small">Silakan unggah foto dokumentasi menggunakan form di atas.</p>
        </div>
    @endif
</div>

<!-- MODAL TAMBAH FOTO LENGKAP -->
<div class="modal fade" id="modalTambahGaleri" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.konten.galeri.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-cloud-arrow-up-fill text-success me-2"></i>Upload Foto Galeri
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Pilih Berkas Foto <span class="text-danger">*</span></label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required onchange="previewModalUpload(this)" />
                        <small class="text-muted" style="font-size: 0.75rem;">Mendukung: JPG, PNG, WEBP (Maksimal 4MB)</small>
                    </div>

                    <div id="modalUploadPreviewContainer" class="mb-3 d-none text-center">
                        <img id="modalUploadPreview" src="#" alt="Preview" class="rounded-3 shadow-sm border" style="max-height: 180px; max-width: 100%; object-fit: cover;" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Keterangan / Caption Foto</label>
                        <input type="text" name="caption" class="form-control" placeholder="Contoh: Kunjungan Tim Penilai Desa Mandiri" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Terkait Berita / Kaba (Opsional)</label>
                        <select name="kaba_nagari_id" class="form-select">
                            <option value="">-- Galeri Umum / Dokumentasi Nagari --</option>
                            @foreach ($kabaList as $k)
                                <option value="{{ $k->id }}">{{ Str::limit($k->judul, 45) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-upload me-1"></i> Unggah Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- LIGHTBOX MODAL PREVIEW -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 bg-dark text-white overflow-hidden">
            <div class="modal-body p-0 position-relative text-center bg-black">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <img id="lightboxImg" src="#" alt="Lightbox" class="w-100" style="max-height: 80vh; object-fit: contain;" />
                <div class="p-3 bg-dark bg-opacity-75 text-start">
                    <h6 class="fw-bold text-white mb-0" id="lightboxCaption"></h6>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewQuickUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('quickUploadPreview').src = e.target.result;
            document.getElementById('quickUploadPreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewModalUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('modalUploadPreview').src = e.target.result;
            document.getElementById('modalUploadPreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditImage(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openPreviewLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption;
    const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    modal.show();
}
</script>
@endpush
