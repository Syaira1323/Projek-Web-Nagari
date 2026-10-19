@extends('admin.layouts.admin')

@section('title', 'Kelola Struktur Organisasi')
@section('page-title', 'Manajemen Posisi & Bagan Struktur Organisasi')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-diagram-3-fill text-success me-2"></i>Daftar Posisi & Bagan Struktur Nagari
        </h4>
        <p class="text-muted small mb-0">
            Kelola hierarki jabatan, pembagian tugas dan fungsi tata kerja, serta foto/bagan visual pemerintahan nagari
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahStruktur">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Posisi Baru
    </button>
</div>

<div class="card-custom">
    @if ($strukturList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase" style="font-size: 0.78rem;">
                        <th style="width: 80px;">BAGAN / FOTO</th>
                        <th>NAMA POSISI / JABATAN</th>
                        <th>URAIAN TUGAS & KETERANGAN</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($strukturList as $item)
                        @php
                            $bImg = null;
                            if ($item->foto_bagan) {
                                if (file_exists(public_path($item->foto_bagan))) {
                                    $bImg = asset($item->foto_bagan);
                                } elseif (file_exists(public_path('image/' . $item->foto_bagan))) {
                                    $bImg = asset('image/' . $item->foto_bagan);
                                } else {
                                    $bImg = asset($item->foto_bagan);
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                @if ($bImg)
                                    <img src="{{ $bImg }}" alt="{{ $item->nama }}" class="rounded-3 border shadow-sm" style="width: 65px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-secondary" style="width: 65px; height: 45px;">
                                        <i class="bi bi-diagram-3" style="font-size: 1.2rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $item->nama }}</strong>
                                <span class="badge bg-success bg-opacity-10 text-success fw-semibold mt-1">Jabatan Struktural</span>
                            </td>
                            <td>
                                <span class="text-muted small" style="line-height: 1.4;">{{ $item->keterangan }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditStruktur{{ $item->id }}" title="Edit Posisi">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalHapusStruktur{{ $item->id }}" title="Hapus Posisi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT STRUKTUR -->
                        <div class="modal fade" id="modalEditStruktur{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.struktur.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-dark text-white p-4">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Posisi Struktur
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Nama Posisi / Jabatan <span class="text-danger">*</span></label>
                                                <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required />
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Ganti Foto Bagan / Ikon Posisi (Opsional)</label>
                                                <input type="file" name="foto_bagan" class="form-control" accept="image/*" onchange="previewEditBaganImg(this, 'editBaganPreview{{ $item->id }}')" />
                                                <small class="text-muted" style="font-size: 0.75rem;">Mendukung format JPG, PNG, WEBP (Maksimal 4MB)</small>
                                            </div>

                                            @if ($bImg)
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary d-block">Bagan / Foto Saat Ini:</label>
                                                    <img id="editBaganPreview{{ $item->id }}" src="{{ $bImg }}" alt="{{ $item->nama }}" class="rounded-3 shadow-sm border" style="max-height: 120px; object-fit: cover;" />
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Tugas, Fungsi & Keterangan <span class="text-danger">*</span></label>
                                                <textarea name="keterangan" rows="4" class="form-control" required placeholder="Jelaskan uraian fungsi jabatan...">{{ $item->keterangan }}</textarea>
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

                        <!-- MODAL HAPUS STRUKTUR -->
                        <div class="modal fade" id="modalHapusStruktur{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.struktur.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Posisi Struktur?</h6>
                                            <p class="text-muted small mb-3"><strong>"{{ $item->nama }}"</strong> akan dihapus permanen dari bagan organisasi.</p>
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
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-diagram-3 fs-1 d-block mb-2"></i>
            Belum ada data posisi struktur organisasi nagari.
        </div>
    @endif
</div>

<!-- MODAL TAMBAH STRUKTUR -->
<div class="modal fade" id="modalTambahStruktur" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.konten.struktur.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i>Tambah Posisi Struktur Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Nama Posisi / Jabatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kasi Kesejahteraan" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Foto Bagan / Ikon Posisi (Opsional)</label>
                        <input type="file" name="foto_bagan" class="form-control" accept="image/*" onchange="previewTambahBaganImg(this)" />
                        <small class="text-muted" style="font-size: 0.75rem;">Mendukung format JPG, PNG, WEBP (Maksimal 4MB)</small>
                    </div>

                    <div id="tambahBaganPreviewContainer" class="mb-3 d-none text-center">
                        <img id="tambahBaganPreview" src="#" alt="Preview" class="rounded-3 shadow-sm border" style="max-height: 120px; object-fit: cover;" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tugas, Fungsi & Keterangan <span class="text-danger">*</span></label>
                        <textarea name="keterangan" rows="4" class="form-control" placeholder="Jelaskan uraian fungsi jabatan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Posisi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewTambahBaganImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('tambahBaganPreview').src = e.target.result;
            document.getElementById('tambahBaganPreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditBaganImg(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
