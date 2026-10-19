// =========================================================================
// admin.js — Logika Panel Administrator Nagari Koto Malintang
// Terhubung ke REST API Laravel (Auth.js) & Database MySQL
// =========================================================================

const authClient = () => (typeof window !== 'undefined' && window.KotoAuth) || (typeof KotoAuth !== 'undefined' ? KotoAuth : (typeof globalThis !== 'undefined' ? globalThis.KotoAuth : null));

let currentFilter = 'semua';
let currentAdminSuratList = [];
let currentActiveSurat = null;

function showPage(id) {
  document.querySelectorAll('.admin-page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item-admin').forEach(n => n.classList.remove('active'));
  const target = document.getElementById('page-' + id);
  if (target) target.classList.add('active');

  document.querySelectorAll('.nav-item-admin').forEach(item => {
    const onclickAttr = item.getAttribute('onclick') || '';
    if (onclickAttr.includes(`'${id}'`) || onclickAttr.includes(`"${id}"`)) {
      item.classList.add('active');
    }
  });

  const titles = {
    dashboard: 'Dashboard Administrator',
    berita: 'Manajemen Berita Publik (Kaba Nagari)',
    galeri: 'Kelola Galeri Foto Publik',
    profil: 'Profil Publik Nagari',
    surat: 'Verifikasi & Penomoran Surat',
    arsip: 'Berkas Arsip Surat Nagari',
    users: 'Data Pengguna & Warga Terdaftar',
    pengaturan: 'Pengaturan Kantor Nagari'
  };
  if (document.getElementById('pageTitle')) {
    document.getElementById('pageTitle').textContent = titles[id] || id;
  }

  if (id === 'dashboard') loadDashboard();
  if (id === 'surat') renderSuratTable(currentFilter);
  if (id === 'arsip') renderArsipTable();
  if (id === 'berita') renderBeritaTable();
  if (id === 'profil') loadProfilNagari();
  if (id === 'users') renderUsersTable();
  if (id === 'pengaturan') loadPengaturanKantor();
}

// 1. DASHBOARD
async function loadDashboard() {
  const tbody = document.getElementById('dashSuratList');
  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminDashboard();
    if (!res || !res.success) throw new Error((res && res.message) || 'Respons dashboard tidak valid.');

    const stats = res.stats || {};
    if (document.getElementById('statSuratBaru')) document.getElementById('statSuratBaru').textContent = stats.surat_menunggu || 0;
    if (document.getElementById('statSuratWali')) document.getElementById('statSuratWali').textContent = stats.surat_wali || 0;
    if (document.getElementById('statSuratArsip')) document.getElementById('statSuratArsip').textContent = stats.surat_disetujui || 0;
    if (document.getElementById('badgeSurat')) document.getElementById('badgeSurat').textContent = stats.surat_menunggu || 0;

    const list = res.surat_terbaru || [];
    if (tbody) {
      tbody.innerHTML = list.length === 0
        ? '<tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada permohonan surat masuk baru saat ini.</td></tr>'
        : list.map(s => {
            const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
            const nik = s.nik_pemohon || (s.user ? s.user.nik : '-');
            const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
            const tgl = s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID') : '-';
            return `<tr>
              <td><strong>${pemohon}</strong><div class="small text-muted">NIK: ${nik}</div></td>
              <td>${jenis}</td>
              <td>${tgl}</td>
              <td>${labelStatus(s.status)}</td>
              <td><button class="btn btn-sm btn-outline-success" onclick="openSuratDetail('${s.id}')">Buka & Periksa</button></td>
            </tr>`;
          }).join('');
    }

    try {
      const kabaRes = await (window.KotoAuth || KotoAuth).getAdminKaba();
      if (kabaRes && kabaRes.success && document.getElementById('statBerita')) {
        document.getElementById('statBerita').textContent = kabaRes.total || (kabaRes.data ? kabaRes.data.length : 0);
      }
    } catch (e) {
      if (document.getElementById('statBerita')) document.getElementById('statBerita').textContent = '-';
      console.warn('Statistik berita tidak dapat dimuat:', e.message);
    }
  } catch (e) {
    console.warn('Dashboard API error:', e.message);
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Gagal mengambil data dashboard: ${e.message}</td></tr>`;
  }
}

// 2. PERSURATAN
async function renderSuratTable(f = 'semua') {
  currentFilter = f;
  const tbody = document.getElementById('suratTableBody');
  if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil data surat dari server...</td></tr>';

  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminSuratList(f);
    currentAdminSuratList = (res && res.data) ? res.data : [];

    if (!tbody) return;
    if (currentAdminSuratList.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada permohonan surat pada kategori ini.</td></tr>';
      return;
    }

    tbody.innerHTML = currentAdminSuratList.map(s => {
      const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
      const nik = s.nik_pemohon || (s.user ? s.user.nik : '-');
      const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
      const noSurat = s.nomor_surat || '<em class="text-muted">(Belum Diberi Nomor)</em>';
      const tgl = s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID') : '-';

      return `<tr>
        <td><strong>${pemohon}</strong><div class="small text-muted">NIK: ${nik}</div></td>
        <td><strong>${jenis}</strong></td>
        <td><span class="font-monospace fw-bold text-primary">${noSurat}</span></td>
        <td>${tgl}</td>
        <td>${labelStatus(s.status)}</td>
        <td>
          <button class="btn btn-sm btn-outline-success me-1" onclick="openSuratDetail('${s.id}')" title="Periksa & Beri Nomor">
            <i class="bi bi-pencil-square me-1"></i> Periksa
          </button>
          ${(s.status === 'menunggu_admin' || s.status === 'dikembalikan_ke_admin') ? `
            <button class="btn btn-sm btn-outline-warning text-dark me-1" onclick="openMintaRevisiModal('${s.id}')" title="Minta Perbaikan Warga">
              <i class="bi bi-arrow-repeat"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="openTolakModal('${s.id}')" title="Tolak Berkas">
              <i class="bi bi-x-circle"></i>
            </button>` : ''}
        </td>
      </tr>`;
    }).join('');
  } catch (e) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data surat: ${e.message}</td></tr>`;
  }
}

