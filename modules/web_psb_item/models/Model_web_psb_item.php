<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_web_psb_item extends MY_Model {

    private $primary_key    = 'id_web_psb_item';
    private $table_name     = 'web_psb_step_item';
    private $field_search   = ['jawaban'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $id_pertanyaan = null)
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);
        $where = NULL;

        if (empty($field)) {
            $where = "(web_psb_step_item.jawaban LIKE '%" . $q . "%')";
        } else {
            $where = "(web_psb_step_item.".$field . " LIKE '%" . $q . "%')";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->_filter_pertanyaan($id_pertanyaan);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $id_pertanyaan = null)
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);
        $where = NULL;

        if (empty($field)) {
            $where = "(web_psb_step_item.jawaban LIKE '%" . $q . "%')";
        } else {
            $where = "(web_psb_step_item.".$field . " LIKE '%" . $q . "%')";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->_filter_pertanyaan($id_pertanyaan);
        $this->db->limit($limit, $offset);
        $this->db->order_by('web_psb_step_item.id_web_psb_item', 'ASC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /** wajib terikat ke satu pertanyaan */
    private function _filter_pertanyaan($id_pertanyaan)
    {
        $this->db->where('web_psb_step_item.id_web_psb_pertanyaan', (int) $id_pertanyaan);
        return $this;
    }

    public function join_avaiable() {
        $this->db->select('web_psb_step_item.*');
        return $this;
    }

    public function filter_avaiable() {
        return $this;
    }
}

/* End of file Model_web_psb_item.php */
/* Location: ./modules/web_psb_item/models/Model_web_psb_item.php */
