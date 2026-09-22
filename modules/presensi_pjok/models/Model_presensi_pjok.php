<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_presensi_pjok extends MY_Model {

    private $primary_key    = 'id_presensi_pjok';
    private $table_name     = 'presensi_pjok';
    private $field_search   = array('hari', 'tanggal', 'jenjang', 'id_siswa_aktif', 'nama_lengkap', 'kelas', 'status_hadir', 'kehadiran', 'status_keaktifan', 'keaktifan', 'total_nilai', 'updated_by');

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
                    $where .= "presensi_pjok." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_pjok." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "presensi_pjok." . $field . " LIKE '%" . $q . "%' )";
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
            $this->db->order_by('presensi_pjok.' . $this->primary_key, 'DESC');
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
                    $where .= "presensi_pjok." . $f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "presensi_pjok." . $f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "presensi_pjok." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) && count($select_field)) {
            $this->db->select($select_field);
        }

        $this->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('presensi_pjok.' . $this->primary_key, 'DESC');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    // -----------------------------------------------------------------------
    // GET WITH RAW SQL
    // -----------------------------------------------------------------------
    public function get_with_filter($filters = array(), $limit = 0, $offset = 0)
    {
        $where = $this->_build_raw_where($filters);

        $sql = "SELECT p.* FROM presensi_pjok p
                WHERE p.deleted_at IS NULL {$where}
                ORDER BY p.tanggal DESC, p.nama_lengkap ASC";

        if ($limit > 0) {
            $sql .= " LIMIT " . (int) $offset . "," . (int) $limit;
        }

        return $this->mymodel->withquery($sql, 'result');
    }

    // -----------------------------------------------------------------------
    // COUNT WITH RAW SQL
    // -----------------------------------------------------------------------
    public function count_with_filter($filters = array())
    {
        $where = $this->_build_raw_where($filters);

        $sql = "SELECT COUNT(*) AS cnt FROM presensi_pjok p
                WHERE p.deleted_at IS NULL {$where}";

        $row = $this->mymodel->withquery($sql, 'row');
        return !empty($row) ? (int) $row->cnt : 0;
    }

    // -----------------------------------------------------------------------
    // SUMMARY
    // -----------------------------------------------------------------------
    public function get_summary($filters = array())
    {
        $where = $this->_build_raw_where($filters);

        $sql = "SELECT
                    COUNT(*) AS total_data,
                    SUM(p.status_hadir = 'Hadir') AS total_hadir,
                    SUM(p.status_hadir = 'Sakit') AS total_sakit,
                    SUM(p.status_hadir = 'Izin') AS total_izin,
                    SUM(p.status_hadir IN ('Alpa', 'Alfa', 'Tidak hadir', 'Terlambat')) AS total_alpa,
                    ROUND(AVG(CAST(p.total_nilai AS DECIMAL(10,2))), 2) AS rata_rata_nilai
                FROM presensi_pjok p
                WHERE p.deleted_at IS NULL {$where}";

        $row = $this->mymodel->withquery($sql, 'row');

        if (empty($row)) {
            return array(
                'total_data'      => 0,
                'total_hadir'     => 0,
                'total_sakit'     => 0,
                'total_izin'      => 0,
                'total_alpa'      => 0,
                'rata_rata_nilai' => 0,
            );
        }

        return array(
            'total_data'      => (int) $row->total_data,
            'total_hadir'     => (int) $row->total_hadir,
            'total_sakit'     => (int) $row->total_sakit,
            'total_izin'      => (int) $row->total_izin,
            'total_alpa'      => (int) $row->total_alpa,
            'rata_rata_nilai' => (float) $row->rata_rata_nilai,
        );
    }

    // -----------------------------------------------------------------------
    // WEEKLY CHART DATA
    // -----------------------------------------------------------------------
    public function get_weekly_chart($filters = array(), $weeks = 8)
    {
        $where = $this->_build_raw_where($filters);

        $sql = "SELECT
                    DATE(p.tanggal) AS tanggal_presensi,
                    COUNT(*) AS jumlah_siswa,
                    SUM(p.status_hadir = 'Hadir') AS total_hadir,
                    ROUND(AVG(CAST(p.total_nilai AS DECIMAL(10,2))), 2) AS rata_rata_nilai
                FROM presensi_pjok p
                WHERE p.deleted_at IS NULL {$where}
                GROUP BY DATE(p.tanggal)
                ORDER BY p.tanggal DESC
                LIMIT " . (int) $weeks;

        $data = $this->mymodel->withquery($sql, 'result');

        if (!empty($data)) {
            $data = array_reverse($data);
        }

        return $data;
    }

    // -----------------------------------------------------------------------
    // BUILD RAW WHERE CLAUSE
    // -----------------------------------------------------------------------
    private function _build_raw_where($filters)
    {
        $conditions = array();

        if (!empty($filters['jenjang_lock']) && is_array($filters['jenjang_lock'])) {
            $in_values = array();
            foreach ($filters['jenjang_lock'] as $j) {
                $in_values[] = "'" . $this->db->escape_str(strtoupper($j)) . "'";
            }
            $conditions[] = "p.jenjang IN (" . implode(',', $in_values) . ")";
        } elseif (!empty($filters['jenjang'])) {
            $conditions[] = "p.jenjang = '" . $this->db->escape_str(strtoupper($filters['jenjang'])) . "'";
        }

        if (!empty($filters['id_tingkatan'])) {
            $jenjang = !empty($filters['jenjang']) ? strtolower($filters['jenjang']) : 'sma';
            $kelas_tbl = 'kelas_' . $jenjang;
            $conditions[] = "p.kelas IN (SELECT label FROM {$kelas_tbl} WHERE id_tingkatan = " . (int) $filters['id_tingkatan'] . ")";
        }

        if (!empty($filters['kelas'])) {
            $conditions[] = "p.kelas = '" . $this->db->escape_str($filters['kelas']) . "'";
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = "p.tanggal >= '" . $this->db->escape_str($filters['start_date']) . "'";
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = "p.tanggal <= '" . $this->db->escape_str($filters['end_date']) . " 23:59:59'";
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
            'jenjang'           => 'presensi_pjok.jenjang',
            'nama_lengkap'      => 'presensi_pjok.nama_lengkap',
            'kelas'             => 'presensi_pjok.kelas',
            'status_hadir'      => 'presensi_pjok.status_hadir',
            'tanggal'           => 'presensi_pjok.tanggal',
            'id_siswa_aktif'    => 'presensi_pjok.id_siswa_aktif',
            'updated_by'        => 'presensi_pjok.updated_by',
        );

        foreach ($filters as $filter) {
            $field    = isset($filter['field']) ? $this->scurity($filter['field']) : '';
            $operator = isset($filter['operator']) ? $this->scurity($filter['operator']) : '';
            $value    = isset($filter['value']) ? $filter['value'] : '';

            if (empty($field) || empty($value)) continue;

            $db_field = isset($field_map[$field]) ? $field_map[$field] : 'presensi_pjok.' . $field;

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
        $this->db->where('presensi_pjok.deleted_at', NULL);
        return $this;
    }

    // -----------------------------------------------------------------------
    // EXPORT
    // -----------------------------------------------------------------------
    public function export_pjok($filters = array())
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
            'font' => array('bold' => true, 'size' => 11),
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
        $sheet->setCellValue('A1', 'LAPORAN PRESENSI PJOK');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Filter info
        $filter_info = array();
        if (!empty($filters['jenjang'])) $filter_info[] = 'Jenjang: ' . strtoupper($filters['jenjang']);
        if (!empty($filters['kelas'])) $filter_info[] = 'Kelas: ' . $filters['kelas'];
        if (!empty($filters['start_date'])) $filter_info[] = 'Dari: ' . $filters['start_date'];
        if (!empty($filters['end_date'])) $filter_info[] = 'Sampai: ' . $filters['end_date'];
        $sheet->setCellValue('A2', implode(' | ', $filter_info));
        $sheet->mergeCells('A2:L2');

        // Headers
        $headers = array('No', 'Tanggal', 'Hari', 'NIS', 'Nama Siswa', 'Kelas', 'Status', 'Kehadiran', 'Status Keaktifan', 'Keaktifan', 'Total Nilai', 'Predikat', 'Deskripsi', 'Diupdate Oleh');
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
            if ($total >= 90) { $predikat = 'A'; $deskripsi = 'Sangat Baik'; }
            elseif ($total >= 80) { $predikat = 'B'; $deskripsi = 'Baik'; }
            elseif ($total >= 70) { $predikat = 'C'; $deskripsi = 'Cukup'; }
            elseif ($total >= 60) { $predikat = 'D'; $deskripsi = 'Perlu Bimbingan'; }
            else { $predikat = 'E'; $deskripsi = 'Perlu Pembinaan Intensif'; }

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
            $sheet->setCellValue('I' . $row, $d->status_keaktifan);
            $sheet->setCellValue('J' . $row, $d->keaktifan);
            $sheet->setCellValue('K' . $row, $d->total_nilai);
            $sheet->setCellValue('L' . $row, $predikat);
            $sheet->setCellValue('M' . $row, $deskripsi);
            $sheet->setCellValue('N' . $row, $d->updated_by);

            for ($c = 'A'; $c <= 'N'; $c++) {
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
                $sheet->getStyle('L' . $row)->applyFromArray(array(
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

        for ($c = 'A'; $c <= 'N'; $c++) {
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
        $filename = 'Presensi PJOK ' . $tingkatan_label . ' - ' . $kelas_label . ' - ' . date('Y-m-d') . '.xls';

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save(FCPATH . 'uploads/' . $filename);

        return $filename;
    }
}

/* End of file Model_presensi_pjok.php */
/* Location: ./modules/presensi_pjok/models/Model_presensi_pjok.php */
