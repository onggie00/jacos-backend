<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_prestasi_siswa_sma extends MY_Model {

    private $primary_key    = 'id_prestasi';
    private $table_name     = 'prestasi_siswa_sma';
    private $field_search   = ['nama_prestasi', 'tgl_raih', 'juara', 'file_prestasi', 'foto_prestasi', 'id_siswa', 'jenis_prestasi_id', 'konten', 'tanggal_posting', 'is_approved'];

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
                    $where .= "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jenis_prestasi.jenis_prestasi LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } 
        else if($field == "kelas"){
            $where .= "" . "kelas_sma.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "nama_lengkap"){
            $where .= "" . "siswa_sma_aktif.nama_lengkap". " LIKE '%" . $q . "%' ";
        }
        else if($field == "jenis_prestasi"){
            $where .= "" . "jenis_prestasi.jenis_prestasi". " LIKE '%" . $q . "%' ";
        }
        else {
            $where .= "(" . "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jenis_prestasi.jenis_prestasi LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } 
        else if($field == "kelas"){
            $where .= "" . "kelas_sma.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "nama_lengkap"){
            $where .= "" . "siswa_sma_aktif.nama_lengkap". " LIKE '%" . $q . "%' ";
        }
        else if($field == "jenis_prestasi"){
            $where .= "" . "jenis_prestasi.jenis_prestasi". " LIKE '%" . $q . "%' ";
        }
        else {
            $where .= "(" . "prestasi_siswa_sma.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('prestasi_siswa_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_sma_aktif', 'siswa_sma_aktif.id_siswa_sma_aktif = prestasi_siswa_sma.id_siswa', 'LEFT');
        $this->db->join('kelas_sma', 'kelas_sma.id_kelas_sma = siswa_sma_aktif.id_kelas', 'LEFT');
        $this->db->join('jenis_prestasi', 'jenis_prestasi.id_jenis_prestasi = prestasi_siswa_sma.jenis_prestasi_id', 'LEFT');
        
        $this->db->select('prestasi_siswa_sma.*,siswa_sma_aktif.nama_lengkap as siswa_sma_aktif_nama_lengkap, jenis_prestasi.jenis_prestasi');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_prestasi_siswa_sma.php */
/* Location: ./application/models/Model_prestasi_siswa_sma.php */