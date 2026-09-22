<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ujian_ruang_pendaftaran_detail_sma extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'ujian_ruang_pendaftaran_detail_sma';
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
                    $where .= "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_detail_sma.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('ujian_ruang_pendaftaran_detail_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ujian_ruang_pendaftaran_sma', 'ujian_ruang_pendaftaran_sma.id_ruang_pendaftaran = ujian_ruang_pendaftaran_detail_sma.id_ruang_pendaftaran', 'LEFT');
        $this->db->join('siswa_sma', 'siswa_sma.no_peserta = ujian_ruang_pendaftaran_detail_sma.nomor_peserta', 'LEFT');
        
        $this->db->select('ujian_ruang_pendaftaran_detail_sma.*,ujian_ruang_pendaftaran_sma.nama_ruang as ujian_ruang_pendaftaran_sma_nama_ruang,siswa_sma.nama_lengkap as siswa_sma_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ujian_ruang_pendaftaran_detail_sma.php */
/* Location: ./application/models/Model_ujian_ruang_pendaftaran_detail_sma.php */