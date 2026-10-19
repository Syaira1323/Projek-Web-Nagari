// =========================================================================
// STATE & KONFIGURASI SURAT (7 JENIS SURAT NAGARI KOTO MALINTANG)
// =========================================================================
const SURAT_CONF = {
    'suket_usaha': {
        nama: 'Surat Keterangan Usaha (SKU)',
        extra1: 'Nama Usaha', extra2: 'Bidang Usaha',
        hExtra1: 'Contoh: Bengkel Bubut Maju Jaya', hExtra2: 'Contoh: Jasa Perbengkelan',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Foto Tempat / Aktivitas Usaha', wajib: true, folder: 'uploads/foto_usaha', key: 'foto_usaha' }
        ]
    },
    'suket_domisili': {
        nama: 'Surat Keterangan Domisili (SKD)',
        extra1: 'Lama Domisili', extra2: 'Status Tempat Tinggal',
        hExtra1: 'Contoh: Sejak Tahun 2018', hExtra2: 'Contoh: Rumah Sendiri / Sewa',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Surat Pengantar Kepala Jorong', wajib: true, folder: 'uploads/surat_pengantar', key: 'surat_pengantar' }
        ]
    },
    'suket_tidak_mampu': {
        nama: 'Surat Keterangan Tidak Mampu (SKTM)',
        extra1: 'Estimasi Penghasilan Keluarga', extra2: 'Kondisi Ekonomi',
        hExtra1: 'Contoh: Rp 800.000 / Bulan', hExtra2: 'Contoh: Buruh Harian / Pra-Sejahtera',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Foto Kondisi Rumah / Tempat Tinggal', wajib: true, folder: 'uploads/foto_rumah', key: 'foto_rumah' },
            { label: 'Surat Keterangan Kurang Mampu dari Kepala Jorong', wajib: true, folder: 'uploads/surat_pengantar', key: 'surat_pengantar' }
        ]
    },
    'surat_nikah_na': {
        nama: 'Surat Pengantar Nikah (Model NA)',
        extra1: 'Nama Calon Pasangan', extra2: 'Bin / Binti Pasangan',
        hExtra1: 'Contoh: Siti Rahmawati', hExtra2: 'Contoh: H. Abdullah',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Foto / Scan KTP Calon Pasangan', wajib: true, folder: 'uploads/ktp_pasangan', key: 'ktp_pasangan' },
            { label: 'Surat Pengantar Nikah dari Kepala Jorong', wajib: true, folder: 'uploads/surat_pengantar', key: 'surat_pengantar' }
        ]
    },
    'sket_penghasilan': {
        nama: 'Surat Keterangan Penghasilan (SKP)',
        extra1: 'Rata-rata Penghasilan (Rp)', extra2: 'Sumber Penghasilan',
        hExtra1: 'Contoh: 1500000', hExtra2: 'Contoh: Petani / Pekebun',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Surat Pernyataan / Slip Gaji', wajib: true, folder: 'uploads/slip_penghasilan', key: 'slip_penghasilan' }
        ]
    },
    'suket_meninggal': {
        nama: 'Surat Keterangan Meninggal (SKM)',
        extra1: 'Nama Almarhum / Almarhumah', extra2: 'Tanggal & Tempat Wafat',
        hExtra1: 'Contoh: H. Rusli Dt. Maruhum', hExtra2: 'Contoh: 14-08-2026 di Rumah Duka',
        syarat: [
            { label: 'Foto / Scan KTP Pelapor (Ahli Waris)', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan KK Almarhum / Almarhumah', wajib: true, folder: 'uploads/kk', key: 'kk' },
            { label: 'Surat Kematian RS/Bidan/Jorong', wajib: true, folder: 'uploads/surat_kematian_rs', key: 'surat_kematian_rs' }
        ]
    },
    'suket_belum_menikah': {
        nama: 'Surat Keterangan Belum Menikah (SKBM)',
        extra1: 'Status Perkawinan', extra2: 'Keperluan Khusus',
        hExtra1: 'Belum Pernah Menikah (Lajang)', hExtra2: 'Pendaftaran CPNS / TNI-Polri',
        syarat: [
            { label: 'Foto / Scan KTP Pemohon', wajib: true, folder: 'uploads/ktp', key: 'ktp' },
            { label: 'Foto / Scan Kartu Keluarga (KK)', wajib: true, folder: 'uploads/kk', key: 'kk' }
        ]
    }
};

let currentUser = null;
let currentSuratList = [];
let modeRevisi = false;
let revisiSuratId = null;
let currentRevisiBerkas = [];

