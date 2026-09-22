<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Acara Presensi Controller
*| --------------------------------------------------------------------------
*| Acara Presensi site
*|
*/
class Acara_presensi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_acara_presensi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Acara Presensis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('acara_presensi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$id_acara = $this->input->get('id_acara');
		$this->limit_page = 40;
		$this->data['acara_presensis'] = $this->model_acara_presensi->get($filter, $field, $id_acara, $this->limit_page, $offset);
		$this->data['acara_presensi_counts'] = $this->model_acara_presensi->count_all($filter, $field, $id_acara);

		$config = [
			'base_url'     => 'administrator/acara_presensi/index/',
			'total_rows'   => $this->model_acara_presensi->count_all($filter, $field, $id_acara),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Acara Presensi List');
		$this->render('backend/standart/administrator/acara_presensi/acara_presensi_list', $this->data);
	}
	
	/**
	* Add new acara_presensis
	*
	*/
	public function add()
	{
		$this->is_allowed('acara_presensi_add');

		$this->template->title('Acara Presensi New');
		$this->render('backend/standart/administrator/acara_presensi/acara_presensi_add', $this->data);
	}

	/**
	* Add New Acara Presensis
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('acara_presensi_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('peserta', 'Peserta', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'npp' => $this->input->post('npp'),
				'peserta' => $this->input->post('peserta'),
				'role' => $this->input->post('role'),
				'role_lainnya' => $this->input->post('role_lainnya'),
				'waktu_presensi' => $this->input->post('waktu_presensi'),
				'waktu_presensi_selesai' => $this->input->post('waktu_presensi_selesai'),
			];

			
			$save_acara_presensi = $this->model_acara_presensi->store($save_data);
            

			if ($save_acara_presensi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_acara_presensi;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/acara_presensi/edit/' . $save_acara_presensi, 'Edit Acara Presensi'),
						anchor('administrator/acara_presensi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/acara_presensi/edit/' . $save_acara_presensi, 'Edit Acara Presensi')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_presensi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_presensi');
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
	* Update view Acara Presensis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('acara_presensi_update');

		$this->data['acara_presensi'] = $this->model_acara_presensi->find($id);

		$this->template->title('Acara Presensi Update');
		$this->render('backend/standart/administrator/acara_presensi/acara_presensi_update', $this->data);
	}

	/**
	* Update Acara Presensis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('acara_presensi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('id_acara', 'Acara', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('peserta', 'Peserta', 'trim|required');
		$this->form_validation->set_rules('role', 'Role', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'id_acara' => $this->input->post('id_acara'),
				'npp' => $this->input->post('npp'),
				'peserta' => $this->input->post('peserta'),
				'role' => $this->input->post('role'),
				'role_lainnya' => $this->input->post('role_lainnya'),
				'waktu_presensi' => $this->input->post('waktu_presensi'),
				'waktu_presensi_selesai' => $this->input->post('waktu_presensi_selesai'),
			];

			
			$save_acara_presensi = $this->model_acara_presensi->change($id, $save_data);

			if ($save_acara_presensi) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/acara_presensi', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara_presensi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara_presensi');
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
	* delete Acara Presensis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('acara_presensi_delete');

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
            set_message(cclang('has_been_deleted', 'acara_presensi'), 'success');
        } else {
            set_message(cclang('error_delete', 'acara_presensi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Acara Presensis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('acara_presensi_view');

		$this->data['acara_presensi'] = $this->model_acara_presensi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Acara Presensi Detail');
		$this->render('backend/standart/administrator/acara_presensi/acara_presensi_view', $this->data);
	}
	
	/**
	* delete Acara Presensis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$acara_presensi = $this->model_acara_presensi->find($id);

		
		
		return $this->model_acara_presensi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('acara_presensi_export');

		//$this->model_acara_presensi->export('acara_presensi', 'acara_presensi');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$id_acara = $this->input->get("id_acara");
		$where = null;
		$field_search = ['id_acara','npp', 'nama_peserta', "role", "waktu_presensi", "waktu_presensi_selesai"];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select * from acara_presensi ","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "nama_lengkap"){
                $where .= "(" . "sa.nama_lengkap LIKE '%" . $inputan . "%' )";
            }
            else if($field == "kelas"){
                $where .= "(" . "k.label LIKE '%" . $inputan . "%' )";
            }
			else if($field == "tahun_ajaran"){
				$where .= "(" . "t.label LIKE '%" . $inputan . "%' )";
			}
            else{
                $where .= "(" . "sa.".$field . " LIKE '%" . $inputan . "%' )";
            }
			$get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_sd_aktif sa join kelas_sd k on sa.id_kelas = k.id_kelas_sd join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_sd s on sa.id_siswa_sd = s.id_siswa_sd where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "sa.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "kelas" || $field == "tahun_ajaran" || $field == "nisn" || $field == "tahun_ajaran" || $field == "email_ms_office" || $field == "email_ms_office_ortu" || $field == "notelp_ibu" || $field == "notelp_ayah" || $field == "spp_siswa"){
	                	continue;
	                }
	                else {
	                    $where .= "OR " . "sa.".$field . " LIKE '%" . $inputan . "%' ";
	                    $where .= "OR " . "k.label LIKE '%" . $inputan . "%' ";
						$where .= "OR " . "t.label LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	            }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_sd_aktif sa join kelas_sd k on sa.id_kelas = k.id_kelas_sd join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_sd s on sa.id_siswa_sd = s.id_siswa_sd where ".$where,"result");
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
			if (strpos($field_search[$i], "id_siswa_sd_aktif") !== false && $i == 0) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if (strpos($field_search[$i], "device_id_siswa") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA SUDAH PAKAI APLIKASI?'));
			}
			else if (strpos($field_search[$i], "device_id_ortu") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('ORTU SUDAH PAKAI APLIKASI?'));
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
		        if (strpos($kolom, "id_siswa_sd_aktif") !== false && $i == 0) {
		        	$value_data = ($key+1);
		        }
				if (strpos($kolom, "file_raport") !== false) {
		        	$value_data = base_url("uploads/siswa_sd_aktif/").$value_data;
		        }
				if (strpos($kolom, "device_id_siswa") !== false && !empty($value_data)) {
		        	$value_data = "SUDAH";
		        }
		        else if (strpos($kolom, "device_id_siswa") !== false && empty($value_data)) {
		        	$value_data = "BELUM";
		        }
		        if (strpos($kolom, "device_id_ortu") !== false && !empty($value_data)) {
		        	$value_data = "SUDAH";
		        }
		        else if(strpos($kolom, "device_id_ortu") !== false && empty($value_data)){
		        	$value_data = "BELUM";
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Presensi Acara - '.$title.'.xls"'); 
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
		$this->is_allowed('acara_presensi_export');

		$this->model_acara_presensi->pdf('acara_presensi', 'acara_presensi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('acara_presensi_export');

		$table = $title = 'acara_presensi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_acara_presensi->find($id);
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

	public function detail_peserta(){
		$id_acara = $this->input->post('id_acara');

		$no = 1;
		$get_data = $this->mymodel->withquery("select p.id_presensi, a.nama_acara as acara, p.npp, p.peserta, p.role, p.waktu_presensi, a.is_certificated, a.no_certificate, a.file_certificate, a.file_certificate_back, p.custom_sertifikat from acara_presensi p 
		join acara a on p.id_acara = a.id_acara 
		where p.id_acara = '".$id_acara."' order by p.waktu_presensi ASC","result");

		if(!empty($get_data)){
			foreach ($get_data as $key => $value) {
				$link_sertifikat = "";
				if(!empty($value->custom_sertifikat)){
					$link_sertifikat = base_url('uploads/acara_presensi/').$value->custom_sertifikat;
				}
				else if($value->is_certificated == 1 && !empty($value->file_certificate)){
					$link_sertifikat = site_url('apiapp/acara/lihat_sertifikat?id_acara='.$id_acara.'&npp='.$value->npp.'&role='.$value->role);
				}

				$value->action = '
					<div style="display:inline-flex;gap:4px;justify-content:center">
						<a href="'.site_url('administrator/acara_presensi/edit/' . $value->id_presensi).'" class="labs-btn labs-btn--warning labs-btn--sm" title="'.cclang('update_button').'"><i class="fa fa-edit"></i> Edit</a>
						<a href="javascript:void(0);" data-href="'.site_url('administrator/acara_presensi/delete/'.$value->id_presensi).'" class="labs-btn labs-btn--danger labs-btn--sm remove-data" title="'.cclang('remove_button').'"><i class="fa fa-trash"></i> Hapus</a>
						<a target="_blank" href="'.$link_sertifikat.'" class="labs-btn labs-btn--info labs-btn--sm" title="Lihat sertifikat"><i class="fa fa-file-pdf-o"></i> Sertifikat</a>
					</div>
				';
				$value->no = $no;
				$no++;
			}
		}
		$data = array(
			"status" => true,
			"message" => "Loading Data Success",
			// "recordsTotal" => count($get_data),
			// "recordsFiltered" => count($get_data),
			"data" => $get_data,
		);
		echo json_encode($data);
	}
}


/* End of file acara_presensi.php */
/* Location: ./application/controllers/administrator/Acara Presensi.php */