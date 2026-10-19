// =========================================================================
// PANEL EKSEKUTIF WALI NAGARI — LOGIKA FRONTEND & API BINDING
// =========================================================================

let currentWaliProfile = null;
let currentPendingSurat = [];
let currentRiwayatSurat = [];
let activeSigningSurat = null;

// 1. Inisialisasi Halaman
document.addEventListener('DOMContentLoaded', async () => {
    await loadWaliProfile();
    showWaliPage('ttd');
});

// 2. Navigasi Antar Halaman
function showWaliPage(id) {
    document.querySelectorAll('.wali-page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item-wali').forEach(n => n.classList.remove('active'));

    const target = document.getElementById('page-' + id);
    if (target) target.classList.add('active');

    document.querySelectorAll('.nav-item-wali').forEach(item => {
        const onclickAttr = item.getAttribute('onclick') || '';
        if (onclickAttr.includes(id)) {
            item.classList.add('active');
        }
    });

    const titles = {
        ttd: 'Penandatanganan Surat Masuk',
        'pengaturan-ttd': 'Pengaturan Tanda Tangan & Stempel Resmi',
        laporan: 'Laporan Akuntabilitas & Statistik Nagari',
        riwayat: 'Riwayat Surat yang Telah Ditandatangani'
    };
    const titleEl = document.getElementById('waliPageTitle');
    if (titleEl) titleEl.textContent = titles[id] || 'Panel Eksekutif Wali Nagari';

    if (id === 'ttd') loadWaliSurat();
    if (id === 'pengaturan-ttd') renderPengaturanTtdView();
    if (id === 'laporan') loadWaliLaporan();
    if (id === 'riwayat') loadWaliRiwayat();
}

// 3. Ambil Profil & Konfigurasi TTD Wali Nagari
async function loadWaliProfile() {
    try {
        const res = await KotoAuth.getWaliProfile();
        if (res && res.success) {
            currentWaliProfile = res.user;

            // Update Topbar & Sidebar User Info
            const nameEl = document.getElementById('sidebarWaliName');
            const avatarEl = document.getElementById('sidebarWaliAvatar');
            const topAvatarEl = document.getElementById('topWaliAvatar');

            if (nameEl) nameEl.textContent = res.user.name;
            const initial = (res.user.name || 'W').charAt(0).toUpperCase();
            if (avatarEl) avatarEl.textContent = initial;
            if (topAvatarEl) topAvatarEl.textContent = initial;
        }
    } catch (e) {
        console.warn('Gagal memuat profil Wali Nagari:', e.message);
    }
}

