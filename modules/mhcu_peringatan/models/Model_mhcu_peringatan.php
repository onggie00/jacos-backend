<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_peringatan extends MY_Model {

    private $primary_key    = 'id_peringatan';
    private $table_name     = 'mhcu_peringatan';
    private $field_search   = array('jenis_alert', 'status_alert', 'diproses_oleh');

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function status_counts()
    {
        $rows = $this->db->select('status_alert, COUNT(*) AS c')
            ->group_by('status_alert')
            ->get($this->table_name)
            ->result();
        $out = array();
        foreach ($rows as $r) {
            $out[$r->status_alert] = intval($r->c);
        }
        return $out;
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
        $this->db->select('mhcu_peringatan.*, mhcu_sesi.npp, mhcu_sesi.nama_lengkap, mhcu_sesi.presensi_role, pk.label_profil AS kategori_keseluruhan, pk.warna AS kategori_warna,
            cb.id_jadwal AS care_id_jadwal, cb.status AS care_status, cj.tanggal AS care_tanggal, cj.sesi_ke AS care_sesi_ke,
            cj.jam_mulai AS care_jam_mulai, cj.jam_selesai AS care_jam_selesai, cj.nama_psikolog AS care_nama_psikolog');
        $this->db->join('mhcu_sesi', 'mhcu_sesi.id_mhcu_sesi = mhcu_peringatan.id_sesi', 'LEFT');
        $this->db->join('mhcu_hasil_individu', 'mhcu_hasil_individu.id_sesi = mhcu_peringatan.id_sesi', 'LEFT');
        $this->db->join('mhcu_profil_kategori pk', 'pk.id_profil_kategori = mhcu_hasil_individu.id_profil_kategori', 'LEFT');
        $this->db->join('mhcu_care_booking cb', "cb.id_sesi = mhcu_peringatan.id_sesi AND cb.status IN ('scheduled','selesai')", 'LEFT');
        $this->db->join('mhcu_care_jadwal cj', 'cj.id_jadwal = cb.id_jadwal', 'LEFT');

        if (!empty($filters['status_alert'])) {
            $this->db->where('mhcu_peringatan.status_alert', $filters['status_alert']);
        }

        if (!empty($filters['jenis_alert'])) {
            $this->db->where('mhcu_peringatan.jenis_alert', $filters['jenis_alert']);
        }

        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                $this->db->like('mhcu_sesi.npp', $q);
                $this->db->or_like('mhcu_sesi.nama_lengkap', $q);
                $this->db->or_like('mhcu_peringatan.diproses_oleh', $q);
                $this->db->group_end();
            } else {
                if ($field == 'npp' || $field == 'nama_lengkap') {
                    $this->db->like('mhcu_sesi.'.$field, $q);
                } else {
                    $this->db->like('mhcu_peringatan.'.$field, $q);
                }
            }
        }
    }

}

/* End of file Model_mhcu_peringatan.php */
