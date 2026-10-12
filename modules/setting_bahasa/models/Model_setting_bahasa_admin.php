<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model modul admin "Pengaturan Bahasa" (fase 6).
 *
 * Semua query memakai helper translator_safe_query (pola _safe_query:
 * db_debug dimatikan sementara, error dicatat, fallback dikembalikan) agar
 * halaman admin menampilkan pesan ramah bila tabel tidak ada — bukan error
 * page CI3. Semua parameter memakai binding.
 */
class Model_setting_bahasa_admin extends CI_Model {

    /** hasil deteksi kolom audit (sekali per request) */
    protected $audit_cols;

    public function __construct()
    {
        parent::__construct();
        $CI =& get_instance();
        $CI->load->helper('translator_safe_db');
    }

    /**
     * Deteksi kolom created_by/updated_by (sekali per request). return array
     * kolom yang ADA (bisa kosong — modul tetap jalan normal). $force utk
     * pengujian (null = deteksi otomatis, array() = paksa tanpa kolom).
     */
    public function audit_cols($force = null)
    {
        if ($force !== null) {
            $this->audit_cols = $force;
        } elseif ($this->audit_cols === null) {
            $r = translator_safe_query(
                "SELECT COLUMN_NAME FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'translation_override'
                   AND COLUMN_NAME IN ('created_by','updated_by')",
                array(), 'result', false);
            $cols = array();
            if (is_array($r)) {
                foreach ($r as $row) $cols[] = $row->COLUMN_NAME;
            }
            $this->audit_cols = $cols;
        }
        return $this->audit_cols;
    }

    /* ===================== B1: setting_bahasa ===================== */

    /**
     * Daftar setting bahasa user + nama user (lookup per role ke tabel akun,
     * email ber-index; lookup gagal => '-' tanpa error).
     */
    public function daftar_setting($filter, $limit, $offset)
    {
        $where  = array('1=1');
        $binds  = array();
        if (!empty($filter['role'])) {
            $where[] = 'sb.role = ?'; $binds[] = $filter['role'];
        }
        if (!empty($filter['kode'])) {
            $where[] = 'sb.kode_bahasa = ?'; $binds[] = $filter['kode'];
        }
        if (!empty($filter['jenjang'])) {
            $where[] = 'sb.jenjang = ?'; $binds[] = $filter['jenjang'];
        }
        if (!empty($filter['q'])) {
            $where[] = 'sb.email_ms_office LIKE ?'; $binds[] = '%' . $filter['q'] . '%';
        }
        $binds[] = (int) $limit; $binds[] = (int) $offset;

        $sql = "SELECT sb.email_ms_office, sb.role, sb.jenjang, sb.kode_bahasa, sb.updated_at,
                   COALESCE(u.nama, '-') AS nama_user
                FROM setting_bahasa sb
                LEFT JOIN (
                    SELECT 'siswa' AS role, email_ms_office AS email, nama_lengkap AS nama FROM siswa_sd WHERE deleted_at IS NULL
                    UNION ALL SELECT 'siswa', email_ms_office, nama_lengkap FROM siswa_smp WHERE deleted_at IS NULL
                    UNION ALL SELECT 'siswa', email_ms_office, nama_lengkap FROM siswa_sma WHERE deleted_at IS NULL
                    UNION ALL SELECT 'siswa', email_ms_office, nama_lengkap FROM siswa_ft WHERE deleted_at IS NULL
                    UNION ALL SELECT 'guru', email_ms_office, nama_lengkap FROM guru_sd WHERE deleted_at IS NULL
                    UNION ALL SELECT 'guru', email_ms_office, nama_lengkap FROM guru_smp WHERE deleted_at IS NULL
                    UNION ALL SELECT 'guru', email_ms_office, nama_lengkap FROM guru_sma WHERE deleted_at IS NULL
                    UNION ALL SELECT 'guru', email_ms_office, nama_lengkap FROM guru_ft WHERE deleted_at IS NULL
                    UNION ALL SELECT 'pimpinan', email_ms_office, nama_lengkap FROM pimpinan_sd WHERE deleted_at IS NULL
                    UNION ALL SELECT 'pimpinan', email_ms_office, nama_lengkap FROM pimpinan_smp WHERE deleted_at IS NULL
                    UNION ALL SELECT 'pimpinan', email_ms_office, nama_lengkap FROM pimpinan_sma WHERE deleted_at IS NULL
                    UNION ALL SELECT 'pegawai', email_ms_office, nama_lengkap FROM pegawai WHERE deleted_at IS NULL
                    UNION ALL SELECT 'ortu', email_ms_office_ortu, COALESCE(nama_ayah, nama_ibu, '-') FROM siswa_sd WHERE email_ms_office_ortu <> '' AND deleted_at_ortu IS NULL
                    UNION ALL SELECT 'ortu', email_ms_office_ortu, COALESCE(nama_ayah, nama_ibu, '-') FROM siswa_smp WHERE email_ms_office_ortu <> '' AND deleted_at_ortu IS NULL
                    UNION ALL SELECT 'ortu', email_ms_office_ortu, COALESCE(nama_ayah, nama_ibu, '-') FROM siswa_sma WHERE email_ms_office_ortu <> '' AND deleted_at_ortu IS NULL
                    UNION ALL SELECT 'ortu', email_ms_office_ortu, COALESCE(nama_ayah, nama_ibu, '-') FROM siswa_ft WHERE email_ms_office_ortu <> '' AND deleted_at_ortu IS NULL
                ) u ON CONVERT(u.role USING utf8mb4) COLLATE utf8mb4_general_ci = sb.role AND CONVERT(u.email USING utf8mb4) COLLATE utf8mb4_general_ci = sb.email_ms_office
                WHERE " . implode(' AND ', $where) . "
                ORDER BY sb.updated_at DESC
                LIMIT ? OFFSET ?";
        return translator_safe_query($sql, $binds, 'result', false);
    }