// 4. Meja Kerja: Penandatanganan Surat Masuk
async function loadWaliSurat() {
    const tbody = document.getElementById('waliSuratTableBody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil antrean surat masuk dari server...</td></tr>';

    try {
        const res = await KotoAuth.getWaliSuratList('menunggu_wali_nagari');
        currentPendingSurat = res.data || [];

        // Fallback / merge dengan localStorage jika API kosong atau offline
        if (currentPendingSurat.length === 0 && typeof KotoAuth.getSuratMasuk === 'function') {
            const localSurat = KotoAuth.getSuratMasuk().filter(s =>
                s.status === 'menunggu_wali_nagari' || s.status === 'menunggu_walinagari' || s.status === 'di_meja_wali'
            );
            if (localSurat.length > 0) {
                currentPendingSurat = localSurat.map(s => ({
                    id: s.id,
                    nama_pemohon: s.pengirim || s.nama_pemohon || '-',
                    nik_pemohon: s.nik || s.nik_pemohon || '-',
                    nama_jenis_surat: s.jenisSurat || s.nama_jenis_surat || '-',
                    nomor_surat: s.nomorSurat || s.nomor_surat || '-',
                    tanggal_format: s.tanggal ? new Date(s.tanggal).toLocaleDateString('id-ID') : '-',
                    keperluan: s.subjek || s.keperluan || '-',
                    isi_surat: s.isi || s.isi_surat || '',
                    status: 'menunggu_wali_nagari'
                }));
            }
        }

        const count = currentPendingSurat.length;
        const badgeEl = document.getElementById('badgeWaliSurat');
        const bannerEl = document.getElementById('bannerJumlahSurat');
        if (badgeEl) badgeEl.textContent = count;
        if (bannerEl) bannerEl.textContent = count + ' Surat Menunggu';

        if (!tbody) return;

        if (count === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-check2-circle text-success fs-4 d-block mb-1"></i>Tidak ada surat yang menunggu penandatanganan saat ini. Semua permohonan telah selesai diproses.</td></tr>';
            return;
        }

        tbody.innerHTML = currentPendingSurat.map(s => {
            const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
            const nik = s.nik_pemohon || (s.user ? s.user.nik : '-');
            const jenis = s.nama_jenis_surat || s.singkatan_jenis_surat || s.jenis_surat || '-';
            const nomor = s.nomor_surat || ('#KM-00' + s.id);
            const tgl = s.tanggal_format || (s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID') : '-');

            return '<tr>' +
                '<td><strong>' + pemohon + '</strong><div class="small text-muted">NIK: ' + nik + '</div></td>' +
                '<td><strong>' + jenis + '</strong></td>' +
                '<td><span class="font-monospace fw-bold text-primary">' + nomor + '</span></td>' +
                '<td>' + tgl + '</td>' +
                '<td>' + labelStatusWali(s.status) + '</td>' +
                '<td>' +
                    '<button class="btn btn-sm btn-success fw-bold px-3 me-1" onclick="openModalTtd(\'' + s.id + '\')" title="Periksa, Tanda Tangani & Stempel">' +
                        '<i class="bi bi-pen-fill me-1"></i> Periksa & TTD' +
                    '</button>' +
                    '<button class="btn btn-sm btn-outline-warning text-dark me-1" onclick="openKembalikanModalWali(\'' + s.id + '\')" title="Kembalikan ke Admin">' +
                        '<i class="bi bi-arrow-counterclockwise"></i>' +
                    '</button>' +
                    '<button class="btn btn-sm btn-outline-danger" onclick="openTolakModalWali(\'' + s.id + '\')" title="Tolak Surat">' +
                        '<i class="bi bi-x-circle"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>';
        }).join('');
    } catch (e) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data dari server backend: ' + e.message + '</td></tr>';
    }
}

// 5. Modal Persetujuan & Penandatanganan Surat
async function openModalTtd(id) {
    let s = currentPendingSurat.find(x => String(x.id) === String(id));
    try {
        if (typeof (window.KotoAuth || KotoAuth).getWaliSuratDetail === 'function') {
            const res = await (window.KotoAuth || KotoAuth).getWaliSuratDetail(id);
            if (res && res.success && res.data) {
                s = res.data;
            }
        }
    } catch (e) {
        console.warn('Gagal memuat detail surat terbaru:', e.message);
    }

    if (!s) return;
    activeSigningSurat = s;

    // Refresh profile to ensure latest TTD
    await loadWaliProfile();

    const bodyEl = document.getElementById('modalTtdWaliBody');
    const footEl = document.getElementById('modalTtdWaliFoot');

    const pemohon = s.nama_pemohon || (s.user ? s.user.name : '-');
    const nik = s.nik_pemohon || (s.user ? s.user.nik : '-');
    const jenis = s.nama_jenis_surat || s.singkatan_jenis_surat || s.jenis_surat || '-';
    const nomor = s.nomor_surat || ('#KM-00' + s.id);
    const subjek = s.keperluan || '-';
    const isiNaskah = s.isi_surat || 'Naskah surat dinas resmi Nagari Koto Malintang.';

    const namaWali = currentWaliProfile?.name || 'NAZARUDDIN, S.Pd.I';
    const gelarWali = currentWaliProfile?.gelar || 'Datuak Bandaro';
    const ttdUrl = currentWaliProfile?.ttd_url;
    const hasTtd = !!ttdUrl;

    const isMenungguWali = s.status === 'menunggu_wali_nagari' || s.status === 'menunggu_walinagari' || s.status === 'di_meja_wali';
    const isDikembalikan = s.status === 'dikembalikan_ke_admin';
    const isPerluRevisi = s.status === 'perlu_revisi_warga';
    const isDisetujui = s.status === 'disetujui';
    const isDitolak = s.status === 'ditolak';

    let alertCatatanHtml = '';
    if (isDikembalikan && s.catatan_wali) {
        alertCatatanHtml = '<div class="alert alert-danger p-3 mb-3 border-danger shadow-sm">' +
            '<div class="d-flex align-items-center mb-1">' +
                '<i class="bi bi-arrow-counterclockwise text-danger me-2 fs-5"></i>' +
                '<strong class="text-danger">Catatan Pengembalian ke Admin:</strong>' +
            '</div>' +
            '<div class="text-dark ms-4 fw-medium">' + s.catatan_wali + '</div>' +
            '<div class="small text-muted ms-4 mt-2"><i class="bi bi-info-circle me-1"></i> Surat sedang berada di meja Admin untuk perbaikan internal/berkas.</div>' +
        '</div>';
    } else if (isPerluRevisi && s.catatan_admin) {
        alertCatatanHtml = '<div class="alert alert-warning p-3 mb-3 border-warning shadow-sm">' +
            '<div class="d-flex align-items-center mb-1">' +
                '<i class="bi bi-arrow-repeat text-dark me-2 fs-5"></i>' +
                '<strong class="text-dark">Catatan Permintaan Perbaikan Berkas:</strong>' +
            '</div>' +
            '<div class="text-dark ms-4">' + s.catatan_admin + '</div>' +
            '<div class="small text-muted ms-4 mt-2"><i class="bi bi-info-circle me-1"></i> Surat sedang menunggu tindakan perbaikan oleh warga.</div>' +
        '</div>';
    } else if (isDitolak && s.alasan_penolakan) {
        alertCatatanHtml = '<div class="alert alert-danger p-3 mb-3 border-danger shadow-sm">' +
            '<div class="d-flex align-items-center mb-1">' +
                '<i class="bi bi-x-circle-fill text-danger me-2 fs-5"></i>' +
                '<strong class="text-danger">Alasan Penolakan:</strong>' +
            '</div>' +
            '<div class="text-dark ms-4">' + s.alasan_penolakan + '</div>' +
        '</div>';
    }

    let ttdPreviewHtml = '';
    if (isMenungguWali) {
        if (hasTtd) {
            ttdPreviewHtml = '<div class="p-3 rounded text-center" style="background:#f0fdf4;border:2px dashed #22c55e;">' +
                '<div class="fw-bold text-success mb-2"><i class="bi bi-patch-check-fill me-1"></i> Pratinjau Tanda Tangan & Stempel Resmi Wali Nagari</div>' +
                '<div class="d-flex justify-content-center align-items-center gap-3 my-2">' +
                    '<img src="image/stempel_resmi.svg" alt="Stempel Resmi" style="width:75px;height:75px;opacity:0.9;transform:rotate(-6deg);filter:drop-shadow(0 2px 4px rgba(0,0,0,0.15));" />' +
                    '<img src="' + ttdUrl + '" alt="Tanda Tangan Wali Nagari" style="max-height:75px;max-width:180px;object-fit:contain;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.15));" />' +
                '</div>' +
                '<div class="fw-bold text-dark mt-1" style="text-decoration:underline;">' + namaWali + '</div>' +
                '<small class="text-muted d-block">' + gelarWali + ' - Wali Nagari Koto Malintang</small>' +
            '</div>';
        } else {
            ttdPreviewHtml = '<div class="p-4 rounded text-center bg-light border border-warning">' +
                '<i class="bi bi-exclamation-triangle-fill text-warning fs-2 d-block mb-2"></i>' +
                '<h6 class="fw-bold text-dark">Tanda Tangan Wali Nagari Belum Tersedia</h6>' +
                '<p class="small text-muted mb-3">Anda belum mengunggah berkas tanda tangan resmi. Silakan unggah tanda tangan terlebih dahulu agar dapat menandatangani surat.</p>' +
                '<button class="btn btn-warning btn-sm fw-bold px-3 rounded-pill" onclick="closeModal(\'modalTtdWali\'); showWaliPage(\'pengaturan-ttd\');">' +
                    '<i class="bi bi-upload me-1"></i> Buka Pengaturan Tanda Tangan' +
                '</button>' +
            '</div>';
        }
    }

    let tanggunganHtml = '';
    if (s.data_spesifik && Array.isArray(s.data_spesifik.tanggungan_keluarga) && s.data_spesifik.tanggungan_keluarga.length > 0) {
        tanggunganHtml = '<div class="mb-3 p-3 rounded" style="background:#f8fafc;border:1px solid #cbd5e1;">' +
            '<h6 class="fw-bold text-dark mb-2" style="font-size:0.85rem;"><i class="bi bi-people-fill text-success me-1"></i>Daftar Tanggungan Keluarga (' + s.data_spesifik.tanggungan_keluarga.length + ' Jiwa):</h6>' +
            '<div class="table-responsive">' +
                '<table class="table table-sm table-bordered bg-white small mb-0" style="font-size:0.75rem;">' +
                    '<thead class="table-light"><tr><th>No</th><th>Nama Lengkap</th><th>Jenis Kelamin</th><th>Umur</th><th>Hubungan</th></tr></thead>' +
                    '<tbody>' +
                        s.data_spesifik.tanggungan_keluarga.map((t, idx) =>
                            '<tr><td class="text-center">' + (idx + 1) + '</td><td><strong>' + (t.nama || '-') + '</strong></td><td>' + (t.jenis_kelamin || '-') + '</td><td>' + (t.umur || '-') + '</td><td>' + (t.hubungan || '-') + '</td></tr>'
                        ).join('') +
                    '</tbody>' +
                '</table>' +
            '</div>' +
        '</div>';
    }

    if (bodyEl) {
        bodyEl.innerHTML = alertCatatanHtml +
            '<div class="p-3 mb-3 rounded" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:0.85rem;">' +
                '<div class="row g-2">' +
                    '<div class="col-6"><strong>Pemohon:</strong> ' + pemohon + '</div>' +
                    '<div class="col-6"><strong>NIK:</strong> <code>' + nik + '</code></div>' +
                    '<div class="col-6"><strong>Jenis Surat:</strong> ' + jenis + '</div>' +
                    '<div class="col-6"><strong>Status Saat Ini:</strong> ' + labelStatusWali(s.status) + '</div>' +
                    '<div class="col-6"><strong>Nomor Surat Resmi:</strong> <span class="badge bg-primary font-monospace">' + nomor + '</span></div>' +
                    '<div class="col-12"><strong>Keperluan:</strong> ' + subjek + '</div>' +
                '</div>' +
            '</div>' +
            tanggunganHtml +
            '<div class="mb-3">' +
                '<label class="form-label small fw-bold">Naskah Surat Resmi:</label>' +
                '<div class="p-3 bg-light rounded border font-monospace" style="font-size:0.85rem;white-space:pre-line;max-height:160px;overflow-y:auto;">' + isiNaskah + '</div>' +
            '</div>' +
            ttdPreviewHtml;
    }

    if (footEl) {
        if (isMenungguWali) {
            let actionBtns = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<button class="btn btn-sm btn-outline-danger" onclick="closeModal(\'modalTtdWali\'); openTolakModalWali(\'' + s.id + '\');">' +
                    '<i class="bi bi-x-circle me-1"></i> Tolak Surat' +
                '</button>' +
                '<button class="btn btn-sm btn-warning text-dark fw-bold" onclick="closeModal(\'modalTtdWali\'); openKembalikanModalWali(\'' + s.id + '\');">' +
                    '<i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan ke Admin' +
                '</button>';

            if (hasTtd) {
                actionBtns += '<button class="btn btn-sm btn-success fw-bold px-4" id="btnEksekusiTtd" onclick="eksekusiTtdWali(\'' + s.id + '\')">' +
                    '<i class="bi bi-patch-check-fill me-1"></i> Setujui, TTD & Bubuhkan Stempel' +
                '</button>';
            }
            footEl.innerHTML = actionBtns;
        } else if (isDikembalikan) {
            footEl.innerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger align-self-center py-2 px-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Surat Telah Dikembalikan ke Admin</span>';
        } else if (isDisetujui) {
            footEl.innerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<button class="btn btn-sm btn-primary" onclick="cetakSuratWali(\'' + s.id + '\')"><i class="bi bi-printer-fill me-1"></i> Cetak Dokumen PDF</button>';
        } else if (isDitolak) {
            footEl.innerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<span class="badge bg-danger align-self-center py-2 px-3"><i class="bi bi-x-circle me-1"></i>Permohonan Telah Ditolak</span>';
        } else if (isPerluRevisi) {
            footEl.innerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<span class="badge bg-warning text-dark align-self-center py-2 px-3"><i class="bi bi-hourglass-split me-1"></i>Menunggu Perbaikan Berkas oleh Warga</span>';
        } else {
            footEl.innerHTML = '<button class="btn btn-sm btn-outline-secondary" onclick="closeModal(\'modalTtdWali\')">Tutup</button>' +
                '<span class="badge bg-warning text-dark align-self-center py-2 px-3"><i class="bi bi-clock me-1"></i>Menunggu Pemeriksaan Admin</span>';
        }
    }

    openModal('modalTtdWali');
}

// 6. Eksekusi TTD & Stempel ke Backend Laravel
async function eksekusiTtdWali(id) {
    const s = currentPendingSurat.find(x => String(x.id) === String(id));
    if (s && s.status !== 'menunggu_wali_nagari' && s.status !== 'menunggu_walinagari' && s.status !== 'di_meja_wali') {
        toastWali('Surat tidak dalam status menunggu penandatanganan Wali Nagari.', 'error');
        return;
    }

    const btn = document.getElementById('btnEksekusiTtd');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menandatangani & membubuhkan stempel...';
    }

    try {
        let msg = 'Surat berhasil ditandatangani & dibubuhi stempel resmi!';
        const res = await (window.KotoAuth || KotoAuth).approveSuratWali(id, 'Disetujui dan dibubuhi Tanda Tangan & Stempel Digital Resmi Wali Nagari.');
        msg = (res && res.message) || msg;

        // Sinkronkan status di localStorage jika ada
        if (typeof (window.KotoAuth || KotoAuth).getSuratMasuk === 'function') {
            const list = (window.KotoAuth || KotoAuth).getSuratMasuk();
            const localS = list.find(x => String(x.id) === String(id));
            if (localS) {
                localS.status = 'disetujui';
                localS.stempel = true;
                localS.ttdWali = true;
                localS.disetujuiPada = new Date().toISOString();
                (window.KotoAuth || KotoAuth).saveSuratMasuk(list);
            }
        }

        closeModal('modalTtdWali');
        toastWali(msg);
        await loadWaliSurat();
        await loadWaliLaporan();
    } catch (err) {
        let errMsg = err.message || 'Gagal menandatangani surat.';
        if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
            errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
        } else if (errMsg.includes('403')) {
            errMsg = 'Anda tidak memiliki akses untuk melakukan tindakan ini.';
        } else if (errMsg.includes('422')) {
            errMsg = 'Status surat sudah berubah atau data tidak valid.';
        }
        toastWali(errMsg, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> Setujui, TTD & Bubuhkan Stempel';
        }
    }
}

