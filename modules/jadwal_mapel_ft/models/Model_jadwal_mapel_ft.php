<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_jadwal_mapel_ft extends MY_Model {

    private $primary_key    = 'id_jadwal';
    private $table_name     = 'jadwal_mapel_ft';
    private $field_search   = ['jam_mulai', 'jam_selesai', 'hari', 'id_kelas', 'id_mapel', 'id_guru', 'ruang_kelas', 'id_tahun_ajaran'];

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
                    $where .= "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "jadwal_mapel_ft.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('jadwal_mapel_ft.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('kelas_ft', 'kelas_ft.id_kelas_ft = jadwal_mapel_ft.id_kelas', 'LEFT');
        $this->db->join('mata_pelajaran_ft', 'mata_pelajaran_ft.id_mapel = jadwal_mapel_ft.id_mapel', 'LEFT');
        $this->db->join('guru_ft', 'guru_ft.id_guru = jadwal_mapel_ft.id_guru', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = jadwal_mapel_ft.id_tahun_ajaran', 'LEFT');
        
        $this->db->select('jadwal_mapel_ft.*,kelas_ft.label as kelas_ft_label,mata_pelajaran_ft.nama_mapel as mata_pelajaran_ft_nama_mapel,guru_ft.nama_lengkap as guru_ft_nama_lengkap,tahun_ajaran.label as tahun_ajaran_label');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_jadwal_mapel_ft.php */
/* Location: ./application/models/Model_jadwal_mapel_ft.php */