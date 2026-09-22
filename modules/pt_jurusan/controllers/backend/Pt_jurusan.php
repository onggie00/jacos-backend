<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pt Jurusan Controller
*| --------------------------------------------------------------------------
*| Pt Jurusan site
*|
*/
class Pt_jurusan extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pt_jurusan');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pt Jurusans
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pt_jurusan_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pt_jurusans'] = $this->model_pt_jurusan->get($filter, $field, $this->limit_page, $offset);
		$this->data['pt_jurusan_counts'] = $this->model_pt_jurusan->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pt_jurusan/index/',
			'total_rows'   => $this->model_pt_jurusan->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jurusan List');
		$this->render('backend/standart/administrator/pt_jurusan/pt_jurusan_list', $this->data);
	}
	
	/**
	* Add new pt_jurusans
	*
	*/
	public function add()
	{
		$this->is_allowed('pt_jurusan_add');

		$this->template->title('Jurusan New');
		$this->render('backend/standart/administrator/pt_jurusan/pt_jurusan_add', $this->data);
	}

	/**
	* Add New Pt Jurusans
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pt_jurusan_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_perguruan_tinggi', 'Id Perguruan Tinggi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jurusan', 'Jurusan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('passing_grade', 'Passing Grade', 'trim|required|max_length[10]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_perguruan_tinggi' => $this->input->post('id_perguruan_tinggi'),
				'jurusan' => $this->input->post('jurusan'),
				'passing_grade' => $this->input->post('passing_grade'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_pt_jurusan = $this->model_pt_jurusan->store($save_data);
            

			if ($save_pt_jurusan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pt_jurusan;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pt_jurusan/edit/' . $save_pt_jurusan, 'Edit Pt Jurusan'),
						anchor('administrator/pt_jurusan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pt_jurusan/edit/' . $save_pt_jurusan, 'Edit Pt Jurusan')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_jurusan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_jurusan');
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
	* Update view Pt Jurusans
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pt_jurusan_update');

		$this->data['pt_jurusan'] = $this->model_pt_jurusan->find($id);

		$this->template->title('Jurusan Update');
		$this->render('backend/standart/administrator/pt_jurusan/pt_jurusan_update', $this->data);
	}

	/**
	* Update Pt Jurusans
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pt_jurusan_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_perguruan_tinggi', 'Id Perguruan Tinggi', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jurusan', 'Jurusan', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('passing_grade', 'Passing Grade', 'trim|required|max_length[10]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_perguruan_tinggi' => $this->input->post('id_perguruan_tinggi'),
				'jurusan' => $this->input->post('jurusan'),
				'passing_grade' => $this->input->post('passing_grade'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
			];

			
			$save_pt_jurusan = $this->model_pt_jurusan->change($id, $save_data);

			if ($save_pt_jurusan) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pt_jurusan', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_jurusan');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_jurusan');
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
	* delete Pt Jurusans
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pt_jurusan_delete');

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
            set_message(cclang('has_been_deleted', 'pt_jurusan'), 'success');
        } else {
            set_message(cclang('error_delete', 'pt_jurusan'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pt Jurusans
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pt_jurusan_view');

		$this->data['pt_jurusan'] = $this->model_pt_jurusan->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jurusan Detail');
		$this->render('backend/standart/administrator/pt_jurusan/pt_jurusan_view', $this->data);
	}
	
	/**
	* delete Pt Jurusans
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pt_jurusan = $this->model_pt_jurusan->find($id);

		
		
		return $this->model_pt_jurusan->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pt_jurusan_export');

		//$this->model_pt_jurusan->export('pt_jurusan', 'pt_jurusan');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id', 'jurusan', 'passing_grade', 'tahun_ajaran'];
		if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "nama_pt"){
                $where .= "(" . "pt.nama_pt LIKE '%" . $inputan . "%' )";
                $get_pt = $this->mymodel->withquery("select pt.id, pt.nama_pt, pt.inisial from pt_perguruan_tinggi pt where ".$where." order by nama_pt ASC","result");
            }
            else{
                $where .= "(" . "j.".$field . " LIKE '%" . $inputan . "%' )";
                $get_pt = $this->mymodel->withquery("select pt.id, pt.nama_pt, pt.inisial from pt_perguruan_tinggi pt order by nama_pt ASC","result");
            }
			
			$get_data = $this->mymodel->withquery("select j.id, pt.nama_pt, j.jurusan, j.passing_grade, j.tahun_ajaran from pt_jurusan j join pt_perguruan_tinggi pt on j.id_perguruan_tinggi = pt.id where ".$where." order by pt.nama_pt ASC","result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "j.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "nama_pt"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "j.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "pt.nama_pt LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_pt = $this->mymodel->withquery("select pt.id, pt.nama_pt, pt.inisial from pt_perguruan_tinggi pt order by nama_pt ASC","result");
	            $get_data = $this->mymodel->withquery("select  j.id, pt.nama_pt, j.jurusan, j.passing_grade, j.tahun_ajaran from pt_jurusan j join pt_perguruan_tinggi pt on j.id_perguruan_tinggi = pt.id where ".$where." order by pt.nama_pt ASC","result");
		}
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$sheet = 0;
		foreach ($get_pt as $key_pt => $value_pt) {
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($sheet);
			// Initialise the Excel row number 
			$rowCount_judul = 4;
			$objPHPExcel->getActiveSheet()->setTitle($value_pt->inisial);
			$objPHPExcel->getActiveSheet()->setCellValue("A2", strtoupper('NAMA PERGURUAN TINGGI'));
			//start of printing column names as names of MySQL fields  
			$column = 'A';
			for ($i = 0; $i < count($field_search); $i++)  
			{
				if (strpos($field_search[$i], "id") !== false) {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount_judul, strtoupper('NO'));
				}
				else{
					$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount_judul, strtoupper(str_replace("_", " ", $field_search[$i])));
				}
			    
			    $column++;
			}
			//end of adding column names  

			//start while loop to get data  
			$rowCount = 5;
			foreach ($get_data as $key => $value) {
				if ($value->nama_pt == $value_pt->nama_pt) {
					$objPHPExcel->getActiveSheet()->setCellValue("C2", $value->nama_pt);
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
				        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
				        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
						$column++;
					}
					$rowCount++;
				}
			}
			$sheet++;
		}

		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data PTN - '.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('pt_jurusan_export');

		$this->model_pt_jurusan->pdf('pt_jurusan', 'pt_jurusan');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pt_jurusan_export');

		$table = $title = 'pt_jurusan';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pt_jurusan->find($id);
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

	public function import_jurusan()
	{
		$tahun_ajaran = $this->input->post("tahun_ajaran");
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
				$nama_ptn = $worksheet->getCellByColumnAndRow(2, 2)->getValue();
				$get_ptn = $this->mymodel->withquery("select * from pt_perguruan_tinggi where nama_pt = '".$nama_ptn."'","row");
				$id_ptn = $get_ptn->id;
				for ($row = 5; $row <= $highestRow; $row++) {
					$jurusan = strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					$passing_grade = $worksheet->getCellByColumnAndRow(2, $row)->getValue();

					$data_jurusan = array(
						"id_perguruan_tinggi" => $id_ptn,
						"jurusan" => $jurusan,
						"passing_grade" => $passing_grade,
						"updated_at" => date("Y-m-d H:i:s"),
						"tahun_ajaran" => $tahun_ajaran
					);

					//cek jurusan di ptn tersebut apakah sdh tersedia
					$cek_jurusan = $this->mymodel->withquery("select * from pt_jurusan where id_perguruan_tinggi = '".$get_ptn->id."' and jurusan = '".$jurusan."' and tahun_ajaran = '".$tahun_ajaran."'","row");
					if (empty($cek_jurusan)) {
						$save_data_jurusan = $this->mymodel->insert("pt_jurusan", $data_jurusan);
					}
					else{
						$save_data_jurusan = $this->mymodel->update("pt_jurusan", $data_jurusan, "id", $cek_jurusan->id);
					}
						// dd($data_pendaftaran);
						if (!$save_data_jurusan) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import program gagal ' . $nama_ptn . " " . $data_jurusan["jurusan"]);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data PTN berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	
}


/* End of file pt_jurusan.php */
/* Location: ./application/controllers/administrator/Pt Jurusan.php */