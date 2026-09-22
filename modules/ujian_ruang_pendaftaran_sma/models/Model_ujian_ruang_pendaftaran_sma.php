<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ujian_ruang_pendaftaran_sma extends MY_Model {

    private $primary_key    = 'id_ruang_pendaftaran';
    private $table_name     = 'ujian_ruang_pendaftaran_sma';
    private $field_search   = ['nama_ruang', 'judul_ujian', 'tahun_ajaran', 'kepala_sekolah', 'meeting_url','meeting_password','meeting_id', 'maks_peserta', 'created_at'];

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
                    $where .= "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ujian_ruang_pendaftaran_sma.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('ujian_ruang_pendaftaran_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('tahun_ajaran_psb', 'tahun_ajaran_psb.label = ujian_ruang_pendaftaran_sma.tahun_ajaran', 'LEFT');
        $this->db->join('data_kepala_sekolah', 'data_kepala_sekolah.nama_kepsek = ujian_ruang_pendaftaran_sma.kepala_sekolah', 'LEFT');
        $this->db->distinct('ujian_ruang_pendaftaran_sma.id_ruang_pendaftaran');
        $this->db->select('ujian_ruang_pendaftaran_sma.*,tahun_ajaran_psb.label as tahun_ajaran_psb_label,data_kepala_sekolah.nama_kepsek as data_kepala_sekolah_nama_kepsek');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ujian_ruang_pendaftaran_sma.php */
/* Location: ./application/models/Model_ujian_ruang_pendaftaran_sma.php */