<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_siswa_tk_aktif extends MY_Model {

    private $primary_key    = 'id_siswa_tk_aktif';
    private $table_name     = 'siswa_tk_aktif';
    private $field_search   = ['nama_lengkap', 'nis', 'id_kelas', 'id_siswa_tk', 'id_tahun_ajaran', 'kewarganegaraan', 'nomor_peserta_ujian', 'nik', 'golongan_darah', 'telp', 'pendidikan_ayah', 'pendidikan_ibu', 'penghasilan_ayah', 'penghasilan_ibu', 'tgl_lahir_ayah', 'tgl_lahir_ibu', 'spp_custom', 'spp_type', 'acc_ujian'];
    private $field_search_join   = ['kelas_tk.label','tahun_ajaran.label'];

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
                    $where .= "siswa_tk_aktif.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_tk_aktif.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_tk.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        }
        else if($field == "kelas"){
            $where .= "" . "kelas_tk.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "tahun_ajaran"){
            $where .= "" . "tahun_ajaran.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "nama_lengkap"){
            $where .= "" . "siswa_tk_aktif.nama_lengkap". " LIKE '%" . $q . "%' ";
        }
        else {
            $where .= "(" . $field . " LIKE '%" . $q . "%' )";
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
            $sort_by = (empty($sort)) ? 'id_siswa_tk_aktif' : $sort;
            $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
            $this->db->order_by('siswa_tk_aktif.' . $sort_by, $sort_type);

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
                    $where .= "siswa_tk_aktif.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "siswa_tk_aktif.".$field . " LIKE '%" . $q . "%' ";
                    $where .= "OR " . "kelas_tk.label LIKE '%" . $q . "%' ";
                    $where .= "OR " . "tahun_ajaran.label LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            $where = '('.$where.')';
        }
        else if($field == "kelas"){
            $where .= "" . "kelas_tk.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "tahun_ajaran"){
            $where .= "" . "tahun_ajaran.label". " LIKE '%" . $q . "%' ";
        }
        else if($field == "nama_lengkap"){
            $where .= "" . "siswa_tk_aktif.nama_lengkap". " LIKE '%" . $q . "%' ";
        }
        else {
            $where .= "(" . "siswa_tk_aktif.".$field . " LIKE '%" . $q . "%' )";
        }
        

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        
        $field = $this->scurity($field);
        $sort = $this->scurity($sort);
        $sort_by = (empty($sort)) ? 'id_siswa_tk_aktif' : $sort;
        $sort_type = (empty($sort_type)) ? 'desc' : $sort_type;
        
        $this->db->order_by('siswa_tk_aktif.' . $sort_by, $sort_type);
        
        $query = $this->db->get($this->table_name);
        
        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('kelas_tk', 'kelas_tk.id_kelas_tk = siswa_tk_aktif.id_kelas', 'LEFT');
        $this->db->join('siswa_tk', 'siswa_tk.id_siswa_tk = siswa_tk_aktif.id_siswa_tk', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = siswa_tk_aktif.id_tahun_ajaran', 'LEFT');

        $this->db->select('siswa_tk_aktif.*,
        kelas_tk.label as kelas_tk_label,
        siswa_tk.nama_lengkap as siswa_tk_nama_lengkap,
        tahun_ajaran.label as tahun_ajaran_label,
        email_ms_office,
        email_ms_office_ortu,
        foto_profil,
        siswa_tk.deleted_at as deleted_at_siswa,
        siswa_tk.deleted_at_ortu as deleted_at_ortu
        ');


        return $this;
    }

    public function join_avaiable_export() {
        $this->db->join('kelas_tk', 'kelas_tk.id_kelas_tk = siswa_tk_aktif.id_kelas', 'LEFT');
        $this->db->join('siswa_tk', 'siswa_tk.id_siswa_tk = siswa_tk_aktif.id_siswa_tk', 'LEFT');
        $this->db->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = siswa_tk_aktif.id_tahun_ajaran', 'LEFT');
        
        $this->db->select('siswa_tk_aktif.nama_lengkap,
        kelas_tk.label as kelas,
        tahun_ajaran.label as tahun_ajaran,
        siswa_tk.email,
        siswa_tk.email_ms_office,
        siswa_tk.email_ms_office_ortu,
        siswa_tk.notelp_ibu,
        siswa_tk.notelp_ayah,
        siswa_tk.nisn,
        siswa_tk_aktif.file_raport,
        siswa_tk_aktif.kewarganegaraan,
        siswa_tk_aktif.nik,
        siswa_tk_aktif.golongan_darah,
        siswa_tk_aktif.telp,
        siswa_tk_aktif.pendidikan_ayah,
        siswa_tk_aktif.pendidikan_ibu,
        siswa_tk_aktif.penghasilan_ayah,
        siswa_tk_aktif.penghasilan_ibu,
        siswa_tk_aktif.tgl_lahir_ayah,
        siswa_tk_aktif.tgl_lahir_ibu,
        siswa_tk_aktif.spp_custom
        ');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Build WHERE clause from multi-filter array
     */
    private function _build_multi_where($filters)
    {
        $conditions = [];

        $field_map = [
            'nama_lengkap'   => 'siswa_tk_aktif.nama_lengkap',
            'nis'            => 'siswa_tk_aktif.nis',
            'kelas'          => 'kelas_tk.label',
            'tahun_ajaran'   => 'tahun_ajaran.label',
            'spp_type'       => 'siswa_tk_aktif.spp_type',
            'acc_ujian'      => 'siswa_tk_aktif.acc_ujian',
            'is_active'      => 'siswa_tk_aktif.is_active',
            'spp_custom'     => 'siswa_tk_aktif.spp_custom',
            'nik'            => 'siswa_tk_aktif.nik',
            'kewarganegaraan'=> 'siswa_tk_aktif.kewarganegaraan',
            'nomor_peserta_ujian' => 'siswa_tk_aktif.nomor_peserta_ujian',
        ];

        foreach ($filters as $filter) {
            $field    = isset($filter['field'])    ? $this->scurity($filter['field'])    : '';
            $operator = isset($filter['operator']) ? $this->scurity($filter['operator']) : '';
            $value    = isset($filter['value'])    ? $filter['value']                     : '';

            if (empty($field) || empty($value)) continue;

            $db_field = isset($field_map[$field]) ? $field_map[$field] : 'siswa_tk_aktif.' . $field;

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
                        $where .= "siswa_tk_aktif." . $field . " LIKE '%" . $value . "%' ";
                    } else {
                        $where .= "OR " . "siswa_tk_aktif." . $field . " LIKE '%" . $value . "%' ";
                    }
                    $iterasi++;
                }
    
                $where = '(' . $where . ')';
            } else {
                $where .= "(" . "siswa_tk_aktif." . $field . " LIKE '%" . $value . "%' )";
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
                    } elseif ($field == 'file_raport' || $field == 'foto_profil') {
                        $data_field = ($data->$field)?base_url('uploads/siswa_tk_aktif/') .$data->$field:$data->$field;
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

	/**
	 * Definisi kolom untuk Export Data Lengkap (siswa_tk_aktif + siswa_tk).
	 * Dipakai oleh view (render checkbox modal) dan controller export_lengkap()
	 * (whitelist kolom + select expression).
	 * Format: key_unik => array(header_excel, select_expression)
	 */
	public function export_lengkap_columns()
	{
		return array(
			'siswa_aktif' => array(
				'title' => 'Kolom Siswa Aktif (siswa_tk_aktif)',
				'cols' => array(
					'nama_lengkap'        => array('NAMA LENGKAP', 'sa.nama_lengkap'),
					'nis'                 => array('NIS', 'sa.nis'),
					'kelas'               => array('KELAS', 'k.label'),
					'tahun_ajaran'        => array('TAHUN AJARAN', 't.label'),
					'nomor_peserta_ujian' => array('NOMOR PESERTA UJIAN', 'sa.nomor_peserta_ujian'),
					'acc_ujian'           => array('ACC UJIAN', 'sa.acc_ujian'),
					'kewarganegaraan'     => array('KEWARGANEGARAAN', 'sa.kewarganegaraan'),
					'nik'                 => array('NIK', 'sa.nik'),
					'golongan_darah'      => array('GOLONGAN DARAH', 'sa.golongan_darah'),
					'telp'                => array('TELP', 'sa.telp'),
					'pendidikan_ayah'     => array('PENDIDIKAN AYAH', 'sa.pendidikan_ayah'),
					'pendidikan_ibu'      => array('PENDIDIKAN IBU', 'sa.pendidikan_ibu'),
					'penghasilan_ayah'    => array('PENGHASILAN AYAH', 'sa.penghasilan_ayah'),
					'penghasilan_ibu'     => array('PENGHASILAN IBU', 'sa.penghasilan_ibu'),
					'tgl_lahir_ayah'      => array('TGL LAHIR AYAH', 'sa.tgl_lahir_ayah'),
					'tgl_lahir_ibu'       => array('TGL LAHIR IBU', 'sa.tgl_lahir_ibu'),
					'is_active'           => array('STATUS AKTIF', 'sa.is_active'),
					'spp_custom'          => array('SPP CUSTOM', 'sa.spp_custom'),
					'spp_type'            => array('SPP TYPE', 'sa.spp_type'),
				),
			),
			'pendaftar' => array(
				'title' => 'Kolom Pendaftar (siswa_tk)',
				'cols' => array(
					'pendaftar_nama_lengkap' => array('NAMA LENGKAP PENDAFTAR', 's.nama_lengkap'),
					'email'                  => array('EMAIL', 's.email'),
					'email_ms_office'        => array('EMAIL MS OFFICE', 's.email_ms_office'),
					'no_peserta'             => array('NO PESERTA', 's.no_peserta'),
					'pendaftar_nik'          => array('NIK PENDAFTAR', 's.nik'),
					'nisn'                   => array('NISN', 's.nisn'),
					'npsn'                   => array('NPSN', 's.npsn'),
					'tempat_lahir'           => array('TEMPAT LAHIR', 's.tempat_lahir'),
					'tgl_lahir'              => array('TGL LAHIR', 's.tgl_lahir'),
					'jenis_kelamin'          => array('JENIS KELAMIN', 's.jenis_kelamin'),
					'agama'                  => array('AGAMA', 's.agama'),
					'email_ms_office_ortu'   => array('EMAIL MS OFFICE ORTU', 's.email_ms_office_ortu'),
					'nama_ibu'               => array('NAMA IBU', 's.nama_ibu'),
					'pekerjaan_ibu'          => array('PEKERJAAN IBU', 's.pekerjaan_ibu'),
					'notelp_ibu'             => array('NO TELP IBU', 's.notelp_ibu'),
					'nama_ayah'              => array('NAMA AYAH', 's.nama_ayah'),
					'pekerjaan_ayah'         => array('PEKERJAAN AYAH', 's.pekerjaan_ayah'),
					'notelp_ayah'            => array('NO TELP AYAH', 's.notelp_ayah'),
					'alamat'                 => array('ALAMAT', 's.alamat'),
					'kode_pos'               => array('KODE POS', 's.kode_pos'),
					'sekolah_asal'           => array('SEKOLAH ASAL', 's.sekolah_asal'),
					'sumber_informasi'       => array('SUMBER INFORMASI', 's.sumber_informasi'),
					'alasan_tertarik'        => array('ALASAN TERTARIK', 's.alasan_tertarik'),
					'is_mutasi'              => array('IS MUTASI', 's.is_mutasi'),
					'ppsbb'                  => array('PPSBB', 's.ppsbb'),
					'status_lulus'           => array('STATUS LULUS', 's.status_lulus'),
					'gelombang'              => array('GELOMBANG', 's.gelombang'),
				),
			),
		);
	}
}

/* End of file Model_siswa_tk_aktif.php */
/* Location: ./application/models/Model_siswa_tk_aktif.php */