<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Kinerja Staff Controller
*| --------------------------------------------------------------------------
*| Laporan Kinerja Staff site
*|
*/
class Laporan_kinerja_staff extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_kinerja_staff');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Laporan Kinerja Staffs
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('laporan_kinerja_staff_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['laporan_kinerja_staffs'] = $this->model_laporan_kinerja_staff->get($filter, $field, $this->limit_page, $offset);
		$this->data['laporan_kinerja_staff_counts'] = $this->model_laporan_kinerja_staff->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/laporan_kinerja_staff/index/',
			'total_rows'   => $this->model_laporan_kinerja_staff->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Laporan Kinerja Staff List');
		$this->render('backend/standart/administrator/laporan_kinerja_staff/laporan_kinerja_staff_list', $this->data);
	}
	
	/**
	* Add new laporan_kinerja_staffs
	*
	*/
	public function add()
	{
		$this->is_allowed('laporan_kinerja_staff_add');

		$this->template->title('Laporan Kinerja Staff New');
		$this->render('backend/standart/administrator/laporan_kinerja_staff/laporan_kinerja_staff_add', $this->data);
	}

	/**
	* Add New Laporan Kinerja Staffs
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('laporan_kinerja_staff_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_staff', 'Staff', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nilai_pimpinan', 'Penilaian Pimpinan', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sejawat', 'Penilaian Sejawat', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sendiri', 'Penilaian Sendiri', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_staff' => $this->input->post('id_staff'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'nilai_pimpinan' => $this->input->post('nilai_pimpinan'),
				'nilai_sejawat' => $this->input->post('nilai_sejawat'),
				'nilai_sendiri' => $this->input->post('nilai_sendiri'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_staff = $this->model_laporan_kinerja_staff->store($save_data);
            

			if ($save_laporan_kinerja_staff) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_laporan_kinerja_staff;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_kinerja_staff/edit/' . $save_laporan_kinerja_staff, 'Edit Laporan Kinerja Staff'),
						anchor('administrator/laporan_kinerja_staff', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/laporan_kinerja_staff/edit/' . $save_laporan_kinerja_staff, 'Edit Laporan Kinerja Staff')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_staff');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_staff');
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
	* Update view Laporan Kinerja Staffs
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('laporan_kinerja_staff_update');

		$this->data['laporan_kinerja_staff'] = $this->model_laporan_kinerja_staff->find($id);

		$this->template->title('Laporan Kinerja Staff Update');
		$this->render('backend/standart/administrator/laporan_kinerja_staff/laporan_kinerja_staff_update', $this->data);
	}

	/**
	* Update Laporan Kinerja Staffs
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_kinerja_staff_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_staff', 'Staff', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('nilai_pimpinan', 'Penilaian Pimpinan', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sejawat', 'Penilaian Sejawat', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sendiri', 'Penilaian Sendiri', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_staff' => $this->input->post('id_staff'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'nilai_pimpinan' => $this->input->post('nilai_pimpinan'),
				'nilai_sejawat' => $this->input->post('nilai_sejawat'),
				'nilai_sendiri' => $this->input->post('nilai_sendiri'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_staff = $this->model_laporan_kinerja_staff->change($id, $save_data);

			if ($save_laporan_kinerja_staff) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/laporan_kinerja_staff', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_staff');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_staff');
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
	* delete Laporan Kinerja Staffs
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('laporan_kinerja_staff_delete');

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
            set_message(cclang('has_been_deleted', 'laporan_kinerja_staff'), 'success');
        } else {
            set_message(cclang('error_delete', 'laporan_kinerja_staff'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Laporan Kinerja Staffs
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('laporan_kinerja_staff_view');

		$this->data['laporan_kinerja_staff'] = $this->model_laporan_kinerja_staff->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Laporan Kinerja Staff Detail');
		$this->render('backend/standart/administrator/laporan_kinerja_staff/laporan_kinerja_staff_view', $this->data);
	}
	
	/**
	* delete Laporan Kinerja Staffs
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$laporan_kinerja_staff = $this->model_laporan_kinerja_staff->find($id);

		
		
		return $this->model_laporan_kinerja_staff->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('laporan_kinerja_staff_export');

		//$this->model_laporan_kinerja_staff->export('laporan_kinerja_staff', 'laporan_kinerja_staff');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'nama_lengkap', 'npp', 'unit_kerja', 'umur', 'masa_kerja', 'golongan', 'kompetensi_profesional', 'kompetensi_kepribadian', 'kompetensi_sosial', 'leadership', 'nilai_prestasi', 'nilai_presensi', 'total_skor'];
		$list_kinerja = array();
		if (!empty($f) && !empty($q)) {
			$query = "select p.id_pegawai, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_staff k 
			join pegawai p on k.id_staff = p.id_pegawai 
			where k.tahun_ajaran like '%".$q."%' 
			order by k.tahun_ajaran DESC, p.nama_lengkap ASC";
			$list_kinerja = $this->mymodel->withquery($query,"result");
		}
		else if(!empty($q)) {
			$list_kinerja = $this->mymodel->withquery("select p.id_pegawai, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial,k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_staff k 
			join pegawai p on k.id_staff = p.id_pegawai 
			where p.nama_lengkap like '%".$q."%' or p.npp like '%".$q."%' or k.unit_kerja like '%".$q."%' or k.tahun_ajaran like '%".$q."%' 
			order by k.tahun_ajaran DESC, p.nama_lengkap ASC","result");
			//$this->session->set_flashdata('failed', 'Data tidak valid');
		}
		else{
			$list_kinerja = $this->mymodel->withquery("select p.id_pegawai, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_staff k 
			join pegawai p on k.id_staff = p.id_pegawai order by k.tahun_ajaran DESC, p.nama_lengkap ASC ","result");
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
		$objPHPExcel->getActiveSheet()->mergeCells('A1:N1')->getStyle('A1:N1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($judul));
		$objPHPExcel->getActiveSheet()->mergeCells('H3:M3')->getStyle('H3:M3')->applyFromArray($style); 
		$objPHPExcel->getActiveSheet()->setCellValue('H3', strtoupper("ASPEK PENILAIAN"));
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
			else if($field[$i] == "umur") {
				$objPHPExcel->getActiveSheet()->mergeCells('E3:E4')->getStyle('E3:E4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('UMUR'));
			}
			else if($field[$i] == "masa_kerja") {
				$objPHPExcel->getActiveSheet()->mergeCells('F3:F4')->getStyle('F3:F4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('MASA KERJA'));
			}
			else if($field[$i] == "golongan") {
				$objPHPExcel->getActiveSheet()->mergeCells('G3:G4')->getStyle('G3:G4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('GOLONGAN'));
			}
			else if($field[$i] == "kompetensi_profesional") {
				$objPHPExcel->getActiveSheet()->getStyle('H4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI PROFESIONAL'));
			}
			else if($field[$i] == "kompetensi_kepribadian") {
				$objPHPExcel->getActiveSheet()->getStyle('I4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI KEPRIBADIAN'));
			}
			else if($field[$i] == "kompetensi_sosial") {
				$objPHPExcel->getActiveSheet()->getStyle('J4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI SOSIAL'));
			}
			else if($field[$i] == "leadership") {
				$objPHPExcel->getActiveSheet()->getStyle('K4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('LEADERSHIP'));
			}
			else if($field[$i] == "nilai_prestasi") {
				$objPHPExcel->getActiveSheet()->getStyle('L4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('PRESTASI'));
			}
			else if($field[$i] == "nilai_presensi") {
				$objPHPExcel->getActiveSheet()->getStyle('M4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KEHADIRAN'));
			}
			else if($field[$i] == "total_skor") {
				$objPHPExcel->getActiveSheet()->mergeCells('N3:N4')->getStyle('N3:N4')->applyFromArray($style);
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
		header('Content-Disposition: attachment;filename="Laporan Kinerja Pegawai - '.date("Y-m-d").'.xls"');
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
		$this->is_allowed('laporan_kinerja_staff_export');

		$this->model_laporan_kinerja_staff->pdf('laporan_kinerja_staff', 'laporan_kinerja_staff');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('laporan_kinerja_staff_export');

		$table = $title = 'laporan_kinerja_staff';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_laporan_kinerja_staff->find($id);
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
					$umur = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$masa_kerja = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$kompetensi_profesional = (float)$worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$kompetensi_kepribadian = (float)$worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$kompetensi_sosial = (float)$worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$leadership = (float)$worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$penilaian_prestasi = (float)$worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$penilaian_presensi = (float)$worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$total_skor = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					//$rank = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
				
					// check npp staff
					$check=$this->mymodel->withquery("select * from pegawai where npp = '".$npp."'", 'row');
					
					if(empty($check)){
						//check nama staff yang mirip
						$check2=$this->mymodel->withquery("select * from pegawai where nama_lengkap like '%".$nama."%'", 'row');
						if(empty($check2)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Input NPP pegawai ".$check2->nama_lengkap." ".$nama." salah. (NPP : ".$check2->npp." ".$npp.")");
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
						"id_staff" => $check->id_pegawai,
						"unit_kerja" => $unit_kerja,
						"umur" => $umur,
						"masa_kerja" => $masa_kerja,
						"golongan" => $golongan,
						"tahun_ajaran" => $this->input->post('tahun_ajaran'),
						"kompetensi_profesional" => $kompetensi_profesional,
						"kompetensi_kepribadian" => $kompetensi_kepribadian,
						"kompetensi_sosial" => $kompetensi_sosial,
						"leadership" => $leadership,
						"nilai_prestasi" => $penilaian_prestasi,
						"nilai_presensi" => $penilaian_presensi,
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
					$check_nilai = $this->mymodel->withquery("select * from laporan_kinerja_staff where id_staff = '".$check->id_pegawai."' and tahun_ajaran = '".$this->input->post('tahun_ajaran')."'", 'row');
					
					if(!empty($check_nilai)){
						$data_laporan['created_at'] = date("Y-m-d H:i:s");
						$this->mymodel->update("laporan_kinerja_staff", $data_laporan, 'id_staff', $check->id_pegawai);
						$insertId = $check_nilai->id_laporan;
					}else{
						$insertId = $this->mymodel->insertid("laporan_kinerja_staff", $data_laporan);
					}

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


/* End of file laporan_kinerja_staff.php */
/* Location: ./application/controllers/administrator/Laporan Kinerja Staff.php */