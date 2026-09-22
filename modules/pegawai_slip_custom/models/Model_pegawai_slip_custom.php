<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pegawai_slip_custom extends MY_Model {

    private $primary_key    = 'id_slip';
    private $table_name     = 'pegawai_slip_custom';
    private $field_search   = ['nama_slip', 'nama_lengkap', 'npp', 'golongan', 'jabatan', 'gaji_pokok', 'tunjangan_istri', 'tunjangan_anak', 'tunjangan_pengelolaan', 'tunjangan_jabatan', 'tunjangan_kesejahteraan', 'tunjangan_masa_kerja', 'tunjangan_fungsional', 'total_kehadiran', 'tunjangan_kehadiran', 'total_mengajar', 'tunjangan_mengajar', 'total_piket', 'rupiah_per_piket', 'tunjangan_piket', 'tunjangan_wali_kelas', 'tunjangan_pembina', 'insentif_ft', 'tunjangan_insentif', 'bonus', 'honor', 'thr', 'gaji14', 'pph21', 'total_penghasilan', 'total_potongan', 'total_diterima', 'kwitansi', 'periode_mulai', 'periode_selesai'];

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
                    $where .= "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "pegawai_slip_custom.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('pegawai_slip_custom.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('pegawai_slip_custom.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_pegawai_slip_custom.php */
/* Location: ./application/models/Model_pegawai_slip_custom.php */