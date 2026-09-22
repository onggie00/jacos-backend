<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_siswa_ft_aktif_raport extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'siswa_ft_aktif_raport';
    private $field_search   = ['nis', 'nama_lengkap', 'id_siswa_aktif', 'tahun_ajaran', 'jenis_ujian', 'tanggal_sync_valid', 'file_raport'];

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
                    $where .= "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "siswa_ft_aktif_raport.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('siswa_ft_aktif_raport.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_ft_aktif', 'siswa_ft_aktif.id_siswa_ft_aktif = siswa_ft_aktif_raport.id_siswa_aktif', 'LEFT');
        
        $this->db->select('siswa_ft_aktif_raport.*,siswa_ft_aktif.nama_lengkap as siswa_ft_aktif_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_siswa_ft_aktif_raport.php */
/* Location: ./application/models/Model_siswa_ft_aktif_raport.php */