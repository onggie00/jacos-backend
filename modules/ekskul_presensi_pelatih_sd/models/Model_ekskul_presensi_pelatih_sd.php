<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ekskul_presensi_pelatih_sd extends MY_Model {

    private $primary_key    = '';
    private $table_name     = 'ekskul_presensi_pelatih_sd';
    private $field_search   = ['id', 'id_ekskul', 'id_pelatih', 'hari_absen', 'tanggal_absen', 'waktu_absen', 'status_absen', 'keterangan_presensi'];

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
                    $where .= "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ekskul_presensi_pelatih_sd.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ekskul', 'ekskul.id_ekskul = ekskul_presensi_pelatih_sd.id_ekskul', 'LEFT');
        $this->db->join('ekskul_manajemen_sd', 'ekskul_manajemen_sd.id_manajemen = ekskul_presensi_pelatih_sd.id_pelatih', 'LEFT');
        
        $this->db->select('ekskul_presensi_pelatih_sd.*,ekskul.nama as ekskul_nama,ekskul_manajemen_sd.nama as ekskul_manajemen_sd_nama');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ekskul_presensi_pelatih_sd.php */
/* Location: ./application/models/Model_ekskul_presensi_pelatih_sd.php */