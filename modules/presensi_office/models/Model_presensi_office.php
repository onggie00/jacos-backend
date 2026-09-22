<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_office extends MY_Model {

    private $primary_key    = 'id_presensi';
    private $table_name     = 'presensi_office';
    private $field_search   = ['npp', 'nama_lengkap', 'presensi_date', 'check_in', 'check_out', 'presensi_device', 'keterangan', 'file_report'];

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

        //count via COUNT(*) — jangan num_rows atas select penuh
        if (!empty($q)) {
            if (empty($field)) {
                foreach ($this->field_search as $fs) {
                    if ($iterasi == 1) {
                        $where .= "presensi_office.".$fs . " LIKE '%" . $q . "%' ";
                    } else {
                        $where .= "OR " . "presensi_office.".$fs . " LIKE '%" . $q . "%' ";
                    }
                    $iterasi++;
                }
                $where = '('.$where.')';
            } else {
                $where .= "(" . "presensi_office.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->select('COUNT(*) AS total', FALSE)->get($this->table_name);

        return (int) $query->row()->total;
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        //skip LIKE '%…%' kalau tidak ada keyword pencarian
        if (!empty($q)) {
            if (empty($field)) {
                foreach ($this->field_search as $fs) {
                    if ($iterasi == 1) {
                        $where .= "presensi_office.".$fs . " LIKE '%" . $q . "%' ";
                    } else {
                        $where .= "OR " . "presensi_office.".$fs . " LIKE '%" . $q . "%' ";
                    }
                    $iterasi++;
                }
                $where = '('.$where.')';
            } else {
                $where .= "(" . "presensi_office.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->limit($limit, $offset);
                $this->db->order_by('presensi_office.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('presensi_office.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_presensi_office.php */
/* Location: ./application/models/Model_presensi_office.php */