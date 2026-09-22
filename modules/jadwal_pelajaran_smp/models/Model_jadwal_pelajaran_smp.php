<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_jadwal_pelajaran_smp extends MY_Model {

    private $primary_key    = 'id_jadwal_pelajaran';
    private $table_name     = 'jadwal_pelajaran_smp';
    private $field_search   = array('guru_smp.nama_lengkap', 'kelas_smp.label', 'mata_pelajaran_smp.nama_mapel', 'pelajaran_hari.hari');

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
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'pelajaran_hari.id_hari' : $sort;
        $sort_type = (empty($sort_type)) ? 'asc' : $sort_type;
        
        $this->db->order_by($sort_by, $sort_type);
        $this->db->order_by('pelajaran_jam.jam_ke', 'asc');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_smp', 'guru_smp.id_guru = jadwal_pelajaran_smp.id_guru', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = jadwal_pelajaran_smp.id_kelas_smp', 'LEFT');
        $this->db->join('mata_pelajaran_smp', 'mata_pelajaran_smp.id_mapel = jadwal_pelajaran_smp.id_mapel', 'LEFT');
        $this->db->join('pelajaran_setting_waktu psw', 'psw.id_pelajaran_waktu = jadwal_pelajaran_smp.id_pelajaran_waktu', 'LEFT');
        $this->db->join('pelajaran_hari', 'pelajaran_hari.id_hari = psw.id_hari', 'LEFT');
        $this->db->join('pelajaran_jam', 'pelajaran_jam.id_jam = psw.id_jam', 'LEFT');
        
        $this->db->select('jadwal_pelajaran_smp.*,
            guru_smp.nama_lengkap as guru_nama,
            guru_smp.kode_mapel as guru_kode_mapel,
            kelas_smp.label as kelas_label,
            kelas_smp.id_tingkatan,
            mata_pelajaran_smp.nama_mapel as mapel_nama,
            mata_pelajaran_smp.kode_mapel as mapel_kode,
            pelajaran_hari.id_hari,
            pelajaran_hari.hari as nama_hari,
            pelajaran_jam.jam_ke,
            pelajaran_jam.jam_pelajaran,
            pelajaran_jam.keterangan as jam_keterangan');

        return $this;
    }

    public function filter_avaiable() {
        return $this;
    }

    /**
     * Get jadwal in matrix format (hari x jam) for a specific kelas
     */
    public function get_matrix_by_kelas($id_kelas_smp)
    {
        $this->db->select('
            jadwal_pelajaran_smp.*,
            guru_smp.nama_lengkap as guru_nama,
            mata_pelajaran_smp.nama_mapel as mapel_nama,
            mata_pelajaran_smp.kode_mapel as mapel_kode,
            pelajaran_hari.id_hari,
            pelajaran_hari.hari as nama_hari,
            pelajaran_jam.id_jam,
            pelajaran_jam.jam_ke,
            pelajaran_jam.jam_pelajaran
        ');
        $this->db->join('guru_smp', 'guru_smp.id_guru = jadwal_pelajaran_smp.id_guru', 'LEFT');
        $this->db->join('mata_pelajaran_smp', 'mata_pelajaran_smp.id_mapel = jadwal_pelajaran_smp.id_mapel', 'LEFT');
        $this->db->join('pelajaran_setting_waktu psw', 'psw.id_pelajaran_waktu = jadwal_pelajaran_smp.id_pelajaran_waktu', 'LEFT');
        $this->db->join('pelajaran_hari', 'pelajaran_hari.id_hari = psw.id_hari', 'LEFT');
        $this->db->join('pelajaran_jam', 'pelajaran_jam.id_jam = psw.id_jam', 'LEFT');
        $this->db->where('jadwal_pelajaran_smp.id_kelas_smp', $id_kelas_smp);
        $this->db->where('pelajaran_jam.keterangan LIKE', 'MENGAJAR%');
        $this->db->order_by('pelajaran_hari.id_hari', 'asc');
        $this->db->order_by('pelajaran_jam.jam_ke', 'asc');
        
        return $this->db->get($this->table_name)->result();
    }

    /**
     * Get summary statistics
     */
    public function get_stats()
    {
        $total = $this->db->count_all($this->table_name);
        
        $per_kelas = $this->db->query("
            SELECT k.label, COUNT(*) as jumlah
            FROM jadwal_pelajaran_smp j
            JOIN kelas_smp k ON k.id_kelas_smp = j.id_kelas_smp
            GROUP BY j.id_kelas_smp
            ORDER BY k.id_tingkatan, k.label
        ")->result();
        
        $per_guru = $this->db->query("
            SELECT g.nama_lengkap, g.kode_mapel, COUNT(*) as jumlah
            FROM jadwal_pelajaran_smp j
            JOIN guru_smp g ON g.id_guru = j.id_guru
            GROUP BY j.id_guru
            ORDER BY g.nama_lengkap
        ")->result();
        
        return array(
            'total' => $total,
            'per_kelas' => $per_kelas,
            'per_guru' => $per_guru
        );
    }
}

/* End of file Model_jadwal_pelajaran_smp.php */
/* Location: ./modules/jadwal_pelajaran_smp/models/Model_jadwal_pelajaran_smp.php */