// 6a. Kembalikan Surat ke Admin
function openKembalikanModalWali(id) {
    const s = currentPendingSurat.find(x => String(x.id) === String(id));
    if (s && s.status !== 'menunggu_wali_nagari' && s.status !== 'menunggu_walinagari' && s.status !== 'di_meja_wali') {
        toastWali('Surat tidak dalam status menunggu Wali Nagari.', 'error');
        return;
    }
    document.getElementById('kembalikanSuratId').value = id;
    document.getElementById('kembalikanCatatanWali').value = '';
    openModal('modalKembalikanWali');
}

async function submitKembalikanWali() {
    const id = document.getElementById('kembalikanSuratId').value;
    const catatan = document.getElementById('kembalikanCatatanWali').value.trim();

    if (!catatan) {
        toastWali('Catatan perbaikan wajib diisi.', 'error');
        return;
    }
    if (catatan.length < 5) {
        toastWali('Catatan perbaikan minimal 5 karakter.', 'error');
        return;
    }
    if (catatan.length > 1000) {
        toastWali('Catatan perbaikan maksimal 1000 karakter.', 'error');
        return;
    }

    const btn = document.getElementById('btnSubmitKembalikanWali');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengembalikan...';
    }

    try {
        const res = await (window.KotoAuth || KotoAuth).kembalikanSuratWali(id, catatan);
        closeModal('modalKembalikanWali');
        toastWali(res.message || 'Surat berhasil dikembalikan kepada Admin.');
        await loadWaliSurat();
        await loadWaliLaporan();
    } catch (e) {
        let errMsg = e.message || 'Gagal mengembalikan surat ke Admin.';
        if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
            errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
        } else if (errMsg.includes('403')) {
            errMsg = 'Anda tidak memiliki akses untuk melakukan tindakan ini.';
        } else if (errMsg.includes('422')) {
            errMsg = 'Catatan belum lengkap atau data tidak valid. Silakan periksa kembali.';
        }
        toastWali(errMsg, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i> Kembalikan ke Admin';
        }
    }
}

