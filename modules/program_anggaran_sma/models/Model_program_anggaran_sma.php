<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_program_anggaran_sma extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'program_anggaran_sma';
    private $field_search   = array('nomor_program', 'tahun_ajaran', 'nama_program', 'tanggal_program', 'jenis_kegiatan', 'sub_jenis_kegiatan', 'nominal_okr', 'nominal_pengajuan', 'file_proposal_pengajuan', 'file_proposal_keuangan', 'status_pengajuan', 'tanggal_pencairan', 'catatan_pengajuan', 'file_laporan_kegiatan', 'file_laporan_keuangan', 'status_laporan', 'catatan_laporan');

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
                    $where .= "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "program_anggaran_status_pengajuan.status LIKE '%" . $q . "%' ";
                    $where .= "OR " . "program_anggaran_status_laporan.status LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if($field == "status_pengajuan"){
                $where .= "(" . "program_anggaran_status_pengajuan.status LIKE '%" . $q . "%' )";
            }
            else if($field == "status_laporan"){
                $where .= "(" . "program_anggaran_status_laporan.status LIKE '%" . $q . "%' )";
            }
            else{
                $where .= "(" . "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->group_by('program_anggaran_sma.id');
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array())
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "program_anggaran_status_pengajuan.status LIKE '%" . $q . "%' ";
                    $where .= "OR " . "program_anggaran_status_laporan.status LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if($field == "status_pengajuan"){
                $where .= "(" . "program_anggaran_status_pengajuan.status LIKE '%" . $q . "%' )";
            }
            else if($field == "status_laporan"){
                $where .= "(" . "program_anggaran_status_laporan.status LIKE '%" . $q . "%' )";
            }
            else{
                $where .= "(" . "program_anggaran_sma.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->group_by('program_anggaran_sma.id');
        $this->db->limit($limit, $offset);
                $this->db->order_by('program_anggaran_sma.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Whitelist filter fields
     */
    private $filterable_fields = array('nomor_program', 'jenis_kegiatan', 'sub_jenis_kegiatan', 'nama_program', 'tahun_ajaran', 'tanggal_program', 'nominal_okr', 'nominal_pengajuan', 'status_pengajuan', 'status_laporan', 'tanggal_pencairan');

    /**
     * Whitelist filter operators
     */
    private $filter_operators = array('contains', 'equals', 'starts_with', 'ends_with', 'gt', 'lt');

    /**
     * Resolusi nama field ke nama kolom SQL
     */
    private function _resolve_column($field)
    {
        if ($field === 'status_pengajuan') {
            return 'program_anggaran_sma.status_pengajuan';
        }
        if ($field === 'status_laporan') {
            return 'program_anggaran_sma.status_laporan';
        }
        return 'program_anggaran_sma.' . $field;
    }

    /**
     * Bangun WHERE clause multi filter + search
     */
    private function _build_filter_where($filters = array(), $search = null)
    {
        // Terapkan multi filter
        if (is_array($filters) && count($filters) > 0) {
            foreach ($filters as $filter) {
                $field    = isset($filter['field']) ? $filter['field'] : '';
                $operator = isset($filter['operator']) ? $filter['operator'] : '';
                $value    = isset($filter['value']) ? $filter['value'] : '';

                if (!in_array($field, $this->filterable_fields)) {
                    continue;
                }
                if (!in_array($operator, $this->filter_operators)) {
                    continue;
                }
                if ($value === '' || $value === null) {
                    continue;
                }

                $column = $this->_resolve_column($field);

                switch ($operator) {
                    case 'equals':
                        $this->db->where($column, $value);
                        break;
                    case 'contains':
                        $this->db->like($column, $value, 'both');
                        break;
                    case 'starts_with':
                        $this->db->like($column, $value, 'after');
                        break;
                    case 'ends_with':
                        $this->db->like($column, $value, 'before');
                        break;
                    case 'gt':
                        if (is_numeric($value)) {
                            $this->db->where($column . ' >', (float)$value);
                        }
                        break;
                    case 'lt':
                        if (is_numeric($value)) {
                            $this->db->where($column . ' <', (float)$value);
                        }
                        break;
                }
            }
        }

        // Terapkan search keyword (multi kolom OR)
        if (!empty($search)) {
            $search = $this->scurity($search);
            $iterasi = 1;
            $where_search = NULL;
            foreach ($this->field_search as $field) {
                if ($field === 'status_pengajuan') {
                    $column = 'program_anggaran_status_pengajuan.status';
                } elseif ($field === 'status_laporan') {
                    $column = 'program_anggaran_status_laporan.status';
                } else {
                    $column = 'program_anggaran_sma.' . $field;
                }

                if ($iterasi == 1) {
                    $where_search .= $column . " LIKE '%" . $search . "%' ";
                } else {
                    $where_search .= "OR " . $column . " LIKE '%" . $search . "%' ";
                }
                $iterasi++;
            }
            $where_search = '(' . $where_search . ')';
            $this->db->where($where_search);
        }
    }

    /**
     * Hitung jumlah data hasil filter
     */
    public function count_filtered($filters = array(), $search = null)
    {
        $this->join_avaiable()->filter_avaiable();
        $this->_build_filter_where($filters, $search);
        $this->db->group_by('program_anggaran_sma.id');
        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    /**
     * Ambil data hasil filter + search
     */
    public function get_filtered($filters = array(), $search = null, $limit = 0, $offset = 0, $select_field = array())
    {
        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->_build_filter_where($filters, $search);
        $this->db->group_by('program_anggaran_sma.id');

        if ((int)$limit > 0) {
            $this->db->limit((int)$limit, (int)$offset);
        }
        $this->db->order_by('program_anggaran_sma.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('program_anggaran', 'program_anggaran.id = program_anggaran_sma.nomor_program', 'LEFT');
        $this->db->join('program_anggaran_status_pengajuan', 'program_anggaran_status_pengajuan.id_program_anggaran_status_pengajuan = program_anggaran_sma.status_pengajuan', 'LEFT');
        $this->db->join('program_anggaran_status_laporan', 'program_anggaran_status_laporan.id_program_anggaran_status_laporan = program_anggaran_sma.status_laporan', 'LEFT');

        $this->db->select('program_anggaran_sma.*,program_anggaran.nomor_program as program_anggaran_nomor_program,program_anggaran_status_pengajuan.status as program_anggaran_status_pengajuan_status,program_anggaran_status_laporan.status as program_anggaran_status_laporan_status');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_program_anggaran_sma.php */
/* Location: ./application/models/Model_program_anggaran_sma.php */