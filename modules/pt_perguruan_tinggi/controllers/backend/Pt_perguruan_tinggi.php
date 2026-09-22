<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pt Perguruan Tinggi Controller
*| --------------------------------------------------------------------------
*| Pt Perguruan Tinggi site
*|
*/
class Pt_perguruan_tinggi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pt_perguruan_tinggi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pt Perguruan Tinggis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pt_perguruan_tinggi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pt_perguruan_tinggis'] = $this->model_pt_perguruan_tinggi->get($filter, $field, $this->limit_page, $offset);
		$this->data['pt_perguruan_tinggi_counts'] = $this->model_pt_perguruan_tinggi->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pt_perguruan_tinggi/index/',
			'total_rows'   => $this->model_pt_perguruan_tinggi->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Perguruan Tinggi List');
		$this->render('backend/standart/administrator/pt_perguruan_tinggi/pt_perguruan_tinggi_list', $this->data);
	}
	
	/**
	* Add new pt_perguruan_tinggis
	*
	*/
	public function add()
	{
		$this->is_allowed('pt_perguruan_tinggi_add');

		$this->template->title('Perguruan Tinggi New');
		$this->render('backend/standart/administrator/pt_perguruan_tinggi/pt_perguruan_tinggi_add', $this->data);
	}

	/**
	* Add New Pt Perguruan Tinggis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pt_perguruan_tinggi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_pt', 'Perguruan Tinggi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('inisial', 'Singkatan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|max_length[120]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|max_length[255]');
		

		if ($this->form_validation->run()) {
			$pt_perguruan_tinggi_logo_ptn_uuid = $this->input->post('pt_perguruan_tinggi_logo_ptn_uuid');
			$pt_perguruan_tinggi_logo_ptn_name = $this->input->post('pt_perguruan_tinggi_logo_ptn_name');
		
			$save_data = [
				'nama_pt' => $this->input->post('nama_pt'),
				'inisial' => $this->input->post('inisial'),
				'provinsi' => $this->input->post('provinsi'),
				'kota' => $this->input->post('kota'),
				'deskripsi' => $this->input->post('deskripsi'),
			];

			if (!is_dir(FCPATH . '/uploads/pt_perguruan_tinggi/')) {
				mkdir(FCPATH . '/uploads/pt_perguruan_tinggi/');
			}

			if (!empty($pt_perguruan_tinggi_logo_ptn_name)) {
				$pt_perguruan_tinggi_logo_ptn_name_copy = date('YmdHis') . '-' . $pt_perguruan_tinggi_logo_ptn_name;

				rename(FCPATH . 'uploads/tmp/' . $pt_perguruan_tinggi_logo_ptn_uuid . '/' . $pt_perguruan_tinggi_logo_ptn_name, 
						FCPATH . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi_logo_ptn_name_copy);

				if (!is_file(FCPATH . '/uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi_logo_ptn_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['logo_ptn'] = $pt_perguruan_tinggi_logo_ptn_name_copy;
			}
		
			
			$save_pt_perguruan_tinggi = $this->model_pt_perguruan_tinggi->store($save_data);
            

			if ($save_pt_perguruan_tinggi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pt_perguruan_tinggi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pt_perguruan_tinggi/edit/' . $save_pt_perguruan_tinggi, 'Edit Pt Perguruan Tinggi'),
						anchor('administrator/pt_perguruan_tinggi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pt_perguruan_tinggi/edit/' . $save_pt_perguruan_tinggi, 'Edit Pt Perguruan Tinggi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_perguruan_tinggi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_perguruan_tinggi');
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
	* Update view Pt Perguruan Tinggis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pt_perguruan_tinggi_update');

		$this->data['pt_perguruan_tinggi'] = $this->model_pt_perguruan_tinggi->find($id);

		$this->template->title('Perguruan Tinggi Update');
		$this->render('backend/standart/administrator/pt_perguruan_tinggi/pt_perguruan_tinggi_update', $this->data);
	}

	/**
	* Update Pt Perguruan Tinggis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pt_perguruan_tinggi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_pt', 'Perguruan Tinggi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('inisial', 'Singkatan', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|max_length[120]');
		$this->form_validation->set_rules('deskripsi', 'Deskripsi', 'trim|max_length[255]');
		
		if ($this->form_validation->run()) {
			$pt_perguruan_tinggi_logo_ptn_uuid = $this->input->post('pt_perguruan_tinggi_logo_ptn_uuid');
			$pt_perguruan_tinggi_logo_ptn_name = $this->input->post('pt_perguruan_tinggi_logo_ptn_name');
		
			$save_data = [
				'nama_pt' => $this->input->post('nama_pt'),
				'inisial' => $this->input->post('inisial'),
				'provinsi' => $this->input->post('provinsi'),
				'kota' => $this->input->post('kota'),
				'deskripsi' => $this->input->post('deskripsi'),
			];

			if (!is_dir(FCPATH . '/uploads/pt_perguruan_tinggi/')) {
				mkdir(FCPATH . '/uploads/pt_perguruan_tinggi/');
			}

			if (!empty($pt_perguruan_tinggi_logo_ptn_uuid)) {
				$pt_perguruan_tinggi_logo_ptn_name_copy = date('YmdHis') . '-' . $pt_perguruan_tinggi_logo_ptn_name;

				rename(FCPATH . 'uploads/tmp/' . $pt_perguruan_tinggi_logo_ptn_uuid . '/' . $pt_perguruan_tinggi_logo_ptn_name, 
						FCPATH . 'uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi_logo_ptn_name_copy);

				if (!is_file(FCPATH . '/uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi_logo_ptn_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['logo_ptn'] = $pt_perguruan_tinggi_logo_ptn_name_copy;
			}
		
			
			$save_pt_perguruan_tinggi = $this->model_pt_perguruan_tinggi->change($id, $save_data);

			if ($save_pt_perguruan_tinggi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pt_perguruan_tinggi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pt_perguruan_tinggi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pt_perguruan_tinggi');
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
	* delete Pt Perguruan Tinggis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pt_perguruan_tinggi_delete');

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
            set_message(cclang('has_been_deleted', 'pt_perguruan_tinggi'), 'success');
        } else {
            set_message(cclang('error_delete', 'pt_perguruan_tinggi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pt Perguruan Tinggis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pt_perguruan_tinggi_view');

		$this->data['pt_perguruan_tinggi'] = $this->model_pt_perguruan_tinggi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Perguruan Tinggi Detail');
		$this->render('backend/standart/administrator/pt_perguruan_tinggi/pt_perguruan_tinggi_view', $this->data);
	}
	
	/**
	* delete Pt Perguruan Tinggis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pt_perguruan_tinggi = $this->model_pt_perguruan_tinggi->find($id);

		if (!empty($pt_perguruan_tinggi->logo_ptn)) {
			$path = FCPATH . '/uploads/pt_perguruan_tinggi/' . $pt_perguruan_tinggi->logo_ptn;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pt_perguruan_tinggi->remove($id);
	}
	
	/**
	* Upload Image Pt Perguruan Tinggi	* 
	* @return JSON
	*/
	public function upload_logo_ptn_file()
	{
		if (!$this->is_allowed('pt_perguruan_tinggi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pt_perguruan_tinggi',
		]);
	}

	/**
	* Delete Image Pt Perguruan Tinggi	* 
	* @return JSON
	*/
	public function delete_logo_ptn_file($uuid)
	{
		if (!$this->is_allowed('pt_perguruan_tinggi_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'logo_ptn', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'pt_perguruan_tinggi',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/pt_perguruan_tinggi/'
        ]);
	}

	/**
	* Get Image Pt Perguruan Tinggi	* 
	* @return JSON
	*/
	public function get_logo_ptn_file($id)
	{
		if (!$this->is_allowed('pt_perguruan_tinggi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pt_perguruan_tinggi = $this->model_pt_perguruan_tinggi->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'logo_ptn', 
            'table_name'        => 'pt_perguruan_tinggi',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/pt_perguruan_tinggi/',
            'delete_endpoint'   => 'administrator/pt_perguruan_tinggi/delete_logo_ptn_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pt_perguruan_tinggi_export');

		//$this->model_pt_perguruan_tinggi->export('pt_perguruan_tinggi', 'pt_perguruan_tinggi');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id', 'nama_pt', 'inisial', 'provinsi', 'kota', 'deskripsi'];
		if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "provinsi"){
                $where .= "(" . "p.name LIKE '%" . $inputan . "%' )";
            }
            else if($field == "kota"){
                $where .= "(" . "r.name LIKE '%" . $inputan . "%' )";
            }
            else{
                $where .= "(" . "pt.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select pt.id, pt.nama_pt, pt.inisial, p.name as provinsi, r.name as kota, pt.deskripsi  from pt_perguruan_tinggi pt join provinces p on pt.provinsi = p.id left join regencies r on pt.kota = r.id where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "pt.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "provinsi" || $field == "kota"){
	                	//continue;
	                }
	                else {
	                    $where .= "OR " . "pt.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "p.name LIKE '%" . $inputan . "%' ";
						$where .= "OR " . "r.name LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select pt.id, pt.nama_pt, pt.inisial, p.name as provinsi, r.name as kota, pt.deskripsi from pt_perguruan_tinggi pt join provinces p on pt.provinsi = p.id left join regencies r on pt.kota = r.id where ".$where,"result");
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
			if (strpos($field_search[$i], "id") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
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
		$this->is_allowed('pt_perguruan_tinggi_export');

		$this->model_pt_perguruan_tinggi->pdf('pt_perguruan_tinggi', 'pt_perguruan_tinggi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pt_perguruan_tinggi_export');

		$table = $title = 'pt_perguruan_tinggi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pt_perguruan_tinggi->find($id);
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

	public function import_ptn()
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
					$nama_ptn = strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					$inisial = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$provinsi = ltrim($worksheet->getCellByColumnAndRow(3, $row)->getValue(), " ");
					$provinsi_id = $this->mymodel->withquery("select id, name from provinces where name like '%".$provinsi."%'","row")->id;

					$data_ptn = array(
						"nama_pt" => ucwords($nama_ptn),
						"inisial" => strtoupper($worksheet->getCellByColumnAndRow(2, $row)->getValue()),
						"provinsi" => $provinsi_id,
						"updated_at" => date("Y-m-d H:i:s"),
					);

					//cek nama_ptn apakah sdh tersedia
					$cek_ptn = $this->mymodel->withquery("select * from pt_perguruan_tinggi where nama_pt = '".$nama_ptn."'","row");
					if (empty($cek_ptn)) {
						$save_data_ptn = $this->mymodel->insert("pt_perguruan_tinggi", $data_ptn);
					}
					else{
						$save_data_ptn = $this->mymodel->update("pt_perguruan_tinggi", $data_ptn, "id", $cek_ptn->id);
					}
						// dd($data_pendaftaran);
						if (!$save_data_ptn) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import program gagal ' . $data_ptn["nama_pt"]);
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


/* End of file pt_perguruan_tinggi.php */
/* Location: ./application/controllers/administrator/Pt Perguruan Tinggi.php */