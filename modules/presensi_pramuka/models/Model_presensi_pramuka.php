<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_pramuka extends MY_Model {

    private $primary_key    = 'id_presensi_pramuka';
    private $table_name     = 'presensi_pramuka';
    private $field_search   = array('hari', 'tanggal', 'jenjang', 'id_siswa_aktif', 'nama_lengkap', 'kelas', 'status_hadir', 'kehadiran', 'status_kelengkapan', 'kelengkapan', 'status_keaktifan', 'keaktifan', 'total_nilai', 'updated_by');

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    // -----------------------------------------------------------------------
    // COUNT ALL — multi-filter aware
    // -----------------------------------------------------------------------
    public function count_all($q = null, $field = null, $filters = null)
    {
        if (is_array($filters) && count($filters) > 0) {
            $this->filter_avaiable();
            $where = $this->_build_multi_where($filters);
            if ($where) {
                $this->db->where($where);
            }
            $query = $this->db->get($this->table_name);
            return $query->num_rows();
        }

        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "presensi_pramuka." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_pramuka." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "presensi_pramuka." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    // -----------------------------------------------------------------------
    // GET — multi-filter aware
    // -----------------------------------------------------------------------
    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = array(), $filters = null)
    {
        if (is_array($filters) && count($filters) > 0) {
            if (is_array($select_field) && count($select_field)) {
                $this->db->select($select_field);
            }
            $this->filter_avaiable();
            $where = $this->_build_multi_where($filters);
            if ($where) {
                $this->db->where($where);
            }
            $this->db->limit($limit, $offset);
            $this->db->order_by('presensi_pramuka.' . $this->primary_key, 'DESC');
            $query = $this->db->get($this->table_name);
            return $query->result();
        }

        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "presensi_pramuka." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_pramuka." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "presensi_pramuka." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) && count($select_field)) {
            $this->db->select($select_field);
        }

        $this->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('presensi_pramuka.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    // -----------------------------------------------------------------------
    // GET WITH FILTER
    // -----------------------------------------------------------------------
    public function get_with_filter($filters = array(), $limit = 0, $offset = 0)
    {
        $where = $this->_build_filter_sql($filters);

        $limit_clause = '';
        if ($limit > 0) {
            $limit_clause = "LIMIT {$limit} OFFSET {$offset}";
        }

        $sql = "SELECT p.* FROM presensi_pramuka p
                WHERE p.deleted_at IS NULL {$where}
                ORDER BY p.tanggal DESC, p.id_presensi_pramuka DESC
                {$limit_clause}";

        return $this->mymodel->withquery($sql, 'result');
    }

    // -----------------------------------------------------------------------
    // COUNT WITH FILTER
    // -----------------------------------------------------------------------
    public function count_with_filter($filters = array())
    {
        $where = $this->_build_filter_sql($filters);

        $sql = "SELECT COUNT(*) AS cnt FROM presensi_pramuka p
                WHERE p.deleted_at IS NULL {$where}";

        $row = $this->mymodel->withquery($sql, 'row');
        return !empty($row) ? (int) $row->cnt : 0;
    }

    // -----------------------------------------------------------------------
    // SUMMARY STATS
    // -----------------------------------------------------------------------
    public function get_summary($filters = array())
    {
        $where = $this->_build_filter_sql($filters);

        $sql = "SELECT
                    COUNT(*) AS total_records,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'hadir' THEN 1 ELSE 0 END) AS total_hadir,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'terlambat' THEN 1 ELSE 0 END) AS total_terlambat,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'izin' THEN 1 ELSE 0 END) AS total_izin,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'sakit' THEN 1 ELSE 0 END) AS total_sakit,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'alfa' THEN 1 ELSE 0 END) AS total_alfa,
                    AVG(CAST(p.kehadiran AS DECIMAL(5,2))) AS avg_kehadiran,
                    AVG(CAST(p.kelengkapan AS DECIMAL(5,2))) AS avg_kelengkapan,
                    AVG(CAST(p.keaktifan AS DECIMAL(5,2))) AS avg_keaktifan,
                    AVG(CAST(p.total_nilai AS DECIMAL(5,2))) AS avg_total
                FROM presensi_pramuka p
                WHERE p.deleted_at IS NULL {$where}";

        return $this->mymodel->withquery($sql, 'row');
    }

    // -----------------------------------------------------------------------
    // WEEKLY CHART DATA
    // -----------------------------------------------------------------------
    public function get_weekly_chart($filters = array(), $weeks = 8)
    {
        $where_base = "p.deleted_at IS NULL";

        if (!empty($filters['jenjang'])) {
            $where_base .= " AND p.jenjang = '" . $this->db->escape_str(strtoupper($filters['jenjang'])) . "'";
        }
        if (!empty($filters['kelas'])) {
            $where_base .= " AND p.kelas = '" . $this->db->escape_str($filters['kelas']) . "'";
        }

        $sql = "SELECT
                    YEARWEEK(p.tanggal, 1) AS yw,
                    MIN(p.tanggal) AS start_date,
                    MAX(p.tanggal) AS end_date,
                    COUNT(*) AS total_siswa,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'hadir' THEN 1 ELSE 0 END) AS hadir,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'terlambat' THEN 1 ELSE 0 END) AS terlambat,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'izin' THEN 1 ELSE 0 END) AS izin,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'sakit' THEN 1 ELSE 0 END) AS sakit,
                    SUM(CASE WHEN LOWER(p.status_hadir) = 'alfa' THEN 1 ELSE 0 END) AS alfa,
                    ROUND(AVG(CAST(p.total_nilai AS DECIMAL(5,2))), 1) AS avg_nilai
                FROM presensi_pramuka p
                WHERE {$where_base}
                  AND p.tanggal >= DATE_SUB(CURDATE(), INTERVAL {$weeks} WEEK)
                GROUP BY yw
                ORDER BY yw ASC";

        return $this->mymodel->withquery($sql, 'result');
    }

    // -----------------------------------------------------------------------
    // BUILD FILTER SQL
    // -----------------------------------------------------------------------
    private function _build_filter_sql($filters)
    {
        $conditions = array();

        if (!empty($filters['jenjang'])) {
            $conditions[] = "p.jenjang = '" . $this->db->escape_str(strtoupper($filters['jenjang'])) . "'";
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = "p.tanggal >= '" . $this->db->escape_str($filters['start_date']) . "'";
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = "p.tanggal <= '" . $this->db->escape_str($filters['end_date']) . "'";
        }

        if (!empty($filters['kelas'])) {
            $conditions[] = "p.kelas = '" . $this->db->escape_str($filters['kelas']) . "'";
        } elseif (!empty($filters['id_tingkatan']) && !empty($filters['jenjang'])) {
            $tbl_k   = get_kelas_table_name(strtolower($filters['jenjang']));
            $tbl_t   = 'tingkatan_' . strtolower($filters['jenjang']);
            $id_col_t = 'id_tingkatan_' . strtolower($filters['jenjang']);
            if ($tbl_k) {
                $conditions[] = "p.kelas IN (
                    SELECT k.label FROM {$tbl_k} k
                    WHERE k.id_tingkatan = " . (int) $filters['id_tingkatan'] . "
                )";
            }
        }

        if (!empty($filters['status_hadir'])) {
            $conditions[] = "p.status_hadir = '" . $this->db->escape_str($filters['status_hadir']) . "'";
        }

        if (!empty($filters['q'])) {
            $q = $this->db->escape_like_str($filters['q']);
            $conditions[] = "(p.nama_lengkap LIKE '%{$q}%' OR p.kelas LIKE '%{$q}%' OR p.id_siswa_aktif LIKE '%{$q}%')";
        }

        if (empty($conditions)) {
            return '';
        }

        return ' AND ' . implode(' AND ', $conditions);
    }

    // -----------------------------------------------------------------------
    // BUILD MULTI WHERE — for Query Builder mode
    // -----------------------------------------------------------------------
    private function _build_multi_where($filters)
    {
        $conditions = array();

        $field_map = array(
            'jenjang'            => 'presensi_pramuka.jenjang',
            'nama_lengkap'       => 'presensi_pramuka.nama_lengkap',
            'kelas'              => 'presensi_pramuka.kelas',
            'status_hadir'       => 'presensi_pramuka.status_hadir',
            'status_kelengkapan' => 'presensi_pramuka.status_kelengkapan',
            'status_keaktifan'   => 'presensi_pramuka.status_keaktifan',
            'tanggal'            => 'presensi_pramuka.tanggal',
            'id_siswa_aktif'     => 'presensi_pramuka.id_siswa_aktif',
            'updated_by'         => 'presensi_pramuka.updated_by',
        );

        foreach ($filters as $filter) {
            $field    = isset($filter['field']) ? $this->scurity($filter['field']) : '';
            $operator = isset($filter['operator']) ? $this->scurity($filter['operator']) : '';
            $value    = isset($filter['value']) ? $filter['value'] : '';

            if (empty($field) || empty($value)) continue;

            $db_field = isset($field_map[$field]) ? $field_map[$field] : 'presensi_pramuka.' . $field;

            switch ($operator) {
                case 'contains':
                    $conditions[] = "{$db_field} LIKE '%{$this->db->escape_str($value)}%'";
                    break;
                case 'equals':
                    $conditions[] = "{$db_field} = '" . $this->db->escape_str($value) . "'";
                    break;
                case 'starts_with':
                    $conditions[] = "{$db_field} LIKE '" . $this->db->escape_str($value) . "%'";
                    break;
                case 'ends_with':
                    $conditions[] = "{$db_field} LIKE '%" . $this->db->escape_str($value) . "'";
                    break;
                case 'gt':
                    $conditions[] = "{$db_field} > '" . $this->db->escape_str($value) . "'";
                    break;
                case 'lt':
                    $conditions[] = "{$db_field} < '" . $this->db->escape_str($value) . "'";
                    break;
                default:
                    $conditions[] = "{$db_field} LIKE '%{$this->db->escape_str($value)}%'";
                    break;
            }
        }

        if (empty($conditions)) return null;
        return implode(' AND ', $conditions);
    }

    // -----------------------------------------------------------------------
    // FILTER — soft delete only
    // -----------------------------------------------------------------------
    public function filter_avaiable()
    {
        $this->db->where('presensi_pramuka.deleted_at', NULL);
        return $this;
    }

    // -----------------------------------------------------------------------
    // EXPORT
    // -----------------------------------------------------------------------
    public function export_pramuka($filters = array())
    {
        $data = $this->get_with_filter($filters, 0, 0);

        // Build NIS lookup map per jenjang
        $nis_map = array();
        if (!empty($data)) {
            $ids_by_jenjang = array();
            foreach ($data as $d) {
                $j = strtolower($d->jenjang);
                if (!isset($ids_by_jenjang[$j])) $ids_by_jenjang[$j] = array();
                $ids_by_jenjang[$j][] = (int) $d->id_siswa_aktif;
            }
            foreach ($ids_by_jenjang as $j => $ids) {
                $tbl_s   = get_siswa_table_name($j);
                $id_col  = get_siswa_id_column($j);
                if (!$tbl_s || !$id_col) continue;
                $in = implode(',', array_unique($ids));
                $rows_nis = $this->mymodel->withquery(
                    "SELECT {$id_col} AS id, nis FROM {$tbl_s} WHERE {$id_col} IN ({$in})",
                    'result'
                );
                if (!empty($rows_nis)) {
                    $nis_map[$j] = array();
                    foreach ($rows_nis as $r) {
                        $nis_map[$j][(int) $r->id] = $r->nis;
                    }
                }
            }
        }

        require_once APPPATH . 'libraries/Excel/PHPExcel.php';

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        $header_style = array(
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFD700'),
            ),
        );

        $cell_style = array(
            'borders' => array(
                'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
            ),
            'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
        );

        // Title
        $sheet->setCellValue('A1', 'LAPORAN PRESENSI PRAMUKA');
        $sheet->mergeCells('A1:P1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Filter info
        $filter_info = array();
        if (!empty($filters['jenjang'])) $filter_info[] = 'Jenjang: ' . strtoupper($filters['jenjang']);
        if (!empty($filters['kelas'])) $filter_info[] = 'Kelas: ' . $filters['kelas'];
        if (!empty($filters['start_date'])) $filter_info[] = 'Dari: ' . $filters['start_date'];
        if (!empty($filters['end_date'])) $filter_info[] = 'Sampai: ' . $filters['end_date'];
        $sheet->setCellValue('A2', implode(' | ', $filter_info));
        $sheet->mergeCells('A2:P2');

        // Headers
        $headers = array('No', 'Tanggal', 'Hari', 'NIS', 'Nama Siswa', 'Kelas', 'Status', 'Kehadiran', 'Status Atribut', 'Kelengkapan', 'Status Keaktifan', 'Keaktifan', 'Total Nilai', 'Predikat', 'Deskripsi', 'Diupdate Oleh');
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $sheet->getStyle($col . '4')->applyFromArray($header_style);
            $col++;
        }

        // Data rows
        $row = 5;
        $no = 1;
        foreach ($data as $d) {
            $total = (float) $d->total_nilai;
            $pred_info = get_predikat_pramuka($total);
            $predikat  = $pred_info['predikat'];
            $deskripsi = $pred_info['deskripsi'];

            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $d->tanggal);
            $sheet->setCellValue('C' . $row, $d->hari);
            $nis = isset($nis_map[strtolower($d->jenjang)][(int) $d->id_siswa_aktif])
                ? $nis_map[strtolower($d->jenjang)][(int) $d->id_siswa_aktif]
                : '';
            $sheet->setCellValue('D' . $row, $nis);
            $sheet->setCellValue('E' . $row, $d->nama_lengkap);
            $sheet->setCellValue('F' . $row, $d->kelas);
            $sheet->setCellValue('G' . $row, $d->status_hadir);
            $sheet->setCellValue('H' . $row, $d->kehadiran);
            $sheet->setCellValue('I' . $row, $d->status_kelengkapan);
            $sheet->setCellValue('J' . $row, $d->kelengkapan);
            $sheet->setCellValue('K' . $row, $d->status_keaktifan);
            $sheet->setCellValue('L' . $row, $d->keaktifan);
            $sheet->setCellValue('M' . $row, $d->total_nilai);
            $sheet->setCellValue('N' . $row, $predikat);
            $sheet->setCellValue('O' . $row, $deskripsi);
            $sheet->setCellValue('P' . $row, $d->updated_by);

            for ($c = 'A'; $c <= 'P'; $c++) {
                $sheet->getStyle($c . $row)->applyFromArray($cell_style);
            }

            $predikat_bg = array(
                'A' => '009F56',
                'B' => '3A8AB7',
                'C' => '005CF1',
                'D' => 'E8940E',
                'E' => 'DB4B38',
            );
            if (isset($predikat_bg[$predikat])) {
                $sheet->getStyle('N' . $row)->applyFromArray(array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => $predikat_bg[$predikat]),
                    ),
                    'font' => array(
                        'color' => array('rgb' => 'FFFFFFFF'),
                        'bold' => true,
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                ));
            }

            $row++;
            $no++;
        }

        for ($c = 'A'; $c <= 'P'; $c++) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $tingkatan_label = 'Semua';
        if (!empty($filters['id_tingkatan']) && !empty($filters['jenjang'])) {
            $tbl_t = 'tingkatan_' . strtolower($filters['jenjang']);
            $id_col_t = 'id_tingkatan_' . strtolower($filters['jenjang']);
            $row_t = $this->mymodel->withquery(
                "SELECT label FROM {$tbl_t} WHERE {$id_col_t} = " . (int) $filters['id_tingkatan'] . " LIMIT 1",
                'row'
            );
            if (!empty($row_t)) $tingkatan_label = $row_t->label;
        }
        $kelas_label = !empty($filters['kelas']) ? $filters['kelas'] : 'Semua';
        $filename = 'Presensi Pramuka ' . $tingkatan_label . ' - ' . $kelas_label . ' - ' . date('Y-m-d') . '.xls';

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(FCPATH . 'uploads/' . $filename);

        return $filename;
    }
}

/* End of file Model_presensi_pramuka.php */
/* Location: ./modules/presensi_pramuka/models/Model_presensi_pramuka.php */
