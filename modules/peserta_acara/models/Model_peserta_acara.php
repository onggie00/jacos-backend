<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_peserta_acara extends CI_Model
{
    // Mapping role khusus → beberapa tabel
    private $role_multi = array(
        'pimpinan' => array('pimpinan_sd', 'pimpinan_smp', 'pimpinan_sma'),
    );

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get acara detail
     */
    public function get_acara($id_acara)
    {
        return $this->db->get_where('acara', array('id_acara' => (int) $id_acara))->row();
    }

    /**
     * Ambil semua peserta undangan dari tabel role
     */
    public function get_semua_undangan($peserta_acara_str)
    {
        $roles = array_filter(array_map('trim', explode(',', $peserta_acara_str)));
        $tables_to_query = array();

        foreach ($roles as $role) {
            $role_lower = strtolower($role);
            if (isset($this->role_multi[$role_lower])) {
                foreach ($this->role_multi[$role_lower] as $tbl) {
                    $tables_to_query[] = array('table' => $tbl, 'role_label' => $role_lower);
                }
            } else {
                $tables_to_query[] = array('table' => $role_lower, 'role_label' => $role_lower);
            }
        }

        $semua_undangan = array(); // key = npp

        foreach ($tables_to_query as $item) {
            $tbl = $item['table'];
            $role_label = $item['role_label'];

            // Cek tabel ada
            $check = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape_str($tbl) . "'")->row();
            if (!$check) continue;

            // Cek kolom yang tersedia
            $cols = $this->db->list_fields($tbl);
            $npp_col = in_array('npp', $cols) ? 'npp' : null;
            $nama_col = null;
            foreach (array('nama_lengkap', 'nama', 'full_name') as $c) {
                if (in_array($c, $cols)) { $nama_col = $c; break; }
            }
            if (!$npp_col || !$nama_col) continue;

            $this->db->select("$npp_col as npp, $nama_col as nama_lengkap");
            $this->db->where("$npp_col IS NOT NULL");
            $this->db->where("$npp_col != ''");
            // Skip non-active users (deleted_at not null)
            if (in_array('deleted_at', $cols)) {
                $this->db->where('deleted_at IS NULL', null, false);
            }
            $rows = $this->db->get($tbl)->result();

            foreach ($rows as $r) {
                $npp = trim($r->npp);
                if ($npp === '') continue;
                // Skip dummy users
                if (stripos($r->nama_lengkap, 'dummy') !== false) continue;
                if (!isset($semua_undangan[$npp])) {
                    $semua_undangan[$npp] = array(
                        'npp'  => $npp,
                        'nama' => $r->nama_lengkap,
                        'role' => $role_label,
                    );
                }
            }
        }

        return $semua_undangan;
    }

    /**
     * Ambil daftar hadir dari acara_presensi
     */
    public function get_hadir($id_acara)
    {
        $presensi_rows = $this->db->get_where('acara_presensi', array('id_acara' => (int) $id_acara))->result();
        $hadir = array();
        foreach ($presensi_rows as $p) {
            $npp = trim($p->npp);
            if ($npp === '') continue;
            // Skip dummy users
            if (stripos($p->peserta, 'dummy') !== false) continue;
            $hadir[$npp] = array(
                'npp'            => $npp,
                'nama'           => $p->peserta,
                'role'           => $p->role,
                'waktu_presensi' => $p->waktu_presensi,
            );
        }
        return $hadir;
    }

    /**
     * Hitung tidak hadir (undangan - hadir)
     */
    public function get_tidak_hadir($semua_undangan, $hadir)
    {
        $tidak_hadir = array();
        foreach ($semua_undangan as $npp => $info) {
            if (!isset($hadir[$npp])) {
                $tidak_hadir[$npp] = $info;
            }
        }
        return $tidak_hadir;
    }
}
