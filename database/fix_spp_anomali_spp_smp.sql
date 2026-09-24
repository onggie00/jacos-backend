-- ============================================================================
-- Rekonsiliasi anomali SPP "lunas palsu" lintas jenjang — TABEL: spp_smp
-- Scope: id_tahun_ajaran = 10 (TA berjalan) SAJA.
--        TA lama sengaja tidak disentuh (banyak diisi manual legacy, format menit).
-- Aman MySQL 5.7 (tanpa CTE, tanpa correlated NOT EXISTS per baris).
-- Jalankan bertahap via mysql client.
--
-- DRY RUN: jalankan bagian 1-3, lalu bagian 4 diakhiri ROLLBACK; review hasil.
-- FINAL  : jalankan ulang bagian 4 diakhiri COMMIT (setelah persetujuan).
-- Cek versi server dulu: SELECT VERSION();  (produksi terdeteksi < 8.0)
-- ============================================================================

-- ---------------------------------------------------------------------------
-- 0. Opsional (DDL, jalankan TERPISAH di jam sepi, butuh persetujuan):
-- CREATE INDEX idx_prbni_trx_id      ON payment_response_bni (trx_id);
-- CREATE INDEX idx_prbri_briva       ON payment_response_bri (briva_no, transaction_date);
-- CREATE INDEX idx_tspp_siswa_ajaran ON transaksi_spp (id_siswa_aktif, id_tahun_ajaran, status_transaksi);
-- ---------------------------------------------------------------------------

-- 1. BACKUP ------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS spp_smp_backup_anomali AS SELECT * FROM spp_smp;

-- 2. BANGUN BUKTI PEMBAYARAN (agregasi sekali-pass + join PK) -----------------
DROP TABLE IF EXISTS tmp_spp_paid_fix;
CREATE TABLE tmp_spp_paid_fix (
  jenjang varchar(8) NOT NULL,
  id_siswa_aktif int NOT NULL,
  id_tahun_ajaran int NOT NULL,
  PRIMARY KEY (jenjang, id_siswa_aktif, id_tahun_ajaran),
  juli datetime NULL, agustus datetime NULL, september datetime NULL,
  oktober datetime NULL, november datetime NULL, desember datetime NULL,
  januari datetime NULL, februari datetime NULL, maret datetime NULL,
  april datetime NULL, mei datetime NULL, juni datetime NULL
) ENGINE=InnoDB;

