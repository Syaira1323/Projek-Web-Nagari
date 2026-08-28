USE nagari_koto_malintang;

CREATE TABLE IF NOT EXISTS profil_nagaris (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nama_nagari VARCHAR(150) NOT NULL,
    kecamatan VARCHAR(150) NOT NULL,
    kabupaten VARCHAR(150) NOT NULL,
    provinsi VARCHAR(150) NOT NULL,

    deskripsi TEXT NULL,

    jumlah_penduduk INT UNSIGNED DEFAULT 0,
    jumlah_jorong INT UNSIGNED DEFAULT 0,
    luas_wilayah DECIMAL(10,2) DEFAULT 0.00,

    alamat TEXT NULL,
    kode_pos VARCHAR(10) NULL,
    email VARCHAR(150) NULL,
    telepon VARCHAR(30) NULL,
    website VARCHAR(255) NULL,

    logo VARCHAR(255) NULL,
    foto_utama VARCHAR(255) NULL,

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);perangkat_nagaris profil_nagaris