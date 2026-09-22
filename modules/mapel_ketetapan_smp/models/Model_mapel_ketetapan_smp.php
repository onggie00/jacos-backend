<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mapel_ketetapan_smp extends MY_Model {

    private $primary_key    = 'id_ketetapan';
    private $table_name     = 'mapel_ketetapan_smp';
    private $field_search   = array('kode_mapel', 'tipe_ketetapan', 'nilai', 'keterangan');

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
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'id_ketetapan' : $sort;
        $sort_type = (empty($sort_type)) ? 'asc' : $sort_type;
        
        $this->db->order_by($sort_by, $sort_type);
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Get ketetapan by kode_mapel
     */
    public function get_by_kode_mapel($kode_mapel)
    {
        return $this->db->get_where($this->table_name, array('kode_mapel' => $kode_mapel))->result();
    }

    /**
     * Get ketetapan as array for quick lookup
     * Returns: array[kode_mapel][tipe] = array of values
     */
    public function get_all_as_map()
    {
        $result = $this->db->get($this->table_name)->result();
        $map = array();
        
        foreach ($result as $row) {
            if (!isset($map[$row->kode_mapel])) {
                $map[$row->kode_mapel] = array();
            }
            $map[$row->kode_mapel][$row->tipe_ketetapan] = explode(',', $row->nilai);
        }
        
        return $map;
    }
}

/* End of file Model_mapel_ketetapan_smp.php */
/* Location: ./modules/mapel_alokasi_smp/models/Model_mapel_ketetapan_smp.php */
