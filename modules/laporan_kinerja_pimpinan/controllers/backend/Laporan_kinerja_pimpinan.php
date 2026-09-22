<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Kinerja Pimpinan Controller
*| --------------------------------------------------------------------------
*| Laporan Kinerja Pimpinan site
*|
*/
class Laporan_kinerja_pimpinan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_kinerja_pimpinan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Laporan Kinerja Pimpinans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('laporan_kinerja_pimpinan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['laporan_kinerja_pimpinans'] = $this->model_laporan_kinerja_pimpinan->get($filter, $field, $this->limit_page, $offset);
		$this->data['laporan_kinerja_pimpinan_counts'] = $this->model_laporan_kinerja_pimpinan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/laporan_kinerja_pimpinan/index/',
			'total_rows'   => $this->model_laporan_kinerja_pimpinan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Laporan Kinerja Pimpinan List');
		$this->render('backend/standart/administrator/laporan_kinerja_pimpinan/laporan_kinerja_pimpinan_list', $this->data);
	}
	
	/**
	* Add new laporan_kinerja_pimpinans
	*
	*/
	public function add()
	{
		$this->is_allowed('laporan_kinerja_pimpinan_add');

		$this->template->title('Laporan Kinerja Pimpinan New');
		$this->render('backend/standart/administrator/laporan_kinerja_pimpinan/laporan_kinerja_pimpinan_add', $this->data);
	}

	/**
	* Add New Laporan Kinerja Pimpinans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('laporan_kinerja_pimpinan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_pimpinan', 'Pimpinan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Unit', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('umur', 'Umur', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('masa_kerja', 'Masa Kerja', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepribadian_sosial', 'Kepribadian Sosial', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('leadership', 'Leadership', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('pengembangan_sekolah', 'Pengembangan Sekolah', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('bidang_tugas_wakil_akademik_kesiswaan', 'Bidang Tugas Wakil Akademik / Kesiswaan', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('total_skor', 'Total Skor', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_pimpinan' => $this->input->post('id_pimpinan'),
				'jenjang' => $this->input->post('jenjang'),
				'jabatan' => $this->input->post('jabatan'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'umur' => $this->input->post('umur'),
				'masa_kerja' => $this->input->post('masa_kerja'),
				'golongan' => $this->input->post('golongan'),
				'kepribadian_sosial' => $this->input->post('kepribadian_sosial'),
				'leadership' => $this->input->post('leadership'),
				'pengembangan_sekolah' => $this->input->post('pengembangan_sekolah'),
				'bidang_tugas_wakil_akademik_kesiswaan' => $this->input->post('bidang_tugas_wakil_akademik_kesiswaan'),
				'total_skor' => $this->input->post('total_skor'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_pimpinan = $this->model_laporan_kinerja_pimpinan->store($save_data);
            

			if ($save_laporan_kinerja_pimpinan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_laporan_kinerja_pimpinan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_kinerja_pimpinan/edit/' . $save_laporan_kinerja_pimpinan, 'Edit Laporan Kinerja Pimpinan'),
						anchor('administrator/laporan_kinerja_pimpinan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/laporan_kinerja_pimpinan/edit/' . $save_laporan_kinerja_pimpinan, 'Edit Laporan Kinerja Pimpinan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_pimpinan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_pimpinan');
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
	* Update view Laporan Kinerja Pimpinans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('laporan_kinerja_pimpinan_update');

		$this->data['laporan_kinerja_pimpinan'] = $this->model_laporan_kinerja_pimpinan->find($id);

		$this->template->title('Laporan Kinerja Pimpinan Update');
		$this->render('backend/standart/administrator/laporan_kinerja_pimpinan/laporan_kinerja_pimpinan_update', $this->data);
	}

	/**
	* Update Laporan Kinerja Pimpinans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_kinerja_pimpinan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_pimpinan', 'Pimpinan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang', 'Unit', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('jabatan', 'Jabatan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('umur', 'Umur', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('masa_kerja', 'Masa Kerja', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('kepribadian_sosial', 'Kepribadian Sosial', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('leadership', 'Leadership', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('pengembangan_sekolah', 'Pengembangan Sekolah', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('bidang_tugas_wakil_akademik_kesiswaan', 'Bidang Tugas Wakil Akademik / Kesiswaan', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('total_skor', 'Total Skor', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_pimpinan' => $this->input->post('id_pimpinan'),
				'jenjang' => $this->input->post('jenjang'),
				'jabatan' => $this->input->post('jabatan'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'umur' => $this->input->post('umur'),
				'masa_kerja' => $this->input->post('masa_kerja'),
				'golongan' => $this->input->post('golongan'),
				'kepribadian_sosial' => $this->input->post('kepribadian_sosial'),
				'leadership' => $this->input->post('leadership'),
				'pengembangan_sekolah' => $this->input->post('pengembangan_sekolah'),
				'bidang_tugas_wakil_akademik_kesiswaan' => $this->input->post('bidang_tugas_wakil_akademik_kesiswaan'),
				'total_skor' => $this->input->post('total_skor'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_pimpinan = $this->model_laporan_kinerja_pimpinan->change($id, $save_data);

			if ($save_laporan_kinerja_pimpinan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/laporan_kinerja_pimpinan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_pimpinan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_pimpinan');
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
	* delete Laporan Kinerja Pimpinans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('laporan_kinerja_pimpinan_delete');

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
            set_message(cclang('has_been_deleted', 'laporan_kinerja_pimpinan'), 'success');
        } else {
            set_message(cclang('error_delete', 'laporan_kinerja_pimpinan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Laporan Kinerja Pimpinans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('laporan_kinerja_pimpinan_view');

		$this->data['laporan_kinerja_pimpinan'] = $this->model_laporan_kinerja_pimpinan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Laporan Kinerja Pimpinan Detail');
		$this->render('backend/standart/administrator/laporan_kinerja_pimpinan/laporan_kinerja_pimpinan_view', $this->data);
	}
	
	/**
	* delete Laporan Kinerja Pimpinans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$laporan_kinerja_pimpinan = $this->model_laporan_kinerja_pimpinan->find($id);

		
		
		return $this->model_laporan_kinerja_pimpinan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('laporan_kinerja_pimpinan_export');

		//$this->model_laporan_kinerja_pimpinan->export('laporan_kinerja_pimpinan', 'laporan_kinerja_pimpinan');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'nama_lengkap', 'npp', 'unit_kerja', 'jabatan', 'umur', 'masa_kerja', 'golongan', 'kepribadian_sosial', 'leadership', 'pengembangan_sekolah', 'bidang_tugas_wakil_akademik_kesiswaan', 'total_skor'];
		$list_kinerja = array();
		if (!empty($f) && !empty($q)) {
			$list_kinerja = $this->mymodel->withquery("select k.id_pimpinan, k.jenjang, k.jabatan, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kepribadian_sosial, k.leadership, k.pengembangan_sekolah, k.bidang_tugas_wakil_akademik_kesiswaan, k.total_skor, k.rank, k.tahun_ajaran, p1.nama_lengkap as nama_lengkap_sd, p1.npp as npp_sd, p2.nama_lengkap as nama_lengkap_smp, p2.npp as npp_smp, p3.nama_lengkap as nama_lengkap_sma, p3.npp as npp_sma from laporan_kinerja_pimpinan k 
			left join pimpinan_sd p1 on k.id_pimpinan = p1.id_pimpinan and k.jenjang = 'SD'
			left join pimpinan_smp p2 on k.id_pimpinan = p2.id_pimpinan and k.jenjang = 'SMP'
			left join pimpinan_sma p3 on k.id_pimpinan = p3.id_pimpinan and k.jenjang = 'SMA'
			where p1.nama_lengkap like '%".$q."%' or p1.npp like '%".$q."%' or 
			p2.nama_lengkap like '%".$q."%' or p2.npp like '%".$q."%' or 
			p3.nama_lengkap like '%".$q."%' or p3.npp like '%".$q."%' or
			k.".$f." like '%".$q."%' 
			order by k.tahun_ajaran DESC, k.jenjang ASC","result");
		}
		else if(!empty($q)) {
			$list_kinerja = $this->mymodel->withquery("select k.id_pimpinan, k.jenjang, k.jabatan, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kepribadian_sosial, k.leadership, k.pengembangan_sekolah, k.bidang_tugas_wakil_akademik_kesiswaan, k.total_skor, k.rank, k.tahun_ajaran, p1.nama_lengkap as nama_lengkap_sd, p1.npp as npp_sd, p2.nama_lengkap as nama_lengkap_smp, p2.npp as npp_smp, p3.nama_lengkap as nama_lengkap_sma, p3.npp as npp_sma from laporan_kinerja_pimpinan k 
			left join pimpinan_sd p1 on k.id_pimpinan = p1.id_pimpinan and k.jenjang = 'SD'
			left join pimpinan_smp p2 on k.id_pimpinan = p2.id_pimpinan and k.jenjang = 'SMP'
			left join pimpinan_sma p3 on k.id_pimpinan = p3.id_pimpinan and k.jenjang = 'SMA'
			where p1.nama_lengkap like '%".$q."%' or p1.npp like '%".$q."%' or 
			p2.nama_lengkap like '%".$q."%' or p2.npp like '%".$q."%' or 
			p3.nama_lengkap like '%".$q."%' or p3.npp like '%".$q."%' or
			k.unit_kerja like '%".$q."%' or k.tahun_ajaran like '%".$q."%' 
			order by k.tahun_ajaran DESC, k.jenjang ASC","result");
			//$this->session->set_flashdata('failed', 'Data tidak valid');
		}
		else{
			$list_kinerja = $this->mymodel->withquery("select k.id_pimpinan, k.jenjang, k.jabatan, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kepribadian_sosial, k.leadership, k.pengembangan_sekolah, k.bidang_tugas_wakil_akademik_kesiswaan, k.total_skor, k.rank, k.tahun_ajaran, p1.nama_lengkap as nama_lengkap_sd, p1.npp as npp_sd, p2.nama_lengkap as nama_lengkap_smp, p2.npp as npp_smp, p3.nama_lengkap as nama_lengkap_sma, p3.npp as npp_sma from laporan_kinerja_pimpinan k 
			left join pimpinan_sd p1 on k.id_pimpinan = p1.id_pimpinan and k.jenjang = 'SD'
			left join pimpinan_smp p2 on k.id_pimpinan = p2.id_pimpinan and k.jenjang = 'SMP'
			left join pimpinan_sma p3 on k.id_pimpinan = p3.id_pimpinan and k.jenjang = 'SMA'
			order by k.tahun_ajaran DESC, k.jenjang ASC ","result");
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
		$style = array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
		/* for($col = 'A'; $col !== 'K'; $col++) {
			$objPHPExcel->getActiveSheet()
				->getColumnDimension($col)
				->setAutoSize(true);
		} */
		$judul = "REKAP PENILAIAN KINERJA KARYAWAN LABSCHOOL CIBUBUR TAHUN AJARAN ";
		$objPHPExcel->getActiveSheet()->mergeCells('A1:M1')->getStyle('A1:M1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($judul));
		$objPHPExcel->getActiveSheet()->mergeCells('I3:L3')->getStyle('I3:L3')->applyFromArray($style); 
		$objPHPExcel->getActiveSheet()->setCellValue('I3', strtoupper("ASPEK PENILAIAN"));
		$column = 'A';
		for ($i = 0; $i < count($field); $i++)  
		{
			if($field[$i] == "nomor") {
				$objPHPExcel->getActiveSheet()->mergeCells('A3:A4')->getStyle('A3:A4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field[$i] == "nama_lengkap") {
				$objPHPExcel->getActiveSheet()->mergeCells('B3:B4')->getStyle('B3:B4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA LENGKAP'));
			}
			else if($field[$i] == "npp") {
				$objPHPExcel->getActiveSheet()->mergeCells('C3:C4')->getStyle('C3:C4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NPP'));
			}
			else if($field[$i] == "unit_kerja") {
				$objPHPExcel->getActiveSheet()->mergeCells('D3:D4')->getStyle('D3:D4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('UNIT KERJA'));
			}
			else if($field[$i] == "jabatan") {
				$objPHPExcel->getActiveSheet()->mergeCells('E3:E4')->getStyle('E3:E4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('JABATAN'));
			}
			else if($field[$i] == "umur") {
				$objPHPExcel->getActiveSheet()->mergeCells('F3:F4')->getStyle('F3:F4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('UMUR'));
			}
			else if($field[$i] == "masa_kerja") {
				$objPHPExcel->getActiveSheet()->mergeCells('G3:G4')->getStyle('G3:G4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('MASA KERJA'));
			}
			else if($field[$i] == "golongan") {
				$objPHPExcel->getActiveSheet()->mergeCells('H3:H4')->getStyle('H3:H4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('GOLONGAN'));
			}
			else if($field[$i] == "kepribadian_sosial") {
				$objPHPExcel->getActiveSheet()->getStyle('I4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KEPRIBADIAN SOSIAL'));
			}
			else if($field[$i] == "leadership") {
				$objPHPExcel->getActiveSheet()->getStyle('J4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('LEADERSHIP'));
			}
			else if($field[$i] == "pengembangan_sekolah") {
				$objPHPExcel->getActiveSheet()->getStyle('K4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('PENGEMBANGAN SEKOLAH'));
			}
			else if($field[$i] == "bidang_tugas_wakil_akademik_kesiswaan") {
				$objPHPExcel->getActiveSheet()->getStyle('L4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('BIDANG TUGAS WAKIL AKADEMIK/KESISWAAN'));
			}
			else if($field[$i] == "total_skor") {
				$objPHPExcel->getActiveSheet()->mergeCells('M3:M4')->getStyle('M3:M4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SKOR'));
			}
			/* else if($field[$i] == "rank") {
				$objPHPExcel->getActiveSheet()->mergeCells('Q3:Q4')->getStyle('Q3:Q4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('RANK'));
			} */
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field[$i])));
			}
			$column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 5;
		foreach ($list_kinerja as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];

					if ($kolom == "nomor")  {
						$value_data = ($key+1);
					}
					else if ($kolom == "nama_lengkap"){
						$nama_pimpinan = "nama_lengkap_".strtolower($value->jenjang);
						$value_data = $value->$nama_pimpinan;
					}
					else if ($kolom == "npp"){
						$npp_pimpinan = "npp_".strtolower($value->jenjang);
						$value_data = $value->$npp_pimpinan;
					}
					elseif(!isset($value->$kolom)) { 
						$value_data = NULL;  
					}
					elseif ($value->$kolom != "")  {
						$value_data = strip_tags($value->$kolom);
					}
					else  {
						$value_data = "";  
					}
					$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->applyFromArray($style);
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Laporan Kinerja Wakil Kepala Sekolah - '.date("Y-m-d").'.xls"');
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
		$this->is_allowed('laporan_kinerja_pimpinan_export');

		$this->model_laporan_kinerja_pimpinan->pdf('laporan_kinerja_pimpinan', 'laporan_kinerja_pimpinan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('laporan_kinerja_pimpinan_export');

		$table = $title = 'laporan_kinerja_pimpinan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_laporan_kinerja_pimpinan->find($id);
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

	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_upload"]["name"])) {
			$path = $_FILES["file_upload"]["tmp_name"];
			$p=$_FILES["file_upload"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('failed', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 5; $row <= $highestRow; $row++) {
					
					if (empty($worksheet->getCellByColumnAndRow(2, $row)->getValue()) ) {
						break;
					}
					// inisial variabel
					$nama = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$npp = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$unit_kerja = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$jabatan = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$umur = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$masa_kerja = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$kepribadian_sosial = (float)$worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$leadership = (float)$worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$pengembangan_sekolah = (float)$worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$bidang_tugas_wakil_akademik_kesiswaan = (float)$worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$total_skor = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					//$rank = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
				
					// check npp pimpinan
					$check=$this->mymodel->withquery("select * from pimpinan_".strtolower($unit_kerja)." where npp = '".$npp."'", 'row');
					
					if(empty($check)){
						//check nama pimpinan yang mirip
						$check2=$this->mymodel->withquery("select * from pimpinan_".strtolower($unit_kerja)." where nama_lengkap like '%".$nama."%'", 'row');
						if(empty($check2)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Input NPP Pimpinan ".$check2->nama_lengkap." ".$nama." salah. (NPP : ".$check2->npp." ".$npp.")");
							redirect($_SERVER['HTTP_REFERER']);
						}
						else{
							$check=$check2;
						}
						/* $this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', "NPP tidak terdaftar / salah. (NPP : ".$npp.")");
						redirect($_SERVER['HTTP_REFERER']); */
					}
				
					$data_laporan = array(
						"id_pimpinan" => $check->id_pimpinan,
						"unit_kerja" => $unit_kerja,
						"jenjang" => $unit_kerja,
						"jabatan" => $jabatan,
						"umur" => $umur,
						"masa_kerja" => $masa_kerja,
						"golongan" => $golongan,
						"tahun_ajaran" => $this->input->post('tahun_ajaran'),
						"kepribadian_sosial" => $kepribadian_sosial,
						"leadership" => $leadership,
						"pengembangan_sekolah" => $pengembangan_sekolah,
						"bidang_tugas_wakil_akademik_kesiswaan" => $bidang_tugas_wakil_akademik_kesiswaan,
						"total_skor" => $total_skor,
						"updated_at" => date('Y-m-d H:i:s')						
					);
					//print_r($data_laporan);
					
					//check null
					foreach ($data_laporan as $key => $item) {
						if(!$item){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
				
					//check apakah npp tersebut di tahun ajaran yg sama sudah ada / belum
					$check_nilai = $this->mymodel->withquery("select * from laporan_kinerja_pimpinan where id_pimpinan = '".$check->id_pimpinan."' and unit_kerja = '".$unit_kerja."' and tahun_ajaran = '".$this->input->post('tahun_ajaran')."'", 'row');
					
					if(!empty($check_nilai)){
						$data_laporan['updated_at'] = date("Y-m-d H:i:s");
						$this->mymodel->update("laporan_kinerja_pimpinan", $data_laporan, 'id_pimpinan', $check->id_pimpinan);
						$insertId = $check_nilai->id_laporan;
					}else{
						$insertId = $this->mymodel->insertid("laporan_kinerja_pimpinan", $data_laporan);
					}
					//echo $this->db->last_query()."<br/>$nama<br/>";

					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal atas nama ' . $nama);
						redirect($_SERVER['HTTP_REFERER']);
					}
					
				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import data berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

}


/* End of file laporan_kinerja_pimpinan.php */
/* Location: ./application/controllers/administrator/Laporan Kinerja Pimpinan.php */