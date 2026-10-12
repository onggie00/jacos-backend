<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setting_bahasa_model — Satu tempat logika setting bahasa, dipakai semua role.
 *
 * Tabel setting_bahasa (jangan ubah struktur):
 *   id_setting_bahasa, kode_bahasa, bahasa, role, jenjang,
 *   email_ms_office, updated_at
 * Unique key: uq_email_role (email_ms_office, role)
 *
 * Aturan bisnis:
 *   - Default bahasa "id" (dari config/bahasa.php).
 *   - Email di-trim dan di-lowercase sebelum simpan/cari.
 *   - Role ortu menyimpan email_ms_office_ortu; caller yang bertanggung
 *     jawab mengoper email ortu, model hanya menyimpan yang dioper.
 *   - Satu setting per (email, role), lintas jenjang. Kolom jenjang hanya
 *     catatan jenjang terakhir user saat set/get.
 *
 * KERAS: fitur bahasa TIDAK BOLEH menggagalkan login / request lain.
 * Semua akses DB lewat _safe_query(): db_debug dimatikan sementara
 * (CI3 memanggil show_error() lalu exit ketika db_debug aktif, sehingga
 * try/catch normal tidak menangkap "table doesn't exist"), hasil FALSE /
 * $this->db->error() dijadikan fallback + ditulis ke log
 * application/logs/log-*.php. db_debug dipulihkan di blok finally.
 *
 * Kompatibel MySQL 5.7 dan PHP 7.4. Query memakai binding bawaan CI3.
 */
class Setting_bahasa_model extends CI_Model {