INSERT INTO tmp_spp_paid_fix
SELECT
  SUBSTRING_INDEX(SUBSTRING_INDEX(no_transaksi,'-',2),'-',-1) AS jenjang,
  id_siswa_aktif,
  id_tahun_ajaran,
  MAX(CASE WHEN LOWER(bulan)='juli'      OR FIND_IN_SET('juli',      LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS juli,
  MAX(CASE WHEN LOWER(bulan)='agustus'   OR FIND_IN_SET('agustus',   LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS agustus,
  MAX(CASE WHEN LOWER(bulan)='september' OR FIND_IN_SET('september', LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS september,
  MAX(CASE WHEN LOWER(bulan)='oktober'   OR FIND_IN_SET('oktober',   LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS oktober,
  MAX(CASE WHEN LOWER(bulan)='november'  OR FIND_IN_SET('november',  LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS november,
  MAX(CASE WHEN LOWER(bulan)='desember'  OR FIND_IN_SET('desember',  LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS desember,
  MAX(CASE WHEN LOWER(bulan)='januari'   OR FIND_IN_SET('januari',   LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS januari,
  MAX(CASE WHEN LOWER(bulan)='februari'  OR FIND_IN_SET('februari',  LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS februari,
  MAX(CASE WHEN LOWER(bulan)='maret'     OR FIND_IN_SET('maret',     LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS maret,
  MAX(CASE WHEN LOWER(bulan)='april'     OR FIND_IN_SET('april',     LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS april,
  MAX(CASE WHEN LOWER(bulan)='mei'       OR FIND_IN_SET('mei',       LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS mei,
  MAX(CASE WHEN LOWER(bulan)='juni'      OR FIND_IN_SET('juni',      LOWER(REPLACE(COALESCE(detail_bulan,''),' ',''))) THEN updated_at END) AS juni
FROM transaksi_spp
WHERE status_transaksi = 2
  AND id_siswa_aktif IS NOT NULL
  AND id_tahun_ajaran IS NOT NULL
GROUP BY jenjang, id_siswa_aktif, id_tahun_ajaran;

-- ---------------------------------------------------------------------------
-- 3. REVIEW (SELECT saja — tidak mengubah data) -------------------------------
-- 3a. Sel bulan yang akan berubah di TA 10 (palsu → NULL, atau dinormalkan
--     ke timestamp pembayaran). Kolom palsu_* = terisi tanpa pembayaran.
SELECT s.id, s.nama, s.kelas, s.id_tahun_ajaran,
  (s.juli      IS NOT NULL AND s.juli      NOT IN ('','-') AND p.juli      IS NULL) AS juli_palsu,
  (s.agustus   IS NOT NULL AND s.agustus   NOT IN ('','-') AND p.agustus   IS NULL) AS agustus_palsu,
  (s.september IS NOT NULL AND s.september NOT IN ('','-') AND p.september IS NULL) AS september_palsu,
  (s.oktober   IS NOT NULL AND s.oktober   NOT IN ('','-') AND p.oktober   IS NULL) AS oktober_palsu,
  (s.november  IS NOT NULL AND s.november  NOT IN ('','-') AND p.november  IS NULL) AS november_palsu,
  (s.desember  IS NOT NULL AND s.desember  NOT IN ('','-') AND p.desember  IS NULL) AS desember_palsu,
  (s.januari   IS NOT NULL AND s.januari   NOT IN ('','-') AND p.januari   IS NULL) AS januari_palsu,
  (s.februari  IS NOT NULL AND s.februari  NOT IN ('','-') AND p.februari  IS NULL) AS februari_palsu,
  (s.maret     IS NOT NULL AND s.maret     NOT IN ('','-') AND p.maret     IS NULL) AS maret_palsu,
  (s.april     IS NOT NULL AND s.april     NOT IN ('','-') AND p.april     IS NULL) AS april_palsu,
  (s.mei       IS NOT NULL AND s.mei       NOT IN ('','-') AND p.mei       IS NULL) AS mei_palsu,
  (s.juni      IS NOT NULL AND s.juni      NOT IN ('','-') AND p.juni      IS NULL) AS juni_palsu
FROM spp_smp s
LEFT JOIN tmp_spp_paid_fix p
  ON p.id_siswa_aktif = s.id_siswa_aktif
 AND p.id_tahun_ajaran = s.id_tahun_ajaran
 AND p.jenjang = 'SMP'
WHERE s.id_tahun_ajaran = 10
  AND ( (NOT (s.juli      = '-' OR (p.juli      <=> s.juli)))
     OR (NOT (s.agustus   = '-' OR (p.agustus   <=> s.agustus)))
     OR (NOT (s.september = '-' OR (p.september <=> s.september)))
     OR (NOT (s.oktober   = '-' OR (p.oktober   <=> s.oktober)))
     OR (NOT (s.november  = '-' OR (p.november  <=> s.november)))
     OR (NOT (s.desember  = '-' OR (p.desember  <=> s.desember)))
     OR (NOT (s.januari   = '-' OR (p.januari   <=> s.januari)))
     OR (NOT (s.februari  = '-' OR (p.februari  <=> s.februari)))
     OR (NOT (s.maret     = '-' OR (p.maret     <=> s.maret)))
     OR (NOT (s.april     = '-' OR (p.april     <=> s.april)))
     OR (NOT (s.mei       = '-' OR (p.mei       <=> s.mei)))
     OR (NOT (s.juni      = '-' OR (p.juni      <=> s.juni))) )
;

-- 3b. Transaksi lunas TA 10 TANPA bukti callback bank (REVIEW MANUAL,
--     TIDAK auto-demote). BNI: push per invoice (trx_id = kode_tagihan).
--     BRI: bayar grup multi-bulan satu VA → join longgar per VA + window 7 hari
--     (updated_at BRI sering ditulis cron berhari-hari setelah bayar);
--     cocok_nominal = 1 berarti ada push BRI dengan amount persis.
SELECT t.id_transaksi, t.no_transaksi, t.kode_tagihan, t.va_number, t.bulan,
       t.total_biaya, t.count_bill, t.updated_at, t.updated_from,
       (r2.id IS NOT NULL) AS cocok_nominal
FROM transaksi_spp t
LEFT JOIN payment_response_bni b
       ON b.trx_id = t.kode_tagihan
LEFT JOIN payment_response_bri r
       ON r.briva_no = t.va_number
      AND r.transaction_date >= DATE_FORMAT(t.updated_at - INTERVAL 7 DAY, '%Y%m%d%H%i%s')
      AND r.transaction_date <= DATE_FORMAT(t.updated_at + INTERVAL 7 DAY, '%Y%m%d%H%i%s')
LEFT JOIN payment_response_bri r2
       ON r2.briva_no = t.va_number
      AND r2.bill_amount = t.total_biaya * t.count_bill
      AND r2.transaction_date >= DATE_FORMAT(t.updated_at - INTERVAL 7 DAY, '%Y%m%d%H%i%s')
      AND r2.transaction_date <= DATE_FORMAT(t.updated_at + INTERVAL 7 DAY, '%Y%m%d%H%i%s')
WHERE t.status_transaksi = 2
  AND t.id_tahun_ajaran = 10
  AND t.no_transaksi LIKE 'LISPP-SMP-%'
  AND b.id IS NULL
  AND r.id IS NULL
ORDER BY t.updated_at
;

-- ---------------------------------------------------------------------------
-- 4. REBUILD (dalam transaksi; DRY RUN = ROLLBACK, FINAL = COMMIT) ------------
START TRANSACTION;

UPDATE spp_smp s
LEFT JOIN tmp_spp_paid_fix p
  ON p.id_siswa_aktif = s.id_siswa_aktif
 AND p.id_tahun_ajaran = s.id_tahun_ajaran
 AND p.jenjang = 'SMP'
SET
  s.juli      = CASE WHEN s.juli      = '-' THEN s.juli      ELSE p.juli      END,
  s.agustus   = CASE WHEN s.agustus   = '-' THEN s.agustus   ELSE p.agustus   END,
  s.september = CASE WHEN s.september = '-' THEN s.september ELSE p.september END,
  s.oktober   = CASE WHEN s.oktober   = '-' THEN s.oktober   ELSE p.oktober   END,
  s.november  = CASE WHEN s.november  = '-' THEN s.november  ELSE p.november  END,
  s.desember  = CASE WHEN s.desember  = '-' THEN s.desember  ELSE p.desember  END,
  s.januari   = CASE WHEN s.januari   = '-' THEN s.januari   ELSE p.januari   END,
  s.februari  = CASE WHEN s.februari  = '-' THEN s.februari  ELSE p.februari  END,
  s.maret     = CASE WHEN s.maret     = '-' THEN s.maret     ELSE p.maret     END,
  s.april     = CASE WHEN s.april     = '-' THEN s.april     ELSE p.april     END,
  s.mei       = CASE WHEN s.mei       = '-' THEN s.mei       ELSE p.mei       END,
  s.juni      = CASE WHEN s.juni      = '-' THEN s.juni      ELSE p.juni      END
WHERE s.id_tahun_ajaran = 10;

-- Verifikasi di dalam transaksi (harus 0 baris):
SELECT s.id, s.nama
FROM spp_smp s
LEFT JOIN tmp_spp_paid_fix p
  ON p.id_siswa_aktif = s.id_siswa_aktif
 AND p.id_tahun_ajaran = s.id_tahun_ajaran
 AND p.jenjang = 'SMP'
WHERE s.id_tahun_ajaran = 10
  AND ( (NOT (s.juli      = '-' OR (p.juli      <=> s.juli)))
     OR (NOT (s.agustus   = '-' OR (p.agustus   <=> s.agustus)))
     OR (NOT (s.september = '-' OR (p.september <=> s.september)))
     OR (NOT (s.oktober   = '-' OR (p.oktober   <=> s.oktober)))
     OR (NOT (s.november  = '-' OR (p.november  <=> s.november)))
     OR (NOT (s.desember  = '-' OR (p.desember  <=> s.desember)))
     OR (NOT (s.januari   = '-' OR (p.januari   <=> s.januari)))
     OR (NOT (s.februari  = '-' OR (p.februari  <=> s.februari)))
     OR (NOT (s.maret     = '-' OR (p.maret     <=> s.maret)))
     OR (NOT (s.april     = '-' OR (p.april     <=> s.april)))
     OR (NOT (s.mei       = '-' OR (p.mei       <=> s.mei)))
     OR (NOT (s.juni      = '-' OR (p.juni      <=> s.juni))) )
;

-- DRY RUN (lihat dulu hasilnya):          ROLLBACK;
-- FINAL (setelah hasil direview user):    COMMIT;

-- ---------------------------------------------------------------------------
-- 5. POST-CHECK (setelah COMMIT; harus 0 baris) — sama dgn query di bagian 4
--    (jalankan ulang query verifikasi di atas).

-- 6. CLEANUP (opsional, setelah yakin):
-- DROP TABLE IF EXISTS tmp_spp_paid_fix;
