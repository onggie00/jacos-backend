<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use SimpleSoftwareIO\QrCode\Generator;

/**
*| --------------------------------------------------------------------------
*| Acara Controller
*| --------------------------------------------------------------------------
*| Acara site
*|
*/
class Acara extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_acara');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Acaras
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('acara_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['acaras'] = $this->model_acara->get($filter, $field, $this->limit_page, $offset);
		$this->data['acara_counts'] = $this->model_acara->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/acara/index/',
			'total_rows'   => $this->model_acara->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Acara List');
		$this->render('backend/standart/administrator/acara/acara_list', $this->data);
	}
	
	/**
	* Add new acaras
	*
	*/
	public function add()
	{
		$this->is_allowed('acara_add');

		$this->template->title('Acara New');
		$this->render('backend/standart/administrator/acara/acara_add', $this->data);
	}

	/**
	* Add New Acaras
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('acara_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('peserta_acara[]', 'Acara Untuk', 'trim|required');
		$this->form_validation->set_rules('nama_acara', 'Nama', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('unique_code', 'Code', 'trim|max_length[50]');
		$this->form_validation->set_rules('waktu_mulai', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('waktu_selesai', 'Waktu Selesai', 'trim|required');
		$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('is_certificated', 'Certificated?', 'trim|required');
		$this->form_validation->set_rules('evaluation_url', 'Evaluation URL', 'trim');

		if ($this->form_validation->run()) {
			$acara_file_certificate_uuid = $this->input->post('acara_file_certificate_uuid');
			$acara_file_certificate_name = $this->input->post('acara_file_certificate_name');
			$acara_file_certificate_back_uuid = $this->input->post('acara_file_certificate_back_uuid');
			$acara_file_certificate_back_name = $this->input->post('acara_file_certificate_back_name');
			
			if (!empty($this->input->post('unique_code'))) {
				$unique_code = $this->input->post('unique_code');
			}
			else{
				$unique_code = rand(100000, 999999);
			}
			if (!empty($this->input->post('unique_code_finish'))) {
				$unique_code_finish = $this->input->post('unique_code_finish');
			}
			else{
				$unique_code_finish = rand(100000, 999999);
			}
			$show_npp = $this->input->post('sertifikat_show_npp') ? 1 : 0;
			$show_instansi = $this->input->post('sertifikat_show_instansi') ? 1 : 0;
			$save_data = [
				'peserta_acara' => implode(',', (array) $this->input->post('peserta_acara')),
				'nama_acara' => $this->input->post('nama_acara'),
				'unique_code' => $unique_code,
				'unique_code_finish' => $unique_code_finish,
				'narasumber' => $this->input->post('narasumber'),
				'keterangan' => $this->input->post('keterangan'),
				'waktu_mulai' => $this->input->post('waktu_mulai'),
				'waktu_selesai' => $this->input->post('waktu_selesai'),
				'lokasi' => $this->input->post('lokasi'),
				'is_certificated' => $this->input->post('is_certificated'),
				'no_certificate' => $this->input->post('no_certificate'),
				'start_number_certificate' => $this->input->post('start_number_certificate') !== '' ? (int)$this->input->post('start_number_certificate') : NULL,
				'evaluation_url' => $this->input->post('evaluation_url'),
				'sertifikat_no_pos_top' => $this->input->post('sertifikat_no_pos_top') !== '' ? $this->input->post('sertifikat_no_pos_top') : NULL,
				'sertifikat_no_pos_left' => $this->input->post('sertifikat_no_pos_left') !== '' ? $this->input->post('sertifikat_no_pos_left') : NULL,
				'sertifikat_no_font_size' => $this->input->post('sertifikat_no_font_size') !== '' ? $this->input->post('sertifikat_no_font_size') : NULL,
				'sertifikat_nama_pos_top' => $this->input->post('sertifikat_nama_pos_top') !== '' ? $this->input->post('sertifikat_nama_pos_top') : NULL,
				'sertifikat_nama_pos_left' => $this->input->post('sertifikat_nama_pos_left') !== '' ? $this->input->post('sertifikat_nama_pos_left') : NULL,
				'sertifikat_nama_font_size' => $this->input->post('sertifikat_nama_font_size') !== '' ? $this->input->post('sertifikat_nama_font_size') : NULL,
				'sertifikat_show_npp' => $show_npp,
				'sertifikat_npp_pos_top' => $show_npp ? ($this->input->post('sertifikat_npp_pos_top') !== '' ? $this->input->post('sertifikat_npp_pos_top') : NULL) : NULL,
				'sertifikat_npp_pos_left' => $show_npp ? ($this->input->post('sertifikat_npp_pos_left') !== '' ? $this->input->post('sertifikat_npp_pos_left') : NULL) : NULL,
				'sertifikat_npp_font_size' => $show_npp ? ($this->input->post('sertifikat_npp_font_size') !== '' ? $this->input->post('sertifikat_npp_font_size') : NULL) : NULL,
				'sertifikat_show_instansi' => $show_instansi,
				'sertifikat_instansi_pos_top' => $show_instansi ? ($this->input->post('sertifikat_instansi_pos_top') !== '' ? $this->input->post('sertifikat_instansi_pos_top') : NULL) : NULL,
				'sertifikat_instansi_pos_left' => $show_instansi ? ($this->input->post('sertifikat_instansi_pos_left') !== '' ? $this->input->post('sertifikat_instansi_pos_left') : NULL) : NULL,
				'sertifikat_instansi_font_size' => $show_instansi ? ($this->input->post('sertifikat_instansi_font_size') !== '' ? $this->input->post('sertifikat_instansi_font_size') : NULL) : NULL,
				'sertifikat_no_font_style' => $this->input->post('sertifikat_no_font_style'),
				'sertifikat_nama_font_style' => $this->input->post('sertifikat_nama_font_style'),
				'sertifikat_instansi_font_style' => $this->input->post('sertifikat_instansi_font_style'),
				'sertifikat_npp_font_style' => $this->input->post('sertifikat_npp_font_style'),
			];

			if (!is_dir(FCPATH . '/uploads/acara/')) {
				mkdir(FCPATH . '/uploads/acara/');
			}
			if (!empty($unique_code)) {
				//generate barcode image
				$qrcode = new Generator;
				$qrCodes = [];
				$save_data['qr_code'] = $unique_code.'.png';
				$qrCodes['simple'] = $qrcode->size(360)->margin(2)->format('png')->generate($unique_code, FCPATH.'/uploads/acara/'.$save_data['qr_code'] );
			}

			if (!empty($unique_code_finish)) {
				//generate barcode image
				$qrcode_finish = new Generator;
				$qrCodes_finish = [];
				$save_data['qr_code_finish'] = $unique_code_finish.'.png';
				$qrCodes_finish['simple'] = $qrcode->size(360)->margin(2)->format('png')->generate($unique_code_finish, FCPATH.'/uploads/acara/'.$save_data['qr_code_finish'] );
				//$qrCodes_finish['merge_logo'] = $qrcode->size(360)->margin(2)->format('png')->merge(FCPATH.'/uploads/logo_labschool_2025.png', .3)->generate($unique_code_finish, FCPATH.'/uploads/acara/'.$save_data['qr_code_finish'] );
			}

			if (!empty($acara_file_certificate_name)) {
				$acara_file_certificate_name_copy = date('YmdHis') . '-' . $acara_file_certificate_name;

				rename(FCPATH . 'uploads/tmp/' . $acara_file_certificate_uuid . '/' . $acara_file_certificate_name,
						FCPATH . 'uploads/acara/' . $acara_file_certificate_name_copy);

				if (!is_file(FCPATH . '/uploads/acara/' . $acara_file_certificate_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_certificate'] = $acara_file_certificate_name_copy;
			}

			if (!empty($acara_file_certificate_back_name)) {
				$acara_file_certificate_back_name_copy = date('YmdHis') . '-' . $acara_file_certificate_back_name;

				rename(FCPATH . 'uploads/tmp/' . $acara_file_certificate_back_uuid . '/' . $acara_file_certificate_back_name,
						FCPATH . 'uploads/acara/' . $acara_file_certificate_back_name_copy);

				if (!is_file(FCPATH . '/uploads/acara/' . $acara_file_certificate_back_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_certificate_back'] = $acara_file_certificate_back_name_copy;
			}


			$save_acara = $this->model_acara->store($save_data);
            

			if ($save_acara) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_acara;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/acara/edit/' . $save_acara, 'Edit Acara'),
						anchor('administrator/acara', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/acara/edit/' . $save_acara, 'Edit Acara')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara');
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
	* Update view Acaras
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('acara_update');

		$this->data['acara'] = $this->model_acara->find($id);

		$this->template->title('Acara Update');
		$this->render('backend/standart/administrator/acara/acara_update', $this->data);
	}

	/**
	* Update Acaras
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('acara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('peserta_acara[]', 'Acara Untuk', 'trim|required');
		$this->form_validation->set_rules('nama_acara', 'Nama', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('unique_code', 'Code', 'trim|max_length[50]');
		$this->form_validation->set_rules('waktu_mulai', 'Waktu Mulai', 'trim|required');
		$this->form_validation->set_rules('waktu_selesai', 'Waktu Selesai', 'trim|required');
		$this->form_validation->set_rules('lokasi', 'Lokasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('is_certificated', 'Certificated?', 'trim|required');
		$this->form_validation->set_rules('evaluation_url', 'Evaluation URL', 'trim');
		
		if ($this->form_validation->run()) {
			$acara_file_certificate_uuid = $this->input->post('acara_file_certificate_uuid');
			$acara_file_certificate_name = $this->input->post('acara_file_certificate_name');
			$acara_file_certificate_back_uuid = $this->input->post('acara_file_certificate_back_uuid');
			$acara_file_certificate_back_name = $this->input->post('acara_file_certificate_back_name');
		
			if (!empty($this->input->post('unique_code'))) {
				$unique_code = $this->input->post('unique_code');
			}
			else{
				$unique_code = rand(100000, 999999);
			}
			if (!empty($this->input->post('unique_code_finish'))) {
				$unique_code_finish = $this->input->post('unique_code_finish');
			}
			else{
				$unique_code_finish = rand(100000, 999999);
			}
			$show_npp = $this->input->post('sertifikat_show_npp') ? 1 : 0;
			$show_instansi = $this->input->post('sertifikat_show_instansi') ? 1 : 0;
			$save_data = [
				'peserta_acara' => implode(',', (array) $this->input->post('peserta_acara')),
				'nama_acara' => $this->input->post('nama_acara'),
				'unique_code' => $unique_code,
				'unique_code_finish' => $unique_code_finish,
				'narasumber' => $this->input->post('narasumber'),
				'keterangan' => $this->input->post('keterangan'),
				'waktu_mulai' => $this->input->post('waktu_mulai'),
				'waktu_selesai' => $this->input->post('waktu_selesai'),
				'lokasi' => $this->input->post('lokasi'),
				'is_certificated' => $this->input->post('is_certificated'),
				'no_certificate' => $this->input->post('no_certificate'),
				'start_number_certificate' => $this->input->post('start_number_certificate') !== '' ? (int)$this->input->post('start_number_certificate') : NULL,
				'evaluation_url' => $this->input->post('evaluation_url'),
				'sertifikat_no_pos_top' => $this->input->post('sertifikat_no_pos_top') !== '' ? $this->input->post('sertifikat_no_pos_top') : NULL,
				'sertifikat_no_pos_left' => $this->input->post('sertifikat_no_pos_left') !== '' ? $this->input->post('sertifikat_no_pos_left') : NULL,
				'sertifikat_no_font_size' => $this->input->post('sertifikat_no_font_size') !== '' ? $this->input->post('sertifikat_no_font_size') : NULL,
				'sertifikat_nama_pos_top' => $this->input->post('sertifikat_nama_pos_top') !== '' ? $this->input->post('sertifikat_nama_pos_top') : NULL,
				'sertifikat_nama_pos_left' => $this->input->post('sertifikat_nama_pos_left') !== '' ? $this->input->post('sertifikat_nama_pos_left') : NULL,
				'sertifikat_nama_font_size' => $this->input->post('sertifikat_nama_font_size') !== '' ? $this->input->post('sertifikat_nama_font_size') : NULL,
				'sertifikat_show_npp' => $show_npp,
				'sertifikat_npp_pos_top' => $show_npp ? ($this->input->post('sertifikat_npp_pos_top') !== '' ? $this->input->post('sertifikat_npp_pos_top') : NULL) : NULL,
				'sertifikat_npp_pos_left' => $show_npp ? ($this->input->post('sertifikat_npp_pos_left') !== '' ? $this->input->post('sertifikat_npp_pos_left') : NULL) : NULL,
				'sertifikat_npp_font_size' => $show_npp ? ($this->input->post('sertifikat_npp_font_size') !== '' ? $this->input->post('sertifikat_npp_font_size') : NULL) : NULL,
				'sertifikat_show_instansi' => $show_instansi,
				'sertifikat_instansi_pos_top' => $show_instansi ? ($this->input->post('sertifikat_instansi_pos_top') !== '' ? $this->input->post('sertifikat_instansi_pos_top') : NULL) : NULL,
				'sertifikat_instansi_pos_left' => $show_instansi ? ($this->input->post('sertifikat_instansi_pos_left') !== '' ? $this->input->post('sertifikat_instansi_pos_left') : NULL) : NULL,
				'sertifikat_instansi_font_size' => $show_instansi ? ($this->input->post('sertifikat_instansi_font_size') !== '' ? $this->input->post('sertifikat_instansi_font_size') : NULL) : NULL,
				'sertifikat_no_font_style' => $this->input->post('sertifikat_no_font_style'),
				'sertifikat_nama_font_style' => $this->input->post('sertifikat_nama_font_style'),
				'sertifikat_instansi_font_style' => $this->input->post('sertifikat_instansi_font_style'),
				'sertifikat_npp_font_style' => $this->input->post('sertifikat_npp_font_style'),
			];

			if (!is_dir(FCPATH . '/uploads/acara/')) {
				mkdir(FCPATH . '/uploads/acara/');
			}
			if (!empty($unique_code)) {
				//generate barcode image
				$qrcode = new Generator;
				$qrCodes = [];
				$save_data['qr_code'] = $unique_code.'.png';
				$qrCodes['simple'] = $qrcode->size(360)->margin(2)->format('png')->generate($unique_code, FCPATH.'/uploads/acara/'.$save_data['qr_code'] );
			}

			if (!empty($unique_code_finish)) {
				//generate barcode image
				$qrcode_finish = new Generator;
				$qrCodes_finish = [];
				$save_data['qr_code_finish'] = $unique_code_finish.'.png';
				$qrCodes_finish['simple'] = $qrcode->size(360)->margin(2)->format('png')->generate($unique_code_finish, FCPATH.'/uploads/acara/'.$save_data['qr_code_finish'] );
				//$qrCodes_finish['merge_logo'] = $qrcode->size(360)->margin(2)->format('png')->merge(FCPATH.'/uploads/logo_labschool_2025.png', .3)->generate($unique_code_finish, FCPATH.'/uploads/acara/'.$save_data['qr_code_finish'] );
			}

			if (!empty($acara_file_certificate_uuid)) {
				$acara_file_certificate_name_copy = date('YmdHis') . '-' . $acara_file_certificate_name;

				rename(FCPATH . 'uploads/tmp/' . $acara_file_certificate_uuid . '/' . $acara_file_certificate_name, 
						FCPATH . 'uploads/acara/' . $acara_file_certificate_name_copy);

				if (!is_file(FCPATH . '/uploads/acara/' . $acara_file_certificate_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_certificate'] = $acara_file_certificate_name_copy;
			}
		
			if (!empty($acara_file_certificate_back_uuid)) {
				$acara_file_certificate_back_name_copy = date('YmdHis') . '-' . $acara_file_certificate_back_name;

				rename(FCPATH . 'uploads/tmp/' . $acara_file_certificate_back_uuid . '/' . $acara_file_certificate_back_name, 
						FCPATH . 'uploads/acara/' . $acara_file_certificate_back_name_copy);

				if (!is_file(FCPATH . '/uploads/acara/' . $acara_file_certificate_back_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['file_certificate_back'] = $acara_file_certificate_back_name_copy;
			}
		
			
			$save_acara = $this->model_acara->change($id, $save_data);

			if ($save_acara) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/acara', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/acara');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = true;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/acara');
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
	* delete Acaras
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('acara_delete');

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
            set_message(cclang('has_been_deleted', 'acara'), 'success');
        } else {
            set_message(cclang('error_delete', 'acara'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Acaras
	*
	* @var $id String
	*/
	public function view($id)
	{
		//$this->is_allowed('acara_view');

		$this->data['acara'] = $this->mymodel->withquery("select * from acara where id_acara = '".$id."'","row");

		$this->template->title('Acara Detail');
		$this->render('backend/standart/administrator/acara/acara_view', $this->data);
	}
	
	/**
	* delete Acaras
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$acara = $this->model_acara->find($id);

		if (!empty($acara->qr_code)) {
			$path = FCPATH . '/uploads/acara/' . $acara->qr_code;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($acara->file_certificate)) {
			$path = FCPATH . '/uploads/acara/' . $acara->file_certificate;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($acara->file_certificate_back)) {
			$path = FCPATH . '/uploads/acara/' . $acara->file_certificate_back;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_acara->remove($id);
	}
	
	/**
	* Upload Image Acara	* 
	* @return JSON
	*/
	public function upload_qr_code_file()
	{
		if (!$this->is_allowed('acara_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'acara',
		]);
	}

	/**
	* Delete Image Acara	* 
	* @return JSON
	*/
	public function delete_qr_code_file($uuid)
	{
		if (!$this->is_allowed('acara_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'qr_code', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/'
        ]);
	}

	/**
	* Get Image Acara	* 
	* @return JSON
	*/
	public function get_qr_code_file($id)
	{
		if (!$this->is_allowed('acara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$acara = $this->model_acara->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'qr_code', 
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/',
            'delete_endpoint'   => 'administrator/acara/delete_qr_code_file'
        ]);
	}
	
	/**
	* Upload Image Acara	* 
	* @return JSON
	*/
	public function upload_file_certificate_file()
	{
		if (!$this->is_allowed('acara_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'acara',
		]);
	}

	/**
	* Delete Image Acara	* 
	* @return JSON
	*/
	public function delete_file_certificate_file($uuid)
	{
		if (!$this->is_allowed('acara_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_certificate', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/'
        ]);
	}

	/**
	* Get Image Acara	* 
	* @return JSON
	*/
	public function get_file_certificate_file($id)
	{
		if (!$this->is_allowed('acara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$acara = $this->model_acara->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_certificate', 
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/',
            'delete_endpoint'   => 'administrator/acara/delete_file_certificate_file'
        ]);
	}
	
	/**
	* Upload Image Acara	* 
	* @return JSON
	*/
	public function upload_file_certificate_back_file()
	{
		if (!$this->is_allowed('acara_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'acara',
		]);
	}

	/**
	* Delete Image Acara	* 
	* @return JSON
	*/
	public function delete_file_certificate_back_file($uuid)
	{
		if (!$this->is_allowed('acara_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'file_certificate_back', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/'
        ]);
	}

	/**
	* Get Image Acara	* 
	* @return JSON
	*/
	public function get_file_certificate_back_file($id)
	{
		if (!$this->is_allowed('acara_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$acara = $this->model_acara->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'file_certificate_back', 
            'table_name'        => 'acara',
            'primary_key'       => 'id_acara',
            'upload_path'       => 'uploads/acara/',
            'delete_endpoint'   => 'administrator/acara/delete_file_certificate_back_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('acara_export');

		$this->model_acara->export('acara', 'acara');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('acara_export');

		$this->model_acara->pdf('acara', 'acara');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('acara_export');

		$table = $title = 'acara';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_acara->find($id);
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
	 * Generate sample sertifikat PDF untuk preview (nama dummy).
	 */
	public function sample_sertifikat($id_acara)
	{
		$this->load->library('HtmlPdf');

		$get_acara = $this->mymodel->withquery("select * from acara where id_acara = ".$this->db->escape($id_acara),"row");

		if (empty($get_acara) || empty($get_acara->file_certificate)) {
			echo 'Sertifikat tidak tersedia.';
			return;
		}

		// Data dummy
		$start_num = (isset($get_acara->start_number_certificate) && (int)$get_acara->start_number_certificate > 0) ? (int)$get_acara->start_number_certificate : 1;
		$nomor_sertifikat = (!empty($get_acara->no_certificate)) ? $get_acara->no_certificate . $start_num : '';

		// Ambil role dari presensi pertama untuk deteksi jenjang guru
		$first_presensi = $this->mymodel->withquery("select role from acara_presensi where id_acara = ".$this->db->escape($id_acara)." order by waktu_presensi ASC limit 1","row");
		$sample_role = (!empty($first_presensi->role)) ? $first_presensi->role : 'guru_sd';

		// Nama instansi otomatis dari role (hanya guru)
		$nama_instansi = '';
		if (strpos($sample_role, 'guru_') !== false) {
			$jenjang_map = array(
				'guru_sd'  => 'SD',
				'guru_smp' => 'SMP',
				'guru_sma' => 'SMA',
				'guru_ft'  => 'FT',
			);
			$jenjang = isset($jenjang_map[$sample_role]) ? $jenjang_map[$sample_role] : 'SD';
			$nama_instansi = strtoupper($jenjang) . ' LABSCHOOL CIBUBUR';
		}

		$datas = array(
			'user' => (object) array(
				'nama_lengkap' => 'DUMMY PESERTA',
				'npp' => '00.00.000',
			),
			'acara' => $get_acara,
			'nomor_sertifikat' => $nomor_sertifikat,
			'tanggal_acara' => formatHari($get_acara->waktu_mulai).", ".formatTanggal($get_acara->waktu_mulai)." - ".formatTanggal($get_acara->waktu_selesai),
			'tanggal_sertifikat' => formatTanggal($get_acara->waktu_selesai),
			'nama_instansi' => $nama_instansi,
		);

		// Style sertifikat (fallback default dalam persen)
		$has_nomor = !empty($datas['nomor_sertifikat']);
		$datas['style_sertifikat'] = array(
			'no_pos_top'     => isset($get_acara->sertifikat_no_pos_top) ? (float)$get_acara->sertifikat_no_pos_top : 19.0,
			'no_pos_left'    => isset($get_acara->sertifikat_no_pos_left) ? (float)$get_acara->sertifikat_no_pos_left : 23.1,
			'no_font_size'   => isset($get_acara->sertifikat_no_font_size) ? (int)$get_acara->sertifikat_no_font_size : 16,
			'nama_pos_top'   => isset($get_acara->sertifikat_nama_pos_top) ? (float)$get_acara->sertifikat_nama_pos_top : ($has_nomor ? 6.5 : 13.1),
			'nama_pos_left'  => isset($get_acara->sertifikat_nama_pos_left) ? (float)$get_acara->sertifikat_nama_pos_left : 13.0,
			'nama_font_size' => isset($get_acara->sertifikat_nama_font_size) ? (int)$get_acara->sertifikat_nama_font_size : 30,
			'show_npp'       => isset($get_acara->sertifikat_show_npp) ? (int)$get_acara->sertifikat_show_npp : 0,
			'npp_pos_top'    => isset($get_acara->sertifikat_npp_pos_top) ? (float)$get_acara->sertifikat_npp_pos_top : 20.9,
			'npp_pos_left'   => isset($get_acara->sertifikat_npp_pos_left) ? (float)$get_acara->sertifikat_npp_pos_left : 13.0,
			'npp_font_size'  => isset($get_acara->sertifikat_npp_font_size) ? (int)$get_acara->sertifikat_npp_font_size : 12,
			'instansi_pos_top'   => isset($get_acara->sertifikat_instansi_pos_top) ? (float)$get_acara->sertifikat_instansi_pos_top : 18.0,
			'instansi_pos_left'  => isset($get_acara->sertifikat_instansi_pos_left) ? (float)$get_acara->sertifikat_instansi_pos_left : 13.0,
			'instansi_font_size' => isset($get_acara->sertifikat_instansi_font_size) ? (int)$get_acara->sertifikat_instansi_font_size : 18,
			'show_instansi'      => isset($get_acara->sertifikat_show_instansi) ? (int)$get_acara->sertifikat_show_instansi : 1,
			'no_font_style'         => isset($get_acara->sertifikat_no_font_style) ? $get_acara->sertifikat_no_font_style : 'normal',
			'nama_font_style'       => isset($get_acara->sertifikat_nama_font_style) ? $get_acara->sertifikat_nama_font_style : 'bold_italic',
			'instansi_font_style'   => isset($get_acara->sertifikat_instansi_font_style) ? $get_acara->sertifikat_instansi_font_style : 'normal',
			'npp_font_style'        => isset($get_acara->sertifikat_npp_font_style) ? $get_acara->sertifikat_npp_font_style : 'normal',
		);

		$pdf = new HTML2PDF('L', 'A4', 'en');
		ob_start();
		$this->load->view('template_sertifikat', $datas);
		$html = ob_get_contents();
		ob_end_clean();

		$pdf->WriteHTML($html);
		$pdf->Output('sample_sertifikat.pdf', 'I');
	}
}


/* End of file acara.php */
/* Location: ./application/controllers/administrator/Acara.php */