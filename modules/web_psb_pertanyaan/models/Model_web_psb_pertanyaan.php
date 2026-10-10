<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_web_psb_pertanyaan extends MY_Model {

    private $primary_key    = 'id_step_pertanyaan';
    private $table_name     = 'web_psb_step_pertanyaan';
    private $field_search   = ['pertanyaan', 'step', 'jenjang'];

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
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . "web_psb_step_pertanyaan.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        // urutan ala web PSB: step numerik lalu no_urut
        $this->db->order_by('CAST(web_psb_step_pertanyaan.step AS UNSIGNED)', 'ASC');
        $this->db->order_by('web_psb_step_pertanyaan.no_urut', 'ASC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {

        $this->db->select('web_psb_step_pertanyaan.*');
        // jumlah jawaban (item) per pertanyaan untuk kolom list
        $this->db->select('(select count(*) from web_psb_step_item wi where wi.id_web_psb_pertanyaan = web_psb_step_pertanyaan.id_step_pertanyaan) as jumlah_item');

        return $this;
    }

    public function filter_avaiable() {

        return $this;
    }

    /** jumlah item per pertanyaan */
    public function count_items($id_pertanyaan)
    {
        $this->db->where('id_web_psb_pertanyaan', (int) $id_pertanyaan);
        return $this->db->count_all_results('web_psb_step_item');
    }
}

/* End of file Model_web_psb_pertanyaan.php */
/* Location: ./modules/web_psb_pertanyaan/models/Model_web_psb_pertanyaan.php */
