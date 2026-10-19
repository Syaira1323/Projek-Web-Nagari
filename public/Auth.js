// =========================================================================
// Auth.js — REST API Client & Autentikasi Nagari Koto Malintang
// Terhubung langsung ke Backend Laravel di C:\laragon\www\layanan-surat
// =========================================================================

(function (global) {
  // 1. Konfigurasi Base URL API Laravel
  // 1. Konfigurasi Base URL API Laravel
  let activeApiBase = (function() {
    if (typeof window !== 'undefined' && window.API_BASE) {
      return window.API_BASE.replace(/\/$/, '');
    }
    if (typeof window !== 'undefined' && window.location) {
      const origin = window.location.origin;
      const pathname = window.location.pathname || '';
      const port = window.location.port || '';

      // Jika diakses melalui Apache subfolder
      if (pathname.includes('/layanan-surat/public')) {
        return origin + '/layanan-surat/public/api';
      }
      if (pathname.includes('/layanan-surat')) {
        return origin + '/layanan-surat/public/api';
      }
      if (pathname.includes('/frontend/public')) {
        return 'http://localhost/layanan-surat/public/api';
      }
      // Jika port Laravel dev server (8000)
      if (port === '8000') {
        return origin + '/api';
      }
      // Jika port Live Server (5500, 5501, etc.) atau file://
      if (['5500', '5501', '5502', '3000', '5173'].includes(port) || window.location.protocol === 'file:') {
        return 'http://localhost/layanan-surat/public/api';
      }
      // Jika hostname localhost atau 127.0.0.1
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        return origin + '/layanan-surat/public/api';
      }
      // Default: gunakan relative path /api
      return origin + '/api';
    }
    return 'http://localhost/layanan-surat/public/api';
  })();

  const fallbackApiBase = 'http://localhost/layanan-surat/public/api';

  // 2. Helper HTTP Fetch Request dengan session cookie Laravel
  async function apiRequest(endpoint, options = {}) {
    let url = `${activeApiBase}${endpoint}`;

    const headers = {
      'Accept': 'application/json',
      ...(options.headers || {})
    };

    let reqBody = options.body;
    const isFormData = typeof FormData !== 'undefined' && reqBody instanceof FormData;
    let requestMethod = options.method || 'GET';

    // PHP only parses multipart fields reliably for POST requests. Laravel
    // supports method spoofing, so file edits remain real PUT/PATCH routes.
    if (isFormData && (requestMethod === 'PUT' || requestMethod === 'PATCH')) {
      reqBody.append('_method', requestMethod);
      requestMethod = 'POST';
    }

    if (!isFormData && reqBody && typeof reqBody === 'object') {
      headers['Content-Type'] = 'application/json';
      reqBody = JSON.stringify(reqBody);
    }

    try {
      let response = null;
      try {
        response = await fetch(url, {
          ...options,
          method: requestMethod,
          body: reqBody,
          headers,
          credentials: 'include',
        });
      } catch (fetchErr) {
        // Fallback jika fetch ke activeApiBase gagal
        if (activeApiBase !== fallbackApiBase) {
          const altUrl = `${fallbackApiBase}${endpoint}`;
          try {
            response = await fetch(altUrl, {
              ...options,
              method: requestMethod,
              body: reqBody,
              headers,
              credentials: 'include',
            });
            activeApiBase = fallbackApiBase;
          } catch (altErr) {
            throw new Error(`Tidak dapat terhubung ke server API (${fetchErr.message})`);
          }
        } else {
          throw new Error(`Tidak dapat terhubung ke server API (${fetchErr.message})`);
        }
      }

      // Jika response 404 Not Found pada URL non-subfolder, coba fallback ke subfolder Laragon
      if (response && response.status === 404 && !url.includes('/layanan-surat/public/api')) {
        const altUrl = `${fallbackApiBase}${endpoint}`;
        try {
          const altRes = await fetch(altUrl, {
            ...options,
            method: requestMethod,
            body: reqBody,
            headers,
            credentials: 'include',
          });
          if (altRes.ok) {
            response = altRes;
            activeApiBase = fallbackApiBase;
          }
        } catch (e) {}
      }

      if (!response) {
        throw new Error('Tidak ada respon dari server API');
      }

      let data = null;
      const contentType = response.headers.get('content-type') || '';

      if (contentType.includes('application/json')) {
        try {
          data = await response.json();
        } catch (jsonErr) {
          console.warn('[KotoAPI] Parsing JSON gagal:', jsonErr);
          data = null;
        }
      } else {
        const textResp = await response.text().catch(() => '');
        console.warn('[KotoAPI] Menerima non-JSON response dari', url, textResp.substring(0, 150));
      }

      if (!response.ok) {
        const errMsg = (data && (data.message || data.error)) || `Terjadi kesalahan pada server (HTTP ${response.status} ${response.statusText})`;
        throw new Error(errMsg);
      }

      return data || { success: true };
    } catch (err) {
      console.warn(`[KotoAPI] Error pada request ke ${endpoint}:`, err.message);
      throw err;
    }
  }

  // 3. Autentikasi & Akun Pengguna
  async function login(loginId, password, remember = false) {
    const res = await apiRequest('/login', {
      method: 'POST',
      body: { login_id: loginId, password: password, remember: remember }
    });

    if (res && res.success) {
      setSession(res.user);
      return res;
    }
    throw new Error(res.message || 'Login gagal.');
  }

  async function register(userData) {
    return await apiRequest('/register', {
      method: 'POST',
      body: userData
    });
  }

  async function logout() {
    try {
      await apiRequest('/logout', { method: 'POST' });
    } catch (e) {
      // Abaikan error jaringan saat logout
    } finally {
      if (typeof localStorage !== 'undefined') {
        localStorage.removeItem('km_session');
      }
    }
  }

  async function updatePassword(currentPassword, newPassword, confirmPassword) {
    return await apiRequest('/profile/password', {
      method: 'PUT',
      body: {
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
        password_lama: currentPassword,
        password_baru: newPassword,
        password_baru_confirmation: confirmPassword,
      }
    });
  }

  // 4. API Dashboard & Layanan Surat Warga
  async function getDashboardWarga() {
    return await apiRequest('/warga/dashboard');
  }

  async function getSuratList(status = 'semua') {
    const q = status !== 'semua' ? `?status=${encodeURIComponent(status)}` : '';
    return await apiRequest(`/pengajuan-surat${q}`);
  }

  async function getSuratDetail(id) {
    return await apiRequest(`/pengajuan-surat/${id}`);
  }

  async function submitSurat(formData) {
    return await apiRequest('/pengajuan-surat', {
      method: 'POST',
      body: formData
    });
  }

  async function submitRevisiSurat(id, formData) {
    return await apiRequest(`/pengajuan-surat/${id}/revisi`, {
      method: 'POST',
      body: formData
    });
  }

  // 5. API Notifikasi Surat
  async function getNotifikasi() {
    return await apiRequest('/notifikasi');
  }

  async function markNotifikasiRead(id) {
    return await apiRequest(`/notifikasi/${id}/read`, { method: 'PATCH' });
  }

  async function markAllNotifikasiRead() {
    return await apiRequest('/notifikasi/read-all', { method: 'PATCH' });
  }

  // 6. API UMKM Warga
  async function getMyUmkm() {
    return await apiRequest('/umkm/my');
  }

  async function tambahUmkm(formData) {
    return await apiRequest('/umkm', {
      method: 'POST',
      body: formData
    });
  }

  async function updateUmkm(id, formData) {
    return await apiRequest(`/umkm/${id}`, {
      method: 'PUT',
      body: formData
    });
  }

  async function deleteUmkm(id) {
    return await apiRequest(`/umkm/${id}`, {
      method: 'DELETE'
    });
  }

  // 7. API Konten Publik Nagari
  async function getProfilNagari() {
    return await apiRequest('/profil-nagari');
  }

  async function getPerangkatNagari() {
    return await apiRequest('/perangkat-nagari');
  }

  async function getVisiMisi() {
    return await apiRequest('/visi-misi');
  }

  async function getSejarah() {
    return await apiRequest('/sejarah');
  }

  async function getAdatBudaya() {
    return await apiRequest('/adat-budaya');
  }

  async function getStrukturNagari() {
    return await apiRequest('/struktur-nagari');
  }

  async function getUmkm(kategori = '') {
    const q = kategori ? `?kategori=${encodeURIComponent(kategori)}` : '';
    return await apiRequest(`/umkm${q}`);
  }

  async function getObjekWisata() {
    return await apiRequest('/objek-wisata');
  }

  async function getKabaNagari(kategori = '') {
    const q = kategori ? `?kategori=${encodeURIComponent(kategori)}` : '';
    return await apiRequest(`/kaba-nagari${q}`);
  }

  async function getJenisSurat() {
    return await apiRequest('/jenis-surat');
  }

  async function getPersyaratanSurat() {
    return await apiRequest('/persyaratan-surat');
  }

  async function getGaleri() {
    return await apiRequest('/galeri');
  }

  // 8. API Panel Wali Nagari
  async function getWaliProfile() {
    return await apiRequest('/wali-nagari/profile');
  }

  async function getWaliDashboard() {
    return await apiRequest('/wali-nagari/dashboard');
  }

  async function getWaliSuratList(status = 'menunggu_wali_nagari') {
    const q = status ? `?status=${encodeURIComponent(status)}` : '';
    return await apiRequest(`/wali-nagari/surat${q}`);
  }

  async function getWaliSuratDetail(id) {
    return await apiRequest(`/wali-nagari/surat/${id}`);
  }

  async function uploadWaliTtd(formData) {
    return await apiRequest('/wali-nagari/tanda-tangan', {
      method: 'POST',
      body: formData
    });
  }

  async function deleteWaliTtd() {
    return await apiRequest('/wali-nagari/tanda-tangan', {
      method: 'DELETE'
    });
  }

  async function approveSuratWali(id, catatan = '') {
    return await apiRequest(`/wali-nagari/surat/${id}/approve`, {
      method: 'POST',
      body: { catatan_wali: catatan }
    });
  }

  async function kembalikanSuratWali(id, catatanWali) {
    return await apiRequest(`/wali-nagari/surat/${id}/kembalikan`, {
      method: 'POST',
      body: { catatan_wali: catatanWali }
    });
  }

  async function rejectSuratWali(id, alasan = '') {
    return await apiRequest(`/wali-nagari/surat/${id}/reject`, {
      method: 'POST',
      body: { alasan_penolakan: alasan }
    });
  }

  // 9. Integrasi Asisten AI Hugging Face
  async function askAiSurat(message, jenisSurat = '', suratId = null) {
    return await apiRequest('/ai/surat', {
      method: 'POST',
      body: {
        message: message,
        jenis_surat: jenisSurat,
        surat_id: suratId
      }
    });
  }

  async function askAiChat(message) {
    return await apiRequest('/ai/chat', {
      method: 'POST',
      body: { message: message }
    });
  }

  async function getRekomendasiSurat(text) {
    return await apiRequest('/ai/rekomendasi-surat', {
      method: 'POST',
      body: { text: text }
    });
  }

  // 10. API Panel Administrator
  async function getAdminDashboard() {
    return await apiRequest('/admin/dashboard');
  }

  async function getAdminSuratList(status = 'semua', search = '') {
    const params = new URLSearchParams();
    if (status && status !== 'semua') params.append('status', status);
    if (search) params.append('search', search);
    const q = params.toString() ? `?${params.toString()}` : '';
    return await apiRequest(`/admin/surat${q}`);
  }

  async function getAdminSuratDetail(id) {
    return await apiRequest(`/admin/surat/${id}`);
  }

  async function verifySuratAdmin(id, payload) {
    return await apiRequest(`/admin/surat/${id}/verify`, {
      method: 'POST',
      body: payload
    });
  }

  async function mintaRevisiAdmin(id, catatanAdmin) {
    return await apiRequest(`/admin/surat/${id}/minta-revisi`, {
      method: 'POST',
      body: { catatan_admin: catatanAdmin }
    });
  }

  async function rejectSuratAdmin(id, alasan) {
    return await apiRequest(`/admin/surat/${id}/reject`, {
      method: 'POST',
      body: { alasan_penolakan: alasan }
    });
  }

  async function getAdminWarga(search = '') {
    const q = search ? `?search=${encodeURIComponent(search)}` : '';
    return await apiRequest(`/admin/warga${q}`);
  }

  async function getAdminProfil() {
    return await apiRequest('/admin/konten/profil');
  }

  async function updateAdminProfil(data) {
    return await apiRequest('/admin/konten/profil', {
      method: 'PUT',
      body: data
    });
  }

  async function getAdminKaba(kategori = '', search = '') {
    const params = new URLSearchParams();
    if (kategori && kategori !== 'semua') params.append('kategori', kategori);
    if (search) params.append('search', search);
    const q = params.toString() ? `?${params.toString()}` : '';
    return await apiRequest(`/admin/kaba-nagari${q}`);
  }

  async function tambahAdminKaba(data) {
    return await apiRequest('/admin/kaba-nagari', {
      method: 'POST',
      body: data
    });
  }

  async function updateAdminKaba(id, data) {
    return await apiRequest(`/admin/kaba-nagari/${id}`, {
      method: 'PUT',
      body: data
    });
  }

  async function deleteAdminKaba(id) {
    return await apiRequest(`/admin/kaba-nagari/${id}`, {
      method: 'DELETE'
    });
  }

  async function getAdminKabaDetail(id) { return await apiRequest(`/admin/kaba-nagari/${id}`); }
  async function uploadAdminKabaGambar(id, formData) { return await apiRequest(`/admin/kaba-nagari/${id}/gambar`, { method: 'POST', body: formData }); }
  async function deleteAdminKabaGambar(id, gambarId) { return await apiRequest(`/admin/kaba-nagari/${id}/gambar/${gambarId}`, { method: 'DELETE' }); }

  // 11. Session & Storage Helpers
  function getSession() {
    try { return JSON.parse(localStorage.getItem('km_session')); } catch (e) { return null; }
  }
  function setSession(s) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_session', JSON.stringify(s));
    }
  }
  function getUsers() {
    try { return JSON.parse(localStorage.getItem('km_users') || '[]'); } catch (e) { return []; }
  }
  function saveUsers(u) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_users', JSON.stringify(u));
    }
  }
  function getSuratMasuk() {
    try { return JSON.parse(localStorage.getItem('km_surat') || '[]'); } catch (e) { return []; }
  }
  function saveSuratMasuk(s) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_surat', JSON.stringify(s));
    }
  }
  function getBerita() {
    try { return JSON.parse(localStorage.getItem('km_berita') || '[]'); } catch (e) { return []; }
  }
  function saveBerita(b) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_berita', JSON.stringify(b));
    }
  }
  function getPengumuman() {
    try { return JSON.parse(localStorage.getItem('km_pengumuman') || '[]'); } catch (e) { return []; }
  }
  function savePengumuman(p) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_pengumuman', JSON.stringify(p));
    }
  }
  function getUmkmSync() {
    try { return JSON.parse(localStorage.getItem('km_umkm') || '[]'); } catch (e) { return []; }
  }
  function saveUmkm(u) {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('km_umkm', JSON.stringify(u));
    }
  }

  const KotoAuthObj = {
    get API_BASE() { return activeApiBase; },
    get activeApiBase() { return activeApiBase; },
    apiRequest,
    login,
    register,
    logout,
    updatePassword,
    getDashboardWarga,
    getSuratList,
    getSuratDetail,
    submitSurat,
    submitRevisiSurat,
    getNotifikasi,
    markNotifikasiRead,
    markAllNotifikasiRead,
    getMyUmkm,
    tambahUmkm,
    updateUmkm,
    deleteUmkm,
    getProfilNagari,
    getPerangkatNagari,
    getVisiMisi,
    getSejarah,
    getAdatBudaya,
    getStrukturNagari,
    getUmkm,
    getObjekWisata,
    getKabaNagari,
    getGaleri,
    getJenisSurat,
    getPersyaratanSurat,
    getWaliProfile,
    getWaliDashboard,
    getWaliSuratList,
    getWaliSuratDetail,
    uploadWaliTtd,
    deleteWaliTtd,
    approveSuratWali,
    kembalikanSuratWali,
    rejectSuratWali,
    getAdminDashboard,
    getAdminSuratList,
    getAdminSuratDetail,
    verifySuratAdmin,
    mintaRevisiAdmin,
    rejectSuratAdmin,
    getAdminWarga,
    getAdminProfil,
    updateAdminProfil,
    getAdminKaba,
    tambahAdminKaba,
    updateAdminKaba,
    deleteAdminKaba,
    getAdminKabaDetail,
    uploadAdminKabaGambar,
    deleteAdminKabaGambar,
    askAiSurat,
    askAiChat,
    getRekomendasiSurat,
    getSession,
    setSession,
    getUsers, saveUsers,
    getSuratMasuk, saveSuratMasuk,
    getBerita, saveBerita,
    getPengumuman, savePengumuman,
    getUmkmSync, saveUmkm
  };

  // Attach to global window and export
  global.KotoAuth = KotoAuthObj;
  if (typeof window !== 'undefined') {
    window.KotoAuth = KotoAuthObj;
  }
  if (typeof globalThis !== 'undefined') {
    globalThis.KotoAuth = KotoAuthObj;
  }
})(typeof window !== 'undefined' ? window : (typeof global !== 'undefined' ? global : this));
