<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_guru_sd_piket extends MY_Model {

    private $primary_key    = 'id_guru_piket';
    private $table_name     = 'guru_sd_piket';
    private $field_search   = ['id_guru', 'id_kelas', 'created_at'];

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
                    $where .= "guru_sd_piket.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_sd_piket.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "guru_sd_piket.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
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
                    $where .= "guru_sd_piket.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_sd_piket.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "guru_sd_piket.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "guru_sd.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('guru_sd_piket.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_sd', 'guru_sd.id_guru = guru_sd_piket.id_guru', 'LEFT');
        $this->db->join('kelas_sd', 'kelas_sd.id_kelas_sd = guru_sd_piket.id_kelas', 'LEFT');
        
        $this->db->select('guru_sd_piket.*,guru_sd.nama_lengkap as guru_sd_nama_lengkap,kelas_sd.nama_kelas as kelas_sd_nama_kelas, kelas_sd.label as kelas_sd_label_kelas');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_guru_sd_piket.php */
/* Location: ./application/models/Model_guru_sd_piket.php */