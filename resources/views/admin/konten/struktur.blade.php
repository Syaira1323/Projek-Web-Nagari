@extends('admin.layouts.admin')

@section('title', 'Kelola Struktur Organisasi')
@section('page-title', 'Manajemen Bagan & Posisi Struktur Organisasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Daftar Posisi & Bagan Struktur</h4>
        <p class="text-muted small mb-0">Kelola posisi, pembagian tugas, serta foto/bagan visual struktur pemerintahan nagari</p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahStruktur">
        <i class="bi bi-plus-circle me-1"></i> Tambah Posisi Baru
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table align-middle table-hover">
            <thead class="table-light">
                <tr class="small text-muted text-uppercase">
                    <th>BAGAN / FOTO</th>
                    <th>NAMA POSISI / JABATAN</th>
                    <th>TUGAS & KETERANGAN</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($strukturList as $item)
                    <tr>
                        <td style="width: 70px;">
                            @if ($item->foto_bagan)
                                <img src="{{ asset($item->foto_bagan) }}" alt="Bagan" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;" />
                            @else
                                <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 45px;">
                                    <i class="bi bi-diagram-3"></i>
                                </div>
                            @endif
                        </td>
                        <td><strong class="text-dark">{{ $item->nama }}</strong></td>
                        <td><small class="text-muted">{{ Str::limit($item->keterangan, 90) }}</small></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditStruktur{{ $item->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.konten.struktur.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus posisi struktur ini?');">
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
                    <div class="modal fade" id="modalEditStruktur{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content rounded-4 border-0">
                                <form action="{{ route('admin.konten.struktur.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header bg-success text-white p-4">
                                        <h5 class="modal-title fw-bold">Edit Posisi Struktur</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Nama Posisi</label>
                                            <input type="text" name="nama" class="form-control" value="{{ $item->nama }}" required />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Foto Bagan Organisasi (Opsional)</label>
                                            <input type="file" name="foto_bagan" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
                                        </div>
                                        @if ($item->foto_bagan)
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Bagan Saat Ini:</small>
                                                <img src="{{ asset($item->foto_bagan) }}" alt="Bagan" class="img-thumbnail rounded-3" style="max-height: 100px;" />
                                            </div>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Tugas & Fungsi</label>
                                            <textarea name="keterangan" rows="4" class="form-control" required>{{ $item->keterangan }}</textarea>
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
<div class="modal fade" id="modalTambahStruktur" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <form action="{{ route('admin.konten.struktur.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">Tambah Posisi Struktur Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Posisi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Kaur Perencanaan" required />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto Bagan Organisasi (Opsional)</label>
                        <input type="file" name="foto_bagan" class="form-control" accept=".jpg,.jpeg,.png,.webp" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tugas & Fungsi</label>
                        <textarea name="keterangan" rows="4" class="form-control" placeholder="Jelaskan uraian tugas jabatan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Posisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
