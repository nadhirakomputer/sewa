-- Database Manajemen Sewa Barang (XAMPP / MySQL / MariaDB)
-- Cara pakai: phpMyAdmin > tab Import > pilih file ini > Go
CREATE DATABASE IF NOT EXISTS sewa_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sewa_barang;

DROP VIEW IF EXISTS v_sewa;
DROP TABLE IF EXISTS sewa;
DROP TABLE IF EXISTS pelanggan;
DROP TABLE IF EXISTS barang;

CREATE TABLE barang (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(30) NULL UNIQUE,
  nama VARCHAR(120) NOT NULL,
  jenis VARCHAR(60) NOT NULL,                -- bebas: Laptop, Printer, Proyektor, dll.
  tarif_per_hari INT UNSIGNED NOT NULL DEFAULT 0,
  denda_per_hari INT UNSIGNED NOT NULL DEFAULT 0,
  catatan VARCHAR(255) NOT NULL DEFAULT '',
  foto VARCHAR(60) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  telepon VARCHAR(30) NOT NULL DEFAULT '',
  no_identitas VARCHAR(50) NOT NULL DEFAULT '', -- NIK / NIS
  alamat VARCHAR(255) NOT NULL DEFAULT '',
  foto VARCHAR(60) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE sewa (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  barang_id INT UNSIGNED NOT NULL,
  pelanggan_id INT UNSIGNED NOT NULL,
  tgl_sewa DATE NOT NULL,
  tgl_jatuh_tempo DATE NOT NULL,             -- tanggal kembali yang dijanjikan
  tgl_kembali DATE NULL,                     -- NULL = belum dikembalikan
  tarif_per_hari INT UNSIGNED NOT NULL,      -- salinan tarif saat disewa
  denda_per_hari INT UNSIGNED NOT NULL,      -- salinan denda saat disewa
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sewa_barang FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE RESTRICT,
  CONSTRAINT fk_sewa_pelanggan FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
  CONSTRAINT ck_tgl CHECK (tgl_jatuh_tempo >= tgl_sewa),
  INDEX idx_aktif (barang_id, tgl_kembali)
) ENGINE=InnoDB;

-- Laporan: lama sewa, keterlambatan, denda, dan total dihitung otomatis
CREATE VIEW v_sewa AS
SELECT s.id, b.kode, b.nama AS barang, b.jenis, p.nama AS pelanggan, p.telepon,
  s.tgl_sewa, s.tgl_jatuh_tempo, s.tgl_kembali,
  GREATEST(1, DATEDIFF(s.tgl_jatuh_tempo, s.tgl_sewa)) AS hari_sewa,
  GREATEST(0, DATEDIFF(COALESCE(s.tgl_kembali, CURDATE()), s.tgl_jatuh_tempo)) AS hari_telat,
  GREATEST(1, DATEDIFF(s.tgl_jatuh_tempo, s.tgl_sewa)) * s.tarif_per_hari AS total_sewa,
  GREATEST(0, DATEDIFF(COALESCE(s.tgl_kembali, CURDATE()), s.tgl_jatuh_tempo)) * s.denda_per_hari AS total_denda,
  GREATEST(1, DATEDIFF(s.tgl_jatuh_tempo, s.tgl_sewa)) * s.tarif_per_hari
   + GREATEST(0, DATEDIFF(COALESCE(s.tgl_kembali, CURDATE()), s.tgl_jatuh_tempo)) * s.denda_per_hari AS total_bayar,
  CASE WHEN s.tgl_kembali IS NOT NULL THEN IF(s.tgl_kembali > s.tgl_jatuh_tempo,'Selesai, telat','Selesai')
       WHEN CURDATE() > s.tgl_jatuh_tempo THEN 'Terlambat' ELSE 'Berjalan' END AS status
FROM sewa s JOIN barang b ON b.id = s.barang_id JOIN pelanggan p ON p.id = s.pelanggan_id;

-- Contoh data (boleh dihapus lewat tombol "Hapus semua data" di aplikasi)
INSERT INTO barang (id,kode,nama,jenis,tarif_per_hari,denda_per_hari) VALUES
 (1,'LPT-01','Laptop Asus Vivobook','Laptop',50000,25000),
 (2,'LPT-02','Laptop Lenovo ThinkPad','Laptop',60000,30000),
 (3,'PRN-01','Printer Epson L3210','Printer',30000,15000),
 (4,'PRJ-01','Proyektor Epson','Proyektor',40000,20000);
INSERT INTO pelanggan (id,nama,telepon,no_identitas,alamat) VALUES
 (1,'Budi Santoso','081234567890','NIK 3516xxxxxxxxxxxx','Gondang, Mojokerto'),
 (2,'Siti Rahmawati','085711112222','NIS 2024001','Ngoro, Mojokerto');
INSERT INTO sewa (barang_id,pelanggan_id,tgl_sewa,tgl_jatuh_tempo,tgl_kembali,tarif_per_hari,denda_per_hari) VALUES
 (1,1,CURDATE()-INTERVAL 6 DAY,CURDATE()-INTERVAL 2 DAY,NULL,50000,25000),
 (3,2,CURDATE()-INTERVAL 1 DAY,CURDATE()+INTERVAL 3 DAY,NULL,30000,15000),
 (2,2,CURDATE()-INTERVAL 12 DAY,CURDATE()-INTERVAL 9 DAY,CURDATE()-INTERVAL 8 DAY,60000,30000);

-- Akun admin (username: admin)
USE sewa_barang;
CREATE TABLE IF NOT EXISTS admin (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,          -- disimpan terenkripsi (bcrypt), bukan teks biasa
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
INSERT INTO admin (username, password_hash) VALUES ('admin', '$2y$10$Ypyau1zqFBDlr7L11KOdpO.95UZfpEjO2acVix1ckyKwecL0j0XBa')
  ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash);
