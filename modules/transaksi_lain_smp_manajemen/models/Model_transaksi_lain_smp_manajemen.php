<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_transaksi_lain_smp_manajemen extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'transaksi_lain_smp_manajemen';
    private $field_search   = ['id_kategori', 'nama_transaksi', 'keterangan', 'nominal', 'id_tahun_ajaran', 'id_tingkatan', 'id_kelas', 'id_siswa', 'tanggal_tagihan_mulai', 'tanggal_tagihan_selesai', 'tipe_bank'];
    // ponytail: field mapping for joined table search
    private $field_joins = array(
        'nama_siswa' => 'siswa_smp_aktif.nama_lengkap',
        'nama_kelas' => 'kelas_smp.nama_kelas',
        'tahun_ajaran' => 'tahun_ajaran.label'
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
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($field) && isset($this->field_joins[$field])) {
            $col = $this->field_joins[$field];
            $where = "(" . $col . " LIKE '%" . $q . "%' )";
        } elseif (empty($field)) {
            foreach ($this->field_search as $f) {
                $prefix = ($iterasi == 1) ? '' : 'OR ';
                $where .= $prefix . "transaksi_lain_smp_manajemen." . $f . " LIKE '%" . $q . "%' ";
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where = "(transaksi_lain_smp_manajemen." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($field) && isset($this->field_joins[$field])) {
            $col = $this->field_joins[$field];
            $where = "(" . $col . " LIKE '%" . $q . "%' )";
        } elseif (empty($field)) {
            foreach ($this->field_search as $f) {
                $prefix = ($iterasi == 1) ? '' : 'OR ';
                $where .= $prefix . "transaksi_lain_smp_manajemen." . $f . " LIKE '%" . $q . "%' ";
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where = "(transaksi_lain_smp_manajemen." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('transaksi_lain_smp_manajemen.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('transaksi_lain_kategori', 'transaksi_lain_kategori.id_kategori = transaksi_lain_smp_manajemen.id_kategori', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = transaksi_lain_smp_manajemen.id_tahun_ajaran', 'LEFT');
        $this->db->join('tingkatan_smp', 'tingkatan_smp.id_tingkatan_smp = transaksi_lain_smp_manajemen.id_tingkatan', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = transaksi_lain_smp_manajemen.id_kelas', 'LEFT');
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = transaksi_lain_smp_manajemen.id_siswa', 'LEFT');
        
        $this->db->select('transaksi_lain_smp_manajemen.*,transaksi_lain_kategori.nama_kategori as transaksi_lain_kategori_nama_kategori,tahun_ajaran.label as tahun_ajaran_label,tingkatan_smp.label as tingkatan_smp_label,kelas_smp.nama_kelas as kelas_smp_nama_kelas,siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap');


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
        $this->db->select('COUNT(*) as total_tagihan, SUM(transaksi_lain_smp_manajemen.nominal) as total_nominal, COUNT(DISTINCT transaksi_lain_smp_manajemen.id_kelas) as total_kelas');
        $query = $this->db->get($this->table_name);
        return $query->row();
    }

    /**
     * Get chart data grouped by kategori
     */
    public function get_chart_data($q = null, $field = null)
    {
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->select('transaksi_lain_kategori.nama_kategori, SUM(transaksi_lain_smp_manajemen.nominal) as total_nominal, COUNT(*) as jumlah_tagihan');
        $this->db->group_by('transaksi_lain_kategori.nama_kategori');
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    /**
     * Export with filter support
     */
    public function export_filtered($filename = 'export', $title = 'export', $q = null, $field = null)
    {
        $where = $this->_build_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where)) $this->db->where($where);
        $this->db->order_by('transaksi_lain_smp_manajemen.id', 'DESC');
        $query = $this->db->get($this->table_name);
        $data = $query->result_array();

        $this->load->library('ciqrcode');
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $header = array('Kategori', 'Nama Tagihan', 'Keterangan', 'Nominal', 'Tahun Ajaran', 'Tingkatan', 'Kelas', 'Siswa', 'Tagihan Mulai', 'Tagihan Selesai', 'Bank');
        echo '<table border="1">';
        echo '<tr>';
        foreach ($header as $h) {
            echo '<th>' . $h . '</th>';
        }
        echo '</tr>';
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . (isset($row['nama_kategori']) ? $row['nama_kategori'] : '') . '</td>';
            echo '<td>' . $row['nama_transaksi'] . '</td>';
            echo '<td>' . $row['keterangan'] . '</td>';
            echo '<td>' . number_format($row['nominal'], 0, ',', '.') . '</td>';
            echo '<td>' . (isset($row['tahun_ajaran_label']) ? $row['tahun_ajaran_label'] : '') . '</td>';
            echo '<td>' . (isset($row['tingkatan_smp_label']) ? $row['tingkatan_smp_label'] : '') . '</td>';
            echo '<td>' . (isset($row['kelas_smp_nama_kelas']) ? $row['kelas_smp_nama_kelas'] : '') . '</td>';
            echo '<td>' . (isset($row['siswa_smp_aktif_nama_lengkap']) ? $row['siswa_smp_aktif_nama_lengkap'] : '') . '</td>';
            echo '<td>' . $row['tanggal_tagihan_mulai'] . '</td>';
            echo '<td>' . $row['tanggal_tagihan_selesai'] . '</td>';
            echo '<td>' . $row['tipe_bank'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        exit;
    }

    /**
     * Build WHERE clause from filter params
     */
    private function _build_where($q = null, $field = null)
    {
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (!empty($field) && isset($this->field_joins[$field])) {
                $col = $this->field_joins[$field];
                $where = "(" . $col . " LIKE '%" . $q . "%' )";
            } elseif (empty($field)) {
                $iterasi = 1;
                foreach ($this->field_search as $f) {
                    $prefix = ($iterasi == 1) ? '' : 'OR ';
                    $where .= $prefix . "transaksi_lain_smp_manajemen." . $f . " LIKE '%" . $q . "%' ";
                    $iterasi++;
                }
                $where = '(' . $where . ')';
            } else {
                $where = "(transaksi_lain_smp_manajemen." . $field . " LIKE '%" . $q . "%' )";
            }
        }
        return $where;
    }

}

/* End of file Model_transaksi_lain_smp_manajemen.php */
/* Location: ./application/models/Model_transaksi_lain_smp_manajemen.php */