    public function hitung_setting($filter)
    {
        $where = array('1=1'); $binds = array();
        if (!empty($filter['role'])) { $where[] = 'role = ?'; $binds[] = $filter['role']; }
        if (!empty($filter['kode'])) { $where[] = 'kode_bahasa = ?'; $binds[] = $filter['kode']; }
        if (!empty($filter['jenjang'])) { $where[] = 'jenjang = ?'; $binds[] = $filter['jenjang']; }
        if (!empty($filter['q'])) { $where[] = 'email_ms_office LIKE ?'; $binds[] = '%' . $filter['q'] . '%'; }
        $r = translator_safe_query(
            "SELECT COUNT(*) AS c FROM setting_bahasa WHERE " . implode(' AND ', $where),
            $binds, 'row', false);
        return ($r === false || $r === null) ? false : (int) $r->c;
    }

    /** Ringkasan: jumlah user per kode_bahasa dan per role. */
    public function ringkasan()
    {
        $per_bahasa = translator_safe_query(
            "SELECT kode_bahasa, COUNT(*) AS jumlah FROM setting_bahasa GROUP BY kode_bahasa ORDER BY jumlah DESC",
            array(), 'result', false);
        $per_role = translator_safe_query(
            "SELECT role, COUNT(*) AS jumlah FROM setting_bahasa GROUP BY role ORDER BY jumlah DESC",
            array(), 'result', false);
        if ($per_bahasa === false || $per_role === false) {
            return false;
        }
        return array('per_bahasa' => $per_bahasa, 'per_role' => $per_role);
    }

    /* ===================== B2: override ===================== */

    public function daftar_override($filter, $limit, $offset)
    {
        $where = array('1=1'); $binds = array();
        if (isset($filter['kode']) && $filter['kode'] !== '') {
            $where[] = 'kode_bahasa = ?'; $binds[] = $filter['kode'];
        }
        if (isset($filter['aktif']) && $filter['aktif'] !== '') {
            $where[] = 'aktif = ?'; $binds[] = (int) $filter['aktif'];
        }
        if (!empty($filter['q'])) {
            $where[] = '(teks_asal LIKE ? OR teks_hasil LIKE ?)';
            $binds[] = '%' . $filter['q'] . '%'; $binds[] = '%' . $filter['q'] . '%';
        }
        $binds[] = (int) $limit; $binds[] = (int) $offset;
        return translator_safe_query(
            "SELECT id, hash_sumber, kode_bahasa, teks_asal, teks_hasil, aktif, catatan, created_at, updated_at" . $this->select_audit() . "
             FROM translation_override WHERE " . implode(' AND ', $where) . "
             ORDER BY id DESC LIMIT ? OFFSET ?",
            $binds, 'result', false);
    }

