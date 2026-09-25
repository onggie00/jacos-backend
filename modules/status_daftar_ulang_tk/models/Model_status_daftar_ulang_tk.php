<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_status_daftar_ulang_tk extends MY_Model {

    private $primary_key    = 'id_daftar_ulang';
    private $table_name     = 'status_daftar_ulang_tk';
    private $field_search   = ['id_siswa_tk', 'status', 'slip_pembayaran', 'kwitansi', 'kartu_sementara', 'tanggal_lulus', 'tgl_daftar_ulang', 'tgl_aktivasi', 'tgl_bayar', 'va_bri'];
    private $field_search_join   = ['siswa_tk.nama_lengkap', 'siswa_tk.email', 'siswa_tk.no_peserta', 'siswa_tk.nisn', 'siswa_tk.tahun_ajaran', 'siswa_tk.gelombang'];

    /**
     * Whitelist alias -> DB column for multi-filter (anti SQL injection via GET)
     */
    private $field_map = array(
        'nama_lengkap'        => 'siswa_tk.nama_lengkap',
        'email'               => 'siswa_tk.email',
        'no_peserta'          => 'siswa_tk.no_peserta',
        'nisn'                => 'siswa_tk.nisn',
        'tahun_ajaran'        => 'siswa_tk.tahun_ajaran',
        'gelombang'           => 'siswa_tk.gelombang',
        'status'              => 'status_daftar_ulang_tk.status',
        'va_bri'              => 'status_daftar_ulang_tk.va_bri',
        'id_daftar_ulang'     => 'status_daftar_ulang_tk.id_daftar_ulang',
        'id_siswa_tk'         => 'status_daftar_ulang_tk.id_siswa_tk',
        'tanggal_lulus'       => 'DATE(status_daftar_ulang_tk.tanggal_lulus)',
        'tgl_daftar_ulang'    => 'status_daftar_ulang_tk.tgl_daftar_ulang',
        'tgl_aktivasi'        => 'DATE(status_daftar_ulang_tk.tgl_aktivasi)',
        'tgl_bayar'           => 'DATE(status_daftar_ulang_tk.tgl_bayar)',
        'expired_datetime'    => 'DATE(transaksi.expired_datetime)',
        'custom_payment'      => 'status_daftar_ulang_tk.custom_payment',
    );

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
            'field_search_join'  => $this->field_search_join,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null, $filters = null)
    {
        // Multi-filter mode
        if (is_array($filters) && count($filters) > 0) {
            $this->join_avaiable()->filter_avaiable();
            $where = $this->_build_multi_where($filters);
            if ($where) {
                $this->db->where($where);
            }
            $query = $this->db->get($this->table_name);
            return $query->num_rows();
        }

        // Legacy single filter mode
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.email LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.no_peserta LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.nama_lengkap LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.email LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.va_number LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $sort = null, $sort_type = null, $filters = null)
    {
        // Multi-filter mode
        if (is_array($filters) && count($filters) > 0) {
            if (is_array($select_field) AND count($select_field)) {
                $this->db->select($select_field);
            }

            $this->join_avaiable()->filter_avaiable();
            $where = $this->_build_multi_where($filters);
            if ($where) {
                $this->db->where($where);
            }
            $this->db->limit($limit, $offset);

            $sort = $this->scurity($sort);
            $sort_by = (empty($sort)) ? $this->primary_key : $sort;
            $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
            $this->db->order_by('status_daftar_ulang_tk.' . $sort_by, $sort_type);

            $query = $this->db->get($this->table_name);
            return $query->result();
        }

        // Legacy single filter mode
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.nama_lengkap LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.email LIKE '%" . $q . "%' ";
                    $where .= "OR " . "siswa_tk.no_peserta LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if($field=="status"){
                if($q=='Pembayaran Berhasil'){
                    $q=2;
                }elseif($q=='Virtual Account Aktif (Menunggu Pembayaran)'){
                    $q=1;
                }else{
                    $q=0;
                }
            }
            $where .= "(" . "status_daftar_ulang_tk.".$field . " LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.nama_lengkap LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.email LIKE '%" . $q . "%' )";
            $where .= "OR (" . "siswa_tk.va_number LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);

        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? $this->primary_key : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        $this->db->order_by('status_daftar_ulang_tk.' . $sort_by, $sort_type);

        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    /**
     * Build WHERE clause from multi-filter array
     * Filters combined with AND, supports operators: contains, equals, starts_with, ends_with, gt, lt, gte, lte
     */
    private function _build_multi_where($filters)
    {
        $conditions = array();

        foreach ($filters as $filter) {
            $field    = isset($filter['field'])    ? $this->scurity($filter['field'])    : '';
            $operator = isset($filter['operator']) ? $this->scurity($filter['operator']) : '';
            $value    = isset($filter['value'])    ? $filter['value']                     : '';

            if (empty($field) || empty($value)) continue;
            if (!isset($this->field_map[$field])) continue;

            $db_field = $this->field_map[$field];
            $escaped  = $this->db->escape_str($value);

            switch ($operator) {
                case 'contains':
                    $conditions[] = "{$db_field} LIKE '%{$escaped}%'";
                    break;
                case 'equals':
                    $conditions[] = "{$db_field} = '" . $escaped . "'";
                    break;
                case 'starts_with':
                    $conditions[] = "{$db_field} LIKE '" . $escaped . "%'";
                    break;
                case 'ends_with':
                    $conditions[] = "{$db_field} LIKE '%" . $escaped . "'";
                    break;
                case 'gt':
                    $conditions[] = "{$db_field} > '" . $escaped . "'";
                    break;
                case 'lt':
                    $conditions[] = "{$db_field} < '" . $escaped . "'";
                    break;
                case 'gte':
                    $conditions[] = "{$db_field} >= '" . $escaped . "'";
                    break;
                case 'lte':
                    $conditions[] = "{$db_field} <= '" . $escaped . "'";
                    break;
                default:
                    $conditions[] = "{$db_field} LIKE '%{$escaped}%'";
                    break;
            }
        }

        if (empty($conditions)) return null;
        return implode(' AND ', $conditions);
    }

    public function join_avaiable() {

        $this->db->join('siswa_tk', 'siswa_tk.id_siswa_tk = status_daftar_ulang_tk.id_siswa_tk', 'LEFT');
        $this->db->join('transaksi', 'transaksi.va_number = status_daftar_ulang_tk.va_bri and transaksi.no_transaksi like "%LDUI%"', 'LEFT');
        $this->db->group_by('status_daftar_ulang_tk.id_daftar_ulang');
        $this->db->select('status_daftar_ulang_tk.*,
            siswa_tk.nama_lengkap,
            siswa_tk.email,
            siswa_tk.no_peserta,
            siswa_tk.nisn,
            siswa_tk.tahun_ajaran,
            siswa_tk.gelombang,
            siswa_tk.va_number,
            MAX(transaksi.expired_datetime) as expired_datetime');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    public function join_avaiable_export()
    {
        $this->db->join('siswa_tk', 'siswa_tk.id_siswa_tk = status_daftar_ulang_tk.id_siswa_tk', 'LEFT');

        $this->db->select('siswa_tk.nama_lengkap,
                            siswa_tk.no_peserta,
                            siswa_tk.nama_ayah,
                            siswa_tk.notelp_ayah,
                            siswa_tk.nama_ibu,
                            siswa_tk.notelp_ibu,
                           status_daftar_ulang_tk.*,
                           (CASE
                                WHEN status_daftar_ulang_tk.status = 0 THEN "Menunggu Aktivasi"
                                WHEN status_daftar_ulang_tk.status = 1 THEN "Menunggu Pembayaran"
                                WHEN status_daftar_ulang_tk.status = 2 THEN "Lunas"
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

/* End of file Model_status_daftar_ulang_tk.php */
/* Location: ./application/models/Model_status_daftar_ulang_tk.php */