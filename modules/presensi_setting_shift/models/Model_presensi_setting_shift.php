<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_setting_shift extends MY_Model {

    private $primary_key    = 'id_shift';
    private $table_name     = 'presensi_setting_shift';
    private $field_search   = ['id_role', 'hari', 'start_checkin', 'limit_checkin', 'start_checkout', 'limit_checkout', 'updated_by', 'updated_at'];

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
                    $where .= "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_setting_shift.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('presensi_setting_shift.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('presensi_setting_role', 'presensi_setting_role.id = presensi_setting_shift.id_role', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = presensi_setting_shift.updated_by', 'LEFT');
        
        $this->db->select('presensi_setting_shift.*,presensi_setting_role.nama_role as presensi_setting_role_nama_role,aauth_users.email as aauth_users_email');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_presensi_setting_shift.php */
/* Location: ./application/models/Model_presensi_setting_shift.php */