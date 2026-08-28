@extends('admin.layouts.admin')

@section('title', 'Kelola Perangkat Nagari')
@section('page-title', 'Manajemen Perangkat & Aparatur Nagari')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Aparatur Pemerintah Nagari</h4>
        <p class="text-muted small mb-0">Kelola susunan perangkat nagari, staf, dan foto resmi</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahPerangkat">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Perangkat Baru
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>FOTO</th>
                    <th>NAMA APARATUR</th>
                    <th>JABATAN</th>
                    <th>NIP</th>
                    <th>TTL & PENDIDIKAN</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($perangkatList as $p)
                    <tr>
                        <td style="width: 70px;">
                            @if ($p->foto)
                                <img src="{{ asset($p->foto) }}" alt="{{ $p->nama }}" class="rounded-circle shadow-sm" style="width: 48px; height: 48px; object-fit: cover;" />
                            @else
                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px;">
                                    {{ substr($p->nama, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark d-block">{{ $p->nama }}</strong>
                            <small class="text-muted">{{ $p->jenis_kelamin }}</small>
                        </td>
                        <td><span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $p->jabatan }}</span></td>
                        <td>{{ $p->nip ?: '-' }}</td>
                        <td>
                            <small class="d-block text-dark">{{ $p->tempat_lahir ?: '-' }}, {{ $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : '-' }}</small>
                            <small class="text-muted">Pendidikan: {{ $p->pendidikan_terakhir ?: '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $p->status_keaktifan === 'Aktif' ? 'success' : 'danger' }}">
                                {{ $p->status_keaktifan }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditP{{ $p->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.konten.perangkat.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data perangkat ini?');">
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
                    <div class="modal fade" id="modalEditP{{ $p->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4 border-0">
                                <form action="{{ route('admin.konten.perangkat.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-success text-white p-4">
                                        <h5 class="modal-title fw-bold">Edit Perangkat Nagari</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="{{ $p->nama }}" required />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Jabatan</label>
                                                <input type="text" name="jabatan" class="form-control" value="{{ $p->jabatan }}" required />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">NIP (Opsional)</label>
                                                <input type="text" name="nip" class="form-control" value="{{ $p->nip }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Jenis Kelamin</label>
                                                <select name="jenis_kelamin" class="form-select" required>
                                                    <option value="Laki-laki" {{ $p->jenis_kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                    <option value="Perempuan" {{ $p->jenis_kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir" class="form-control" value="{{ $p->tempat_lahir }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Tanggal Lahir</label>
                                                <input type="date" name="tanggal_lahir" class="form-control" value="{{ $p->tanggal_lahir ? $p->tanggal_lahir->format('Y-m-d') : '' }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Pendidikan Terakhir</label>
                                                <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $p->pendidikan_terakhir }}" />
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Status Keaktifan</label>
                                                <select name="status_keaktifan" class="form-select" required>
                                                    <option value="Aktif" {{ $p->status_keaktifan === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="Nonaktif" {{ $p->status_keaktifan === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Upload Foto Aparatur (Opsional)</label>
                                                <input type="file" name="foto" class="form-control" accept="image/*" />
                                                @if ($p->foto)
                                                    <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($p->foto) }}" target="_blank">Lihat Foto</a></small>
                                                @endif
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
<div class="modal fade" id="modalTambahPerangkat" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.perangkat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tambah Perangkat Nagari Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama aparatur" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jabatan</label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Kaur Keuangan" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">NIP (Opsional)</label>
                            <input type="text" name="nip" class="form-control" placeholder="NIP jika ada" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Koto Malintang" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control" placeholder="S1 / SMA / dll" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Status Keaktifan</label>
                            <select name="status_keaktifan" class="form-select" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Upload Foto Aparatur (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
