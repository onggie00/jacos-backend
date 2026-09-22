<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ekskul_member_smp extends MY_Model {

    private $primary_key    = 'id_member';
    private $table_name     = 'ekskul_member_smp';
    private $field_search   = ['id_ekskul', 'id_siswa', 'id_kelas', 'file_pembayaran', 'status_member', 'joined_date'];

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
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        $this->join_avaiable()->filter_avaiable();

        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                $this->db->like('ekskul.nama', $q);
                $this->db->or_like('siswa_smp_aktif.nama_lengkap', $q);
                $this->db->or_like('kelas_smp.label', $q);
                $this->db->or_like('siswa_smp_aktif.nis', $q);
                $this->db->or_like('ekskul_member_smp.tahun_ajaran', $q);
                $this->db->group_end();
            } elseif ($field == 'nama_kelas') {
                $this->db->like('kelas_smp.label', $q);
            } elseif ($field == 'nama_lengkap') {
                $this->db->like('siswa_smp_aktif.nama_lengkap', $q);
            } elseif ($field == 'nama_ekskul') {
                $this->db->like('ekskul.nama', $q);
            } else {
                $this->db->like('ekskul_member_smp.' . $field, $q);
            }
        }

        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();

        if (!empty($q)) {
            if (empty($field)) {
                $this->db->group_start();
                $this->db->like('ekskul.nama', $q);
                $this->db->or_like('siswa_smp_aktif.nama_lengkap', $q);
                $this->db->or_like('kelas_smp.label', $q);
                $this->db->or_like('siswa_smp_aktif.nis', $q);
                $this->db->or_like('ekskul_member_smp.tahun_ajaran', $q);
                $this->db->group_end();
            } elseif ($field == 'nama_kelas') {
                $this->db->like('kelas_smp.label', $q);
            } elseif ($field == 'nama_lengkap') {
                $this->db->like('siswa_smp_aktif.nama_lengkap', $q);
            } elseif ($field == 'nama_ekskul') {
                $this->db->like('ekskul.nama', $q);
            } else {
                $this->db->like('ekskul_member_smp.' . $field, $q);
            }
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('ekskul_member_smp.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('ekskul', 'ekskul.id_ekskul = ekskul_member_smp.id_ekskul and ekskul.jenjang = "smp"', 'LEFT');
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = ekskul_member_smp.id_siswa', 'LEFT');
        $this->db->join('kelas_smp', 'siswa_smp_aktif.id_kelas = kelas_smp.id_kelas_smp', 'LEFT');
        
        $this->db->select('ekskul_member_smp.*,ekskul.nama as nama_ekskul,siswa_smp_aktif.nama_lengkap as nama_lengkap, siswa_smp_aktif.nis as nis, siswa_smp_aktif.id_kelas, kelas_smp.label as nama_kelas');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ekskul_member_smp.php */
/* Location: ./application/models/Model_ekskul_member_smp.php */