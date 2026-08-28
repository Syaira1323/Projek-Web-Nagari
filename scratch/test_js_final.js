    // 1. Data Konfigurasi 7 Jenis Surat & Syarat Upload

    const suratTypes = {

        'suket_usaha': {

            nama: 'Surat Keterangan Usaha (SKU)',

            judulResmi: 'SURAT KETERANGAN USAHA',

            keperluanHint: 'Contoh: Untuk keperluan pengajuan tambahan modal usaha KUR Bank BRI',

            extra1Label: 'Nama Usaha',

            extra2Label: 'Bidang Usaha',

            extra1Hint: 'Contoh: Kedai Kopi Tradisional / Bengkel Las',

            extra2Hint: 'Contoh: Kuliner / Jasa Perbaikan',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & Kartu Keluarga (KK)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' },

                { label: 'Foto Tempat / Aktivitas Usaha', wajib: true, folder: 'uploads/foto_usaha' }

            ]

        },

        'suket_domisili': {

            nama: 'Surat Keterangan Domisili (SKD)',

            judulResmi: 'SURAT KETERANGAN DOMISILI',

            keperluanHint: 'Contoh: Untuk kelengkapan administrasi domisili tempat tinggal dan perbankan',

            extra1Label: 'Lama Menetap',

            extra2Label: 'Status Tempat Tinggal',

            extra1Hint: 'Contoh: Sejak tahun 2018 / Sejak Lahir',

            extra2Hint: 'Contoh: Rumah Milik Sendiri / Sewa',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & Kartu Keluarga (KK)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' }

            ]

        },

        'suket_tidak_mampu': {

            nama: 'Surat Keterangan Tidak Mampu (SKTM)',

            judulResmi: 'SURAT KETERANGAN TIDAK MAMPU (SKTM)',

            keperluanHint: 'Contoh: Untuk permohonan beasiswa pendidikan KIP-Kuliah / Keringanan Biaya',

            extra1Label: 'Penghasilan Keluarga',

            extra2Label: 'Kondisi Ekonomi',

            extra1Hint: 'Contoh: Rp 800.000 / Bulan',

            extra2Hint: 'Contoh: Buruh Harian / Keluarga Pra-Sejahtera',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & Kartu Keluarga (KK)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Rekomendasi Tidak Mampu dari Wali Jorong', wajib: true, folder: 'uploads/pengantar_jorong' },

                { label: 'Foto Rumah Tampak Depan / KIS / BPJS / KIP', wajib: true, folder: 'uploads/foto_rumah' }

            ]

        },

        'surat_nikah_na': {

            nama: 'Surat Pengantar Nikah (Model NA)',

            judulResmi: 'SURAT PENGANTAR NIKAH (MODEL N-1 s.d N-4)',

            keperluanHint: 'Contoh: Untuk pendaftaran kehendak nikah di Kantor Urusan Agama (KUA)',

            extra1Label: 'Nama Calon Pasangan',

            extra2Label: 'Bin / Binti Pasangan',

            extra1Hint: 'Contoh: Nama Lengkap Calon Pasangan',

            extra2Hint: 'Contoh: Nama Ayah Kandung Pasangan',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & KK Calon Pengantin (Pria & Wanita)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Akta Kelahiran & Ijazah Terakhir', wajib: true, folder: 'uploads/akta_kelahiran' },

                { label: 'Fotokopi KTP Orang Tua / Wali Nikah', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' },

                { label: 'Pas Foto 2x3 & 4x6 Latar Biru (3 Lembar)', wajib: true, folder: 'uploads/pas_foto' }

            ]

        },

        'sket_penghasilan': {

            nama: 'Surat Keterangan Penghasilan (SKP)',

            judulResmi: 'SURAT KETERANGAN PENGHASILAN',

            keperluanHint: 'Contoh: Untuk persyaratan pengajuan beasiswa pendidikan anak',

            extra1Label: 'Rata-rata Penghasilan (Rp)',

            extra2Label: 'Sumber Penghasilan',

            extra1Hint: 'Contoh: Rp 1.500.000 / Bulan',

            extra2Hint: 'Contoh: Hasil Pertanian & Perkebunan',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & Kartu Keluarga (KK)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' }

            ]

        },

        'suket_meninggal': {

            nama: 'Surat Keterangan Meninggal (SKM)',

            judulResmi: 'SURAT KETERANGAN MENINGGAL DUNIA',

            keperluanHint: 'Contoh: Untuk pencatatan administrasi kependudukan dan klaim ahli waris',

            extra1Label: 'Nama Almarhum / Almarhumah',

            extra2Label: 'Penyebab / Lokasi Wafat',

            extra1Hint: 'Contoh: Nama Lengkap Almarhum/ah',

            extra2Hint: 'Contoh: Sakit Menua / Wafat di Rumah Duka',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & KK Almarhum / Almarhumah', wajib: true, folder: 'uploads/ktp' },

                { label: 'Fotokopi KTP & KK Pelapor (Ahli Waris)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' }

            ]

        },

        'suket_belum_menikah': {

            nama: 'Surat Keterangan Belum Menikah (SKBM)',

            judulResmi: 'SURAT KETERANGAN BELUM PERNAH MENIKAH',

            keperluanHint: 'Contoh: Untuk persyaratan administrasi melamar pekerjaan / pendaftaran instansi',

            extra1Label: 'Status Perkawinan',

            extra2Label: 'Keterangan Tambahan',

            extra1Hint: 'Contoh: Belum Pernah Menikah / Lajang',

            extra2Hint: 'Contoh: Warga Asli Nagari Koto Malintang',

            dokumenSyarat: [

                { label: 'Fotokopi KTP & Kartu Keluarga (KK)', wajib: true, folder: 'uploads/ktp' },

                { label: 'Surat Pengantar Wali Jorong Setempat', wajib: true, folder: 'uploads/pengantar_jorong' }

            ]

        }

    };



    // 2. Fungsi Utama Pemilihan Surat

    window.selectSurat = function(key, label) {

        if (!key || !suratTypes[key]) key = 'suket_usaha';



        // Update visual card active state

        document.querySelectorAll('.surat-card').forEach(function(el) {

            el.classList.remove('selected');

            el.style.borderColor = '#e5e7eb';

            el.style.backgroundColor = '#ffffff';

        });



        var activeCard = document.getElementById('card_' + key);

        if (activeCard) {

            activeCard.classList.add('selected');

            activeCard.style.borderColor = '#198754';

            activeCard.style.backgroundColor = '#f0fdf4';

        }



        // Update Badge & Hidden Type Value

        var labelElem = document.getElementById('labelSelectedSurat');

        if (labelElem) labelElem.innerText = suratTypes[key].nama;



        var formJenis = document.getElementById('form_jenis_surat');

        if (formJenis) formJenis.value = key;



        // Update Dynamic Form Fields

        var item = suratTypes[key];

        var fKeperluan = document.getElementById('f_keperluan');

        if (fKeperluan) {

            fKeperluan.placeholder = item.keperluanHint;

            fKeperluan.value = '';

        }



        var lExtra1 = document.getElementById('label_extra1');

        var lExtra2 = document.getElementById('label_extra2');

        var fExtra1 = document.getElementById('f_extra1');

        var fExtra2 = document.getElementById('f_extra2');



        if (lExtra1) lExtra1.innerText = item.extra1Label;

        if (lExtra2) lExtra2.innerText = item.extra2Label;

        if (fExtra1) {

            fExtra1.placeholder = item.extra1Hint;

            fExtra1.value = '';

        }

        if (fExtra2) {

            fExtra2.placeholder = item.extra2Hint;

            fExtra2.value = '';

        }



        // Render Dynamic Upload Requirements

        var docContainer = document.getElementById('dokumen_persyaratan_container');

        if (docContainer && item.dokumenSyarat) {

            docContainer.innerHTML = item.dokumenSyarat.map(function(doc, idx) {

                var wajibText = doc.wajib ? 'Wajib' : 'Opsional';

                var reqAttr = doc.wajib ? 'required' : '';

                return '<div class="col-md-6 mb-2">' +

                    '<label class="form-label small fw-semibold text-dark d-flex justify-content-between mb-1" style="font-size:0.8rem;">' +

                        '<span><i class="bi bi-file-earmark-arrow-up text-success me-1"></i>' + doc.label + '</span>' +

                        '<span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:0.68rem;">' + wajibText + '</span>' +

                    '</label>' +

                    '<input type="file" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf" ' + reqAttr + ' onchange="validateDocSize(this, 5)" data-folder="' + doc.folder + '" />' +

                    '<div class="file-size-feedback text-muted mt-1" style="font-size:0.72rem;">Format: JPG, PNG, PDF (Maks 5MB)</div>' +

                '</div>';

            }).join('');

        }

    };



    // 3. Validasi Ukuran File Upload (5MB)

    window.validateDocSize = function(input, maxMb) {

        if (!input.files || !input.files[0]) return;

        var file = input.files[0];

        var maxBytes = maxMb * 1024 * 1024;

        var feedback = input.nextElementSibling;



        if (file.size > maxBytes) {

            var actualMb = (file.size / (1024 * 1024)).toFixed(2);

            alert('⚠️ Perhatian:\nUkuran file "' + file.name + '" adalah ' + actualMb + ' MB, melebihi batas maksimal ' + maxMb + ' MB.\n\nSilakan pilih file dokumen dengan ukuran maksimal ' + maxMb + ' MB.');

            input.value = '';

            if (feedback) feedback.innerHTML = '<span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>File ditolak (Ukuran > ' + maxMb + 'MB)</span>';

        } else {

            var sizeKb = (file.size / 1024).toFixed(0);

            var sizeDisplay = file.size > 1024 * 1024 ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' : sizeKb + ' KB';

            if (feedback) feedback.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + file.name + ' (' + sizeDisplay + ')</span>';

        }

    };



    // 4. Fitur AI Asisten Surat (DistilBERT Simulation)

    window.askAI = function() {

        var input = document.getElementById('aiInput');

        if (!input) return;

        var text = input.value.trim();

        if (!text) return;



        var chatBody = document.getElementById('aiChatBody');

        var userBubble = document.createElement('div');

        userBubble.className = 'user-bubble';

        userBubble.innerText = text;

        chatBody.appendChild(userBubble);

        input.value = '';

        chatBody.scrollTop = chatBody.scrollHeight;



        var lower = text.toLowerCase();

        var matchedKey = 'suket_domisili';

        var aiResponse = '';



        if (lower.includes('usaha') || lower.includes('jual') || lower.includes('warung') || lower.includes('kur') || lower.includes('modal') || lower.includes('bank') || lower.includes('sku') || lower.includes('dagang')) {

            matchedKey = 'suket_usaha';

            aiResponse = 'Baik Sanak! Berdasarkan keterangan usaha, saya telah memilihkan <strong>Surat Keterangan Usaha (SKU)</strong>. Formulir dan persyaratan upload berkas di sebelah kanan sudah disesuaikan.';

        } else if (lower.includes('mati') || lower.includes('meninggal') || lower.includes('wafat') || lower.includes('kematian') || lower.includes('skm') || lower.includes('almarhum')) {

            matchedKey = 'suket_meninggal';

            aiResponse = 'Innalillahi wa inna ilaihi rajiun. Saya telah memilihkan <strong>Surat Keterangan Meninggal (SKM)</strong> untuk pengurusan administrasi ahli waris.';

        } else if (lower.includes('lajang') || lower.includes('belum nikah') || lower.includes('belum menikah') || lower.includes('single') || lower.includes('skbm')) {

            matchedKey = 'suket_belum_menikah';

            aiResponse = 'Siap Sanak! Saya telah memilihkan <strong>Surat Keterangan Belum Menikah (SKBM)</strong> untuk kelengkapan administrasi instansi/pekerjaan.';

        } else if (lower.includes('nikah') || lower.includes('kawin') || lower.includes('kua') || lower.includes('pengantin') || lower.includes('na') || lower.includes('catin')) {

            matchedKey = 'surat_nikah_na';

            aiResponse = 'Selamat Sanak! Saya telah memilihkan <strong>Surat Pengantar Nikah (Model NA)</strong> untuk pendaftaran ke KUA Tanjung Raya.';

        } else if (lower.includes('gaji') || lower.includes('penghasilan') || lower.includes('pendapatan') || lower.includes('slip') || lower.includes('skp')) {

            matchedKey = 'sket_penghasilan';

            aiResponse = 'Baik Sanak! Saya telah memilihkan <strong>Surat Keterangan Penghasilan (SKP)</strong> untuk pengajuan beasiswa / bantuan.';

        } else if (lower.includes('tidak mampu') || lower.includes('sktm') || lower.includes('miskin') || lower.includes('bantuan') || lower.includes('kip') || lower.includes('keringanan')) {

            matchedKey = 'suket_tidak_mampu';

            aiResponse = 'Siap Sanak! Saya telah memilihkan <strong>Surat Keterangan Tidak Mampu (SKTM)</strong> untuk persyaratan beasiswa atau keringanan biaya.';

        } else {

            matchedKey = 'suket_domisili';

            aiResponse = 'Baik Sanak! Berdasarkan kebutuhan Anda, saya telah memilihkan <strong>Surat Keterangan Domisili (SKD)</strong>.';

        }



        setTimeout(function() {

            var aiBubble = document.createElement('div');

            aiBubble.className = 'ai-bubble';

            aiBubble.innerHTML = aiResponse;

            chatBody.appendChild(aiBubble);

            chatBody.scrollTop = chatBody.scrollHeight;

            window.selectSurat(matchedKey, suratTypes[matchedKey].nama);

        }, 250);

    };



    window.triggerQuickAI = function(queryText) {

        var input = document.getElementById('aiInput');

        if (input) {

            input.value = queryText;

            window.askAI();

        }

    };



    // 5. Modal Pratinjau Surat Resmi

    window.showPreviewModal = function() {

        var jenis = document.getElementById('form_jenis_surat').value;

        var conf = suratTypes[jenis] || suratTypes['suket_usaha'];

        var nama = document.getElementById('f_nama').value || '[Nama Pemohon]';

        var nik = document.getElementById('f_nik').value || '[NIK Pemohon]';

        var tempat = document.getElementById('f_tempat_lahir').value || '[Tempat Lahir]';

        var tgl = document.getElementById('f_tanggal_lahir').value || '[Tanggal Lahir]';

        var jk = document.getElementById('f_jk').value || '[Jenis Kelamin]';

        var pekerjaan = document.getElementById('f_pekerjaan').value || '[Pekerjaan]';

        var alamat = document.getElementById('f_alamat').value || '[Alamat Lengkap]';

        var keperluan = document.getElementById('f_keperluan').value || '[Keperluan Surat]';

        var extra1 = document.getElementById('f_extra1')?.value || '-';

        var extra2 = document.getElementById('f_extra2')?.value || '-';



        document.getElementById('pv_judul').innerText = conf.judulResmi;

        document.getElementById('pv_isi').innerHTML = 

            '<p>Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, menerangkan bahwa:</p>' +

            '<table style="width:100%;margin-bottom:12px;">' +

                '<tr><td style="width:180px;">Nama Lengkap</td><td>: <strong>' + nama + '</strong></td></tr>' +

                '<tr><td>NIK KTP</td><td>: ' + nik + '</td></tr>' +

                '<tr><td>Tempat / Tgl Lahir</td><td>: ' + tempat + ', ' + tgl + '</td></tr>' +

                '<tr><td>Jenis Kelamin</td><td>: ' + jk + '</td></tr>' +

                '<tr><td>Pekerjaan</td><td>: ' + pekerjaan + '</td></tr>' +

                '<tr><td>Alamat</td><td>: ' + alamat + '</td></tr>' +

                '<tr><td>' + conf.extra1Label + '</td><td>: <strong>' + extra1 + '</strong></td></tr>' +

                '<tr><td>' + conf.extra2Label + '</td><td>: ' + extra2 + '</td></tr>' +

            '</table>' +

            '<p>Orang tersebut di atas adalah benar penduduk yang berdomisili di Nagari Koto Malintang dan surat ini diberikan untuk keperluan: <strong>' + keperluan + '</strong>.</p>' +

            '<p>Demikian surat keterangan ini kami perbuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.</p>';



        var modal = new bootstrap.Modal(document.getElementById('modalPreviewSurat'));

        modal.show();

    };



    // 6. Pengiriman Formulir Surat ke Storage Antrean

    window.handleWargaFormSubmit = function(e) {

        e.preventDefault();

        var jenis = document.getElementById('form_jenis_surat').value;

        var conf = suratTypes[jenis] || suratTypes['suket_usaha'];

        var nama = document.getElementById('f_nama').value.trim();

        var nik = document.getElementById('f_nik').value.trim();

        var tempat = document.getElementById('f_tempat_lahir').value.trim();

        var tgl = document.getElementById('f_tanggal_lahir').value;

        var jk = document.getElementById('f_jk').value;

        var pekerjaan = document.getElementById('f_pekerjaan').value.trim();

        var alamat = document.getElementById('f_alamat').value.trim();

        var keperluan = document.getElementById('f_keperluan').value.trim();

        var extra1 = document.getElementById('f_extra1')?.value || '';

        var extra2 = document.getElementById('f_extra2')?.value || '';



        var berkasLampiran = [];

        var fileInputs = document.querySelectorAll('#dokumen_persyaratan_container input[type="file"]');

        var timestamp = Date.now();



        fileInputs.forEach(function(input, index) {

            var labelElem = input.parentElement.querySelector('label span');

            var docLabel = labelElem ? labelElem.innerText.trim() : ('Dokumen ' + (index + 1));

            var fileName = input.files && input.files[0] ? input.files[0].name : ('dokumen_' + (index + 1) + '.pdf');

            var ext = fileName.split('.').pop() || 'pdf';

            var folder = input.getAttribute('data-folder') || 'uploads/ktp';

            var prefix = folder.replace('uploads/', '');



            var storagePath = folder + '/' + prefix + '_' + nik + '_' + timestamp + '.' + ext;

            berkasLampiran.push({

                label: docLabel,

                folder: folder,

                namaFile: fileName,

                path: storagePath,

                uploadedAt: new Date().toISOString()

            });

        });



        var newSurat = {

            id: 'srt-' + timestamp,

            jenisSurat: conf.nama,

            pengirim: nama,

            nik: nik,

            tempatLahir: tempat,

            tanggalLahir: tgl,

            jk: jk,

            pekerjaan: pekerjaan,

            alamat: alamat,

            subjek: keperluan,

            nomorSurat: '',

            isi: 'Yang bertanda tangan di bawah ini, Wali Nagari Koto Malintang, Kecamatan Tanjung Raya, Kabupaten Agam, menerangkan bahwa:\n\n' +

                 'Nama Lengkap: ' + nama + '\n' +

                 'NIK: ' + nik + '\n' +

                 'Tempat / Tgl Lahir: ' + tempat + ', ' + tgl + '\n' +

                 'Jenis Kelamin: ' + jk + '\n' +

                 'Pekerjaan: ' + pekerjaan + '\n' +

                 'Alamat: ' + alamat + '\n' +

                 conf.extra1Label + ': ' + extra1 + '\n' +

                 conf.extra2Label + ': ' + extra2 + '\n\n' +

                 'Surat keterangan ini diberikan untuk keperluan: ' + keperluan + '.\n' +

                 'Demikian surat keterangan ini kami perbuat dengan sebenarnya agar dapat dipergunakan sebagaimana mestinya.',

            tanggal: new Date().toISOString(),

            status: 'menunggu_admin',

            alasanTolak: '',

            stempel: false,

            ttdWali: false,

            berkasLampiran: berkasLampiran

        };



        if (typeof KotoAuth !== 'undefined') {

            var list = KotoAuth.getSuratMasuk();

            list.unshift(newSurat);

            KotoAuth.saveSuratMasuk(list);

        }



        var folderListStr = berkasLampiran.map(function(b) { return '📁 ' + b.folder + '/' + b.namaFile; }).join('\n');

        alert('✅ Permohonan Surat Berhasil Diajukan!\n\nDokumen persyaratan Anda telah tersimpan rapi per kategori folder:\n' + folderListStr + '\n\nSurat Anda kini masuk antrean verifikasi Admin Nagari Koto Malintang.');

        e.target.reset();

        window.selectSurat('suket_usaha', 'Surat Keterangan Usaha (SKU)');

    };



    // 7. Event Binding saat Halaman Dimuat

    document.addEventListener('DOMContentLoaded', function() {

        // Inisialisasi surat pertama (SKU)

        window.selectSurat('suket_usaha', 'Surat Keterangan Usaha (SKU)');



        // Pasang Event Listener langsung pada kartu 7 surat

        document.querySelectorAll('.surat-card').forEach(function(card) {

            card.addEventListener('click', function() {

                var id = this.id.replace('card_', '');

                var name = this.querySelector('h6')?.innerText || 'Surat';

                window.selectSurat(id, name);

            });

        });



        // Event Listener untuk Tombol Kirim AI dan Enter

        var sendBtn = document.getElementById('btnSendAI');

        if (sendBtn) {

            sendBtn.addEventListener('click', window.askAI);

        }



        var aiInp = document.getElementById('aiInput');

        if (aiInp) {

            aiInp.addEventListener('keydown', function(e) {

                if (e.key === 'Enter') {

                    e.preventDefault();

                    window.askAI();

                }

            });

        }

    });
    