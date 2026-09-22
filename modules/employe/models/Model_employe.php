<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_employe extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'employe';
	private $field_search 	= ['name', 'company', 'position', 'head_id', 'sub_id', 'created_at'];

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
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
	                $where .= "employe.".$field . " LIKE '%" . $q . "%' ";
	            } else {
	                $where .= "OR " . "employe.".$field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }

	        $where = '('.$where.')';
        } else {
        	$where .= "(" . "employe.".$field . " LIKE '%" . $q . "%' )";
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
	                $where .= "employe.".$field . " LIKE '%" . $q . "%' ";
	            } else {
	                $where .= "OR " . "employe.".$field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }

	        $where = '('.$where.')';
        } else {
        	$where .= "(" . "employe.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
        	$this->db->select($select_field);
        }
		
		$this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('employe.'.$this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

    public function join_avaiable() {
        $this->db->join('company', 'company.id = employe.company', 'LEFT');
        $this->db->join('position', 'position.id = employe.position', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = employe.head_id', 'LEFT');
        $this->db->join('aauth_users aauth_users1', 'aauth_users1.id = employe.sub_id', 'LEFT');
        
    	$this->db->select('employe.*,company.name as company_name,position.name as position_name,aauth_users.full_name as aauth_users_full_name,aauth_users.email as aauth_users_email');


        return $this;
    }

    public function filter_avaiable() {
        
        return $this;
    }

}

/* End of file Model_employe.php */
/* Location: ./application/models/Model_employe.php */