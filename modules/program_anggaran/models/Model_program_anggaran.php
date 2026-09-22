<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_program_anggaran extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'program_anggaran';
    private $field_search   = array('nomor_program', 'nama_program', 'tahun_ajaran', 'jenis_kegiatan', 'nominal_okr', 'jenjang', 'is_active');

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
                    $where .= "program_anggaran.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "program_anggaran.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "program_anggaran.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
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
                    $where .= "program_anggaran.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "program_anggaran.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "program_anggaran.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('program_anggaran.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Whitelist filter fields
     */
    private $filterable_fields = array('nomor_program', 'nama_program', 'tahun_ajaran', 'jenis_kegiatan', 'nominal_okr', 'jenjang', 'is_active');

    /**
     * Whitelist filter operators
     */
    private $filter_operators = array('contains', 'equals', 'starts_with', 'ends_with', 'gt', 'lt');

    /**
     * Mapping scope URL ke nilai jenjang yang diizinkan.
     * - null  : tidak ada filter (semua data)
     * - 'sd'  : ['SD']
     * - 'smp' : ['SMP']
     * - 'sma' : ['SMA', 'FT'] (admin SMA mengelola data SMA + FT)
     * @param string|null $jenjang_context
     * @return array
     */
    public function get_scope_values($jenjang_context = null)
    {
        if (empty($jenjang_context)) {
            return array();
        }
        $map = array(
            'sd'  => array('SD'),
            'smp' => array('SMP'),
            'sma' => array('SMA', 'FT'),
        );
        $key = strtolower($jenjang_context);
        if (!isset($map[$key])) {
            return array();
        }
        return $map[$key];
    }

    /**
     * Bangun WHERE clause multi filter
     * - Filter jenjang dikunci jika $jenjang != null
     */
    private function _build_filter_where($filters = array(), $search = null, $jenjang = null)
    {
        // Lock jenjang jika mode per-jenjang (SMA mencakup SMA + FT)
        $scope_values = $this->get_scope_values($jenjang);
        if (!empty($scope_values)) {
            if (count($scope_values) === 1) {
                $this->db->where('program_anggaran.jenjang', $scope_values[0]);
            } else {
                $this->db->where_in('program_anggaran.jenjang', $scope_values);
            }
        }

        // Terapkan multi filter
        if (is_array($filters) && count($filters) > 0) {
            foreach ($filters as $filter) {
                $field    = isset($filter['field']) ? $filter['field'] : '';
                $operator = isset($filter['operator']) ? $filter['operator'] : '';
                $value    = isset($filter['value']) ? $filter['value'] : '';

                // Validasi field
                if (!in_array($field, $this->filterable_fields)) {
                    continue;
                }
                // Validasi operator
                if (!in_array($operator, $this->filter_operators)) {
                    continue;
                }
                // Validasi value
                if ($value === '' || $value === null) {
                    continue;
                }

                switch ($operator) {
                    case 'equals':
                        $this->db->where('program_anggaran.' . $field, $value);
                        break;
                    case 'contains':
                        $this->db->like('program_anggaran.' . $field, $value, 'both');
                        break;
                    case 'starts_with':
                        $this->db->like('program_anggaran.' . $field, $value, 'after');
                        break;
                    case 'ends_with':
                        $this->db->like('program_anggaran.' . $field, $value, 'before');
                        break;
                    case 'gt':
                        if (is_numeric($value)) {
                            $this->db->where('program_anggaran.' . $field . ' >', (float)$value);
                        }
                        break;
                    case 'lt':
                        if (is_numeric($value)) {
                            $this->db->where('program_anggaran.' . $field . ' <', (float)$value);
                        }
                        break;
                }
            }
        }

        // Terapkan search keyword (multi kolom OR)
        if (!empty($search)) {
            $search = $this->scurity($search);
            $iterasi = 1;
            $num = count($this->field_search);
            $where_search = NULL;
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where_search .= "program_anggaran." . $field . " LIKE '%" . $search . "%' ";
                } else {
                    $where_search .= "OR program_anggaran." . $field . " LIKE '%" . $search . "%' ";
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
    public function count_filtered($filters = array(), $search = null, $jenjang = null)
    {
        $this->join_avaiable()->filter_avaiable();
        $this->_build_filter_where($filters, $search, $jenjang);
        $query = $this->db->get($this->table_name);
        return $query->num_rows();
    }

    /**
     * Ambil data hasil filter + search + jenjang lock
     */
    public function get_filtered($filters = array(), $search = null, $jenjang = null, $limit = 0, $offset = 0, $select_field = array())
    {
        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->_build_filter_where($filters, $search, $jenjang);

        if ((int)$limit > 0) {
            $this->db->limit((int)$limit, (int)$offset);
        }
        $this->db->order_by('program_anggaran.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {

        $this->db->select('program_anggaran.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * ============================================================
     * DASHBOARD WIDGET METHODS
     * Agregat OKR Terkumpul / Diajukan / Dicairkan per tahun ajaran.
     * ============================================================
     */

    /**
     * Tabel sumber data OKR per-jenjang.
     * FT tidak punya tabel (sesuai temuan pengecekan data, FT=0 rows).
     */
    private $dashboard_tables = array('sd' => 'program_anggaran_sd', 'smp' => 'program_anggaran_smp', 'sma' => 'program_anggaran_sma');

    /**
     * Ambil label tahun ajaran aktif dari tabel tahun_ajaran (sequence = MAX).
     * Return string label, atau NULL jika tabel kosong / tidak ada.
     */
    public function get_active_tahun_ajaran()
    {
        $row = $this->db->select('label')
            ->from('tahun_ajaran')
            ->order_by('sequence', 'DESC')
            ->limit(1)
            ->get()
            ->row();

        if (empty($row)) {
            return null;
        }
        return $row->label;
    }

    /**
     * Fallback: jika TA aktif 0 row di tabel program_anggaran,
     * ambil label dengan row terbanyak dari tabel sumber.
     * @param string|null $jenjang_context 'sd'/'smp'/'sma' atau null untuk aggregate.
     * @return string|null
     */
    public function get_fallback_tahun_ajaran($jenjang_context = null)
    {
        $labels = array();
        $tables = array();

        if (!empty($jenjang_context) && isset($this->dashboard_tables[$jenjang_context])) {
            $tables[] = $this->dashboard_tables[$jenjang_context];
        } else {
            $tables = array_values($this->dashboard_tables);
        }

        foreach ($tables as $tbl) {
            $r = $this->db->select('tahun_ajaran, COUNT(*) AS c')
                ->from($tbl)
                ->group_by('tahun_ajaran')
                ->get()
                ->result();

            foreach ($r as $row) {
                $lbl = $row->tahun_ajaran;
                if (!isset($labels[$lbl])) {
                    $labels[$lbl] = 0;
                }
                $labels[$lbl] += (int)$row->c;
            }
        }

        if (count($labels) === 0) {
            return null;
        }

        arsort($labels);
        return key($labels);
    }

    /**
     * Hitung 3 metrik dashboard pada tahun ajaran tertentu.
     *
     * Definisi:
     * - okr  : SUM(nominal_okr)
     * - ajuan: SUM(nominal_pengajuan) WHERE status_pengajuan IN (2,3,4,6)
     *          (exclude "Berkas Belum Lengkap" = 1, "Ditolak" = 5)
     * - cair : SUM(nominal) WHERE status_pengajuan = 6 ("Dana Sudah Dicairkan")
     *
     * @param string $tahun_ajaran label tahun ajaran (mis. "2025/2026")
     * @param string|null $jenjang_context 'sd'/'smp'/'sma' atau null untuk aggregate
     * @return array('okr'=>float,'ajuan'=>float,'cair'=>float)
     */
    public function dashboard_aggregate($tahun_ajaran, $jenjang_context = null)
    {
        $result = array('okr' => 0.0, 'ajuan' => 0.0, 'cair' => 0.0);

        if (empty($tahun_ajaran)) {
            return $result;
        }

        $tables = array();
        if (!empty($jenjang_context) && isset($this->dashboard_tables[$jenjang_context])) {
            $tables[] = $this->dashboard_tables[$jenjang_context];
        } else {
            $tables = array_values($this->dashboard_tables);
        }

        foreach ($tables as $tbl) {
            // OKR terkumpul = SUM(nominal_okr)
            $row_okr = $this->db->select('SUM(nominal_okr) AS total')
                ->from($tbl)
                ->where('tahun_ajaran', $tahun_ajaran)
                ->get()
                ->row();
            $result['okr'] += (float)(isset($row_okr->total) ? $row_okr->total : 0);

            // OKR diajukan = SUM(nominal_pengajuan) WHERE status_pengajuan IN (2,3,4,6)
            $row_ajuan = $this->db->select('SUM(nominal_pengajuan) AS total')
                ->from($tbl)
                ->where('tahun_ajaran', $tahun_ajaran)
                ->where_in('status_pengajuan', array(2, 3, 4, 6))
                ->get()
                ->row();
            $result['ajuan'] += (float)(isset($row_ajuan->total) ? $row_ajuan->total : 0);

            // OKR dicairkan = SUM(nominal) WHERE status_pengajuan = 6
            $row_cair = $this->db->select('SUM(nominal) AS total')
                ->from($tbl)
                ->where('tahun_ajaran', $tahun_ajaran)
                ->where('status_pengajuan', 6)
                ->get()
                ->row();
            $result['cair'] += (float)(isset($row_cair->total) ? $row_cair->total : 0);
        }

        return $result;
    }

    /**
     * Ambil distinct tahun_ajaran dari tabel sumber (untuk opsi dropdown filter chart).
     * @param string|null $jenjang_context
     * @return array list label, diurut descending.
     */
    public function get_tahun_ajaran_options($jenjang_context = null)
    {
        $labels = array();
        $tables = array();

        if (!empty($jenjang_context) && isset($this->dashboard_tables[$jenjang_context])) {
            $tables[] = $this->dashboard_tables[$jenjang_context];
        } else {
            $tables = array_values($this->dashboard_tables);
        }

        foreach ($tables as $tbl) {
            $r = $this->db->select('tahun_ajaran')
                ->from($tbl)
                ->group_by('tahun_ajaran')
                ->get()
                ->result();
            foreach ($r as $row) {
                $lbl = $row->tahun_ajaran;
                if (!in_array($lbl, $labels)) {
                    $labels[] = $lbl;
                }
            }
        }

        rsort($labels);
        return $labels;
    }

}

/* End of file Model_program_anggaran.php */
/* Location: ./application/models/Model_program_anggaran.php */