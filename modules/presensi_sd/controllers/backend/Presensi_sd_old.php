<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Presensi Sd Controller
*| --------------------------------------------------------------------------
*| Presensi Sd site
*|
*/
class Presensi_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_presensi_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Presensi Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('presensi_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$this->limit_page = 40;
		$this->data['presensi_sds'] = $this->model_presensi_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['presensi_sd_counts'] = $this->model_presensi_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/presensi_sd/index/',
			'total_rows'   => $this->model_presensi_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi SD List');
		$this->render('backend/standart/administrator/presensi_sd/presensi_sd_list', $this->data);
	}
	
	/**
	* Add new presensi_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('presensi_sd_add');

		$this->template->title('Presensi SD New');
		$this->render('backend/standart/administrator/presensi_sd/presensi_sd_add', $this->data);
	}

	/**
	* Add New Presensi Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('presensi_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ssid', 'Wifi SSID', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ip', 'IP', 'trim|max_length[50]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('status_absen', 'Status Presensi', 'trim|required');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|max_length[11]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'wifi_ssid' => $this->input->post('wifi_ssid'),
				'wifi_ip' => $this->input->post('wifi_ip'),
				'hari_absen' => $this->input->post('hari_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'alasan_terlambat' => $this->input->post('alasan_terlambat'),
				'id_izin' => $this->input->post('id_izin'),
			];

			
			$save_presensi_sd = $this->model_presensi_sd->store($save_data);
            

			if ($save_presensi_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_presensi_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/presensi_sd/edit/' . $save_presensi_sd, 'Edit Presensi Sd'),
						anchor('administrator/presensi_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/presensi_sd/edit/' . $save_presensi_sd, 'Edit Presensi Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_sd');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Presensi Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('presensi_sd_update');

		$this->data['presensi_sd'] = $this->model_presensi_sd->find($id);

		$this->template->title('Presensi SD Update');
		$this->render('backend/standart/administrator/presensi_sd/presensi_sd_update', $this->data);
	}

	/**
	* Update Presensi Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('presensi_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa Aktif', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ssid', 'Wifi SSID', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('wifi_ip', 'IP', 'trim|max_length[50]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('status_absen', 'Status Presensi', 'trim|required');
		$this->form_validation->set_rules('id_izin', 'Izin', 'trim|max_length[11]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'wifi_ssid' => $this->input->post('wifi_ssid'),
				'wifi_ip' => $this->input->post('wifi_ip'),
				'hari_absen' => $this->input->post('hari_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'alasan_terlambat' => $this->input->post('alasan_terlambat'),
				'id_izin' => $this->input->post('id_izin'),
			];

			
			$save_presensi_sd = $this->model_presensi_sd->change($id, $save_data);

			if ($save_presensi_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/presensi_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/presensi_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/presensi_sd');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Presensi Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('presensi_sd_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'presensi_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'presensi_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Presensi Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('presensi_sd_view');

		$this->data['presensi_sd'] = $this->model_presensi_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi SD Detail');
		$this->render('backend/standart/administrator/presensi_sd/presensi_sd_view', $this->data);
	}
	
	/**
	* delete Presensi Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$presensi_sd = $this->model_presensi_sd->find($id);

		
		
		return $this->model_presensi_sd->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('presensi_sd_export');

		//$this->model_presensi_sd->export('presensi_sd', 'presensi_sd');
		$id_tahun_ajaran = null;
		$id_kelas = null;
		$id_tingkatan = null;
		$bln = date("m");
		$thn = date("Y");
		if (!empty($this->input->get("id_tahun_ajaran"))) {
			$id_tahun_ajaran = $this->input->get("id_tahun_ajaran");
		}
		if (!empty($this->input->get("id_tingkatan"))) {
			$id_tingkatan = $this->input->get("id_tingkatan");
		}
		if (!empty($this->input->get("id_kelas"))) {
			$id_kelas = $this->input->get("id_kelas");
		}
		if (!empty($this->input->get("start_date"))) {
			$bln = date("m", strtotime($this->input->get("start_date")) );
		}
		if (!empty($this->input->get("start_date"))) {
			$thn = date("Y", strtotime($this->input->get("start_date")) );
		}
		if (!empty($id_tingkatan)) {
			$get_kelas = $this->mymodel->withquery("select * from kelas_sd where id_tingkatan = '".$id_tingkatan."' ","result");
		}
		else{
			$get_kelas = $this->mymodel->withquery("select * from kelas_sd where id_kelas_sd = '".$id_kelas."' ","result");
		}
		$total_day_in_month = date("t", strtotime($thn."-".$bln));
		$bulan = $bln;
		$tahun = $thn;
		$field = ['nomor', 'nis', 'id_siswa', 'nama_kelas'];
		for ($i=1; $i <= $total_day_in_month; $i++) { 
			array_push($field, $i."/".$bulan."/".$tahun);
		}
		array_push($field, "total_hadir");
		array_push($field, "total_tidak_hadir");
		array_push($field, "total_terlambat");
		array_push($field, "total_sakit");
		array_push($field, "total_izin");

		$where2 = null;
		$where = null;
		if ($where == null && !empty($id_tahun_ajaran)) {
			$where .= "where s.id_tahun_ajaran='".$id_tahun_ajaran."'";
		}
		else if(!empty($id_tahun_ajaran)){
			$where .= " and s.id_tahun_ajaran='".$id_tahun_ajaran."'";
		}

		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();

		if (!empty($get_kelas)) {
			foreach ($get_kelas as $key_kelas => $value_kelas) {
				$where = null;
				if ($where == null && !empty($id_tahun_ajaran)) {
					$where .= "where s.id_tahun_ajaran='".$id_tahun_ajaran."'";
				}
				else if(!empty($id_tahun_ajaran)){
					$where .= " and s.id_tahun_ajaran='".$id_tahun_ajaran."'";
				}
				if ($where == null && !empty($value_kelas->id_kelas)) {
					$where .= "where s.id_kelas='".$value_kelas->id_kelas."'";
				}
				else if(!empty($value_kelas->id_kelas)){
					$where .= " and s.id_kelas = '".$value_kelas->id_kelas."'";
				}
				$get_siswa = $this->mymodel->withquery("select s.id_siswa_sd_aktif as id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label as nama_kelas from siswa_sd_aktif s join kelas_sd k on s.id_kelas = k.id_kelas_sd ".$where." order by s.nama_lengkap ASC","result");
				foreach ($get_siswa as $key => $value) {
					$total_h = 0; $total_th = 0; $total_t = 0; $total_s = 0; $total_i = 0;
					for ($i=1; $i <= $total_day_in_month; $i++) { 
						$get_presensi = $this->mymodel->withquery("select * from presensi_sd where tanggal_absen = '". $tahun."-".$bulan."-".$i."' and id_siswa_aktif = '".$value->id_siswa."'","row");
						$nama_kolom = $i."/".$bulan."/".$tahun;
						if (!empty($get_presensi)) {
							if ($get_presensi->status_absen == "Hadir") {
								$value->$nama_kolom = "H (".date("H:i", strtotime($get_presensi->waktu_absen)).")"." (".$get_presensi->wifi_ssid.")";
								$total_h++;
							}
							else if ($get_presensi->status_absen == "Tidak Hadir") {
								$value->$nama_kolom = "TH (".date("H:i", strtotime($get_presensi->waktu_absen)).")"." (".$get_presensi->wifi_ssid.")";
								$total_th++;
							}
							else if($get_presensi->status_absen == "Terlambat"){
								$value->$nama_kolom = "T (".date("H:i", strtotime($get_presensi->waktu_absen)).") "." (".$get_presensi->wifi_ssid.")".$get_presensi->alasan_terlambat;
								$total_t++;
							}
							else if($get_presensi->status_absen == "Sakit"){
								$value->$nama_kolom = "S ";
								$total_s++;
							}
							else if($get_presensi->status_absen == "Izin"){
								$value->$nama_kolom = "I ";
								$total_i++;
							}
						}
						else{
							$value->$nama_kolom = "-";
						}
						//total presensi
						$value->total_hadir = $total_h;
						$value->total_tidak_hadir = $total_th;
						$value->total_terlambat = $total_t;
						$value->total_sakit = $total_s;
						$value->total_izin = $total_i;
					}

					/*print_r($value);
					echo "<br/><br/>";*/
				}
				//Create New Sheet
				$objPHPExcel->createSheet();
				// Set the active Excel worksheet to sheet 0 
				$objPHPExcel->setActiveSheetIndex($key_kelas);
				$objPHPExcel->getActiveSheet()->setTitle($value_kelas->label);

				// Initialise the Excel row number 
				$rowCount = 3;

				//start of printing column names as names of MySQL fields  
				$column = 'A';
				for ($i = 0; $i < count($field); $i++)  
				{
					//if (strpos($field_search[$i], "id") !== false) {
					if($field[$i] == "nomor") {
						$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
					}
					else if($field[$i] == "id_siswa") {
						$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA'));
					}
					else{
						$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
					}
				    
				    $column++;
				}
				//end of adding column names

				//start while loop to get data  
				$rowCount = 4;
				foreach ($get_siswa as $key => $value) {
					$column = 'A';
					for ($j=0; $j < count($field); $j++) {
						$kolom = $field[$j];
						if(!isset($value->$kolom)) { 
				            $value_data = NULL;  
				        }
				        elseif ($value->$kolom != "")  {
				            $value_data = strip_tags($value->$kolom);  
				        }
				        else {
				            $value_data = "";  
				        }
				        if (strpos($kolom, "nomor") !== false) {
				        	$value_data = ($key+1);
				        }
				        if (strpos($kolom, "id_kelas") !== false) {
				        	$value_data = $value->nama_kelas;
				        }
				        if (strpos($kolom, "id_siswa") !== false) {
				        	$value_data = $value->nama_lengkap;
				        }
				        if (strpos($kolom, "total_hadir") !== false) {
				        	$value_data = $value->$kolom;
				        }
				        if (strpos($kolom, "total_tidak_hadir") !== false) {
				        	$value_data = $value->$kolom;
				        }
				        if (strpos($kolom, "total_izin") !== false) {
				        	$value_data = $value->$kolom;
				        }
				        if (strpos($kolom, "total_sakit") !== false) {
				        	$value_data = $value->$kolom;
				        }
				        if (strpos($kolom, "total_terlambat") !== false) {
				        	$value_data = $value->$kolom;
				        }
				        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
				        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
						$column++;
					}
					$rowCount++;
				}
			}
		}
		else{
			$get_siswa = $this->mymodel->withquery("select s.id_siswa_sd_aktif as id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label as nama_kelas from siswa_sd_aktif s join kelas_sd k on s.id_kelas = k.id_kelas_sd ".$where." order by s.nama_lengkap ASC","result");
			foreach ($get_siswa as $key => $value) {
				$total_h = 0; $total_th = 0; $total_t = 0; $total_s = 0; $total_i = 0;
				for ($i=1; $i <= $total_day_in_month; $i++) { 
					$get_presensi = $this->mymodel->withquery("select * from presensi_sd where tanggal_absen = '". $tahun."-".$bulan."-".$i."' and id_siswa_aktif = '".$value->id_siswa."'","row");
					$nama_kolom = $i."/".$bulan."/".$tahun;
					if (!empty($get_presensi)) {
						if ($get_presensi->status_absen == "Hadir") {
							$value->$nama_kolom = "H (".date("H:i", strtotime($get_presensi->waktu_absen)).")";
							$total_h++;
						}
						else if ($get_presensi->status_absen == "Tidak Hadir") {
							$value->$nama_kolom = "TH (".date("H:i", strtotime($get_presensi->waktu_absen)).")";
							$total_th++;
						}
						else if($get_presensi->status_absen == "Terlambat"){
							$value->$nama_kolom = "T (".date("H:i", strtotime($get_presensi->waktu_absen)).") ".$get_presensi->alasan_terlambat;
							$total_t++;
						}
						else if($get_presensi->status_absen == "Sakit"){
							$value->$nama_kolom = "S ";
							$total_s++;
						}
						else if($get_presensi->status_absen == "Izin"){
							$value->$nama_kolom = "I ";
							$total_i++;
						}
					}
					else{
						$value->$nama_kolom = "-";
					}
					//total presensi
					$value->total_hadir = $total_h;
					$value->total_tidak_hadir = $total_th;
					$value->total_terlambat = $total_t;
					$value->total_sakit = $total_s;
					$value->total_izin = $total_i;
				}

				/*print_r($value);
				echo "<br/><br/>";*/
			}
			//Create New Sheet
			$objPHPExcel->createSheet();
			// Set the active Excel worksheet to sheet 0 
			$objPHPExcel->setActiveSheetIndex($key);
			$objPHPExcel->getActiveSheet()->setTitle($value->nama_kelas);

			// Initialise the Excel row number 
			$rowCount = 3;

			//start of printing column names as names of MySQL fields  
			$column = 'A';
			for ($i = 0; $i < count($field); $i++)  
			{
				//if (strpos($field_search[$i], "id") !== false) {
				if($field[$i] == "nomor") {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
				}
				else if($field[$i] == "id_siswa") {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA'));
				}
				else{
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
				}
			    
			    $column++;
			}
			//end of adding column names

			//start while loop to get data  
			$rowCount = 4;
			foreach ($get_siswa as $key => $value) {
				$column = 'A';
				for ($j=0; $j < count($field); $j++) {
					$kolom = $field[$j];
					if(!isset($value->$kolom)) { 
			            $value_data = NULL;  
			        }
			        elseif ($value->$kolom != "")  {
			            $value_data = strip_tags($value->$kolom);  
			        }
			        else {
			            $value_data = "";  
			        }
			        if (strpos($kolom, "nomor") !== false) {
			        	$value_data = ($key+1);
			        }
			        if (strpos($kolom, "id_kelas") !== false) {
			        	$value_data = $value->nama_kelas;
			        }
			        if (strpos($kolom, "id_siswa") !== false) {
			        	$value_data = $value->nama_lengkap;
			        }
			        if (strpos($kolom, "total_hadir") !== false) {
			        	$value_data = $value->$kolom;
			        }
			        if (strpos($kolom, "total_tidak_hadir") !== false) {
			        	$value_data = $value->$kolom;
			        }
			        if (strpos($kolom, "total_izin") !== false) {
			        	$value_data = $value->$kolom;
			        }
			        if (strpos($kolom, "total_sakit") !== false) {
			        	$value_data = $value->$kolom;
			        }
			        if (strpos($kolom, "total_terlambat") !== false) {
			        	$value_data = $value->$kolom;
			        }
			        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
			        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
					$column++;
				}
				$rowCount++;
			}
		}

		/*if ($where2 == null && !empty($tanggal)) {
			$where2 .= "where month(p.tanggal_absen) = '".$bulan."' and year(p.tanggal_absen) = '".$tahun."'";
		}
		else if (!empty($tanggal)) {
			$where2 .= " and month(p.tanggal_absen) = '".$bulan."' and year(p.tanggal_absen) = '".$tahun."'";
		}*/
		
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Presensi Siswa SD - '.formatBulan(date("Y-m-d", strtotime($thn."-".$bln."-1"))).' '.$thn.' - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('presensi_sd_export');

		$this->model_presensi_sd->pdf('presensi_sd', 'presensi_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('presensi_sd_export');

		$table = $title = 'presensi_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_presensi_sd->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function filter_presensi($offset = 0){
		$this->is_allowed('presensi_sd_list');
		$get = $this->input->get();
		$limit = 40;
		$where = "";

		if (!empty($get['id_tahun_ajaran'])) {
			if ($where == "") {
				$where .= "where s.id_tahun_ajaran = '".$get['id_tahun_ajaran']."'";
			}
			else{
				$where .= " and s.id_tahun_ajaran = '".$get['id_tahun_ajaran']."'";
			}
		}
		if (!empty($get['id_tingkatan'])) {
			if ($where == "") {
				$where .= "where k.id_tingkatan = '".$get['id_tingkatan']."'";
			}
			else{
				$where .= " and k.id_tingkatan = '".$get['id_tingkatan']."'";
			}
		}
		if (!empty($get['id_kelas'])) {
			if ($where == "") {
				$where .= "where k.id_kelas_sd = '".$get['id_kelas']."'";
			}
			else{
				$where .= " and k.id_kelas_sd = '".$get['id_kelas']."'";
			}
		}
		if (!empty($get['start_date']) && !empty($get['end_date'])) {
			if ($where == "") {
				$where .= "where p.tanggal_absen >= '".$get['start_date']."' and p.tanggal_absen <= '".$get['end_date']."'";
			}
			else{
				$where .= " and p.tanggal_absen >= '".$get['start_date']."' and p.tanggal_absen <= '".$get['end_date']."'";
			}
		}
		/*if (!empty($get['bulan']) && !empty($get['tahun'])) {
			if ($where == "") {
				$where .= "where month(p.tanggal_absen) = '".$get['bulan']."' and year(p.tanggal_absen) = '".$get['tahun']."'";
			}
			else{
				$where .= " and month(p.tanggal_absen) = '".$get['bulan']."' and year(p.tanggal_absen) = '".$get['tahun']."'";
			}
		}*/

		$get_data = $this->mymodel->withquery("select p.*, s.nama_lengkap, k.id_tingkatan, k.label as nama_kelas, i.jenis_izin from presensi_sd p join siswa_sd_aktif s on p.id_siswa_aktif = s.id_siswa_sd_aktif join kelas_sd k on s.id_kelas = k.id_kelas_sd left join izin_siswa_sd i on p.id_izin = i.id ".$where." order by s.nama_lengkap ASC limit ".$offset.",".$limit." ","result");
		$total_rows = count($this->mymodel->withquery("select p.*, s.nama_lengkap, k.label as nama_kelas, i.jenis_izin from presensi_sd p join siswa_sd_aktif s on p.id_siswa_aktif = s.id_siswa_sd_aktif join kelas_sd k on s.id_kelas = k.id_kelas_sd left join izin_siswa_sd i on p.id_izin = i.id ".$where." order by s.nama_lengkap ASC ","result"));

		$config = array(
			'base_url'     => 'administrator/presensi_sd/filter_presensi/',
			'total_rows'   => $total_rows,
			'per_page'     => $limit,
			'uri_segment'  => 4,
		);

		$this->data['data_presensi'] = $get_data;
		$this->data['value_counts'] = $total_rows;
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi SD List');
		$this->render('backend/standart/administrator/presensi_sd/presensi_sd_list_filter', $this->data);
	}

	
}


/* End of file presensi_sd.php */
/* Location: ./application/controllers/administrator/Presensi Sd.php */