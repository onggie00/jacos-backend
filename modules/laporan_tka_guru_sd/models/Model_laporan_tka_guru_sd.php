<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_tka_guru_sd extends MY_Model {

    private $primary_key    = 'id_laporan';
    private $table_name     = 'laporan_tka_guru_sd';
    private $field_search   = ['unit_kerja', 'mata_pelajaran', 'bhs_indonesia', 'bhs_inggris', 'numerasi', 'total_skor', 'tahun'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );
        parent::__construct($config);
    }

    private function nama_expr() {
        return "CASE WHEN ".$this->table_name.".id_guru = 0 THEN ".$this->table_name.".nama_lengkap WHEN ".$this->table_name.".jenjang = 'sd' THEN guru_sd.nama_lengkap WHEN ".$this->table_name.".jenjang = 'smp' THEN guru_smp.nama_lengkap WHEN ".$this->table_name.".jenjang = 'sma' THEN guru_sma.nama_lengkap END";
    }

    private function npp_expr() {
        return "CASE WHEN ".$this->table_name.".id_guru = 0 THEN '-' WHEN ".$this->table_name.".jenjang = 'sd' THEN guru_sd.npp WHEN ".$this->table_name.".jenjang = 'smp' THEN guru_smp.npp WHEN ".$this->table_name.".jenjang = 'sma' THEN guru_sma.npp END";
    }

    public function count_all($q = null, $field = null, $tahun = null)
    {
        $this->db->from($this->table_name);
        $this->join_avaiable()->filter_avaiable();
        
        if (!empty($tahun)) {
            $this->db->where($this->table_name.'.tahun', $tahun);
        }
        
        if (!empty($q)) {
            $q = $this->scurity($q);
            if (!empty($field)) {
                if ($field == 'guru') {
                    $this->db->where($this->nama_expr()." LIKE '%".$q."%'", null, false);
                } else {
                    $this->db->like($this->table_name.'.'.$field, $q);
                }
            } else {
                $this->db->group_start();
                $this->db->where($this->nama_expr()." LIKE '%".$q."%'", null, false);
                foreach ($this->field_search as $f) {
                    $this->db->or_like($this->table_name.'.'.$f, $q);
                }
                $this->db->group_end();
            }
        }
        
        return $this->db->count_all_results();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $tahun = null)
    {
        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        
        if (!empty($tahun)) {
            $this->db->where($this->table_name.'.tahun', $tahun);
        }
        
        if (!empty($q)) {
            $q = $this->scurity($q);
            if (!empty($field)) {
                if ($field == 'guru') {
                    $this->db->where($this->nama_expr()." LIKE '%".$q."%'", null, false);
                } else {
                    $this->db->like($this->table_name.'.'.$field, $q);
                }
            } else {
                $this->db->group_start();
                $this->db->where($this->nama_expr()." LIKE '%".$q."%'", null, false);
                foreach ($this->field_search as $f) {
                    $this->db->or_like($this->table_name.'.'.$f, $q);
                }
                $this->db->group_end();
            }
        }
        
        $this->db->limit($limit, $offset);
        $this->db->order_by($this->table_name.'.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_sd', 'guru_sd.id_guru = '.$this->table_name.'.id_guru AND '.$this->table_name.".jenjang = 'sd'", 'LEFT');
        $this->db->join('guru_smp', 'guru_smp.id_guru = '.$this->table_name.'.id_guru AND '.$this->table_name.".jenjang = 'smp'", 'LEFT');
        $this->db->join('guru_sma', 'guru_sma.id_guru = '.$this->table_name.'.id_guru AND '.$this->table_name.".jenjang = 'sma'", 'LEFT');
        $this->db->select($this->table_name.'.*, '.$this->nama_expr().' as guru_sd_nama_lengkap, '.$this->npp_expr().' as npp');
        return $this;
    }

    public function filter_avaiable() {
        return $this;
    }

    public function get_distinct_tahun() {
        $this->db->select('tahun');
        $this->db->distinct();
        $this->db->order_by('tahun', 'DESC');
        $query = $this->db->get($this->table_name);
        return $query->result_array();
    }
}
