<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_acara_evaluasi_form extends MY_Model {

    private $primary_key    = 'id_evaluasi_form';
    private $table_name     = 'acara_evaluasi_form';
    private $field_search   = ['id_acara', 'pertanyaan', 'tipe_pertanyaan', 'is_required', 'jawaban_pertanyaan', 'no_urut'];

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
                    $where .= "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara_evaluasi_form.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('acara_evaluasi_form.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('acara', 'acara.id_acara = acara_evaluasi_form.id_acara', 'LEFT');
        
        $this->db->select('acara_evaluasi_form.*,acara.nama_acara as acara_nama_acara');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_acara_evaluasi_form.php */
/* Location: ./application/models/Model_acara_evaluasi_form.php */