function filterSurat(f) {
  renderSuratTable(f);
}

async function openSuratDetail(id) {
  let s = null;
  let berkasList = [];
  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminSuratDetail(id);
    if (res && res.success) {
      s = res.data;
      berkasList = res.berkas || s.berkas_persyaratans || [];
    }
  } catch (e) {
    s = currentAdminSuratList.find(x => String(x.id) === String(id));
    if (!s) {
      toast('Gagal mengambil detail surat: ' + e.message, 'error');
      return;
    }
    berkasList = s.berkas_persyaratans || [];
  }

  if (!s) return;
  currentActiveSurat = s;

  const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
  const nik = s.nik_pemohon || (s.user ? s.user.nik : '-');
  const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
  const subjek = s.keperluan || '-';
  const defaultNo = s.nomor_surat || `470 / 0${Math.floor(Math.random()*80 + 10)} / WN-KM / VIII / 2026`;
  const isiText = s.isi_surat || `Yang bertanda tangan di bawah ini Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, menerangkan bahwa:\n\nNama: ${pemohon}\nNIK: ${nik}\n\nAdalah benar warga tercatat di Nagari Koto Malintang. Surat keterangan ini dibuat untuk keperluan: ${subjek}.`;

  let lampiranHtml = '';
  if (berkasList && berkasList.length > 0) {
    lampiranHtml = `
      <div class="mb-3 p-3 rounded" style="background:#f8fafc;border:1px solid #cbd5e1;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="fw-bold text-dark mb-0" style="font-size:0.85rem;"><i class="bi bi-folder2-open text-primary me-1"></i>Persyaratan Berkas (${berkasList.length} Dokumen Terlampir):</h6>
          <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size:0.7rem;"><i class="bi bi-check-all me-1"></i>Lengkap</span>
        </div>
        <div class="row g-2">
          ${berkasList.map(b => {
            const dlUrl = b.download_url || (`warga/berkas/${b.id}/download`);
            const label = b.label_jenis || b.jenis_berkas || 'Berkas Persyaratan';
            const filename = b.nama_file_asli || '-';
            const size = b.formatted_size ? ` (${b.formatted_size})` : '';
            return `
            <div class="col-md-6">
              <div class="p-2 rounded bg-white border d-flex align-items-center justify-content-between shadow-sm">
                <div class="overflow-hidden me-2">
                  <span class="badge bg-success bg-opacity-10 text-success font-monospace mb-1" style="font-size:0.7rem;"><i class="bi bi-check-circle-fill me-1"></i>${label}</span>
                  <div class="small fw-semibold text-dark text-truncate" title="${filename}">${filename}</div>
                  <small class="text-muted" style="font-size:0.72rem;">${size}</small>
                </div>
                <a href="${dlUrl}" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1 text-nowrap" title="Lihat / Download Berkas">
                  <i class="bi bi-box-arrow-up-right me-1"></i> Lihat
                </a>
              </div>
            </div>`;
          }).join('')}
        </div>
      </div>`;
  } else {
    lampiranHtml = `<div class="mb-3 p-2 rounded bg-light border text-muted small"><i class="bi bi-info-circle me-1"></i> Belum ada berkas lampiran persyaratan terlampir.</div>`;
  }

  let tanggunganHtml = '';
  if (s.data_spesifik && Array.isArray(s.data_spesifik.tanggungan_keluarga) && s.data_spesifik.tanggungan_keluarga.length > 0) {
    tanggunganHtml = `
      <div class="mb-3 p-3 rounded" style="background:#f8fafc;border:1px solid #cbd5e1;">
        <h6 class="fw-bold text-dark mb-2" style="font-size:0.85rem;"><i class="bi bi-people-fill text-success me-1"></i>Daftar Tanggungan Keluarga (${s.data_spesifik.tanggungan_keluarga.length} Jiwa):</h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered bg-white small mb-0" style="font-size:0.75rem;">
            <thead class="table-light">
              <tr><th>No</th><th>Nama Lengkap</th><th>Jenis Kelamin</th><th>Umur</th><th>Hubungan</th></tr>
            </thead>
            <tbody>
              ${s.data_spesifik.tanggungan_keluarga.map((t, idx) => `
                <tr>
                  <td class="text-center">${idx + 1}</td>
                  <td><strong>${t.nama}</strong></td>
                  <td>${t.jenis_kelamin}</td>
                  <td>${t.umur} thn</td>
                  <td>${t.hubungan}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        </div>
      </div>
    `;
  }

  const isPerluRevisi = s.status === 'perlu_revisi_warga';
  const isDikembalikanWali = s.status === 'dikembalikan_ke_admin';
  const isMenungguAdmin = s.status === 'menunggu_admin';
  const canEdit = isMenungguAdmin || isDikembalikanWali;

  let alertCatatanHtml = '';
  if (isDikembalikanWali && s.catatan_wali) {
    alertCatatanHtml = `
      <div class="alert alert-danger p-3 mb-3 border-danger shadow-sm">
        <div class="d-flex align-items-center mb-1">
          <i class="bi bi-arrow-counterclockwise text-danger me-2 fs-5"></i>
          <strong class="text-danger">Catatan Pengembalian dari Wali Nagari:</strong>
        </div>
        <div class="text-dark ms-4 fw-medium">${s.catatan_wali}</div>
        <div class="small text-muted ms-4 mt-2">
          <em>Petunjuk:</em> Perbaiki nomor/redaksi surat jika kekeliruan internal nagari, atau klik <strong>Minta Perbaikan Warga</strong> jika kesalahan berasal dari berkas/data pemohon.
        </div>
      </div>
    `;
  } else if (isPerluRevisi && s.catatan_admin) {
    alertCatatanHtml = `
      <div class="alert alert-warning p-3 mb-3 border-warning shadow-sm">
        <div class="d-flex align-items-center mb-1">
          <i class="bi bi-arrow-repeat text-dark me-2 fs-5"></i>
          <strong class="text-dark">Catatan Permintaan Perbaikan untuk Warga:</strong>
        </div>
        <div class="text-dark ms-4">${s.catatan_admin}</div>
        <div class="small text-muted ms-4 mt-2">
          <i class="bi bi-info-circle me-1"></i> Pengajuan sedang dalam penanganan warga. Admin menunggu warga mengunggah berkas/data perbaikan.
        </div>
      </div>
    `;
  } else if (s.status === 'ditolak' && s.alasan_penolakan) {
    alertCatatanHtml = `
      <div class="alert alert-danger p-3 mb-3 border-danger shadow-sm">
        <div class="d-flex align-items-center mb-1">
          <i class="bi bi-x-circle-fill text-danger me-2 fs-5"></i>
          <strong class="text-danger">Alasan Penolakan:</strong>
        </div>
        <div class="text-dark ms-4">${s.alasan_penolakan}</div>
      </div>
    `;
  }

  document.getElementById('modalSuratBody').innerHTML = `
    ${alertCatatanHtml}
    ${lampiranHtml}
    ${tanggunganHtml}
    <div class="p-3 mb-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:0.85rem;">
      <div class="row g-2">
        <div class="col-6"><strong>Nama Pemohon:</strong> ${pemohon}</div>
        <div class="col-6"><strong>NIK:</strong> <code>${nik}</code></div>
        <div class="col-6"><strong>Jenis Surat:</strong> <span class="badge bg-success">${jenis}</span></div>
        <div class="col-6"><strong>Status Saat Ini:</strong> ${labelStatus(s.status)}</div>
        <div class="col-12"><strong>Keperluan:</strong> ${subjek}</div>
      </div>
    </div>

    <div class="mb-3 p-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;">
      <label class="form-label small fw-bold text-primary"><i class="bi bi-123 me-1"></i>Beri Nomor Surat Dinas Resmi Nagari:</label>
      <input type="text" id="modalNomorSurat" class="form-control fw-bold text-primary font-monospace" value="${s.nomor_surat || defaultNo}" placeholder="Contoh: 470 / 024 / WN-KM / VIII / 2026" ${canEdit ? '' : 'readonly'}/>
      <small class="text-muted" style="font-size:0.75rem;">Nomor surat akan tercetak pada naskah dinas resmi.</small>
    </div>

    <div class="mb-3">
      <label class="form-label small fw-bold">Redaksi Isi Naskah Surat:</label>
      <textarea id="modalIsiSurat" class="form-control font-monospace" rows="5" style="font-size:0.85rem;" ${canEdit ? '' : 'readonly'}>${isiText}</textarea>
    </div>
  `;

  let footButtons = `<button class="btn btn-sm btn-outline-secondary" onclick="closeModal('modalSurat')">Tutup</button>`;

  if (canEdit) {
    footButtons += `
      <button class="btn btn-sm btn-danger" onclick="closeModal('modalSurat'); openTolakModal('${s.id}');"><i class="bi bi-x-circle me-1"></i> Tolak Surat</button>
      <button class="btn btn-sm btn-warning text-dark fw-bold" onclick="closeModal('modalSurat'); openMintaRevisiModal('${s.id}');"><i class="bi bi-arrow-repeat me-1"></i> Minta Perbaikan Warga</button>
      <button class="btn-hijau" id="btnTeruskanWali" onclick="teruskanKeWali('${s.id}')"><i class="bi bi-arrow-right-circle-fill me-1"></i> ${isDikembalikanWali ? 'Simpan Nomor & Kirim Ulang ke Wali' : 'Simpan Nomor & Teruskan ke Wali Nagari'}</button>
    `;
  } else if (isPerluRevisi) {
    footButtons += `<span class="badge bg-warning text-dark align-self-center py-2 px-3"><i class="bi bi-hourglass-split me-1"></i>Menunggu Warga Memperbaiki Berkas</span>`;
  } else if (s.status === 'menunggu_wali_nagari' || s.status === 'menunggu_walinagari' || s.status === 'di_meja_wali') {
    footButtons += `<span class="badge bg-primary align-self-center py-2 px-3"><i class="bi bi-send-check me-1"></i>Surat Sedang Berada di Meja Wali Nagari</span>`;
  } else if (s.status === 'disetujui') {
    footButtons += `<button class="btn btn-sm btn-primary" onclick="cetakSuratResmi('${s.id}')"><i class="bi bi-printer-fill me-1"></i> Cetak Dokumen PDF</button>`;
  } else if (s.status === 'ditolak') {
    footButtons += `<span class="badge bg-danger align-self-center py-2 px-3"><i class="bi bi-x-circle me-1"></i>Permohonan Surat Ditolak</span>`;
  }

  document.getElementById('modalSuratFoot').innerHTML = footButtons;
  openModal('modalSurat');
}

async function teruskanKeWali(id) {
  const no = document.getElementById('modalNomorSurat').value.trim();
  const isi = document.getElementById('modalIsiSurat').value.trim();
  if (!no) { toast('Nomor surat dinas wajib diisi sebelum diteruskan!', 'error'); return; }

  const btn = document.getElementById('btnTeruskanWali');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan ke server...';
  }

  try {
    const res = await (window.KotoAuth || KotoAuth).verifySuratAdmin(id, {
      nomor_surat: no,
      isi_surat: isi
    });
    closeModal('modalSurat');
    toast(res.message || 'Surat berhasil diverifikasi & diteruskan ke Meja Wali Nagari!');
    await renderSuratTable(currentFilter);
    await loadDashboard();
  } catch (e) {
    let errMsg = e.message || 'Gagal memverifikasi surat.';
    if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
      errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
    } else if (errMsg.includes('422')) {
      errMsg = 'Data tidak dapat diproses. Periksa kembali nomor dan redaksi surat.';
    }
    toast(errMsg, 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-arrow-right-circle-fill me-1"></i> Simpan Nomor & Teruskan ke Wali Nagari';
    }
  }
}

function openMintaRevisiModal(id) {
  document.getElementById('revisiSuratId').value = id;
  document.getElementById('revisiCatatanAdmin').value = '';
  openModal('modalMintaRevisi');
}

async function submitMintaRevisi() {
  const id = document.getElementById('revisiSuratId').value;
  const catatan = document.getElementById('revisiCatatanAdmin').value.trim();
  if (!catatan) { toast('Catatan perbaikan wajib diisi untuk warga.', 'error'); return; }
  if (catatan.length < 5) { toast('Catatan perbaikan minimal 5 karakter.', 'error'); return; }

  const btn = document.getElementById('btnSubmitMintaRevisi');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
  }

  try {
    const res = await (window.KotoAuth || KotoAuth).mintaRevisiAdmin(id, catatan);
    closeModal('modalMintaRevisi');
    toast(res.message || 'Permintaan perbaikan berhasil dikirim kepada warga.');
    await renderSuratTable(currentFilter);
    await loadDashboard();
  } catch (e) {
    let errMsg = e.message || 'Gagal meminta perbaikan berkas.';
    if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
      errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
    } else if (errMsg.includes('422')) {
      errMsg = 'Data tidak dapat diproses. Periksa kembali isian Anda.';
    }
    toast(errMsg, 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Kirim Permintaan Perbaikan';
    }
  }
}

function openTolakModal(id) {
  document.getElementById('tolakSuratId').value = id;
  document.getElementById('tolakAlasan').value = '';
  openModal('modalTolak');
}

async function submitTolakSurat() {
  const id = document.getElementById('tolakSuratId').value;
  const alasan = document.getElementById('tolakAlasan').value.trim();
  if (!alasan) { toast('Alasan penolakan wajib diisi untuk warga.', 'error'); return; }

  try {
    const res = await (window.KotoAuth || KotoAuth).rejectSuratAdmin(id, alasan);
    closeModal('modalTolak');
    toast(res.message || 'Permohonan surat ditolak dan alasan terkirim ke warga.');
    await renderSuratTable(currentFilter);
    await loadDashboard();
  } catch (e) {
    let errMsg = e.message || 'Gagal menolak surat.';
    if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
      errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
    } else if (errMsg.includes('422')) {
      errMsg = 'Data tidak dapat diproses. Periksa kembali isian Anda.';
    }
    toast(errMsg, 'error');
  }
}

// 3. ARSIP SURAT
async function renderArsipTable() {
  const tbody = document.getElementById('arsipTableBody');
  if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil arsip...</td></tr>';

  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminSuratList('disetujui');
    const list = (res && res.data) ? res.data : [];

    if (!tbody) return;
    if (list.length === 0) {
      tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada berkas arsip surat yang selesai.</td></tr>';
      return;
    }

    tbody.innerHTML = list.map(s => {
      const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
      const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
      const no = s.nomor_surat || '-';
      const tglSelesai = s.disetujui_wali_at ? new Date(s.disetujui_wali_at).toLocaleDateString('id-ID') : '-';

      return `<tr>
        <td><span class="font-monospace fw-bold text-primary">${no}</span></td>
        <td><strong>${pemohon}</strong></td>
        <td>${jenis}</td>
        <td>${tglSelesai}</td>
        <td><span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Selesai & Berstempel</span></td>
        <td><button class="btn btn-sm btn-outline-primary" onclick="cetakSuratResmi('${s.id}')"><i class="bi bi-printer me-1"></i> Cetak</button></td>
      </tr>`;
    }).join('');
  } catch (e) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat arsip: ${e.message}</td></tr>`;
  }
}

