<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ekskul_manajemen_sd extends MY_Model {

    private $primary_key    = 'id_manajemen';
    private $table_name     = 'ekskul_manajemen_sd';
    private $field_search   = ['id_ekskul', 'id_guru_pembina', 'nama', 'keterangan', 'email_pelatih', 'notelp_pelatih'];

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
                    $where .= "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "ekskul.nama LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if($field == "id_ekskul"){
                $where .= "(" . "ekskul.nama LIKE '%" . $q . "%' )";
            }
            else if($field == "id_guru_pembina"){
                $where .= "(" . "guru_sd.nama_lengkap LIKE '%" . $q . "%' )";
            }
            else{
                $where .= "(" . "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' )";
            }
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
                    $where .= "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "ekskul.nama LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if($field == "id_ekskul"){
                $where .= "(" . "ekskul.nama LIKE '%" . $q . "%' )";
            }
            else if($field == "id_guru_pembina"){
                $where .= "(" . "guru_sd.nama_lengkap LIKE '%" . $q . "%' )";
            }
            else{
                $where .= "(" . "ekskul_manajemen_sd.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('ekskul_manajemen_sd.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ekskul', 'ekskul.id_ekskul = ekskul_manajemen_sd.id_ekskul', 'LEFT');
        $this->db->join('guru_sd', 'guru_sd.id_guru = ekskul_manajemen_sd.id_guru_pembina', 'LEFT');
        
        $this->db->select('ekskul_manajemen_sd.*,ekskul.nama as ekskul_nama,guru_sd.nama_lengkap as guru_sd_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ekskul_manajemen_sd.php */
/* Location: ./application/models/Model_ekskul_manajemen_sd.php */