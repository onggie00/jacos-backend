<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_realtime_tracker extends MY_Model {

    private $primary_key    = 'id_acara';
    private $table_name     = 'acara';

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => array(),
         );

        parent::__construct($config);
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
        
        // List sudah isi
        $this->db->select('ap.npp, ap.peserta, ap.role');
        $this->db->select('MIN(ape.created_at) as waktu_submit_pertama');
        $this->db->select('MAX(ape.created_at) as waktu_submit_terakhir');
        $this->db->from('acara_presensi ap');
        $this->db->join('acara_presensi_evaluasi ape', 'ap.id_acara = ape.id_acara AND ap.npp = ape.npp', 'inner');
        $this->db->where('ap.id_acara', $id_acara);
        $this->db->group_by('ap.npp, ap.peserta, ap.role');
        $this->db->order_by('waktu_submit_terakhir', 'DESC');
        $list_sudah = $this->db->get()->result();
        
        // List belum isi
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
        
        $acara = $this->db->get_where('acara', array('id_acara' => $id_acara))->row();
        if (!$acara) return null;
        
        $this->db->where('id_acara', $id_acara);
        $acara->jumlah_pertanyaan = $this->db->count_all_results('acara_evaluasi_form');
        
        return $acara;
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

}

/* End of file Model_realtime_tracker.php */
/* Location: ./modules/acara_presensi_realtime_tracker/models/Model_realtime_tracker.php */
