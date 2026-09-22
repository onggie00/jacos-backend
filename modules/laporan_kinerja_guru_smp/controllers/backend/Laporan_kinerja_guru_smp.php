<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Laporan Kinerja Guru Smp Controller
*| --------------------------------------------------------------------------
*| Laporan Kinerja Guru Smp site
*|
*/
class Laporan_kinerja_guru_smp extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_laporan_kinerja_guru_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Laporan Kinerja Guru Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('laporan_kinerja_guru_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['laporan_kinerja_guru_smps'] = $this->model_laporan_kinerja_guru_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['laporan_kinerja_guru_smp_counts'] = $this->model_laporan_kinerja_guru_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/laporan_kinerja_guru_smp/index/',
			'total_rows'   => $this->model_laporan_kinerja_guru_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Laporan Kinerja Guru SMP List');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_smp/laporan_kinerja_guru_smp_list', $this->data);
	}
	
	/**
	* Add new laporan_kinerja_guru_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('laporan_kinerja_guru_smp_add');

		$this->template->title('Laporan Kinerja Guru SMP New');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_smp/laporan_kinerja_guru_smp_add', $this->data);
	}

	/**
	* Add New Laporan Kinerja Guru Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('laporan_kinerja_guru_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nilai_pimpinan', 'Penilaian Pimpinan', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sejawat', 'Penilaian Sejawat', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_siswa', 'Penilaian Siswa', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_sendiri', 'Penilaian Sendiri', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[2]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'nilai_pimpinan' => $this->input->post('nilai_pimpinan'),
				'nilai_sejawat' => $this->input->post('nilai_sejawat'),
				'nilai_siswa' => $this->input->post('nilai_siswa'),
				'nilai_sendiri' => $this->input->post('nilai_sendiri'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_guru_smp = $this->model_laporan_kinerja_guru_smp->store($save_data);
            

			if ($save_laporan_kinerja_guru_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_laporan_kinerja_guru_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/laporan_kinerja_guru_smp/edit/' . $save_laporan_kinerja_guru_smp, 'Edit Laporan Kinerja Guru Smp'),
						anchor('administrator/laporan_kinerja_guru_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/laporan_kinerja_guru_smp/edit/' . $save_laporan_kinerja_guru_smp, 'Edit Laporan Kinerja Guru Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_smp');
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
	* Update view Laporan Kinerja Guru Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('laporan_kinerja_guru_smp_update');

		$this->data['laporan_kinerja_guru_smp'] = $this->model_laporan_kinerja_guru_smp->find($id);

		$this->template->title('Laporan Kinerja Guru SMP Update');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_smp/laporan_kinerja_guru_smp_update', $this->data);
	}

	/**
	* Update Laporan Kinerja Guru Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('laporan_kinerja_guru_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_guru', 'Guru', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('unit_kerja', 'Unit Kerja', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('mata_pelajaran', 'Mata Pelajaran', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kompetensi_pedagogik', 'Kompetensi Pedagogik', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('kompetensi_profesional', 'Kompetensi Profesional', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('kompetensi_kepribadian', 'Kompetensi Kepribadian', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('kompetensi_sosial', 'Kompetensi Sosial', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('leadership', 'Leadership', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('nilai_prestasi', 'Penilaian Prestasi', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('nilai_presensi', 'Penilaian Presensi', 'trim|required|max_length[3]');
		$this->form_validation->set_rules('rank', 'Rank', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[50]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_guru' => $this->input->post('id_guru'),
				'unit_kerja' => $this->input->post('unit_kerja'),
				'mata_pelajaran' => $this->input->post('mata_pelajaran'),
				'kompetensi_pedagogik' => $this->input->post('kompetensi_pedagogik'),
				'kompetensi_profesional' => $this->input->post('kompetensi_profesional'),
				'kompetensi_kepribadian' => $this->input->post('kompetensi_kepribadian'),
				'kompetensi_sosial' => $this->input->post('kompetensi_sosial'),
				'leadership' => $this->input->post('leadership'),
				'nilai_prestasi' => $this->input->post('nilai_prestasi'),
				'nilai_presensi' => $this->input->post('nilai_presensi'),
				'rank' => $this->input->post('rank'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_laporan_kinerja_guru_smp = $this->model_laporan_kinerja_guru_smp->change($id, $save_data);

			if ($save_laporan_kinerja_guru_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/laporan_kinerja_guru_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/laporan_kinerja_guru_smp');
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
	* delete Laporan Kinerja Guru Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('laporan_kinerja_guru_smp_delete');

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
            set_message(cclang('has_been_deleted', 'laporan_kinerja_guru_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'laporan_kinerja_guru_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Laporan Kinerja Guru Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('laporan_kinerja_guru_smp_view');

		$this->data['laporan_kinerja_guru_smp'] = $this->model_laporan_kinerja_guru_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Laporan Kinerja Guru SMP Detail');
		$this->render('backend/standart/administrator/laporan_kinerja_guru_smp/laporan_kinerja_guru_smp_view', $this->data);
	}
	
	/**
	* delete Laporan Kinerja Guru Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$laporan_kinerja_guru_smp = $this->model_laporan_kinerja_guru_smp->find($id);

		
		
		return $this->model_laporan_kinerja_guru_smp->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('laporan_kinerja_guru_smp_export');

		//$this->model_laporan_kinerja_guru_smp->export('laporan_kinerja_guru_smp', 'laporan_kinerja_guru_smp');
		$f = $this->input->get('f');
		$q = $this->input->get('q');
		$where = "";
		$field = ['nomor', 'nama_lengkap', 'npp', 'unit_kerja', 'mata_pelajaran', 'umur', 'masa_kerja', 'golongan', 'kompetensi_pedagogik', 'kompetensi_profesional', 'kompetensi_kepribadian', 'kompetensi_sosial', 'leadership', 'nilai_prestasi', 'nilai_presensi', 'total_skor'];
		$list_kinerja = array();
		if (!empty($f) && !empty($q)) {
			$query = "select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_guru_smp k 
			join guru_smp p on k.id_guru = p.id_guru 
			where k.tahun_ajaran like '%".$q."%' or k.mata_pelajaran like '%".$q."%'
			order by k.tahun_ajaran DESC, p.nama_lengkap ASC";
			$list_kinerja = $this->mymodel->withquery($query,"result");
		}
		else if(!empty($q)) {
			$list_kinerja = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial,k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_guru_smp k 
			join guru_smp p on k.id_guru = p.id_guru 
			where p.nama_lengkap like '%".$q."%' or p.npp like '%".$q."%' or k.unit_kerja like '%".$q."%' or k.tahun_ajaran like '%".$q."%' 
			order by k.tahun_ajaran DESC, p.nama_lengkap ASC","result");
			//$this->session->set_flashdata('failed', 'Data tidak valid');
		}
		else{
			$list_kinerja = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.mata_pelajaran, k.umur, k.masa_kerja, k.golongan, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.total_skor from laporan_kinerja_guru_smp k 
			join guru_smp p on k.id_guru = p.id_guru order by k.tahun_ajaran DESC, p.nama_lengkap ASC ","result");
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
		$objPHPExcel->getActiveSheet()->mergeCells('A1:P1')->getStyle('A1:P1')->applyFromArray($style);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($judul));
		$objPHPExcel->getActiveSheet()->mergeCells('I3:O3')->getStyle('I3:O3')->applyFromArray($style); 
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
			else if($field[$i] == "mata_pelajaran") {
				$objPHPExcel->getActiveSheet()->mergeCells('E3:E4')->getStyle('E3:E4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('MATA PELAJARAN'));
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
			else if($field[$i] == "kompetensi_pedagogik") {
				$objPHPExcel->getActiveSheet()->getStyle('I4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI PEDAGOGIK'));
			}
			else if($field[$i] == "kompetensi_profesional") {
				$objPHPExcel->getActiveSheet()->getStyle('J4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI PROFESIONAL'));
			}
			else if($field[$i] == "kompetensi_kepribadian") {
				$objPHPExcel->getActiveSheet()->getStyle('K4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI KEPRIBADIAN'));
			}
			else if($field[$i] == "kompetensi_sosial") {
				$objPHPExcel->getActiveSheet()->getStyle('L4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KOMPETENSI SOSIAL'));
			}
			else if($field[$i] == "leadership") {
				$objPHPExcel->getActiveSheet()->getStyle('M4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('LEADERSHIP'));
			}
			else if($field[$i] == "nilai_prestasi") {
				$objPHPExcel->getActiveSheet()->getStyle('N4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('PRESTASI'));
			}
			else if($field[$i] == "nilai_presensi") {
				$objPHPExcel->getActiveSheet()->getStyle('O4')->applyFromArray($style);
				$objPHPExcel->getActiveSheet()->setCellValue($column.($rowCount+1), strtoupper('KEHADIRAN'));
			}
			else if($field[$i] == "total_skor") {
				$objPHPExcel->getActiveSheet()->mergeCells('P3:P4')->getStyle('P3:P4')->applyFromArray($style);
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
		header('Content-Disposition: attachment;filename="Laporan Kinerja Guru SMP - '.date("Y-m-d").'.xls"');
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
		$this->is_allowed('laporan_kinerja_guru_smp_export');

		$this->model_laporan_kinerja_guru_smp->pdf('laporan_kinerja_guru_smp', 'laporan_kinerja_guru_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('laporan_kinerja_guru_smp_export');

		$table = $title = 'laporan_kinerja_guru_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_laporan_kinerja_guru_smp->find($id);
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
					$mata_pelajaran = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$umur = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$masa_kerja = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$golongan = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
					$kompetensi_pedagogik = (float)$worksheet->getCellByColumnAndRow(8, $row)->getValue();
					$kompetensi_profesional = (float)$worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$kompetensi_kepribadian = (float)$worksheet->getCellByColumnAndRow(10, $row)->getValue();
					$kompetensi_sosial = (float)$worksheet->getCellByColumnAndRow(11, $row)->getValue();
					$leadership = (float)$worksheet->getCellByColumnAndRow(12, $row)->getValue();
					$penilaian_prestasi = (float)$worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$penilaian_presensi = (float)$worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$total_skor = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					//$rank = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
				
					// check npp staff
					$check=$this->mymodel->withquery("select * from guru_smp where npp = '".$npp."'", 'row');
					
					if(empty($check)){
						//check nama staff yang mirip
						$check2=$this->mymodel->withquery("select * from guru_smp where nama_lengkap like '%".$nama."%'", 'row');
						if(empty($check2)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Input NPP guru_smp ".$check2->nama_lengkap." ".$nama." salah. (NPP : ".$check2->npp." ".$npp.")");
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
						"id_guru" => $check->id_guru,
						"unit_kerja" => $unit_kerja,
						"mata_pelajaran" => $mata_pelajaran,
						"umur" => $umur,
						"masa_kerja" => $masa_kerja,
						"golongan" => $golongan,
						"tahun_ajaran" => $this->input->post('tahun_ajaran'),
						"kompetensi_pedagogik" => $kompetensi_pedagogik,
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
					/* foreach ($data_laporan as $key => $item) {
						if(!$item){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					} */
				
					//check apakah npp tersebut di tahun ajaran yg sama sudah ada / belum
					$check_nilai = $this->mymodel->withquery("select * from laporan_kinerja_guru_smp where id_guru = '".$check->id_guru."' and tahun_ajaran = '".$this->input->post('tahun_ajaran')."'", 'row');
					
					if(!empty($check_nilai)){
						$data_laporan['created_at'] = date("Y-m-d H:i:s");
						$this->mymodel->update("laporan_kinerja_guru_smp", $data_laporan, 'id_guru', $check->id_guru);
						$insertId = $check_nilai->id_laporan;
					}else{
						$insertId = $this->mymodel->insertid("laporan_kinerja_guru_smp", $data_laporan);
					}

					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal atas nama ' . $nama . json_encode($data_laporan));
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


/* End of file laporan_kinerja_guru_smp.php */
/* Location: ./application/controllers/administrator/Laporan Kinerja Guru Smp.php */