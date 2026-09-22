<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_mapel_alokasi_smp extends MY_Model {

    private $primary_key    = 'id_alokasi_smp';
    private $table_name     = 'mapel_alokasi_smp';
    private $field_search   = array('mapel_alokasi_smp.id_alokasi_smp', 'mata_pelajaran_smp.kode_mapel', 'mata_pelajaran_smp.nama_mapel', 'guru_smp.nama_lengkap', 'kelas_smp.label');

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
            // Field already contains table prefix
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
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
            // Field already contains table prefix
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'id_alokasi_smp' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        
        $this->db->order_by('mapel_alokasi_smp.' . $sort_by, $sort_type);
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('guru_smp', 'guru_smp.id_guru = mapel_alokasi_smp.id_guru', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = mapel_alokasi_smp.id_kelas_smp', 'LEFT');
        $this->db->join('mata_pelajaran_smp', 'mata_pelajaran_smp.id_mapel = mapel_alokasi_smp.id_mapel', 'LEFT');
        
        $this->db->select('mapel_alokasi_smp.*, 
            guru_smp.nama_lengkap as guru_nama_lengkap,
            guru_smp.kode_mapel as guru_kode_mapel,
            guru_smp.jam_ajar as guru_jam_ajar,
            kelas_smp.label as kelas_label,
            kelas_smp.id_tingkatan as kelas_tingkatan,
            mata_pelajaran_smp.nama_mapel as mapel_nama,
            mata_pelajaran_smp.kode_mapel as mapel_kode');

        return $this;
    }

    public function filter_avaiable() {
        if (!$this->aauth->is_admin()) {
        }
        return $this;
    }

    /**
     * Get current load guru (total jam sudah terisi di semua kelas)
     */
    public function get_current_load($id_guru)
    {
        $this->db->select_sum('jam_terpenuhi');
        $this->db->where('id_guru', $id_guru);
        $row = $this->db->get($this->table_name)->row();
        return (int) ($row->jam_terpenuhi ? $row->jam_terpenuhi : 0);
    }

    /**
     * Get all combinations for draft generation
     * Based on guru_smp.kode_mapel matching kelas_smp
     */
    public function get_draft_combinations()
    {
        // Get all active guru with kode_mapel
        $gurus = $this->db->query("
            SELECT g.id_guru, g.nama_lengkap, g.kode_mapel, g.jam_ajar,
                   mp.id_mapel, mp.nama_mapel
            FROM guru_smp g
            JOIN mata_pelajaran_smp mp ON mp.kode_mapel = g.kode_mapel
            WHERE g.kode_mapel IS NOT NULL AND g.kode_mapel != ''
            ORDER BY g.nama_lengkap
        ")->result();

        // Get all active kelas (exclude tingkatan 4 = keluar/lulus)
        $kelas_list = $this->db->query("
            SELECT id_kelas_smp, id_tingkatan, label 
            FROM kelas_smp 
            WHERE id_tingkatan != 4
            ORDER BY id_tingkatan, label
        ")->result();

        // Get existing alokasi
        $existing = $this->db->query("
            SELECT id_guru, id_kelas_smp, id_mapel, jam_target, jam_terpenuhi, status
            FROM mapel_alokasi_smp
        ")->result();
        
        $existing_map = array();
        foreach ($existing as $e) {
            $key = $e->id_guru . '_' . $e->id_kelas_smp . '_' . $e->id_mapel;
            $existing_map[$key] = $e;
        }

        // Build combinations
        $combinations = array();
        foreach ($gurus as $guru) {
            foreach ($kelas_list as $kelas) {
                $key = $guru->id_guru . '_' . $kelas->id_kelas_smp . '_' . $guru->id_mapel;
                
                if (isset($existing_map[$key])) {
                    // Already exists
                    $row = $existing_map[$key];
                    $combinations[] = array(
                        'id_alokasi_smp' => $row->id_alokasi_smp ?? null,
                        'id_guru' => $guru->id_guru,
                        'id_kelas_smp' => $kelas->id_kelas_smp,
                        'id_mapel' => $guru->id_mapel,
                        'nama_lengkap' => $guru->nama_lengkap,
                        'kode_mapel' => $guru->kode_mapel,
                        'nama_mapel' => $guru->nama_mapel,
                        'kelas_label' => $kelas->label,
                        'tingkatan' => $kelas->id_tingkatan,
                        'jam_target' => $row->jam_target,
                        'jam_terpenuhi' => $row->jam_terpenuhi,
                        'status' => $row->status,
                        'is_existing' => true
                    );
                } else {
                    // New draft
                    $combinations[] = array(
                        'id_alokasi_smp' => null,
                        'id_guru' => $guru->id_guru,
                        'id_kelas_smp' => $kelas->id_kelas_smp,
                        'id_mapel' => $guru->id_mapel,
                        'nama_lengkap' => $guru->nama_lengkap,
                        'kode_mapel' => $guru->kode_mapel,
                        'nama_mapel' => $guru->nama_mapel,
                        'kelas_label' => $kelas->label,
                        'tingkatan' => $kelas->id_tingkatan,
                        'jam_target' => 0,
                        'jam_terpenuhi' => 0,
                        'status' => 'new',
                        'is_existing' => false
                    );
                }
            }
        }

        return $combinations;
    }

    /**
     * Get guru load summary
     */
    public function get_guru_load_summary()
    {
        $result = $this->db->query("
            SELECT 
                g.id_guru,
                g.nama_lengkap,
                g.kode_mapel,
                g.jam_ajar,
                COALESCE(SUM(a.jam_target), 0) as total_jam_target,
                COALESCE(SUM(a.jam_terpenuhi), 0) as total_jam_terpenuhi,
                g.jam_ajar - COALESCE(SUM(a.jam_target), 0) as sisa_kuota
            FROM guru_smp g
            LEFT JOIN mapel_alokasi_smp a ON a.id_guru = g.id_guru
            WHERE g.kode_mapel IS NOT NULL AND g.kode_mapel != ''
            GROUP BY g.id_guru
            ORDER BY g.nama_lengkap
        ")->result();

        return $result;
    }
}

/* End of file Model_mapel_alokasi_smp.php */
/* Location: ./modules/mapel_alokasi_smp/models/Model_mapel_alokasi_smp.php */