// 6b. Tolak Permohonan Surat oleh Wali Nagari
function openTolakModalWali(id) {
    const s = currentPendingSurat.find(x => String(x.id) === String(id));
    if (s && s.status !== 'menunggu_wali_nagari' && s.status !== 'menunggu_walinagari' && s.status !== 'di_meja_wali') {
        toastWali('Surat tidak dalam status menunggu Wali Nagari.', 'error');
        return;
    }
    document.getElementById('tolakSuratIdWali').value = id;
    document.getElementById('tolakAlasanWali').value = '';
    openModal('modalTolakWali');
}

async function submitTolakWali() {
    const id = document.getElementById('tolakSuratIdWali').value;
    const alasan = document.getElementById('tolakAlasanWali').value.trim();

    if (!alasan) {
        toastWali('Alasan penolakan wajib diisi untuk pemohon.', 'error');
        return;
    }
    if (alasan.length < 5) {
        toastWali('Alasan penolakan minimal 5 karakter.', 'error');
        return;
    }

    const btn = document.getElementById('btnSubmitTolakWali');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menolak...';
    }

    try {
        const res = await (window.KotoAuth || KotoAuth).rejectSuratWali(id, alasan);
        closeModal('modalTolakWali');
        toastWali(res.message || 'Surat telah ditolak dan alasan terkirim ke warga.');
        await loadWaliSurat();
        await loadWaliLaporan();
    } catch (e) {
        let errMsg = e.message || 'Gagal menolak surat.';
        if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
            errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
        } else if (errMsg.includes('403')) {
            errMsg = 'Anda tidak memiliki akses untuk melakukan tindakan ini.';
        } else if (errMsg.includes('422')) {
            errMsg = 'Data tidak valid. Silakan periksa kembali.';
        }
        toastWali(errMsg, 'error');
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i> Tolak Surat';
        }
    }
}