async function backupLocalDisk() {
  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminSuratList('semua');
    const data = JSON.stringify(res.data || [], null, 2);
    const blob = new Blob([data], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Arsip_Surat_Koto_Malintang_' + new Date().toISOString().split('T')[0] + '.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    toast('Berkas cadangan arsip berhasil diunduh ke Local Disk!');
  } catch (e) {
    toast('Gagal mengekspor arsip: ' + e.message, 'error');
  }
}

// 4. BERITA / KABA NAGARI
async function renderBeritaTable() {
  const tbody = document.getElementById('beritaTableBody');
  if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil berita dari database MySQL...</td></tr>';

  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminKaba();
    const list = (res && res.data) ? res.data : [];

    if (!tbody) return;
    if (list.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada berita yang diterbitkan di database.</td></tr>';
      return;
    }

    tbody.innerHTML = list.map(b => {
      const ringkas = b.ringkasan || (b.isi ? b.isi.substring(0, 80) + '...' : '-');
      return `<tr>
        <td><strong>${b.judul}</strong></td>
        <td><span class="badge bg-success">${b.kategori}</span></td>
        <td>${b.tanggal || (b.created_at ? new Date(b.created_at).toLocaleDateString('id-ID') : '-')}</td>
        <td><small class="text-muted">${ringkas}</small></td>
        <td><button class="btn btn-sm btn-outline-success me-1" onclick="editBerita(${b.id})"><i class="bi bi-pencil"></i></button><button class="btn btn-sm btn-outline-danger" onclick="deleteBerita(${b.id})"><i class="bi bi-trash"></i></button></td>
      </tr>`;
    }).join('');
  } catch (e) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat berita: ${e.message}</td></tr>`;
  }
}

function openBeritaModal() {
  document.getElementById('bId').value = '';
  document.getElementById('beritaModalTitle').textContent = 'Tambah Berita (Kaba Nagari)';
  document.getElementById('bJudul').value = '';
  document.getElementById('bKategori').value = 'Pemerintahan';
  document.getElementById('bTanggal').value = new Date().toISOString().split('T')[0];
  document.getElementById('bIsi').value = '';
  document.getElementById('bGambar').value = '';
  document.getElementById('bFoto').value = '';
  document.getElementById('bFotoPreview').innerHTML = '';
  document.getElementById('bFotoExisting').innerHTML = '';
  openModal('modalBerita');
}

function renderFotoExisting(foto) {
  const box = document.getElementById('bFotoExisting');
  box.innerHTML = (foto || []).map(f => `<div class="position-relative"><img src="${f.gambar}" alt="Foto berita" class="rounded border" style="width:72px;height:56px;object-fit:cover"><button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0" style="width:20px;height:20px" onclick="deleteFotoBerita(${f.kaba_nagari_id},${f.id})">×</button></div>`).join('');
}

