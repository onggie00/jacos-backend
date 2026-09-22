<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_kategori_catatan extends MY_Model {

    private $primary_key    = 'id_kategori_catatan';
    private $table_name     = 'presensi_kategori_catatan';
    private $field_search   = ['nama_kategori', 'skor', 'is_custom', 'status'];

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
                    $where .= "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array())
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_kategori_catatan.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('presensi_kategori_catatan.'.$this->primary_key, "ASC");
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    public function join_avaiable() {
        $this->db->select('presensi_kategori_catatan.*');
        return $this;
    }

    public function filter_avaiable() {
        if (!$this->aauth->is_admin()) {
        }
        return $this;
    }
}

/* End of file Model_presensi_kategori_catatan.php */
/* Location: ./application/models/Model_presensi_kategori_catatan.php */
