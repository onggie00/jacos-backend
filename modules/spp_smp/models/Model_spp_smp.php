<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_spp_smp extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'spp_smp';
    private $field_search   = ['id_siswa_aktif', 'kelas', 'tahun_ajaran', 'nominal', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember', 'januari', 'februari', 'maret', 'april', 'mei', 'juni'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    private function _build_where($q, $field)
    {
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            // Search all fields
            $conditions = array();
            foreach ($this->field_search as $f) {
                $conditions[] = "spp_smp." . $f . " LIKE '%" . $q . "%'";
            }
            $conditions[] = "siswa_smp_aktif.nis LIKE '%" . $q . "%'";
            $conditions[] = "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%'";
            $conditions[] = "kelas_smp.nama_kelas LIKE '%" . $q . "%'";
            $conditions[] = "tahun_ajaran.label LIKE '%" . $q . "%'";
            $where = '(' . implode(' OR ', $conditions) . ')';
        }
        else if ($field == "nis") {
            $where = "siswa_smp_aktif.nis LIKE '%" . $q . "%'";
        }
        else if ($field == "nama_lengkap") {
            $where = "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%'";
        }
        else if ($field == "nama_kelas") {
            $where = "(spp_smp.kelas LIKE '%" . $q . "%' OR kelas_smp.label LIKE '%" . $q . "%')";
        }
        else if ($field == "tahun_ajaran") {
            $where = "tahun_ajaran.label LIKE '%" . $q . "%'";
        }
        else {
            $where = "spp_smp." . $field . " LIKE '%" . $q . "%'";
        }

        return $where;
    }

    public function count_all($q = null, $field = null)
    {
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $where = $this->_build_where($q, $field);

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('spp_smp.' . $this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = spp_smp.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = siswa_smp_aktif.id_kelas', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = spp_smp.id_tahun_ajaran', 'LEFT');
        
        $this->db->select('spp_smp.*,siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap, siswa_smp_aktif.nis, kelas_smp.nama_kelas, tahun_ajaran.label as tahun_ajaran_label');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_spp_smp.php */
/* Location: ./application/models/Model_spp_smp.php */