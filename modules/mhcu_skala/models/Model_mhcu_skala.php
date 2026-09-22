<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_skala extends MY_Model {

    private $primary_key    = 'id_skala';
    private $table_name     = 'mhcu_skala';
    private $field_search   = array('kode_skala', 'nama_skala');

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

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $sort = null, $sort_type = 'ASC', $multi_filters = array())
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
            $this->db->order_by('mhcu_skala.'.$sort, $sort_type);
        } else {
            $this->db->order_by('mhcu_skala.'.$this->primary_key, "ASC");
        }

        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    public function get_options($id_skala)
    {
        return $this->db->where('id_skala', $id_skala)->order_by('no_urut_option', 'ASC')->get('mhcu_skala_option')->result();
    }

    public function save_option($data)
    {
        return $this->db->insert('mhcu_skala_option', $data);
    }

    public function update_option($id, $data)
    {
        return $this->db->where('id_skala_option', $id)->update('mhcu_skala_option', $data);
    }

    public function delete_option($id)
    {
        return $this->db->where('id_skala_option', $id)->delete('mhcu_skala_option');
    }

    public function delete_options_by_skala($id_skala)
    {
        return $this->db->where('id_skala', $id_skala)->delete('mhcu_skala_option');
    }

    private function _apply_simple_filter($q = null, $field = null)
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                foreach ($this->field_search as $f) {
                    $this->db->or_like('mhcu_skala.'.$f, $q);
                }
                $this->db->group_end();
            } else {
                $this->db->like('mhcu_skala.'.$field, $q);
            }
        }
    }

    private function _apply_multi_filter($multi_filters)
    {
        foreach ($multi_filters as $f) {
            $db_field = 'mhcu_skala.' . $this->scurity($f['field']);
            $operator = $f['operator'];
            $value = $f['value'];

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

/* End of file Model_mhcu_skala.php */
