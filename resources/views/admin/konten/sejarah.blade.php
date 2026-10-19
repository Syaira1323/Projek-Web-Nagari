@extends('admin.layouts.admin')

@section('title', 'Kelola Sejarah Nagari')
@section('page-title', 'Manajemen Sejarah Nagari & Narasi Periode')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-hourglass-split text-success me-2"></i>Daftar Narasi & Periode Sejarah
        </h4>
        <p class="text-muted small mb-0">
            Kelola narasi sejarah singkat, periodisasi sejarah masa ke masa, dan foto dokumentasi Nagari Koto Malintang
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahSejarah">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Entri Sejarah
    </button>
</div>

<div class="card-custom">
    @if ($sejarahList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase" style="font-size: 0.78rem;">
                        <th style="width: 70px;">FOTO</th>
                        <th style="width: 80px;">URUTAN</th>
                        <th style="width: 110px;">TIPE</th>
                        <th>JUDUL SEJARAH</th>
                        <th>PERIODE WAKTU</th>
                        <th>ISI RINGKAS</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sejarahList as $item)
                        @php
                            $sImg = null;
                            if ($item->gambar) {
                                if (file_exists(public_path($item->gambar))) {
                                    $sImg = asset($item->gambar);
                                } elseif (file_exists(public_path('image/' . $item->gambar))) {
                                    $sImg = asset('image/' . $item->gambar);
                                } else {
                                    $sImg = asset($item->gambar);
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                @if ($sImg)
                                    <img src="{{ $sImg }}" alt="{{ $item->judul }}" class="rounded-3 border shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-light border d-flex align-items-center justify-content-center text-secondary" style="width: 60px; height: 45px;">
                                        <i class="bi bi-image" style="font-size: 1.2rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-25 text-dark font-monospace fw-bold px-2 py-1">#{{ $item->urutan }}</span>
                            </td>
                            <td>
                                @if ($item->tipe === 'singkat')
                                    <span class="badge bg-info bg-opacity-20 text-info fw-bold px-2 py-1">
                                        <i class="bi bi-card-text me-1"></i> Singkat
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-20 text-success fw-bold px-2 py-1">
                                        <i class="bi bi-clock-history me-1"></i> Periode
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.92rem;">{{ $item->judul }}</strong>
                            </td>
                            <td>
                                @if ($item->periode)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">{{ $item->periode }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                    {{ Str::limit($item->isi, 85) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditSejarah{{ $item->id }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalHapusSejarah{{ $item->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT SEJARAH -->
                        <div class="modal fade" id="modalEditSejarah{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.sejarah.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-dark text-white p-4">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Entri Sejarah
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-dark">Tipe Sejarah <span class="text-danger">*</span></label>
                                                    <select name="tipe" class="form-select" required>
                                                        <option value="singkat" {{ $item->tipe === 'singkat' ? 'selected' : '' }}>Singkat (Gambaran Umum)</option>
                                                        <option value="periode" {{ $item->tipe === 'periode' ? 'selected' : '' }}>Periode (Era Waktu)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Judul Sejarah / Era <span class="text-danger">*</span></label>
                                                    <input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required />
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold text-dark">Urutan <span class="text-danger">*</span></label>
                                                    <input type="number" name="urutan" class="form-control font-monospace" value="{{ $item->urutan }}" required />
                                                </div>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Rentang Periode Waktu</label>
                                                    <input type="text" name="periode" class="form-control" value="{{ $item->periode }}" placeholder="Contoh: ±1800-an / 1945–1979 / Modern" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Ganti Foto Dokumentasi (Opsional)</label>
                                                    <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewEditSejarahImg(this, 'editSejarahPreview{{ $item->id }}')" />
                                                </div>
                                            </div>

                                            @if ($sImg)
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary d-block">Foto Dokumentasi Saat Ini:</label>
                                                    <img id="editSejarahPreview{{ $item->id }}" src="{{ $sImg }}" alt="{{ $item->judul }}" class="rounded-3 shadow-sm border" style="max-height: 120px; object-fit: cover;" />
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-dark">Isi Narasi Sejarah <span class="text-danger">*</span></label>
                                                <textarea name="isi" rows="6" class="form-control" required placeholder="Tuliskan isi cerita atau narasi sejarah...">{{ $item->isi }}</textarea>
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

                        <!-- MODAL HAPUS SEJARAH -->
                        <div class="modal fade" id="modalHapusSejarah{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.sejarah.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Entri Sejarah?</h6>
                                            <p class="text-muted small mb-3"><strong>"{{ $item->judul }}"</strong> akan dihapus permanen.</p>
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
            <i class="bi bi-hourglass-split fs-1 d-block mb-2"></i>
            Belum ada data sejarah.
        </div>
    @endif
</div>

<!-- MODAL TAMBAH SEJARAH -->
<div class="modal fade" id="modalTambahSejarah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.konten.sejarah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill text-success me-2"></i>Tambah Entri Sejarah Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Tipe Sejarah <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select" required>
                                <option value="periode" selected>Periode (Era Waktu)</option>
                                <option value="singkat">Singkat (Gambaran Umum)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Judul Sejarah / Nama Era <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Era Kolonial Belanda" required />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-dark">Urutan <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control font-monospace" value="{{ $sejarahList->count() + 1 }}" required />
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Rentang Periode Waktu</label>
                            <input type="text" name="periode" class="form-control" placeholder="Contoh: 1900–1945" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Foto Dokumentasi (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" onchange="previewTambahSejarahImg(this)" />
                        </div>
                    </div>

                    <div id="tambahSejarahPreviewContainer" class="mb-3 d-none text-center">
                        <img id="tambahSejarahPreview" src="#" alt="Preview" class="rounded-3 shadow-sm border" style="max-height: 140px; object-fit: cover;" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Isi Narasi Cerita Sejarah <span class="text-danger">*</span></label>
                        <textarea name="isi" rows="6" class="form-control" placeholder="Tuliskan narasi sejarah lengkap..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Entri Sejarah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewTambahSejarahImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('tambahSejarahPreview').src = e.target.result;
            document.getElementById('tambahSejarahPreviewContainer').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewEditSejarahImg(input, targetId) {
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
