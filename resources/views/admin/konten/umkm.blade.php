@extends('admin.layouts.admin')

@section('title', 'Kelola UMKM Nagari')
@section('page-title', 'Manajemen Katalog UMKM Nagari')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-shop-window text-success me-2"></i>Daftar Pelaku UMKM Nagari
        </h4>
        <p class="text-muted small mb-0">
            Kelola dan pantau seluruh direktori UMKM lokal, kategori usaha, kontak, media sosial, serta foto produk
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahUmkm">
        <i class="bi bi-plus-circle-fill me-1"></i> Tambah UMKM Baru
    </button>
</div>

<div class="card-custom">
    @if ($umkmList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase" style="font-size: 0.78rem;">
                        <th style="width: 80px;">FOTO</th>
                        <th>NAMA USAHA & KATEGORI</th>
                        <th>PEMILIK & EST</th>
                        <th>KONTAK & SOSMED</th>
                        <th>DESKRIPSI</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($umkmList as $u)
                        @php
                            $uFoto = null;
                            if ($u->foto) {
                                if (file_exists(public_path($u->foto))) {
                                    $uFoto = asset($u->foto);
                                } elseif (file_exists(public_path('image/' . $u->foto))) {
                                    $uFoto = asset('image/' . $u->foto);
                                } else {
                                    $uFoto = asset($u->foto);
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                @if ($uFoto)
                                    <img src="{{ $uFoto }}" alt="{{ $u->nama_usaha }}" class="rounded-3 shadow-sm border" style="width: 65px; height: 48px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center shadow-sm" style="width: 65px; height: 48px; font-size: 1.3rem;">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $u->nama_usaha }}</strong>
                                <span class="badge bg-warning text-dark fw-semibold mt-1">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $u->kategori }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $u->pemilik }}</strong>
                                <small class="text-muted">{{ $u->tahun_berdiri ? 'Est. ' . $u->tahun_berdiri : '-' }}</small>
                            </td>
                            <td>
                                @if ($u->whatsapp)
                                    <small class="d-block text-dark">
                                        <i class="bi bi-whatsapp text-success me-1"></i>{{ $u->whatsapp }}
                                    </small>
                                @endif
                                @if ($u->instagram)
                                    <small class="d-block text-muted">
                                        <i class="bi bi-instagram text-danger me-1"></i>{{ Str::limit($u->instagram, 25) }}
                                    </small>
                                @endif
                                @if (!$u->whatsapp && !$u->instagram)
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted d-block" style="line-height: 1.4;">
                                    {{ Str::limit($u->deskripsi, 60) }}
                                </small>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditU{{ $u->id }}" title="Edit UMKM">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalHapusU{{ $u->id }}" title="Hapus UMKM">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT UMKM -->
                        <div class="modal fade" id="modalEditU{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.umkm.update', $u->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-dark text-white p-4">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Data UMKM
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold text-dark">Nama Usaha <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama_usaha" class="form-control" value="{{ $u->nama_usaha }}" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold text-dark">Kategori <span class="text-danger">*</span></label>
                                                    <select name="kategori" class="form-select" required>
                                                        <option value="Kuliner" {{ $u->kategori === 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                                                        <option value="Kerajinan" {{ $u->kategori === 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                                        <option value="Pertanian" {{ $u->kategori === 'Pertanian' ? 'selected' : '' }}>Pertanian</option>
                                                        <option value="Jasa" {{ $u->kategori === 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                                        <option value="Perdagangan" {{ $u->kategori === 'Perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Nama Pemilik / Pengelola <span class="text-danger">*</span></label>
                                                    <input type="text" name="pemilik" class="form-control" value="{{ $u->pemilik }}" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-dark">Tahun Berdiri</label>
                                                    <input type="text" name="tahun_berdiri" class="form-control" value="{{ $u->tahun_berdiri }}" placeholder="Contoh: 2023" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold text-dark">Jam Operasional</label>
                                                    <input type="text" name="jam_operasional" class="form-control" value="{{ $u->jam_operasional }}" placeholder="08.00 - 17.00" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">WhatsApp</label>
                                                    <input type="text" name="whatsapp" class="form-control" value="{{ $u->whatsapp }}" placeholder="08xxxxxxxxxx" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Instagram</label>
                                                    <input type="text" name="instagram" class="form-control" value="{{ $u->instagram }}" placeholder="https://instagram.com/... atau @akun" />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-dark">URL Google Maps (Opsional)</label>
                                                    <input type="text" name="google_maps_url" class="form-control" value="{{ $u->google_maps_url }}" placeholder="https://maps.app.goo.gl/..." />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-dark">Ganti Foto Produk / Usaha (Opsional)</label>
                                                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewUmkmImg(this, 'editFotoPreview{{ $u->id }}', 'editFotoBox{{ $u->id }}')" />
                                                    <small class="text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, WEBP (Maksimal 4MB)</small>
                                                </div>

                                                <div class="col-12" id="editFotoBox{{ $u->id }}" style="{{ $uFoto ? '' : 'display: none;' }}">
                                                    <label class="form-label small fw-bold text-secondary d-block">Foto Usaha Saat Ini / Preview:</label>
                                                    <img id="editFotoPreview{{ $u->id }}" src="{{ $uFoto ?: '' }}" alt="{{ $u->nama_usaha }}" class="rounded-3 shadow-sm border" style="max-height: 120px; object-fit: cover;" />
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-dark">Deskripsi Usaha <span class="text-danger">*</span></label>
                                                    <textarea name="deskripsi" rows="4" class="form-control" required placeholder="Jelaskan produk usaha, keunggulan, dan profil singkat...">{{ $u->deskripsi }}</textarea>
                                                </div>
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

                        <!-- MODAL HAPUS UMKM -->
                        <div class="modal fade" id="modalHapusU{{ $u->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.umkm.destroy', $u->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Data UMKM?</h6>
                                            <p class="text-muted small mb-3"><strong>"{{ $u->nama_usaha }}"</strong> akan dihapus permanen dari katalog.</p>
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

        <div class="p-3 border-top">
            {{ $umkmList->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-shop fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada data katalog UMKM nagari. Silakan tambahkan UMKM baru.</p>
        </div>
    @endif
</div>

<!-- MODAL TAMBAH UMKM -->
<div class="modal fade" id="modalTambahUmkm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.konten.umkm.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle-fill me-2"></i>Tambah UMKM Baru ke Katalog
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-dark">Nama Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Dapoer Pipin / Songket Koto Malintang" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-dark">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" class="form-select" required>
                                <option value="Kuliner">Kuliner</option>
                                <option value="Kerajinan">Kerajinan</option>
                                <option value="Pertanian">Pertanian</option>
                                <option value="Jasa">Jasa</option>
                                <option value="Perdagangan">Perdagangan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Pemilik / Pengelola <span class="text-danger">*</span></label>
                            <input type="text" name="pemilik" class="form-control" placeholder="Nama pemilik usaha" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Tahun Berdiri</label>
                            <input type="text" name="tahun_berdiri" class="form-control" placeholder="Contoh: 2024" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-dark">Jam Operasional</label>
                            <input type="text" name="jam_operasional" class="form-control" placeholder="09.00 - 17.00" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Instagram</label>
                            <input type="text" name="instagram" class="form-control" placeholder="https://instagram.com/... atau @akun" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">URL Google Maps (Opsional)</label>
                            <input type="text" name="google_maps_url" class="form-control" placeholder="https://maps.app.goo.gl/..." />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Upload Foto Produk / Usaha (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewUmkmImg(this, 'tambahFotoPreview', 'tambahFotoBox')" />
                            <small class="text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, WEBP (Maksimal 4MB)</small>
                        </div>

                        <div class="col-12" id="tambahFotoBox" style="display: none;">
                            <label class="form-label small fw-bold text-secondary d-block">Preview Foto:</label>
                            <img id="tambahFotoPreview" src="#" alt="Preview Foto" class="rounded-3 shadow-sm border" style="max-height: 120px; object-fit: cover;" />
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Deskripsi Usaha <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" rows="4" class="form-control" placeholder="Jelaskan produk usaha, keunggulan, dan profil singkat..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Data UMKM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewUmkmImg(input, previewId, containerId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                const box = document.getElementById(containerId);
                if (img) {
                    img.src = e.target.result;
                }
                if (box) {
                    box.style.display = 'block';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection

