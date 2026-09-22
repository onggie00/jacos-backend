<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_prestasi_siswa_sd extends MY_Model {

    private $primary_key    = 'id_prestasi';
    private $table_name     = 'prestasi_siswa_sd';
    private $field_search   = array('nama_prestasi', 'tgl_raih', 'juara', 'file_prestasi', 'foto_prestasi', 'id_siswa', 'jenis_prestasi_id', 'konten', 'tanggal_posting', 'is_approved');

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
     * Hitung total baris berdasarkan filter array.
     * Filter keys: q, f, id_siswa, jenis_prestasi_id, id_prestasi_bidang,
     *              is_approved, tgl_raih_from, tgl_raih_to, kurasi_pusprenas.
     */
    public function count_all($filters = array())
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from($this->table_name);
        $this->join_avaiable();
        $this->apply_filters($filters);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return (int)$query->row()->total;
        }
        return 0;
    }

    /**
     * Ambil data prestasi_siswa_sd dengan filter array.
     *
     * @param array  $filters         Array key-value filter (lihat apply_filters).
     * @param int    $limit           LIMIT (0 = no limit).
     * @param int    $offset          OFFSET.
     * @param array  $select_field    Custom SELECT fields (kosongkan untuk default).
     * @param bool   $exclude_mutasi  TRUE untuk exclude kelas berlabel "mutasi" (dipakai export).
     * @return array Object result.
     */
    public function get($filters = array(), $limit = 0, $offset = 0, $select_field = array(), $exclude_mutasi = false)
    {
        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable();

        if ($exclude_mutasi) {
            $this->db->where('kelas_sd.label NOT LIKE', '%mutasi%');
        }

        $this->apply_filters($filters);

        $this->db->order_by('prestasi_siswa_sd.' . $this->primary_key, 'DESC');
        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Bangun WHERE clause dari filter array.
     * Digunakan oleh get() dan count_all() — DRY.
     */
    private function apply_filters($filters = array())
    {
        if (!is_array($filters)) {
            return;
        }

        // 1. Keyword search + field selector
        if (!empty($filters['q']) && !empty($filters['f'])) {
            $f = $filters['f'];
            $field_map = array(
                'nama_lengkap'     => 'siswa_sd_aktif.nama_lengkap',
                'kelas'            => 'kelas_sd.label',
                'jenis_prestasi'   => 'jenis_prestasi.jenis_prestasi',
                'nama_bidang'      => 'prestasi_siswa_bidang.nama_bidang',
            );
            $actual_field = isset($field_map[$f]) ? $field_map[$f] : 'prestasi_siswa_sd.' . $f;
            $this->db->like($actual_field, $filters['q']);
        } elseif (!empty($filters['q'])) {
            // Search all configured fields (backward compat dgn mode tanpa field selector)
            $iterasi = 1;
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $this->db->like('prestasi_siswa_sd.' . $f, $filters['q']);
                } else {
                    $this->db->or_like('prestasi_siswa_sd.' . $f, $filters['q']);
                }
                $iterasi++;
            }
            $this->db->or_like('siswa_sd_aktif.nama_lengkap', $filters['q']);
            $this->db->or_like('kelas_sd.label', $filters['q']);
            $this->db->or_like('jenis_prestasi.jenis_prestasi', $filters['q']);
        }

        // 2. FK filters
        if (!empty($filters['id_siswa'])) {
            $this->db->where('prestasi_siswa_sd.id_siswa', (int)$filters['id_siswa']);
        }
        if (!empty($filters['jenis_prestasi_id'])) {
            $this->db->where('prestasi_siswa_sd.jenis_prestasi_id', (int)$filters['jenis_prestasi_id']);
        }
        if (!empty($filters['id_prestasi_bidang'])) {
            $this->db->where('prestasi_siswa_sd.id_prestasi_bidang', (int)$filters['id_prestasi_bidang']);
        }

        // 3. Status approval
        if (isset($filters['is_approved']) && $filters['is_approved'] !== '' && $filters['is_approved'] !== null) {
            $this->db->where('prestasi_siswa_sd.is_approved', (int)$filters['is_approved']);
        }

        // 4. Tanggal raih range
        if (!empty($filters['tgl_raih_from'])) {
            $this->db->where('prestasi_siswa_sd.tgl_raih >=', $filters['tgl_raih_from']);
        }
        if (!empty($filters['tgl_raih_to'])) {
            $this->db->where('prestasi_siswa_sd.tgl_raih <=', $filters['tgl_raih_to']);
        }

        // 5. Kurasi Pusprenas
        if (!empty($filters['kurasi_pusprenas'])) {
            $this->db->where('prestasi_siswa_sd.kurasi_pusprenas', $filters['kurasi_pusprenas']);
        }
    }

    /**
     * JOIN dengan tabel referensi + base SELECT.
     * Dipanggil oleh get(), count_all(), dan view() (via controller).
     */
    public function join_avaiable() {
        $this->db->join('siswa_sd_aktif', 'siswa_sd_aktif.id_siswa_sd_aktif = prestasi_siswa_sd.id_siswa', 'LEFT');
        $this->db->join('kelas_sd', 'kelas_sd.id_kelas_sd = siswa_sd_aktif.id_kelas', 'LEFT');
        $this->db->join('jenis_prestasi', 'jenis_prestasi.id_jenis_prestasi = prestasi_siswa_sd.jenis_prestasi_id', 'LEFT');
        $this->db->join('prestasi_siswa_bidang', 'prestasi_siswa_bidang.id_prestasi_bidang = prestasi_siswa_sd.id_prestasi_bidang', 'LEFT');

        $this->db->select('prestasi_siswa_sd.*, siswa_sd_aktif.nama_lengkap as siswa_sd_aktif_nama_lengkap, jenis_prestasi.jenis_prestasi, kelas_sd.label as kelas_sd_label, prestasi_siswa_bidang.nama_bidang');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_prestasi_siswa_sd.php */
/* Location: ./application/models/Model_prestasi_siswa_sd.php */
