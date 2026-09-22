<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_instrument extends MY_Model {

    private $primary_key    = 'id_instrument';
    private $table_name     = 'mhcu_instrument';
    private $field_search   = array('nama_instrument', 'kode_instrument');

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
        if (!empty($multi_filters)) { $this->_apply_multi_filter($multi_filters); } else { $this->_apply_simple_filter($q, $field); }
        return $this->db->get($this->table_name)->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $sort = null, $sort_type = 'ASC', $multi_filters = array())
    {
        if (!empty($multi_filters)) { $this->_apply_multi_filter($multi_filters); } else { $this->_apply_simple_filter($q, $field); }
        if (is_array($select_field) AND count($select_field)) { $this->db->select($select_field); }
        $this->db->limit($limit, $offset);
        $this->db->order_by('mhcu_instrument.'.(!empty($sort) ? $sort : 'no_urut'), $sort_type);
        return $this->db->get($this->table_name)->result();
    }

    private function _apply_simple_filter($q = null, $field = null)
    {
        $q = $this->scurity($q); $field = $this->scurity($field);
        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                foreach ($this->field_search as $f) { $this->db->or_like('mhcu_instrument.'.$f, $q); }
                $this->db->group_end();
            } else { $this->db->like('mhcu_instrument.'.$field, $q); }
        }
    }

    private function _apply_multi_filter($multi_filters)
    {
        foreach ($multi_filters as $f) {
            $db_field = 'mhcu_instrument.' . $this->scurity($f['field']);
            switch ($f['operator']) {
                case 'equals': $this->db->where($db_field, $f['value']); break;
                case 'starts_with': $this->db->like($db_field, $f['value'], 'after'); break;
                case 'ends_with': $this->db->like($db_field, $f['value'], 'before'); break;
                default: $this->db->like($db_field, $f['value']); break;
            }
        }
    }

    public function get_items($id_instrument)
    {
        $this->db->select('mhcu_instrument_item.*, mhcu_skala.nama_skala');
        $this->db->join('mhcu_skala', 'mhcu_skala.id_skala = mhcu_instrument_item.id_skala', 'LEFT');
        $this->db->where('mhcu_instrument_item.id_instrument', $id_instrument);
        $this->db->where('mhcu_instrument_item.deleted_at IS NULL');
        $this->db->order_by('mhcu_instrument_item.no_urut_item', 'ASC');
        return $this->db->get('mhcu_instrument_item')->result();
    }

    public function get_item($id_item)
    {
        return $this->db->where('id_instrument_item', $id_item)->get('mhcu_instrument_item')->row();
    }

    public function save_item($data)
    {
        // Generate ID if needed
        if (!isset($data['id_instrument_item'])) {
            $max = $this->db->select_max('id_instrument_item')->get('mhcu_instrument_item')->row();
            $data['id_instrument_item'] = ($max && $max->id_instrument_item) ? $max->id_instrument_item + 1 : 1;
        }
        return $this->db->insert('mhcu_instrument_item', $data);
    }

    public function update_item($id, $data)
    {
        return $this->db->where('id_instrument_item', $id)->update('mhcu_instrument_item', $data);
    }

    public function delete_item($id)
    {
        // Soft delete
        return $this->db->where('id_instrument_item', $id)->update('mhcu_instrument_item', array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function delete_items_by_instrument($id_instrument)
    {
        return $this->db->where('id_instrument', $id_instrument)->update('mhcu_instrument_item', array('deleted_at' => date('Y-m-d H:i:s')));
    }

    public function get_all_skalas()
    {
        return $this->db->order_by('nama_skala', 'ASC')->get('mhcu_skala')->result();
    }

}

/* End of file Model_mhcu_instrument.php */
