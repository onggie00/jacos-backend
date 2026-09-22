<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pimpinan Sd Controller
*| --------------------------------------------------------------------------
*| Pimpinan Sd site
*|
*/
class Pimpinan_sd extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pimpinan_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pimpinan Sds
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pimpinan_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['pimpinan_sds'] = $this->model_pimpinan_sd->get($filter, $field, $this->limit_page, $offset,[],$sort,$sort_type);
		$this->data['pimpinan_sd_counts'] = $this->model_pimpinan_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pimpinan_sd/index/',
			'total_rows'   => $this->model_pimpinan_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pimpinan Sd List');
		$this->render('backend/standart/administrator/pimpinan_sd/pimpinan_sd_list', $this->data);
	}
	
	public function add_slip_gaji()
	{
		$this->is_allowed('pimpinan_sd_add');

		$this->template->title('Pimpinan SD Slip Gaji');
		$this->render('backend/standart/administrator/pimpinan_sd/pimpinan_sd_add_slip_gaji', $this->data);
	}

	public function add_slip_gaji_save()
	{
		if (!$this->is_allowed('pimpinan_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$ids = $this->input->post('id_pimpinan');
		$uuid_files = $this->input->post('slip_gaji_uuid');
		$name_files = $this->input->post('slip_gaji_name');

		// dd($this->input->post('id_siswa_sd'));

		foreach ($ids as $key => $item) {
			$slip_gaji_uuid = $uuid_files[$key];
			$slip_gaji_name = $name_files[$key];

			$save_data = [];

			if (!is_dir(FCPATH . '/uploads/pimpinan_sd/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sd/');
			}

			if (!empty($slip_gaji_name)) {
				$slip_gaji_name_copy = date('YmdHis') . '-' . $slip_gaji_name;

				rename(
					FCPATH . 'uploads/tmp/' . $slip_gaji_uuid . '/' . $slip_gaji_name,
					FCPATH . 'uploads/pimpinan_sd/' . $slip_gaji_name_copy
				);

				if (!is_file(FCPATH . '/uploads/pimpinan_sd/' . $slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['slip_gaji'] = $slip_gaji_name_copy;
			}


			$save_pimpinan_sd = $this->model_pimpinan_sd->change($item, $save_data);
		}


		if ($save_pimpinan_sd) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_pimpinan_sd;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/pimpinan_sd', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/pimpinan_sd/edit/' . $save_pimpinan_sd, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/pimpinan_sd');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/pimpinan_sd');
			}
		}

		echo json_encode($this->data);
	}

	/**
	* Add new pimpinan_sds
	*
	*/
	public function add()
	{
		$this->is_allowed('pimpinan_sd_add');

		$this->template->title('Pimpinan Sd New');
		$this->render('backend/standart/administrator/pimpinan_sd/pimpinan_sd_add', $this->data);
	}

	/**
	* Add New Pimpinan Sds
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pimpinan_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'Nuptk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'Npp', 'trim|max_length[255]');
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		

		if ($this->form_validation->run()) {
			$pimpinan_sd_foto_profil_uuid = $this->input->post('pimpinan_sd_foto_profil_uuid');
			$pimpinan_sd_foto_profil_name = $this->input->post('pimpinan_sd_foto_profil_name');
			$pimpinan_sd_slip_gaji_uuid = $this->input->post('pimpinan_sd_slip_gaji_uuid');
			$pimpinan_sd_slip_gaji_name = $this->input->post('pimpinan_sd_slip_gaji_name');
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'npwp' => $this->input->post('npwp'),
				'alamat' => $this->input->post('alamat'),
				'agama' => $this->input->post('agama'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'status_menikah' => $this->input->post('status_menikah'),
				'jumlah_anak' => $this->input->post('jumlah_anak'),
				'no_telp' => $this->input->post('no_telp'),
				'email' => $this->input->post('email'),
				'id_posisi' => $this->input->post('id_posisi'),
				'satuan_pendidikan' => $this->input->post('satuan_pendidikan'),
				'unit' => $this->input->post('unit'),
				'id_mapel' => $this->input->post('id_mapel'),
				'status_kepegawaian' => $this->input->post('status_kepegawaian'),
				'informasi_kepala_pimpinan' => $this->input->post('informasi_kepala_pimpinan'),
				'emp_code' => $this->input->post('emp_code'),
				'token' => $this->input->post('token'),
				'token_expired' => $this->input->post('token_expired'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'no_kk' => $this->input->post('no_kk'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'presensi_role' => $this->input->post('presensi_role'),
			];

			if (!is_dir(FCPATH . '/uploads/pimpinan_sd/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sd/');
			}

			if (!empty($pimpinan_sd_foto_profil_name)) {
				$pimpinan_sd_foto_profil_name_copy = date('YmdHis') . '-' . $pimpinan_sd_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sd_foto_profil_uuid . '/' . $pimpinan_sd_foto_profil_name, 
						FCPATH . 'uploads/pimpinan_sd/' . $pimpinan_sd_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pimpinan_sd_foto_profil_name_copy;
			}
		
			if (!empty($pimpinan_sd_slip_gaji_name)) {
				$pimpinan_sd_slip_gaji_name_copy = date('YmdHis') . '-' . $pimpinan_sd_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sd_slip_gaji_uuid . '/' . $pimpinan_sd_slip_gaji_name, 
						FCPATH . 'uploads/pimpinan_sd/' . $pimpinan_sd_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pimpinan_sd_slip_gaji_name_copy;
			}
		
			
			$save_pimpinan_sd = $this->model_pimpinan_sd->store($save_data);
            

			if ($save_pimpinan_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pimpinan_sd;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pimpinan_sd/edit/' . $save_pimpinan_sd, 'Edit Pimpinan Sd'),
						anchor('administrator/pimpinan_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pimpinan_sd/edit/' . $save_pimpinan_sd, 'Edit Pimpinan Sd')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan_sd');
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
	* Update view Pimpinan Sds
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pimpinan_sd_update');

		$this->data['pimpinan_sd'] = $this->model_pimpinan_sd->find($id);

		$this->template->title('Pimpinan Sd Update');
		$this->render('backend/standart/administrator/pimpinan_sd/pimpinan_sd_update', $this->data);
	}

	/**
	* Update Pimpinan Sds
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pimpinan_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'Nuptk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'Npp', 'trim|max_length[255]');
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		
		if ($this->form_validation->run()) {
			$pimpinan_sd_foto_profil_uuid = $this->input->post('pimpinan_sd_foto_profil_uuid');
			$pimpinan_sd_foto_profil_name = $this->input->post('pimpinan_sd_foto_profil_name');
			$pimpinan_sd_slip_gaji_uuid = $this->input->post('pimpinan_sd_slip_gaji_uuid');
			$pimpinan_sd_slip_gaji_name = $this->input->post('pimpinan_sd_slip_gaji_name');
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'npwp' => $this->input->post('npwp'),
				'alamat' => $this->input->post('alamat'),
				'agama' => $this->input->post('agama'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'status_menikah' => $this->input->post('status_menikah'),
				'jumlah_anak' => $this->input->post('jumlah_anak'),
				'no_telp' => $this->input->post('no_telp'),
				'email' => $this->input->post('email'),
				'id_posisi' => $this->input->post('id_posisi'),
				'satuan_pendidikan' => $this->input->post('satuan_pendidikan'),
				'unit' => $this->input->post('unit'),
				'id_mapel' => $this->input->post('id_mapel'),
				'status_kepegawaian' => $this->input->post('status_kepegawaian'),
				'informasi_kepala_pimpinan' => $this->input->post('informasi_kepala_pimpinan'),
				'emp_code' => $this->input->post('emp_code'),
				'token' => $this->input->post('token'),
				'token_expired' => $this->input->post('token_expired'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'no_kk' => $this->input->post('no_kk'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'presensi_role' => $this->input->post('presensi_role'),
			];

			if (!is_dir(FCPATH . '/uploads/pimpinan_sd/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sd/');
			}

			if (!empty($pimpinan_sd_foto_profil_uuid)) {
				$pimpinan_sd_foto_profil_name_copy = date('YmdHis') . '-' . $pimpinan_sd_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sd_foto_profil_uuid . '/' . $pimpinan_sd_foto_profil_name, 
						FCPATH . 'uploads/pimpinan_sd/' . $pimpinan_sd_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pimpinan_sd_foto_profil_name_copy;
			}
		
			if (!empty($pimpinan_sd_slip_gaji_uuid)) {
				$pimpinan_sd_slip_gaji_name_copy = date('YmdHis') . '-' . $pimpinan_sd_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sd_slip_gaji_uuid . '/' . $pimpinan_sd_slip_gaji_name, 
						FCPATH . 'uploads/pimpinan_sd/' . $pimpinan_sd_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pimpinan_sd_slip_gaji_name_copy;
			}
		
			
			$save_pimpinan_sd = $this->model_pimpinan_sd->change($id, $save_data);

			if ($save_pimpinan_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pimpinan_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan_sd');
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
	* delete Pimpinan Sds
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pimpinan_sd_delete');

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
            set_message(cclang('has_been_deleted', 'pimpinan_sd'), 'success');
        } else {
            set_message(cclang('error_delete', 'pimpinan_sd'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pimpinan Sds
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pimpinan_sd_view');

		$this->data['pimpinan_sd'] = $this->model_pimpinan_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pimpinan Sd Detail');
		$this->render('backend/standart/administrator/pimpinan_sd/pimpinan_sd_view', $this->data);
	}
	
	/**
	* delete Pimpinan Sds
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pimpinan_sd = $this->model_pimpinan_sd->find($id);

		if (!empty($pimpinan_sd->foto_profil)) {
			$path = FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd->foto_profil;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($pimpinan_sd->slip_gaji)) {
			$path = FCPATH . '/uploads/pimpinan_sd/' . $pimpinan_sd->slip_gaji;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pimpinan_sd->remove($id);
	}
	
	/**
	* Upload Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function upload_foto_profil_file()
	{
		if (!$this->is_allowed('pimpinan_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pimpinan_sd',
		]);
	}

	/**
	* Delete Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function delete_foto_profil_file($uuid)
	{
		if (!$this->is_allowed('pimpinan_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'foto_profil', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'pimpinan_sd',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sd/'
        ]);
	}

	/**
	* Get Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function get_foto_profil_file($id)
	{
		if (!$this->is_allowed('pimpinan_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pimpinan_sd = $this->model_pimpinan_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_profil', 
            'table_name'        => 'pimpinan_sd',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sd/',
            'delete_endpoint'   => 'administrator/pimpinan_sd/delete_foto_profil_file'
        ]);
	}
	
	/**
	* Upload Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function upload_slip_gaji_file()
	{
		if (!$this->is_allowed('pimpinan_sd_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pimpinan_sd',
		]);
	}

	/**
	* Delete Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function delete_slip_gaji_file($uuid)
	{
		if (!$this->is_allowed('pimpinan_sd_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'slip_gaji', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'pimpinan_sd',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sd/'
        ]);
	}

	/**
	* Get Image Pimpinan Sd	* 
	* @return JSON
	*/
	public function get_slip_gaji_file($id)
	{
		if (!$this->is_allowed('pimpinan_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pimpinan_sd = $this->model_pimpinan_sd->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'slip_gaji', 
            'table_name'        => 'pimpinan_sd',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sd/',
            'delete_endpoint'   => 'administrator/pimpinan_sd/delete_slip_gaji_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pimpinan_sd_export');

		$this->model_pimpinan_sd->export('pimpinan_sd', 'pimpinan_sd');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pimpinan_sd_export');

		$this->model_pimpinan_sd->pdf('pimpinan_sd', 'pimpinan_sd');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pimpinan_sd_export');

		$table = $title = 'pimpinan_sd';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pimpinan_sd->find($id);
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


/* End of file pimpinan_sd.php */
/* Location: ./application/controllers/administrator/Pimpinan Sd.php */