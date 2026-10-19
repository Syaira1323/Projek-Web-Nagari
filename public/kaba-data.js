// Data berita Kaba Nagari bersama dan fungsi render bersama
(function(){
  window.KABA_POSTS = [
    {
      id: 'umkm',
      title: 'Pembukaan Pendaftaran Program Bantuan UMKM',
      date: '15 Agustus 2024',
      category: 'Pengumuman',
      image: 'image/wisata1.jpg',
      excerpt: 'Pemerintah Nagari Koto Malintang membuka pendaftaran program bantuan untuk pelaku UMKM guna meningkatkan ekonomi masyarakat nagari.',
      detail: `
        <p>Pemerintah Nagari Koto Malintang resmi membuka pendaftaran program bantuan untuk pelaku usaha mikro, kecil, dan menengah (UMKM). Program ini diinisiasi sebagai bentuk dukungan nyata terhadap perkembangan sektor ekonomi masyarakat nagari, terutama pasca masa pandemi yang cukup menghambat aktivitas usaha.</p>
        <p>Melalui program ini, pelaku UMKM berkesempatan memperoleh bantuan modal usaha, bimbingan teknis, hingga pendampingan usaha secara berkelanjutan. Pendaftaran dapat dilakukan di kantor wali nagari setiap hari kerja atau secara online melalui formulir resmi yang sudah disediakan.</p>
        <p>Persyaratan yang harus dipenuhi antara lain: fotokopi KTP, surat keterangan usaha, serta proposal singkat mengenai rencana penggunaan bantuan. Semua berkas akan diverifikasi oleh tim khusus sebelum ditetapkan penerima bantuan.</p>
        <p>Wali Nagari Koto Malintang menyampaikan harapannya agar bantuan ini bisa menjadi stimulan dalam mengembangkan usaha masyarakat, menciptakan lapangan kerja baru, dan meningkatkan perekonomian lokal. Selain itu, program ini juga diharapkan dapat memperkuat daya saing UMKM nagari di tingkat kabupaten maupun provinsi.</p>
      `
    },
    {
      id: 'jalan',
      title: 'Perbaikan Jalan Nagari Dimulai Minggu Depan',
      date: '12 Agustus 2024',
      category: 'Pembangunan',
      image: 'image/bg2.jpg',
      excerpt: 'Proyek perbaikan jalan utama nagari sepanjang 3 km akan dimulai. Warga diimbau menggunakan jalur alternatif selama pembangunan.',
      detail: `
        <p>Pemerintah Nagari Koto Malintang akan segera memulai proyek perbaikan jalan utama nagari sepanjang 3 kilometer. Jalan ini merupakan akses vital yang menghubungkan antar-jorong sekaligus jalur distribusi utama hasil pertanian dan perdagangan warga.</p>
        <p>Proyek ini direncanakan berlangsung selama 45 hari kerja dengan melibatkan kontraktor lokal dan pengawasan langsung dari dinas terkait. Pekerjaan mencakup perataan jalan, pengaspalan baru, serta perbaikan drainase di beberapa titik yang rawan banjir.</p>
        <p>Selama masa perbaikan, pengguna jalan diminta untuk menggunakan jalur alternatif yang sudah ditetapkan oleh pihak nagari. Petugas juga akan ditempatkan di beberapa persimpangan untuk memberikan arahan kepada pengendara.</p>
        <p>Wali Nagari berharap perbaikan ini tidak hanya meningkatkan kenyamanan berkendara, tetapi juga memperlancar distribusi barang dan hasil tani, sehingga memberikan dampak positif pada pertumbuhan ekonomi nagari.</p>
      `
    },
    {
      id: 'vaksin',
      title: 'Vaksinasi Booster COVID-19 Gratis',
      date: '10 Agustus 2024',
      category: 'Kesehatan',
      image: 'image/irene.jpg',
      excerpt: 'Posyandu Nagari Koto Malintang menyelenggarakan vaksinasi booster gratis. Pendaftaran dapat dilakukan di kantor wali nagari.',
      detail: `
        <p>Dalam rangka menjaga imunitas masyarakat, Posyandu Nagari Koto Malintang menggelar vaksinasi booster COVID-19 gratis. Program ini terbuka untuk seluruh warga tanpa dipungut biaya.</p>
        <p>Kegiatan vaksinasi akan dilakukan di Balai Nagari mulai pukul 08.00 hingga 15.00 WIB. Warga cukup membawa fotokopi KTP atau KK untuk proses pendaftaran. Bagi yang memiliki riwayat penyakit tertentu, disarankan untuk berkonsultasi dengan tenaga medis yang sudah disiapkan di lokasi.</p>
        <p>Program ini merupakan bentuk kerja sama antara pemerintah nagari, puskesmas setempat, dan tenaga relawan kesehatan. Target utama adalah menjangkau kelompok rentan, seperti lansia, ibu hamil, serta warga dengan penyakit penyerta.</p>
        <p>Diharapkan kegiatan ini dapat meningkatkan cakupan vaksinasi di nagari, sehingga tercipta herd immunity yang lebih kuat dan masyarakat bisa beraktivitas dengan lebih aman.</p>
      `
    },
    {
      id: 'gotongroyong',
      title: 'Gotong Royong Bersih Nagari Serentak',
      date: '8 Agustus 2024',
      category: 'Sosial',
      image: 'image/re.jpg',
      excerpt: 'Masyarakat Nagari Koto Malintang berpartisipasi dalam gotong royong bersih nagari yang dilaksanakan serentak di seluruh jorong.',
      detail: `
        <p>Seluruh masyarakat Nagari Koto Malintang turut berpartisipasi dalam kegiatan gotong royong bersih nagari yang dilaksanakan serentak di seluruh jorong. Kegiatan ini bertujuan untuk menjaga kebersihan lingkungan sekaligus mempererat tali persaudaraan antarwarga.</p>
        <p>Gotong royong meliputi pembersihan saluran irigasi, jalan nagari, fasilitas umum, hingga tempat ibadah. Setiap jorong memiliki titik fokus kegiatan sesuai kebutuhan wilayah masing-masing.</p>
        <p>Kegiatan ini juga mendapat dukungan dari perangkat nagari, karang taruna, serta kelompok ibu PKK. Bahkan, beberapa sekolah turut mengirimkan siswa untuk ikut berpartisipasi dalam aksi kebersamaan ini.</p>
        <p>Pemerintah nagari berharap budaya gotong royong tetap terjaga sebagai warisan sosial yang memperkuat kebersamaan masyarakat. Dengan lingkungan bersih dan sehat, kualitas hidup warga nagari juga akan semakin meningkat.</p>
      `
    },
    {
      id: 'beasiswa',
      title: 'Bantuan Beasiswa Untuk Siswa Berprestasi',
      date: '5 Agustus 2024',
      category: 'Pendidikan',
      image: 'image/forest1.jpg',
      excerpt: 'Program beasiswa untuk siswa berprestasi dari keluarga kurang mampu telah disalurkan kepada 25 siswa guna meringankan beban pendidikan.',
      detail: `
        <p>Pemerintah Nagari Koto Malintang kembali menyalurkan bantuan beasiswa bagi siswa berprestasi dari keluarga kurang mampu. Tahun ini, sebanyak 25 siswa dari tingkat SD hingga SMA menerima bantuan tersebut.</p>
        <p>Bantuan berupa biaya pendidikan, perlengkapan sekolah, serta tunjangan buku diberikan langsung kepada siswa melalui sekolah masing-masing. Proses seleksi dilakukan secara transparan berdasarkan prestasi akademik, keaktifan siswa, serta kondisi ekonomi keluarga.</p>
        <p>Dalam sambutannya, Wali Nagari menyampaikan bahwa program ini merupakan wujud nyata kepedulian terhadap generasi muda. Ia berharap dengan adanya beasiswa ini, para siswa dapat semakin bersemangat dalam belajar dan mencapai cita-citanya.</p>
        <p>Pemerintah nagari juga berkomitmen untuk memperluas program ini ke tahun-tahun berikutnya, dengan jumlah penerima yang lebih banyak, seiring dengan meningkatnya alokasi dana pendidikan.</p>
      `
    },
    {
      id: 'lingkungan',
      title: 'Program Penghijauan Nagari Sukses',
      date: '3 Agustus 2024',
      category: 'Lingkungan',
      image: 'image/forest2.jpg',
      excerpt: 'Program penanaman 1000 pohon buah dan pohon pelindung berhasil dilaksanakan dengan melibatkan seluruh elemen masyarakat nagari.',
      detail: `
        <p>Program penghijauan yang dilaksanakan oleh Pemerintah Nagari Koto Malintang berhasil menanam lebih dari 1000 pohon, terdiri dari pohon buah dan pohon pelindung. Kegiatan ini dilaksanakan sebagai bentuk kepedulian terhadap kelestarian lingkungan dan pengendalian perubahan iklim.</p>
        <p>Penanaman dilakukan di lahan-lahan kosong, sepanjang jalan nagari, serta area sekitar sungai. Masyarakat turut serta dalam kegiatan ini, mulai dari kelompok tani, karang taruna, hingga para pelajar yang diajak untuk lebih peduli terhadap lingkungan.</p>
        <p>Selain menambah ruang hijau, program ini juga diharapkan dapat memberikan manfaat ekonomi dalam jangka panjang melalui hasil panen buah yang bisa dimanfaatkan warga. Pohon pelindung yang ditanam juga berfungsi mencegah erosi dan memperbaiki kualitas udara.</p>
        <p>Pemerintah nagari berkomitmen menjadikan kegiatan penghijauan sebagai agenda rutin tahunan agar lingkungan tetap asri, sehat, dan memberikan manfaat bagi generasi mendatang.</p>
      `
    }
  ];

  // Render untuk beranda (kartu Bootstrap)
  window.renderIndexNews = function(selector){
    const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!el) return;
    el.innerHTML = window.KABA_POSTS.map(p => `
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <img src="${p.image}" class="card-img-top" alt="${p.title}">
          <div class="card-body">
            <h5 class="card-title">${p.title}</h5>
            <small class="text-muted d-block mb-2"><i class="bi bi-calendar3 me-1"></i>${p.date}</small>
            <p class="card-text">${p.excerpt}</p>
            <a href="kaba-detail.html?id=${p.id}" class="btn btn-success btn-sm">Baca Selengkapnya</a>
          </div>
        </div>
      </div>
    `).join('');
  };

  // Render untuk halaman Kaba Nagari (kartu kustom .news-card)
  window.renderKabaGrid = function(selector){
    const el = typeof selector === 'string' ? document.querySelector(selector) : selector;
    if (!el) return;
    el.innerHTML = window.KABA_POSTS.map(p => `
      <div class="news-card">
        <a href="kaba-detail.html?id=${p.id}" class="text-decoration-none text-reset">
          <div class="news-image">
            <img src="${p.image}" alt="${p.title}" onerror="this.src='image/wisata1.jpg'" />
          </div>
          <div class="news-content">
            <span class="news-category">${p.category}</span>
            <h4 class="news-title">${p.title}</h4>
            <div class="news-date">
              <i class="bi bi-calendar3"></i>
              <span>${p.date}</span>
            </div>
            <p class="news-excerpt">${p.excerpt}</p>
            <span class="read-more btn btn-success"><i class="bi bi-arrow-right me-1"></i> Baca Selengkapnya</span>
          </div>
        </a>
      </div>
    `).join('');
  };

  // Sinkronisasi Live News dari REST API Laravel jika tersedia
  if (typeof fetch !== 'undefined') {
    const fetchLiveNews = async () => {
      try {
        let liveNews = null;
        if (typeof window !== 'undefined' && window.KotoAuth && typeof window.KotoAuth.getKabaNagari === 'function') {
          const res = await window.KotoAuth.getKabaNagari();
          liveNews = res.data?.data || res.data || res.items;
        } else {
          let apiBase = window.API_BASE;
          if (!apiBase && typeof window !== 'undefined' && window.location) {
            const origin = window.location.origin;
            const pathname = window.location.pathname || '';
            const port = window.location.port || '';
            if (pathname.includes('/layanan-surat/public')) {
              apiBase = origin + '/layanan-surat/public/api';
            } else if (pathname.includes('/layanan-surat')) {
              apiBase = origin + '/layanan-surat/public/api';
            } else if (pathname.includes('/frontend/public')) {
              apiBase = 'http://localhost/layanan-surat/public/api';
            } else if (port === '8000') {
              apiBase = origin + '/api';
            } else if (['5500', '5501', '5502', '3000', '5173'].includes(port) || window.location.protocol === 'file:') {
              apiBase = 'http://localhost/layanan-surat/public/api';
            } else if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
              apiBase = origin + '/layanan-surat/public/api';
            } else {
              apiBase = origin + '/api';
            }
          }
          apiBase = apiBase || 'http://localhost/layanan-surat/public/api';
          let res = null;
          try {
            res = await fetch(`${apiBase}/kaba-nagari`, {
              headers: { 'Accept': 'application/json' }
            });
          } catch (err) {
            if (apiBase !== 'http://localhost/layanan-surat/public/api') {
              try {
                res = await fetch('http://localhost/layanan-surat/public/api/kaba-nagari', {
                  headers: { 'Accept': 'application/json' }
                });
              } catch (e2) {}
            }
          }
          if (res && res.ok) {
            const contentType = res.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
              const json = await res.json();
              liveNews = json.data?.data || json.data || json.items;
            }
          }
        }

        if (Array.isArray(liveNews) && liveNews.length > 0) {
          const formatted = liveNews.map(item => ({
            id: item.slug || item.id,
            title: item.judul || item.title,
            date: item.tanggal ? (new Date(item.tanggal).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})) : (item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : 'Terbaru'),
            category: item.kategori || 'Berita',
            image: item.gambar ? (item.gambar.startsWith('http') ? item.gambar : (item.gambar.startsWith('/') ? item.gambar : '/' + item.gambar)) : 'image/wisata1.jpg',
            excerpt: item.ringkasan || (item.isi ? item.isi.substring(0, 140) + '...' : ''),
            detail: item.isi || item.konten || ''
          }));
          window.KABA_POSTS = [...formatted, ...window.KABA_POSTS];
          if (document.querySelector('.news-grid')) {
            window.renderKabaGrid('.news-grid');
          }
        }
      } catch (e) {
        // Safe fallback - keep existing DEFAULT_POSTS
      }
    };
    fetchLiveNews();
  }
})();