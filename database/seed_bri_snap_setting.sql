-- ============================================================
-- Jacos — seed kredensial BRI SNAP ke tabel pengaturan_akun
-- Dipakai oleh controller:
--   apiapp/bri/Snap_token_request.php
--   apiapp/Bri_snap_notification.php
--   apiapp/Bri_snap_notify_payment_intrabank.php
--   apiapp/Bri_snap_token_get.php
--   apiapp/Snap_bri_test.php
--
-- ISI VALUE MANUAL (kredensial sandbox BRI milik Jacos) — jangan commit nilai asli.
-- Jalankan manual di DB jacos_db. Cek dulu jangan sampai dobel insert:
--   SELECT * FROM pengaturan_akun WHERE name_setting LIKE 'bri_snap_%';
-- ============================================================

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
VALUES
  ('bri_snap_client_id',  'ISI_MANUAL_clientId_sandbox_BRI_Jacos', NOW()),
  ('bri_snap_client_secret', 'ISI_MANUAL_clientSecret_sandbox_BRI_Jacos', NOW());
