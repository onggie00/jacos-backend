<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_kinerja_pimpinan extends MY_Model {

    private $primary_key    = 'id_laporan';
    private $table_name     = 'laporan_kinerja_pimpinan';
    private $field_search   = ['id_pimpinan', 'jenjang', 'jabatan', 'total_skor', 'rank', 'tahun_ajaran'];

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
                    $where .= "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR (" . "pimpinan_sd.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sma.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_smp.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sd.npp LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_smp.npp LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sma.npp LIKE '%" . $q . "%') ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pimpinan_sd.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sma.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_smp.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sd.npp LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_smp.npp LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sma.npp LIKE '%" . $q . "%') ";
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
                    $where .= "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR (" . "pimpinan_sd.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sma.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_smp.nama_lengkap LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sd.npp LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_smp.npp LIKE '%" . $q . "%') ";
                    $where .= "OR (" . "pimpinan_sma.npp LIKE '%" . $q . "%') ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_pimpinan.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pimpinan_sd.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sma.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_smp.nama_lengkap LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sd.npp LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_smp.npp LIKE '%" . $q . "%') ";
            $where .= "OR (" . "pimpinan_sma.npp LIKE '%" . $q . "%') ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('laporan_kinerja_pimpinan.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('pimpinan_sd', 'pimpinan_sd.id_pimpinan = laporan_kinerja_pimpinan.id_pimpinan', 'LEFT');
        $this->db->join('pimpinan_smp', 'pimpinan_smp.id_pimpinan = laporan_kinerja_pimpinan.id_pimpinan', 'LEFT');
        $this->db->join('pimpinan_sma', 'pimpinan_sma.id_pimpinan = laporan_kinerja_pimpinan.id_pimpinan', 'LEFT');
        
        $this->db->select('laporan_kinerja_pimpinan.*,pimpinan_sd.nama_lengkap as pimpinan_sd_nama_lengkap, pimpinan_sd.npp as npp_sd, pimpinan_smp.nama_lengkap as pimpinan_smp_nama_lengkap, pimpinan_smp.npp as npp_smp, pimpinan_sma.nama_lengkap as pimpinan_sma_nama_lengkap, pimpinan_sma.npp as npp_sma');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_laporan_kinerja_pimpinan.php */
/* Location: ./application/models/Model_laporan_kinerja_pimpinan.php */