// =========================================================================
// NAVIGASI TAB
// =========================================================================
function switchTab(tabId) {
    document.querySelectorAll('.warga-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.nav-link-warga').forEach(el => el.classList.remove('active'));

    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.add('active');

    document.querySelectorAll('.nav-link-warga').forEach(link => {
        const onclickAttr = link.getAttribute('onclick') || '';
        if (onclickAttr.includes(tabId)) {
            link.classList.add('active');
        }
    });

    // Trigger load data sesuai tab
    if (tabId === 'dashboard') loadDashboardData();
    if (tabId === 'status') loadStatusSurat();
    if (tabId === 'notifikasi') loadFullNotifikasi();
    if (tabId === 'umkm') loadMyUmkm();
    if (tabId === 'profil') loadProfilForm();

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// =========================================================================
// 1. DASHBOARD LOAD DATA
// =========================================================================
async function loadDashboardData() {
    try {
        const res = await KotoAuth.getDashboardWarga();
        if (res && res.success) {
            currentUser = res.user;
            const firstName = (res.user.name || 'Warga').split(' ')[0];
            const navUserEl = document.getElementById('navUserName');
            const welcomeUserEl = document.getElementById('welcomeUserName');
            if (navUserEl) navUserEl.innerText = firstName;
            if (welcomeUserEl) welcomeUserEl.innerText = res.user.name || 'Warga';

            const statMenungguEl = document.getElementById('statMenungguCount');
            const statNotifEl = document.getElementById('statNotifCount');
            const statUmkmEl = document.getElementById('statUmkmCount');
            const navNotifEl = document.getElementById('navNotifBadge');

            if (statMenungguEl) statMenungguEl.innerText = res.stats.menunggu || 0;
            if (statNotifEl) statNotifEl.innerText = res.stats.unread_notif || 0;
            if (statUmkmEl) statUmkmEl.innerText = res.stats.total_umkm || 0;
            if (navNotifEl) navNotifEl.innerText = res.stats.unread_notif || 0;

            renderDashSuratTable(res.surat_terbaru || []);
        }
    } catch (e) {
        console.warn('Load dashboard warning:', e.message);
    }
}

function renderDashSuratTable(list) {
    const tbody = document.getElementById('dashSuratTbody');
    if (!tbody) return;

    if (!list || list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted small">Belum ada pengajuan surat. Klik <strong>Ajukan Surat Baru</strong> untuk memulai permohonan.</td></tr>';
        return;
    }

    tbody.innerHTML = list.map(s => {
        const nomor = s.nomor_surat || ('#KM-00' + s.id);
        const jenis = s.nama_jenis_surat || s.jenis_surat || '-';
        const keperluan = s.keperluan || '-';
        const tgl = s.tanggal_format || '-';
        const color = s.status_badge_color || 'secondary';
        const status = s.status_label || s.status || '-';

        return '<tr>' +
            '<td><strong class="font-monospace text-primary">' + nomor + '</strong></td>' +
            '<td><strong>' + jenis + '</strong></td>' +
            '<td class="small text-muted text-truncate" style="max-width:200px;">' + keperluan + '</td>' +
            '<td class="small">' + tgl + '</td>' +
            '<td><span class="badge bg-' + color + '">' + status + '</span></td>' +
            '<td><button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="openDetailSuratModal(' + s.id + ')"><i class="bi bi-eye-fill me-1"></i> Detail</button></td>' +
        '</tr>';
    }).join('');
}

// =========================================================================
// 2. FORM PENGAJUAN SURAT & DOKUMEN PERSYARATAN
// =========================================================================
function selectSuratType(key) {
    if (!SURAT_CONF[key]) key = 'suket_usaha';

    document.querySelectorAll('.surat-card').forEach(c => c.classList.remove('selected'));
    const activeCard = document.getElementById('card_' + key);
    if (activeCard) activeCard.classList.add('selected');

    const conf = SURAT_CONF[key];
    const inJenis = document.getElementById('in_jenis_surat');
    const badgeSelected = document.getElementById('formSelectedBadge');
    if (inJenis) inJenis.value = key;
    if (badgeSelected) badgeSelected.innerText = conf.nama;

    // Update Labels Extra Fields
    const l1 = document.getElementById('labelExtra1');
    const l2 = document.getElementById('labelExtra2');
    const i1 = document.getElementById('in_extra1');
    const i2 = document.getElementById('in_extra2');
    if (l1) l1.innerText = conf.extra1;
    if (l2) l2.innerText = conf.extra2;
    if (i1) i1.placeholder = conf.hExtra1;
    if (i2) i2.placeholder = conf.hExtra2;

    // Toggle Box Tanggungan Keluarga khusus SKTM
    const boxTanggungan = document.getElementById('boxTanggunganKeluarga');
    if (boxTanggungan) {
        if (key === 'suket_tidak_mampu') {
            boxTanggungan.classList.remove('d-none');
            const tbody = document.getElementById('tbodyTanggungan');
            if (tbody && tbody.children.length === 0) {
                addTanggunganRow();
            }
        } else {
            boxTanggungan.classList.add('d-none');
        }
    }

    // Render Dynamic Upload Inputs
    if (modeRevisi && currentRevisiBerkas && currentRevisiBerkas.length > 0) {
        renderDokumenRevisi(key, currentRevisiBerkas);
    } else {
        const boxDok = document.getElementById('boxDokumenSyarat');
        if (boxDok) {
            boxDok.innerHTML = conf.syarat.map((dok, idx) => {
                return '<div class="col-md-6 mb-2">' +
                    '<label class="form-label small fw-bold d-flex justify-content-between mb-1" style="font-size:0.78rem;">' +
                        '<span><i class="bi bi-file-earmark-check text-success me-1"></i>' + dok.label + '</span>' +
                        '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.65rem;">Wajib</span>' +
                    '</label>' +
                    '<input type="file" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf" data-key="' + (dok.key || (dok.folder || '').replace('uploads/', '')) + '" data-folder="' + dok.folder + '" required />' +
                    '<small class="text-muted" style="font-size:0.7rem;">Format JPG, PNG, PDF (Maks 5MB)</small>' +
                '</div>';
            }).join('');
        }
    }
}

// Helper Tanggungan Keluarga
function addTanggunganRow() {
    const tbody = document.getElementById('tbodyTanggungan');
    if (!tbody) return;
    const rowCount = tbody.querySelectorAll('tr').length + 1;
    const tr = document.createElement('tr');
    tr.className = 'tanggungan-row';
    tr.innerHTML = `
        <td class="text-center align-middle row-number fw-bold">${rowCount}</td>
        <td>
            <input type="text" class="form-control form-control-sm tg-nama" placeholder="Nama lengkap anggota keluarga" required>
        </td>
        <td>
            <select class="form-select form-select-sm tg-jk" required>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm text-center tg-umur" placeholder="Umur" min="0" max="130" required>
        </td>
        <td>
            <select class="form-select form-select-sm tg-hubungan" required>
                <option value="Istri">Istri</option>
                <option value="Suami">Suami</option>
                <option value="Anak">Anak</option>
                <option value="Orang Tua">Orang Tua</option>
                <option value="Famili Lain">Famili Lain</option>
            </select>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle" onclick="removeTanggunganRow(this)" title="Hapus baris">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    reindexTanggunganRows();
}

function removeTanggunganRow(btn) {
    const tbody = document.getElementById('tbodyTanggungan');
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    if (rows.length <= 1) {
        alert('Minimal 1 tanggungan keluarga untuk Surat Keterangan Tidak Mampu.');
        return;
    }
    btn.closest('tr').remove();
    reindexTanggunganRows();
}

function reindexTanggunganRows() {
    const tbody = document.getElementById('tbodyTanggungan');
    if (!tbody) return;
    tbody.querySelectorAll('tr').forEach((tr, index) => {
        const numCol = tr.querySelector('.row-number');
        if (numCol) numCol.textContent = index + 1;
    });
}

function getTanggunganKeluargaData() {
    const list = [];
    const rows = document.querySelectorAll('#tbodyTanggungan tr.tanggungan-row');
    rows.forEach(tr => {
        const nama = tr.querySelector('.tg-nama')?.value.trim() || '';
        const jk = tr.querySelector('.tg-jk')?.value || 'Laki-laki';
        const umur = tr.querySelector('.tg-umur')?.value.trim() || '';
        const hub = tr.querySelector('.tg-hubungan')?.value || 'Anak';
        if (nama) {
            list.push({
                nama: nama,
                jenis_kelamin: jk,
                umur: umur,
                hubungan: hub
            });
        }
    });
    return list;
}

async function submitFormSurat(e) {
    e.preventDefault();
    const jenis = document.getElementById('in_jenis_surat').value;
    const conf = SURAT_CONF[jenis] || SURAT_CONF['suket_usaha'];

    const btn = document.getElementById('btnSubmitSurat');
    btn.disabled = true;
    btn.innerHTML = modeRevisi
        ? '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim perbaikan...'
        : '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim permohonan ke database...';

    const formData = new FormData();
    formData.append('jenis_surat', jenis);
    formData.append('nama_pemohon', document.getElementById('in_nama').value.trim());
    formData.append('nik_pemohon', document.getElementById('in_nik').value.trim());
    formData.append('tempat_lahir', document.getElementById('in_tempat_lahir').value.trim());
    formData.append('tanggal_lahir', document.getElementById('in_tanggal_lahir').value);
    formData.append('jenis_kelamin', document.getElementById('in_jk').value);
    formData.append('agama', 'Islam');
    formData.append('pekerjaan', document.getElementById('in_pekerjaan').value.trim());
    formData.append('alamat', document.getElementById('in_alamat').value.trim());
    formData.append('keperluan', document.getElementById('in_keperluan').value.trim());

    // Extra data spesifik
    const extra1 = document.getElementById('in_extra1').value.trim();
    const extra2 = document.getElementById('in_extra2').value.trim();
    if (jenis === 'suket_usaha') {
        formData.append('nama_usaha', extra1);
        formData.append('bidang_usaha', extra2);
    } else if (jenis === 'suket_meninggal') {
        formData.append('nama_almarhum', extra1);
        formData.append('sebab_meninggal', extra2);
    } else if (jenis === 'surat_nikah_na') {
        formData.append('nama_calon_pasangan', extra1);
        formData.append('bin_binti_pasangan', extra2);
    } else if (jenis === 'sket_penghasilan') {
        formData.append('nominal_penghasilan', extra1);
        formData.append('sumber_penghasilan', extra2);
    } else if (jenis === 'suket_tidak_mampu') {
        formData.append('estimasi_penghasilan', extra1);
        formData.append('kondisi_ekonomi', extra2);
        const tanggunganData = getTanggunganKeluargaData();
        if (tanggunganData.length === 0) {
            alert('Mohon isi minimal 1 data tanggungan keluarga untuk SKTM.');
            btn.disabled = false;
            btn.innerHTML = modeRevisi
                ? '<i class="bi bi-send-check-fill me-1"></i> Kirim Perbaikan Pengajuan'
                : '<i class="bi bi-send-fill me-1"></i> Ajukan Permohonan Surat';
            return;
        }
        formData.append('tanggungan_keluarga', JSON.stringify(tanggunganData));
    }

    // Upload files (only append if file selected)
    const fileInputs = document.querySelectorAll('#boxDokumenSyarat input[type="file"]');
    fileInputs.forEach((input, idx) => {
        if (input.files && input.files[0]) {
            const fileKey = input.getAttribute('data-key') || (input.getAttribute('data-folder') || '').replace('uploads/', '') || `dokumen_${idx+1}`;
            formData.append('berkas_' + fileKey, input.files[0]);
            formData.append(fileKey, input.files[0]);
            formData.append('files[]', input.files[0]);
        }
    });

    try {
        if (modeRevisi && revisiSuratId) {
            await (window.KotoAuth || KotoAuth).submitRevisiSurat(revisiSuratId, formData);
            alert('✅ Perbaikan pengajuan berhasil dikirim!\n\nPengajuan surat Anda akan diperiksa kembali oleh Admin Nagari.');
            batalRevisi();
            switchTab('status');
        } else {
            await (window.KotoAuth || KotoAuth).submitSurat(formData);
            alert('✅ Permohonan Berhasil Dikirim!\n\nPermohonan ' + conf.nama + ' Anda telah tersimpan di database resmi Nagari Koto Malintang.\nSurat kini masuk antrean verifikasi Admin.');
            e.target.reset();
            selectSuratType('suket_usaha');
            switchTab('status');
        }
    } catch (err) {
        let errMsg = err.message || 'Gagal memproses permohonan surat.';
        if (errMsg.includes('401') || errMsg.toLowerCase().includes('unauthenticated')) {
            errMsg = 'Sesi login sudah berakhir. Silakan login kembali.';
        } else if (errMsg.includes('403')) {
            errMsg = 'Pengajuan ini tidak dapat diperbaiki pada status saat ini.';
        } else if (errMsg.includes('422')) {
            errMsg = 'Periksa kembali data dan dokumen yang Anda masukkan.';
        }
        alert('⚠️ ' + errMsg);
    } finally {
        btn.disabled = false;
        btn.innerHTML = modeRevisi
            ? '<i class="bi bi-send-check-fill me-1"></i> Kirim Perbaikan Pengajuan'
            : '<i class="bi bi-send-fill me-1"></i> Ajukan Permohonan Surat';
    }
}

function renderDokumenRevisi(key, berkasList) {
    const conf = SURAT_CONF[key] || SURAT_CONF['suket_usaha'];
    const boxDok = document.getElementById('boxDokumenSyarat');
    if (!boxDok) return;

    boxDok.innerHTML = conf.syarat.map((dok, idx) => {
        const fileKey = dok.key || (dok.folder || '').replace('uploads/', '') || `dokumen_${idx+1}`;
        const existingBerkas = (berkasList || []).find(b => (
            b.jenis_berkas === fileKey ||
            b.jenis_berkas === dok.key ||
            (b.path_file && b.path_file.includes(fileKey))
        ));

        let existingHtml = '';
        if (existingBerkas) {
            const namaFile = existingBerkas.nama_file_asli || 'Dokumen telah terunggah';
            existingHtml = '<div class="mb-1 text-success small fw-semibold" style="font-size:0.72rem;">' +
                '<i class="bi bi-check-circle-fill me-1"></i>Sudah tersedia: <span class="text-dark">' + namaFile + '</span>' +
            '</div>';
        }

        return '<div class="col-md-6 mb-2">' +
            '<label class="form-label small fw-bold d-flex justify-content-between mb-1" style="font-size:0.78rem;">' +
                '<span><i class="bi bi-file-earmark-check text-success me-1"></i>' + dok.label + '</span>' +
                (existingBerkas ? '<span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.65rem;">Tersimpan</span>' : '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.65rem;">Wajib</span>') +
            '</label>' +
            existingHtml +
            '<input type="file" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf" data-key="' + fileKey + '" data-folder="' + dok.folder + '" ' + (existingBerkas ? '' : 'required') + ' />' +
            '<small class="text-muted" style="font-size:0.7rem;">' + (existingBerkas ? 'Pilih file baru jika ingin mengganti dokumen' : 'Format JPG, PNG, PDF (Maks 5MB)') + '</small>' +
        '</div>';
    }).join('');
}

function renderRevisiBanner(s) {
    let banner = document.getElementById('revisiBanner');
    if (!banner) {
        banner = document.createElement('div');
        banner.id = 'revisiBanner';
        const formEl = document.getElementById('formPengajuanWarga');
        if (formEl && formEl.parentNode) {
            formEl.parentNode.insertBefore(banner, formEl);
        }
    }
    if (banner) {
        const noRef = s.nomor_surat || ('#KM-00' + s.id);
        const namaJenis = s.nama_jenis_surat || s.jenis_surat || '-';
        banner.className = 'alert alert-warning border-warning shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3';
        banner.innerHTML = '<div>' +
            '<strong class="text-dark"><i class="bi bi-arrow-repeat me-1"></i> Mode Perbaikan Pengajuan: ' + noRef + ' (' + namaJenis + ')</strong>' +
            '<div class="text-dark small mt-1"><strong>Catatan Admin:</strong> ' + (s.catatan_admin || 'Silakan periksa dan perbaiki data atau dokumen persyaratan Anda.') + '</div>' +
        '</div>' +
        '<button type="button" class="btn btn-sm btn-outline-dark fw-semibold" onclick="batalRevisi()">' +
            '<i class="bi bi-x-lg me-1"></i> Batal Perbaikan' +
        '</button>';
        banner.classList.remove('d-none');
    }
}

function batalRevisi() {
    modeRevisi = false;
    revisiSuratId = null;
    currentRevisiBerkas = [];
    const banner = document.getElementById('revisiBanner');
    if (banner) banner.classList.add('d-none');

    const formEl = document.getElementById('formPengajuanWarga');
    if (formEl) formEl.reset();

    const btnSubmit = document.getElementById('btnSubmitSurat');
    if (btnSubmit) {
        btnSubmit.className = 'btn btn-success fw-bold px-4';
        btnSubmit.innerHTML = '<i class="bi bi-send-fill me-1"></i> Ajukan Permohonan Surat';
    }

    selectSuratType('suket_usaha');
}

async function bukaFormRevisi(id) {
    try {
        const modalEl = document.getElementById('modalDetailSurat');
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();
        }

        const res = await (window.KotoAuth || KotoAuth).getSuratDetail(id);
        if (!res || !res.success || !res.data) {
            alert('Gagal mengambil data pengajuan untuk perbaikan.');
            return;
        }

        const s = res.data;
        if (s.status !== 'perlu_revisi_warga') {
            alert('Pengajuan ini tidak dalam status perlu perbaikan berkas.');
            return;
        }

        modeRevisi = true;
        revisiSuratId = s.id;
        currentRevisiBerkas = s.berkas || s.berkas_persyaratans || [];

        switchTab('ajukan');

        const jenisKey = s.jenis_surat || 'suket_usaha';
        selectSuratType(jenisKey);

        const setVal = (idEl, val) => { const el = document.getElementById(idEl); if (el) el.value = val || ''; };
        setVal('in_nama', s.nama_pemohon);
        setVal('in_nik', s.nik_pemohon);
        setVal('in_tempat_lahir', s.tempat_lahir);
        setVal('in_tanggal_lahir', s.tanggal_lahir ? s.tanggal_lahir.substring(0, 10) : '');
        setVal('in_jk', s.jenis_kelamin || 'Laki-laki');
        setVal('in_pekerjaan', s.pekerjaan);
        setVal('in_alamat', s.alamat);
        setVal('in_keperluan', s.keperluan);

        const ds = s.data_spesifik || {};
        if (jenisKey === 'suket_usaha') {
            setVal('in_extra1', ds.nama_usaha || s.nama_usaha);
            setVal('in_extra2', ds.bidang_usaha || s.bidang_usaha);
        } else if (jenisKey === 'suket_meninggal') {
            setVal('in_extra1', ds.nama_almarhum || s.nama_almarhum);
            setVal('in_extra2', ds.sebab_meninggal || s.sebab_meninggal);
        } else if (jenisKey === 'surat_nikah_na') {
            setVal('in_extra1', ds.nama_calon_pasangan || s.nama_calon_pasangan);
            setVal('in_extra2', ds.bin_binti_pasangan || s.bin_binti_pasangan);
        } else if (jenisKey === 'sket_penghasilan') {
            setVal('in_extra1', ds.nominal_penghasilan || s.nominal_penghasilan);
            setVal('in_extra2', ds.sumber_penghasilan || s.sumber_penghasilan);
        } else if (jenisKey === 'suket_tidak_mampu') {
            setVal('in_extra1', ds.estimasi_penghasilan || s.estimasi_penghasilan);
            setVal('in_extra2', ds.kondisi_ekonomi || s.kondisi_ekonomi);

            const listTg = ds.tanggungan_keluarga || [];
            const tbodyTg = document.getElementById('tbodyTanggungan') || document.getElementById('tanggunganTbody');
            if (tbodyTg) {
                tbodyTg.innerHTML = '';
                if (Array.isArray(listTg) && listTg.length > 0) {
                    listTg.forEach((tg, idx) => {
                        const tr = document.createElement('tr');
                        tr.className = 'tanggungan-row';
                        tr.innerHTML = `
                            <td class="text-center align-middle row-number fw-bold">${idx + 1}</td>
                            <td><input type="text" class="form-control form-control-sm tg-nama" value="${tg.nama || ''}" required></td>
                            <td>
                                <select class="form-select form-select-sm tg-jk" required>
                                    <option value="Laki-laki" ${tg.jenis_kelamin === 'Laki-laki' ? 'selected' : ''}>Laki-laki</option>
                                    <option value="Perempuan" ${tg.jenis_kelamin === 'Perempuan' ? 'selected' : ''}>Perempuan</option>
                                </select>
                            </td>
                            <td><input type="number" class="form-control form-control-sm text-center tg-umur" value="${tg.umur || ''}" min="0" max="130" required></td>
                            <td>
                                <select class="form-select form-select-sm tg-hubungan" required>
                                    <option value="Istri" ${tg.hubungan === 'Istri' ? 'selected' : ''}>Istri</option>
                                    <option value="Suami" ${tg.hubungan === 'Suami' ? 'selected' : ''}>Suami</option>
                                    <option value="Anak" ${tg.hubungan === 'Anak' ? 'selected' : ''}>Anak</option>
                                    <option value="Orang Tua" ${tg.hubungan === 'Orang Tua' ? 'selected' : ''}>Orang Tua</option>
                                    <option value="Famili Lain" ${tg.hubungan === 'Famili Lain' ? 'selected' : ''}>Famili Lain</option>
                                </select>
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-circle" onclick="removeTanggunganRow(this)" title="Hapus baris">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        `;
                        tbodyTg.appendChild(tr);
                    });
                } else {
                    addTanggunganRow();
                }
            }
        }

        renderDokumenRevisi(jenisKey, currentRevisiBerkas);
        renderRevisiBanner(s);

        const btnSubmit = document.getElementById('btnSubmitSurat');
        if (btnSubmit) {
            btnSubmit.className = 'btn btn-warning text-dark fw-bold px-4';
            btnSubmit.innerHTML = '<i class="bi bi-send-check-fill me-1"></i> Kirim Perbaikan Pengajuan';
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err) {
        alert('Gagal membuka form perbaikan: ' + err.message);
    }
}

