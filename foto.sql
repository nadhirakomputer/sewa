-- Tambah kolom foto. Pakai file ini jika database sudah pernah diimpor dan Anda ingin mempertahankan datanya.
-- (Jika Anda mengimpor ulang database.sql, file ini tidak perlu dijalankan.)
USE sewa_barang;
ALTER TABLE barang ADD COLUMN IF NOT EXISTS foto VARCHAR(60) NULL;
ALTER TABLE pelanggan ADD COLUMN IF NOT EXISTS foto VARCHAR(60) NULL;
