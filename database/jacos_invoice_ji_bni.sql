-- SQL produksi: Jacos Invoice (JI) + metode bayar kwitansi
-- Aman dijalankan ulang (idempotent).

-- 1. Tabel metode pembayaran kwitansi
CREATE TABLE IF NOT EXISTS web_payment_method (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  is_show TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Seed opsi kwitansi (idempotent)
INSERT INTO web_payment_method (nama)
SELECT 'Cash'
WHERE NOT EXISTS (SELECT 1 FROM web_payment_method WHERE nama = 'Cash');

INSERT INTO web_payment_method (nama)
SELECT 'Transfer BNI/BCA'
WHERE NOT EXISTS (SELECT 1 FROM web_payment_method WHERE nama = 'Transfer BNI/BCA');

INSERT INTO web_payment_method (nama)
SELECT 'QRIS BNI/BCA'
WHERE NOT EXISTS (SELECT 1 FROM web_payment_method WHERE nama = 'QRIS BNI/BCA');

INSERT INTO web_payment_method (nama)
SELECT 'VA BNI'
WHERE NOT EXISTS (SELECT 1 FROM web_payment_method WHERE nama = 'VA BNI');

-- 3. Kolom penunjuk metode bayar di transaksi (nullable; diisi webhook VA BNI saat pelunasan,
--    atau manual via modul transaksi di admin panel)
ALTER TABLE transaksi ADD COLUMN payment_method_id INT NULL AFTER nama_bank;
EOF
echo "---"
