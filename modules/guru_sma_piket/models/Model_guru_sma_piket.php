<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_guru_sma_piket extends MY_Model {

    private $primary_key    = 'id_guru_piket';
    private $table_name     = 'guru_sma_piket';
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
                    $where .= "guru_sma_piket.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_sma_piket.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "guru_sma_piket.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "guru_sma_piket.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "guru_sma_piket.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "guru_sma_piket.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('guru_sma_piket.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_sma', 'guru_sma.id_guru = guru_sma_piket.id_guru', 'LEFT');
        $this->db->join('kelas_sma', 'kelas_sma.id_kelas_sma = guru_sma_piket.id_kelas', 'LEFT');
        $this->db->join('guru_ft', 'guru_ft.id_guru = guru_sma_piket.id_guru', 'LEFT');
        $this->db->join('kelas_ft', 'kelas_ft.id_kelas_ft = guru_sma_piket.id_kelas', 'LEFT');
        
        $this->db->select('guru_sma_piket.*,guru_sma.nama_lengkap as guru_sma_nama_lengkap,kelas_sma.nama_kelas as kelas_sma_nama_kelas, kelas_sma.label as kelas_sma_label_kelas, guru_ft.nama_lengkap as guru_ft_nama_lengkap,kelas_ft.nama_kelas as kelas_ft_nama_kelas, kelas_ft.label as kelas_ft_label_kelas');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_guru_sma_piket.php */
/* Location: ./application/models/Model_guru_sma_piket.php */