async function editBerita(id) {
  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminKabaDetail(id);
    const b = res.data;
    document.getElementById('bId').value = b.id;
    document.getElementById('beritaModalTitle').textContent = 'Edit Berita (Kaba Nagari)';
    document.getElementById('bJudul').value = b.judul || '';
    document.getElementById('bKategori').value = b.kategori || '';
    document.getElementById('bTanggal').value = b.tanggal || '';
    document.getElementById('bIsi').value = b.isi || '';
    document.getElementById('bGambar').value = '';
    document.getElementById('bFoto').value = '';
    document.getElementById('bFotoPreview').innerHTML = '';
    renderFotoExisting(b.gambar_kaba_nagaris);
    openModal('modalBerita');
  } catch (e) { toast('Gagal memuat berita: ' + e.message, 'error'); }
}

async function saveBerita() {
  const judul = document.getElementById('bJudul').value.trim();
  const kategori = document.getElementById('bKategori').value.trim();
  const tanggal = document.getElementById('bTanggal').value;
  const isi = document.getElementById('bIsi').value.trim();

  if (!judul || !isi) {
    toast('Judul dan isi berita wajib diisi!', 'error');
    return;
  }

  const btn = document.getElementById('btnSaveBerita');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
  }

  try {
    const data = new FormData();
    data.append('judul', judul); data.append('kategori', kategori); data.append('tanggal', tanggal); data.append('isi', isi);
    const cover = document.getElementById('bGambar').files[0];
    if (cover) data.append('gambar', cover);
    const id = document.getElementById('bId').value;
    const res = id ? await (window.KotoAuth || KotoAuth).updateAdminKaba(id, data) : await (window.KotoAuth || KotoAuth).tambahAdminKaba(data);
    const beritaId = id || res.data.id;
    const foto = document.getElementById('bFoto').files;
    if (foto.length) { const galeri = new FormData(); Array.from(foto).forEach(file => galeri.append('gambar[]', file)); await (window.KotoAuth || KotoAuth).uploadAdminKabaGambar(beritaId, galeri); }
    closeModal('modalBerita');
    toast(res.message || 'Berita berhasil diterbitkan ke database!');
    await renderBeritaTable();
    await loadDashboard();
  } catch (e) {
    toast('Gagal menyimpan berita: ' + e.message, 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = 'Simpan ke Database';
    }
  }
}

