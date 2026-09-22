<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pt_perguruan_tinggi extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'pt_perguruan_tinggi';
    private $field_search   = ['nama_pt', 'inisial', 'provinsi', 'kota', 'logo_ptn', 'updated_at'];

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
                    $where .= "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    //$where .= "OR " . "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.deskripsi LIKE '%" . $q . "%' ";
                    $where .= "OR " . "provinces.name LIKE '%" . $q . "%' ";
                    $where .= "OR " . "regencies.name LIKE '%" . $q . "%' ";
                    //$where .= "OR " . "districts.name LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' )";
            $where .= " OR (" . "provinces.name LIKE '%" . $q . "%' )";
            $where .= " OR (" . "regencies.name LIKE '%" . $q . "%' )";
            //$where .= " OR (" . "districts.name LIKE '%" . $q . "%' )";
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
                    $where .= "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    //$where .= "OR " . "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.deskripsi LIKE '%" . $q . "%' ";
                    $where .= "OR " . "provinces.name LIKE '%" . $q . "%' ";
                    $where .= "OR " . "regencies.name LIKE '%" . $q . "%' ";
                    //$where .= "OR " . "districts.name LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_perguruan_tinggi.".$field . " LIKE '%" . $q . "%' )";
            $where .= " OR (" . "provinces.name LIKE '%" . $q . "%' )";
            $where .= " OR (" . "regencies.name LIKE '%" . $q . "%' )";
            //$where .= " OR (" . "districts.name LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('pt_perguruan_tinggi.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('provinces', 'provinces.id = pt_perguruan_tinggi.provinsi', 'LEFT');
        $this->db->join('regencies', 'regencies.id = pt_perguruan_tinggi.kota', 'LEFT');
        
        $this->db->select('pt_perguruan_tinggi.*,provinces.name as provinces_name,regencies.name as regencies_name');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pt_perguruan_tinggi.php */
/* Location: ./application/models/Model_pt_perguruan_tinggi.php */