<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_nilai_raport_sma extends MY_Model {

    private $primary_key    = 'id_nilai_raport_sma';
    private $table_name     = 'nilai_raport_sma';
    private $field_search   = ['id_siswa', 'b_inggris', 'b_inggris2', 'b_inggris3', 'b_inggris4', 'b_indonesia', 'b_indonesia2', 'b_indonesia3', 'b_indonesia4', 'ipa', 'ipa2', 'ipa3', 'ipa4', 'ips', 'ips2', 'ips3', 'ips4', 'matematika', 'matematika2', 'matematika3', 'matematika4', 'file_raport', 'file_raport2', 'file_raport3', 'file_raport4'];

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
                    $where .= "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "nilai_raport_sma.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('nilai_raport_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->join('siswa_sma', 'siswa_sma.id_siswa_sma = nilai_raport_sma.id_siswa', 'LEFT');

        $this->db->select('siswa_sma.nama_lengkap,siswa_sma.no_peserta,siswa_sma.notelp_ayah,siswa_sma.notelp_ibu,siswa_sma.sekolah_asal,nilai_raport_sma.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_nilai_raport_sma.php */
/* Location: ./application/models/Model_nilai_raport_sma.php */