<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_acara extends MY_Model {

    private $primary_key    = 'id_acara';
    private $table_name     = 'acara';
    private $field_search   = ['peserta_acara', 'nama_acara', 'unique_code', 'unique_code_finish', 'keterangan', 'qr_code', 'qr_code_finish', 'waktu_mulai', 'waktu_selesai', 'lokasi', 'is_certificated', 'no_certificate', 'file_certificate', 'file_certificate_back'];

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
                    $where .= "acara.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "acara.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "acara.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "acara.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('acara.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('acara.*');


        return $this;
    }

    /**
     * Get role breakdown for chart
     * 
     * @param int $id_acara
     * @return array
     */
    public function get_role_breakdown($id_acara)
    {
        $id_acara = (int) $id_acara;
        
        // Hitung per role: total, sudah, belum
        $sql = "SELECT 
                    ap.role,
                    COUNT(DISTINCT ap.npp) as total,
                    COUNT(DISTINCT CASE WHEN ape.npp IS NOT NULL THEN ap.npp END) as sudah,
                    COUNT(DISTINCT CASE WHEN ape.npp IS NULL THEN ap.npp END) as belum
                FROM acara_presensi ap
                LEFT JOIN acara_presensi_evaluasi ape 
                    ON ap.id_acara = ape.id_acara AND ap.npp = ape.npp
                WHERE ap.id_acara = ?
                GROUP BY ap.role
                ORDER BY ap.role";
        
        return $this->db->query($sql, array($id_acara))->result();
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Get realtime evaluation status for an acara
     * 
     * @param int $id_acara
     * @return array
     */
    public function get_status_realtime($id_acara)
    {
        $id_acara = (int) $id_acara;
        
        // Total wajib isi = semua peserta di acara_presensi
        $this->db->where('id_acara', $id_acara);
        $total_wajib = $this->db->count_all_results('acara_presensi');
        
        // Total sudah isi = DISTINCT npp yang punya baris di acara_presensi_evaluasi
        $this->db->select('COUNT(DISTINCT npp) as cnt');
        $this->db->where('id_acara', $id_acara);
        $row = $this->db->get('acara_presensi_evaluasi')->row();
        $total_sudah = (int) $row->cnt;
        
        $total_belum = $total_wajib - $total_sudah;
        if ($total_belum < 0) $total_belum = 0;
        
        // List sudah isi: LEFT JOIN dari acara_presensi ke evaluasi
        // Ambil yang punya baris evaluasi (INNER JOIN sebenarnya, tapi kita pakai subquery)
        $this->db->select('ap.npp, ap.peserta, ap.role');
        $this->db->select('MIN(ape.created_at) as waktu_submit_pertama');
        $this->db->select('MAX(ape.created_at) as waktu_submit_terakhir');
        $this->db->from('acara_presensi ap');
        $this->db->join('acara_presensi_evaluasi ape', 'ap.id_acara = ape.id_acara AND ap.npp = ape.npp', 'inner');
        $this->db->where('ap.id_acara', $id_acara);
        $this->db->group_by('ap.npp, ap.peserta, ap.role');
        $this->db->order_by('waktu_submit_terakhir', 'DESC');
        $list_sudah = $this->db->get()->result();
        
        // List belum isi: LEFT JOIN, cari yang NULL
        $this->db->select('ap.npp, ap.peserta, ap.role');
        $this->db->from('acara_presensi ap');
        $this->db->join('acara_presensi_evaluasi ape', 'ap.id_acara = ape.id_acara AND ap.npp = ape.npp', 'left');
        $this->db->where('ap.id_acara', $id_acara);
        $this->db->where('ape.npp IS NULL', null, false);
        $this->db->group_by('ap.npp, ap.peserta, ap.role');
        $this->db->order_by('ap.peserta', 'ASC');
        $list_belum = $this->db->get()->result();
        
        return array(
            'total_wajib_isi'  => $total_wajib,
            'total_sudah_isi'  => $total_sudah,
            'total_belum_isi' => $total_belum,
            'list_sudah_isi'   => $list_sudah,
            'list_belum_isi'   => $list_belum
        );
    }

    /**
     * Get activity feed - recent unique submissions
     * 
     * @param int $id_acara
     * @param int $limit
     * @return array
     */
    public function get_activity_feed($id_acara, $limit = 10)
    {
        $id_acara = (int) $id_acara;
        $limit = (int) $limit;
        
        // Ambil peserta unik dengan waktu submit terbaru
        // GROUP BY npp supaya satu peserta hanya muncul sekali
        $this->db->select('ape.npp, ap.peserta, ap.role');
        $this->db->select('MAX(ape.created_at) as waktu_submit');
        $this->db->from('acara_presensi_evaluasi ape');
        $this->db->join('acara_presensi ap', 'ape.id_acara = ap.id_acara AND ape.npp = ap.npp', 'left');
        $this->db->where('ape.id_acara', $id_acara);
        $this->db->group_by('ape.npp, ap.peserta, ap.role');
        $this->db->order_by('waktu_submit', 'DESC');
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Get acara detail with question count
     * 
     * @param int $id_acara
     * @return object|null
     */
    public function get_acara_detail($id_acara)
    {
        $id_acara = (int) $id_acara;
        
        // Ambil data acara
        $acara = $this->db->get_where('acara', array('id_acara' => $id_acara))->row();
        if (!$acara) return null;
        
        // Hitung jumlah pertanyaan evaluasi
        $this->db->where('id_acara', $id_acara);
        $acara->jumlah_pertanyaan = $this->db->count_all_results('acara_evaluasi_form');
        
        return $acara;
    }

}

/* End of file Model_acara.php */
/* Location: ./application/models/Model_acara.php */