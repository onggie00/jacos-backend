<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Ujian Ruang Kelas Controller
*| --------------------------------------------------------------------------
*| Ujian Ruang Kelas site
*|
*/
class Ujian_ruang_kelas extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_ujian_ruang_kelas');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Ujian Ruang Kelass
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('ujian_ruang_kelas_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['ujian_ruang_kelass'] = $this->model_ujian_ruang_kelas->get($filter, $field, $this->limit_page, $offset);
		$this->data['ujian_ruang_kelas_counts'] = $this->model_ujian_ruang_kelas->count_all($filter, $field);
		foreach ($this->data['ujian_ruang_kelass'] as $key => $value) {
			$tingkatan = $this->mymodel->withquery("select * from tingkatan_".strtolower($value->jenjang_kelas)." where id_tingkatan_".strtolower($value->jenjang_kelas)." = '".$value->id_tingkatan."'  ","row");
			if (!empty($tingkatan)) {
				$value->tingkatan = $tingkatan->label;
			}
			else{
				$value->tingkatan = "";
			}
		}

		$config = [
			'base_url'     => 'administrator/ujian_ruang_kelas/index/',
			'total_rows'   => $this->model_ujian_ruang_kelas->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Daftar Ruang Kelas List');
		$this->render('backend/standart/administrator/ujian_ruang_kelas/ujian_ruang_kelas_list', $this->data);
	}
	
	/**
	* Add new ujian_ruang_kelass
	*
	*/
	public function add()
	{
		$this->is_allowed('ujian_ruang_kelas_add');
		$tingkatan_sd = $this->mymodel->withquery("select * from tingkatan_sd","result");
		$tingkatan_smp = $this->mymodel->withquery("select * from tingkatan_smp","result");
		$tingkatan_sma = $this->mymodel->withquery("select * from tingkatan_sma","result");
		$tingkatan_ft = $this->mymodel->withquery("select * from tingkatan_ft","result");
		$list_tingkatan = array();
		foreach ($tingkatan_sd as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_sd,
				"label" => $value->label,
				"jenjang" => strtoupper("sd")
			);
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_smp as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_smp,
				"label" => $value->label,
				"jenjang" => strtoupper("smp")
			);
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_sma as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_sma,
				"label" => $value->label,
				"jenjang" => strtoupper("sma")
			);
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_ft as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_ft,
				"label" => $value->label,
				"jenjang" => strtoupper("ft")
			);
			array_push($list_tingkatan, $data_save);
		}

		$this->data['list_tingkatan'] = $list_tingkatan;

		$this->template->title('Daftar Ruang Kelas New');
		$this->render('backend/standart/administrator/ujian_ruang_kelas/ujian_ruang_kelas_add', $this->data);
	}

	/**
	* Add New Ujian Ruang Kelass
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('ujian_ruang_kelas_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_ruang', 'Nama Ruangan', 'trim|required');
		$this->form_validation->set_rules('kapasitas', 'Kapasitas', 'trim|required');
		$this->form_validation->set_rules('kelas', 'Kelas', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenjang_kelas', 'Jenjang Kelas', 'trim|required');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|required');
		$this->form_validation->set_rules('nomor_peserta_awal', 'Nomor Peserta Awal', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('nomor_peserta_akhir', 'Nomor Peserta Akhir', 'trim|required|max_length[10]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ruang' => $this->input->post('id_ruang'),
				'kapasitas' => $this->input->post('kapasitas'),
				'kelas' => $this->input->post('kelas'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'jenjang_kelas' => $this->input->post('jenjang_kelas'),
				'nomor_peserta_awal' => $this->input->post('nomor_peserta_awal'),
				'nomor_peserta_akhir' => $this->input->post('nomor_peserta_akhir'),
			];

			
			$save_ujian_ruang_kelas = $this->model_ujian_ruang_kelas->store($save_data);
            

			if ($save_ujian_ruang_kelas) {
				$post = $this->input->post();
				//generate nomor peserta sesuai kapasitas dari awal dan akhir
				$nomor_peserta = $post["nomor_peserta_awal"];
				$kode_peserta = substr($nomor_peserta, 0, 4);
				$nomor_awal = (int)substr($nomor_peserta, 4);
				for ($i=0; $i < $post["kapasitas"]; $i++) {
					$nomor_urut = sprintf("%03d", $nomor_awal+$i);
					$data_insert = array(
						"id_ruang" => $post["id_ruang"],
						"id_ruang_kelas" => $save_ujian_ruang_kelas,
						"nomor_peserta_ujian" => $kode_peserta.$nomor_urut,
						"jenjang" => strtoupper($this->input->post('jenjang_kelas')),
						"boleh_ujian" => "TIDAK",
						"nama_siswa" => "",
						"created_at" => date("Y-m-d H:i:s"),
						"updated_at" => date("Y-m-d H:i:s")
					);
					$this->mymodel->insert("ujian_ruang_detail", $data_insert);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_ujian_ruang_kelas;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/ujian_ruang_kelas/edit/' . $save_ujian_ruang_kelas, 'Edit Ujian Ruang Kelas'),
						anchor('administrator/ujian_ruang_kelas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/ujian_ruang_kelas/edit/' . $save_ujian_ruang_kelas, 'Edit Ujian Ruang Kelas')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_kelas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_kelas');
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
	* Update view Ujian Ruang Kelass
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('ujian_ruang_kelas_update');

		$this->data['ujian_ruang_kelas'] = $this->model_ujian_ruang_kelas->find($id);
		$tingkatan_sd = $this->mymodel->withquery("select * from tingkatan_sd","result");
		$tingkatan_smp = $this->mymodel->withquery("select * from tingkatan_smp","result");
		$tingkatan_sma = $this->mymodel->withquery("select * from tingkatan_sma","result");
		$tingkatan_ft = $this->mymodel->withquery("select * from tingkatan_ft","result");
		$list_tingkatan = array();
		foreach ($tingkatan_sd as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_sd,
				"label" => $value->label,
				"jenjang" => strtoupper("sd")
			);
			if ($this->data['ujian_ruang_kelas']->id_tingkatan == $value->id_tingkatan_sd && $this->data['ujian_ruang_kelas']->jenjang_kelas == strtoupper("sd")) {
				$data_save['selected'] = 1;
			}
			else{
				$data_save['selected'] = 0;
			}
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_smp as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_smp,
				"label" => $value->label,
				"jenjang" => strtoupper("smp")
			);
			if ($this->data['ujian_ruang_kelas']->id_tingkatan == $value->id_tingkatan_smp && $this->data['ujian_ruang_kelas']->jenjang_kelas == strtoupper("smp")) {
				$data_save['selected'] = 1;
			}
			else{
				$data_save['selected'] = 0;
			}
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_sma as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_sma,
				"label" => $value->label,
				"jenjang" => strtoupper("sma")
			);
			if ($this->data['ujian_ruang_kelas']->id_tingkatan == $value->id_tingkatan_sma && $this->data['ujian_ruang_kelas']->jenjang_kelas == strtoupper("sma")) {
				$data_save['selected'] = 1;
			}
			else{
				$data_save['selected'] = 0;
			}
			array_push($list_tingkatan, $data_save);
		}
		foreach ($tingkatan_ft as $key => $value) {
			$data_save = array(
				"id_tingkatan" => $value->id_tingkatan_ft,
				"label" => $value->label,
				"jenjang" => strtoupper("ft")
			);
			if ($this->data['ujian_ruang_kelas']->id_tingkatan == $value->id_tingkatan_ft && $this->data['ujian_ruang_kelas']->jenjang_kelas == strtoupper("ft")) {
				$data_save['selected'] = 1;
			}
			else{
				$data_save['selected'] = 0;
			}
			array_push($list_tingkatan, $data_save);
		}

		$this->data['list_tingkatan'] = $list_tingkatan;

		$this->template->title('Daftar Ruang Kelas Update');
		$this->render('backend/standart/administrator/ujian_ruang_kelas/ujian_ruang_kelas_update', $this->data);
	}

	/**
	* Update Ujian Ruang Kelass
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('ujian_ruang_kelas_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_ruang', 'Nama Ruangan', 'trim|required');
		$this->form_validation->set_rules('kapasitas', 'Kapasitas', 'trim|required');
		$this->form_validation->set_rules('kelas', 'Kelas', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('jenjang_kelas', 'Jenjang Kelas', 'trim|required');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|required');
		$this->form_validation->set_rules('nomor_peserta_awal', 'Nomor Peserta Awal', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('nomor_peserta_akhir', 'Nomor Peserta Akhir', 'trim|required|max_length[10]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_ruang' => $this->input->post('id_ruang'),
				'kapasitas' => $this->input->post('kapasitas'),
				'kelas' => $this->input->post('kelas'),
				'jenjang_kelas' => $this->input->post('jenjang_kelas'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'nomor_peserta_awal' => $this->input->post('nomor_peserta_awal'),
				'nomor_peserta_akhir' => $this->input->post('nomor_peserta_akhir'),
			];

			
			$save_ujian_ruang_kelas = $this->model_ujian_ruang_kelas->change($id, $save_data);

			if ($save_ujian_ruang_kelas) {
				$post = $this->input->post();
				//delete existing data
				$this->mymodel->delete("ujian_ruang_detail", "id_ruang_kelas", $id);
				//regenerate nomor peserta sesuai kapasitas dari awal dan akhir
				$nomor_peserta = $post["nomor_peserta_awal"];
				$kode_peserta = substr($nomor_peserta, 0, 4);
				$nomor_awal = (int)substr($nomor_peserta, 4);
				for ($i=0; $i < $post["kapasitas"]; $i++) {
					$nomor_urut = sprintf("%03d", $nomor_awal+$i);
					$data_insert = array(
						"id_ruang" => $post["id_ruang"],
						"id_ruang_kelas" => $id,
						"nomor_peserta_ujian" => $kode_peserta.$nomor_urut,
						"jenjang" => strtoupper($this->input->post('jenjang_kelas')),
						"boleh_ujian" => "TIDAK",
						"nama_siswa" => "",
						"created_at" => date("Y-m-d H:i:s"),
						"updated_at" => date("Y-m-d H:i:s")
					);
					$this->mymodel->insert("ujian_ruang_detail", $data_insert);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/ujian_ruang_kelas', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/ujian_ruang_kelas');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/ujian_ruang_kelas');
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
	* delete Ujian Ruang Kelass
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('ujian_ruang_kelas_delete');

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

			$this->mymodel->delete("ujian_ruang_detail", "id_ruang_kelas", $id);
			//echo $this->db->last_query();
		if ($remove) {
			//remove peserta ujian per kelas
			//$this->mymodel->update("ujian_ruang_kelas", array("id_ruang" => 0), "id_ruang", $id);
			//$this->mymodel->update("ujian_ruang_detail", array("id_ruang" => 0), "id_ruang", $id);
            set_message(cclang('has_been_deleted', 'ujian_ruang_kelas'), 'success');
        } else {
            set_message(cclang('error_delete', 'ujian_ruang_kelas'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Ujian Ruang Kelass
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('ujian_ruang_kelas_view');

		$this->data['ujian_ruang_kelas'] = $this->model_ujian_ruang_kelas->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Daftar Ruang Kelas Detail');
		$this->render('backend/standart/administrator/ujian_ruang_kelas/ujian_ruang_kelas_view', $this->data);
	}
	
	/**
	* delete Ujian Ruang Kelass
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ujian_ruang_kelas = $this->model_ujian_ruang_kelas->find($id);

		
		
		return $this->model_ujian_ruang_kelas->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('ujian_ruang_kelas_export');

		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id_ruang_kelas', 'id_ruang', 'id_tingkatan', 'kapasitas', 'kelas', 'jenjang_kelas', 'nomor_peserta_awal', 'nomor_peserta_akhir'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select u.*, r.nama_ruang from ujian_ruang_kelas u join ujian_ruang r on u.id_ruang = r.id_ruang ","result");
			foreach ($get_data as $key => $value) {
				$value->tingkatan = $this->mymodel->withquery("select * from tingkatan_".strtolower($value->jenjang_kelas)." where id_tingkatan_".strtolower($value->jenjang_kelas)." = '".$value->id_tingkatan."' ","row")->label;
			}
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "jenjang_kelas"){
                $where .= "(" . "u.jenjang_kelas = '" . $inputan . "' )";
                $jenjang = $inputan;
                $get_data = $this->mymodel->withquery("select u.*, r.nama_ruang, t.label as tingkatan from ujian_ruang_kelas u join ujian_ruang r on u.id_ruang = r.id_ruang join tingkatan_".strtolower($jenjang)." t on u.id_tingkatan = t.id_tingkatan_".strtolower($jenjang)." where ".$where,"result");
            }
            else{
                $where .= "(" . "u.".$field . " LIKE '%" . $inputan . "%' )";
                $get_data = $this->mymodel->withquery("select u.*, r.nama_ruang from ujian_ruang_kelas u join ujian_ruang r on u.id_ruang = r.id_ruang where ".$where,"result");
                foreach ($get_data as $key => $value) {
					$value->tingkatan = $this->mymodel->withquery("select * from tingkatan_".strtolower($value->jenjang_kelas)." where id_tingkatan_".strtolower($value->jenjang_kelas)." = '".$value->id_tingkatan."' ","row")->label;
				}
            }
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "u.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "nama_mapel" || $field == "id_tingkatan"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "u.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select u.*, r.nama_ruang from ujian_ruang_kelas u join ujian_ruang r on u.id_ruang = r.id_ruang where ".$where,"result");
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
			if (strpos($field_search[$i], "id_ruang_kelas") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			elseif (strpos($field_search[$i], "id_ruang") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA RUANGAN'));
			}
			elseif (strpos($field_search[$i], "id_tingkatan") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TINGKATAN'));
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
		        if (strpos($kolom, "id_ruang_kelas") !== false) {
		        	$value_data = ($key+1);
		        }
				if (strpos($kolom, "id_ruang") !== false) {
		        	$value_data = strip_tags($value->nama_ruang); 
		        }
		        if (strpos($kolom, "id_tingkatan") !== false) {
		        	$value_data = strip_tags($value->tingkatan);
		        }

		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Ruang Kelas - '.date("Y-m-d Hi").'.xls"'); 
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
		$this->is_allowed('ujian_ruang_kelas_export');

		$this->model_ujian_ruang_kelas->pdf('ujian_ruang_kelas', 'ujian_ruang_kelas');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('ujian_ruang_kelas_export');

		$table = $title = 'ujian_ruang_kelas';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_ujian_ruang_kelas->find($id);
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

	public function cetak_kartu_ujian(){
		$id_ruang_kelas = $this->input->get("id_ruang_kelas");
		$jenjang = strtolower($this->input->get("jenjang"));

		$get_siswa = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, s.nis from ujian_ruang_detail d left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas left join siswa_".$jenjang."_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian where d.id_ruang_kelas = '".$id_ruang_kelas."' and d.nama_siswa != '' order by s.id_siswa_".$jenjang."_aktif DESC, d.urutan_kursi ASC","result");
		$daftar_kelas = "";
		if($jenjang == "sd")
		{
			foreach($get_siswa as $key => $value){
				if($daftar_kelas == "")
				{
					$daftar_kelas = $value->kelas;
				}
				else
				{
					if(strpos($daftar_kelas, $value->kelas) === false)
					{
						$daftar_kelas .= ", ".$value->kelas;
					}
				}
			}
			$get_ujian = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, t.label as tingkatan, m.nama_mapel, j.daftar_kelas from jadwal_ujian_".$jenjang." j join ujian_mapel_".$jenjang." m on j.id_mapel = m.id_mapel join tingkatan_".$jenjang." t on j.id_tingkatan = t.id_tingkatan_".$jenjang." where j.id_tingkatan = '".$get_siswa[0]->id_tingkatan."' and j.daftar_kelas like '%".$get_siswa[0]->kelas."%' order by tanggal ASC, jam_mulai ASC","result");
		}
		else{
			$get_ujian = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, t.label as tingkatan, m.nama_mapel, j.daftar_kelas from jadwal_ujian_".$jenjang." j join ujian_mapel_".$jenjang." m on j.id_mapel = m.id_mapel join tingkatan_".$jenjang." t on j.id_tingkatan = t.id_tingkatan_".$jenjang." where j.id_tingkatan = '".$get_siswa[0]->id_tingkatan."' order by tanggal ASC, jam_mulai ASC","result");
		}
		$arr_ujian = array();
		foreach ($get_ujian as $key => $value) {
        	$value->tgl_format = formatTanggal($value->tanggal);
        	// Create a key using tanggal, jam_mulai, and jam_selesai
		    $keyword = $value->tanggal . '|' . $value->jam_mulai . '|' . $value->jam_selesai;
		    
		    // If the key already exists, concatenate nama_mapel
		    if (isset($arr_ujian[$keyword])) {
		        $arr_ujian[$keyword]->nama_mapel .= '/ ' . $value->nama_mapel;
		    } else {
		        // Otherwise, create a new entry
		        $arr_ujian[$keyword] = clone $value;
		    }
        }
        $arr_ujian = array_values($arr_ujian);
        foreach ($get_siswa as $key => $value) {
        	$value->judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_".$jenjang." j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_ruang_kelas = '".$id_ruang_kelas."'","row")->nama_ujian;
        }

		//$get_nama_ujian = $this->mymodel->withquery("select nama_ujian from jenis_ujian where id_jenis_ujian = '".$get_ujian[0]->id_jenis_ujian."'","row");
        $tahun_ajaran = $this->mymodel->withquery("select label from tahun_ajaran where id_tahun_ajaran = '".$get_ujian[0]->id_tahun_ajaran."'","row");
        $get_ruang = $this->mymodel->withquery("select nama_ruang from ujian_ruang where id_ruang ='".$get_siswa[0]->id_ruang."'", "row");

		$datas['list_siswa'] = $get_siswa;
		$datas['jenjang'] = $jenjang;
		$datas['ujian'] = $arr_ujian;
		$datas['tahun_ajar'] = $tahun_ajaran->label;
        //$datas['judul_ujian'] = $get_nama_ujian->nama_ujian;
        $datas['ruang'] = $get_ruang;
        $datas['today_date'] = formatTanggal(date("Y-m-d"));
        $datas['kepsek'] = $this->mymodel->withquery("select nama_kepsek,file_ttd from data_kepala_sekolah where jenjang = '".$jenjang."'","row");
        $this->load->library('HtmlPdf');
		$pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
            
       	$this->load->view('template_kartu_ujian_admin', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = 'KARTU_UJIAN '.$get_ruang->nama_ruang."-".$jenjang.'.pdf';
        //$pdf->Output(FCPATH.'/uploads/kartu_ujian/'.$nama_file, 'I');
        $pdf->Output($nama_file, 'I');
	}

	public function print_absensi($id_ruang_kelas){
		$this->load->library('HtmlPdf');

        $get_kelas = $this->mymodel->withquery("select id_ruang, id_tingkatan, id_ruang_kelas, kelas, kapasitas from ujian_ruang_kelas where id_ruang_kelas = '".$id_ruang_kelas."'","row");
        $get_ruang = $this->mymodel->getbywhere("ujian_ruang", "id_ruang", $get_kelas->id_ruang,"row");
        $get_peserta = $this->mymodel->withquery("select * from ujian_ruang_detail where id_ruang_kelas = '".$id_ruang_kelas."' order by urutan_kursi ASC","result");
        $jenjang = $get_peserta[0]->jenjang;
        if ($jenjang == "ft") {
        	$jenjang = "sma";
        }
        $get_ujian = $this->mymodel->withquery("select j.*, m.nama_mapel, ju.nama_ujian, t.label as tingkatan from jadwal_ujian_".strtolower($jenjang)." j join ujian_mapel_".strtolower($jenjang)." m on j.id_mapel = m.id_mapel left join jenis_ujian ju on j.id_jenis_ujian = ju.id_jenis_ujian join tingkatan_".strtolower($jenjang)." t on j.id_tingkatan = t.id_tingkatan_".strtolower($jenjang)." where j.id_tingkatan = '".$get_kelas->id_tingkatan."' ","result");
        //$get_nama_ujian = $this->mymodel->withquery("select nama_ujian from jenis_ujian where id_jenis_ujian = '".$get_ujian[0]->id_jenis_ujian."'","row");
        $tahun_ajaran = $this->mymodel->withquery("select label from tahun_ajaran where id_tahun_ajaran = '".$get_ujian[0]->id_tahun_ajaran."'","row");

        $datas['tahun_ajar'] = $tahun_ajaran->label;
        //$datas['judul_ujian'] = $get_ujian[0]->nama_ujian;
        $datas['ujian'] = $get_ujian;
        $datas['ruang'] = $get_ruang;
        $datas['kelas'] = $get_kelas;
        $datas['peserta'] = $get_peserta;

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_absensi_ujian', $datas);
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $pdf->Output('ABSENSI '.strtoupper($get_ruang->nama_ruang).'.pdf', 'I');
        //$pdf->Output(FCPATH.'/uploads/kartu_siswa_sementara/'.$tipe_siswa.'-'.$data->no_peserta.'-'.$data->nama_lengkap.'.pdf', 'F');
	}

	public function import_kelas()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();
		$jenjang = $this->input->post('jenjang_kelas');
		$id_ruang = $this->input->post('id_ruang');

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' || $ext!='xls'){
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);
			//delete semua ruang per jenjang / per ruang
			if (!empty($jenjang)) {
				$this->mymodel->delete("ujian_ruang_kelas", "jenjang_kelas =", $jenjang);
				$this->mymodel->delete("ujian_ruang_detail", "jenjang =", $jenjang);
			}
			if (!empty($id_ruang)) {
				$this->mymodel->delete("ujian_ruang_kelas", "id_ruang =", $id_ruang);
				$this->mymodel->delete("ujian_ruang_detail", "id_ruang =", $id_ruang);
			}
			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					$jenjang = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$nama_ruang = strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					$get_ruang = $this->mymodel->withquery("select id_ruang from ujian_ruang where nama_ruang = '".$nama_ruang."'","row");
					$tingkatan = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$get_tingkatan = $this->mymodel->withquery("select id_tingkatan_".strtolower($jenjang)." from tingkatan_".strtolower($jenjang)." where label = '".$tingkatan."'","row");
					$id_tingkatan = "id_tingkatan_".strtolower($jenjang);
					$data_kelas = array(
						"id_ruang" => $get_ruang->id_ruang,
						"id_tingkatan" => $get_tingkatan->$id_tingkatan,
						"kapasitas" => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"kelas" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
						"jenjang_kelas" => strtoupper($jenjang),
						"nomor_peserta_awal" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
						"nomor_peserta_akhir" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
					);
					//check null
					foreach ($data_kelas as $key => $item) {
						if(empty($item)){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong");
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					//isi ulang dengan kelas terbaru
					$save_data = $this->mymodel->insertid("ujian_ruang_kelas", $data_kelas);
					//generate nomor peserta sesuai kapasitas dari awal dan akhir
					$nomor_peserta = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
					$kode_peserta = substr($nomor_peserta, 0, 4);
					$nomor_awal = (int)substr($nomor_peserta, 4);
					for ($i=0; $i < (int)$worksheet->getCellByColumnAndRow(3, $row)->getValue(); $i++) {
						$nomor_urut = sprintf("%03d", $nomor_awal+$i);
						$data_insert = array(
							"id_ruang" => $get_ruang->id_ruang,
							"id_ruang_kelas" => $save_data,
							"nomor_peserta_ujian" => $kode_peserta.$nomor_urut,
							"jenjang" => strtoupper($jenjang),
							"boleh_ujian" => "TIDAK",
							"nama_siswa" => "",
							"created_at" => date("Y-m-d H:i:s"),
							"updated_at" => date("Y-m-d H:i:s")
						);
						$this->mymodel->insert("ujian_ruang_detail", $data_insert);
					}
					// dd($data_pendaftaran);
					if (empty($save_data)) {
						$this->db->trans_rollback();
						//$this->load->library("session");
						$this->session->set_flashdata('failed', 'Import data gagal ' . $data_mapel["nama_mapel"]);
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data ruang kelas ujian berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	
}


/* End of file ujian_ruang_kelas.php */
/* Location: ./application/controllers/administrator/Ujian Ruang Kelas.php */