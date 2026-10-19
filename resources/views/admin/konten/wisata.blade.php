@extends('admin.layouts.admin')

@section('title', 'Kelola Objek Wisata')
@section('page-title', 'Manajemen Objek Wisata Nagari')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Destinasi Objek Wisata</h4>
        <p class="text-muted small mb-0">Kelola potensi, foto keindahan, dan daya tarik wisata Nagari Koto Malintang</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahWisata">
        <i class="bi bi-plus-circle me-1"></i> Tambah Wisata Baru
    </button>
</div>

<div class="card-custom">
    @if ($wisataList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>FOTO</th>
                        <th>NAMA DESTINASI</th>
                        <th>KATEGORI</th>
                        <th>LOKASI</th>
                        <th>DESKRIPSI</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($wisataList as $w)
                        @php
                            $wisataImg = null;
                            if ($w->gambar) {
                                if (file_exists(public_path($w->gambar))) {
                                    $wisataImg = asset($w->gambar);
                                } elseif (file_exists(public_path('image/' . $w->gambar))) {
                                    $wisataImg = asset('image/' . $w->gambar);
                                } else {
                                    $wisataImg = asset($w->gambar);
                                }
                            }
                        @endphp
                        <tr>
                            <td style="width: 80px;">
                                @if ($wisataImg)
                                    <img src="{{ $wisataImg }}" alt="{{ $w->nama }}" class="rounded-3 shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center small fw-bold" style="width: 60px; height: 45px;">
                                        <i class="bi bi-compass"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $w->nama }}</strong>
                                @if ($w->google_maps_url)
                                    <a href="{{ $w->google_maps_url }}" target="_blank" class="small text-decoration-none text-success" style="font-size:0.75rem;">
                                        <i class="bi bi-geo-alt"></i> Buka Google Maps
                                    </a>
                                @endif
                            </td>
                            <td><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $w->kategori }}</span></td>
                            <td><small class="text-muted">{{ $w->lokasi ?: '-' }}</small></td>
                            <td><small class="text-muted">{{ Str::limit($w->deskripsi, 60) }}</small></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditW{{ $w->id }}" title="Edit Objek Wisata">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.konten.wisata.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus destinasi wisata \'{{ addslashes($w->nama) }}\'?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Hapus Wisata">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEditW{{ $w->id }}" tabindex="-1" aria-labelledby="modalEditWLabel{{ $w->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.konten.wisata.update', $w->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-success text-white p-4">
                                            <h5 class="modal-title fw-bold" id="modalEditWLabel{{ $w->id }}">
                                                <i class="bi bi-pencil-square me-2"></i>Edit Destinasi Objek Wisata
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Nama Wisata <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama" class="form-control" value="{{ old('nama', $w->nama) }}" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Kategori <span class="text-danger">*</span></label>
                                                    <select name="kategori" class="form-select" required>
                                                        <option value="Wisata Alam" {{ $w->kategori === 'Wisata Alam' ? 'selected' : '' }}>Wisata Alam</option>
                                                        <option value="Wisata Budaya" {{ $w->kategori === 'Wisata Budaya' ? 'selected' : '' }}>Wisata Budaya</option>
                                                        <option value="Wisata Religi" {{ $w->kategori === 'Wisata Religi' ? 'selected' : '' }}>Wisata Religi</option>
                                                        <option value="Wisata Kuliner" {{ $w->kategori === 'Wisata Kuliner' ? 'selected' : '' }}>Wisata Kuliner</option>
                                                        <option value="Wisata Rekreasi" {{ $w->kategori === 'Wisata Rekreasi' ? 'selected' : '' }}>Wisata Rekreasi</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Lokasi Jorong / Alamat</label>
                                                    <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi', $w->lokasi) }}" placeholder="Contoh: Jorong Muko-Muko, Koto Malintang" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Link Google Maps (Opsional)</label>
                                                    <input type="text" name="google_maps_url" class="form-control" value="{{ old('google_maps_url', $w->google_maps_url) }}" placeholder="https://maps.app.goo.gl/..." />
                                                </div>
                                                <div class="col-12 p-3 bg-light rounded-3 border">
                                                    <label class="form-label small fw-bold"><i class="bi bi-image text-success me-1"></i> Foto Wisata</label>
                                                    @if ($wisataImg)
                                                        <div class="mb-2 d-flex align-items-center gap-3">
                                                            <img src="{{ $wisataImg }}" alt="{{ $w->nama }}" class="img-thumbnail rounded-3" style="height: 70px; width: 100px; object-fit: cover;" />
                                                            <small class="text-muted">Foto saat ini terpasang. Unggah berkas baru di bawah jika ingin mengganti.</small>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="gambar" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp" />
                                                    <small class="text-muted">Format: JPG, PNG, WEBP (Maks 4MB)</small>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Deskripsi Wisata <span class="text-danger">*</span></label>
                                                    <textarea name="deskripsi" rows="5" class="form-control" required>{{ old('deskripsi', $w->deskripsi) }}</textarea>
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
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-compass text-muted fs-1 d-block mb-3"></i>
            <h6 class="text-muted">Belum ada data objek wisata yang ditambahkan.</h6>
            <button type="button" class="btn btn-success rounded-pill px-4 mt-2" data-bs-toggle="modal" data-bs-target="#modalTambahWisata">
                <i class="bi bi-plus-circle me-1"></i> Tambah Objek Wisata Pertama
            </button>
        </div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahWisata" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.wisata.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tambah Objek Wisata Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Wisata</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Panorama Danau Maninjau" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Wisata Alam">Wisata Alam</option>
                                <option value="Wisata Budaya">Wisata Budaya</option>
                                <option value="Wisata Religi">Wisata Religi</option>
                                <option value="Wisata Kuliner">Wisata Kuliner</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Lokasi Jorong</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Jorong Muko-Muko" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Link Google Maps (Opsional)</label>
                            <input type="url" name="google_maps_url" class="form-control" placeholder="https://maps.app.goo.gl/..." />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Upload Foto Wisata (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi Wisata</label>
                            <textarea name="deskripsi" rows="5" class="form-control" placeholder="Jelaskan daya tarik, fasilitas, dan keunikan wisata..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Wisata</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
