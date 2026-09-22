<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_prestasi_siswa_smp extends MY_Model {

    private $primary_key    = 'id_prestasi';
    private $table_name     = 'prestasi_siswa_smp';
    private $field_search   = ['nama_prestasi', 'tgl_raih', 'juara', 'file_prestasi', 'foto_prestasi', 'id_siswa', 'jenis_prestasi_id', 'konten', 'tanggal_posting', 'is_approved'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    /**
     * Apply filters dari controller _build_where().
     * Mirip pola Model_prestasi_siswa_sd.
     */
    private function _apply_filters($filters)
    {
        if (empty($filters)) return;

        // 1. Keyword search
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $f = !empty($filters['f']) ? $filters['f'] : '';

            if (empty($f)) {
                $fields = array('nama_prestasi', 'tgl_raih', 'juara', 'konten');
                $this->db->group_start();
                foreach ($fields as $field) {
                    $this->db->or_like('prestasi_siswa_smp.' . $field, $q);
                }
                $this->db->or_like('siswa_smp_aktif.nama_lengkap', $q);
                $this->db->or_like('kelas_smp.label', $q);
                $this->db->or_like('jenis_prestasi.jenis_prestasi', $q);
                $this->db->group_end();
            } else {
                $allowed = array('nama_prestasi', 'nama_lengkap', 'kelas', 'juara', 'konten');
                if (in_array($f, $allowed)) {
                    if ($f === 'nama_lengkap') {
                        $this->db->like('siswa_smp_aktif.nama_lengkap', $q);
                    } elseif ($f === 'kelas') {
                        $this->db->like('kelas_smp.label', $q);
                    } else {
                        $this->db->like('prestasi_siswa_smp.' . $f, $q);
                    }
                }
            }
        }

        // 2. FK filters
        if (!empty($filters['id_siswa'])) {
            $this->db->where('prestasi_siswa_smp.id_siswa', (int)$filters['id_siswa']);
        }
        if (!empty($filters['jenis_prestasi_id'])) {
            $this->db->where('prestasi_siswa_smp.jenis_prestasi_id', (int)$filters['jenis_prestasi_id']);
        }
        if (!empty($filters['id_prestasi_bidang'])) {
            $this->db->where('prestasi_siswa_smp.id_prestasi_bidang', (int)$filters['id_prestasi_bidang']);
        }

        // 3. Status approval
        if (isset($filters['is_approved']) && $filters['is_approved'] !== '' && $filters['is_approved'] !== null) {
            $this->db->where('prestasi_siswa_smp.is_approved', (int)$filters['is_approved']);
        }

        // 4. Tanggal raih range
        if (!empty($filters['tgl_raih_from'])) {
            $this->db->where('prestasi_siswa_smp.tgl_raih >=', $filters['tgl_raih_from']);
        }
        if (!empty($filters['tgl_raih_to'])) {
            $this->db->where('prestasi_siswa_smp.tgl_raih <=', $filters['tgl_raih_to']);
        }

        // 5. Kurasi Pusprenas
        if (!empty($filters['kurasi_pusprenas'])) {
            $this->db->where('prestasi_siswa_smp.kurasi_pusprenas', $filters['kurasi_pusprenas']);
        }
    }

    public function count_all($filters = null, $field_legacy = null)
    {
        // Backward compat: kalau dipanggil dengan string (old controller), konversi
        if (is_string($filters) && !empty($filters)) {
            $filters = array('q' => $filters, 'f' => $field_legacy);
        }
        if (!is_array($filters)) $filters = array();

        $this->join_avaiable()->filter_avaiable();
        $this->_apply_filters($filters);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($filters = null, $limit = 0, $offset = 0, $select_field = array())
    {
        // Backward compat: old signature get($q, $field, $limit, $offset)
        if (is_string($filters) || is_null($filters)) {
            $args = func_get_args();
            $q      = isset($args[0]) ? $args[0] : null;
            $field  = isset($args[1]) ? $args[1] : null;
            $limit  = isset($args[2]) ? $args[2] : 0;
            $offset = isset($args[3]) ? $args[3] : 0;
            $filters = array();
            if (!empty($q)) $filters['q'] = $q;
            if (!empty($field)) $filters['f'] = $field;
        }

        if (!is_array($filters)) $filters = array();

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->_apply_filters($filters);
        $this->db->limit($limit, $offset);
        $this->db->order_by('prestasi_siswa_smp.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = prestasi_siswa_smp.id_siswa', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = siswa_smp_aktif.id_kelas', 'LEFT');
        $this->db->join('jenis_prestasi', 'jenis_prestasi.id_jenis_prestasi = prestasi_siswa_smp.jenis_prestasi_id', 'LEFT');

        $this->db->select('prestasi_siswa_smp.*, siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap, jenis_prestasi.jenis_prestasi, kelas_smp.label as kelas_smp_label');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of Model_prestasi_siswa_smp.php */
