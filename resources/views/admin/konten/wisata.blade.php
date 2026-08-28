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
    <div class="table-responsive">
        <table class="table align-middle table-hover">
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
                    <tr>
                        <td style="width: 80px;">
                            @if ($w->gambar)
                                <img src="{{ asset($w->gambar) }}" alt="{{ $w->nama }}" class="rounded-3 shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" />
                            @else
                                <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center small fw-bold" style="width: 60px; height: 45px;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong class="text-dark">{{ $w->nama }}</strong></td>
                        <td><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $w->kategori }}</span></td>
                        <td><small class="text-muted">{{ $w->lokasi ?: '-' }}</small></td>
                        <td><small class="text-muted">{{ Str::limit($w->deskripsi, 60) }}</small></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditW{{ $w->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.konten.wisata.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Hapus destinasi wisata ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEditW{{ $w->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0">
                                <form action="{{ route('admin.konten.wisata.update', $w->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-success text-white p-4">
                                        <h5 class="modal-title fw-bold">Edit Destinasi Wisata</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-8">
                                                <label class="form-label small fw-bold">Nama Wisata</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $w->nama }}" required />
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Kategori</label>
                                                <select name="kategori" class="form-select" required>
                                                    <option value="Wisata Alam" {{ $w->kategori === 'Wisata Alam' ? 'selected' : '' }}>Wisata Alam</option>
                                                    <option value="Wisata Budaya" {{ $w->kategori === 'Wisata Budaya' ? 'selected' : '' }}>Wisata Budaya</option>
                                                    <option value="Wisata Religi" {{ $w->kategori === 'Wisata Religi' ? 'selected' : '' }}>Wisata Religi</option>
                                                    <option value="Wisata Kuliner" {{ $w->kategori === 'Wisata Kuliner' ? 'selected' : '' }}>Wisata Kuliner</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Lokasi Jorong</label>
                                                <input type="text" name="lokasi" class="form-control" value="{{ $w->lokasi }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Link Google Maps (Opsional)</label>
                                                <input type="url" name="google_maps_url" class="form-control" value="{{ $w->google_maps_url }}" />
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Upload Foto Wisata (Opsional)</label>
                                                <input type="file" name="gambar" class="form-control" accept="image/*" />
                                                @if ($w->gambar)
                                                    <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($w->gambar) }}" target="_blank">Lihat Foto</a></small>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Deskripsi Wisata</label>
                                                <textarea name="deskripsi" rows="5" class="form-control" required>{{ $w->deskripsi }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer p-4 pt-0 border-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
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