    /** Kode bahasa fallback jika terjadi error atau row tidak ada */
    const DEFAULT_KODE = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->load->config('bahasa');
    }

    /* ========================= LAYER QUERY AMAN ========================= */

    /**
     * Jalankan query dengan db_debug sementara OFF agar error DB tidak
     * memanggil show_error()/exit. Error ditulis ke log; kembalikan fallback.
     *
     * @param string $sql           SQL dengan placeholder "?"
     * @param array  $binds         parameter query
     * @param string $mode          'row' | 'result' | 'write'
     * @param mixed  $fallback      nilai kembalian saat gagal
     * @return mixed hasil row/result/TRUE 'write', atau fallback saat gagal
     */
    private function _safe_query($sql, $binds = array(), $mode = 'row', $fallback = null)
    {
        $prev_debug = isset($this->db->db_debug) ? $this->db->db_debug : false;

        try {
            $this->db->db_debug = false;

            $query = $this->db->query($sql, $binds);

            if ($query === false || $query === true && $mode !== 'write') {
                $err = $this->db->error();
                $msg = isset($err['message']) ? trim(str_replace(array(':', ' '), ' ', $err['message'])) : '';
                log_message('error', 'Setting_bahasa_model: query gagal (' . $msg . ') SQL: ' . $sql);
                return $fallback;
            }

            if ($mode === 'write') {
                return true;
            }

            if ($mode === 'row') {
                return $query->row();
            }
            return $query->result();
        } catch (Throwable $e) {
            log_message('error', 'Setting_bahasa_model: exception ' . get_class($e) . ': ' . $e->getMessage() . ' SQL: ' . $sql);
            return $fallback;
        } catch (Exception $e) {
            // untuk kompatibilitas engine lama
            log_message('error', 'Setting_bahasa_model: exception: ' . $e->getMessage() . ' SQL: ' . $sql);
            return $fallback;
        } finally {
            $this->db->db_debug = $prev_debug;
        }
    }

    /**
     * SELECT row; false = DB error, null = row tidak ada, object = row ada.
     */
    private function _select_row($email, $role)
    {
        return $this->_safe_query(
            "SELECT * FROM setting_bahasa WHERE email_ms_office = ? AND role = ? LIMIT 1",
            array($email, $role),
            'row',
            false
        );
    }

    private function _insert($data)
    {
        return $this->_safe_query(
            "INSERT IGNORE INTO setting_bahasa (kode_bahasa, bahasa, role, jenjang, email_ms_office, updated_at) VALUES (?, ?, ?, ?, ?, NOW())",
            array($data['kode_bahasa'], $data['bahasa'], $data['role'], $data['jenjang'], $data['email_ms_office']),
            'write'
        );
    }

    /* ========================= CONFIG ========================= */

    private function _list_bahasa()
    {
        return $this->config->item('bahasa');
    }

    private function _default_kode()
    {
        // entri dengan default=TRUE; fallback konstanta jika config tidak menandainya
        $list = $this->_list_bahasa();
        if (is_array($list)) {
            foreach ($list as $b) {
                if (!empty($b['default']) && !empty($b['kode'])) {
                    return $b['kode'];
                }
            }
        }
        return self::DEFAULT_KODE;
    }

    private function _nama_bahasa($kode)
    {
        $list = $this->_list_bahasa();
        if (isset($list[$kode]['nama'])) {
            return $list[$kode]['nama'];
        }
        return '';
    }

    /**
     * Daftar bahasa yang tersedia untuk ditampilkan di API.
     */
    public function daftar_bahasa()
    {
        $out = array();
        $list = $this->_list_bahasa();
        if (is_array($list)) {
            foreach ($list as $b) {
                $out[] = array(
                    'kode_bahasa' => $b['kode'],
                    'bahasa'      => $b['nama'],
                );
            }
        }
        return $out;
    }

    /**
     * Kode canonical dari config (matching case-insensitive),
     * atau null jika tidak ada.
     */
    public function canonical_kode($kode_bahasa)
    {
        $list = $this->_list_bahasa();
        if (!is_array($list)) {
            return null;
        }
        $cari = strtolower(trim((string) $kode_bahasa));
        foreach ($list as $kode => $b) {
            if (strtolower((string) $kode) === $cari) {
                return $kode;
            }
        }
        return null;
    }

    public function is_kode_valid($kode_bahasa)
    {
        return $this->canonical_kode($kode_bahasa) !== null;
    }

    /* ========================= NORMALISASI ========================= */

    private function _norm_email($email)
    {
        return strtolower(trim((string) $email));
    }

    private function _norm_jenjang($jenjang)
    {
        // kolom jenjang NOT NULL varchar(5); null => '' (tanpa catatan jenjang)
        if ($jenjang === null) {
            return '';
        }
        return substr(strtolower(trim((string) $jenjang)), 0, 5);
    }

    private function _norm_role($role)
    {
        return strtolower(trim((string) $role));
    }

    /* ========================= CRUD ========================= */

    /**
     * Ambil row setting. Jika belum ada, buatkan row default (aman race
     * condition lewat INSERT IGNORE lalu SELECT).
     *
     * @param string $email   email akun (normalisasi di dalam)
     * @param string $role    siswa|ortu|guru|pegawai|pimpinan
     * @param string $jenjang catatan jenjang terakhir (boleh null)
     * @return object|null row setting_bahasa; null saat row tidak bisa dibuat /
     *                    email / role kosong; FALSE saat error DB (caller harus
     *                    jawab JSON terkontrol, bukan biarkan CI show_error).
     */
    public function get_or_create_default($email, $role, $jenjang = null)
    {
        $email   = $this->_norm_email($email);
        $role    = $this->_norm_role($role);
        $jenjang = $this->_norm_jenjang($jenjang);

        if ($email === '' || $role === '') {
            return null;
        }

        $row = $this->_select_row($email, $role);
        if (!empty($row)) {
            return $row;
        }

        $kode_default = $this->_default_kode();
        $ok = $this->_insert(array(
            'kode_bahasa'     => $kode_default,
            'bahasa'          => $this->_nama_bahasa($kode_default),
            'role'            => $role,
            'jenjang'         => $jenjang,
            'email_ms_office' => $email,
        ));
        if ($ok !== true) {
            return false; // DB error
        }

        $row = $this->_select_row($email, $role);
        if ($row === false) {
            return false; // DB error
        }
        return $row;
    }

    /**
     * Upsert bahasa pilihan user.
     * updated_at hanya diisi ulang ketika nilai kode_bahasa benar-benar berubah.
     * Race-safe: INSERT IGNORE dulu untuk menjamin row ada, baru UPDATE.
     *
     * @return array array('success'=>bool, 'message'=>string, 'changed'=>bool, 'db_error'=>bool, 'data'=>row|null)
     */
    public function update_bahasa($email, $role, $kode_bahasa, $jenjang = null)
    {
        $email   = $this->_norm_email($email);
        $role    = $this->_norm_role($role);
        $jenjang = $this->_norm_jenjang($jenjang);
        $kode    = trim((string) $kode_bahasa);

        $gagal_db = array('success' => false, 'message' => 'Gagal menyimpan setting bahasa (database error)', 'changed' => false, 'db_error' => true, 'data' => null);

        if ($email === '' || $role === '') {
            return array('success' => false, 'message' => 'Identitas user tidak ditemukan', 'changed' => false, 'db_error' => false, 'data' => null);
        }
        if ($kode === '') {
            return array('success' => false, 'message' => 'Parameter kode_bahasa wajib diisi', 'changed' => false, 'db_error' => false, 'data' => null);
        }
        if (!$this->is_kode_valid($kode)) {
            return array('success' => false, 'message' => 'Kode bahasa tidak valid: ' . $kode, 'changed' => false, 'db_error' => false, 'data' => null);
        }
        $kode = $this->canonical_kode($kode); // kode dalam ejaan canonical dari config

        $row = $this->_select_row($email, $role);
        if ($row === false) {
            return $gagal_db;
        }

        if (empty($row)) {
            $ok = $this->_insert(array(
                'kode_bahasa'     => $kode,
                'bahasa'          => $this->_nama_bahasa($kode),
                'role'            => $role,
                'jenjang'         => $jenjang,
                'email_ms_office' => $email,
            ));
            if ($ok !== true) {
                return $gagal_db;
            }
            $row = $this->_select_row($email, $role);
        }

        if (empty($row)) {
            return $gagal_db;
        }

        if ($row->kode_bahasa === $kode) {
            // tidak berubah — updated_at tidak disentuh
            return array('success' => true, 'message' => 'Bahasa tidak berubah', 'changed' => false, 'db_error' => false, 'data' => $row);
        }

        $ok = $this->_safe_query(
            "UPDATE setting_bahasa SET kode_bahasa = ?, bahasa = ?, jenjang = ?, updated_at = NOW() WHERE email_ms_office = ? AND role = ?",
            array($kode, $this->_nama_bahasa($kode), $jenjang, $email, $role),
            'write',
            false
        );
        if ($ok !== true) {
            return $gagal_db;
        }

        $row = $this->_select_row($email, $role);
        if (empty($row)) {
            return $gagal_db;
        }

        return array('success' => true, 'message' => 'Bahasa berhasil diubah', 'changed' => true, 'db_error' => false, 'data' => $row);
    }

    /**
     * Kode bahasa berdasarkan npp + nama tabel role (Home_notification).
     * Tipe_user dibatasi allowlist agar query tabel aman.
     * Gagal / tidak ketemu => 'id'.
     */
    public function kode_bahasa_by_npp($npp, $tipe_user)
    {
        $memo_npp =& $this->kode_memo; // memo per-request (instance)
        $npp = trim((string) $npp);
        $tipe = strtolower(trim((string) $tipe_user));
        $key = $tipe . '|' . $npp;
        if (isset($memo_npp[$key])) {
            return $memo_npp[$key];
        }
        $kode = self::DEFAULT_KODE;
        $izin = array('guru_sd' => 'guru', 'guru_smp' => 'guru', 'guru_sma' => 'guru',
                      'guru_ft' => 'guru', 'pegawai' => 'pegawai',
                      'pimpinan_sd' => 'pimpinan', 'pimpinan_smp' => 'pimpinan',
                      'pimpinan_sma' => 'pimpinan');
        if ($npp !== '' && isset($izin[$tipe])) {
            try {
                $row = translator_safe_query(
                    "SELECT email_ms_office FROM {$tipe} WHERE npp = ? AND deleted_at IS NULL LIMIT 1",
                    array($npp), 'row', null);
                if (!empty($row) && !empty($row->email_ms_office)) {
                    $kode = $this->get_kode_bahasa($row->email_ms_office, $izin[$tipe]);
                } else {
                }
            } catch (Throwable $e) {
                log_message('error', 'kode_bahasa_by_npp gagal: ' . substr($e->getMessage(), 0, 100));
            } catch (Exception $e) {
                log_message('error', 'kode_bahasa_by_npp gagal: ' . substr($e->getMessage(), 0, 100));
            }
        }
        $memo_npp[$key] = $kode;
        return $kode;
    }

    /**
     * Kode bahasa user, dengan memo per-request (dipakai fase translate agar
     * tidak query berulang). Error / row tidak ada => kembalikan default 'id',
     * tidak melempar error.
     *
     * @return string kode_bahasa
     */
    /** memo per request (instance; static akan persisten antar request di mod_php) */
    protected $kode_memo = array();

    public function get_kode_bahasa($email, $role)
    {
        $memo =& $this->kode_memo;

        $email = $this->_norm_email($email);
        $role  = $this->_norm_role($role);
        $key   = $role . '|' . $email;

        if (isset($memo[$key])) {
            return $memo[$key];
        }

        $kode = self::DEFAULT_KODE;
        if ($email !== '' && $role !== '') {
            try {
                $row = $this->_select_row($email, $role);
                if (!empty($row) && !empty($row->kode_bahasa)) {
                    $kode = $row->kode_bahasa;
                }
            } catch (Throwable $e) {
                log_message('error', 'get_kode_bahasa gagal: ' . $e->getMessage());
                $kode = self::DEFAULT_KODE;
            } catch (Exception $e) {
                log_message('error', 'get_kode_bahasa gagal: ' . $e->getMessage());
                $kode = self::DEFAULT_KODE;
            }
        }

        // kode tidak dikenal di config => fallback ke default
        if (!$this->is_kode_valid($kode)) {
            $kode = $this->_default_kode();
        }

        $memo[$key] = $kode;
        return $kode;
    }
}
