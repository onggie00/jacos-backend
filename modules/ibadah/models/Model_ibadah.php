<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_ibadah extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'ibadah';
    private $field_search   = array('id_siswa_aktif', 'jenjang', 'id_ibadah', 'tanggal', 'waktu', 'jumlah_rakaat', 'keterangan');

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    private function _build_search_where($q, $field)
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($q)) return NULL;

        // kolom yang bisa di-search di tabel join
        $join_cols = array(
            'siswa_sma_aktif.nama_lengkap',
            'siswa_ft_aktif.nama_lengkap',
            'siswa_smp_aktif.nama_lengkap',
            'kelas_sma.label',
            'kelas_ft.label',
            'kelas_smp.label',
            'ibadah_setting.ibadah',
        );

        if (!empty($field)) {
            // search di kolom tertentu + join tables
            $parts = array("ibadah." . $field . " LIKE '%{$q}%'");
            foreach ($join_cols as $col) {
                $parts[] = "{$col} LIKE '%{$q}%'";
            }
            return '(' . implode(' OR ', $parts) . ')';
        }

        // search semua kolom ibadah + join tables
        $parts = array();
        foreach ($this->field_search as $f) {
            $parts[] = "ibadah.{$f} LIKE '%{$q}%'";
        }
        foreach ($join_cols as $col) {
            $parts[] = "{$col} LIKE '%{$q}%'";
        }
        return '(' . implode(' OR ', $parts) . ')';
    }

    public function count_all($q = null, $field = null)
    {
        $where = $this->_build_search_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if ($where) $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array())
    {
        $where = $this->_build_search_where($q, $field);

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        if ($where) $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('ibadah.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Count + get untuk filter_presensi (query manual via withquery)
     * Dipecah supaya controller bisa hitung total_rows tanpa query berat dua kali
     */
    public function count_filter_presensi($jenjang, $where)
    {
        $sql = "SELECT COUNT(*) as cnt FROM ibadah i
            JOIN siswa_{$jenjang}_aktif s ON i.id_siswa_aktif = s.id_siswa_{$jenjang}_aktif
            JOIN kelas_{$jenjang} k ON s.id_kelas = k.id_kelas_{$jenjang}
            JOIN ibadah_setting ib ON i.id_ibadah = ib.id
            {$where}";
        $row = $this->mymodel->withquery($sql, "row");
        return $row ? intval($row->cnt) : 0;
    }

    public function join_avaiable() {
        $this->db->join('siswa_sma_aktif', 'siswa_sma_aktif.id_siswa_sma_aktif = ibadah.id_siswa_aktif', 'LEFT');
        $this->db->join('siswa_ft_aktif', 'siswa_ft_aktif.id_siswa_ft_aktif = ibadah.id_siswa_aktif', 'LEFT');
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = ibadah.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_sma', 'kelas_sma.id_kelas_sma = siswa_sma_aktif.id_kelas', 'LEFT');
        $this->db->join('kelas_ft', 'kelas_ft.id_kelas_ft = siswa_ft_aktif.id_kelas', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = siswa_smp_aktif.id_kelas', 'LEFT');
        $this->db->join('ibadah_setting', 'ibadah_setting.id = ibadah.id_ibadah', 'LEFT');
        
        $this->db->select('ibadah.*, siswa_sma_aktif.nama_lengkap as siswa_sma_aktif_nama_lengkap, ibadah_setting.ibadah as ibadah_setting_ibadah, siswa_ft_aktif.nama_lengkap as siswa_ft_aktif_nama_lengkap, kelas_sma.label as nama_kelas_sma, kelas_ft.label as nama_kelas_ft, siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap, kelas_smp.label as nama_kelas_smp');

        return $this;
    }

    public function filter_avaiable() {
        if (!$this->aauth->is_admin()) {
        }
        return $this;
    }

    /**
     * Stats untuk infobox di list utama
     */
    public function get_stats()
    {
        $stats = new stdClass();
        $stats->total = $this->db->count_all($this->table_name);
        $stats->hari_ini = $this->db->where('tanggal', date('Y-m-d'))->count_all_results($this->table_name);
        // minggu ini (Senin - Minggu)
        $monday = date('Y-m-d', strtotime('monday this week'));
        $sunday = date('Y-m-d', strtotime('sunday this week'));
        $stats->minggu_ini = $this->db->where('tanggal >=', $monday)->where('tanggal <=', $sunday)->count_all_results($this->table_name);
        // siswa unik yang pernah ibadah
        $q = $this->db->query('SELECT COUNT(DISTINCT id_siswa_aktif) as cnt FROM ibadah');
        $row = $q->row();
        $stats->siswa_aktif = $row ? intval($row->cnt) : 0;
        return $stats;
    }

    /**
     * Stats untuk infobox filter_presensi
     */
    public function get_stats_filter($jenjang, $where)
    {
        $stats = new stdClass();
        $base = "FROM ibadah i
            JOIN siswa_{$jenjang}_aktif s ON i.id_siswa_aktif = s.id_siswa_{$jenjang}_aktif
            JOIN kelas_{$jenjang} k ON s.id_kelas = k.id_kelas_{$jenjang}
            JOIN ibadah_setting ib ON i.id_ibadah = ib.id
            {$where}";

        $r = $this->mymodel->withquery("SELECT COUNT(*) as cnt {$base}", 'row');
        $stats->total = $r ? intval($r->cnt) : 0;

        $r = $this->mymodel->withquery("SELECT COUNT(DISTINCT i.id_siswa_aktif) as cnt {$base}", 'row');
        $stats->siswa_unik = $r ? intval($r->cnt) : 0;

        $r = $this->mymodel->withquery("SELECT MIN(i.tanggal) as tgl {$base}", 'row');
        $stats->tanggal_mulai = ($r && $r->tgl) ? $r->tgl : null;

        $r = $this->mymodel->withquery("SELECT MAX(i.tanggal) as tgl {$base}", 'row');
        $stats->tanggal_akhir = ($r && $r->tgl) ? $r->tgl : null;

        return $stats;
    }

}

/* End of file Model_ibadah.php */
/* Location: ./application/models/Model_ibadah.php */