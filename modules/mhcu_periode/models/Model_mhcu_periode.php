<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_periode extends MY_Model {

    private $primary_key    = 'id_mhcu_periode';
    private $table_name     = 'mhcu_periode';
    private $field_search   = array('nama_periode', 'tanggal_mulai', 'tanggal_selesai');

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $multi_filters = array())
    {
        if (!empty($multi_filters)) {
            $this->_apply_multi_filter($multi_filters);
        } else {
            $this->_apply_simple_filter($q, $field);
        }

        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $sort = null, $sort_type = 'DESC', $multi_filters = array())
    {
        if (!empty($multi_filters)) {
            $this->_apply_multi_filter($multi_filters);
        } else {
            $this->_apply_simple_filter($q, $field);
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->db->limit($limit, $offset);

        if (!empty($sort)) {
            $this->db->order_by('mhcu_periode.'.$sort, $sort_type);
        } else {
            $this->db->order_by('mhcu_periode.'.$this->primary_key, "DESC");
        }

        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    private function _apply_simple_filter($q = null, $field = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                foreach ($this->field_search as $f) {
                    if ($iterasi == 1) {
                        $where .= "mhcu_periode.".$f . " LIKE '%" . $q . "%' ";
                    } else {
                        $where .= "OR " . "mhcu_periode.".$f . " LIKE '%" . $q . "%' ";
                    }
                    $iterasi++;
                }
                $where = '('.$where.')';
            } else {
                $where .= "(" . "mhcu_periode.".$field . " LIKE '%" . $q . "%' )";
            }
            $this->db->where($where);
        }
    }

    private function _apply_multi_filter($multi_filters)
    {
        foreach ($multi_filters as $f) {
            $field = $this->scurity($f['field']);
            $operator = $f['operator'];
            $value = $f['value'];

            $db_field = 'mhcu_periode.' . $field;

            switch ($operator) {
                case 'contains':
                    $this->db->like($db_field, $value);
                    break;
                case 'equals':
                    $this->db->where($db_field, $value);
                    break;
                case 'starts_with':
                    $this->db->like($db_field, $value, 'after');
                    break;
                case 'ends_with':
                    $this->db->like($db_field, $value, 'before');
                    break;
                default:
                    $this->db->like($db_field, $value);
                    break;
            }
        }
    }

}

/* End of file Model_mhcu_periode.php */
