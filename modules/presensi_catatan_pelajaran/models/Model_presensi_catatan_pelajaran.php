<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_catatan_pelajaran extends MY_Model {

    private $primary_key    = 'id_presensi_catatan';
    private $table_name     = 'presensi_catatan_pelajaran';
    private $field_search   = ['jenjang', 'id_siswa_aktif', 'nama_lengkap', 'hari', 'tanggal_waktu', 'status_hadir', 'keterangan', 'jam_ke'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_catatan_pelajaran.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('presensi_catatan_pelajaran.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        // ponytail: dynamic LEFT JOIN by `jenjang` instead of hardcoded siswa_sd_aktif,
        // because id_siswa_aktif is per-jenjang. Caller must filter jenjang if needed.
        $jenjang = isset($this->filter_avaiable_jenjang) ? $this->filter_avaiable_jenjang : 'sd';
        $col_aktif = 'id_siswa_' . $jenjang . '_aktif';
        $this->db->join('siswa_' . $jenjang . '_aktif', 'siswa_' . $jenjang . '_aktif.' . $col_aktif . ' = presensi_catatan_pelajaran.id_siswa_aktif', 'LEFT');
        $this->db->select('presensi_catatan_pelajaran.*, siswa_' . $jenjang . '_aktif.nis as siswa_' . $jenjang . '_aktif_nis');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_presensi_catatan_pelajaran.php */
/* Location: ./application/models/Model_presensi_catatan_pelajaran.php */