async function deleteFotoBerita(kabaId, fotoId) {
  if (!confirm('Hapus foto ini?')) return;
  try { await (window.KotoAuth || KotoAuth).deleteAdminKabaGambar(kabaId, fotoId); await editBerita(kabaId); toast('Foto berita dihapus.'); } catch (e) { toast('Gagal menghapus foto: ' + e.message, 'error'); }
}

document.addEventListener('change', e => {
  if (e.target && e.target.id === 'bFoto') {
    document.getElementById('bFotoPreview').innerHTML = Array.from(e.target.files).map(f => `<img src="${URL.createObjectURL(f)}" alt="Preview" class="rounded border" style="width:72px;height:56px;object-fit:cover">`).join('');
  }
});

async function deleteBerita(id) {
  if (!confirm('Apakah Anda yakin ingin menghapus berita ini dari database?')) return;
  try {
    const res = await (window.KotoAuth || KotoAuth).deleteAdminKaba(id);
    toast(res.message || 'Berita berhasil dihapus.');
    await renderBeritaTable();
    await loadDashboard();
  } catch (e) {
    toast('Gagal menghapus berita: ' + e.message, 'error');
  }
}

// 5. PROFIL NAGARI
async function loadProfilNagari() {
  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminProfil();
    if (res && res.success && res.data) {
      const p = res.data;
      if (document.getElementById('pNama')) document.getElementById('pNama').value = p.nama_nagari || '';
      if (document.getElementById('pKec')) document.getElementById('pKec').value = p.kecamatan || '';
      if (document.getElementById('pKab')) document.getElementById('pKab').value = p.kabupaten || '';
      if (document.getElementById('pProv')) document.getElementById('pProv').value = p.provinsi || '';
      if (document.getElementById('pPenduduk')) document.getElementById('pPenduduk').value = p.jumlah_penduduk || 0;
      if (document.getElementById('pJorong')) document.getElementById('pJorong').value = p.jumlah_jorong || 0;
      if (document.getElementById('pLuas')) document.getElementById('pLuas').value = p.luas_wilayah || 0;
      if (document.getElementById('pDeskripsi')) document.getElementById('pDeskripsi').value = p.deskripsi || '';
      if (document.getElementById('pAlamat')) document.getElementById('pAlamat').value = p.alamat || '';
      if (document.getElementById('pEmail')) document.getElementById('pEmail').value = p.email || '';
      if (document.getElementById('pTelepon')) document.getElementById('pTelepon').value = p.telepon || '';
    }
  } catch (e) {
    console.warn('Gagal memuat profil nagari:', e.message);
  }
}

