<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_transaksi extends MY_Model {

    private $primary_key    = 'id_transaksi';
    private $table_name     = 'transaksi';
    private $field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'user_phone', 'description', 'total_biaya', 'status_transaksi', 'expired_datetime', 'created_at', 'updated_at'];

    // field pencarian biodata siswa (ada di 4 tabel siswa_*, join via parsing no_transaksi)
    private $field_search_siswa = ['nama_lengkap', 'nisn', 'no_peserta'];


    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    /*
     * Bangun where LIKE untuk pencarian.
     * - q kosong -> tanpa where pencarian (mencegah row hilang gara-gara LIKE '%%' di kolom join yang NULL)
     * - field biodata (nama_lengkap/nisn/no_peserta) -> dicari di 4 tabel siswa_* sekaligus
     */
    private function build_where($q = null, $field = null)
    {
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($q)) {
            return NULL;
        }

        $where = NULL;

        if (empty($field)) {
            // cari di semua kolom transaksi + biodata siswa
            $conds = array();
            foreach ($this->field_search as $f) {
                $conds[] = "transaksi." . $f . " LIKE '%" . $q . "%'";
            }
            foreach ($this->field_search_siswa as $f) {
                $conds[] = "COALESCE(siswa_sd." . $f . ", '') LIKE '%" . $q . "%'";
                $conds[] = "COALESCE(siswa_smp." . $f . ", '') LIKE '%" . $q . "%'";
                $conds[] = "COALESCE(siswa_sma." . $f . ", '') LIKE '%" . $q . "%'";
                $conds[] = "COALESCE(siswa_ft." . $f . ", '') LIKE '%" . $q . "%'";
            }
            $where = '(' . implode(' OR ', $conds) . ')';
        } elseif (in_array($field, $this->field_search_siswa)) {
            // field biodata: cari di 4 tabel siswa
            $conds = array();
            foreach (array('sd', 'smp', 'sma', 'ft') as $t) {
                $conds[] = "COALESCE(siswa_" . $t . "." . $field . ", '') LIKE '%" . $q . "%'";
            }
            $where = '(' . implode(' OR ', $conds) . ')';
        } else {
            $where = "(transaksi." . $field . " LIKE '%" . $q . "%')";
        }

        return $where;
    }

    /*
     * Join biodata siswa via parsing no_transaksi: PREFIX-JENJANG-timestamp-id_siswa.
     * Kode jenjang dipetakan ke tabel siswa_sd/smp/sma/ft (sama dengan logika edit_save di controller).
     */
    public function join_avaiable() {

        $jenjang_sd = "'SD','SDM','PSBSD'";
        $jenjang_smp = "'SMP','PSBSMP','PPSBBSMP'";
        $jenjang_sma = "'SMA','PSBSMA','PPSBBSMA'";
        $jenjang_ft = "'FT'";

        $this->db->join('biaya_pendaftaran', 'biaya_pendaftaran.id_biaya_pendaftaran = transaksi.id_biaya_pendaftaran', 'LEFT');
        $this->db->join('siswa_sd', "siswa_sd.id_siswa_sd = SUBSTRING_INDEX(transaksi.no_transaksi, '-', -1) AND SUBSTRING_INDEX(SUBSTRING_INDEX(transaksi.no_transaksi, '-', 2), '-', -1) IN (" . $jenjang_sd . ")", 'LEFT');
        $this->db->join('siswa_smp', "siswa_smp.id_siswa_smp = SUBSTRING_INDEX(transaksi.no_transaksi, '-', -1) AND SUBSTRING_INDEX(SUBSTRING_INDEX(transaksi.no_transaksi, '-', 2), '-', -1) IN (" . $jenjang_smp . ")", 'LEFT');
        $this->db->join('siswa_sma', "siswa_sma.id_siswa_sma = SUBSTRING_INDEX(transaksi.no_transaksi, '-', -1) AND SUBSTRING_INDEX(SUBSTRING_INDEX(transaksi.no_transaksi, '-', 2), '-', -1) IN (" . $jenjang_sma . ")", 'LEFT');
        $this->db->join('siswa_ft', "siswa_ft.id_siswa_ft = SUBSTRING_INDEX(transaksi.no_transaksi, '-', -1) AND SUBSTRING_INDEX(SUBSTRING_INDEX(transaksi.no_transaksi, '-', 2), '-', -1) IN (" . $jenjang_ft . ")", 'LEFT');
        $this->db->where('transaksi.is_show',1);
        $this->db->select('transaksi.*,biaya_pendaftaran.jenjang');
        $this->db->select('siswa_sd.id_siswa_sd AS id_siswa_sd, siswa_sd.nama_lengkap AS nama_lengkap_sd, siswa_sd.nisn AS nisn_sd, siswa_sd.no_peserta AS no_peserta_sd');
        $this->db->select('siswa_smp.id_siswa_smp AS id_siswa_smp, siswa_smp.nama_lengkap AS nama_lengkap_smp, siswa_smp.nisn AS nisn_smp, siswa_smp.no_peserta AS no_peserta_smp');
        $this->db->select('siswa_sma.id_siswa_sma AS id_siswa_sma, siswa_sma.nama_lengkap AS nama_lengkap_sma, siswa_sma.nisn AS nisn_sma, siswa_sma.no_peserta AS no_peserta_sma');
        $this->db->select('siswa_ft.id_siswa_ft AS id_siswa_ft, siswa_ft.nama_lengkap AS nama_lengkap_ft, siswa_ft.nisn AS nisn_ft, siswa_ft.no_peserta AS no_peserta_ft');


        return $this;
    }

    /* Summary infobox: total terbayar & belum terbayar (status_transaksi, is_show=1). */
    public function get_summary()
    {
        $this->db->select("SUM(CASE WHEN status_transaksi = 1 THEN total_biaya ELSE 0 END) AS total_terbayar", FALSE);
        $this->db->select("SUM(CASE WHEN status_transaksi = 0 THEN total_biaya ELSE 0 END) AS total_belum", FALSE);
        $this->db->select("SUM(status_transaksi = 1) AS jumlah_terbayar", FALSE);
        $this->db->select("SUM(status_transaksi = 0) AS jumlah_belum", FALSE);
        $this->db->where('is_show', 1);
        $query = $this->db->get($this->table_name);

        return $query->row();
    }

    public function count_all($q = null, $field = null)
    {
        $this->join_avaiable()->filter_avaiable();

        $where = $this->build_where($q, $field);
        if (!empty($where)) {
            $this->db->where($where);
        }

        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $sort = null, $sort_type = null)
    {
        $q = $this->scurity($q);
        $sort = $this->scurity($sort);
        $sort_type = $this->scurity($sort_type);
        $field = $this->scurity($field);

        $sort_by = (empty($sort)) ? 'id_transaksi' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;

        $this->join_avaiable()->filter_avaiable();

        $where = $this->build_where($q, $field);
        if (!empty($where)) {
            $this->db->where($where);
        }

        $this->db->limit($limit, $offset);
        $this->db->order_by('transaksi.' . $sort_by, $sort_type);
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    public function export_transaksi($table, $subject = 'file')
    {
        $this->load->library('excel');
        $this->join_avaiable();
        $result = $this->db->get($table);

        $this->excel->setActiveSheetIndex(0);

        $fields = $result->list_fields();

        $alphabet = 'ABCDEFGHIJKLMOPQRSTUVWXYZ';
        $alphabet_arr = str_split($alphabet);
        $column = [];

        foreach ($alphabet_arr as $alpha) {
            $column[] =  $alpha;
        }

        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                $column[] =  $alpha.$alpha2;
            }
        }
        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                foreach ($alphabet_arr as $alpha3) {
                    $column[] =  $alpha.$alpha2.$alpha3;
                }
            }
        }

        foreach($column as $col)
        {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setWidth(20);
        }

        $col_total = $column[count($fields)-1];

        //styling
        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'DA3232')
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            )
        );

        $phpColor = new PHPExcel_Style_Color();
        $phpColor->setRGB('FFFFFF');  

        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')->getFont()->setColor($phpColor);

        $this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);

        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.'1')
        ->getAlignment()->setWrapText(true); 

        $col = 0;
        foreach ($fields as $field)
        {
            
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, ucwords(str_replace('_', ' ', $field)));
            $col++;
        }
 
        $row = 2;
        foreach($result->result() as $data)
        {
            $col = 0;
            foreach ($fields as $field)
            {
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
                $this->excel->getActiveSheet()->getCellByColumnAndRow($col, $row)->setValueExplicit($data->$field, PHPExcel_Cell_DataType::TYPE_STRING);
                $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }
 
            $row++;
        }

        foreach (range('A', $this->excel->getActiveSheet()->getHighestColumn()) as $col) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
         }
         
        //set border
        $styleArray = array(
              'borders' => array(
                  'allborders' => array(
                      'style' => PHPExcel_Style_Border::BORDER_THIN
                  )
              )
          );
        $this->excel->getActiveSheet()->getStyle('A1:'.$col_total.''.$row)->applyFromArray($styleArray);

        $this->excel->getActiveSheet()->setTitle(ucwords($subject));

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.ucwords($subject).'-'.date('Y-m-d').'.xls');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); 
        header ('Cache-Control: cache, must-revalidate');
        header ('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

}

/* End of file Model_transaksi.php */
/* Location: ./application/models/Model_transaksi.php */