<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_kinerja_staff extends MY_Model {

    private $primary_key    = 'id_laporan';
    private $table_name     = 'laporan_kinerja_staff';
    private $field_search   = ['id_staff', 'kompetensi_profesional', 'kompetensi_kepribadian', 'kompetensi_sosial', 'leadership', 'nilai_prestasi', 'nilai_presensi', 'total_skor', 'tahun_ajaran'];

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
                    $where .= "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.unit LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.unit LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.npp LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.nama_lengkap LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.unit LIKE '%" . $q . "%' )";
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
                    $where .= "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pegawai.unit LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_staff.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.npp LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.nama_lengkap LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pegawai.unit LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('laporan_kinerja_staff.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('pegawai', 'pegawai.id_pegawai = laporan_kinerja_staff.id_staff', 'LEFT');
        
        $this->db->select('laporan_kinerja_staff.*,pegawai.nama_lengkap as pegawai_nama_lengkap, pegawai.npp as npp, pegawai.unit as unit');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_laporan_kinerja_staff.php */
/* Location: ./application/models/Model_laporan_kinerja_staff.php */