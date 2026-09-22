<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mhcu_demografi extends MY_Model {

    private $primary_key    = 'id_demografi_pertanyaan';
    private $table_name     = 'mhcu_demografi_pertanyaan';
    private $field_search   = array('demografi_kode', 'demografi_pertanyaan', 'input_type');

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
        if (is_array($select_field) AND count($select_field)) { $this->db->select($select_field); }
        $this->db->limit($limit, $offset);
        $this->db->order_by('mhcu_demografi_pertanyaan.'.(!empty($sort) ? $sort : 'no_urut'), $sort_type);
        return $this->db->get($this->table_name)->result();
    }

    public function get_options($id_pertanyaan)
    {
        return $this->db->where('id_demografi_pertanyaan', $id_pertanyaan)->order_by('no_urut', 'ASC')->get('mhcu_demografi_option')->result();
    }

    public function save_option($data) { return $this->db->insert('mhcu_demografi_option', $data); }
    public function update_option($id, $data) { return $this->db->where('id_demografi_option', $id)->update('mhcu_demografi_option', $data); }
    public function delete_option($id) { return $this->db->where('id_demografi_option', $id)->delete('mhcu_demografi_option'); }
    public function delete_options_by_pertanyaan($id) { return $this->db->where('id_demografi_pertanyaan', $id)->delete('mhcu_demografi_option'); }

    private function _apply_simple_filter($q = null, $field = null)
    {
        $q = $this->scurity($q); $field = $this->scurity($field);
        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                foreach ($this->field_search as $f) { $this->db->or_like('mhcu_demografi_pertanyaan.'.$f, $q); }
                $this->db->group_end();
            } else { $this->db->like('mhcu_demografi_pertanyaan.'.$field, $q); }
        }
    }

    private function _apply_multi_filter($multi_filters)
    {
        foreach ($multi_filters as $f) {
            $db_field = 'mhcu_demografi_pertanyaan.' . $this->scurity($f['field']);
            switch ($f['operator']) {
                case 'equals': $this->db->where($db_field, $f['value']); break;
                case 'starts_with': $this->db->like($db_field, $f['value'], 'after'); break;
                case 'ends_with': $this->db->like($db_field, $f['value'], 'before'); break;
                default: $this->db->like($db_field, $f['value']); break;
            }
        }
    }
}