async function saveProfilNagari(e) {
  if (e) e.preventDefault();
  const btn = document.getElementById('btnSaveProfil');
  const alertBox = document.getElementById('profilAlert');

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan ke MySQL...';
  }

  const payload = {
    nama_nagari: document.getElementById('pNama').value.trim(),
    kecamatan: document.getElementById('pKec').value.trim(),
    kabupaten: document.getElementById('pKab').value.trim(),
    provinsi: document.getElementById('pProv').value.trim(),
    jumlah_penduduk: parseInt(document.getElementById('pPenduduk').value) || 0,
    jumlah_jorong: parseInt(document.getElementById('pJorong').value) || 0,
    luas_wilayah: parseFloat(document.getElementById('pLuas').value) || 0,
    deskripsi: document.getElementById('pDeskripsi').value.trim(),
    alamat: document.getElementById('pAlamat').value.trim(),
    email: document.getElementById('pEmail').value.trim(),
    telepon: document.getElementById('pTelepon').value.trim(),
  };

  try {
    const res = await (window.KotoAuth || KotoAuth).updateAdminProfil(payload);
    toast(res.message || 'Profil Nagari berhasil diperbarui di MySQL!');
    if (alertBox) {
      alertBox.className = 'alert alert-success';
      alertBox.textContent = res.message || 'Profil Nagari berhasil diperbarui di MySQL!';
      alertBox.classList.remove('d-none');
      setTimeout(() => alertBox.classList.add('d-none'), 4000);
    }
  } catch (err) {
    toast('Gagal memperbarui profil: ' + err.message, 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Perubahan Profil ke Database';
    }
  }
}

