<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_trans_bri_open extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'trans_bri_open';
    private $field_search   = ['label'];

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
                    $where .= "trans_bri_open.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "trans_bri_open.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "trans_bri_open.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "trans_bri_open.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "trans_bri_open.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "trans_bri_open.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('trans_bri_open.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('trans_bri_open.*');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

        //custom export excel
        public function export_bri($d, $subject = 'file')
        {
            $this->load->library('excel');
            $result = $d;
            // dd($result);
            $this->excel->setActiveSheetIndex(0);
    
            $fields = array('brivaNo','custCode','nama','keterangan','amount','paymentDate','tellerid','no_rek');
    
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
            // dd($fields);
            $totalAll = 0;
            $totalPay = 0;
            $totalNotPay = 0;
    
            $row = 2;
            foreach ($result as $key => $data) {
                $col = 0;
                

                foreach ($fields as $f) {
                    // dd($data[$f]);
                    
                    $this->excel->getActiveSheet()->getCellByColumnAndRow($col, $row)->setValueExplicit($data[$f], PHPExcel_Cell_DataType::TYPE_STRING);
                    $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                    // $this->excel->getNumberFormat()->setFormatCode( PHPExcel_Cell_DataType::TYPE_STRING );
                    $col++;
                    
                }
    
                $row++;
            }
    
            foreach (range('A', $this->excel->getActiveSheet()->getHighestColumn()) as $a) {
                $this->excel->getActiveSheet()->getColumnDimension($a)->setAutoSize(true);
            }
    
            //set border
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            // $this->excel->getActiveSheet()->getStyle('A1:' . $col_total . '' . $row)->applyFromArray($styleArray);
            // $row++;
    
            // $this->excel->getActiveSheet()->getCellByColumnAndRow('A:'.$col, $row)->setValueExplicit('Total Pendaftar: '.$totalAll, PHPExcel_Cell_DataType::TYPE_STRING);
            // $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            
    
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

/* End of file Model_trans_bri_open.php */
/* Location: ./application/models/Model_trans_bri_open.php */