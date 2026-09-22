<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pegawai extends MY_Model {

    private $primary_key    = 'id_pegawai';
    private $table_name     = 'pegawai';
    private $field_search   = ['nama_lengkap', 'nik', 'nuptk', 'npp', 'kk', 'npwp', 'alamat', 'agama', 'jenis_kelamin', 'status_menikah', 'jumlah_anak', 'no_telp', 'email', 'id_posisi', 'unit', 'status_kepegawaian', 'informasi_kepala_pimpinan', 'emp_code', 'token', 'token_expired', 'email_ms_office', 'no_kk', 'tempat_lahir', 'tgl_lahir', 'foto_profil', 'slip_gaji'];

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
                    $where .= "pegawai.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pegawai.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pegawai.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "pegawai.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pegawai.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pegawai.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $field = $this->scurity($field);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'id_pegawai' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        
        $this->db->order_by('pegawai.' . $sort_by, $sort_type);
        
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('agama', 'agama.value = pegawai.agama', 'LEFT');
        $this->db->join('posisi_pegawai', 'posisi_pegawai.id_posisi = pegawai.id_posisi', 'LEFT');
        
        $this->db->select('pegawai.*,agama.label as agama_label,posisi_pegawai.nama as posisi_pegawai_nama');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pegawai.php */
/* Location: ./application/models/Model_pegawai.php */