    public function hitung_override($filter)
    {
        $where = array('1=1'); $binds = array();
        if (isset($filter['kode']) && $filter['kode'] !== '') { $where[] = 'kode_bahasa = ?'; $binds[] = $filter['kode']; }
        if (isset($filter['aktif']) && $filter['aktif'] !== '') { $where[] = 'aktif = ?'; $binds[] = (int) $filter['aktif']; }
        if (!empty($filter['q'])) {
            $where[] = '(teks_asal LIKE ? OR teks_hasil LIKE ?)';
            $binds[] = '%' . $filter['q'] . '%'; $binds[] = '%' . $filter['q'] . '%';
        }
        $r = translator_safe_query("SELECT COUNT(*) AS c FROM translation_override WHERE " . implode(' AND ', $where), $binds, 'row', false);
        return ($r === false || $r === null) ? false : (int) $r->c;
    }

    public function ambil_override($id)
    {
        return translator_safe_query(
            "SELECT * FROM translation_override WHERE id = ? LIMIT 1",
            array((int) $id), 'row', null);
    }

    /** Cek duplikat hash+kode (kembalikan id bila ada). */
    public function cari_override_hash($hash, $kode_bahasa)
    {
        $r = translator_safe_query(
            "SELECT id FROM translation_override WHERE hash_sumber = ? AND kode_bahasa = ? LIMIT 1",
            array($hash, $kode_bahasa), 'row', null);
        return !empty($r) ? (int) $r->id : null;
    }

    /** kolom SELECT tambahan utk audit (dipakai daftar_override) */
    public function select_audit()
    {
        $kolom = $this->audit_cols();
        $out = '';
        if (in_array('created_by', $kolom, true)) $out .= ', created_by';
        if (in_array('updated_by', $kolom, true)) $out .= ', updated_by';
        return $out;
    }

    public function simpan_override($data, $user_id = null)
    {
        $kolom_audit = $this->audit_cols();
        $sql = "INSERT INTO translation_override (hash_sumber, kode_bahasa, teks_asal, teks_hasil, aktif, catatan, created_at";
        $val = "(?, ?, ?, ?, ?, ?, NOW()";
        $binds = array($data['hash_sumber'], $data['kode_bahasa'], $data['teks_asal'], $data['teks_hasil'], (int) $data['aktif'], $data['catatan']);
        if (in_array('created_by', $kolom_audit, true) && $user_id !== null) {
            $sql .= ', created_by'; $val .= ', ?'; $binds[] = (int) $user_id;
        }
        $sql .= ') VALUES ' . $val . ')';
        return translator_safe_query($sql, $binds, 'write', false);
    }

    public function ubah_override($id, $data, $user_id = null)
    {
        $kolom_audit = $this->audit_cols();
        $sql = "UPDATE translation_override SET kode_bahasa = ?, teks_asal = ?, teks_hasil = ?, aktif = ?, catatan = ?, updated_at = NOW()";
        $binds = array($data['kode_bahasa'], $data['teks_asal'], $data['teks_hasil'], (int) $data['aktif'], $data['catatan']);
        if (in_array('updated_by', $kolom_audit, true) && $user_id !== null) {
            $sql .= ', updated_by = ?'; $binds[] = (int) $user_id;
        }
        $sql .= ' WHERE id = ?'; $binds[] = (int) $id;
        return translator_safe_query($sql, $binds, 'write', false);
    }

    public function toggle_override($id, $aktif)
    {
        return translator_safe_query(
            "UPDATE translation_override SET aktif = ?, updated_at = NOW() WHERE id = ?",
            array((int) $aktif, (int) $id), 'write', false);
    }

    public function hapus_override($id)
    {
        return translator_safe_query(
            "DELETE FROM translation_override WHERE id = ?",
            array((int) $id), 'write', false);
    }

    /* ===================== B3: cache ===================== */

    public function daftar_cache($filter, $limit, $offset)
    {
        $where = array('1=1'); $binds = array();
        if (!empty($filter['kode'])) { $where[] = 'kode_bahasa = ?'; $binds[] = $filter['kode']; }
        if (!empty($filter['q']) && strlen($filter['q']) >= 3) {
            $where[] = 'teks_asal LIKE ?'; $binds[] = $filter['q'] . '%';
        }
        $order = 'created_at DESC';
        if (!empty($filter['urut'])) {
            if ($filter['urut'] === 'hit') $order = 'hit_count DESC';
            elseif ($filter['urut'] === 'last_used') $order = 'last_used_at DESC';
        }
        $binds[] = (int) $limit; $binds[] = (int) $offset;
        return translator_safe_query(
            "SELECT id_translation_cache, hash_sumber, kode_bahasa, teks_asal, teks_hasil, hit_count, last_used_at, created_at
             FROM translation_cache WHERE " . implode(' AND ', $where) . "
             ORDER BY {$order} LIMIT ? OFFSET ?",
            $binds, 'result', false);
    }

