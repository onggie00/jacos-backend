<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_siswa_sma extends MY_Model
{

    private $primary_key    = 'id_siswa_sma';
    private $table_name     = 'siswa_sma';
    private $field_search   = ['nama_lengkap', 'email', 'email_ms_office', 'nisn', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'agama', 'email_ms_office_ortu', 'nama_ibu', 'pekerjaan_ibu', 'notelp_ibu', 'nama_ayah', 'pekerjaan_ayah', 'notelp_ayah', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'kode_pos', 'sekolah_asal', 'foto_peserta', 'akte_lahir', 'kartu_keluarga', 'sumber_informasi', 'alasan_tertarik', 'peminatan_sma', 'ppsbb', 'status_lulus', 'no_transaksi', 'no_peserta', 'provinsi', 'jenis_ppsbb', 'va_number'];

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
                    $where .= "siswa_sma." . $field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_sma." . $field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "siswa_sma." . $field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->where('siswa_sma.is_show', 1);
        $this->db->group_by('siswa_sma.id_siswa_sma');
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $sort = null, $sort_type = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);
        $sort = $this->scurity($sort);
        $sort_type = $this->scurity($sort_type);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                // if($field=="ppsbb"){
                //     $q=ppsbb(strtoupper($q));
                // }
                if ($iterasi == 1) {
                    $where .= "siswa_sma." . $field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_sma." . $field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '(' . $where . ')';
        } else {
            // if($field=="ppsbb"){
            //     $q=ppsbb(strtoupper($q));
            // }
            $where .= "(" . "siswa_sma." . $field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) and count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->where('siswa_sma.is_show', 1);
        $this->db->limit($limit, $offset);

        $sort_by = (empty($sort)) ? 'id_siswa_sma' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        $this->db->order_by('siswa_sma.' . $sort_by, $sort_type);
        $this->db->group_by('siswa_sma.id_siswa_sma');
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable()
    {
        $wlike ="( transaksi.no_transaksi  LIKE '%LI-PSBSMA%' ESCAPE '!' 
        OR  transaksi.no_transaksi  LIKE '%LI-PPSBBSMA%' ESCAPE '!' )";
        $this->db->join('areas', 'areas.id = siswa_sma.kelurahan', 'LEFT');
        $this->db->join('districts', 'districts.id = siswa_sma.kecamatan', 'LEFT');
        $this->db->join('regencies', 'regencies.id = siswa_sma.kota', 'LEFT');
        $this->db->join('peminatan_sma', 'peminatan_sma.id_peminatan_sma = siswa_sma.peminatan_sma', 'LEFT');
        $this->db->join('status_lulus', 'status_lulus.id_status_lulus = siswa_sma.status_lulus', 'LEFT');
        $this->db->join('provinces', 'provinces.id = siswa_sma.provinsi', 'LEFT');
        $this->db->join('status_daftar_ulang_sma daftar_ulang', 'daftar_ulang.id_siswa_sma = siswa_sma.id_siswa_sma', 'LEFT');
        $this->db->join('transaksi', 'transaksi.user_email = siswa_sma.email', 'LEFT');
        $this->db->where($wlike);
        // $this->db->or_like(array('transaksi.no_transaksi'=>'LI-PPSBBSMA'));
        
        $this->db->select('siswa_sma.*,
                           areas.name as areas_name,
                           districts.name as districts_name,
                           regencies.name as regencies_name,
                           peminatan_sma.peminatan as peminatan_sma_peminatan,
                           status_lulus.status_lulus as status_lulus_status_lulus,
                           provinces.name as provinces_name,
                           transaksi.created_at as tanggal_daftar,
                           transaksi.updated_at as tanggal_pembayaran,
                           daftar_ulang.tgl_daftar_ulang,
                           daftar_ulang.tgl_aktivasi as tanggal_aktivasi,
                           daftar_ulang.tgl_bayar as tanggal_bayar_daftar_ulang,
                           (CASE
                                WHEN daftar_ulang.status = 0 THEN "Menunggu Aktivasi"
                                WHEN daftar_ulang.status = 1 THEN "Menunggu Pembayaran"
                                WHEN daftar_ulang.status = 2 THEN "Lunas"
                                ELSE "-"
                            END) as status_daftar_ulang,
                            (CASE WHEN ppsbb = 1 THEN "IYA" ELSE "TIDAK" END) as ppsbb
                           ');


        return $this;
    }

    public function filter_avaiable()
    {

        if (!$this->aauth->is_admin()) { }

        return $this;
    }

    public function join_avaiable_export()
    {
        $this->db->join('areas', 'areas.id = siswa_sma.kelurahan', 'LEFT');
        $this->db->join('districts', 'districts.id = siswa_sma.kecamatan', 'LEFT');
        $this->db->join('regencies', 'regencies.id = siswa_sma.kota', 'LEFT');
        $this->db->join('peminatan_sma', 'peminatan_sma.id_peminatan_sma = siswa_sma.peminatan_sma', 'LEFT');
        $this->db->join('status_lulus', 'status_lulus.id_status_lulus = siswa_sma.status_lulus', 'LEFT');
        $this->db->join('provinces', 'provinces.id = siswa_sma.provinsi', 'LEFT');
        $this->db->join('status_daftar_ulang_sma daftar_ulang', 'daftar_ulang.id_siswa_sma = siswa_sma.id_siswa_sma', 'LEFT');
        $this->db->join('transaksi', 'transaksi.user_email = siswa_sma.email', 'LEFT');
        $this->db->or_like(array('transaksi.no_transaksi'=>'LI-PSBSMA'));
        $this->db->or_like(array('transaksi.no_transaksi'=>'LI-PPSBBSMA'));
        
        $this->db->select('siswa_sma.*,
                            areas.name as kelurahan,
                            districts.name as kecamatan,
                            regencies.name as kota,
                            provinces.name as provinsi,
                           peminatan_sma.peminatan as peminatan_sma_peminatan,
                           status_lulus.status_lulus as status_lulus_status_lulus,
                           transaksi.created_at as tanggal_daftar,
                           transaksi.updated_at as tanggal_pembayaran,
                           daftar_ulang.tgl_daftar_ulang,
                           daftar_ulang.tgl_aktivasi as tanggal_aktivasi,
                           daftar_ulang.tgl_bayar as tanggal_bayar_daftar_ulang,
                           (CASE
                                WHEN daftar_ulang.status = 0 THEN "Menunggu Aktivasi"
                                WHEN daftar_ulang.status = 1 THEN "Menunggu Pembayaran"
                                WHEN daftar_ulang.status = 2 THEN "Lunas"
                                ELSE "-"
                            END) as status_daftar_ulang,
                            (CASE WHEN ppsbb = 1 THEN "IYA" ELSE "TIDAK" END) as ppsbb
                           ');

        $this->db->where('siswa_sma.is_show', 1);
        $this->db->order_by('id_siswa_sma', 'ASC');

        return $this;
    }

    //custom export excel
    public function export_siswa($table, $subject = 'file',$value=null,$col=null)
    {
        $this->load->library('excel');
        $this->join_avaiable_export();
        $iterasi=1;
        $where="";
        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "siswa_sma." . $field . " LIKE '%" . $value . "%' ";
                } else {
                    $where .= "OR " . "siswa_sma." . $field . " LIKE '%" . $value . "%' ";
                }
                $iterasi++;
            }

            $where = '(' . $where . ')';
        } else {
            $where .= "(" . "siswa_sma." . $field . " LIKE '%" . $value . "%' )";
        }

        $this->db->where($where);
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
                $column[] =  $alpha . $alpha2;
            }
        }
        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                foreach ($alphabet_arr as $alpha3) {
                    $column[] =  $alpha . $alpha2 . $alpha3;
                }
            }
        }

        foreach ($column as $col) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setWidth(20);
        }

        $col_total = $column[count($fields) - 1];

        //styling
        $this->excel->getActiveSheet()->getStyle('A1:' . $col_total . '1')->applyFromArray(
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

        $this->excel->getActiveSheet()->getStyle('A1:' . $col_total . '1')->getFont()->setColor($phpColor);

        $this->excel->getActiveSheet()->getRowDimension(1)->setRowHeight(40);

        $this->excel->getActiveSheet()->getStyle('A1:' . $col_total . '1')
            ->getAlignment()->setWrapText(true);

        $col = 0;
        foreach ($fields as $field) {

            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, ucwords(str_replace('_', ' ', $field)));
            $col++;
        }

        $row = 2;
        foreach ($result->result() as $data) {

            $col = 0;
            foreach ($fields as $field) {
                if ($field == 'nama_lengkap') {
                    $data_field = ucwords(strtolower($data->$field));
                } else {
                    $data_field = $data->$field;
                }

                $this->excel->getActiveSheet()->getCellByColumnAndRow($col, $row)->setValueExplicit($data_field, PHPExcel_Cell_DataType::TYPE_STRING);
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
        $this->excel->getActiveSheet()->getStyle('A1:' . $col_total . '' . $row)->applyFromArray($styleArray);

        $this->excel->getActiveSheet()->setTitle(ucwords($subject));

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . ucwords($subject) . '-' . date('Y-m-d') . '.xls');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save('php://output');
    }

    //custom export pdf
    public function export_siswa_pdf($table, $title)
    {
        $this->load->library('HtmlPdf');

        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);

        $this->join_avaiable_export();
        $result = $this->db->get($table);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('template_list_siswa_pdf', [
            'results' => $result->result(),
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table . '.pdf', 'H');
    }
}

/* End of file Model_siswa_sma.php */
/* Location: ./application/models/Model_siswa_sma.php */
