<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Jadwal Ujian Sd Controller
*| --------------------------------------------------------------------------
*| Jadwal Ujian Sd site
*|
*/
class Jadwal_ujian_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_jadwal_ujian_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Jadwal Ujian Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('jadwal_ujian_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['jadwal_ujian_sds'] = $this->model_jadwal_ujian_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['jadwal_ujian_sd_counts'] = $this->model_jadwal_ujian_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/jadwal_ujian_sd/index/',
			'total_rows'   => $this->model_jadwal_ujian_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Jadwal Ujian Aktif SD List');
		$this->render('backend/standart/administrator/jadwal_ujian_sd/jadwal_ujian_sd_list', $this->data);
	}
	
	/**
	* Add new jadwal_ujian_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('jadwal_ujian_sd_add');

		$this->template->title('Jadwal Ujian Aktif SD New');
		$this->render('backend/standart/administrator/jadwal_ujian_sd/jadwal_ujian_sd_add', $this->data);
	}

	/**
	* Add New Jadwal Ujian Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('jadwal_ujian_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_mapel', 'Mata Pelajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jenis_ujian', 'Jenis Ujian', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('daftar_kelas[]', 'Daftar Kelas', 'trim|required');
		$this->form_validation->set_rules('tanggal', 'Tanggal Ujian', 'trim|required');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mapel' => $this->input->post('id_mapel'),
				'id_jenis_ujian' => $this->input->post('id_jenis_ujian'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'daftar_kelas' => implode(',', (array) $this->input->post('daftar_kelas')),
				'tanggal' => $this->input->post('tanggal'),
				'hari' => $this->input->post('hari'),
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			
			$save_jadwal_ujian_sd = $this->model_jadwal_ujian_sd->store($save_data);
            

			if ($save_jadwal_ujian_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_jadwal_ujian_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/jadwal_ujian_sd/edit/' . $save_jadwal_ujian_sd, 'Edit Jadwal Ujian Sd'),
						anchor('administrator/jadwal_ujian_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/jadwal_ujian_sd/edit/' . $save_jadwal_ujian_sd, 'Edit Jadwal Ujian Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_sd');
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
	* Update view Jadwal Ujian Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('jadwal_ujian_sd_update');

		$this->data['jadwal_ujian_sd'] = $this->model_jadwal_ujian_sd->find($id);

		$this->template->title('Jadwal Ujian Aktif SD Update');
		$this->render('backend/standart/administrator/jadwal_ujian_sd/jadwal_ujian_sd_update', $this->data);
	}

	/**
	* Update Jadwal Ujian Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('jadwal_ujian_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_mapel', 'Mata Pelajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_jenis_ujian', 'Jenis Ujian', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tingkatan', 'Tingkatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('daftar_kelas[]', 'Daftar Kelas', 'trim|required');
		$this->form_validation->set_rules('tanggal', 'Tanggal Ujian', 'trim|required');
		$this->form_validation->set_rules('hari', 'Hari', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('jam_mulai', 'Jam Mulai', 'trim|required');
		$this->form_validation->set_rules('jam_selesai', 'Jam Selesai', 'trim|required');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('semester', 'Semester', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_mapel' => $this->input->post('id_mapel'),
				'id_jenis_ujian' => $this->input->post('id_jenis_ujian'),
				'id_tingkatan' => $this->input->post('id_tingkatan'),
				'daftar_kelas' => implode(',', (array) $this->input->post('daftar_kelas')),
				'tanggal' => $this->input->post('tanggal'),
				'hari' => $this->input->post('hari'),
				'jam_mulai' => $this->input->post('jam_mulai'),
				'jam_selesai' => $this->input->post('jam_selesai'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'semester' => $this->input->post('semester'),
			];

			
			$save_jadwal_ujian_sd = $this->model_jadwal_ujian_sd->change($id, $save_data);

			if ($save_jadwal_ujian_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/jadwal_ujian_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/jadwal_ujian_sd');
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
	* delete Jadwal Ujian Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('jadwal_ujian_sd_delete');

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
            set_message(cclang('has_been_deleted', 'jadwal_ujian_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'jadwal_ujian_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Jadwal Ujian Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('jadwal_ujian_sd_view');

		$this->data['jadwal_ujian_sd'] = $this->model_jadwal_ujian_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Jadwal Ujian Aktif SD Detail');
		$this->render('backend/standart/administrator/jadwal_ujian_sd/jadwal_ujian_sd_view', $this->data);
	}
	
	/**
	* delete Jadwal Ujian Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$jadwal_ujian_sd = $this->model_jadwal_ujian_sd->find($id);

		
		
		return $this->model_jadwal_ujian_sd->remove($id);
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('jadwal_ujian_sd_export');

		$this->model_jadwal_ujian_sd->pdf('jadwal_ujian_sd', 'jadwal_ujian_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('jadwal_ujian_sd_export');

		$table = $title = 'jadwal_ujian_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_jadwal_ujian_sd->find($id);
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

	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('jadwal_ujian_sd_export');

		//$this->model_siswa_sd_aktif->export_siswa('siswa_sd_aktif', 'siswa_sd_aktif');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['id_jadwal', 'id_mapel', 'id_jenis_ujian', 'id_tingkatan', 'daftar_kelas', 'tanggal', 'hari', 'jam_mulai', 'jam_selesai', 'id_tahun_ajaran', 'semester'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select j.*, m.nama_mapel, ju.nama_ujian, t.label as tingkatan, ta.label as tahun_ajaran from jadwal_ujian_sd j join ujian_mapel_sd m on j.id_mapel = m.id_mapel join jenis_ujian ju on j.id_jenis_ujian = ju.id_jenis_ujian join tingkatan_sd t on j.id_tingkatan = t.id_tingkatan_sd join tahun_ajaran ta on j.id_tahun_ajaran = ta.id_tahun_ajaran ","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "nama_mapel"){
                $where .= "(" . "m.nama_mapel LIKE '%" . $inputan . "%' )";
            }
            else if($field == "jenis_ujian"){
                $where .= "(" . "ju.nama_ujian LIKE '%" . $inputan . "%' )";
            }
            else if($field == "tingkatan"){
                $where .= "(" . "t.label LIKE '%" . $inputan . "%' )";
            }
			else if($field == "tahun_ajaran"){
				$where .= "(" . "ta.label LIKE '%" . $inputan . "%' )";
			}
            else{
                $where .= "(" . "j.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select j.*, m.nama_mapel, ju.nama_ujian, t.label as tingkatan, ta.label as tahun_ajaran from jadwal_ujian_sd j join ujian_mapel_sd m on j.id_mapel = m.id_mapel join jenis_ujian ju on j.id_jenis_ujian = ju.id_jenis_ujian join tingkatan_sd t on j.id_tingkatan = t.id_tingkatan_sd join tahun_ajaran ta on j.id_tahun_ajaran = ta.id_tahun_ajaran where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "j.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "nama_mapel" || $field == "tahun_ajaran" || $field == "tingkatan" || $field == "jenis_ujian"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "j.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "m.nama_mapel LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "ju.nama_ujian LIKE '%" . $inputan . "%' ";
						$where .= "OR " . "t.label LIKE '%" . $inputan . "%' ";
						$where .= "OR " . "ta.label LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select j.*, m.nama_mapel, ju.nama_ujian, t.label as tingkatan, ta.label as tahun_ajaran from jadwal_ujian_sd j join ujian_mapel_sd m on j.id_mapel = m.id_mapel join jenis_ujian ju on j.id_jenis_ujian = ju.id_jenis_ujian join tingkatan_sd t on j.id_tingkatan = t.id_tingkatan_sd join tahun_ajaran ta on j.id_tahun_ajaran = ta.id_tahun_ajaran where ".$where,"result");
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
			if (strpos($field_search[$i], "id_jadwal") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			elseif (strpos($field_search[$i], "id_mapel") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('MATA PELAJARAN'));
			}
			elseif (strpos($field_search[$i], "id_jenis_ujian") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NAMA UJIAN'));
			}
			elseif (strpos($field_search[$i], "id_tingkatan") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TINGKATAN'));
			}
			elseif (strpos($field_search[$i], "daftar_kelas") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('KELAS'));
			}
			elseif (strpos($field_search[$i], "id_tahun_ajaran") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TAHUN AJARAN'));
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
		        if (strpos($kolom, "id_jadwal") !== false) {
		        	$value_data = ($key+1);
		        }
				if (strpos($kolom, "id_mapel") !== false) {
		        	$value_data = strip_tags($value->nama_mapel); 
		        }
		        if (strpos($kolom, "id_jenis_ujian") !== false) {
		        	$value_data = strip_tags($value->nama_ujian);
		        }
		        if (strpos($kolom, "id_tingkatan") !== false) {
		        	$value_data = strip_tags($value->tingkatan);
		        }
		        if (strpos($kolom, "id_tahun_ajaran") !== false) {
		        	$value_data = strip_tags($value->tahun_ajaran);
		        }

		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Jadwal Ujian Aktif SD - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	public function import_jadwal()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext=='xlsx' || $ext=='xls'){
				
			}
			else{
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls '.$ext);
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);
			//delete semua ujian
			$this->mymodel->delete("jadwal_ujian_sd", "id_jadwal !=", 0);
			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					$nama_mapel = strtoupper($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					$get_mapel = $this->mymodel->withquery("select id_mapel from ujian_mapel_sd where nama_mapel = '".$nama_mapel."'","row");
					if (empty($get_mapel)) {
						$this->db->trans_rollback();
						//$this->load->library("session");
						$this->session->set_flashdata('error', 'Import data gagal ' . $nama_mapel.' tidak ditemukan. (Baris ke-'.$row.')');
						redirect($_SERVER['HTTP_REFERER']);
					}
					$nama_ujian = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$get_jenis_ujian = $this->mymodel->withquery("select id_jenis_ujian from jenis_ujian where nama_ujian = '".$nama_ujian."'","row");
					$tingkatan = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$get_tingkatan = $this->mymodel->withquery("select id_tingkatan_sd from tingkatan_sd where label = '".$tingkatan."'","row");
					$kelas = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$tahun_ajaran = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
					$get_tahun_ajaran = $this->mymodel->withquery("select id_tahun_ajaran from tahun_ajaran where label = '".$tahun_ajaran."'","row");
					$data_jadwal = array(
						"id_mapel" => $get_mapel->id_mapel,
						"id_jenis_ujian" => $get_jenis_ujian->id_jenis_ujian,
						"id_tingkatan" => $get_tingkatan->id_tingkatan_sd,
						"daftar_kelas" => $kelas,
						"tanggal" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
						"hari" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
						"jam_mulai" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
						"jam_selesai" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
						"id_tahun_ajaran" => $get_tahun_ajaran->id_tahun_ajaran,
						"semester" => $worksheet->getCellByColumnAndRow(10, $row)->getValue()
					);
					//check null
					foreach ($data_jadwal as $key => $item) {
						if(empty($item)){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong ".$key.' (Baris ke-'.$row.')');
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					//isi ulang dengan jadwal terbaru
					$save_data = $this->mymodel->insertid("jadwal_ujian_sd", $data_jadwal);
					// dd($data_pendaftaran);
					if (empty($save_data)) {
						$this->db->trans_rollback();
						//$this->load->library("session");
						$this->session->set_flashdata('error', 'Import data gagal ' .'(Baris ke-'.$row.')');
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data jadwal ujian berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	
}


/* End of file jadwal_ujian_sd.php */
/* Location: ./application/controllers/administrator/Jadwal Ujian Sd.php */