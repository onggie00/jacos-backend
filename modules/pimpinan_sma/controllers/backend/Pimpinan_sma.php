<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Pimpinan Sma Controller
*| --------------------------------------------------------------------------
*| Pimpinan Sma site
*|
*/
class Pimpinan_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pimpinan_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pimpinan Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pimpinan_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['pimpinan_smas'] = $this->model_pimpinan_sma->get($filter, $field, $this->limit_page, $offset);
		$this->data['pimpinan_sma_counts'] = $this->model_pimpinan_sma->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pimpinan_sma/index/',
			'total_rows'   => $this->model_pimpinan_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pimpinan Sma List');
		$this->render('backend/standart/administrator/pimpinan_sma/pimpinan_sma_list', $this->data);
	}
	
		
	public function add_slip_gaji()
	{
		$this->is_allowed('pimpinan_sma_add');

		$this->template->title('Pimpinan SMA Slip Gaji');
		$this->render('backend/standart/administrator/pimpinan_sma/pimpinan_sma_add_slip_gaji', $this->data);
	}

	public function add_slip_gaji_save()
	{
		if (!$this->is_allowed('pimpinan_sma_add', false)) {
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

			if (!is_dir(FCPATH . '/uploads/pimpinan_sma/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sma/');
			}

			if (!empty($slip_gaji_name)) {
				$slip_gaji_name_copy = date('YmdHis') . '-' . $slip_gaji_name;

				rename(
					FCPATH . 'uploads/tmp/' . $slip_gaji_uuid . '/' . $slip_gaji_name,
					FCPATH . 'uploads/pimpinan_sma/' . $slip_gaji_name_copy
				);

				if (!is_file(FCPATH . '/uploads/pimpinan_sma/' . $slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['slip_gaji'] = $slip_gaji_name_copy;
			}


			$save_pimpinan_sma = $this->model_pimpinan_sma->change($item, $save_data);
		}


		if ($save_pimpinan_sma) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_pimpinan_sma;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/pimpinan_sma', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/pimpinan_sma/edit/' . $save_pimpinan_sma, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/pimpinan_sma');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/pimpinan_sma');
			}
		}

		echo json_encode($this->data);
	}

	/**
	* Add new pimpinan_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('pimpinan_sma_add');

		$this->template->title('Pimpinan Sma New');
		$this->render('backend/standart/administrator/pimpinan_sma/pimpinan_sma_add', $this->data);
	}

	/**
	* Add New Pimpinan Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pimpinan_sma_add', false)) {
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
			$pimpinan_sma_foto_profil_uuid = $this->input->post('pimpinan_sma_foto_profil_uuid');
			$pimpinan_sma_foto_profil_name = $this->input->post('pimpinan_sma_foto_profil_name');
			$pimpinan_sma_slip_gaji_uuid = $this->input->post('pimpinan_sma_slip_gaji_uuid');
			$pimpinan_sma_slip_gaji_name = $this->input->post('pimpinan_sma_slip_gaji_name');
		
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

			if (!is_dir(FCPATH . '/uploads/pimpinan_sma/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sma/');
			}

			if (!empty($pimpinan_sma_foto_profil_name)) {
				$pimpinan_sma_foto_profil_name_copy = date('YmdHis') . '-' . $pimpinan_sma_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sma_foto_profil_uuid . '/' . $pimpinan_sma_foto_profil_name, 
						FCPATH . 'uploads/pimpinan_sma/' . $pimpinan_sma_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pimpinan_sma_foto_profil_name_copy;
			}
		
			if (!empty($pimpinan_sma_slip_gaji_name)) {
				$pimpinan_sma_slip_gaji_name_copy = date('YmdHis') . '-' . $pimpinan_sma_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sma_slip_gaji_uuid . '/' . $pimpinan_sma_slip_gaji_name, 
						FCPATH . 'uploads/pimpinan_sma/' . $pimpinan_sma_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pimpinan_sma_slip_gaji_name_copy;
			}
		
			
			$save_pimpinan_sma = $this->model_pimpinan_sma->store($save_data);
            

			if ($save_pimpinan_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pimpinan_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pimpinan_sma/edit/' . $save_pimpinan_sma, 'Edit Pimpinan Sma'),
						anchor('administrator/pimpinan_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pimpinan_sma/edit/' . $save_pimpinan_sma, 'Edit Pimpinan Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan_sma');
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
	* Update view Pimpinan Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pimpinan_sma_update');

		$this->data['pimpinan_sma'] = $this->model_pimpinan_sma->find($id);

		$this->template->title('Pimpinan Sma Update');
		$this->render('backend/standart/administrator/pimpinan_sma/pimpinan_sma_update', $this->data);
	}

	/**
	* Update Pimpinan Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pimpinan_sma_update', false)) {
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
			$pimpinan_sma_foto_profil_uuid = $this->input->post('pimpinan_sma_foto_profil_uuid');
			$pimpinan_sma_foto_profil_name = $this->input->post('pimpinan_sma_foto_profil_name');
			$pimpinan_sma_slip_gaji_uuid = $this->input->post('pimpinan_sma_slip_gaji_uuid');
			$pimpinan_sma_slip_gaji_name = $this->input->post('pimpinan_sma_slip_gaji_name');
		
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

			if (!is_dir(FCPATH . '/uploads/pimpinan_sma/')) {
				mkdir(FCPATH . '/uploads/pimpinan_sma/');
			}

			if (!empty($pimpinan_sma_foto_profil_uuid)) {
				$pimpinan_sma_foto_profil_name_copy = date('YmdHis') . '-' . $pimpinan_sma_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sma_foto_profil_uuid . '/' . $pimpinan_sma_foto_profil_name, 
						FCPATH . 'uploads/pimpinan_sma/' . $pimpinan_sma_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pimpinan_sma_foto_profil_name_copy;
			}
		
			if (!empty($pimpinan_sma_slip_gaji_uuid)) {
				$pimpinan_sma_slip_gaji_name_copy = date('YmdHis') . '-' . $pimpinan_sma_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pimpinan_sma_slip_gaji_uuid . '/' . $pimpinan_sma_slip_gaji_name, 
						FCPATH . 'uploads/pimpinan_sma/' . $pimpinan_sma_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pimpinan_sma_slip_gaji_name_copy;
			}
		
			
			$save_pimpinan_sma = $this->model_pimpinan_sma->change($id, $save_data);

			if ($save_pimpinan_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pimpinan_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pimpinan_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pimpinan_sma');
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
	* delete Pimpinan Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pimpinan_sma_delete');

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
            set_message(cclang('has_been_deleted', 'pimpinan_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'pimpinan_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pimpinan Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pimpinan_sma_view');

		$this->data['pimpinan_sma'] = $this->model_pimpinan_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pimpinan Sma Detail');
		$this->render('backend/standart/administrator/pimpinan_sma/pimpinan_sma_view', $this->data);
	}
	
	/**
	* delete Pimpinan Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pimpinan_sma = $this->model_pimpinan_sma->find($id);

		if (!empty($pimpinan_sma->foto_profil)) {
			$path = FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma->foto_profil;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($pimpinan_sma->slip_gaji)) {
			$path = FCPATH . '/uploads/pimpinan_sma/' . $pimpinan_sma->slip_gaji;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pimpinan_sma->remove($id);
	}
	
	/**
	* Upload Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function upload_foto_profil_file()
	{
		if (!$this->is_allowed('pimpinan_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pimpinan_sma',
		]);
	}

	/**
	* Delete Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function delete_foto_profil_file($uuid)
	{
		if (!$this->is_allowed('pimpinan_sma_delete', false)) {
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
            'table_name'        => 'pimpinan_sma',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sma/'
        ]);
	}

	/**
	* Get Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function get_foto_profil_file($id)
	{
		if (!$this->is_allowed('pimpinan_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pimpinan_sma = $this->model_pimpinan_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_profil', 
            'table_name'        => 'pimpinan_sma',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sma/',
            'delete_endpoint'   => 'administrator/pimpinan_sma/delete_foto_profil_file'
        ]);
	}
	
	/**
	* Upload Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function upload_slip_gaji_file()
	{
		if (!$this->is_allowed('pimpinan_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pimpinan_sma',
		]);
	}

	/**
	* Delete Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function delete_slip_gaji_file($uuid)
	{
		if (!$this->is_allowed('pimpinan_sma_delete', false)) {
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
            'table_name'        => 'pimpinan_sma',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sma/'
        ]);
	}

	/**
	* Get Image Pimpinan Sma	* 
	* @return JSON
	*/
	public function get_slip_gaji_file($id)
	{
		if (!$this->is_allowed('pimpinan_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pimpinan_sma = $this->model_pimpinan_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'slip_gaji', 
            'table_name'        => 'pimpinan_sma',
            'primary_key'       => 'id_pimpinan',
            'upload_path'       => 'uploads/pimpinan_sma/',
            'delete_endpoint'   => 'administrator/pimpinan_sma/delete_slip_gaji_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pimpinan_sma_export');

		$this->model_pimpinan_sma->export('pimpinan_sma', 'pimpinan_sma');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pimpinan_sma_export');

		$this->model_pimpinan_sma->pdf('pimpinan_sma', 'pimpinan_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pimpinan_sma_export');

		$table = $title = 'pimpinan_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pimpinan_sma->find($id);
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


/* End of file pimpinan_sma.php */
/* Location: ./application/controllers/administrator/Pimpinan Sma.php */