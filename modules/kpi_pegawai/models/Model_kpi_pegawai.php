<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kpi_pegawai extends MY_Model {

    private $primary_key    = 'id_kpi';
    private $table_name     = 'kpi_pegawai';
    private $field_search   = ['nama_kpi', 'id_pegawai', 'judul_kpi', 'id_jenis_kpi', 'tanggal', 'keterangan', 'file_piagam', 'is_approved'];

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
                if($field=='is_approve'){
                    $q=is_approve_status($q);
                }
                if ($iterasi == 1) {
                    $where .= "kpi_pegawai.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kpi_pegawai.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kpi_pegawai.".$field . " LIKE '%" . $q . "%' )";
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
                if($field=='is_approve'){
                    $q=is_approve_status($q);
                }
                if ($iterasi == 1) {
                    $where .= "kpi_pegawai.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kpi_pegawai.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kpi_pegawai.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('kpi_pegawai.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('pegawai', 'pegawai.id_pegawai = kpi_pegawai.id_pegawai', 'LEFT');
        $this->db->join('jenis_kpi_pegawai', 'jenis_kpi_pegawai.id_jenis_kpi = kpi_pegawai.id_jenis_kpi', 'LEFT');
        
        $this->db->select('kpi_pegawai.*,pegawai.nama_lengkap as pegawai_nama_lengkap,jenis_kpi_pegawai.nama_jenis as jenis_kpi_pegawai_nama_jenis');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_kpi_pegawai.php */
/* Location: ./application/models/Model_kpi_pegawai.php */