// 6. DATA PENGGUNA (DARI MYSQL users TABLE)
async function renderUsersTable() {
  const tbody = document.getElementById('usersTableBody');
  if (tbody) tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil data akun dari database MySQL...</td></tr>';

  try {
    const res = await (window.KotoAuth || KotoAuth).getAdminWarga();
    const users = (res && res.data) ? res.data : [];

    if (!tbody) return;
    if (users.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada akun warga terdaftar di database.</td></tr>';
      return;
    }

    tbody.innerHTML = users.map(u => `
      <tr>
        <td><strong>${u.name}</strong></td>
        <td><code>${u.nik || '-'}</code><div class="small text-muted">@${u.username}</div></td>
        <td><span class="badge ${u.role === 'admin' ? 'bg-success' : (u.role === 'wali_nagari' ? 'bg-warning text-dark' : 'bg-primary')}">${u.role}</span></td>
        <td>${u.email || u.phone || '-'}</td>
        <td><span class="badge bg-secondary font-monospace">${u.surat_permohonans_count || 0} Surat</span></td>
      </tr>
    `).join('');
  } catch (e) {
    if (tbody) tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat pengguna: ${e.message}</td></tr>`;
  }
}

// 7. PENGATURAN KANTOR & IDENTITAS WALI NAGARI
async function loadPengaturanKantor() {
  try {
    const res = await (window.KotoAuth || KotoAuth).apiRequest('/admin/pengaturan');
    if (res && res.success && res.data) {
      const d = res.data;
      if (document.getElementById('setNamaWali')) document.getElementById('setNamaWali').value = d.nama_wali || d.nama_wali_nagari || '';
      if (document.getElementById('setGelarWali')) document.getElementById('setGelarWali').value = d.gelar_wali || d.gelar_wali_nagari || '';
      if (document.getElementById('setJabatanWali')) document.getElementById('setJabatanWali').value = d.jabatan_wali || d.jabatan_wali_nagari || '';
      if (document.getElementById('setNamaKantor')) document.getElementById('setNamaKantor').value = d.nama_kantor || '';
      if (document.getElementById('setJamLayanan')) document.getElementById('setJamLayanan').value = d.jam_layanan || '';
      if (document.getElementById('setTeleponKantor')) document.getElementById('setTeleponKantor').value = d.telepon_kantor || '';
      if (document.getElementById('setEmailKantor')) document.getElementById('setEmailKantor').value = d.email_kantor || '';
      if (document.getElementById('setAlamatKantor')) document.getElementById('setAlamatKantor').value = d.alamat_kantor || '';
    }
  } catch (e) {
    console.warn('Gagal memuat pengaturan kantor & wali nagari:', e.message);
  }
}

async function savePengaturanKantor(e) {
  if (e) e.preventDefault();
  const btn = document.getElementById('btnSavePengaturan');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan ke MySQL...';
  }
  try {
    const res = await (window.KotoAuth || KotoAuth).apiRequest('/admin/pengaturan', {
      method: 'PUT',
      body: {
        nama_wali: (document.getElementById('setNamaWali')?.value || '').trim(),
        gelar_wali: (document.getElementById('setGelarWali')?.value || '').trim(),
        jabatan_wali: (document.getElementById('setJabatanWali')?.value || '').trim(),
        nama_kantor: (document.getElementById('setNamaKantor')?.value || '').trim(),
        jam_layanan: (document.getElementById('setJamLayanan')?.value || '').trim(),
        telepon_kantor: (document.getElementById('setTeleponKantor')?.value || '').trim(),
        email_kantor: (document.getElementById('setEmailKantor')?.value || '').trim(),
        alamat_kantor: (document.getElementById('setAlamatKantor')?.value || '').trim(),
      }
    });
    toast(res.message || 'Pengaturan kantor & identitas Wali Nagari berhasil disimpan ke database!');
  } catch (err) {
    toast('Gagal menyimpan pengaturan: ' + err.message, 'error');
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Pengaturan Kantor & Wali Nagari';
    }
  }
}

// 8. CETAK DOKUMEN SURAT RESMI
async function cetakSuratResmi(id) {
  let s = currentAdminSuratList.find(x => String(x.id) === String(id));
  if (!s) {
    try {
      const res = await (window.KotoAuth || KotoAuth).getAdminSuratDetail(id);
      s = res.data;
    } catch (e) {}
  }
  if (!s) return;

  const printWin = window.open('', '_blank');
  const doc = printWin.document;
  doc.open();

  const jenis = (s.nama_jenis_surat || s.jenis_surat || 'SURAT KETERANGAN RESMI').toUpperCase();
  const nomor = s.nomor_surat || '470 / 024 / WN-KM / VIII / 2026';
  const isi = s.isi_surat || '';
  const tgl = s.disetujui_wali_at ? new Date(s.disetujui_wali_at).toLocaleDateString('id-ID') : new Date().toLocaleDateString('id-ID');

  const content = '<!DOCTYPE html><html><head><title>Surat Resmi - ' + jenis + '</title>' +
    '<style>' +
    'body { font-family: "Times New Roman", serif; padding: 40px 60px; color: #000; line-height: 1.8; }' +
    '.kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: center; gap: 20px; }' +
    '.kop img { width: 70px; } .kop h4 { margin: 2px 0; font-size: 1.15rem; text-transform: uppercase; }' +
    '.kop h3 { margin: 2px 0; font-size: 1.35rem; font-weight: 800; text-transform: uppercase; }' +
    '.kop p { margin: 2px 0; font-size: 0.85rem; font-style: italic; }' +
    '.judul { text-align: center; margin: 24px 0 20px; } .judul h5 { text-decoration: underline; font-size: 1.15rem; text-transform: uppercase; margin: 0; }' +
    '.isi { text-align: justify; white-space: pre-line; font-size: 1rem; margin-bottom: 35px; }' +
    '.ttd-box { float: right; width: 280px; text-align: center; position: relative; }' +
    '.stempel-img { position: absolute; left: 10px; top: 25px; width: 90px; opacity: 0.85; transform: rotate(-8deg); }' +
    '</style></head><body>' +
    '<div class="kop"><img src="image/logo agam.png" alt="Logo"/><div><h4>PEMERINTAH KABUPATEN AGAM</h4><h4>KECAMATAN TANJUNG RAYA</h4><h3>PEMERINTAH NAGARI KOTO MALINTANG</h3><p>Alamat: Jl. Raya Maninjau - Lubuk Basung, Kode Pos: 26454</p></div></div>' +
    '<div class="judul"><h5>' + jenis + '</h5><div>Nomor: ' + nomor + '</div></div>' +
    '<div class="isi">' + isi + '</div>' +
    '<div class="ttd-box"><div>Koto Malintang, ' + tgl + '</div><div style="font-weight:bold;">WALI NAGARI KOTO MALINTANG</div>' +
    '<div style="height:90px;display:flex;align-items:center;justify-content:center;position:relative;">' +
    '<img src="image/stempel_resmi.svg" class="stempel-img"/>' +
    (s.ttd_wali_path ? '<img src="' + s.ttd_wali_path + '" style="max-height:80px;max-width:180px;object-fit:contain;position:relative;z-index:2;"/>' : '<div style="height:50px;"></div>') +
    '</div>' +
    '<div style="font-weight:bold;text-decoration:underline;">NAZARUDDIN, S.Pd.I</div><small>Datuak Bandaro</small></div></body></html>';

  doc.write(content);
  doc.close();
  printWin.focus();
  setTimeout(() => { try { printWin.print(); } catch(e) {} }, 500);
}

// 9. HELPER MODAL & TOAST
function labelStatus(s) {
  if (s === 'menunggu_admin') return '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Pemeriksaan Admin</span>';
  if (s === 'perlu_revisi_warga') return '<span class="badge bg-warning text-dark border border-warning"><i class="bi bi-arrow-repeat me-1"></i>Perlu Perbaikan Berkas</span>';
  if (s === 'dikembalikan_ke_admin') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Dikembalikan Wali Nagari</span>';
  if (s === 'menunggu_wali_nagari' || s === 'menunggu_walinagari' || s === 'di_meja_wali') return '<span class="badge bg-primary"><i class="bi bi-send me-1"></i>Menunggu Wali Nagari</span>';
  if (s === 'disetujui') return '<span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Disetujui</span>';
  if (s === 'ditolak') return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
  return '<span class="badge bg-secondary">Arsip</span>';
}

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

function toast(msg, type = 'success') {
  const t = document.getElementById('toastAdmin');
  const icon = document.getElementById('toastIcon');
  document.getElementById('toastMsg').textContent = msg;
  t.className = 'toast-admin show' + (type === 'error' ? ' error' : '');
  if (icon) icon.className = type === 'error' ? 'bi bi-exclamation-triangle-fill' : 'bi bi-check-circle-fill';
  setTimeout(() => t.classList.remove('show'), 3500);
}

function doLogout() {
  if (confirm('Keluar dari Panel Admin?')) {
    (window.KotoAuth || KotoAuth).logout();
    window.location.href = 'login.html';
  }
}

document.addEventListener('DOMContentLoaded', () => {
  loadDashboard();
});
