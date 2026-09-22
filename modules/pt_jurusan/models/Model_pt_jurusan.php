<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pt_jurusan extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'pt_jurusan';
    private $field_search   = ['id_perguruan_tinggi', 'jurusan', 'passing_grade', 'updated_at'];

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
                    $where .= "pt_jurusan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    //$where .= "OR " . "pt_jurusan.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.jurusan LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.tahun_ajaran LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_jurusan.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' )";
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
                    $where .= "pt_jurusan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pt_jurusan.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.jurusan LIKE '%" . $q . "%' ";
                    $where .= "OR " . "pt_jurusan.tahun_ajaran LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pt_jurusan.".$field . " LIKE '%" . $q . "%' )";
            $where .= "(" . "pt_jurusan.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_perguruan_tinggi.nama_pt LIKE '%" . $q . "%' )";
            $where .= "OR (" . "pt_perguruan_tinggi.inisial LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('pt_jurusan.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('pt_perguruan_tinggi', 'pt_perguruan_tinggi.id = pt_jurusan.id_perguruan_tinggi', 'LEFT');
        
        $this->db->select('pt_jurusan.*,pt_perguruan_tinggi.nama_pt as pt_perguruan_tinggi_nama_pt, pt_perguruan_tinggi.inisial');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pt_jurusan.php */
/* Location: ./application/models/Model_pt_jurusan.php */