// =========================================================================
// 3. AI ASISTEN LAYANAN SURAT
// =========================================================================
async function handleAiChat(e) {
    e.preventDefault();
    const input = document.getElementById('aiChatInput');
    const msg = input.value.trim();
    if (!msg) return;

    const chatBox = document.getElementById('aiChatBox');

    // User bubble
    const uBubble = document.createElement('div');
    uBubble.className = 'user-bubble';
    uBubble.innerText = msg;
    chatBox.appendChild(uBubble);
    input.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;

    // Loading bubble
    const lBubble = document.createElement('div');
    lBubble.className = 'ai-bubble';
    lBubble.innerHTML = '<span class="spinner-border spinner-border-sm me-1 text-success"></span> <em>AI sedang memproses dengan data resmi nagari...</em>';
    chatBox.appendChild(lBubble);
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
        const jenis = document.getElementById('in_jenis_surat').value;
        const res = await KotoAuth.askAiSurat(msg, jenis);
        const reply = res.data?.response || res.message || res.reply || 'Maaf, terjadi kendala respon.';
        lBubble.innerHTML = '<strong>🤖 Asisten Nagari:</strong><br/>' + reply.replace(/\n/g, '<br/>');
    } catch (err) {
        lBubble.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i> Asisten AI sedang sibuk. Silakan coba sesaat lagi.</span>';
    }
    chatBox.scrollTop = chatBox.scrollHeight;
}

