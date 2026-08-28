@extends('admin.layouts.admin')

@section('title', 'Kelola UMKM Nagari')
@section('page-title', 'Manajemen Katalog UMKM Nagari')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Pelaku UMKM Nagari</h4>
        <p class="text-muted small mb-0">Kelola dan pantau seluruh direktori UMKM lokal beserta foto produk</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahUmkm">
        <i class="bi bi-plus-circle me-1"></i> Tambah UMKM Baru
    </button>
</div>

<div class="card-custom">
    @if ($umkmList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase">
                        <th>FOTO</th>
                        <th>NAMA USAHA</th>
                        <th>PEMILIK</th>
                        <th>KATEGORI</th>
                        <th>KONTAK WA</th>
                        <th>DESKRIPSI</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($umkmList as $u)
                        <tr>
                            <td style="width: 80px;">
                                @if ($u->foto)
                                    <img src="{{ asset($u->foto) }}" alt="{{ $u->nama_usaha }}" class="rounded-3 shadow-sm" style="width: 60px; height: 45px; object-fit: cover;" />
                                @else
                                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center small fw-bold" style="width: 60px; height: 45px;">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                @endif
                            </td>
                            <td><strong class="text-dark">{{ $u->nama_usaha }}</strong></td>
                            <td>{{ $u->pemilik }}</td>
                            <td><span class="badge bg-warning text-dark">{{ $u->kategori }}</span></td>
                            <td><small class="text-muted">{{ $u->whatsapp ?: '-' }}</small></td>
                            <td><small class="text-muted">{{ Str::limit($u->deskripsi, 50) }}</small></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditU{{ $u->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('admin.konten.umkm.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus UMKM ini?');">
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
                        <div class="modal fade" id="modalEditU{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content rounded-4 border-0">
                                    <form action="{{ route('admin.konten.umkm.update', $u->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-success text-white p-4">
                                            <h5 class="modal-title fw-bold">Edit Data UMKM</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3 mb-3">
                                                <div class="col-md-8">
                                                    <label class="form-label small fw-bold">Nama Usaha</label>
                                                    <input type="text" name="nama_usaha" class="form-control" value="{{ $u->nama_usaha }}" required />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small fw-bold">Kategori</label>
                                                    <select name="kategori" class="form-select" required>
                                                        <option value="Kuliner" {{ $u->kategori === 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                                                        <option value="Kerajinan" {{ $u->kategori === 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                                                        <option value="Pertanian" {{ $u->kategori === 'Pertanian' ? 'selected' : '' }}>Pertanian</option>
                                                        <option value="Jasa" {{ $u->kategori === 'Jasa' ? 'selected' : '' }}>Jasa</option>
                                                        <option value="Perdagangan" {{ $u->kategori === 'Perdagangan' ? 'selected' : '' }}>Perdagangan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Nama Pemilik</label>
                                                    <input type="text" name="pemilik" class="form-control" value="{{ $u->pemilik }}" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Tahun Berdiri</label>
                                                    <input type="text" name="tahun_berdiri" class="form-control" value="{{ $u->tahun_berdiri }}" />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small fw-bold">Jam Operasional</label>
                                                    <input type="text" name="jam_operasional" class="form-control" value="{{ $u->jam_operasional }}" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">WhatsApp</label>
                                                    <input type="text" name="whatsapp" class="form-control" value="{{ $u->whatsapp }}" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold">Instagram</label>
                                                    <input type="text" name="instagram" class="form-control" value="{{ $u->instagram }}" />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Upload Foto Produk / Usaha (Opsional)</label>
                                                    <input type="file" name="foto" class="form-control" accept="image/*" />
                                                    @if ($u->foto)
                                                        <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($u->foto) }}" target="_blank">Lihat Foto</a></small>
                                                    @endif
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">Deskripsi Usaha</label>
                                                    <textarea name="deskripsi" rows="4" class="form-control" required>{{ $u->deskripsi }}</textarea>
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
            {{ $umkmList->links() }}
        </div>
    @else
        <div class="text-center py-4 text-muted">Belum ada data UMKM.</div>
    @endif
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahUmkm" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.umkm.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tambah UMKM Baru ke Katalog</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nama Usaha</label>
                            <input type="text" name="nama_usaha" class="form-control" placeholder="Contoh: Songket Koto Malintang" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Kuliner">Kuliner</option>
                                <option value="Kerajinan">Kerajinan</option>
                                <option value="Pertanian">Pertanian</option>
                                <option value="Jasa">Jasa</option>
                                <option value="Perdagangan">Perdagangan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Pemilik</label>
                            <input type="text" name="pemilik" class="form-control" placeholder="Nama pemilik" required />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tahun Berdiri</label>
                            <input type="text" name="tahun_berdiri" class="form-control" placeholder="Contoh: 2022" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Jam Operasional</label>
                            <input type="text" name="jam_operasional" class="form-control" placeholder="08.00 - 17.00" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">WhatsApp</label>
                            <input type="text" name="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Instagram</label>
                            <input type="text" name="instagram" class="form-control" placeholder="@username" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Upload Foto Produk / Usaha (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Deskripsi Usaha</label>
                            <textarea name="deskripsi" rows="4" class="form-control" placeholder="Jelaskan produk usaha..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan UMKM</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
