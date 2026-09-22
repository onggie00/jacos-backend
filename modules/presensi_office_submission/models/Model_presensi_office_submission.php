<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_office_submission extends MY_Model {

    private $primary_key    = 'id_submission';
    private $table_name     = 'presensi_office_submission';
    private $field_search   = ['npp', 'nama_lengkap', 'submission_code', 'file_submission', 'submission_status', 'date_start', 'created_at', 'updated_at', 'updated_by'];

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
                    $where .= "presensi_office_submission.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_office_submission.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_office_submission.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "presensi_office_submission.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_office_submission.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_office_submission.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('presensi_office_submission.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('presensi_submission_type', 'presensi_submission_type.code = presensi_office_submission.submission_code', 'LEFT');
        $this->db->join('presensi_setting_role', 'presensi_setting_role.id = presensi_office_submission.role', 'LEFT');
        
        $this->db->select('presensi_office_submission.*,presensi_submission_type.name as presensi_submission_type_name,presensi_setting_role.nama_role as presensi_setting_role_nama_role');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_presensi_office_submission.php */
/* Location: ./application/models/Model_presensi_office_submission.php */