    public function hitung_cache($filter)
    {
        $where = array('1=1'); $binds = array();
        if (!empty($filter['kode'])) { $where[] = 'kode_bahasa = ?'; $binds[] = $filter['kode']; }
        if (!empty($filter['q']) && strlen($filter['q']) >= 3) { $where[] = 'teks_asal LIKE ?'; $binds[] = $filter['q'] . '%'; }
        $r = translator_safe_query("SELECT COUNT(*) AS c FROM translation_cache WHERE " . implode(' AND ', $where), $binds, 'row', false);
        return ($r === false || $r === null) ? false : (int) $r->c;
    }

    public function ambil_cache($id)
    {
        return translator_safe_query(
            "SELECT * FROM translation_cache WHERE id_translation_cache = ? LIMIT 1",
            array((int) $id), 'row', null);
    }

    /* ===================== B4: statistik ===================== */

    /** Perkiraan jumlah baris & ukuran tabel dari information_schema (murah). */
    public function info_tabel($tabel)
    {
        $r = translator_safe_query(
            "SELECT TABLE_ROWS AS rows_est, (DATA_LENGTH + INDEX_LENGTH) AS bytes
             FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            array($tabel), 'row', false);
        return $r;
    }

    /** Cache per kode_bahasa: jumlah + total hit. */
    public function cache_per_kode()
    {
        return translator_safe_query(
            "SELECT kode_bahasa, COUNT(*) AS jumlah, COALESCE(SUM(hit_count),0) AS total_hit
             FROM translation_cache GROUP BY kode_bahasa",
            array(), 'result', false);
    }

    /** 20 entri cache terpopuler. */
    public function cache_populer($limit = 20)
    {
        return translator_safe_query(
            "SELECT kode_bahasa, teks_asal, teks_hasil, hit_count, last_used_at FROM translation_cache
             ORDER BY hit_count DESC LIMIT " . (int) $limit,
            array(), 'result', false);
    }

    /** Jumlah entri tidak terpakai > 90 hari (created_at < NOW()-90d AND last_used_at NULL/old). */
    public function cache_tak_terpakai_90_hari()
    {
        $r = translator_safe_query(
            "SELECT COUNT(*) AS c FROM translation_cache
             WHERE created_at < NOW() - INTERVAL 90 DAY
               AND (last_used_at IS NULL OR last_used_at < NOW() - INTERVAL 90 DAY)",
            array(), 'row', false);
        return ($r === false || $r === null) ? false : (int) $r->c;
    }

    /** Statistik kuota per bulan (6 bulan terakhir, termasuk berjalan). */
    public function usage_6_bulan()
    {
        return translator_safe_query(
            "SELECT bulan, penyedia, jumlah_request, jumlah_karakter, jumlah_string
             FROM translation_usage_bulanan
             WHERE bulan >= DATE_FORMAT(NOW() - INTERVAL 6 MONTH, '%Y-%m')
             ORDER BY bulan DESC",
            array(), 'result', false);
    }

    public function usage_bulan_ini($penyedia = 'deepl')
    {
        $r = translator_safe_query(
            "SELECT jumlah_request, jumlah_karakter, jumlah_string FROM translation_usage_bulanan
             WHERE bulan = DATE_FORMAT(NOW(), '%Y-%m') AND penyedia = ? LIMIT 1",
            array($penyedia), 'row', null);
        return $r;
    }

    public function hitung_override_aktif($aktif = 1)
    {
        $r = translator_safe_query(
            "SELECT COUNT(*) AS c FROM translation_override WHERE aktif = ?",
            array((int) $aktif), 'row', false);
        return ($r === false || $r === null) ? false : (int) $r->c;
    }

    /* ===================== B5: uji lookup ===================== */

    /** Cari teks di override (aktif), cache — hash sama dengan Translator. */
    public function cari_untuk_uji($hash, $kode_bahasa)
    {
        $ov = translator_safe_query(
            "SELECT teks_hasil FROM translation_override WHERE hash_sumber = ? AND kode_bahasa = ? AND aktif = 1 LIMIT 1",
            array($hash, $kode_bahasa), 'row', null);
        if (!empty($ov)) {
            return array('sumber' => 'override', 'hasil' => $ov->teks_hasil);
        }
        $ca = translator_safe_query(
            "SELECT teks_hasil FROM translation_cache WHERE hash_sumber = ? AND kode_bahasa = ? LIMIT 1",
            array($hash, $kode_bahasa), 'row', null);
        if (!empty($ca)) {
            return array('sumber' => 'cache', 'hasil' => $ca->teks_hasil);
        }
        return null;
    }
}
