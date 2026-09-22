<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| List Sekolah Tk Controller
*| --------------------------------------------------------------------------
*| List Sekolah Tk site
*|
*/
class List_sekolah_tk extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_list_sekolah_tk');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all List Sekolah Tks
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('list_sekolah_tk_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['list_sekolah_tks'] = $this->model_list_sekolah_tk->get($filter, $field, $this->limit_page, $offset);
		$this->data['list_sekolah_tk_counts'] = $this->model_list_sekolah_tk->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/list_sekolah_tk/index/',
			'total_rows'   => $this->model_list_sekolah_tk->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Daftar Sekolah Asal TK List');
		$this->render('backend/standart/administrator/list_sekolah_tk/list_sekolah_tk_list', $this->data);
	}
	
	/**
	* Add new list_sekolah_tks
	*
	*/
	public function add()
	{
		$this->is_allowed('list_sekolah_tk_add');

		$this->template->title('Daftar Sekolah Asal TK New');
		$this->render('backend/standart/administrator/list_sekolah_tk/list_sekolah_tk_add', $this->data);
	}

	/**
	* Add New List Sekolah Tks
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('list_sekolah_tk_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_sekolah' => $this->input->post('nama_sekolah'),
				'no_sekolah' => $this->input->post('no_sekolah'),
			];

			
			$save_list_sekolah_tk = $this->model_list_sekolah_tk->store($save_data);
            

			if ($save_list_sekolah_tk) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_list_sekolah_tk;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/list_sekolah_tk/edit/' . $save_list_sekolah_tk, 'Edit List Sekolah Tk'),
						anchor('administrator/list_sekolah_tk', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/list_sekolah_tk/edit/' . $save_list_sekolah_tk, 'Edit List Sekolah Tk')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/list_sekolah_tk');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/list_sekolah_tk');
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
	* Update view List Sekolah Tks
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('list_sekolah_tk_update');

		$this->data['list_sekolah_tk'] = $this->model_list_sekolah_tk->find($id);

		$this->template->title('Daftar Sekolah Asal TK Update');
		$this->render('backend/standart/administrator/list_sekolah_tk/list_sekolah_tk_update', $this->data);
	}

	/**
	* Update List Sekolah Tks
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('list_sekolah_tk_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_sekolah', 'Nama Sekolah', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'nama_sekolah' => $this->input->post('nama_sekolah'),
				'no_sekolah' => $this->input->post('no_sekolah'),
			];

			
			$save_list_sekolah_tk = $this->model_list_sekolah_tk->change($id, $save_data);

			if ($save_list_sekolah_tk) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/list_sekolah_tk', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/list_sekolah_tk');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/list_sekolah_tk');
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
	* delete List Sekolah Tks
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('list_sekolah_tk_delete');

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
            set_message(cclang('has_been_deleted', 'list_sekolah_tk'), 'success');
        } else {
            set_message(cclang('error_delete', 'list_sekolah_tk'), 'error');
        }

		redirect_back();
	}

		/**
	* View view List Sekolah Tks
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('list_sekolah_tk_view');

		$this->data['list_sekolah_tk'] = $this->model_list_sekolah_tk->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Daftar Sekolah Asal TK Detail');
		$this->render('backend/standart/administrator/list_sekolah_tk/list_sekolah_tk_view', $this->data);
	}
	
	/**
	* delete List Sekolah Tks
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$list_sekolah_tk = $this->model_list_sekolah_tk->find($id);

		
		
		return $this->model_list_sekolah_tk->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('list_sekolah_tk_export');

		//$this->model_list_sekolah_tk->export('list_sekolah_tk', 'list_sekolah_tk');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id_list_sekolah_tk', 'nama_sekolah', 'no_sekolah', 'lokasi'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select * from list_sekolah_tk order by id_list_sekolah_tk","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			$where .= "(" . "sa.".$field . " LIKE '%" . $inputan . "%' )";
			$get_data = $this->mymodel->withquery("select * from list_sekolah_tk where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else {
	                    $where .= "OR " .$field . " LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select * from list_sekolah_tk where ".$where,"result");
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
			if (strpos($field_search[$i], "id_list_sekolah_tk") !== false && $i == 0) {
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
		        if (strpos($kolom, "id_list_sekolah_tk") !== false && $i == 0) {
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
		header('Content-Disposition: attachment;filename="List sekolah TK - '.date("Y-m-d").'.xls"'); 
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
		$this->is_allowed('list_sekolah_tk_export');

		$this->model_list_sekolah_tk->pdf('list_sekolah_tk', 'list_sekolah_tk');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('list_sekolah_tk_export');

		$table = $title = 'list_sekolah_tk';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_list_sekolah_tk->find($id);
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

	public function import(){
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_siswa"]["name"]));
		$this->db->trans_begin();

		// dd($this->input->post('kelas_sd'));
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

				for ($row = 2; $row <= $highestRow; $row++) {
					
					if (empty($worksheet->getCellByColumnAndRow(1, $row)->getValue()) ) {
						break;
					}
					// inisial variabel
					$nama_sekolah = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
					$no_sekolah = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$lokasi = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					
					$data_sekolah = array(
						'nama_sekolah' => $nama_sekolah,
						'no_sekolah' => $no_sekolah,
						'lokasi' => $lokasi
					);
					
					//print_r($data_sekolah);
					//echo "<br/>";
					
					//check null
					/* foreach ($data_sekolah as $key => $item) {
						if(!$item && ($key != 'tunjangan_anak' || $key != 'tunjangan_istri')){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					} */
				
					//check apakah npp / pegawai tersebut di periode_selesai & periode_mulai yg sama sudah ada / belum
					if (empty($data_sekolah['lokasi'])) {
						$check_data = $this->mymodel->withquery("select * from list_sekolah_tk where no_sekolah = '".$data_sekolah['no_sekolah']."'","row");
					}
					else{
						$check_data = $this->mymodel->withquery("select * from list_sekolah_tk where no_sekolah = '".$data_sekolah['no_sekolah']."' and lokasi = '".$data_sekolah['lokasi']."'","row");
					}
					//echo $this->db->last_query();
					if(!empty($check_data)){
						$insertId = $this->mymodel->update("list_sekolah_tk", $data_sekolah, 'id_list_sekolah_tk', $check_data->id_list_sekolah_tk);
						if ($insertId) {
							$insertId = $check_data->id_list_sekolah_tk;
						}
						else{
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('failed', 'Import gagal nama sekolah ' . $data_sekolah['nama_sekolah']);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}else{
						$insertId = $this->mymodel->insertid("list_sekolah_tk", $data_sekolah);
					}
					//echo $this->db->last_query();
					/* if (empty($insertId)) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import gagal nama sekolah ' . $data_sekolah['nama_sekolah']);
						redirect($_SERVER['HTTP_REFERER']);
					}
					else{
						$this->db->trans_commit();
						$this->load->library("session");
						$this->session->set_flashdata('success', 'Import data berhasil');
						redirect($_SERVER['HTTP_REFERER']);
					} */
					
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

	/**
	* Delete all List Sekolah Tks (truncate) for re-import workflow
	*
	* Requires ?confirm=yes double confirmation
	*/
	public function delete_all()
	{
		$this->is_allowed('list_sekolah_tk_delete');

		if ($this->input->get('confirm') !== 'yes') {
			set_message(cclang('error_delete', 'list_sekolah_tk'), 'error');
			redirect_back();
		}

		$this->db->truncate('list_sekolah_tk');

		set_message(cclang('has_been_deleted', 'all list_sekolah_tk'), 'success');
		redirect_back();
	}
}


/* End of file list_sekolah_tk.php */
/* Location: ./application/controllers/administrator/List Sekolah Tk.php */