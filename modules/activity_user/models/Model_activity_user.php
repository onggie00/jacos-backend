<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_activity_user extends MY_Model {

    private $primary_key    = 'id_activity';
    private $table_name     = 'activity_user';
    private $field_search   = ['keterangan', 'value', 'endpoint', 'ip_address', 'created_at', 'updated_by'];

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
                    $where .= "activity_user.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "activity_user.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "activity_user.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "activity_user.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "activity_user.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "activity_user.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('activity_user.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('activity_user.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Get recent activities (last N days, limited)
     */
    public function get_recent($days = 3, $limit = 10)
    {
        $date_from = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $this->db->where('created_at >=', $date_from);
        $this->db->order_by($this->primary_key, 'DESC');
        $this->db->limit($limit);

        return $this->db->get($this->table_name)->result();
    }

}

/* End of file Model_activity_user.php */
/* Location: ./application/models/Model_activity_user.php */