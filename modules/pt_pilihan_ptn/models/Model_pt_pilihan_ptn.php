<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pt_pilihan_ptn extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'pt_pilihan_ptn';
    private $field_search   = ['jenjang', 'id_siswa_aktif', 'id_pt', 'id_jurusan'];

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
                    $where .= "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.jurusan LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_jurusan.jurusan LIKE '%" . $q . "%' ) OR (" . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ) OR (" . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' )";
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
                    $where .= "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.jurusan LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_pilihan_ptn.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_jurusan.jurusan LIKE '%" . $q . "%' ) OR (" . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ) OR (" . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('pt_pilihan_ptn.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_ft_aktif', 'siswa_ft_aktif.id_siswa_ft_aktif = pt_pilihan_ptn.id_siswa_aktif', 'LEFT');
        $this->db->join('siswa_sma_aktif', 'siswa_sma_aktif.id_siswa_sma_aktif = pt_pilihan_ptn.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_ft', 'kelas_ft.id_kelas_ft = siswa_ft_aktif.id_kelas', 'LEFT');
        $this->db->join('kelas_sma', 'kelas_sma.id_kelas_sma = siswa_sma_aktif.id_kelas', 'LEFT');
        $this->db->join('pt_perguruan_tinggi', 'pt_perguruan_tinggi.id = pt_pilihan_ptn.id_pt', 'LEFT');
        $this->db->join('pt_jurusan', 'pt_jurusan.id = pt_pilihan_ptn.id_jurusan', 'LEFT');
        
        $this->db->select('pt_pilihan_ptn.*, siswa_ft_aktif.nama_lengkap as siswa_ft_aktif_nama_lengkap, siswa_sma_aktif.nama_lengkap as siswa_sma_aktif_nama_lengkap, siswa_ft_aktif.nis as siswa_ft_aktif_nis, siswa_sma_aktif.nis as siswa_sma_aktif_nis, kelas_sma.label as kelas_sma_label, kelas_ft.label as kelas_ft_label, pt_perguruan_tinggi.nama_pt as pt_perguruan_tinggi_nama_pt,pt_jurusan.jurusan as pt_jurusan_jurusan, pt_jurusan.passing_grade as pt_jurusan_passing_grade');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pt_pilihan_ptn.php */
/* Location: ./application/models/Model_pt_pilihan_ptn.php */