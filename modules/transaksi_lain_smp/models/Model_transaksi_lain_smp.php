<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_transaksi_lain_smp extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'transaksi_lain_smp';
    private $field_search   = ['id_siswa_aktif', 'id_transaksi_lain', 'tanggal_bayar', 'va_number', 'kode_tagihan', 'file_kwitansi', 'nominal_bayar', 'status_transaksi', 'expired_at'];

    // ponytail: field mapping for joined table search
    private $field_joins = array(
        'nama_siswa' => 'siswa_smp_aktif.nama_lengkap',
        'nama_kelas' => 'kelas_smp.label',
        'nama_transaksi' => 'transaksi_lain_smp_manajemen.nama_transaksi',
        'va_number' => 'transaksi_lain_smp.va_number',
        'kode_tagihan' => 'transaksi_lain_smp.kode_tagihan',
        'status_transaksi' => 'transaksi_lain_smp.status_transaksi'
    );

    // ponytail: status text to value mapping
    private $status_map = array(
        'belum dibayar' => '0',
        'menunggu pembayaran' => '1',
        'lunas' => '2',
        'kadaluarsa' => '3',
        'expired' => '3'
    );

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
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
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
        if (!empty($where)) $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('transaksi_lain_smp.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = transaksi_lain_smp.id_siswa_aktif', 'LEFT');
        $this->db->join('transaksi_lain_smp_manajemen', 'transaksi_lain_smp_manajemen.id = transaksi_lain_smp.id_transaksi_lain', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = siswa_smp_aktif.id_kelas', 'LEFT');
        
        $this->db->select('transaksi_lain_smp.*,siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap,transaksi_lain_smp_manajemen.nama_transaksi as transaksi_lain_smp_manajemen_nama_transaksi, kelas_smp.label as nama_kelas, transaksi_lain_smp_manajemen.tipe_bank as tipe_bank');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Get aggregate stats for infobox
     */
    public function get_stats($q = null, $field = null)
    {
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->select('
            COUNT(*) as total_transaksi,
            SUM(transaksi_lain_smp.nominal_bayar) as total_nominal,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 0 THEN 1 ELSE 0 END) as belum_bayar,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 1 THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 2 THEN 1 ELSE 0 END) as lunas,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 3 THEN 1 ELSE 0 END) as kadaluarsa,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 2 THEN transaksi_lain_smp.nominal_bayar ELSE 0 END) as nominal_lunas
        ');
        $query = $this->db->get($this->table_name);
        return $query->row();
    }

    /**
     * Get chart data grouped by tagihan name + status
     */
    public function get_chart_data($q = null, $field = null)
    {
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->select('
            transaksi_lain_smp_manajemen.nama_transaksi as nama_tagihan,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 0 THEN 1 ELSE 0 END) as belum_bayar,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 1 THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 2 THEN 1 ELSE 0 END) as lunas,
            SUM(CASE WHEN transaksi_lain_smp.status_transaksi = 3 THEN 1 ELSE 0 END) as kadaluarsa,
            SUM(transaksi_lain_smp.nominal_bayar) as total_nominal
        ');
        $this->db->group_by('transaksi_lain_smp_manajemen.nama_transaksi');
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    /**
     * Build WHERE clause from filter params
     * Handles: specific field search, status text, all-column search
     */
    private function _build_where($q = null, $field = null)
    {
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($q)) return NULL;

        // Status text search
        $status_val = null;
        foreach ($this->status_map as $text => $val) {
            if (strpos($q, $text) !== false) {
                $status_val = $val;
                break;
            }
        }

        if (!empty($field) && isset($this->field_joins[$field])) {
            // Specific field filter
            $col = $this->field_joins[$field];
            $where = "(" . $col . " LIKE '%" . $q . "%' )";
            // Also check status text for status_transaksi field
            if ($field == 'status_transaksi' && $status_val !== null) {
                $where = "(transaksi_lain_smp.status_transaksi = '" . $status_val . "')";
            }
        } elseif (empty($field)) {
            // All-column search
            $parts = array();
            $parts[] = "transaksi_lain_smp.id_siswa_aktif LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp.id_transaksi_lain LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp.tanggal_bayar LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp.va_number LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp.kode_tagihan LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp.nominal_bayar LIKE '%" . $q . "%'";
            $parts[] = "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%'";
            $parts[] = "kelas_smp.label LIKE '%" . $q . "%'";
            $parts[] = "transaksi_lain_smp_manajemen.nama_transaksi LIKE '%" . $q . "%'";
            if ($status_val !== null) {
                $parts[] = "transaksi_lain_smp.status_transaksi = '" . $status_val . "'";
            }
            $where = '(' . implode(' OR ', $parts) . ')';
        } else {
            // Specific field but not in field_joins
            $where = "(transaksi_lain_smp." . $field . " LIKE '%" . $q . "%' )";
        }

        return $where;
    }

}

/* End of file Model_transaksi_lain_smp.php */
/* Location: ./application/models/Model_transaksi_lain_smp.php */
