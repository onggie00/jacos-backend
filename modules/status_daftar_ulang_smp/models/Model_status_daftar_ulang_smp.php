<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_status_daftar_ulang_smp extends MY_Model {

    private $primary_key    = 'id_daftar_ulang';
    private $table_name     = 'status_daftar_ulang_smp';
    private $field_search   = ['id_siswa_smp', 'status', 'slip_pembayaran', 'kwitansi', 'kartu_sementara', 'tanggal_lulus', 'tgl_daftar_ulang'];

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
                    $where .= "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "status_daftar_ulang_smp.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('status_daftar_ulang_smp.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->join('siswa_smp', 'siswa_smp.id_siswa_smp = status_daftar_ulang_smp.id_siswa_smp', 'LEFT');

        $this->db->select('status_daftar_ulang_smp.*,siswa_smp.nama_lengkap,siswa_smp.email');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    public function join_avaiable_export()
    {
        $this->db->join('siswa_smp', 'siswa_smp.id_siswa_smp = status_daftar_ulang_smp.id_siswa_smp', 'LEFT');

        $this->db->select('siswa_smp.nama_lengkap,
                            siswa_smp.no_peserta,
                            siswa_smp.nama_ayah,
                            siswa_smp.notelp_ayah,
                            siswa_smp.nama_ibu,
                            siswa_smp.notelp_ibu,
                           status_daftar_ulang_smp.*,
                           (CASE
                                WHEN status_daftar_ulang_smp.status = 0 THEN "Menunggu Aktivasi"
                                WHEN status_daftar_ulang_smp.status = 1 THEN "Menunggu Pembayaran"
                                WHEN status_daftar_ulang_smp.status = 2 THEN "Lunas"
                                ELSE "-"
                            END) as status
                          ');

        $this->db->order_by('id_daftar_ulang', 'ASC');

        return $this;
    }

        //custom export excel
        public function export_siswa($table, $subject = 'file')
        {
            $this->load->library('excel');
            $this->join_avaiable_export();
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
                    } else if($field == 'slip_pembayaran' && $data->$field != ''){
                        $data_field = base_url('uploads/slip_pembayaran/').str_replace(' ','%20',$data->$field);
                    } else if($field == 'kwitansi' && $data->$field != ''){
                        $data_field = base_url('uploads/kwitansi/').str_replace(' ','%20',$data->$field);
                    } else if($field == 'kartu_sementara' && $data->$field != ''){
                        $data_field = base_url('uploads/kartu_siswa_sementara/').str_replace(' ','%20',$data->$field);
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

}

/* End of file Model_status_daftar_ulang_smp.php */
/* Location: ./application/models/Model_status_daftar_ulang_smp.php */