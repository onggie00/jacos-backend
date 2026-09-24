<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kelas_kb extends MY_Model {

    private $primary_key    = 'id_kelas_kb';
    private $table_name     = 'kelas_kb';
    private $field_search   = ['id_tingkatan', 'nama_kelas'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($filter = array())
    {
        $q = isset($filter['q']) ? $filter['q'] : null;
        $field = isset($filter['f']) ? $filter['f'] : null;
        $id_tingkatan = isset($filter['id_tingkatan']) ? $filter['id_tingkatan'] : null;

        $where = '';
        if (!empty($q)) {
            $q = $this->scurity($q);
            if (empty($field)) {
                $where = "(kelas_kb.nama_kelas LIKE '%" . $q . "%' OR tingkatan_kb.label LIKE '%" . $q . "%')";
            } else {
                $where = "(kelas_kb." . $field . " LIKE '%" . $q . "%')";
            }
        }

        if (!empty($id_tingkatan)) {
            $id_tingkatan = (int) $id_tingkatan;
            if (!empty($where)) {
                $where .= " AND kelas_kb.id_tingkatan = " . $id_tingkatan;
            } else {
                $where = "kelas_kb.id_tingkatan = " . $id_tingkatan;
            }
        }

        $this->join_avaiable();
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($filter = array(), $limit = 0, $offset = 0)
    {
        $q = isset($filter['q']) ? $filter['q'] : null;
        $field = isset($filter['f']) ? $filter['f'] : null;
        $id_tingkatan = isset($filter['id_tingkatan']) ? $filter['id_tingkatan'] : null;

        $where = '';
        if (!empty($q)) {
            $q = $this->scurity($q);
            if (empty($field)) {
                $where = "(kelas_kb.nama_kelas LIKE '%" . $q . "%' OR tingkatan_kb.label LIKE '%" . $q . "%')";
            } else {
                $where = "(kelas_kb." . $field . " LIKE '%" . $q . "%')";
            }
        }

        if (!empty($id_tingkatan)) {
            $id_tingkatan = (int) $id_tingkatan;
            if (!empty($where)) {
                $where .= " AND kelas_kb.id_tingkatan = " . $id_tingkatan;
            } else {
                $where = "kelas_kb.id_tingkatan = " . $id_tingkatan;
            }
        }

        $this->join_avaiable();
        if (!empty($where)) {
            $this->db->where($where);
        }
        $this->db->limit($limit, $offset);
        $this->db->order_by('kelas_kb.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('tingkatan_kb', 'tingkatan_kb.id_tingkatan_kb = kelas_kb.id_tingkatan', 'LEFT');
        $this->db->select('kelas_kb.*,tingkatan_kb.label as tingkatan_kb_label');
        return $this;
    }

    public function filter_avaiable() {
        if (!$this->aauth->is_admin()) { }
        return $this;
    }

}

/* End of file Model_kelas_kb.php */
/* Location: ./application/models/Model_kelas_kb.php */
