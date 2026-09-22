<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_cron_setting extends MY_Model {

    private $primary_key    = 'id_cron_setting';
    private $table_name     = 'cron_setting';
    private $field_search   = ['nama_cron', 'id_siswa_aktif', 'jenjang', 'id_kelas', 'is_active', 'date_deactivate', 'date_reactivate'];

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
                    $where .= "cron_setting.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "cron_setting.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sd_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_smp.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_ft.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "cron_setting.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "siswa_sd_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_smp.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_ft.label LIKE '%" . $q . "%' ";
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
                    $where .= "cron_setting.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "cron_setting.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sd_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_smp.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_ft.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "cron_setting.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR " . "siswa_sd_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_smp_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_sma_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "siswa_ft_aktif.nama_lengkap LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sd.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_smp.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_sma.label LIKE '%" . $q . "%' ";
            $where .= "OR " . "kelas_ft.label LIKE '%" . $q . "%' ";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('cron_setting.id_kelas', "ASC");
                $this->db->order_by('cron_setting.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('siswa_sd_aktif', 'siswa_sd_aktif.id_siswa_sd_aktif = cron_setting.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_sd', 'kelas_sd.id_kelas_sd = cron_setting.id_kelas', 'LEFT');
        $this->db->join('siswa_smp_aktif', 'siswa_smp_aktif.id_siswa_smp_aktif = cron_setting.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_smp', 'kelas_smp.id_kelas_smp = cron_setting.id_kelas', 'LEFT');
        $this->db->join('siswa_sma_aktif', 'siswa_sma_aktif.id_siswa_sma_aktif = cron_setting.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_sma', 'kelas_sma.id_kelas_sma = cron_setting.id_kelas', 'LEFT');
        $this->db->join('siswa_ft_aktif', 'siswa_ft_aktif.id_siswa_ft_aktif = cron_setting.id_siswa_aktif', 'LEFT');
        $this->db->join('kelas_ft', 'kelas_ft.id_kelas_ft = cron_setting.id_kelas', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = cron_setting.updated_by', 'LEFT');
        
        $this->db->select('cron_setting.*,
        siswa_sd_aktif.nama_lengkap as siswa_sd_aktif_nama_lengkap,kelas_sd.label as kelas_sd_nama_kelas,
        siswa_smp_aktif.nama_lengkap as siswa_smp_aktif_nama_lengkap,kelas_smp.label as kelas_smp_nama_kelas,
        siswa_sma_aktif.nama_lengkap as siswa_sma_aktif_nama_lengkap,kelas_sma.label as kelas_sma_nama_kelas,
        siswa_ft_aktif.nama_lengkap as siswa_ft_aktif_nama_lengkap,kelas_ft.label as kelas_ft_nama_kelas,
        aauth_users.email as aauth_users_email');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_cron_setting.php */
/* Location: ./application/models/Model_cron_setting.php */