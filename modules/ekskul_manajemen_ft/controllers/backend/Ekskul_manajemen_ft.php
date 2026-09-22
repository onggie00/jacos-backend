<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ekskul Manajemen Ft Controller
*| --------------------------------------------------------------------------
*| Ekskul Manajemen Ft site
*|
*/
class Ekskul_manajemen_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ekskul_manajemen_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ekskul Manajemen Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ekskul_manajemen_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ekskul_manajemen_fts'] = $this->model_ekskul_manajemen_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['ekskul_manajemen_ft_counts'] = $this->model_ekskul_manajemen_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/ekskul_manajemen_ft/index/',
			'total_rows'   => $this->model_ekskul_manajemen_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Manajemen Ekskul FT List');
		$this->render('backend/standart/administrator/ekskul_manajemen_ft/ekskul_manajemen_ft_list', $this->data);
	}
	
	/**
	* Add new ekskul_manajemen_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('ekskul_manajemen_ft_add');

		$this->template->title('Manajemen Ekskul FT New');
		$this->render('backend/standart/administrator/ekskul_manajemen_ft/ekskul_manajemen_ft_add', $this->data);
	}

	/**
	* Add New Ekskul Manajemen Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ekskul_manajemen_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_guru_pembina', 'Pembina', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang_pembina', 'Jenjang Pembina', 'required');
		$this->form_validation->set_rules('nama', 'Nama', 'trim|max_length[200]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_pelatih', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('notelp_pelatih', 'Nomor Telepon', 'trim|max_length[14]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_guru_pembina' => $this->input->post('id_guru_pembina'),
				'jenjang_pembina' => $this->input->post('jenjang_pembina'),
				'nama' => $this->input->post('nama'),
				'keterangan' => $this->input->post('keterangan'),
				'email_pelatih' => $this->input->post('email_pelatih'),
				'notelp_pelatih' => $this->input->post('notelp_pelatih'),
			];

			
			$save_ekskul_manajemen_ft = $this->model_ekskul_manajemen_ft->store($save_data);
            

			if ($save_ekskul_manajemen_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ekskul_manajemen_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ekskul_manajemen_ft/edit/' . $save_ekskul_manajemen_ft, 'Edit Ekskul Manajemen Ft'),
						anchor('administrator/ekskul_manajemen_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ekskul_manajemen_ft/edit/' . $save_ekskul_manajemen_ft, 'Edit Ekskul Manajemen Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_manajemen_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_manajemen_ft');
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
	* Update view Ekskul Manajemen Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ekskul_manajemen_ft_update');

		$this->data['ekskul_manajemen_ft'] = $this->model_ekskul_manajemen_ft->find($id);

		$this->template->title('Manajemen Ekskul FT Update');
		$this->render('backend/standart/administrator/ekskul_manajemen_ft/ekskul_manajemen_ft_update', $this->data);
	}

	/**
	* Update Ekskul Manajemen Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ekskul_manajemen_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ekskul', 'Ekstrakurikuler', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_guru_pembina', 'Pembina', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('jenjang_pembina', 'Jenjang Pembina', 'required');
		$this->form_validation->set_rules('nama', 'Nama', 'trim|max_length[200]');
		$this->form_validation->set_rules('keterangan', 'Keterangan', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_pelatih', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('notelp_pelatih', 'Nomor Telepon', 'trim|max_length[14]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ekskul' => $this->input->post('id_ekskul'),
				'id_guru_pembina' => $this->input->post('id_guru_pembina'),
				'jenjang_pembina' => $this->input->post('jenjang_pembina'),
				'nama' => $this->input->post('nama'),
				'keterangan' => $this->input->post('keterangan'),
				'email_pelatih' => $this->input->post('email_pelatih'),
				'notelp_pelatih' => $this->input->post('notelp_pelatih'),
			];

			
			$save_ekskul_manajemen_ft = $this->model_ekskul_manajemen_ft->change($id, $save_data);

			if ($save_ekskul_manajemen_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ekskul_manajemen_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ekskul_manajemen_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ekskul_manajemen_ft');
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
	* delete Ekskul Manajemen Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ekskul_manajemen_ft_delete');

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
            set_message(cclang('has_been_deleted', 'ekskul_manajemen_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'ekskul_manajemen_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ekskul Manajemen Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ekskul_manajemen_ft_view');

		$this->data['ekskul_manajemen_ft'] = $this->model_ekskul_manajemen_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Manajemen Ekskul FT Detail');
		$this->render('backend/standart/administrator/ekskul_manajemen_ft/ekskul_manajemen_ft_view', $this->data);
	}
	
	/**
	* delete Ekskul Manajemen Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ekskul_manajemen_ft = $this->model_ekskul_manajemen_ft->find($id);

		
		
		return $this->model_ekskul_manajemen_ft->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ekskul_manajemen_ft_export');

		//$this->model_ekskul_manajemen_ft->export('ekskul_manajemen_ft', 'ekskul_manajemen_ft');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search   = ['id_manajemen', 'id_ekskul', 'id_guru_pembina', 'nama', 'keterangan', 'email_pelatih', 'notelp_pelatih','jenjang_pembina'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select m.id_manajemen, m.id_ekskul, e.nama as nama_ekskul, e.hari, e.jam, m.id_guru_pembina, g.nama_lengkap, m.nama as nama_pelatih, m.keterangan, m.email_pelatih, m.notelp_pelatih, m.jenjang_pembina from ekskul_manajemen_ft m join guru_ft g on m.id_guru_pembina = g.id_guru join ekskul e on m.id_ekskul = e.id_ekskul","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "id_ekskul"){
                $where .= "(" . "e.nama LIKE '%" . $inputan . "%' )";
            }
            else if($field == "id_guru_pembina"){
                $where .= "(" . "g.nama_lengkap LIKE '%" . $inputan . "%' )";
            }
            else{
                $where .= "(" . "m.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select m.id_manajemen, m.id_ekskul, e.nama as nama_ekskul, e.hari, e.jam, m.id_guru_pembina, g.nama_lengkap, m.nama as nama_pelatih, m.keterangan, m.email_pelatih, m.notelp_pelatih, m.jenjang_pembina from ekskul_manajemen_ft m join guru_ft g on m.id_guru_pembina = g.id_guru join ekskul e on m.id_ekskul = e.id_ekskul where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "m.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else {
	                    $where .= "OR " . "m.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "e.nama LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "g.nama_lengkap LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	        }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select m.id_manajemen, m.id_ekskul, e.nama as nama_ekskul, e.hari, e.jam, m.id_guru_pembina, g.nama_lengkap, m.nama as nama_pelatih, m.keterangan, m.email_pelatih, m.notelp_pelatih, m.jenjang_pembina from ekskul_manajemen_ft m join guru_ft g on m.id_guru_pembina = g.id_guru join ekskul e on m.id_ekskul = e.id_ekskul where ".$where,"result");
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
			if (strpos($field_search[$i], "id_manajemen")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if (strpos($field_search[$i], "id_ekskul")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('EKSTRAKURIKULER'));
			}
			else if (strpos($field_search[$i], "id_guru_pembina")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('PEMBINA'));
			}
			else if (strpos($field_search[$i], "nama")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA PELATIH'));
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
		        if (strpos($kolom, "id_manajemen") !== false) {
		        	$value_data = ($key+1);
		        }
		        if (strpos($kolom, "id_ekskul") !== false && !empty($value_data)) {
		        	$value_data = $value->nama_ekskul;
		        }
		        if (strpos($kolom, "id_guru_pembina") !== false && !empty($value_data)) {
		        	$value_data = $value->nama_lengkap;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Manajemen Ekstrakurikuler FT_'.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('ekskul_manajemen_ft_export');

		$this->model_ekskul_manajemen_ft->pdf('ekskul_manajemen_ft', 'ekskul_manajemen_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ekskul_manajemen_ft_export');

		$table = $title = 'ekskul_manajemen_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ekskul_manajemen_ft->find($id);
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

	
}


/* End of file ekskul_manajemen_ft.php */
/* Location: ./application/controllers/administrator/Ekskul Manajemen Ft.php */