// 7. Pengaturan Tanda Tangan & Stempel Resmi
function renderPengaturanTtdView() {
    const nama = currentWaliProfile?.name || 'Nazaruddin, S.Pd.I';
    const gelar = currentWaliProfile?.gelar || 'Datuak Bandaro';
    const jabatan = currentWaliProfile?.jabatan || 'Wali Nagari Koto Malintang';
    const ttdUrl = currentWaliProfile?.ttd_url;
    const hasTtd = !!ttdUrl;

    const previewContainer = document.getElementById('ttdCurrentDisplay');
    if (previewContainer) {
        if (hasTtd) {
            previewContainer.innerHTML = '<div class="p-3 bg-white rounded-3 border text-center">' +
                '<span class="badge bg-success mb-2 px-3 py-1"><i class="bi bi-check2-circle me-1"></i> Tanda Tangan Aktif</span>' +
                '<div class="my-2">' +
                    '<img src="' + ttdUrl + '" alt="Tanda Tangan Wali Nagari" style="max-height:120px;max-width:250px;object-fit:contain;border:1px dashed #cbd5e1;padding:8px;border-radius:8px;background:#fff;" />' +
                '</div>' +
                '<small class="text-muted d-block">Tanda tangan ini akan otomatis digunakan saat menyetujui surat dinas.</small>' +
                '<button type="button" class="btn btn-outline-danger btn-sm mt-3" onclick="hapusTtdWali()">' +
                    '<i class="bi bi-trash me-1"></i> Hapus Tanda Tangan' +
                '</button>' +
            '</div>';
        } else {
            previewContainer.innerHTML = '<div class="p-4 bg-light rounded-3 border text-center text-muted">' +
                '<i class="bi bi-card-image fs-1 d-block mb-2 text-secondary"></i>' +
                '<h6>Belum ada tanda tangan yang tersimpan</h6>' +
                '<small>Silakan pilih file gambar tanda tangan di bawah ini untuk mengunggah tanda tangan baru.</small>' +
            '</div>';
        }
    }

    // Set input values
    const inNama = document.getElementById('ttdInNama');
    const inGelar = document.getElementById('ttdInGelar');
    const inJabatan = document.getElementById('ttdInJabatan');
    if (inNama) inNama.value = nama;
    if (inGelar) inGelar.value = gelar;
    if (inJabatan) inJabatan.value = jabatan;
}

