<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_sesi extends MY_Model {

    private $primary_key    = 'id_mhcu_sesi';
    private $table_name     = 'mhcu_sesi';
    private $field_search   = array('npp', 'nama_lengkap', 'presensi_role', 'status');

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $filters = array())
    {
        $this->_apply_filters($q, $field, $filters);
        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $filters = array())
    {
        $this->_apply_filters($q, $field, $filters);
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->table_name.'.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    private function _apply_filters($q = null, $field = null, $filters = array())
    {
        $this->db->select('mhcu_sesi.*, mhcu_periode.nama_periode, pk.label_profil AS kategori_keseluruhan, pk.warna AS kategori_warna, mhcu_hasil_individu.is_krisis');
        $this->db->join('mhcu_periode', 'mhcu_periode.id_mhcu_periode = mhcu_sesi.id_mhcu_periode', 'LEFT');
        $this->db->join('mhcu_hasil_individu', 'mhcu_hasil_individu.id_sesi = mhcu_sesi.id_mhcu_sesi', 'LEFT');
        $this->db->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT');
        $this->db->where('mhcu_sesi.deleted_at IS NULL', null, false);

        // Filter by period
        if (!empty($filters['id_mhcu_periode'])) {
            $this->db->where('mhcu_sesi.id_mhcu_periode', intval($filters['id_mhcu_periode']));
        }

        // Filter by role
        if (!empty($filters['presensi_role'])) {
            $this->db->where('mhcu_sesi.presensi_role', $filters['presensi_role']);
        }

        // Filter by status
        if (!empty($filters['status'])) {
            $this->db->where('mhcu_sesi.status', $filters['status']);
        }

        // Filter by kategori
        if (!empty($filters['kategori'])) {
            $this->db->where('pk.label_profil', $filters['kategori']);
        }

        // Search
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                $this->db->like('mhcu_sesi.npp', $q);
                $this->db->or_like('mhcu_sesi.nama_lengkap', $q);
                $this->db->group_end();
            } else {
                $this->db->like('mhcu_sesi.'.$field, $q);
            }
        }
    }

    public function get_periodes()
    {
        return $this->db->order_by('id_mhcu_periode', 'DESC')->get('mhcu_periode')->result();
    }

    /**
     * Ambil periode terbaru (untuk default filter)
     */
    public function get_latest_periode()
    {
        return $this->db->order_by('id_mhcu_periode', 'DESC')->limit(1)->get('mhcu_periode')->row();
    }

    /**
     * Sumber peserta dan role mapping-nya.
     * Prioritas: PIMPINAN -> GURU -> PEGAWAI
     */
    private function _sumber_peserta()
    {
        return array(
            // PIMPINAN (prioritas utama)
            'pimpinan' => array(
                'tables' => array('pimpinan_sd' => 'SD', 'pimpinan_smp' => 'SMP', 'pimpinan_sma' => 'SMA'),
                'priority' => 1,
            ),
            // GURU (skip jika sudah ketemu di PIMPINAN)
            'guru' => array(
                'tables' => array('guru_sd' => 'GuruSD', 'guru_smp' => 'GuruSMP', 'guru_sma' => 'GuruSMA'),
                'priority' => 2,
            ),
            // PEGAWAI (skip jika sudah ketemu di PIMPINAN atau GURU)
            'pegawai' => array(
                'tables' => array('pegawai' => 'PEGAWAI'),
                'priority' => 3,
            ),
        );
    }

    /**
     * Bangun SQL untuk UNION semua sumber, dengan filter role opsional.
     * Return array sql_parts (per tabel).
     */
    private function _build_union_peserta($role_filter = null)
    {
        $sources = $this->_sumber_peserta();
        $parts = array();
        $table_index = 0;
        $role_table_map = array(); // role => table alias yg dipakai utk dedup nanti

        foreach ($sources as $group_key => $group) {
            foreach ($group['tables'] as $tbl => $role) {
                if (!empty($role_filter) && strtoupper($role_filter) !== strtoupper($role)) {
                    continue;
                }
                $alias = 'src' . $table_index;
                $table_index++;

                $sql = "SELECT `{$tbl}`.`npp`, `{$tbl}`.`nama_lengkap`, '" . addslashes($role) . "' AS `presensi_role` FROM `{$tbl}`";
                $sql .= " WHERE `{$tbl}`.`npp` IS NOT NULL AND `{$tbl}`.`npp` <> ''";
                $sql .= " AND `{$tbl}`.`nama_lengkap` IS NOT NULL AND `{$tbl}`.`nama_lengkap` <> ''";
                $sql .= " AND `{$tbl}`.`deleted_at` IS NULL";
                // Exclude nama mengandung 'dummy', 'tes', atau 'Guru Native'
                $sql .= " AND LOWER(`{$tbl}`.`nama_lengkap`) NOT LIKE '%dummy%'";
                $sql .= " AND LOWER(`{$tbl}`.`nama_lengkap`) NOT LIKE '%tes%'";
                $sql .= " AND LOWER(`{$tbl}`.`nama_lengkap`) NOT LIKE '%guru native%'";

                // Jika bukan prioritas 1, exclude yang sudah ada di prioritas lebih tinggi
                if ($group['priority'] > 1) {
                    $higher_unions = array();
                    foreach ($sources as $hk => $hg) {
                        if ($hk === $group_key) break; // skip group ini dan setelahnya
                        foreach ($hg['tables'] as $ht => $hr) {
                            if (!empty($role_filter) && strtoupper($role_filter) !== strtoupper($hr)) continue;
                            $higher_unions[] = "SELECT `npp` FROM `{$ht}` WHERE `npp` IS NOT NULL AND `npp` <> ''";
                        }
                    }
                    if (!empty($higher_unions)) {
                        $sql .= " AND NOT EXISTS (SELECT 1 FROM (" . implode(' UNION ', $higher_unions) . ") higher WHERE higher.npp = `{$tbl}`.`npp`)";
                    }
                }

                $parts[] = $sql;
            }
        }

        return $parts;
    }

    /**
     * Peserta yang punya npp tapi TIDAK submit di periode X.
     */
    public function get_tidak_hadir($periode_id, $role_filter = null)
    {
        if (empty($periode_id)) return array();

        $union_parts = $this->_build_union_peserta($role_filter);
        if (empty($union_parts)) return array();

        $sql = "SELECT t.npp, t.nama_lengkap, t.presensi_role FROM (" . implode(' UNION ', $union_parts) . ") t";
        $sql .= " WHERE NOT EXISTS (";
        $sql .= "   SELECT 1 FROM `mhcu_sesi` s";
        $sql .= "   WHERE s.`npp` = t.`npp`";
        $sql .= "   AND s.`id_mhcu_periode` = " . intval($periode_id);
        $sql .= "   AND s.`deleted_at` IS NULL";
        $sql .= "   AND s.`presensi_role` IS NOT NULL AND s.`presensi_role` <> ''";
        $sql .= " )";
        // Dedup by nama_lengkap (pilih baris pertama per group via ANY_VALUE)
        // Kompatibel dengan MySQL 5.7+ strict mode
        $sql = "SELECT ANY_VALUE(t.npp) AS npp, t.nama_lengkap, ANY_VALUE(t.presensi_role) AS presensi_role FROM (";
        $sql .= "  SELECT * FROM (" . implode(' UNION ', $union_parts) . ") raw";
        $sql .= "  WHERE NOT EXISTS (";
        $sql .= "    SELECT 1 FROM `mhcu_sesi` s";
        $sql .= "    WHERE s.`npp` = raw.`npp`";
        $sql .= "    AND s.`id_mhcu_periode` = " . intval($periode_id);
        $sql .= "    AND s.`deleted_at` IS NULL";
        $sql .= "    AND s.`presensi_role` IS NOT NULL AND s.`presensi_role` <> ''";
        $sql .= "  )";
        $sql .= ") t";
        $sql .= " GROUP BY t.`nama_lengkap`";
        $sql .= " ORDER BY ANY_VALUE(t.`presensi_role`) ASC, t.`nama_lengkap` ASC";

        return $this->db->query($sql)->result();
    }

    /**
     * Hitung jumlah peserta tidak hadir
     */
    public function count_tidak_hadir($periode_id, $role_filter = null)
    {
        if (empty($periode_id)) return 0;
        return count($this->get_tidak_hadir($periode_id, $role_filter));
    }

    /**
     * Total seluruh peserta target (semua npp valid) — untuk konteks stat card
     */
    public function count_total_peserta($role_filter = null)
    {
        $union_parts = $this->_build_union_peserta($role_filter);
        if (empty($union_parts)) return 0;
        $sql = "SELECT COUNT(*) as cnt FROM (SELECT t.`nama_lengkap` FROM (" . implode(' UNION ', $union_parts) . ") t GROUP BY t.`nama_lengkap`) x";
        $row = $this->db->query($sql)->row();
        return $row ? intval($row->cnt) : 0;
    }

    public function get_roles()
    {
        return $this->db->distinct()->select('presensi_role')->where('deleted_at IS NULL', null, false)->get('mhcu_sesi')->result();
    }

    public function get_sesi_detail($id_sesi)
    {
        $sesi = $this->db
            ->select('mhcu_sesi.*, mhcu_periode.nama_periode')
            ->join('mhcu_periode', 'mhcu_periode.id_mhcu_periode = mhcu_sesi.id_mhcu_periode', 'LEFT')
            ->where('mhcu_sesi.id_mhcu_sesi', $id_sesi)
            ->get('mhcu_sesi')->row();

        $hasil = $this->db->select('mhcu_hasil_individu.*, pk.label_profil AS kategori_keseluruhan, pk.warna AS kategori_warna, pk.narasi_kategori, pk.saran_kategori AS saran_rekomendasi, pk.emoji AS kategori_emoji')
            ->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT')
            ->where('mhcu_hasil_individu.id_sesi', $id_sesi)->get('mhcu_hasil_individu')->row();

        $scores = $this->db
            ->select('mhcu_instrument_score.*, mhcu_instrument.nama_instrument, mhcu_instrument.kode_instrument')
            ->join('mhcu_instrument', 'mhcu_instrument.id_instrument = mhcu_instrument_score.id_instrument', 'LEFT')
            ->where('mhcu_instrument_score.id_sesi', $id_sesi)
            ->order_by('mhcu_instrument.no_urut', 'ASC')
            ->get('mhcu_instrument_score')->result();

        $peringatan = $this->db->where('id_sesi', $id_sesi)->get('mhcu_peringatan')->result();

        return array(
            'sesi' => $sesi,
            'hasil' => $hasil,
            'scores' => $scores,
            'peringatan' => $peringatan,
        );
    }

}

/* End of file Model_mhcu_sesi.php */
/* Location: ./application/models/Model_mhcu_sesi.php */
