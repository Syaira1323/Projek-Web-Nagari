// =============================================
// Auth.js — Sistem Autentikasi & Data Store Nagari Koto Malintang
// =============================================

const KotoAuth = (function () {
  const DEFAULT_USERS = [
    { id: 'usr-admin', username: 'admin', password: 'password', name: 'Administrator Nagari', role: 'admin', email: 'admin@kotomalintang.id', status: 'active', createdAt: '2026-01-01' },
    { id: 'usr-wali', username: 'walinagari', password: 'password', name: 'Nazaruddin, S.Pd.I', role: 'walinagari', email: 'wali@kotomalintang.id', status: 'active', createdAt: '2026-01-01' },
    { id: 'usr-warga', username: 'warga', password: 'password', name: 'Ahmad Fauzi', role: 'warga', email: 'ahmad.fauzi@kotomalintang.id', nik: '1306011205900001', status: 'active', createdAt: '2026-02-10' }
  ];

  const DEFAULT_SURAT = [
    {
      id: 'srt-001',
      jenisSurat: 'Surat Keterangan Usaha (SKU)',
      pengirim: 'Ahmad Fauzi',
      nik: '1306011205900001',
      email: 'ahmad.fauzi@kotomalintang.id',
      subjek: 'Pengajuan Tambahan Modal Usaha KUR Bank BRI',
      nomorSurat: '510 / 024 / WN-KM / VIII / 2026',
      isi: 'Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang menerangkan bahwa:\n\nNama: Ahmad Fauzi\nNIK: 1306011205900001\nUsaha: Bengkel Las & Bubut Karya Maju\nAlamat: Jorong Koto Gadang, Nagari Koto Malintang\n\nAdalah benar memiliki usaha tersebut dan surat ini dibuat untuk keperluan pengajuan modal usaha KUR.',
      tanggal: '2026-08-25T08:30:00.000Z',
      status: 'menunggu_walinagari', // menunggu_admin | menunggu_walinagari | disetujui | ditolak | arsip
      alasanTolak: '',
      stempel: false,
      ttdWali: false,
      disetujuiPada: null,
      arsipLocal: true,
      gdriveUrl: 'https://drive.google.com/drive/folders/kotomalintang_arsip_2026'
    },
    {
      id: 'srt-002',
      jenisSurat: 'Surat Keterangan Tidak Mampu (SKTM)',
      pengirim: 'Siti Aminah',
      nik: '1306015508880002',
      email: 'siti.aminah@gmail.com',
      subjek: 'Permohonan Beasiswa KIP-Kuliah',
      nomorSurat: '',
      isi: 'Yang bertanda tangan di bawah ini, menerangkan bahwa Siti Aminah benar berasal dari keluarga pra-sejahtera.',
      tanggal: '2026-08-26T02:15:00.000Z',
      status: 'menunggu_admin',
      alasanTolak: '',
      stempel: false,
      ttdWali: false,
      disetujuiPada: null,
      arsipLocal: false,
      gdriveUrl: ''
    },
    {
      id: 'srt-003',
      jenisSurat: 'Surat Keterangan Domisili (SKD)',
      pengirim: 'Rian Hidayat',
      nik: '1306012003950003',
      email: 'rian.hidayat@yahoo.com',
      subjek: 'Keterangan Domisili Tempat Tinggal Baru',
      nomorSurat: '470 / 012 / WN-KM / VIII / 2026',
      isi: 'Menerangkan bahwa Rian Hidayat benar berdomisili di Jorong Muko-Muko, Nagari Koto Malintang.',
      tanggal: '2026-08-22T09:00:00.000Z',
      status: 'disetujui',
      alasanTolak: '',
      stempel: true,
      ttdWali: true,
      disetujuiPada: '2026-08-23T10:00:00.000Z',
      arsipLocal: true,
      gdriveUrl: 'https://drive.google.com/drive/folders/kotomalintang_arsip_2026'
    },
    {
      id: 'srt-004',
      jenisSurat: 'Surat Pengantar Nikah (Model NA)',
      pengirim: 'Budi Santoso',
      nik: '1306011406980004',
      email: 'budi.santoso@gmail.com',
      subjek: 'Pengantar Nikah ke KUA Tanjung Raya',
      nomorSurat: '',
      isi: 'Permohonan pengantar nikah ke KUA.',
      tanggal: '2026-08-21T11:00:00.000Z',
      status: 'ditolak',
      alasanTolak: 'Berkas belum lengkap: Mohon lampirkan Surat Keterangan Imunisasi Catin dari Puskesmas dan Fotokopi KTP Orang Tua.',
      stempel: false,
      ttdWali: false,
      disetujuiPada: null,
      arsipLocal: false,
      gdriveUrl: ''
    }
  ];

  const DEFAULT_BERITA = [
    { id: 'b1', judul: 'Musrenbang Nagari Koto Malintang Tahun Anggaran 2027 Sukses Digelar', kategori: 'Pemerintahan', tanggal: '2026-08-20', status: 'publish', isi: 'Pemerintah Nagari Koto Malintang bersama Bamus dan tokoh masyarakat menyepakati prioritas pembangunan infrastruktur dan pemberdayaan ekonomi.' },
    { id: 'b2', judul: 'Penyaluran Bantuan Langsung Tunai (BLT) Dana Desa Tahap III', kategori: 'Sosial', tanggal: '2026-08-15', status: 'publish', isi: 'Sebanyak 85 Keluarga Penerima Manfaat (KPM) menerima penyaluran BLT Dana Desa di Kantor Wali Nagari.' },
    { id: 'b3', judul: 'Pelatihan Digital Marketing dan Packaging untuk UMKM Lokal', kategori: 'Ekonomi', tanggal: '2026-08-10', status: 'publish', isi: 'Mendorong daya saing produk UMKM Danau Maninjau melalui pelatihan pemasaran digital dan pengemasan higienis.' }
  ];

  const DEFAULT_PENGUMUMAN = [
    { id: 'p1', judul: 'Jadwal Gotong Royong Massal Kebersihan Lingkungan Nagari', tanggal: '2026-08-28', status: 'aktif', isi: 'Dihimbau kepada seluruh warga di 4 Jorong untuk berpartisipasi dalam goro bersama pada hari Minggu, 30 Agustus 2026.' },
    { id: 'p2', judul: 'Pelayanan Perekaman KTP Elektronik Keliling dari Disdukcapil Agam', tanggal: '2026-08-22', status: 'aktif', isi: 'Pelayanan jemput bola perekaman KTP-el akan dilaksanakan di Aula Kantor Wali Nagari Koto Malintang.' }
  ];

  const DEFAULT_UMKM = [
    { id: 'u1', nama: 'Kopi Robusta Maninjau Asli', kategori: 'Kuliner', pemilik: 'Mak Datuk', wa: '081234567890', alamat: 'Jorong Koto Gadang', deskripsi: 'Kopi robusta pegunungan Maninjau diproses secara tradisional dengan aroma khas dan nikmat.' },
    { id: 'u2', nama: 'Kerajinan Anyaman Pandan Koto', kategori: 'Kerajinan', pemilik: 'Ibu Nurhayati', wa: '085298765432', alamat: 'Jorong Muko-Muko', deskripsi: 'Produk tas, tikar, dan souvenir anyaman pandan alami ramah lingkungan khas Nagari Koto Malintang.' },
    { id: 'u3', nama: 'Rinuak Goreng Renyah Danau Maninjau', kategori: 'Kuliner', pemilik: 'Uni Erna', wa: '082188776655', alamat: 'Jorong Tanjung Alai', deskripsi: 'Olahan ikan rinuak khas Danau Maninjau yang gurih, renyah, dan kaya protein.' }
  ];

  function init() {
    if (!localStorage.getItem('km_users')) localStorage.setItem('km_users', JSON.stringify(DEFAULT_USERS));
    if (!localStorage.getItem('km_surat')) localStorage.setItem('km_surat', JSON.stringify(DEFAULT_SURAT));
    if (!localStorage.getItem('km_berita')) localStorage.setItem('km_berita', JSON.stringify(DEFAULT_BERITA));
    if (!localStorage.getItem('km_pengumuman')) localStorage.setItem('km_pengumuman', JSON.stringify(DEFAULT_PENGUMUMAN));
    if (!localStorage.getItem('km_umkm')) localStorage.setItem('km_umkm', JSON.stringify(DEFAULT_UMKM));
    if (!localStorage.getItem('km_gdrive_folder')) localStorage.setItem('km_gdrive_folder', 'https://drive.google.com/drive/folders/kotomalintang_arsip_2026');
  }
  init();

  function getUsers() { return JSON.parse(localStorage.getItem('km_users') || '[]'); }
  function saveUsers(u) { localStorage.setItem('km_users', JSON.stringify(u)); }

  function getSuratMasuk() { return JSON.parse(localStorage.getItem('km_surat') || '[]'); }
  function saveSuratMasuk(s) { localStorage.setItem('km_surat', JSON.stringify(s)); }

  function getBerita() { return JSON.parse(localStorage.getItem('km_berita') || '[]'); }
  function saveBerita(b) { localStorage.setItem('km_berita', JSON.stringify(b)); }

  function getPengumuman() { return JSON.parse(localStorage.getItem('km_pengumuman') || '[]'); }
  function savePengumuman(p) { localStorage.setItem('km_pengumuman', JSON.stringify(p)); }

  function getUmkm() { return JSON.parse(localStorage.getItem('km_umkm') || '[]'); }
  function saveUmkm(u) { localStorage.setItem('km_umkm', JSON.stringify(u)); }

  function getSession() {
    try { return JSON.parse(localStorage.getItem('km_session')); } catch (e) { return null; }
  }
  function setSession(s) { localStorage.setItem('km_session', JSON.stringify(s)); }
  function logout() { localStorage.removeItem('km_session'); }

  return {
    getUsers, saveUsers,
    getSuratMasuk, saveSuratMasuk,
    getBerita, saveBerita,
    getPengumuman, savePengumuman,
    getUmkm, saveUmkm,
    getSession, setSession, logout
  };
})();
