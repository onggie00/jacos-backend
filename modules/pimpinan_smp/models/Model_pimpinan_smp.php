<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pimpinan_smp extends MY_Model {

    private $primary_key    = 'id_pimpinan';
    private $table_name     = 'pimpinan_smp';
    private $field_search   = ['nama_lengkap', 'nik', 'nuptk', 'npp', 'npwp', 'alamat', 'agama', 'jenis_kelamin', 'status_menikah', 'jumlah_anak', 'no_telp', 'email', 'id_posisi', 'satuan_pendidikan', 'unit', 'id_mapel', 'status_kepegawaian', 'informasi_kepala_pimpinan', 'emp_code', 'token', 'token_expired', 'email_ms_office', 'foto_profil', 'no_kk', 'tempat_lahir', 'tgl_lahir', 'slip_gaji'];

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
                    $where .= "pimpinan_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pimpinan_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pimpinan_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [],$sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "pimpinan_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pimpinan_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pimpinan_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $field = $this->scurity($field);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'id_pimpinan' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        
        $this->db->order_by('pimpinan_smp.' . $sort_by, $sort_type);
        
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('agama', 'agama.value = pimpinan_smp.agama', 'LEFT');
        $this->db->join('posisi_pimpinan', 'posisi_pimpinan.id_posisi = pimpinan_smp.id_posisi', 'LEFT');
        $this->db->join('mata_pelajaran_smp', 'mata_pelajaran_smp.id_mapel = pimpinan_smp.id_mapel', 'LEFT');
        
        $this->db->select('pimpinan_smp.*,agama.label as agama_label,posisi_pimpinan.nama_posisi as posisi_pimpinan_nama_posisi,mata_pelajaran_smp.nama_mapel as mata_pelajaran_smp_nama_mapel');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pimpinan_smp.php */
/* Location: ./application/models/Model_pimpinan_smp.php */