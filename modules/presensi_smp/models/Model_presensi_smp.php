<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_smp extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'presensi_smp';
    private $field_search   = ['id_siswa_aktif', 'wifi_ssid', 'wifi_ip', 'hari_absen', 'waktu_absen', 'tanggal_absen', 'status_absen', 'alasan_terlambat', 'id_izin'];

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
                    $where .= "presensi_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_smp.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "presensi_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->select("(SELECT COUNT(*) FROM presensi_catatan_pelajaran pcp WHERE pcp.id_siswa_aktif = presensi_smp.id_siswa_aktif AND pcp.jenjang = 'smp' AND pcp.tanggal_waktu >= presensi_smp.tanggal_absen AND pcp.tanggal_waktu < DATE_ADD(presensi_smp.tanggal_absen, INTERVAL 1 DAY)) AS jml_catatan", FALSE);
        $this->db->limit($limit, $offset);
                $this->db->order_by('presensi_smp.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = presensi_smp.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_smp', 'siswa_smp_aktif.id_kelas = kelas_smp.id_kelas_smp', 'LEFT');
        $this->db->join('pengaturan_whitelist_ssid', 'pengaturan_whitelist_ssid.nama_ssid = presensi_smp.wifi_ssid', 'LEFT');
        $this->db->join('izin_siswa_smp', 'izin_siswa_smp.id = presensi_smp.id_izin', 'LEFT');
        
        $this->db->select('presensi_smp.*,siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap, siswa_smp_aktif.nis, kelas_smp.label as nama_kelas,pengaturan_whitelist_ssid.nama_ssid as pengaturan_whitelist_ssid_nama_ssid,izin_siswa_smp.jenis_izin as izin_siswa_smp_jenis_izin');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Agregasi jumlah status_absen sesuai filter (q, field) yang sama dengan get().
     * Digunakan untuk info-box rekap di listing presensi_smp.
     */
    public function get_status_summary($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $col) {
                if ($iterasi == 1) {
                    $where .= "presensi_smp.".$col . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR presensi_smp.".$col . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        } else {
            $where .= "(" . "presensi_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        $sql = "SELECT 
            SUM(status_absen='Hadir') AS total_hadir,
            SUM(status_absen='Terlambat') AS total_terlambat,
            SUM(status_absen='Izin') AS total_izin,
            SUM(status_absen='Sakit') AS total_sakit,
            SUM(status_absen IN ('Alfa','Tidak Hadir')) AS total_alfa
        FROM presensi_smp WHERE ".$where;

        $query = $this->db->query($sql);
        return $query->row();
    }

}

/* End of file Model_presensi_smp.php */
/* Location: ./application/models/Model_presensi_smp.php */