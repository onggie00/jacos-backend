<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_jadwal_ujian_sd extends MY_Model {

    private $primary_key    = 'id_jadwal';
    private $table_name     = 'jadwal_ujian_sd';
    private $field_search   = ['id_mapel', 'id_jenis_ujian', 'id_tingkatan', 'daftar_kelas', 'tanggal', 'hari', 'jam_mulai', 'jam_selesai', 'id_tahun_ajaran', 'semester'];

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
                    $where .= "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_mapel_sd.nama_mapel LIKE '%" . $q . "%' ";
                    $where .= "OR " . "ujian_mapel_sd.kode_mapel_ujian LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jenis_ujian.nama_ujian LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "ujian_mapel_sd.nama_mapel LIKE '%" . $q . "%' ";
            $where .= "OR " . "ujian_mapel_sd.kode_mapel_ujian LIKE '%" . $q . "%' ";
            $where .= "OR " . "jenis_ujian.nama_ujian LIKE '%" . $q . "%' ";
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
                    $where .= "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ujian_mapel_sd.nama_mapel LIKE '%" . $q . "%' ";
                    $where .= "OR " . "ujian_mapel_sd.kode_mapel_ujian LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jenis_ujian.nama_ujian LIKE '%" . $q . "%' ";
                    $where .= "OR " . "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "jadwal_ujian_sd.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "ujian_mapel_sd.nama_mapel LIKE '%" . $q . "%' ";
            $where .= "OR " . "ujian_mapel_sd.kode_mapel_ujian LIKE '%" . $q . "%' ";
            $where .= "OR " . "jenis_ujian.nama_ujian LIKE '%" . $q . "%' ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('jadwal_ujian_sd.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ujian_mapel_sd', 'ujian_mapel_sd.id_mapel = jadwal_ujian_sd.id_mapel', 'LEFT');
        $this->db->join('jenis_ujian', 'jenis_ujian.id_jenis_ujian = jadwal_ujian_sd.id_jenis_ujian', 'LEFT');
        $this->db->join('tingkatan_sd', 'tingkatan_sd.id_tingkatan_sd = jadwal_ujian_sd.id_tingkatan', 'LEFT');
        $this->db->join('kelas_sd', 'kelas_sd.label = jadwal_ujian_sd.daftar_kelas', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = jadwal_ujian_sd.id_tahun_ajaran', 'LEFT');
        
        $this->db->select('jadwal_ujian_sd.*,ujian_mapel_sd.nama_mapel as ujian_mapel_sd_nama_mapel,jenis_ujian.nama_ujian as jenis_ujian_nama_ujian,tingkatan_sd.label as tingkatan_sd_label,kelas_sd.label as kelas_sd_label,tahun_ajaran.label as tahun_ajaran_label');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_jadwal_ujian_sd.php */
/* Location: ./application/models/Model_jadwal_ujian_sd.php */