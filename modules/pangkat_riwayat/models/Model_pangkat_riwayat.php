<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_pangkat_riwayat extends MY_Model {

    private $primary_key    = 'id_pangkat';
    private $table_name     = 'pangkat_riwayat';
    private $field_search   = ['unit', 'nama_tabel', 'id_user', 'npp', 'nomor', 'pangkat', 'golongan', 'tmt', 'is_sk_calon', 'is_cant_promoted'];

    // Mapping table name to primary key
    private $table_pk_map = [
        'pegawai' => 'id_pegawai',
        'guru_sd' => 'id_guru',
        'guru_smp' => 'id_guru',
        'guru_sma' => 'id_guru',
        'pimpinan_sd' => 'id_pimpinan',
        'pimpinan_smp' => 'id_pimpinan',
        'pimpinan_sma' => 'id_pimpinan',
        'pramubhakti' => 'id_pramubhakti',
        'security' => 'id_security',
    ];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    /**
     * Subquery untuk mendapatkan nama_lengkap berdasarkan nama_tabel
     */
    private function _nama_lengkap_subquery()
    {
        return "(CASE 
            WHEN pangkat_riwayat.nama_tabel = 'pegawai' THEN (SELECT nama_lengkap FROM pegawai WHERE id_pegawai = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'guru_sd' THEN (SELECT nama_lengkap FROM guru_sd WHERE id_guru = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'guru_smp' THEN (SELECT nama_lengkap FROM guru_smp WHERE id_guru = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'guru_sma' THEN (SELECT nama_lengkap FROM guru_sma WHERE id_guru = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'pimpinan_sd' THEN (SELECT nama_lengkap FROM pimpinan_sd WHERE id_pimpinan = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'pimpinan_smp' THEN (SELECT nama_lengkap FROM pimpinan_smp WHERE id_pimpinan = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'pimpinan_sma' THEN (SELECT nama_lengkap FROM pimpinan_sma WHERE id_pimpinan = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'pramubhakti' THEN (SELECT nama_lengkap FROM pramubhakti WHERE id_pramubhakti = pangkat_riwayat.id_user LIMIT 1)
            WHEN pangkat_riwayat.nama_tabel = 'security' THEN (SELECT nama_lengkap FROM security WHERE id_security = pangkat_riwayat.id_user LIMIT 1)
            ELSE NULL
        END)";
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        $nama_lengkap_sub = $this->_nama_lengkap_subquery();

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "pangkat_riwayat.".$f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pangkat_riwayat.".$f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            // Also search by nama_lengkap
            $where .= "OR " . $nama_lengkap_sub . " LIKE '%" . $q . "%' ";

            $where = '('.$where.')';
        } else {
            if ($field == 'nama_lengkap') {
                $where .= "(" . $nama_lengkap_sub . " LIKE '%" . $q . "%' )";
            } else {
                $where .= "(" . "pangkat_riwayat.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        $nama_lengkap_sub = $this->_nama_lengkap_subquery();

        if (empty($field)) {
            foreach ($this->field_search as $f) {
                if ($iterasi == 1) {
                    $where .= "pangkat_riwayat.".$f . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "pangkat_riwayat.".$f . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }
            // Also search by nama_lengkap
            $where .= "OR " . $nama_lengkap_sub . " LIKE '%" . $q . "%' ";

            $where = '('.$where.')';
        } else {
            if ($field == 'nama_lengkap') {
                $where .= "(" . $nama_lengkap_sub . " LIKE '%" . $q . "%' )";
            } else {
                $where .= "(" . "pangkat_riwayat.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('pangkat_riwayat.'.$this->primary_key, "DESC");
        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $nama_lengkap_sub = $this->_nama_lengkap_subquery();
        
        // Ganti JOIN biasa dengan subquery agar tidak multiply rows
        $role_sub = "(SELECT nama_role FROM presensi_setting_role 
                      WHERE presensi_setting_role.nama_tabel = pangkat_riwayat.nama_tabel 
                      LIMIT 1)";
        
        $this->db->select('pangkat_riwayat.*, 
            ' . $role_sub . ' as presensi_setting_role_nama_role,
            ' . $nama_lengkap_sub . ' as nama_lengkap_user');
    
        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Export custom untuk pangkat_riwayat
     */
    public function export_pangkat_riwayat($table, $subject = 'file', $value = null, $col = null)
    {
        $this->load->library('excel');
        $this->load->helper('tanggal');
        
        $nama_lengkap_sub = $this->_nama_lengkap_subquery();
        
        // Gunakan subquery untuk role agar tidak duplikat
        $role_sub = "(SELECT nama_role FROM presensi_setting_role 
                      WHERE presensi_setting_role.nama_tabel = pangkat_riwayat.nama_tabel 
                      LIMIT 1)";
        
        $this->db->select('pangkat_riwayat.id_pangkat,
            pangkat_riwayat.unit,
            ' . $role_sub . ' as role,
            ' . $nama_lengkap_sub . ' as nama_lengkap,
            pangkat_riwayat.npp,
            pangkat_riwayat.nomor,
            pangkat_riwayat.pangkat,
            pangkat_riwayat.golongan,
            pangkat_riwayat.tmt,
            pangkat_riwayat.is_sk_calon,
            pangkat_riwayat.is_cant_promoted');
        
        $iterasi = 1;
        $where = "";
        
        if (!empty($value)) {
            if (empty($col)) {
                foreach ($this->field_search as $field) {
                    if ($iterasi == 1) {
                        $where .= "pangkat_riwayat." . $field . " LIKE '%" . $value . "%' ";
                    } else {
                        $where .= "OR " . "pangkat_riwayat." . $field . " LIKE '%" . $value . "%' ";
                    }
                    $iterasi++;
                }
                $where .= "OR " . $nama_lengkap_sub . " LIKE '%" . $value . "%' ";
                $where = '(' . $where . ')';
            } else {
                if ($col == 'nama_lengkap') {
                    $where .= "(" . $nama_lengkap_sub . " LIKE '%" . $value . "%' )";
                } else {
                    $where .= "(" . "pangkat_riwayat." . $col . " LIKE '%" . $value . "%' )";
                }
            }
            $this->db->where($where);
        }
        
        $this->db->order_by('pangkat_riwayat.' . $this->primary_key, 'DESC');
        $result = $this->db->get($table);

        $this->excel->setActiveSheetIndex(0);

        $fields = $result->list_fields();

        $alphabet = 'ABCDEFGHIJKLMOPQRSTUVWXYZ';
        $alphabet_arr = str_split($alphabet);
        $column = [];

        foreach ($alphabet_arr as $alpha) {
            $column[] = $alpha;
        }

        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                $column[] = $alpha . $alpha2;
            }
        }
        foreach ($alphabet_arr as $alpha) {
            foreach ($alphabet_arr as $alpha2) {
                foreach ($alphabet_arr as $alpha3) {
                    $column[] = $alpha . $alpha2 . $alpha3;
                }
            }
        }

        foreach ($column as $col_letter) {
            $this->excel->getActiveSheet()->getColumnDimension($col_letter)->setWidth(20);
        }

        $col_total = $column[count($fields) - 1];

        // Styling header
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
            // Rename columns for better readability
            $header_map = [
                'id_pangkat' => 'No',
                'unit' => 'Unit',
                'role' => 'Role',
                'nama_lengkap' => 'Nama Lengkap',
                'npp' => 'NPP',
                'nomor' => 'Nomor',
                'pangkat' => 'Pangkat',
                'golongan' => 'Golongan',
                'tmt' => 'TMT',
                'is_sk_calon' => 'SK Calon',
                'is_cant_promoted' => 'Sudah Maksimal',
            ];
            
            $header_name = isset($header_map[$field]) ? $header_map[$field] : ucwords(str_replace('_', ' ', $field));
            $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $header_name);
            $col++;
        }

        $row = 2;
        $nomor_urut = 1;
        foreach ($result->result() as $data) {
            $col = 0;
            foreach ($fields as $field) {
                $value = isset($data->$field) ? $data->$field : '';
                
                // Ganti kolom ID dengan nomor urut
                if ($field == 'id_pangkat') {
                    $value = $nomor_urut;
                }
                // Format tanggal
                elseif ($field == 'tmt' && !empty($value) && $value != '0000-00-00') {
                    $value = formatTanggal($value);
                }
                // Format boolean fields
                elseif ($field == 'is_sk_calon' || $field == 'is_cant_promoted') {
                    $value = ($value == 1 || $value == 'ya') ? 'Ya' : 'Tidak';
                }
                // Ganti kosong dengan strip
                elseif (empty($value) || $value === null || $value === '') {
                    $value = '-';
                }
                
                $this->excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $nomor_urut++;
            $row++;
        }

        // Set border
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
     * Import data dari Excel
     */
    public function import_data($file_path)
    {
        $this->load->library('excel');
        
        try {
            $objPHPExcel = PHPExcel_IOFactory::load($file_path);
            $worksheet = $objPHPExcel->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            $header = array_shift($rows);
            
            // Map header names to database fields
            $header_map = [
                'ID' => 'id_pangkat',
                'Unit' => 'unit',
                'Role' => 'nama_tabel',
                'Nama Lengkap' => 'nama_lengkap',
                'NPP' => 'npp',
                'Nomor' => 'nomor',
                'Pangkat' => 'pangkat',
                'Golongan' => 'golongan',
                'TMT' => 'tmt',
                'SK Calon' => 'is_sk_calon',
                'Sudah Maksimal' => 'is_cant_promoted',
            ];
            
            // Get role mapping (nama_role -> nama_tabel)
            $roles = $this->db->get('presensi_setting_role')->result();
            $role_map = [];
            foreach ($roles as $role) {
                $role_map[strtolower($role->nama_role)] = $role->nama_tabel;
            }
            
            $success_count = 0;
            $update_count = 0;
            $insert_count = 0;
            $error_count = 0;
            $errors = [];
            
            foreach ($rows as $row_index => $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows
                
                $data = [];
                foreach ($header as $col_index => $col_name) {
                    $col_name = trim($col_name);
                    if (isset($header_map[$col_name])) {
                        $field = $header_map[$col_name];
                        $data[$field] = isset($row[$col_index]) ? trim($row[$col_index]) : '';
                    }
                }
                
                // Convert role name to nama_tabel
                if (!empty($data['nama_tabel'])) {
                    $role_key = strtolower($data['nama_tabel']);
                    if (isset($role_map[$role_key])) {
                        $data['nama_tabel'] = $role_map[$role_key];
                    }
                }
                
                // Find id_user based on nama_lengkap and nama_tabel
                if (!empty($data['nama_lengkap']) && !empty($data['nama_tabel'])) {
                    $id_user = $this->_find_user_id($data['nama_lengkap'], $data['nama_tabel']);
                    if ($id_user) {
                        $data['id_user'] = $id_user;
                    } else {
                        $errors[] = "Baris " . ($row_index + 2) . ": User '{$data['nama_lengkap']}' tidak ditemukan di tabel {$data['nama_tabel']}";
                        $error_count++;
                        continue;
                    }
                }
                
                // Remove nama_lengkap from data (not a database field)
                unset($data['nama_lengkap']);
                
                // Convert boolean fields
                if (isset($data['is_sk_calon'])) {
                    $data['is_sk_calon'] = (strtolower($data['is_sk_calon']) == 'ya' || $data['is_sk_calon'] == '1') ? 1 : 0;
                }
                if (isset($data['is_cant_promoted'])) {
                    $data['is_cant_promoted'] = (strtolower($data['is_cant_promoted']) == 'ya' || $data['is_cant_promoted'] == '1') ? 1 : 0;
                }
                
                // Check if data already exists based on npp or nomor
                $existing_id = $this->_find_existing_data($data);
                
                if ($existing_id) {
                    // Update existing data
                    unset($data['id_pangkat']);
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $this->db->where('id_pangkat', $existing_id);
                    $this->db->update($this->table_name, $data);
                    
                    if ($this->db->affected_rows() >= 0) {
                        $success_count++;
                        $update_count++;
                    } else {
                        $error_count++;
                        $errors[] = "Baris " . ($row_index + 2) . ": Gagal mengupdate data";
                    }
                } else {
                    // Insert new data
                    unset($data['id_pangkat']);
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $this->db->insert($this->table_name, $data);
                    
                    if ($this->db->insert_id() > 0) {
                        $success_count++;
                        $insert_count++;
                    } else {
                        $error_count++;
                        $errors[] = "Baris " . ($row_index + 2) . ": Gagal menyimpan data baru";
                    }
                }
            }
            
            $message = "Import selesai. Total: {$success_count} (Baru: {$insert_count}, Update: {$update_count}), Gagal: {$error_count}";
            
            return [
                'success' => true,
                'message' => $message,
                'errors' => $errors
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error membaca file: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Find user ID based on name and table
     */
    private function _find_user_id($nama_lengkap, $nama_tabel)
    {
        $pk_map = [
            'pegawai' => 'id_pegawai',
            'guru_sd' => 'id_guru',
            'guru_smp' => 'id_guru',
            'guru_sma' => 'id_guru',
            'pimpinan_sd' => 'id_pimpinan',
            'pimpinan_smp' => 'id_pimpinan',
            'pimpinan_sma' => 'id_pimpinan',
            'pramubhakti' => 'id_pramubhakti',
            'security' => 'id_security',
        ];
        
        if (!isset($pk_map[$nama_tabel])) {
            return false;
        }
        
        $pk = $pk_map[$nama_tabel];
        $query = $this->db->select($pk)
            ->where('nama_lengkap', $nama_lengkap)
            ->where('deleted_at IS NULL', null, false)
            ->get($nama_tabel);
        
        if ($query->num_rows() > 0) {
            return $query->row()->$pk;
        }
        
        return false;
    }

    /**
     * Get data for promotion recommendation
     * Shows employees who should or will be promoted based on TMT
     * 
     * @return array Data for chart and table
     */
    public function get_promotion_recommendation()
    {
        $today = date('Y-m-d');
        $four_years_ago = date('Y-m-d', strtotime('-4 years', strtotime($today)));
        
        // Get latest pangkat per user (highest TMT)
        $this->db->select('pangkat_riwayat.*, 
            ' . $this->_nama_lengkap_subquery() . ' as nama_lengkap_user,
            (SELECT nama_role FROM presensi_setting_role 
             WHERE presensi_setting_role.nama_tabel = pangkat_riwayat.nama_tabel 
             LIMIT 1) as role_name');
        $this->db->where('is_cant_promoted', 0);
        $this->db->where('deleted_at IS NULL', null, false);
        $all_data = $this->db->get($this->table_name)->result();
        
        // Group by user to get latest TMT
        $user_latest = [];
        foreach ($all_data as $row) {
            $key = $row->nama_tabel . '_' . $row->id_user;
            if (!isset($user_latest[$key]) || strtotime($row->tmt) > strtotime($user_latest[$key]->tmt)) {
                $user_latest[$key] = $row;
            }
        }
        
        $must_promote = []; // Harus naik
        $will_promote = []; // Akan naik
        $chart_data = [
            'must_promote' => 0,
            'will_promote' => 0,
            'labels' => []
        ];
        
        foreach ($user_latest as $key => $row) {
            if (empty($row->tmt) || $row->tmt == '0000-00-00') continue;
            
            $tmt_date = new DateTime($row->tmt);
            $today_date = new DateTime($today);
            $diff = $tmt_date->diff($today_date);
            $days_diff = $diff->days;
            
            // 4 years = 1460 days (approximately)
            $four_years_in_days = 1460;
            
            // Calculate promotion date (TMT + 4 years)
            $promotion_date = clone $tmt_date;
            $promotion_date->modify('+4 years');
            
            if ($days_diff >= $four_years_in_days) {
                // Harus naik (already 4+ years)
                $row->promotion_status = 'harus_naik';
                $row->promotion_date = $promotion_date->format('d-m-Y');
                $row->days_overdue = $days_diff - $four_years_in_days;
                $must_promote[] = $row;
            } else {
                // Akan naik (approaching 4 years)
                $row->promotion_status = 'akan_naik';
                $row->promotion_date = $promotion_date->format('d-m-Y');
                $row->days_remaining = $four_years_in_days - $days_diff;
                $will_promote[] = $row;
            }
        }
        
        // Sort must_promote by days_overdue descending
        usort($must_promote, function($a, $b) {
            return $b->days_overdue - $a->days_overdue;
        });
        
        // Sort will_promote by days_remaining ascending
        usort($will_promote, function($a, $b) {
            return $a->days_remaining - $b->days_remaining;
        });
        
        $chart_data['must_promote'] = count($must_promote);
        $chart_data['will_promote'] = count($will_promote);
        
        return [
            'must_promote' => $must_promote,
            'will_promote' => $will_promote,
            'chart_data' => $chart_data
        ];
    }

    /**
     * Find existing data based on npp or nomor
     * @param array $data
     * @return int|false existing id_pangkat or false
     */
    private function _find_existing_data($data)
    {
        $this->db->where('deleted_at IS NULL', null, false);
        
        $has_npp = !empty($data['npp']);
        $has_nomor = !empty($data['nomor']);
        
        if ($has_npp && $has_nomor) {
            // Both npp and nomor provided, check with OR
            $this->db->group_start();
            $this->db->where('npp', $data['npp']);
            $this->db->or_where('nomor', $data['nomor']);
            $this->db->group_end();
        } elseif ($has_npp) {
            $this->db->where('npp', $data['npp']);
        } elseif ($has_nomor) {
            $this->db->where('nomor', $data['nomor']);
        } else {
            return false;
        }
        
        // Also match by id_user and nama_tabel if available for more accurate matching
        if (!empty($data['id_user']) && !empty($data['nama_tabel'])) {
            $this->db->where('id_user', $data['id_user']);
            $this->db->where('nama_tabel', $data['nama_tabel']);
        }
        
        $query = $this->db->select('id_pangkat')
            ->limit(1)
            ->get($this->table_name);
        
        if ($query->num_rows() > 0) {
            return $query->row()->id_pangkat;
        }
        
        return false;
    }
}

/* End of file Model_pangkat_riwayat.php */
/* Location: ./application/models/Model_pangkat_riwayat.php */
