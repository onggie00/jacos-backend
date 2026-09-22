<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_prestasi_guru_ft extends MY_Model {

    private $primary_key    = 'id_prestasi';
    private $table_name     = 'prestasi_guru_ft';
    private $field_search   = ['nama_prestasi', 'id_guru', 'keterangan', 'file_prestasi', 'foto_prestasi', 'tgl_raih', 'is_approve'];

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
                    $where .= "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "prestasi_guru_ft.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('prestasi_guru_ft.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_ft', 'guru_ft.id_guru = prestasi_guru_ft.id_guru', 'LEFT');
        
        $this->db->select('prestasi_guru_ft.*,guru_ft.nama_lengkap as guru_ft_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_prestasi_guru_ft.php */
/* Location: ./application/models/Model_prestasi_guru_ft.php */