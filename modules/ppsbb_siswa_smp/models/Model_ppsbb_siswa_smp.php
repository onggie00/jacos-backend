<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ppsbb_siswa_smp extends MY_Model {

    private $primary_key    = 'id_ppsbb_siswa_smp';
    private $table_name     = 'ppsbb_siswa_smp';
    private $field_search   = ['id_siswa_smp', 'jenis_prestasi', 'nama_prestasi', 'keterangan_prestasi', 'keterangan_prestasi_lainnya', 'tahun_prestasi', 'sertifikat', 'jenis_jenjang', 'jenis_lomba'];

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
                    $where .= "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "ppsbb_siswa_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('ppsbb_siswa_smp.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('jenis_prestasi', 'jenis_prestasi.id_jenis_prestasi = ppsbb_siswa_smp.jenis_prestasi', 'LEFT');
        $this->db->join('keterangan_prestasi', 'keterangan_prestasi.id_keterangan_prestasi = ppsbb_siswa_smp.keterangan_prestasi', 'LEFT');
        $this->db->join('jenis_jenjang', 'jenis_jenjang.id_jenis_jenjang = ppsbb_siswa_smp.jenis_jenjang', 'LEFT');
        $this->db->join('jenis_lomba', 'jenis_lomba.jenis_lomba = ppsbb_siswa_smp.jenis_lomba', 'LEFT');
        
        $this->db->select('ppsbb_siswa_smp.*,jenis_prestasi.jenis_prestasi as jenis_prestasi_jenis_prestasi,keterangan_prestasi.keterangan_prestasi as keterangan_prestasi_keterangan_prestasi,jenis_jenjang.jenis_jenjang as jenis_jenjang_jenis_jenjang,jenis_lomba.jenis_lomba as jenis_lomba_jenis_lomba');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_ppsbb_siswa_smp.php */
/* Location: ./application/models/Model_ppsbb_siswa_smp.php */