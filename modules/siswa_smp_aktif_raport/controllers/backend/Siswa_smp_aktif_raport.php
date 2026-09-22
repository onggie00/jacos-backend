<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Siswa Smp Aktif Raport Controller
*| --------------------------------------------------------------------------
*| Siswa Smp Aktif Raport site
*|
*/
class Siswa_smp_aktif_raport extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_smp_aktif_raport');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Siswa Smp Aktif Raports
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_smp_aktif_raport_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['siswa_smp_aktif_raports'] = $this->model_siswa_smp_aktif_raport->get($filter, $field, $this->limit_page, $offset);
		$this->data['siswa_smp_aktif_raport_counts'] = $this->model_siswa_smp_aktif_raport->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/siswa_smp_aktif_raport/index/',
			'total_rows'   => $this->model_siswa_smp_aktif_raport->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Rapor SMP List');
		$this->render('backend/standart/administrator/siswa_smp_aktif_raport/siswa_smp_aktif_raport_list', $this->data);
	}
	
	/**
	* Add new siswa_smp_aktif_raports
	*
	*/
	public function add()
	{
		$this->is_allowed('siswa_smp_aktif_raport_add');

		$this->template->title('Rapor SMP New');
		$this->render('backend/standart/administrator/siswa_smp_aktif_raport/siswa_smp_aktif_raport_add', $this->data);
	}

	/**
	* Add New Siswa Smp Aktif Raports
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('siswa_smp_aktif_raport_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nis', 'NIS', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Detail Siswa', 'trim|max_length[11]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('jenis_ujian', 'Nama Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tanggal_sync_valid', 'Tanggal Sync SPP', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_aktif_raport_file_raport_name', 'File Rapor', 'trim|required|max_length[255]');
		

		if ($this->form_validation->run()) {
			$siswa_smp_aktif_raport_file_raport_uuid = $this->input->post('siswa_smp_aktif_raport_file_raport_uuid');
			$siswa_smp_aktif_raport_file_raport_name = $this->input->post('siswa_smp_aktif_raport_file_raport_name');
		
			$save_data = [
				'nis' => $this->input->post('nis'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'jenis_ujian' => $this->input->post('jenis_ujian'),
				'tanggal_sync_valid' => $this->input->post('tanggal_sync_valid'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_smp_aktif_raport/')) {
				mkdir(FCPATH . '/uploads/siswa_smp_aktif_raport/');
			}

			if (!empty($siswa_smp_aktif_raport_file_raport_name)) {
				$siswa_smp_aktif_raport_file_raport_name_copy = date('YmdHis') . '-' . $siswa_smp_aktif_raport_file_raport_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_aktif_raport_file_raport_uuid . '/' . $siswa_smp_aktif_raport_file_raport_name, 
						FCPATH . 'uploads/siswa_smp_aktif_raport/' . $siswa_smp_aktif_raport_file_raport_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp_aktif_raport/' . $siswa_smp_aktif_raport_file_raport_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport'] = $siswa_smp_aktif_raport_file_raport_name_copy;
			}
		
			
			$save_siswa_smp_aktif_raport = $this->model_siswa_smp_aktif_raport->store($save_data);
            

			if ($save_siswa_smp_aktif_raport) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_smp_aktif_raport;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_smp_aktif_raport/edit/' . $save_siswa_smp_aktif_raport, 'Edit Siswa Smp Aktif Raport'),
						anchor('administrator/siswa_smp_aktif_raport', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/siswa_smp_aktif_raport/edit/' . $save_siswa_smp_aktif_raport, 'Edit Siswa Smp Aktif Raport')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_smp_aktif_raport');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_smp_aktif_raport');
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
	* Update view Siswa Smp Aktif Raports
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('siswa_smp_aktif_raport_update');

		$this->data['siswa_smp_aktif_raport'] = $this->model_siswa_smp_aktif_raport->find($id);

		$this->template->title('Rapor SMP Update');
		$this->render('backend/standart/administrator/siswa_smp_aktif_raport/siswa_smp_aktif_raport_update', $this->data);
	}

	/**
	* Update Siswa Smp Aktif Raports
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_smp_aktif_raport_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nis', 'NIS', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_siswa_aktif', 'Detail Siswa', 'trim|max_length[11]');
		$this->form_validation->set_rules('tahun_ajaran', 'Tahun Ajaran', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('jenis_ujian', 'Nama Ujian', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tanggal_sync_valid', 'Tanggal Sync SPP', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_aktif_raport_file_raport_name', 'File Rapor', 'trim|required|max_length[255]');
		
		if ($this->form_validation->run()) {
			$siswa_smp_aktif_raport_file_raport_uuid = $this->input->post('siswa_smp_aktif_raport_file_raport_uuid');
			$siswa_smp_aktif_raport_file_raport_name = $this->input->post('siswa_smp_aktif_raport_file_raport_name');
		
			$save_data = [
				'nis' => $this->input->post('nis'),
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'id_siswa_aktif' => $this->input->post('id_siswa_aktif'),
				'tahun_ajaran' => $this->input->post('tahun_ajaran'),
				'jenis_ujian' => $this->input->post('jenis_ujian'),
				'tanggal_sync_valid' => $this->input->post('tanggal_sync_valid'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_smp_aktif_raport/')) {
				mkdir(FCPATH . '/uploads/siswa_smp_aktif_raport/');
			}

			if (!empty($siswa_smp_aktif_raport_file_raport_uuid)) {
				$siswa_smp_aktif_raport_file_raport_name_copy = date('YmdHis') . '-' . $siswa_smp_aktif_raport_file_raport_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_aktif_raport_file_raport_uuid . '/' . $siswa_smp_aktif_raport_file_raport_name, 
						FCPATH . 'uploads/siswa_smp_aktif_raport/' . $siswa_smp_aktif_raport_file_raport_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp_aktif_raport/' . $siswa_smp_aktif_raport_file_raport_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_raport'] = $siswa_smp_aktif_raport_file_raport_name_copy;
			}
		
			
			$save_siswa_smp_aktif_raport = $this->model_siswa_smp_aktif_raport->change($id, $save_data);

			if ($save_siswa_smp_aktif_raport) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/siswa_smp_aktif_raport', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_smp_aktif_raport');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_smp_aktif_raport');
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
	* delete Siswa Smp Aktif Raports
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('siswa_smp_aktif_raport_delete');

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
            set_message(cclang('has_been_deleted', 'siswa_smp_aktif_raport'), 'success');
        } else {
            set_message(cclang('error_delete', 'siswa_smp_aktif_raport'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Siswa Smp Aktif Raports
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('siswa_smp_aktif_raport_view');

		$this->data['siswa_smp_aktif_raport'] = $this->model_siswa_smp_aktif_raport->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Rapor SMP Detail');
		$this->render('backend/standart/administrator/siswa_smp_aktif_raport/siswa_smp_aktif_raport_view', $this->data);
	}
	
	/**
	* delete Siswa Smp Aktif Raports
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$siswa_smp_aktif_raport = $this->model_siswa_smp_aktif_raport->find($id);

		if (!empty($siswa_smp_aktif_raport->file_raport)) {
			$path = FCPATH . '/uploads/siswa_smp_aktif_raport/' . $siswa_smp_aktif_raport->file_raport;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_siswa_smp_aktif_raport->remove($id);
	}
	
	/**
	* Upload Image Siswa Smp Aktif Raport	* 
	* @return JSON
	*/
	public function upload_file_raport_file()
	{
		if (!$this->is_allowed('siswa_smp_aktif_raport_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_smp_aktif_raport',
		]);
	}

	/**
	* Delete Image Siswa Smp Aktif Raport	* 
	* @return JSON
	*/
	public function delete_file_raport_file($uuid)
	{
		if (!$this->is_allowed('siswa_smp_aktif_raport_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_raport', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'siswa_smp_aktif_raport',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/siswa_smp_aktif_raport/'
        ]);
	}

	/**
	* Get Image Siswa Smp Aktif Raport	* 
	* @return JSON
	*/
	public function get_file_raport_file($id)
	{
		if (!$this->is_allowed('siswa_smp_aktif_raport_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_smp_aktif_raport = $this->model_siswa_smp_aktif_raport->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_raport', 
            'table_name'        => 'siswa_smp_aktif_raport',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/siswa_smp_aktif_raport/',
            'delete_endpoint'   => 'administrator/siswa_smp_aktif_raport/delete_file_raport_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('siswa_smp_aktif_raport_export');

		$this->model_siswa_smp_aktif_raport->export('siswa_smp_aktif_raport', 'siswa_smp_aktif_raport');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('siswa_smp_aktif_raport_export');

		$this->model_siswa_smp_aktif_raport->pdf('siswa_smp_aktif_raport', 'siswa_smp_aktif_raport');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_smp_aktif_raport_export');

		$table = $title = 'siswa_smp_aktif_raport';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_siswa_smp_aktif_raport->find($id);
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

	public function import_raport(){
		//$jenjang = $this->input->post('jenjang');
		$tahun_ajaran = $this->input->post('tahun_ajaran');
		$tanggal_sync_valid = $this->input->post('tanggal_sync_valid');
		$jenis_ujian = $this->input->post('jenis_ujian');
		$total_file = count($_FILES['file_raport']['name']);
		$file_raport = $_FILES['file_raport'];

		//validasi data raport sudah ada / belum
		for($i = 0; $i < $total_file; $i++) {
			$nama_file = $file_raport['name'][$i];
			$tmp_file = $file_raport['tmp_name'][$i];
			$ext_file = pathinfo($nama_file, PATHINFO_EXTENSION);
			$nis = substr($nama_file, 0, (strpos($nama_file, $ext_file)-1));
			//get_siswa
			$siswa = $this->mymodel->withquery("select id_siswa_smp_aktif, nama_lengkap, nis from siswa_smp_aktif where nis = '".$nis."'","row");
			$check_data = $this->mymodel->withquery("select * from siswa_smp_aktif_raport where tahun_ajaran = '".$tahun_ajaran."' and nis = '".$nis."' and nama_lengkap = '".$siswa->nama_lengkap."' and jenis_ujian = '".$jenis_ujian."'","row");
			//echo $nama_file."-".$ext_file."-".$nis;
			$nama_file = $nis."_".str_replace("/", "-", $tahun_ajaran)."_".str_replace("/", "-", $jenis_ujian).".".$ext_file;
			if (empty($check_data)) {
				$data = [
					'tahun_ajaran' => $tahun_ajaran,
					'tanggal_sync_valid' => $tanggal_sync_valid,
					'nis' => $nis,
					'nama_lengkap' => $siswa->nama_lengkap,
					'id_siswa_aktif' => $siswa->id_siswa_smp_aktif,
					'jenis_ujian' => $jenis_ujian,
					'file_raport' => $nama_file,
				];
				$id_rapor = $this->mymodel->insertid('siswa_smp_aktif_raport', $data);
			}
			else{
				$data = [
					'tanggal_sync_valid' => $tanggal_sync_valid,
					'jenis_ujian' => $jenis_ujian,
					'file_raport' => $nama_file,
				];
				$this->mymodel->update('siswa_smp_aktif_raport', $data, ['id' => $check_data->id]);
				$id_rapor = $check_data->id;
			}
			//pindah dan rename file upload
			$trans_folder = './uploads/siswa_smp_aktif_raport/';
            if(!is_dir($trans_folder)){
                mkdir($trans_folder, 0777);
            }
            $uploaddir = $trans_folder;
            /* $img = explode('.', $_FILES['file_raport']['name'][$i]);
            $extension = end($img);
            $filename_original = "";
            for ($j=0; $j < count($img)-1; $j++) {
            	if ($filename_original == "") {
                	$filename_original .= $img[$j];
                }
                else{
                	$filename_original .= ".".$img[$j];
                }
            } */
            /* $filename_no_extension = $filename_original;
            $file_name =  $filename_no_extension.".".$extension; */
            $uploadfile = $uploaddir.$nama_file;
            if (move_uploaded_file($_FILES['file_raport']['tmp_name'][$i], $uploadfile)) {
                /* if($extension == "pdf" || $extension == "doc" || $extension == "docx" || $extension == "docs"){
                    $data = array(
                        "file_raport" => $nama_file
                    );
                }
                $up = $this->mymodel->update("siswa_smp_aktif",$data, "nis", $filename_original); */
                $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                /* $filenya = array(
                    "file_upload" => base_url("uploads/siswa_smp_aktif/").$filename_no_extension.".pdf",
                    "nama_asli" => $_FILES['file_raport']['name'][$i],
                    "ukuran" => $_FILES['file_raport']['size'][$i]
                );
                array_push($data_file, $filenya);
                $file_terupload++; */
            }
            else{
            	$msg = array('success'=>0,'message'=>'Upload File Gagal ');
            }
		}
		$this->session->set_flashdata('success', 'Import raport selesai');
		redirect($_SERVER['HTTP_REFERER']);
	}
}


/* End of file siswa_smp_aktif_raport.php */
/* Location: ./application/controllers/administrator/Siswa Smp Aktif Raport.php */