<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_kinerja_guru_ft extends MY_Model {

    private $primary_key    = 'id_laporan';
    private $table_name     = 'laporan_kinerja_guru_ft';
    private $field_search   = ['id_guru', 'unit_kerja', 'mata_pelajaran', 'nilai_pimpinan', 'nilai_sejawat', 'nilai_siswa', 'nilai_sendiri', 'nilai_prestasi', 'nilai_presensi', 'rank', 'tahun_ajaran'];

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
                    $where .= "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "laporan_kinerja_guru_ft.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('laporan_kinerja_guru_ft.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_ft', 'guru_ft.id_guru = laporan_kinerja_guru_ft.id_guru', 'LEFT');
        
        $this->db->select('laporan_kinerja_guru_ft.*,guru_ft.nama_lengkap as guru_ft_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_laporan_kinerja_guru_ft.php */
/* Location: ./application/models/Model_laporan_kinerja_guru_ft.php */