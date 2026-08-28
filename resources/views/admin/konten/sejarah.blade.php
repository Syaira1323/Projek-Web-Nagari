@extends('admin.layouts.admin')

@section('title', 'Kelola Sejarah Nagari')
@section('page-title', 'Manajemen Sejarah Nagari & Foto Dokumentasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Periode & Cerita Sejarah</h4>
        <p class="text-muted small mb-0">Kelola narasi sejarah singkat, periodisasi sejarah, dan foto dokumentasi tempo dulu</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahSejarah">
        <i class="bi bi-plus-circle me-1"></i> Tambah Entri Sejarah
    </button>
</div>

<div class="card-custom">
    @if ($sejarahList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>FOTO</th>
                        <th>URUTAN</th>
                        <th>TIPE</th>
                        <th>JUDUL</th>
                        <th>PERIODE</th>
                        <th>ISI RINGKAS</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sejarahList as $item)
                        <tr>
                            <td style="width: 70px;">
                                @if ($item->gambar)
                                    <img src="{{ asset($item->gambar) }}" alt="Foto Sejarah" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 45px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary rounded-pill px-3">{{ $item->urutan }}</span></td>
                            <td>
                                <span class="badge bg-{{ $item->tipe === 'singkat' ? 'info' : 'success' }} text-dark">
                                    {{ ucfirst($item->tipe) }}
                                </span>
                            </td>
                            <td><strong class="text-dark">{{ $item->judul }}</strong></td>
                            <td>{{ $item->periode ?: '-' }}</td>
                            <td><small class="text-muted">{{ Str::limit($item->isi, 60) }}</small></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditSejarah{{ $item->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.konten.sejarah.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus entri sejarah ini?');">
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
                        <div class="modal fade" id="modalEditSejarah{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.konten.sejarah.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-success text-white p-4">
                                            <h5 class="modal-title fw-bold">Edit Sejarah Nagari</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Tipe</label>
                                                    <select name="tipe" class="form-select" required>
                                                        <option value="singkat" {{ $item->tipe === 'singkat' ? 'selected' : '' }}>Singkat (Gambaran Umum)</option>
                                                        <option value="periode" {{ $item->tipe === 'periode' ? 'selected' : '' }}>Periode (Era Waktu)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Judul</label>
                                                    <input type="text" name="judul" class="form-control" value="{{ $item->judul }}" required />
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small fw-bold">Urutan</label>
                                                    <input type="number" name="urutan" class="form-control" value="{{ $item->urutan }}" required />
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Rentang Periode Waktu</label>
                                                    <input type="text" name="periode" class="form-control" value="{{ $item->periode }}" placeholder="Contoh: ±1800-an / 1900-1945" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Ganti Foto Dokumentasi</label>
                                                    <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
                                                </div>
                                            </div>
                                            @if ($item->gambar)
                                                <div class="mb-3">
                                                    <small class="text-muted d-block mb-1">Foto Saat Ini:</small>
                                                    <img src="{{ asset($item->gambar) }}" alt="Foto" class="img-thumbnail rounded-3" style="max-height: 100px;" />
                                                </div>
                                            @endif
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Isi Narasi Sejarah</label>
                                                <textarea name="isi" rows="6" class="form-control" required>{{ $item->isi }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer p-4 pt-0 border-0">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Perubahan</button>
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
        <div class="text-center py-4 text-muted">Belum ada data sejarah.</div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahSejarah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.sejarah.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tambah Entri Sejarah Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Tipe</label>
                            <select name="tipe" class="form-select" required>
                                <option value="periode">Periode (Era Waktu)</option>
                                <option value="singkat">Singkat (Gambaran Umum)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Judul</label>
                            <input type="text" name="judul" class="form-control" placeholder="Contoh: Era Kerajaan Minangkabau" required />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Urutan</label>
                            <input type="number" name="urutan" class="form-control" value="{{ $sejarahList->count() + 1 }}" required />
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Rentang Periode Waktu</label>
                            <input type="text" name="periode" class="form-control" placeholder="Contoh: 1945–1979" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Foto Dokumentasi Sejarah (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Isi Narasi Sejarah</label>
                        <textarea name="isi" rows="6" class="form-control" placeholder="Tuliskan cerita sejarah..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
