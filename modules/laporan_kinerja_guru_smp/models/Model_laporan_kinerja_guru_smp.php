<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_kinerja_guru_smp extends MY_Model {

    private $primary_key    = 'id_laporan';
    private $table_name     = 'laporan_kinerja_guru_smp';
    private $field_search   = ['id_guru', 'unit_kerja', 'mata_pelajaran', 'kompetensi_pedagogik', 'kompetensi_profesional', 'kompetensi_kepribadian', 'kompetensi_sosial', 'leadership', 'nilai_prestasi', 'nilai_presensi', 'rank', 'total_skor', 'tahun_ajaran'];

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
                    $where .= "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
            $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
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
                    $where .= "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_guru_smp.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "guru_smp.npp LIKE '%" . $q . "%' ";
            $where .= "OR " . "guru_smp.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "guru_smp.unit LIKE '%" . $q . "%' ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('laporan_kinerja_guru_smp.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_smp', 'guru_smp.id_guru = laporan_kinerja_guru_smp.id_guru', 'LEFT');
        
        $this->db->select('laporan_kinerja_guru_smp.*,guru_smp.nama_lengkap as guru_smp_nama_lengkap, guru_smp.npp as npp, guru_smp.unit as unit');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_laporan_kinerja_guru_smp.php */
/* Location: ./application/models/Model_laporan_kinerja_guru_smp.php */