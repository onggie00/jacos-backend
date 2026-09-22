<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pt To Siswa Controller
*| --------------------------------------------------------------------------
*| Pt To Siswa site
*|
*/
class Pt_to_siswa extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pt_to_siswa');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pt To Siswas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pt_to_siswa_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pt_to_siswas'] = $this->model_pt_to_siswa->get($filter, $field, $this->limit_page, $offset);
		$this->data['pt_to_siswa_counts'] = $this->model_pt_to_siswa->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pt_to_siswa/index/',
			'total_rows'   => $this->model_pt_to_siswa->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('UTBK Siswa List');
		$this->render('backend/standart/administrator/pt_to_siswa/pt_to_siswa_list', $this->data);
	}
	
	/**
	* Add new pt_to_siswas
	*
	*/
	public function add()
	{
		$this->is_allowed('pt_to_siswa_add');

		$this->template->title('UTBK Siswa New');
		$this->render('backend/standart/administrator/pt_to_siswa/pt_to_siswa_add', $this->data);
	}

	/**
	* Add New Pt To Siswas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pt_to_siswa_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		$this->form_validation->set_rules('nilai_utbk1', 'Nilai UTBK 1', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk2', 'Nilai UTBK 2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk3', 'Nilai UTBK 3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk4', 'Nilai UTBK 4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_nasional', 'Nilai Nasional', 'trim|required|max_length[5]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_kelas' => $this->input->post('id_kelas'),
				'nilai_utbk1' => $this->input->post('nilai_utbk1'),
				'nilai_utbk2' => $this->input->post('nilai_utbk2'),
				'nilai_utbk3' => $this->input->post('nilai_utbk3'),
				'nilai_utbk4' => $this->input->post('nilai_utbk4'),
				'nilai_nasional' => $this->input->post('nilai_nasional'),
			];

			
			$save_pt_to_siswa = $this->model_pt_to_siswa->store($save_data);
            

			if ($save_pt_to_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pt_to_siswa;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pt_to_siswa/edit/' . $save_pt_to_siswa, 'Edit Pt To Siswa'),
						anchor('administrator/pt_to_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pt_to_siswa/edit/' . $save_pt_to_siswa, 'Edit Pt To Siswa')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_to_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_to_siswa');
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
	* Update view Pt To Siswas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pt_to_siswa_update');

		$this->data['pt_to_siswa'] = $this->model_pt_to_siswa->find($id);

		$this->template->title('UTBK Siswa Update');
		$this->render('backend/standart/administrator/pt_to_siswa/pt_to_siswa_update', $this->data);
	}

	/**
	* Update Pt To Siswas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pt_to_siswa_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('jenjang', 'Jenjang', 'trim|required');
		$this->form_validation->set_rules('id_siswa_aktif', 'Siswa', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'trim|required');
		$this->form_validation->set_rules('nilai_utbk1', 'Nilai UTBK 1', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk2', 'Nilai UTBK 2', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk3', 'Nilai UTBK 3', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_utbk4', 'Nilai UTBK 4', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('nilai_nasional', 'Nilai Nasional', 'trim|required|max_length[5]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'jenjang' => $this->input->post('jenjang'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'id_kelas' => $this->input->post('id_kelas'),
				'nilai_utbk1' => $this->input->post('nilai_utbk1'),
				'nilai_utbk2' => $this->input->post('nilai_utbk2'),
				'nilai_utbk3' => $this->input->post('nilai_utbk3'),
				'nilai_utbk4' => $this->input->post('nilai_utbk4'),
				'nilai_nasional' => $this->input->post('nilai_nasional'),
			];

			
			$save_pt_to_siswa = $this->model_pt_to_siswa->change($id, $save_data);

			if ($save_pt_to_siswa) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pt_to_siswa', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_to_siswa');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_to_siswa');
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
	* delete Pt To Siswas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pt_to_siswa_delete');

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
            set_message(cclang('has_been_deleted', 'pt_to_siswa'), 'success');
        } else {
            set_message(cclang('error_delete', 'pt_to_siswa'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pt To Siswas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pt_to_siswa_view');

		$this->data['pt_to_siswa'] = $this->model_pt_to_siswa->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('UTBK Siswa Detail');
		$this->render('backend/standart/administrator/pt_to_siswa/pt_to_siswa_view', $this->data);
	}
	
	/**
	* delete Pt To Siswas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pt_to_siswa = $this->model_pt_to_siswa->find($id);

		
		
		return $this->model_pt_to_siswa->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pt_to_siswa_export');

		//$this->model_pt_to_siswa->export('pt_to_siswa', 'pt_to_siswa');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id', 'jenjang','id_siswa_aktif', 'id_kelas', 'nilai_utbk1', 'nilai_utbk2', 'nilai_utbk3', 'nilai_utbk4', 'nilai_nasional', 'tahun_ajaran'];
		if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "id_kelas"){
                $where .= "(" . "k.id_kelas LIKE '%" . $inputan . "%' )";
            }
            else{
                $where .= "(" . "pt.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select pt.* from pt_to_siswa pt where ".$where." order by pt.id_kelas ASC, pt.jenjang DESC,pt.nilai_nasional DESC","result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "pt.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "id_kelas"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "pt.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select pt.* from pt_to_siswa pt where ".$where." order by pt.id_kelas ASC, pt.jenjang DESC,pt.nilai_nasional DESC","result");
		}
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);  
		// Initialise the Excel row number 
		$rowCount = 1;

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 0; $i < count($field_search); $i++)  
		{
			//if (strpos($field_search[$i], "id") !== false) {
			if($field_search[$i] == "id") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if($field_search[$i] == "id_siswa_aktif") {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA'));
			}
			else{
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    
		    $column++;
		}
		//end of adding column names

		//start while loop to get data  
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			$jenjang = $value->jenjang;
			$get_siswa = $this->mymodel->withquery("select s.nama_lengkap, k.label as nama_kelas from siswa_".strtolower($jenjang)."_aktif s join kelas_".strtolower($jenjang)." k on s.id_kelas = k.id_kelas_".strtolower($jenjang)." where  s.id_siswa_".strtolower($jenjang)."_aktif = '".$value->id_siswa_aktif."' ","row");
			$column = 'A';
			for ($j=0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) { 
		            $value_data = NULL;  
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "id") !== false) {
		        	$value_data = ($key+1);
		        }
		        if (strpos($kolom, "id_kelas") !== false) {
		        	$value_data = $get_siswa->nama_kelas;
		        }
		        if (strpos($kolom, "id_siswa_aktif") !== false) {
		        	$value_data = $get_siswa->nama_lengkap;
		        }
		        if (strpos($kolom, "jenjang") !== false) {
		        	$value_data = strtoupper($value->jenjang);
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data UTBK Siswa - '.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('pt_to_siswa_export');

		$this->model_pt_to_siswa->pdf('pt_to_siswa', 'pt_to_siswa');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pt_to_siswa_export');

		$table = $title = 'pt_to_siswa';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pt_to_siswa->find($id);
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

	public function import_utbk()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' || $ext!='xls'){
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					$jenjang = strtolower(strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue()));
					$nama_lengkap = strtoupper($worksheet->getCellByColumnAndRow(2, $row)->getValue());
					$kelas = strtoupper($worksheet->getCellByColumnAndRow(3, $row)->getValue());
					$get_data = $this->mymodel->withquery("select s.id_siswa_".$jenjang."_aktif as id_siswa_aktif, s.nama_lengkap, k.id_kelas_".$jenjang." as id_kelas from siswa_".$jenjang."_aktif s join kelas_".$jenjang." k on s.id_kelas = k.id_kelas_".$jenjang." where s.nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and k.label = '".$kelas."' ","row");
					
					if (!empty($get_data)) {
						$data_siswa = array(
							"jenjang" => strtoupper($jenjang),
							"id_siswa_aktif" => $get_data->id_siswa_aktif,
							"id_kelas" => $get_data->id_kelas,
							"nilai_utbk1" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
							"nilai_utbk2" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
							"nilai_utbk3" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
							"nilai_utbk4" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
							"nilai_nasional" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
							"tahun_ajaran" => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
							"created_at" => date("Y-m-d H:i:s"),
							"updated_at" => date("Y-m-d H:i:s"),
						);

						//cek nama_ptn apakah sdh tersedia
						$tahun_ajaran = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
						$cek_siswa = $this->mymodel->withquery("select * from pt_to_siswa where id_siswa_aktif = '".$get_data->id_siswa_aktif."' and id_kelas = '".$get_data->id_kelas."' and jenjang = '".strtoupper($jenjang)."' and tahun_ajaran = '".$tahun_ajaran."'","row");
						if (empty($cek_siswa) && !empty($data_siswa)) {
							$save_data_siswa = $this->mymodel->insert("pt_to_siswa", $data_siswa);
						}
						else{
							$save_data_siswa = $this->mymodel->update("pt_to_siswa", $data_siswa, "id", $cek_siswa->id);
						}
						// dd($data_pendaftaran);
						if (!$save_data_siswa) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import program gagal baris ke ' . $row);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
					else{

					}
				}
			}
			//echo $this->db->last_query();
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data UTBK berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	
}


/* End of file pt_to_siswa.php */
/* Location: ./application/controllers/administrator/Pt To Siswa.php */