function handleTtdFileSelect(e) {
    const file = e.target.files[0];
    const previewBox = document.getElementById('ttdLivePreview');
    const previewImg = document.getElementById('ttdLivePreviewImg');
    const alertBox = document.getElementById('ttdUploadAlert');

    if (alertBox) alertBox.classList.add('d-none');

    if (!file) {
        if (previewBox) previewBox.classList.add('d-none');
        return;
    }

    // Validate image format
    const validTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        if (alertBox) {
            alertBox.className = 'alert alert-danger small';
            alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Format file harus berupa gambar (PNG, JPG, atau WEBP). Disarankan PNG transparan.';
            alertBox.classList.remove('d-none');
        }
        e.target.value = '';
        if (previewBox) previewBox.classList.add('d-none');
        return;
    }

    // Validate size (max 3MB)
    if (file.size > 3 * 1024 * 1024) {
        if (alertBox) {
            alertBox.className = 'alert alert-danger small';
            alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Ukuran file terlalu besar (Maksimal 3 MB).';
            alertBox.classList.remove('d-none');
        }
        e.target.value = '';
        if (previewBox) previewBox.classList.add('d-none');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(evt) {
        if (previewImg) previewImg.src = evt.target.result;
        if (previewBox) previewBox.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

async function submitPengaturanTtd(e) {
    e.preventDefault();
    const fileIn = document.getElementById('ttdFileInput');
    const btn = document.getElementById('btnSimpanTtd');
    const alertBox = document.getElementById('ttdUploadAlert');

    if (!fileIn.files || !fileIn.files[0]) {
        if (!currentWaliProfile?.ttd_url) {
            if (alertBox) {
                alertBox.className = 'alert alert-warning small';
                alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Pilih file gambar tanda tangan terlebih dahulu.';
                alertBox.classList.remove('d-none');
            }
            return;
        }
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan tanda tangan...';
    }

    const formData = new FormData();
    if (fileIn.files && fileIn.files[0]) {
        formData.append('tanda_tangan', fileIn.files[0]);
    }

    try {
        const res = await KotoAuth.uploadWaliTtd(formData);
        if (alertBox) {
            alertBox.className = 'alert alert-success small';
            alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + (res.message || 'Tanda tangan berhasil disimpan!');
            alertBox.classList.remove('d-none');
        }
        fileIn.value = '';
        const previewBox = document.getElementById('ttdLivePreview');
        if (previewBox) previewBox.classList.add('d-none');

        await loadWaliProfile();
        renderPengaturanTtdView();
        toastWali('Tanda Tangan Wali Nagari Berhasil Diperbarui!');
    } catch (err) {
        if (alertBox) {
            alertBox.className = 'alert alert-danger small';
            alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> ' + (err.message || 'Gagal menyimpan tanda tangan.');
            alertBox.classList.remove('d-none');
        }
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Simpan Tanda Tangan';
        }
    }
}

async function hapusTtdWali() {
    if (!confirm('Apakah Anda yakin ingin menghapus tanda tangan yang tersimpan?')) return;
    try {
        await KotoAuth.deleteWaliTtd();
        await loadWaliProfile();
        renderPengaturanTtdView();
        toastWali('Tanda tangan berhasil dihapus.');
    } catch (e) {
        alert('Gagal menghapus: ' + e.message);
    }
}

// 8. Laporan & Rekapitulasi
async function loadWaliLaporan() {
    try {
        const res = await KotoAuth.getWaliDashboard();
        if (res && res.success) {
            const disetujuiEl = document.getElementById('waliStatDisetujui');
            const menungguEl = document.getElementById('waliStatMenunggu');
            if (disetujuiEl) disetujuiEl.textContent = res.stats.disetujui || 0;
            if (menungguEl) menungguEl.textContent = res.stats.antrean || 0;
        }
    } catch (e) {
        console.warn('Laporan stat warning:', e.message);
    }
}

// 9. Riwayat Surat yang Telah Ditandatangani
async function loadWaliRiwayat() {
    const tbody = document.getElementById('waliRiwayatTableBody');
    if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil riwayat surat dari server...</td></tr>';

    try {
        const res = await KotoAuth.getWaliSuratList('disetujui');
        currentRiwayatSurat = res.data || [];

        if (!tbody) return;

        if (currentRiwayatSurat.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat surat yang disetujui.</td></tr>';
            return;
        }

        tbody.innerHTML = currentRiwayatSurat.map(s => {
            const nomor = s.nomor_surat || ('#KM-00' + s.id);
            const pemohon = s.nama_pemohon || '-';
            const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
            const tgl = s.tanggal_format || (s.created_at ? new Date(s.created_at).toLocaleDateString('id-ID') : '-');

            return '<tr>' +
                '<td><span class="font-monospace fw-bold text-primary">' + nomor + '</span></td>' +
                '<td><strong>' + pemohon + '</strong></td>' +
                '<td>' + jenis + '</td>' +
                '<td>' + tgl + '</td>' +
                '<td><span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Sudah Ditandatangani</span></td>' +
                '<td><button class="btn btn-sm btn-outline-primary" onclick="cetakSuratWali(\'' + s.id + '\')"><i class="bi bi-printer-fill me-1"></i> Cetak Dokumen</button></td>' +
            '</tr>';
        }).join('');
    } catch (e) {
        if (tbody) tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat riwayat surat.</td></tr>';
    }
}

// 10. Cetak Surat dengan Gambar TTD Asli
function cetakSuratWali(id) {
    const s = currentRiwayatSurat.find(x => String(x.id) === String(id)) || currentPendingSurat.find(x => String(x.id) === String(id));
    if (!s) return;

    const printWin = window.open('', '_blank');
    const jenisTitle = (s.nama_jenis_surat || s.jenis_surat || 'SURAT KETERANGAN RESMI').toUpperCase();
    const nomor = s.nomor_surat || ('470 / 0' + s.id + ' / WN-KM / VIII / 2026');
    const isi = s.isi_surat || '';
    const tglText = s.tanggal_format || new Date().toLocaleDateString('id-ID');
    const namaWali = currentWaliProfile?.name || 'NAZARUDDIN, S.Pd.I';
    const gelarWali = currentWaliProfile?.gelar || 'Datuak Bandaro';
    const ttdUrl = currentWaliProfile?.ttd_url;

    let ttdImgHtml = '';
    if (ttdUrl) {
        ttdImgHtml = '<img src="' + ttdUrl + '" style="max-height:80px;max-width:180px;object-fit:contain;position:relative;z-index:2;" />';
    } else {
        ttdImgHtml = '<div style="height:60px;"></div>';
    }

    const htmlParts = [
        '<!DOCTYPE html><html><head><title>Surat Resmi - ' + jenisTitle + '</title>',
        '<style>',
        'body { font-family: "Times New Roman", serif; padding: 40px 60px; color: #000; line-height: 1.8; }',
        '.kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 24px; display: flex; align-items: center; justify-content: center; gap: 20px; }',
        '.kop img { width: 70px; } .kop h4 { margin: 2px 0; font-size: 1.15rem; text-transform: uppercase; }',
        '.kop h3 { margin: 2px 0; font-size: 1.35rem; font-weight: 800; text-transform: uppercase; }',
        '.kop p { margin: 2px 0; font-size: 0.85rem; font-style: italic; }',
        '.judul { text-align: center; margin: 24px 0 20px; } .judul h5 { text-decoration: underline; font-size: 1.15rem; text-transform: uppercase; margin: 0; }',
        '.isi { text-align: justify; white-space: pre-line; font-size: 1rem; margin-bottom: 35px; }',
        '.ttd-box { float: right; width: 280px; text-align: center; position: relative; }',
        '.stempel-img { position: absolute; left: 10px; top: 25px; width: 90px; opacity: 0.85; transform: rotate(-8deg); z-index:1; }',
        '</style></head><body>',
        '<div class="kop"><img src="image/logo agam.png" alt="Logo"/><div><h4>PEMERINTAH KABUPATEN AGAM</h4><h4>KECAMATAN TANJUNG RAYA</h4><h3>PEMERINTAH NAGARI KOTO MALINTANG</h3><p>Alamat: Jl. Raya Maninjau - Lubuk Basung, Kode Pos: 26454</p></div></div>',
        '<div class="judul"><h5>' + jenisTitle + '</h5><div>Nomor: ' + nomor + '</div></div>',
        '<div class="isi">' + isi + '</div>',
        '<div class="ttd-box"><div>Koto Malintang, ' + tglText + '</div><div style="font-weight:bold;">WALI NAGARI KOTO MALINTANG</div>',
        '<div style="height:90px;display:flex;align-items:center;justify-content:center;position:relative;">',
        '<img src="image/stempel_resmi.svg" class="stempel-img"/>',
        ttdImgHtml,
        '</div>',
        '<div style="font-weight:bold;text-decoration:underline;">' + namaWali + '</div><small>' + gelarWali + '</small></div></body></html>'
    ];

    printWin.document.open();
    printWin.document.write(htmlParts.join('\n'));
    printWin.document.close();
    printWin.focus();
    setTimeout(() => { try { printWin.print(); } catch(e) {} }, 500);
}

// 11. Modal, Status & Toast Helper
function labelStatusWali(s) {
    if (s === 'menunggu_admin') return '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Pemeriksaan Admin</span>';
    if (s === 'perlu_revisi_warga') return '<span class="badge bg-warning text-dark border border-warning"><i class="bi bi-arrow-repeat me-1"></i>Perlu Perbaikan Berkas</span>';
    if (s === 'dikembalikan_ke_admin') return '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Dikembalikan Wali Nagari</span>';
    if (s === 'menunggu_wali_nagari' || s === 'menunggu_walinagari' || s === 'di_meja_wali') return '<span class="badge bg-primary"><i class="bi bi-send me-1"></i>Menunggu Wali Nagari</span>';
    if (s === 'disetujui') return '<span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Disetujui</span>';
    if (s === 'ditolak') return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
    return '<span class="badge bg-secondary">Arsip</span>';
}

function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('open');
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('open');
}
function toastWali(msg, type = 'success') {
    const t = document.getElementById('toastWali');
    const msgEl = document.getElementById('toastWaliMsg');
    const icon = t ? t.querySelector('i') : null;
    if (msgEl) msgEl.textContent = msg;
    if (t) {
        t.style.background = type === 'error' ? '#ef4444' : '#064e3b';
        if (icon) {
            icon.className = type === 'error' ? 'bi bi-exclamation-triangle-fill' : 'bi bi-check-circle-fill';
        }
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }
}
function doLogout() {
    if (confirm('Keluar dari Panel Wali Nagari?')) {
        KotoAuth.logout();
        window.location.href = 'login.html';
    }
}