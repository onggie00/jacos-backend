-- =====================================================================
-- Seed kredensial Moodle + BRI sandbox + device face recognition
-- ke tabel pengaturan_akun (pola: seed_bri_snap_setting.sql / opsi 4)
--
-- IDEMPOTENT: INSERT hanya bila name_setting belum ada.
-- VALUE DIISI MANUAL di server (jangan commit nilai asli ke repo).
--
-- Latar belakang: token Moodle (SMP/SMA) & BRI sandbox
-- clientId/clientSecret sebelumnya hardcoded di controller —
-- dipindah ke sini. Token Moodle lama (24eeaadd...) dead code,
-- tidak di-seed. Device face recognition belum ada → value kosong.
-- =====================================================================

-- ---------- Moodle (role: siswa; satu token per instance CBT) ----------
-- URL instance Moodle CBT per jenjang
INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_url_sd', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_url_sd');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_url_smp', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_url_smp');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_url_sma', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_url_sma');

-- Token webservice Moodle (role siswa) per instance
INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_token_sd', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_token_sd');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_token_smp', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_token_smp');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'moodle_token_sma', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'moodle_token_sma');

-- ---------- BRI SNAP sandbox (dipakai MY_Snap_bri + controller apiapp/bri/) ----------
INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'bri_sandbox_client_id', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'bri_sandbox_client_id');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'bri_sandbox_client_secret', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'bri_sandbox_client_secret');

-- ---------- Device face recognition (belum ada device → kosong) ----------
INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'face_device_username', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'face_device_username');

INSERT INTO pengaturan_akun (name_setting, value, updated_at)
SELECT 'face_device_password', '', NOW()
WHERE NOT EXISTS (SELECT 1 FROM pengaturan_akun WHERE name_setting = 'face_device_password');
