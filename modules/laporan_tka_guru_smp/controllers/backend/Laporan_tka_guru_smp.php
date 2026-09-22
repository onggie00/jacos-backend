<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Tka Guru Smp Controller
*| --------------------------------------------------------------------------
*| Laporan Tka Guru Smp site
*|
*/
class Laporan_tka_guru_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_tka_guru_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	public function index($offset = 0)
	{
		$this->is_allowed('laporan_tka_guru_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$tahun 	= $this->input->get('tahun');

		$this->data['laporan_tka_guru_smps'] = $this->model_laporan_tka_guru_smp->get($filter, $field, $this->limit_page, $offset, [], $tahun);
		$this->data['laporan_tka_guru_smp_counts'] = $this->model_laporan_tka_guru_smp->count_all($filter, $field, $tahun);
		$this->data['list_tahun'] = $this->model_laporan_tka_guru_smp->get_distinct_tahun();

		$baseUrl = 'administrator/laporan_tka_guru_smp/index/';
		if (!empty($tahun)) {
			$baseUrl .= '?tahun='.$tahun;
		}

		$config = [
			'base_url'     => $baseUrl,
			'total_rows'   => $this->model_laporan_tka_guru_smp->count_all($filter, $field, $tahun),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('TKA Guru SMP List');
		$this->render('backend/standart/administrator/laporan_tka_guru_smp/laporan_tka_guru_smp_list', $this->data);
	}
	
	public function add()
	{
		$this->is_allowed('laporan_tka_guru_smp_add');
		$this->template->title('TKA Guru SMP New');
		$this->render('backend/standart/administrator/laporan_tka_guru_smp/laporan_tka_guru_smp_add', $this->data);
	}

	public function add_save()
	{
		if (!$this->is_allowed('laporan_tka_guru_smp_add', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('umur', 'Umur', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('masa_kerja', 'Masa Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bhs_indonesia', 'Bahasa Indonesia', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('bhs_inggris', 'Bahasa Inggris', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('numerasi', 'Numerasi', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('total_skor', 'Total Skor', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required|max_length[50]');

		if ($this->form_validation->run()) {
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'umur' => $this->input->post('umur'),
				'masa_kerja' => $this->input->post('masa_kerja'),
				'golongan' => $this->input->post('golongan'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'bhs_indonesia' => $this->input->post('bhs_indonesia'),
				'bhs_inggris' => $this->input->post('bhs_inggris'),
				'numerasi' => $this->input->post('numerasi'),
				'total_skor' => $this->input->post('total_skor'),
				'tahun' => $this->input->post('tahun'),
			];

			$save_laporan_tka_guru_smp = $this->model_laporan_tka_guru_smp->store($save_data);

			if ($save_laporan_tka_guru_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $save_laporan_tka_guru_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_tka_guru_smp/edit/' . $save_laporan_tka_guru_smp, 'Edit'),
						anchor('administrator/laporan_tka_guru_smp', ' Go back to list')
					]);
				} else {
					set_message(cclang('success_save_data_redirect', [anchor('administrator/laporan_tka_guru_smp/edit/' . $save_laporan_tka_guru_smp, 'Edit')]), 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_tka_guru_smp');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				if ($this->input->post('save_type') != 'stay') {
					$this->data['redirect'] = base_url('administrator/laporan_tka_guru_smp');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	public function edit($id)
	{
		$this->is_allowed('laporan_tka_guru_smp_update');
		$this->data['laporan_tka_guru_smp'] = $this->model_laporan_tka_guru_smp->find($id);
		$this->template->title('TKA Guru SMP Update');
		$this->render('backend/standart/administrator/laporan_tka_guru_smp/laporan_tka_guru_smp_update', $this->data);
	}

	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_tka_guru_smp_update', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		
		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('umur', 'Umur', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('masa_kerja', 'Masa Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('golongan', 'Golongan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('bhs_indonesia', 'Bahasa Indonesia', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('bhs_inggris', 'Bahasa Inggris', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('numerasi', 'Numerasi', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('total_skor', 'Total Skor', 'trim|required|max_length[5]');
		$this->form_validation->set_rules('tahun', 'Tahun', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'umur' => $this->input->post('umur'),
				'masa_kerja' => $this->input->post('masa_kerja'),
				'golongan' => $this->input->post('golongan'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'bhs_indonesia' => $this->input->post('bhs_indonesia'),
				'bhs_inggris' => $this->input->post('bhs_inggris'),
				'numerasi' => $this->input->post('numerasi'),
				'total_skor' => $this->input->post('total_skor'),
				'tahun' => $this->input->post('tahun'),
			];

			$save_laporan_tka_guru_smp = $this->model_laporan_tka_guru_smp->change($id, $save_data);

			if ($save_laporan_tka_guru_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $id;
					$this->data['message'] = cclang('success_update_data_stay', [anchor('administrator/laporan_tka_guru_smp', ' Go back to list')]);
				} else {
					set_message(cclang('success_update_data_redirect', []), 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_tka_guru_smp');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				if ($this->input->post('save_type') != 'stay') {
					$this->data['redirect'] = base_url('administrator/laporan_tka_guru_smp');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}
	
	public function delete($id = null)
	{
		$this->is_allowed('laporan_tka_guru_smp_delete');
		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'laporan_tka_guru_smp'), 'success');
		} else {
			set_message(cclang('error_delete', 'laporan_tka_guru_smp'), 'error');
		}

		redirect_back();
	}

	public function delete_by_tahun()
	{
		$this->is_allowed('laporan_tka_guru_smp_delete');

		$tahun = $this->input->post('tahun');
		if (empty($tahun)) {
			set_message('Tahun tidak valid', 'error');
			redirect_back();
		}

		$this->db->where('tahun', $tahun);
		$deleted = $this->db->delete('laporan_tka_guru_smp');

		if ($deleted) {
			set_message('Data tahun '.$tahun.' berhasil dihapus', 'success');
		} else {
			set_message('Gagal menghapus data', 'error');
		}

		redirect('administrator/laporan_tka_guru_smp');
	}

	public function view($id)
	{
		$this->is_allowed('laporan_tka_guru_smp_view');
		$this->data['laporan_tka_guru_smp'] = $this->model_laporan_tka_guru_smp->join_avaiable()->filter_avaiable()->find($id);
		$this->template->title('TKA Guru SMP Detail');
		$this->render('backend/standart/administrator/laporan_tka_guru_smp/laporan_tka_guru_smp_view', $this->data);
	}
	
	private function _remove($id)
	{
		$laporan_tka_guru_smp = $this->model_laporan_tka_guru_smp->find($id);
		return $this->model_laporan_tka_guru_smp->remove($id);
	}
	
	public function export()
	{
		$this->is_allowed('laporan_tka_guru_smp_export');

		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$tahun = $this->input->get('tahun');
		$field = ['nomor', 'kode', 'nama_lengkap', 'npp', 'unit_kerja', 'mata_pelajaran', 'umur', 'masa_kerja', 'golongan', 'bhs_indonesia', 'bhs_inggris', 'numerasi', 'total_skor', 'rerata_unit', 'rerata_sekolah', 'rank_unit', 'rank'];
		$list_tka = array();
		
		$baseWhere = !empty($tahun) ? "where k.tahun = '".$tahun."' " : "";
		
		$join_sql = "from laporan_tka_guru_smp k 
			left join guru_sd g_sd on g_sd.id_guru = k.id_guru and k.jenjang = 'sd'
			left join guru_smp g_smp on g_smp.id_guru = k.id_guru and k.jenjang = 'smp'
			left join guru_sma g_sma on g_sma.id_guru = k.id_guru and k.jenjang = 'sma'";
		$nama_expr = "CASE WHEN k.jenjang = 'sd' THEN g_sd.nama_lengkap WHEN k.jenjang = 'smp' THEN g_smp.nama_lengkap WHEN k.jenjang = 'sma' THEN g_sma.nama_lengkap END";
		$npp_expr = "CASE WHEN k.jenjang = 'sd' THEN g_sd.npp WHEN k.jenjang = 'smp' THEN g_smp.npp WHEN k.jenjang = 'sma' THEN g_sma.npp END";
		
		if (!empty($f) && !empty($q)) {
			$andWhere = !empty($baseWhere) ? " and " : " where ";
			if ($f == 'guru') {
				$searchWhere = "(".$nama_expr." like '%".$q."%' or ".$npp_expr." like '%".$q."%')";
			} else {
				$searchWhere = "(k.".$f." like '%".$q."%' or ".$nama_expr." like '%".$q."%' or ".$npp_expr." like '%".$q."%')";
			}
			$query = "select k.id_guru, ".$nama_expr." as nama_lengkap, ".$npp_expr." as npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.total_skor, k.rerata_sekolah, k.rerata_unit, k.rank, k.rank_unit, k.tahun ".$join_sql."
			".$baseWhere." ".$andWhere." ".$searchWhere."
			order by k.tahun DESC, ".$nama_expr." ASC";
			$list_tka = $this->mymodel->withquery($query,"result");
		}
		else if(!empty($q)) {
			$andWhere = !empty($baseWhere) ? " and " : " where ";
			$list_tka = $this->mymodel->withquery("select k.id_guru, ".$nama_expr." as nama_lengkap, ".$npp_expr." as npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.total_skor, k.rerata_sekolah, k.rerata_unit, k.rank, k.rank_unit, k.tahun ".$join_sql."
			".$baseWhere." ".$andWhere." (".$nama_expr." like '%".$q."%' or ".$npp_expr." like '%".$q."%' or k.unit_kerja like '%".$q."%' or k.mata_pelajaran like '%".$q."%')
			order by k.tahun DESC, ".$nama_expr." ASC","result");
		}
		else{
			$list_tka = $this->mymodel->withquery("select k.id_guru, ".$nama_expr." as nama_lengkap, ".$npp_expr." as npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.total_skor, k.rerata_sekolah, k.rerata_unit, k.rank, k.rank_unit, k.tahun ".$join_sql." ".$baseWhere." order by k.tahun DESC, ".$nama_expr." ASC","result");
		}
		
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();  
		$objPHPExcel->setActiveSheetIndex(0);
		$rowCount = 3;

		$style = array(
			'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
			'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
		);

		$judul = "REKAP HASIL TES KOMPETENSI AKADEMIK GURU SMP LABSCHOOL CIBUBUR ".(!empty($tahun) ? $tahun : "");
		$objPHPExcel->getActiveSheet()->mergeCells('A1:Q1')->getStyle('A1:Q1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($judul));
		$objPHPExcel->getActiveSheet()->mergeCells('A3:A4')->getStyle('A3:A4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('A3', strtoupper('NO'));
		$objPHPExcel->getActiveSheet()->mergeCells('B3:B4')->getStyle('B3:B4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('B3', strtoupper('KODE'));
		$objPHPExcel->getActiveSheet()->mergeCells('C3:C4')->getStyle('C3:C4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('C3', strtoupper('NAMA'));
		$objPHPExcel->getActiveSheet()->mergeCells('D3:D4')->getStyle('D3:D4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('D3', strtoupper('NPP'));
		$objPHPExcel->getActiveSheet()->mergeCells('E3:E4')->getStyle('E3:E4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('E3', strtoupper('UNIT KERJA'));
		$objPHPExcel->getActiveSheet()->mergeCells('F3:F4')->getStyle('F3:F4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('F3', strtoupper('MATA PELAJARAN'));
		$objPHPExcel->getActiveSheet()->mergeCells('G3:G4')->getStyle('G3:G4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('G3', strtoupper('UMUR'));
		$objPHPExcel->getActiveSheet()->mergeCells('H3:H4')->getStyle('H3:H4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('H3', strtoupper('MASA KERJA'));
		$objPHPExcel->getActiveSheet()->mergeCells('I3:I4')->getStyle('I3:I4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('I3', strtoupper('GOLONGAN'));
		$objPHPExcel->getActiveSheet()->mergeCells('J3:J4')->getStyle('J3:J4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('J3', strtoupper('BAHASA INDONESIA'));
		$objPHPExcel->getActiveSheet()->mergeCells('K3:K4')->getStyle('K3:K4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('K3', strtoupper('BAHASA INGGRIS'));
		$objPHPExcel->getActiveSheet()->mergeCells('L3:L4')->getStyle('L3:L4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('L3', strtoupper('NUMERASI/ KUANTITATIF'));
		$objPHPExcel->getActiveSheet()->mergeCells('M3:M4')->getStyle('M3:M4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('M3', strtoupper('SKOR TOTAL'));
		$objPHPExcel->getActiveSheet()->mergeCells('N3:N4')->getStyle('N3:N4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('N3', strtoupper('Rerata Guru Per Unit'));
		$objPHPExcel->getActiveSheet()->mergeCells('O3:O4')->getStyle('O3:O4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('O3', strtoupper('Rerata Guru Labschool'));
		$objPHPExcel->getActiveSheet()->mergeCells('P3:P4')->getStyle('P3:P4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('P3', strtoupper('RANK UNIT SEKOLAH'));
		$objPHPExcel->getActiveSheet()->mergeCells('Q3:Q4')->getStyle('Q3:Q4')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('Q3', strtoupper('RANK SELURUH GURU'));
		$column = 'A';
		for ($i = 0; $i < count($field); $i++) { $column++; }

		$rowCount = 5;
		$kode_jenjang = 'SMP';
		foreach ($list_tka as $key => $value) {
			$column = 'A';
			for ($j=0; $j < count($field); $j++) {
				$kolom = $field[$j];
				if ($kolom == "nomor") {
					$value_data = ($key+1);
				}
				elseif ($kolom == "kode") {
					$value_data = 'Guru-'.$kode_jenjang.'-'.($key+1);
				}
				elseif(!isset($value->$kolom)) {
					$value_data = NULL;
				} elseif ($value->$kolom != "") {
					$value_data = strip_tags($value->$kolom);
				} else {
					$value_data = "";
				}
				$objPHPExcel->getActiveSheet()->getStyle($column.$rowCount)->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
				$column++;
			}
			$rowCount++;
		}

		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Laporan TKA Guru SMP - '.date("Y-m-d").'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}

	public function export_pdf()
	{
		$this->is_allowed('laporan_tka_guru_smp_export');
		$this->model_laporan_tka_guru_smp->pdf('laporan_tka_guru_smp', 'laporan_tka_guru_smp');
	}

	public function single_pdf($id = null)
	{
		$this->is_allowed('laporan_tka_guru_smp_export');
		$table = $title = 'laporan_tka_guru_smp';
		$this->load->library('HtmlPdf');
        $config = array('orientation' => 'p', 'format' => 'a4', 'marges' => array(5, 5, 5, 5));
        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 
        $result = $this->db->get($table);
        $data = $this->model_laporan_tka_guru_smp->find($id);
        $fields = $result->list_fields();
        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', ['data' => $data, 'fields' => $fields, 'title' => $title], TRUE);
        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function import()
	{
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_upload"]["name"])) {
			$path = $_FILES["file_upload"]["tmp_name"];
			$p = $_FILES["file_upload"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if ($ext != 'xlsx' && $ext != 'xls') {
				$this->session->set_flashdata('f_message', 'Format file harus .xlsx atau .xls');
				$this->session->set_flashdata('f_type', 'error');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			$errors = array();
			$success_count = 0;
			$npp_minus_rows = array();

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();

				for ($row = 5; $row <= $highestRow; $row++) {
					// skip baris kosong (cek kolom NAMA di C)
					$nama_val = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					if (empty($nama_val)) {
						continue;
					}

					$nama = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$nama = trim(explode(',', $nama)[0]);
					$nama = str_replace("'", "", $nama);
					$npp = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$unit_kerja = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$mata_pelajaran = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$umur = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$masa_kerja = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$bhs_indonesia = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$bhs_inggris = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$numerasi = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$total_skor = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$rerata_unit = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$rerata_sekolah = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$rank_unit = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$rank = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
					$tahun = $this->input->post('tahun');

					$row_errors = array();
					if (empty($nama)) $row_errors[] = 'NAMA (C) kosong';
					if (empty($unit_kerja)) $row_errors[] = 'UNIT KERJA (E) kosong';
					if (empty($mata_pelajaran)) $row_errors[] = 'MATA PELAJARAN (F) kosong';
					if ($bhs_indonesia === null || $bhs_indonesia === '') $row_errors[] = 'B.INDO (J) kosong';
					if ($bhs_inggris === null || $bhs_inggris === '') $row_errors[] = 'B.ING (K) kosong';
					if ($numerasi === null || $numerasi === '') $row_errors[] = 'NUMERASI (L) kosong';
					if ($total_skor === null || $total_skor === '') $row_errors[] = 'SKOR TOTAL (M) kosong';

					if (!empty($row_errors)) {
						$errors[] = 'Baris ' . $row . ': ' . implode(', ', $row_errors);
						continue;
					}

					// cari guru: smp -> sd -> sma
					$check = null;
					$jenjang = '';
					$npp_display = $npp;

					// normalisasi npp: kosong atau '-' dianggap tidak ada
					$npp_valid = (!empty($npp) && trim($npp) !== '-');

					// 1. Cek guru_smp by npp
					if ($npp_valid) {
						$check = $this->mymodel->withquery("select * from guru_smp where npp = '" . $npp . "'", 'row');
						if (!empty($check)) { $jenjang = 'smp'; }
					}

					// 2. Cek guru_smp by nama
					if (empty($check)) {
						$check = $this->mymodel->withquery("select * from guru_smp where nama_lengkap like '%" . $nama . "%'", 'row');
						if (!empty($check)) { $jenjang = 'smp'; }
					}

					// 3. Cek guru_sd by npp
					if (empty($check) && $npp_valid) {
						$check = $this->mymodel->withquery("select * from guru_sd where npp = '" . $npp . "'", 'row');
						if (!empty($check)) { $jenjang = 'sd'; }
					}

					// 4. Cek guru_sd by nama
					if (empty($check)) {
						$check = $this->mymodel->withquery("select * from guru_sd where nama_lengkap like '%" . $nama . "%'", 'row');
						if (!empty($check)) { $jenjang = 'sd'; }
					}

					// 5. Cek guru_sma by npp
					if (empty($check) && $npp_valid) {
						$check = $this->mymodel->withquery("select * from guru_sma where npp = '" . $npp . "'", 'row');
						if (!empty($check)) { $jenjang = 'sma'; }
					}

					// 6. Cek guru_sma by nama
					if (empty($check)) {
						$check = $this->mymodel->withquery("select * from guru_sma where nama_lengkap like '%" . $nama . "%'", 'row');
						if (!empty($check)) { $jenjang = 'sma'; }
					}

					// flag guru tidak ditemukan - tetap insert dengan npp '-'
					$guru_not_found = empty($check);
					if ($guru_not_found) {
						$jenjang = 'smp';
						$npp_display = '-';
					}

					// fallback umur/masa_kerja/golongan (hanya jika guru ditemukan)
					if (!$guru_not_found) {
						$kinerja_table = 'laporan_kinerja_guru_' . $jenjang;
						if (empty($umur) || empty($masa_kerja) || empty($golongan)) {
							$kinerja = $this->mymodel->withquery("SELECT umur, masa_kerja, golongan FROM " . $kinerja_table . " WHERE id_guru = '" . $check->id_guru . "' ORDER BY id_laporan DESC LIMIT 1", 'row');
							if (!empty($kinerja)) {
								if (empty($umur)) $umur = $kinerja->umur;
								if (empty($masa_kerja)) $masa_kerja = $kinerja->masa_kerja;
								if (empty($golongan)) $golongan = $kinerja->golongan;
							}
						}
					}

					$data_laporan = array(
						"id_guru" => $guru_not_found ? 0 : $check->id_guru,
						"nama_lengkap" => $nama,
						"unit_kerja" => $unit_kerja ? $unit_kerja : '-',
						"jenjang" => $jenjang,
						"mata_pelajaran" => $mata_pelajaran ? $mata_pelajaran : '-',
						"umur" => $umur,
						"masa_kerja" => $masa_kerja,
						"golongan" => $golongan,
						"bhs_indonesia" => ($bhs_indonesia !== null && $bhs_indonesia !== '') ? $bhs_indonesia : 0,
						"bhs_inggris" => ($bhs_inggris !== null && $bhs_inggris !== '') ? $bhs_inggris : 0,
						"numerasi" => ($numerasi !== null && $numerasi !== '') ? $numerasi : 0,
						"total_skor" => ($total_skor !== null && $total_skor !== '') ? $total_skor : 0,
						"rerata_sekolah" => ($rerata_sekolah !== null && $rerata_sekolah !== '') ? $rerata_sekolah : 0,
						"rerata_unit" => ($rerata_unit !== null && $rerata_unit !== '') ? $rerata_unit : 0,
						"rank" => ($rank !== null && $rank !== '') ? $rank : 0,
						"rank_unit" => ($rank_unit !== null && $rank_unit !== '') ? $rank_unit : 0,
						"tahun" => $tahun,
						"updated_at" => date('Y-m-d H:i:s')
					);

					// cek existing hanya jika guru ditemukan
					if (!$guru_not_found) {
						$check_nilai = $this->mymodel->withquery("select * from laporan_tka_guru_smp where id_guru = '" . $check->id_guru . "' and jenjang = '" . $jenjang . "' and tahun = '" . $tahun . "'", 'row');
					} else {
						$check_nilai = null;
					}

					if (!empty($check_nilai)) {
						$data_laporan['created_at'] = date("Y-m-d H:i:s");
						$this->mymodel->update("laporan_tka_guru_smp", $data_laporan, 'id_laporan', $check_nilai->id_laporan);
						$insertId = $check_nilai->id_laporan;
					} else {
						$insertId = $this->mymodel->insertid("laporan_tka_guru_smp", $data_laporan);
					}

					if ($insertId == 0) {
						$db_error = $this->db->error();
						$errors[] = 'Baris ' . $row . ': Gagal simpan "' . $nama . '" - DB Error: ' . $db_error['message'] . ' (Code: ' . $db_error['code'] . ')';
						continue;
					}

					$success_count++;
					if ($guru_not_found) {
						$npp_minus_rows[] = $row;
					}
				}
			}

			if (!empty($errors)) {
				$this->db->trans_rollback();
				$error_html = '<b>Import Gagal!</b> Ditemukan ' . count($errors) . ' error:<br><br>';
				$error_html .= '<div style="max-height:300px;overflow-y:auto;text-align:left">';
				foreach ($errors as $i => $err) {
					$error_html .= '<b>' . ($i + 1) . '.</b> ' . $err . '<br>';
				}
				$error_html .= '</div>';
				$this->session->set_flashdata('f_message', $error_html);
				$this->session->set_flashdata('f_type', 'error');
			} else {
				$warning_html = '';
				if (!empty($npp_minus_rows)) {
					$warning_html = '<br><br><b>Catatan:</b> ' . count($npp_minus_rows) . ' baris dengan NPP "-" (guru tidak ditemukan): Baris ' . implode(', ', $npp_minus_rows);
				}
				$this->db->trans_commit();
				$this->session->set_flashdata('f_message', 'Import berhasil! ' . $success_count . ' baris data diproses.' . $warning_html);
				$this->session->set_flashdata('f_type', 'success');
			}
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	/**
	* Import Detail Nilai from excel (AJAX JSON)
	*/
	public function import_detail()
	{
		if (!$this->is_allowed('laporan_tka_guru_smp_add', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		if (!isset($_FILES["file_import_detail"]["name"]) || empty($_FILES["file_import_detail"]["name"])) {
			echo json_encode(['success' => false, 'message' => 'File tidak ditemukan. Silakan pilih file Excel.']);
			exit;
		}

		$tahun = $this->input->post('tahun_detail');
		if (empty($tahun)) {
			echo json_encode(['success' => false, 'message' => 'Tahun wajib diisi.']);
			exit;
		}

		$path = $_FILES["file_import_detail"]["tmp_name"];
		$filename = $_FILES["file_import_detail"]["name"];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		if ($ext != 'xlsx' && $ext != 'xls') {
			echo json_encode(['success' => false, 'message' => 'Format file harus .xlsx atau .xls']);
			exit;
		}

		try {
			$this->load->library('Excel/PHPExcel');
			$object = PHPExcel_IOFactory::load($path);
			$worksheet = $object->getActiveSheet();
			$highestRow = $worksheet->getHighestRow();
			$highestCol = $worksheet->getHighestColumn();

			$no_columns = [];
			$highestColIndex = PHPExcel_Cell::columnIndexFromString($highestCol);
			for ($col = 0; $col < $highestColIndex; $col++) {
				$header = trim($worksheet->getCellByColumnAndRow($col, 1)->getValue());
				if (preg_match('/^No-(\d+)$/', $header, $m)) {
					$no_columns[$m[1]] = $col;
				}
			}

			if (empty($no_columns)) {
				echo json_encode(['success' => false, 'message' => 'Tidak ditemukan kolom soal (No-1, No-2, dll) di header.']);
				exit;
			}

			$indikators = $this->mymodel->withquery("SELECT id_indikator, no_urut FROM laporan_tka_indikator WHERE tahun = '".$tahun."'", "result");
			$indikator_map = [];
			foreach ($indikators as $ind) {
				$indikator_map[$ind->no_urut] = $ind->id_indikator;
			}

			$errors = [];
			$success_count = 0;
			$detail_count = 0;
			$processed_laporan = [];

			$this->db->trans_begin();

			for ($row = 2; $row <= $highestRow; $row++) {
				$nama = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());
				if (empty($nama)) continue;

				$nama_bersih = trim(explode(',', str_replace("'", "", $nama))[0]);
				$nama_like = $this->db->escape_like_str($nama_bersih);

				$id_laporan = null;

				// 1. Cek guru_sd
				$guru = $this->mymodel->withquery("SELECT id_guru FROM guru_sd WHERE nama_lengkap LIKE '%".$nama_like."%'", 'row');
				if (!empty($guru)) {
					$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE id_guru = '".$guru->id_guru."' AND jenjang = 'sd' AND tahun = '".$tahun."'", 'row');
					if (!empty($lap)) $id_laporan = $lap->id_laporan;
				}
				if (empty($id_laporan)) {
					$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE nama_lengkap LIKE '%".$nama_like."%' AND jenjang = 'sd' AND tahun = '".$tahun."'", 'row');
					if (!empty($lap)) $id_laporan = $lap->id_laporan;
				}

				// 2. Cek guru_smp
				if (empty($id_laporan)) {
					$guru = $this->mymodel->withquery("SELECT id_guru FROM guru_smp WHERE nama_lengkap LIKE '%".$nama_like."%'", 'row');
					if (!empty($guru)) {
						$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE id_guru = '".$guru->id_guru."' AND jenjang = 'smp' AND tahun = '".$tahun."'", 'row');
						if (!empty($lap)) $id_laporan = $lap->id_laporan;
					}
					if (empty($id_laporan)) {
						$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE nama_lengkap LIKE '%".$nama_like."%' AND jenjang = 'smp' AND tahun = '".$tahun."'", 'row');
						if (!empty($lap)) $id_laporan = $lap->id_laporan;
					}
				}

				// 3. Cek guru_sma
				if (empty($id_laporan)) {
					$guru = $this->mymodel->withquery("SELECT id_guru FROM guru_sma WHERE nama_lengkap LIKE '%".$nama_like."%'", 'row');
					if (!empty($guru)) {
						$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE id_guru = '".$guru->id_guru."' AND jenjang = 'sma' AND tahun = '".$tahun."'", 'row');
						if (!empty($lap)) $id_laporan = $lap->id_laporan;
					}
					if (empty($id_laporan)) {
						$lap = $this->mymodel->withquery("SELECT id_laporan FROM laporan_tka_guru_smp WHERE nama_lengkap LIKE '%".$nama_like."%' AND jenjang = 'sma' AND tahun = '".$tahun."'", 'row');
						if (!empty($lap)) $id_laporan = $lap->id_laporan;
					}
				}

				if (empty($id_laporan)) {
					$errors[] = 'Baris '.$row.': Nama "'.$nama.'" (dicari: "'.$nama_bersih.'") tidak ditemukan di semua jenjang untuk tahun '.$tahun;
					continue;
				}

				if (!in_array($id_laporan, $processed_laporan)) {
					$this->db->where('id_laporan', $id_laporan)->delete('laporan_tka_detail_smp');
					$processed_laporan[] = $id_laporan;
				}

				$row_detail = 0;
				foreach ($no_columns as $no_soal => $col_idx) {
					$keterangan = trim($worksheet->getCellByColumnAndRow($col_idx, $row)->getValue());

					if (empty($keterangan) || ($keterangan !== 'Benar' && $keterangan !== 'Salah')) continue;

					if (!isset($indikator_map[$no_soal])) {
						$errors[] = 'Baris '.$row.' ('.$nama.'): Soal No-'.$no_soal.' tidak ditemukan di indikator tahun '.$tahun;
						continue;
					}

					$save = $this->mymodel->insertid('laporan_tka_detail_smp', [
						'id_laporan' => $id_laporan,
						'id_indikator' => $indikator_map[$no_soal],
						'keterangan' => $keterangan
					]);

					if ($save) $row_detail++;
					else {
						$db_error = $this->db->error();
						$errors[] = 'Baris '.$row.' ('.$nama.') No-'.$no_soal.': Gagal simpan - '.$db_error['message'];
					}
				}

				$success_count++;
				$detail_count += $row_detail;
			}

			if (!empty($errors)) {
				$this->db->trans_rollback();
				$error_html = '<b>Import Gagal!</b> Ditemukan '.count($errors).' error:<br><br><div style="max-height:300px;overflow-y:auto;text-align:left">';
				foreach ($errors as $i => $err) $error_html .= '<b>'.($i+1).'.</b> '.htmlspecialchars($err).'<br>';
				$error_html .= '</div>';
				echo json_encode(['success' => false, 'message' => $error_html]);
			} else {
				$this->db->trans_commit();
				echo json_encode(['success' => true, 'message' => 'Import berhasil! '.$success_count.' guru diproses, '.$detail_count.' detail nilai diimport.']);
			}

		} catch (\Exception $e) {
			if ($this->db->trans_started()) $this->db->trans_rollback();
			echo json_encode(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
		}
	}

	/**
	* Get detail nilai per laporan (AJAX JSON)
	*/
	public function detail_nilai($id_laporan)
	{
		$this->is_allowed('laporan_tka_guru_smp_view');

		$laporan = $this->model_laporan_tka_guru_smp->find($id_laporan);
		if (empty($laporan)) {
			echo json_encode(['success' => false, 'message' => 'Laporan tidak ditemukan']);
			exit;
		}

		$nama_guru = '-';
		if ($laporan->id_guru > 0) {
			$guru_table = 'guru_'.$laporan->jenjang;
			$guru = $this->mymodel->withquery("SELECT nama_lengkap FROM ".$guru_table." WHERE id_guru = '".$laporan->id_guru."'", 'row');
			if (!empty($guru)) $nama_guru = $guru->nama_lengkap;
		} else {
			$nama_guru = $laporan->nama_lengkap ?? '-';
		}

		$details = $this->mymodel->withquery(
			"SELECT d.id_detail, d.keterangan, i.no_urut, i.soal, i.judul_kategori ".
			"FROM laporan_tka_detail_smp d ".
			"JOIN laporan_tka_indikator i ON i.id_indikator = d.id_indikator ".
			"WHERE d.id_laporan = '".$id_laporan."' ORDER BY i.no_urut ASC",
			"result"
		);

		echo json_encode([
			'success' => true,
			'nama_guru' => $nama_guru,
			'unit_kerja' => $laporan->unit_kerja,
			'tahun' => $laporan->tahun,
			'details' => $details
		]);
	}
}