// =========================================================================
// 4. STATUS & RIWAYAT SURAT DENGAN TIMELINE STEPPER
// =========================================================================
async function loadStatusSurat(filter) {
    if (!filter) filter = 'semua';
    const box = document.getElementById('statusCardsContainer');
    if (!box) return;
    box.innerHTML = '<div class="p-4 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil status surat terbaru dari database...</div>';

    try {
        const res = await KotoAuth.getSuratList(filter);
        currentSuratList = res.data || [];
        renderStatusCards(currentSuratList);
    } catch (e) {
        box.innerHTML = '<div class="alert alert-warning small"><i class="bi bi-exclamation-circle me-1"></i> Tidak dapat memuat status surat. Pastikan backend terhubung.</div>';
    }
}

function filterStatusTab(status, btn) {
    document.querySelectorAll('#tab-status .btn-group button').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    loadStatusSurat(status);
}

function renderStatusCards(list) {
    const box = document.getElementById('statusCardsContainer');
    if (!box) return;

    if (!list || list.length === 0) {
        box.innerHTML = '<div class="text-center py-5">' +
            '<div class="stat-icon bg-light text-muted mx-auto mb-2"><i class="bi bi-inbox fs-3"></i></div>' +
            '<h6 class="fw-bold text-dark">Belum Ada Pengajuan Surat</h6>' +
            '<p class="small text-muted mb-3">Anda belum memiliki pengajuan surat pada kategori ini.</p>' +
            '<button class="btn btn-success btn-sm rounded-pill px-3" onclick="switchTab(\'ajukan\')">' +
                '<i class="bi bi-file-earmark-plus me-1"></i> Ajukan Surat Sekarang' +
            '</button>' +
        '</div>';
        return;
    }

    box.innerHTML = list.map(s => {
        const step = s.timeline_step || 1;
        const isApproved = (s.status === 'disetujui');
        const isRejected = (s.status === 'ditolak');
        const isRevision = (s.status === 'perlu_revisi_warga');

        const step1Cls = (step >= 1 ? 'complete' : '') + (step === 1 ? ' active' : '');
        const step2Cls = (step >= 2 ? 'complete' : '') + (step === 2 ? ' active' : '');
        const step3Cls = (step >= 3 ? 'complete' : '') + (step === 3 ? ' active' : '');
        const step4Cls = isApproved ? 'complete active' : (isRejected ? 'active' : '');

        const circle4Cls = isRejected ? 'bg-danger text-white border-danger' : '';
        const circle4Icon = isRejected ? 'bi-x-lg' : 'bi-check-lg';
        const label4Cls = isRejected ? 'text-danger' : '';
        const label4Text = isRejected ? 'Ditolak' : 'Selesai';

        const alasanHtml = (isRejected && s.catatan_admin) ?
            '<div class="alert alert-danger small py-2 mb-3"><strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan Penolakan:</strong> ' + s.catatan_admin + '</div>' : '';

        const catatanRevisiHtml = (isRevision && s.catatan_admin) ?
            '<div class="alert alert-warning small py-2 mb-3 border-warning"><strong><i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Catatan Perbaikan Berkas:</strong> ' + s.catatan_admin + '</div>' : '';

        const revisiBtnHtml = isRevision ?
            '<button class="btn btn-warning text-dark btn-sm rounded-pill px-3 fw-bold me-1" onclick="bukaFormRevisi(' + s.id + ')"><i class="bi bi-pencil-square me-1"></i> Perbaiki Pengajuan</button>' : '';

        const printBtnHtml = isApproved ?
            '<button class="btn btn-danger btn-sm rounded-pill px-3 fw-bold me-1" onclick="downloadSuratFinalPdf(' + s.id + ')"><i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF</button>' +
            '<button class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" onclick="printDirectSurat(' + s.id + ')"><i class="bi bi-printer-fill me-1"></i> Cetak Dokumen</button>' : '';

        return '<div class="card-custom p-4">' +
            '<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">' +
                '<div>' +
                    '<span class="badge bg-light text-primary border font-monospace mb-1">' + (s.nomor_surat || '-') + '</span>' +
                    '<h5 class="fw-bold mb-0 text-dark">' + (s.nama_jenis_surat || '-') + '</h5>' +
                    '<small class="text-muted"><i class="bi bi-calendar3 me-1"></i>Diajukan pada: ' + (s.tanggal_format || '-') + '</small>' +
                '</div>' +
                '<span class="badge bg-' + (s.status_badge_color || (isRevision ? 'warning text-dark' : 'secondary')) + ' fs-6 px-3 py-2">' +
                    (s.status_label || (isRevision ? 'Perlu Perbaikan Berkas' : (s.status || '-'))) +
                '</span>' +
            '</div>' +
            '<div class="timeline-steps mb-3">' +
                '<div class="step-item ' + step1Cls + '"><div class="step-circle"><i class="bi bi-file-earmark-check"></i></div><div class="step-label">Diajukan</div></div>' +
                '<div class="step-item ' + step2Cls + '"><div class="step-circle"><i class="bi bi-person-check"></i></div><div class="step-label">Verifikasi Admin</div></div>' +
                '<div class="step-item ' + step3Cls + '"><div class="step-circle"><i class="bi bi-pen"></i></div><div class="step-label">TTD Wali Nagari</div></div>' +
                '<div class="step-item ' + step4Cls + '"><div class="step-circle ' + circle4Cls + '"><i class="bi ' + circle4Icon + '"></i></div><div class="step-label ' + label4Cls + '">' + label4Text + '</div></div>' +
            '</div>' +
            alasanHtml +
            catatanRevisiHtml +
            '<div class="d-flex justify-content-between align-items-center pt-2 border-top">' +
                '<span class="small text-muted">Keperluan: <em>' + (s.keperluan || '-') + '</em></span>' +
                '<div class="d-flex gap-2">' +
                    '<button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="openDetailSuratModal(' + s.id + ')"><i class="bi bi-eye-fill me-1"></i> Lihat Detail</button>' +
                    revisiBtnHtml +
                    printBtnHtml +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

// =========================================================================
// 5. DETAIL SURAT MODAL & DOWNLOAD PDF
// =========================================================================
let activeDetailSurat = null;

async function openDetailSuratModal(id) {
    try {
        const res = await KotoAuth.getSuratDetail(id);
        if (res && res.success) {
            const s = res.data;
            activeDetailSurat = s;

            document.getElementById('dt_judulSurat').innerText = s.nama_jenis_surat || 'Surat Keterangan';
            document.getElementById('dt_nomorRef').innerText = s.nomor_surat || ('#KM-00' + s.id);
            document.getElementById('dt_badgeStatus').className = 'badge bg-' + (s.status_badge_color || (s.status === 'perlu_revisi_warga' ? 'warning text-dark' : 'secondary'));
            document.getElementById('dt_badgeStatus').innerText = s.status_label || (s.status === 'perlu_revisi_warga' ? 'Perlu Perbaikan Berkas' : s.status);

            document.getElementById('dt_namaPemohon').innerText = s.nama_pemohon || '-';
            document.getElementById('dt_nikPemohon').innerText = s.nik_pemohon || '-';
            document.getElementById('dt_tanggal').innerText = s.tanggal_format || '-';
            document.getElementById('dt_keperluan').innerText = s.keperluan || '-';
            document.getElementById('dt_isiSurat').innerText = s.isi_surat || 'Naskah dalam penyusunan dinas.';

            // Stepper update
            const step = s.timeline_step || 1;
            document.getElementById('dt_step1').className = 'step-item ' + (step >= 1 ? 'complete active' : '');
            document.getElementById('dt_step2').className = 'step-item ' + (step >= 2 ? 'complete active' : '');
            document.getElementById('dt_step3').className = 'step-item ' + (step >= 3 ? 'complete active' : '');
            document.getElementById('dt_step4').className = 'step-item ' + (s.status === 'disetujui' ? 'complete active' : '');

            // Alasan Tolak / Catatan Revisi
            const tolakBox = document.getElementById('dt_alasanTolakBox');
            if (tolakBox) {
                if (s.status === 'perlu_revisi_warga' && s.catatan_admin) {
                    tolakBox.className = 'alert alert-warning small border-warning';
                    tolakBox.innerHTML = '<strong><i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Catatan Perbaikan Berkas:</strong><div id="dt_alasanTolakText" class="mt-1">' + s.catatan_admin + '</div>';
                    tolakBox.classList.remove('d-none');
                } else if (s.status === 'ditolak' && (s.catatan_admin || s.catatan_wali)) {
                    tolakBox.className = 'alert alert-danger small';
                    tolakBox.innerHTML = '<strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Catatan Penolakan:</strong><div id="dt_alasanTolakText" class="mt-1">' + (s.catatan_admin || s.catatan_wali) + '</div>';
                    tolakBox.classList.remove('d-none');
                } else {
                    tolakBox.classList.add('d-none');
                }
            }

            // Berkas
            const bList = document.getElementById('dt_berkasList');
            if (bList) {
                if (s.berkas && s.berkas.length > 0) {
                    bList.innerHTML = s.berkas.map(b => {
                        return '<span class="badge bg-light text-dark border p-2">' +
                            '<i class="bi bi-file-earmark-check text-success me-1"></i> ' + (b.label_jenis || 'Dokumen') + ' (' + (b.nama_file_asli || 'berkas') + ')' +
                        '</span>';
                    }).join('');
                } else {
                    bList.innerHTML = '<span class="text-muted small">Berkas terlampir pada sistem arsip digital.</span>';
                }
            }

            // Tombol Perbaiki Pengajuan di Modal Detail
            const btnRevisiModal = document.getElementById('dt_btnPerbaikiRevisi');
            if (btnRevisiModal) {
                if (s.status === 'perlu_revisi_warga') {
                    btnRevisiModal.classList.remove('d-none');
                    btnRevisiModal.onclick = () => bukaFormRevisi(s.id);
                } else {
                    btnRevisiModal.classList.add('d-none');
                }
            }

            // Print & PDF Download button
            const btnPrint = document.getElementById('dt_btnPrintSurat');
            const btnPdf = document.getElementById('dt_btnDownloadPdf');
            if (s.status === 'disetujui') {
                if (btnPrint) btnPrint.classList.remove('d-none');
                if (btnPdf) btnPdf.classList.remove('d-none');
            } else {
                if (btnPrint) btnPrint.classList.add('d-none');
                if (btnPdf) btnPdf.classList.add('d-none');
            }

            const modal = new bootstrap.Modal(document.getElementById('modalDetailSurat'));
            modal.show();
        }
    } catch (err) {
        alert('Gagal memuat detail surat: ' + err.message);
    }
}

async function downloadSuratFinalPdf(id) {
    const suratId = id || (activeDetailSurat ? activeDetailSurat.id : null);
    if (!suratId) {
        alert('ID surat tidak valid.');
        return;
    }

    const btnModal = document.getElementById('dt_btnDownloadPdf');
    const originalText = btnModal ? btnModal.innerHTML : '';

    try {
        if (btnModal && !id) {
            btnModal.disabled = true;
            btnModal.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunduh...';
        }

        await KotoAuth.downloadSuratPdf(suratId);
    } catch (err) {
        alert(err.message || 'Surat belum dapat diunduh. Silakan coba kembali.');
    } finally {
        if (btnModal && !id) {
            btnModal.disabled = false;
            btnModal.innerHTML = originalText || '<i class="bi bi-file-earmark-pdf-fill me-1"></i> Download PDF';
        }
    }
}

function printDirectSurat(id) {
    openDetailSuratModal(id).then(() => {
        printSuratFromDetail();
    });
}

function printSuratFromDetail() {
    if (!activeDetailSurat) return;
    const s = activeDetailSurat;

    const printWin = window.open('', '_blank');
    const tglDisetujui = s.disetujui_pada ? s.disetujui_pada.split(',')[0] : new Date().toLocaleDateString('id-ID');
    const jenisTitle = (s.nama_jenis_surat || 'SURAT KETERANGAN').toUpperCase();
    const nomor = s.nomor_surat || ('#KM-00' + s.id);
    const isi = s.isi_surat || '';
    const namaWali = s.nama_wali || 'NAZARUDDIN, S.Pd.I';
    const gelarWali = s.gelar_wali || 'Datuak Bandaro';
    const ttdUrl = s.ttd_wali_url;

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
        '.stempel-img { position: absolute; left: 10px; top: 25px; width: 90px; opacity: 0.85; transform: rotate(-8deg); z-index: 1; }',
        '</style></head><body>',
        '<div class="kop"><img src="image/logo agam.png" alt="Logo"/><div><h4>PEMERINTAH KABUPATEN AGAM</h4><h4>KECAMATAN TANJUNG RAYA</h4><h3>PEMERINTAH NAGARI KOTO MALINTANG</h3><p>Alamat: Jl. Raya Maninjau - Lubuk Basung, Kode Pos: 26454</p></div></div>',
        '<div class="judul"><h5>' + jenisTitle + '</h5><div>Nomor: ' + nomor + '</div></div>',
        '<div class="isi">' + isi + '</div>',
        '<div class="ttd-box"><div>Koto Malintang, ' + tglDisetujui + '</div><div style="font-weight:bold;">WALI NAGARI KOTO MALINTANG</div>',
        '<div style="height:90px;display:flex;align-items:center;justify-content:center;position:relative;">',
        '<img src="image/stempel_resmi.svg" class="stempel-img" onerror="this.style.display=\'none\'"/>',
        ttdImgHtml,
        '</div>',
        '<div style="font-weight:bold;text-decoration:underline;">' + namaWali + '</div><small>' + gelarWali + '</small></div></body></html>'
    ];

    printWin.document.open();
    printWin.document.write(htmlParts.join('\n'));
    printWin.document.close();
    printWin.focus();
    setTimeout(() => { try { printWin.print(); } catch(e){} }, 500);
}

// =========================================================================
// 6. NOTIFIKASI SURAT
// =========================================================================
async function loadFullNotifikasi() {
    const box = document.getElementById('fullNotifikasiList');
    if (box) box.innerHTML = '<div class="p-4 text-center text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil notifikasi...</div>';

    try {
        const res = await KotoAuth.getNotifikasi();
        const list = res.data || [];
        const unread = res.unread_count || 0;

        const navBadge = document.getElementById('navNotifBadge');
        const statNotif = document.getElementById('statNotifCount');
        if (navBadge) navBadge.innerText = unread;
        if (statNotif) statNotif.innerText = unread;

        // Render ke Dropdown Navbar
        const drop = document.getElementById('notifDropdownList');
        if (drop) {
            if (list.length === 0) {
                drop.innerHTML = '<li class="p-3 text-center text-muted small">Belum ada notifikasi baru.</li>';
            } else {
                drop.innerHTML = '<li class="p-2 border-bottom d-flex justify-content-between align-items-center">' +
                    '<strong class="small text-dark">Pemberitahuan Surat</strong>' +
                    '<a href="javascript:void(0)" class="small text-success text-decoration-none" onclick="markAllRead()">Tandai Semua Dibaca</a>' +
                '</li>' +
                list.slice(0, 5).map(n => {
                    const shortMsg = (n.message || '').substring(0, 70);
                    return '<li class="p-2 border-bottom">' +
                        '<a href="javascript:void(0)" class="text-decoration-none text-dark d-flex gap-2" onclick="openDetailSuratModal(' + n.surat_id + ')">' +
                            '<i class="bi ' + (n.icon || 'bi-bell') + ' fs-5 mt-1"></i>' +
                            '<div>' +
                                '<strong class="small d-block">' + (n.title || 'Notifikasi') + '</strong>' +
                                '<small class="text-muted d-block" style="font-size:0.75rem;">' + shortMsg + '...</small>' +
                                '<span class="text-muted" style="font-size:0.68rem;">' + (n.time || '-') + '</span>' +
                            '</div>' +
                        '</a>' +
                    '</li>';
                }).join('');
            }
        }

        // Render ke Full Tab
        if (box) {
            if (list.length === 0) {
                box.innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-bell-slash fs-2 mb-2 d-block"></i><h6>Tidak Ada Notifikasi</h6><small>Belum ada pemberitahuan baru terkait surat Anda.</small></div>';
            } else {
                box.innerHTML = list.map(n => {
                    return '<div class="p-3 rounded-3 border bg-white d-flex align-items-start gap-3 shadow-sm">' +
                        '<div class="p-2 rounded-circle bg-light"><i class="bi ' + (n.icon || 'bi-bell') + ' fs-4"></i></div>' +
                        '<div class="flex-grow-1">' +
                            '<div class="d-flex justify-content-between align-items-center mb-1">' +
                                '<strong class="text-dark">' + (n.title || 'Notifikasi') + '</strong>' +
                                '<small class="text-muted">' + (n.time || '-') + '</span>' +
                            '</div>' +
                            '<p class="small text-muted mb-2">' + (n.message || '-') + '</p>' +
                            '<button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="openDetailSuratModal(' + n.surat_id + ')">' +
                                '<i class="bi bi-search me-1"></i> Lihat Status Surat' +
                            '</button>' +
                        '</div>' +
                    '</div>';
                }).join('');
            }
        }
    } catch (e) {
        if (box) box.innerHTML = '<div class="alert alert-warning small">Gagal memuat notifikasi.</div>';
    }
}

async function markAllRead() {
    await KotoAuth.markAllNotifikasiRead();
    const navBadge = document.getElementById('navNotifBadge');
    const statNotif = document.getElementById('statNotifCount');
    if (navBadge) navBadge.innerText = '0';
    if (statNotif) statNotif.innerText = '0';
    loadFullNotifikasi();
}

// =========================================================================
// 7. UMKM SAYA (TAMBAH, EDIT, HAPUS)
// =========================================================================
async function loadMyUmkm() {
    const box = document.getElementById('myUmkmGridContainer');
    if (!box) return;
    box.innerHTML = '<div class="col-12 text-center py-4 text-muted small"><span class="spinner-border spinner-border-sm me-1"></span> Mengambil data UMKM Anda...</div>';

    try {
        const res = await KotoAuth.getMyUmkm();
        const list = res.data || [];
        const statUmkm = document.getElementById('statUmkmCount');
        if (statUmkm) statUmkm.innerText = list.length;

        if (list.length === 0) {
            box.innerHTML = '<div class="col-12 text-center py-5">' +
                '<div class="stat-icon bg-light text-muted mx-auto mb-2"><i class="bi bi-shop fs-3"></i></div>' +
                '<h6 class="fw-bold text-dark">Belum Ada UMKM Terdaftar</h6>' +
                '<p class="small text-muted mb-3">Tambahkan data usaha Anda agar dapat dipromosikan melalui direktori UMKM Nagari.</p>' +
                '<button class="btn btn-success btn-sm rounded-pill px-3" onclick="openModalTambahUmkm()">' +
                    '<i class="bi bi-plus-circle-fill me-1"></i> Tambah UMKM Sekarang' +
                '</button>' +
            '</div>';
            return;
        }

        box.innerHTML = list.map(u => {
            const nama = u.nama_usaha || u.nama || '-';
            const kat = u.kategori || 'Umum';
            const thn = u.tahun_berdiri || '-';
            const desc = u.deskripsi || '-';
            const pem = u.pemilik || '-';
            const waRaw = u.whatsapp || u.wa || '';
            const waClean = waRaw.replace(/[^0-9]/g, '');

            return '<div class="col-md-6">' +
                '<div class="card-custom p-3 h-100 d-flex flex-column" style="border: 2px solid #10b981;">' +
                    '<div class="d-flex align-items-start justify-content-between gap-2 mb-2">' +
                        '<div class="d-flex align-items-center gap-2">' +
                            '<div class="stat-icon bg-success bg-opacity-10 text-success" style="width:40px;height:40px;">' +
                                '<i class="bi bi-shop fs-5"></i>' +
                            '</div>' +
                            '<div>' +
                                '<h6 class="fw-bold text-dark mb-0">' + nama + '</h6>' +
                                '<span class="badge bg-success text-white" style="font-size:0.7rem;">' + kat + '</span>' +
                            '</div>' +
                        '</div>' +
                        '<span class="badge bg-light text-muted border" style="font-size:0.7rem;">Est. ' + thn + '</span>' +
                    '</div>' +
                    '<p class="small text-muted mb-3 flex-grow-1" style="line-height:1.5;">' + desc + '</p>' +
                    '<div class="p-2 bg-light rounded-3 small mb-3">' +
                        '<div class="d-flex justify-content-between"><span class="text-muted">Pemilik:</span><strong>' + pem + '</strong></div>' +
                        '<div class="d-flex justify-content-between"><span class="text-muted">WhatsApp:</span><strong class="text-success">' + (waRaw || '-') + '</strong></div>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center pt-2 border-top mt-auto">' +
                        '<a href="https://wa.me/' + waClean + '" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3">' +
                            '<i class="bi bi-whatsapp me-1"></i> Hubungi' +
                        '</a>' +
                        '<button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="hapusUmkm(' + u.id + ')">' +
                            '<i class="bi bi-trash me-1"></i> Hapus' +
                        '</button>' +
                    '</div>' +
                '</div>' +
            '</div>';
        }).join('');
    } catch (e) {
        box.innerHTML = '<div class="col-12 alert alert-warning small">Gagal memuat UMKM.</div>';
    }
}

function openModalTambahUmkm() {
    document.getElementById('umkmEditId').value = '';
    document.getElementById('um_nama').value = '';
    document.getElementById('um_pemilik').value = currentUser?.name || '';
    document.getElementById('um_wa').value = currentUser?.phone || '';
    document.getElementById('um_alamat').value = currentUser?.address || 'Nagari Koto Malintang';
    document.getElementById('um_deskripsi').value = '';
    document.getElementById('um_tahun').value = new Date().getFullYear();
    document.getElementById('umkmModalTitle').innerText = '+ Tambah Data UMKM Baru';

    const modal = new bootstrap.Modal(document.getElementById('modalFormUmkm'));
    modal.show();
}

async function submitFormUmkm(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitUmkm');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    const formData = new FormData();
    formData.append('nama_usaha', document.getElementById('um_nama').value.trim());
    formData.append('pemilik', document.getElementById('um_pemilik').value.trim());
    formData.append('kategori', document.getElementById('um_kategori').value);
    formData.append('whatsapp', document.getElementById('um_wa').value.trim());
    formData.append('tahun_berdiri', document.getElementById('um_tahun').value);
    formData.append('jam_operasional', document.getElementById('um_jam').value.trim() || '08:00 - 17:00 WIB');
    formData.append('instagram', document.getElementById('um_ig').value.trim());
    formData.append('alamat', document.getElementById('um_alamat').value.trim());
    formData.append('deskripsi', document.getElementById('um_deskripsi').value.trim());

    const fotoIn = document.getElementById('um_foto');
    if (fotoIn.files && fotoIn.files[0]) {
        formData.append('foto', fotoIn.files[0]);
    }

    try {
        await KotoAuth.tambahUmkm(formData);
        bootstrap.Modal.getInstance(document.getElementById('modalFormUmkm')).hide();
        alert('UMKM Berhasil Ditambahkan ke Database Nagari!');
        loadMyUmkm();
    } catch (err) {
        alert('Gagal menyimpan UMKM: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Simpan UMKM';
    }
}

async function hapusUmkm(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus data UMKM ini?')) return;
    try {
        await KotoAuth.deleteUmkm(id);
        alert('Data UMKM berhasil dihapus.');
        loadMyUmkm();
    } catch (e) {
        alert('Gagal menghapus: ' + e.message);
    }
}

// =========================================================================
// 8. UBAH PASSWORD
// =========================================================================
async function handleUpdatePassword(e) {
    e.preventDefault();
    const pCur = document.getElementById('pwdCurrent').value;
    const pNew = document.getElementById('pwdNew').value;
    const pConf = document.getElementById('pwdConfirm').value;
    const alertBox = document.getElementById('alertPassword');
    const btn = document.getElementById('btnSubmitPwd');

    if (pNew !== pConf) {
        alertBox.className = 'alert alert-danger small';
        alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> Konfirmasi password baru tidak cocok!';
        alertBox.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses di server...';

    try {
        const res = await KotoAuth.updatePassword(pCur, pNew, pConf);
        alertBox.className = 'alert alert-success small';
        alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + (res.message || 'Kata sandi berhasil diperbarui!');
        alertBox.classList.remove('d-none');
        e.target.reset();
    } catch (err) {
        alertBox.className = 'alert alert-danger small';
        alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> ' + (err.message || 'Gagal mengubah password. Periksa password lama Anda.');
        alertBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Perbarui Kata Sandi';
    }
}

// =========================================================================
// 9. PROFIL SAYA
// =========================================================================
function loadProfilForm() {
    if (!currentUser) return;
    const tName = document.getElementById('profTitleName');
    const pName = document.getElementById('profName');
    const pNik = document.getElementById('profNik');
    const pPhone = document.getElementById('profPhone');
    const pUser = document.getElementById('profUsername');
    const pAddr = document.getElementById('profAddress');

    if (tName) tName.innerText = currentUser.name;
    if (pName) pName.value = currentUser.name || '';
    if (pNik) pNik.value = currentUser.nik || '';
    if (pPhone) pPhone.value = currentUser.phone || '';
    if (pUser) pUser.value = currentUser.username || '';
    if (pAddr) pAddr.value = currentUser.address || '';
}

async function handleUpdateProfil(e) {
    e.preventDefault();
    const alertBox = document.getElementById('alertProfil');
    const btn = document.getElementById('btnSubmitProfil');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    try {
        const res = await KotoAuth.apiRequest('/warga/profil', {
            method: 'PUT',
            body: {
                name: document.getElementById('profName').value.trim(),
                nik: document.getElementById('profNik').value.trim(),
                phone: document.getElementById('profPhone').value.trim(),
                address: document.getElementById('profAddress').value.trim(),
            }
        });

        currentUser = res.user;
        KotoAuth.setSession(res.user);
        const navUser = document.getElementById('navUserName');
        const profTitle = document.getElementById('profTitleName');
        if (navUser) navUser.innerText = (res.user.name || '').split(' ')[0];
        if (profTitle) profTitle.innerText = res.user.name;

        alertBox.className = 'alert alert-success small';
        alertBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Profil berhasil diperbarui di database!';
        alertBox.classList.remove('d-none');
    } catch (err) {
        alertBox.className = 'alert alert-danger small';
        alertBox.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i> ' + (err.message || 'Gagal memperbarui profil.');
        alertBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-save me-1"></i> Simpan Perubahan Profil';
    }
}

// =========================================================================
// 10. MODAL PRATINJAU NASKAH SURAT
// =========================================================================
function showPreviewModal() {
    const jenis = document.getElementById('in_jenis_surat').value;
    const conf = SURAT_CONF[jenis] || SURAT_CONF['suket_usaha'];
    const nama = document.getElementById('in_nama').value || '[Nama Pemohon]';
    const nik = document.getElementById('in_nik').value || '[NIK Pemohon]';
    const tempat = document.getElementById('in_tempat_lahir').value || '[Tempat]';
    const tgl = document.getElementById('in_tanggal_lahir').value || '[Tanggal]';
    const jk = document.getElementById('in_jk').value;
    const pekerjaan = document.getElementById('in_pekerjaan').value || '[Pekerjaan]';
    const alamat = document.getElementById('in_alamat').value || '[Alamat Jorong]';
    const keperluan = document.getElementById('in_keperluan').value || '[Keperluan Surat]';
    const extra1 = document.getElementById('in_extra1').value || '-';
    const extra2 = document.getElementById('in_extra2').value || '-';

    let extraTanggunganHtml = '';
    if (jenis === 'suket_tidak_mampu') {
        const listTg = getTanggunganKeluargaData();
        if (listTg.length > 0) {
            extraTanggunganHtml = `
                <p class="small mb-1 fw-bold">Dengan tanggungan keluarga sebagai berikut:</p>
                <table class="table table-sm table-bordered small mb-3" style="font-size:0.75rem;">
                    <thead class="table-light text-center">
                        <tr><th>No</th><th>Nama</th><th>Jenis Kelamin</th><th>Umur</th><th>Hubungan dg KK</th></tr>
                    </thead>
                    <tbody>
                        ${listTg.map((t, idx) => `
                            <tr>
                                <td class="text-center">${idx + 1}</td>
                                <td><strong>${t.nama}</strong></td>
                                <td class="text-center">${t.jenis_kelamin}</td>
                                <td class="text-center">${t.umur}</td>
                                <td>${t.hubungan}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }
    }

    const modalBody = document.getElementById('pvContentBody');
    if (modalBody) {
        modalBody.innerHTML = '<div class="text-center mb-3">' +
            '<h6 class="fw-bold mb-0 text-decoration-underline">' + conf.nama.toUpperCase() + '</h6>' +
            '<small class="text-muted">Nomor: 470 / [Nomor Dinas] / WN-KM / VIII / 2026</small>' +
        '</div>' +
        '<p class="small">Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, dengan ini menerangkan bahwa:</p>' +
        '<table class="table table-sm table-borderless small mb-3">' +
            '<tr><td style="width:170px;">Nama Lengkap</td><td>: <strong>' + nama + '</strong></td></tr>' +
            '<tr><td>NIK KTP</td><td>: ' + nik + '</td></tr>' +
            '<tr><td>Tempat / Tgl Lahir</td><td>: ' + tempat + ', ' + tgl + '</td></tr>' +
            '<tr><td>Jenis Kelamin</td><td>: ' + jk + '</td></tr>' +
            '<tr><td>Pekerjaan</td><td>: ' + pekerjaan + '</td></tr>' +
            '<tr><td>Alamat Domisili</td><td>: ' + alamat + '</td></tr>' +
            '<tr><td>' + conf.extra1 + '</td><td>: <strong>' + extra1 + '</strong></td></tr>' +
            '<tr><td>' + conf.extra2 + '</td><td>: ' + extra2 + '</td></tr>' +
        '</table>' +
        extraTanggunganHtml +
        '<p class="small">Adalah benar warga Nagari Koto Malintang dan surat ini diberikan atas permohonan yang bersangkutan untuk keperluan: <strong>' + keperluan + '</strong>.</p>' +
        '<p class="small mb-0">Demikian surat keterangan ini kami buat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>';
    }

    const modal = new bootstrap.Modal(document.getElementById('modalPreviewSurat'));
    modal.show();
}

// =========================================================================
// 11. LOGOUT
// =========================================================================
async function doLogout() {
    if (confirm('Keluar dari Portal Warga?')) {
        await KotoAuth.logout();
        window.location.href = 'login.html';
    }
}

// =========================================================================
// 12. INITIALIZATION
// =========================================================================
document.addEventListener('DOMContentLoaded', () => {
    selectSuratType('suket_usaha');
    loadDashboardData();
    loadFullNotifikasi();
});