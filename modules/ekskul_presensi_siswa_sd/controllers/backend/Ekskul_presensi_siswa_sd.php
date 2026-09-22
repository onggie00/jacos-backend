<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Presensi Siswa Sd Controller
*| --------------------------------------------------------------------------
*| Ekskul Presensi Siswa Sd site
*|
*/
class Ekskul_presensi_siswa_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul_presensi_siswa_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskul Presensi Siswa Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ekskul_presensi_siswa_sds'] = $this->model_ekskul_presensi_siswa_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['ekskul_presensi_siswa_sd_counts'] = $this->model_ekskul_presensi_siswa_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ekskul_presensi_siswa_sd/index/',
			'total_rows'   => $this->model_ekskul_presensi_siswa_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Siswa SD List');
		$this->render('backend/standart/administrator/ekskul_presensi_siswa_sd/ekskul_presensi_siswa_sd_list', $this->data);
	}
	
	/**
	* Add new ekskul_presensi_siswa_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_add');

		$this->template->title('Presensi Siswa SD New');
		$this->render('backend/standart/administrator/ekskul_presensi_siswa_sd/ekskul_presensi_siswa_sd_add', $this->data);
	}

	/**
	* Add New Ekskul Presensi Siswa Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_presensi_siswa_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('status_absen', 'Status', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan_presensi', 'Keterangan', 'trim|required|max_length[100]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_ekskul' => $this->input->post('id_ekskul'),
				'hari_absen' => $this->input->post('hari_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'keterangan_presensi' => $this->input->post('keterangan_presensi'),
			];

			
			$save_ekskul_presensi_siswa_sd = $this->model_ekskul_presensi_siswa_sd->store($save_data);
            

			if ($save_ekskul_presensi_siswa_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul_presensi_siswa_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul_presensi_siswa_sd/edit/' . $save_ekskul_presensi_siswa_sd, 'Edit Ekskul Presensi Siswa Sd'),
						anchor('administrator/ekskul_presensi_siswa_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul_presensi_siswa_sd/edit/' . $save_ekskul_presensi_siswa_sd, 'Edit Ekskul Presensi Siswa Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_siswa_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_siswa_sd');
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
	* Update view Ekskul Presensi Siswa Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_update');

		$this->data['ekskul_presensi_siswa_sd'] = $this->model_ekskul_presensi_siswa_sd->find($id);

		$this->template->title('Presensi Siswa SD Update');
		$this->render('backend/standart/administrator/ekskul_presensi_siswa_sd/ekskul_presensi_siswa_sd_update', $this->data);
	}

	/**
	* Update Ekskul Presensi Siswa Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_presensi_siswa_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('hari_absen', 'Hari Presensi', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tanggal_absen', 'Tanggal Presensi', 'trim|required');
		$this->form_validation->set_rules('waktu_absen', 'Waktu Presensi', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('status_absen', 'Status', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('keterangan_presensi', 'Keterangan', 'trim|required|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_ekskul' => $this->input->post('id_ekskul'),
				'hari_absen' => $this->input->post('hari_absen'),
				'tanggal_absen' => $this->input->post('tanggal_absen'),
				'waktu_absen' => $this->input->post('waktu_absen'),
				'status_absen' => $this->input->post('status_absen'),
				'keterangan_presensi' => $this->input->post('keterangan_presensi'),
			];

			
			$save_ekskul_presensi_siswa_sd = $this->model_ekskul_presensi_siswa_sd->change($id, $save_data);

			if ($save_ekskul_presensi_siswa_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul_presensi_siswa_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_siswa_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_presensi_siswa_sd');
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
	* delete Ekskul Presensi Siswa Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul_presensi_siswa_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul_presensi_siswa_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskul Presensi Siswa Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_view');

		$this->data['ekskul_presensi_siswa_sd'] = $this->model_ekskul_presensi_siswa_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Presensi Siswa SD Detail');
		$this->render('backend/standart/administrator/ekskul_presensi_siswa_sd/ekskul_presensi_siswa_sd_view', $this->data);
	}
	
	/**
	* delete Ekskul Presensi Siswa Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul_presensi_siswa_sd = $this->model_ekskul_presensi_siswa_sd->find($id);

		
		
		return $this->model_ekskul_presensi_siswa_sd->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_export');

		//$this->model_ekskul_presensi_siswa_sd->export('ekskul_presensi_siswa_sd', 'ekskul_presensi_siswa_sd');
		$id_ekskul = null;
		$bln = date("m");
		$thn = date("Y");
		if (!empty($this->input->get("id_ekskul"))) {
			$id_ekskul = $this->input->get("id_ekskul");
		}
		if (!empty($this->input->get("bulan"))) {
			$bln = $this->input->get("bulan");
		}
		if (!empty($this->input->get("tahun"))) {
			$thn = $this->input->get("tahun");
		}
		$total_day_in_month = date("t", strtotime($thn."-".$bln));
		$bulan = $bln;
		$tahun = $thn;
		$field = ['nomor', 'id_ekskul', 'id_siswa', 'nis', 'nama_kelas'];
		for ($i=1; $i <= $total_day_in_month; $i++) { 
			array_push($field, $i."/".$bulan."/".$tahun);
		}
		$where = null;
		$where2 = null;
		if ($where == null && !empty($id_ekskul)) {
			$where .= "where m.id_ekskul='".$id_ekskul."'";
		}
		else if(!empty($id_ekskul)){
			$where .= " and m.id_ekskul='".$id_ekskul."'";
		}

		/*if ($where2 == null && !empty($tanggal)) {
			$where2 .= "where month(p.tanggal_absen) = '".$bulan."' and year(p.tanggal_absen) = '".$tahun."'";
		}
		else if (!empty($tanggal)) {
			$where2 .= " and month(p.tanggal_absen) = '".$bulan."' and year(p.tanggal_absen) = '".$tahun."'";
		}*/

		$get_siswa = $this->mymodel->withquery("select m.id_member, e.nama as nama_ekskul, s.id_siswa_sd_aktif as id_siswa, s.nama_lengkap, s.nis, s.id_kelas, k.label as nama_kelas from ekskul_member_sd m join ekskul e on m.id_ekskul = e.id_ekskul join siswa_sd_aktif s on m.id_siswa = s.id_siswa_sd_aktif join kelas_sd k on s.id_kelas = k.id_kelas_sd ".$where." and (m.status_member = 1) order by s.nama_lengkap ASC","result");
		foreach ($get_siswa as $key => $value) {
			for ($i=1; $i <= $total_day_in_month; $i++) { 
				$get_presensi = $this->mymodel->withquery("select * from ekskul_presensi_siswa_sd where tanggal_absen = '". $tahun."-".$bulan."-".$i."' and id_siswa_aktif = '".$value->id_siswa."'","row");
				$nama_kolom = $i."/".$bulan."/".$tahun;
				if (!empty($get_presensi)) {
					if ($get_presensi->status_absen == "Hadir") {
						$value->$nama_kolom = "H (".date("H:i", strtotime($get_presensi->waktu_absen)).")";
					}
					else if ($get_presensi->status_absen == "Tidak Hadir") {
						$value->$nama_kolom = "TH (".date("H:i", strtotime($get_presensi->waktu_absen)).")";
					}
				}
				else{
					$value->$nama_kolom = "-";
				}
			}
			/*print_r($value);
			echo "<br/><br/>";*/
		}
		
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);
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
			else if($field[$i] == "id_ekskul") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('EKSTRAKURIKULER'));
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
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "nomor") !== false) {
		        	$value_data = ($key+1);
		        }
		        if (strpos($kolom, "id_ekskul") !== false) {
		        	$value_data = $value->nama_ekskul;
		        }
		        if (strpos($kolom, "id_siswa") !== false) {
		        	$value_data = $value->nama_lengkap;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Presensi Ekskul '.$get_siswa[0]->nama_ekskul.' Siswa SD - '.formatBulan(date("Y-m-d", strtotime($tahun."-".$bln."-1"))).' '.$tahun.' - '.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('ekskul_presensi_siswa_sd_export');

		$this->model_ekskul_presensi_siswa_sd->pdf('ekskul_presensi_siswa_sd', 'ekskul_presensi_siswa_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_presensi_siswa_sd_export');

		$table = $title = 'ekskul_presensi_siswa_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul_presensi_siswa_sd->find($id);
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
		$this->is_allowed('ekskul_presensi_siswa_sd_list');
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
		if (!empty($get['bulan']) && !empty($get['tahun'])) {
			if ($where == "") {
				$where .= "where month(p.tanggal_absen) = '".$get['bulan']."' and year(p.tanggal_absen) = '".$get['tahun']."'";
			}
			else{
				$where .= " and month(p.tanggal_absen) = '".$get['bulan']."' and year(p.tanggal_absen) = '".$get['tahun']."'";
			}
		}

		$get_data = $this->mymodel->withquery("select p.*, s.nama_lengkap, k.label as nama_kelas, e.nama as nama_ekskul from ekskul_presensi_siswa_sd p join siswa_sd_aktif s on p.id_siswa_aktif = s.id_siswa_sd_aktif join kelas_sd k on s.id_kelas = k.id_kelas_sd join ekskul e on p.id_ekskul = e.id_ekskul ".$where." order by s.nama_lengkap ASC limit ".$offset.",".$limit." ","result");
		$total_rows = count($get_data);

		$config = array(
			'base_url'     => 'administrator/ekskul_presensi_siswa_sd/filter_presensi/',
			'total_rows'   => $total_rows,
			'per_page'     => $limit,
			'uri_segment'  => 4,
		);

		$this->data['data_presensi'] = $get_data;
		$this->data['value_counts'] = $total_rows;
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Presensi Ekstrakurikuler SD List');
		$this->render('backend/standart/administrator/ekskul_presensi_siswa_sd/ekskul_presensi_siswa_sd_list_filter', $this->data);
	}
}


/* End of file ekskul_presensi_siswa_sd.php */
/* Location: ./application/controllers/administrator/Ekskul Presensi Siswa Sd.php */