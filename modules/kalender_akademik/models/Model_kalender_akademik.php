<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kalender_akademik extends MY_Model {

    private $primary_key    = 'id_kalender';
    private $table_name     = 'kalender_akademik';
    private $field_search   = ['id_tipe', 'label', 'date', 'id_tahun_ajaran', 'bulan'];

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
                    $where .= "kalender_akademik.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kalender_akademik.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kalender_akademik.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
            $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
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
                    $where .= "kalender_akademik.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kalender_akademik.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kalender_akademik.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "tipe_kalender.nama_tipe LIKE '%" . $q . "%' ";
            $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('kalender_akademik.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('tipe_kalender', 'tipe_kalender.id_tipe = kalender_akademik.id_tipe', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kalender_akademik.id_tahun_ajaran', 'LEFT');
        
        $this->db->select('kalender_akademik.*,tipe_kalender.nama_tipe as tipe_kalender_nama_tipe,tahun_ajaran.label as tahun_ajaran_label');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_kalender_akademik.php */
/* Location: ./application/models/Model_kalender_akademik.php */