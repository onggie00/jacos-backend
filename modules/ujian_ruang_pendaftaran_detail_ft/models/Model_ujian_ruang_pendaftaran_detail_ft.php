<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ujian_ruang_pendaftaran_detail_ft extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'ujian_ruang_pendaftaran_detail_ft';
    private $field_search   = ['id_ruang_pendaftaran', 'nomor_peserta', 'added_at'];

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
                    $where .= "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_detail_ft.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('ujian_ruang_pendaftaran_detail_ft.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ujian_ruang_pendaftaran_ft', 'ujian_ruang_pendaftaran_ft.id_ruang_pendaftaran = ujian_ruang_pendaftaran_detail_ft.id_ruang_pendaftaran', 'LEFT');
        $this->db->join('siswa_ft', 'siswa_ft.no_peserta = ujian_ruang_pendaftaran_detail_ft.nomor_peserta', 'LEFT');
        
        $this->db->select('ujian_ruang_pendaftaran_detail_ft.*,ujian_ruang_pendaftaran_ft.nama_ruang as ujian_ruang_pendaftaran_ft_nama_ruang,siswa_ft.nama_lengkap as siswa_ft_nama_lengkap, siswa_ft.password_ujian, siswa_ft.no_peserta');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ujian_ruang_pendaftaran_detail_ft.php */
/* Location: ./application/models/Model_ujian_ruang_pendaftaran_detail_ft.php */