@extends('admin.layouts.admin')

@section('title', 'Kelola Perangkat Nagari')
@section('page-title', 'Manajemen Perangkat & Aparatur Nagari')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-person-lines-fill text-success me-2"></i>Daftar Aparatur Pemerintah Nagari
        </h4>
        <p class="text-muted small mb-0">
            Kelola susunan perangkat nagari, pimpinan, staf urusan, wali jorong, serta foto resmi aparatur
        </p>
    </div>
    <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahPerangkat">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Perangkat Baru
    </button>
</div>

<div class="card-custom">
    @if ($perangkatList->count() > 0)
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr class="small text-muted text-uppercase" style="font-size: 0.78rem;">
                        <th style="width: 70px;">FOTO</th>
                        <th>NAMA APARATUR</th>
                        <th>JABATAN</th>
                        <th>PANGKAT / GOL & NIP</th>
                        <th>TTL & PENDIDIKAN</th>
                        <th class="text-end" style="width: 140px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perangkatList as $p)
                        @php
                            $pFoto = null;
                            if ($p->foto) {
                                if (file_exists(public_path($p->foto))) {
                                    $pFoto = asset($p->foto);
                                } elseif (file_exists(public_path('image/' . $p->foto))) {
                                    $pFoto = asset('image/' . $p->foto);
                                } else {
                                    $pFoto = asset($p->foto);
                                }
                            }
                        @endphp
                        <tr>
                            <td>
                                @if ($pFoto)
                                    <img src="{{ $pFoto }}" alt="{{ $p->nama }}" class="rounded-circle shadow-sm border" style="width: 48px; height: 48px; object-fit: cover;" />
                                @else
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.1rem;">
                                        {{ strtoupper(substr($p->nama, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block" style="font-size: 0.95rem;">{{ $p->nama }}</strong>
                                <span class="badge bg-light text-secondary border small">{{ $p->jenis_kelamin ?? '-' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1" style="font-size: 0.85rem;">{{ $p->jabatan }}</span>
                            </td>
                            <td>
                                <small class="d-block text-dark"><i class="bi bi-award text-success me-1"></i>Gol: {{ $p->pangkat_gol ?: '-' }}</small>
                                <small class="text-muted"><i class="bi bi-card-heading text-secondary me-1"></i>NIP: {{ $p->nip ?: '-' }}</small>
                            </td>
                            <td>
                                <small class="d-block text-dark"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $p->tempat_lahir ?: '-' }}, {{ $p->tanggal_lahir ? $p->tanggal_lahir->format('d/m/Y') : '-' }}</small>
                                <small class="text-muted"><i class="bi bi-mortarboard text-primary me-1"></i>Pendidikan: {{ $p->pendidikan_terakhir ?: '-' }}</small>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditP{{ $p->id }}" title="Edit Aparatur">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalHapusP{{ $p->id }}" title="Hapus Aparatur">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT PERANGKAT -->
                        <div class="modal fade" id="modalEditP{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.perangkat.update', $p->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-dark text-white p-4">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square text-warning me-2"></i>Edit Perangkat Nagari
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" name="nama" class="form-control" value="{{ $p->nama }}" required />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Jabatan <span class="text-danger">*</span></label>
                                                    <input type="text" name="jabatan" class="form-control" value="{{ $p->jabatan }}" required />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Pangkat / Golongan</label>
                                                    <input type="text" name="pangkat_gol" class="form-control" value="{{ $p->pangkat_gol }}" placeholder="Contoh: Penata Muda / -" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">NIP</label>
                                                    <input type="text" name="nip" class="form-control" value="{{ $p->nip }}" placeholder="NIP jika ada" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Jenis Kelamin</label>
                                                    <select name="jenis_kelamin" class="form-select">
                                                        <option value="Laki-laki" {{ $p->jenis_kelamin === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ $p->jenis_kelamin === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Pendidikan Terakhir</label>
                                                    <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ $p->pendidikan_terakhir }}" placeholder="Contoh: S1 / SMA / SLTA" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Tempat Lahir</label>
                                                    <input type="text" name="tempat_lahir" class="form-control" value="{{ $p->tempat_lahir }}" placeholder="Contoh: Koto Malintang" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-bold text-dark">Tanggal Lahir</label>
                                                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $p->tanggal_lahir ? $p->tanggal_lahir->format('Y-m-d') : '' }}" />
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold text-dark">Ganti Foto Aparatur (Opsional)</label>
                                                    <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewPerangkatImg(this, 'editFotoPreview{{ $p->id }}', 'editFotoBox{{ $p->id }}')" />
                                                    <small class="text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, WEBP (Maksimal 4MB)</small>
                                                </div>

                                                <div class="col-12" id="editFotoBox{{ $p->id }}" style="{{ $pFoto ? '' : 'display: none;' }}">
                                                    <label class="form-label small fw-bold text-secondary d-block">Foto Aparatur Saat Ini / Preview:</label>
                                                    <img id="editFotoPreview{{ $p->id }}" src="{{ $pFoto ?: '' }}" alt="{{ $p->nama }}" class="rounded-circle shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;" />
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

                        <!-- MODAL HAPUS PERANGKAT -->
                        <div class="modal fade" id="modalHapusP{{ $p->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('admin.konten.perangkat.destroy', $p->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <i class="bi bi-exclamation-triangle text-danger fs-1 mb-2 d-block"></i>
                                            <h6 class="fw-bold text-dark mb-1">Hapus Perangkat Nagari?</h6>
                                            <p class="text-muted small mb-3"><strong>"{{ $p->nama }}"</strong> ({{ $p->jabatan }}) akan dihapus permanen.</p>
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
            <i class="bi bi-person-lines-fill fs-1 d-block mb-2"></i>
            <p class="mb-0">Belum ada data perangkat nagari. Silakan tambahkan aparatur baru.</p>
        </div>
    @endif
</div>

<!-- MODAL TAMBAH PERANGKAT -->
<div class="modal fade" id="modalTambahPerangkat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.konten.perangkat.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-success text-white p-4">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Tambah Perangkat Nagari Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: HENDRA YANTO" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Wali Nagari / Kaur Keuangan" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pangkat / Golongan</label>
                            <input type="text" name="pangkat_gol" class="form-control" placeholder="Contoh: Penata Muda / -" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="NIP jika ada / -" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Pendidikan Terakhir</label>
                            <input type="text" name="pendidikan_terakhir" class="form-control" placeholder="Contoh: S1 / SLTA / Sederajat" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control" placeholder="Contoh: Koto Malintang" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" />
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-dark">Upload Foto Aparatur (Opsional)</label>
                            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewPerangkatImg(this, 'tambahFotoPreview', 'tambahFotoBox')" />
                            <small class="text-muted" style="font-size: 0.75rem;">Mendukung JPG, PNG, WEBP (Maksimal 4MB)</small>
                        </div>

                        <div class="col-12" id="tambahFotoBox" style="display: none;">
                            <label class="form-label small fw-bold text-secondary d-block">Preview Foto:</label>
                            <img id="tambahFotoPreview" src="#" alt="Preview Foto" class="rounded-circle shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 pt-0 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Data Aparatur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewPerangkatImg(input, previewId, containerId) {
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
