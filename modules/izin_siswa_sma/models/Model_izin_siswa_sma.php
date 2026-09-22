<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_izin_siswa_sma extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'izin_siswa_sma';
    private $field_search   = ['id_siswa_aktif', 'tanggal_mulai', 'tanggal_selesai', 'file_izin', 'keterangan', 'jenis_izin', 'status', 'id_approver'];

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
                    $where .= "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "izin_siswa_sma.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('izin_siswa_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_sma_aktif', 'siswa_sma_aktif.id_siswa_sma_aktif = izin_siswa_sma.id_siswa_aktif', 'LEFT');
        $this->db->join('guru_sma', 'guru_sma.id_guru = izin_siswa_sma.id_approver', 'LEFT');
        
        $this->db->select('izin_siswa_sma.*,siswa_sma_aktif.nama_lengkap as siswa_sma_aktif_nama_lengkap,guru_sma.nama_lengkap as guru_sma_nama_lengkap');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_izin_siswa_sma.php */
/* Location: ./application/models/Model_izin_siswa_sma.php */