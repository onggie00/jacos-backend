<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_laporan_tka_indikator extends MY_Model {

    private $primary_key    = 'id_indikator';
    private $table_name     = 'laporan_tka_indikator';
    private $field_search   = ['judul_kategori', 'soal', 'no_urut', 'tahun'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $tahun = null, $kategori = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                foreach ($this->field_search as $f) {
                    if ($iterasi == 1) {
                        $where .= "laporan_tka_indikator.".$f . " LIKE '%" . $q . "%' ";
                    } else {
                        $where .= "OR " . "laporan_tka_indikator.".$f . " LIKE '%" . $q . "%' ";
                    }
                    $iterasi++;
                }
                $where = '('.$where.')';
            } else {
                $where .= "(" . "laporan_tka_indikator.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        $this->join_avaiable()->filter_avaiable();

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Filter tahun
        if (!empty($tahun)) {
            $this->db->where('laporan_tka_indikator.tahun', $tahun);
        }

        // Filter kategori
        if (!empty($kategori)) {
            $this->db->where('laporan_tka_indikator.judul_kategori', $kategori);
        }

        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $tahun = null, $kategori = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (!empty($q)) {
            if (empty($field)) {
                foreach ($this->field_search as $f) {
                    if ($iterasi == 1) {
                        $where .= "laporan_tka_indikator.".$f . " LIKE '%" . $q . "%' ";
                    } else {
                        $where .= "OR " . "laporan_tka_indikator.".$f . " LIKE '%" . $q . "%' ";
                    }
                    $iterasi++;
                }
                $where = '('.$where.')';
            } else {
                $where .= "(" . "laporan_tka_indikator.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();

        if (!empty($where)) {
            $this->db->where($where);
        }

        // Filter tahun
        if (!empty($tahun)) {
            $this->db->where('laporan_tka_indikator.tahun', $tahun);
        }

        // Filter kategori
        if (!empty($kategori)) {
            $this->db->where('laporan_tka_indikator.judul_kategori', $kategori);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('laporan_tka_indikator.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
    * Get unique tahun values for filter
    */
    public function get_unique_tahun()
    {
        $this->db->select('tahun');
        $this->db->distinct();
        $this->db->order_by('tahun', 'DESC');
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    /**
    * Get unique kategori values for filter
    */
    public function get_unique_kategori()
    {
        $this->db->select('judul_kategori');
        $this->db->distinct();
        $this->db->order_by('judul_kategori', 'ASC');
        $query = $this->db->get($this->table_name);
        return $query->result();
    }

    /**
    * Cek duplikat berdasarkan tahun, soal, dan kategori
    */
    public function is_duplicate($tahun, $soal, $kategori, $no_urut)
    {
        $this->db->where('tahun', $tahun);
        $this->db->where('soal', $soal);
        $this->db->where('judul_kategori', $kategori);
        $this->db->where('no_urut', $no_urut);
        $query = $this->db->get($this->table_name);
        return $query->num_rows() > 0;
    }

    /**
    * Export data dengan filter
    */
    public function export_filtered($filename, $tahun = null, $kategori = null)
    {
        $CI =& get_instance();
        $CI->load->library('Excel/PHPExcel');

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Header style
        $headerStyle = array(
            'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '3C8DBC')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );
        $dataStyle = array(
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
        );

        // Set headers
        $sheet->setCellValue('A1', 'No Urut');
        $sheet->setCellValue('B1', 'Indikator Soal');
        $sheet->setCellValue('C1', 'Kategori');
        $sheet->setCellValue('D1', 'Tahun');
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Get data
        $this->db->select('no_urut, soal, judul_kategori, tahun');
        if (!empty($tahun)) $this->db->where('tahun', $tahun);
        if (!empty($kategori)) $this->db->where('judul_kategori', $kategori);
        $this->db->order_by('id_indikator', 'ASC');
        $query = $this->db->get($this->table_name);
        $data = $query->result();

        // Fill data
        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->no_urut);
            $sheet->setCellValue('B' . $row, $item->soal);
            $sheet->setCellValue('C' . $row, $item->judul_kategori);
            $sheet->setCellValue('D' . $row, $item->tahun);
            $row++;
        }

        // Column width
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(10);

        // Border data
        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle('A2:D' . $lastRow)->applyFromArray($dataStyle);
        }

        // Sheet title
        $sheetTitle = 'Laporan TKA Indikator';
        if (!empty($tahun)) $sheetTitle .= ' - ' . $tahun;
        $sheet->setTitle($sheetTitle);

        // Download
        $exportFilename = $filename;
        if (!empty($tahun)) $exportFilename .= '_' . $tahun;
        if (!empty($kategori)) $exportFilename .= '_' . str_replace(' ', '_', $kategori);
        $exportFilename .= '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $exportFilename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    /**
    * Hapus data berdasarkan filter tahun dan/atau kategori
    */
    public function delete_filtered($tahun = null, $kategori = null)
    {
        if (!empty($tahun)) {
            $this->db->where('tahun', $tahun);
        }
        if (!empty($kategori)) {
            $this->db->where('judul_kategori', $kategori);
        }
        return $this->db->delete($this->table_name);
    }

    public function join_avaiable() {
        
        $this->db->select('laporan_tka_indikator.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_laporan_tka_indikator.php */
/* Location: ./application/models/Model_laporan_tka_indikator.php */
