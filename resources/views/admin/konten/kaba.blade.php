@extends('admin.layouts.admin')

@section('title', 'Kelola Kaba Nagari')
@section('page-title', 'Manajemen Kaba Nagari (Berita & Informasi)')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Publikasi Kaba Nagari</h4>
        <p class="text-muted small mb-0">Kelola warta berita, agenda kegiatan, pengumuman, dan foto liputan nagari</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahKaba">
        <i class="bi bi-plus-circle me-1"></i> Tulis Berita Baru
    </button>
</div>

<div class="card-custom">
    @if ($kabaList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>FOTO</th>
                        <th>JUDUL BERITA</th>
                        <th>KATEGORI</th>
                        <th>TANGGAL</th>
                        <th>RINGKASAN</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kabaList as $k)
                        <tr>
                            <td style="width: 80px;">
                                @if ($k->gambar)
                                    <img src="{{ asset($k->gambar) }}" alt="{{ $k->judul }}" class="rounded-3 shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center small fw-bold" style="width: 60px; height: 45px;">
                                        <i class="bi bi-newspaper"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong class="text-dark">{{ Str::limit($k->judul, 45) }}</strong></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $k->kategori }}</span></td>
                            <td><small class="text-muted">{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') : '-' }}</small></td>
                            <td><small class="text-muted">{{ Str::limit($k->ringkasan, 50) }}</small></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditK{{ $k->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.konten.kaba.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus berita ini?');">
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
                        <div class="modal fade" id="modalEditK{{ $k->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.konten.kaba.update', $k->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-success text-white p-4">
                                            <h5 class="modal-title fw-bold">Edit Kaba Nagari</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Judul Berita</label>
                                                    <input type="text" name="judul" class="form-control" value="{{ $k->judul }}" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Kategori</label>
                                                    <select name="kategori" class="form-select" required>
                                                        <option value="Pemerintahan" {{ $k->kategori === 'Pemerintahan' ? 'selected' : '' }}>Pemerintahan</option>
                                                        <option value="Kegiatan" {{ $k->kategori === 'Kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                                                        <option value="Sosial" {{ $k->kategori === 'Sosial' ? 'selected' : '' }}>Sosial</option>
                                                        <option value="Pembangunan" {{ $k->kategori === 'Pembangunan' ? 'selected' : '' }}>Pembangunan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Tanggal Publikasi</label>
                                                    <input type="date" name="tanggal" class="form-control" value="{{ $k->tanggal ? \Carbon\Carbon::parse($k->tanggal)->format('Y-m-d') : '' }}" required />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Upload Foto Liputan (Opsional)</label>
                                                    <input type="file" name="gambar" class="form-control" accept="image/*" />
                                                    @if ($k->gambar)
                                                        <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($k->gambar) }}" target="_blank">Lihat Foto</a></small>
                                                    @endif
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Ringkasan Singkat</label>
                                                    <textarea name="ringkasan" rows="2" class="form-control" required>{{ $k->ringkasan }}</textarea>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Isi Lengkap Berita</label>
                                                    <textarea name="isi" rows="6" class="form-control" required>{{ $k->isi }}</textarea>
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

        <div class="mt-3">
            {{ $kabaList->links() }}
        </div>
    @else
        <div class="text-center py-4 text-muted">Belum ada berita kaba nagari.</div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahKaba" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.kaba.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tulis Kaba Nagari Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control" placeholder="Tuliskan judul berita..." required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Pemerintahan">Pemerintahan</option>
                                <option value="Kegiatan">Kegiatan</option>
                                <option value="Sosial">Sosial</option>
                                <option value="Pembangunan">Pembangunan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Publikasi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Upload Foto Liputan (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Ringkasan Singkat</label>
                            <textarea name="ringkasan" rows="2" class="form-control" placeholder="Ringkasan 1-2 kalimat..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Isi Lengkap Berita</label>
                            <textarea name="isi" rows="6" class="form-control" placeholder="Tuliskan berita lengkap..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
