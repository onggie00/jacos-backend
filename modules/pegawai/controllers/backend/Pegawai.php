<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define( 'CLIENT_ID', 'cb3ca268-e619-4b43-8aff-81538fa687c0');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '4aa8a802-af7f-41cf-b7f6-34e50fc432e4'); //backup active secret : bf68c70f-691c-4b2b-a3c6-0314d60d51a5
define( 'CLIENT_SECRET', ''); // [JACOS] TODO(manual): client secret Azure AD milik Jacos — secret LabSchool dihapus //April 2028
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All');


/**
*| --------------------------------------------------------------------------
*| Pegawai Controller
*| --------------------------------------------------------------------------
*| Pegawai site
*|
*/
class Pegawai extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_pegawai');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Pegawais
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('pegawai_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['pegawais'] = $this->model_pegawai->get($filter, $field, $this->limit_page, $offset,[],$sort,$sort_type);
		$this->data['pegawai_counts'] = $this->model_pegawai->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/pegawai/index/',
			'total_rows'   => $this->model_pegawai->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Pegawai List');
		$this->render('backend/standart/administrator/pegawai/pegawai_list', $this->data);
	}
	
	public function add_slip_gaji()
	{
		$this->is_allowed('pegawai_add');

		$this->template->title('Siswa Sd Tambah slip_gaji');
		$this->render('backend/standart/administrator/pegawai/pegawai_add_slip_gaji', $this->data);
	}

	public function add_slip_gaji_save()
	{
		if (!$this->is_allowed('pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$ids = $this->input->post('id_pegawai');
		$uuid_files = $this->input->post('slip_gaji_uuid');
		$name_files = $this->input->post('slip_gaji_name');

		// dd($this->input->post('id_siswa_sd'));

		foreach ($ids as $key => $item) {
			$slip_gaji_uuid = $uuid_files[$key];
			$slip_gaji_name = $name_files[$key];

			$save_data = [];

			if (!is_dir(FCPATH . '/uploads/pegawai/')) {
				mkdir(FCPATH . '/uploads/pegawai/');
			}

			if (!empty($slip_gaji_name)) {
				$slip_gaji_name_copy = date('YmdHis') . '-' . $slip_gaji_name;

				rename(
					FCPATH . 'uploads/tmp/' . $slip_gaji_uuid . '/' . $slip_gaji_name,
					FCPATH . 'uploads/pegawai/' . $slip_gaji_name_copy
				);

				if (!is_file(FCPATH . '/uploads/pegawai/' . $slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['slip_gaji'] = $slip_gaji_name_copy;
			}


			$save_pegawai = $this->model_pegawai->change($item, $save_data);
		}


		if ($save_pegawai) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_pegawai;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/pegawai', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/pegawai/edit/' . $save_pegawai, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/pegawai');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/pegawai');
			}
		}

		echo json_encode($this->data);
	}

	/**
	* Add new pegawais
	*
	*/
	public function add()
	{
		$this->is_allowed('pegawai_add');

		$this->template->title('Pegawai New');
		$this->render('backend/standart/administrator/pegawai/pegawai_add', $this->data);
	}

	/**
	* Add New Pegawais
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'Nuptk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'Npp', 'trim|max_length[255]');
		$this->form_validation->set_rules('kk', 'Kk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		$this->form_validation->set_rules('presensi_role', 'Presensi Role', 'required');
		

		if ($this->form_validation->run()) {
			$pegawai_foto_profil_uuid = $this->input->post('pegawai_foto_profil_uuid');
			$pegawai_foto_profil_name = $this->input->post('pegawai_foto_profil_name');
			$pegawai_slip_gaji_uuid = $this->input->post('pegawai_slip_gaji_uuid');
			$pegawai_slip_gaji_name = $this->input->post('pegawai_slip_gaji_name');
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'kk' => $this->input->post('kk'),
				'npwp' => $this->input->post('npwp'),
				'alamat' => $this->input->post('alamat'),
				'agama' => $this->input->post('agama'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'status_menikah' => $this->input->post('status_menikah'),
				'jumlah_anak' => $this->input->post('jumlah_anak'),
				'no_telp' => $this->input->post('no_telp'),
				'email' => $this->input->post('email'),
				'id_posisi' => $this->input->post('id_posisi'),
				'unit' => $this->input->post('unit'),
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

			if (!is_dir(FCPATH . '/uploads/pegawai/')) {
				mkdir(FCPATH . '/uploads/pegawai/');
			}

			if (!empty($pegawai_foto_profil_name)) {
				$pegawai_foto_profil_name_copy = date('YmdHis') . '-' . $pegawai_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pegawai_foto_profil_uuid . '/' . $pegawai_foto_profil_name, 
						FCPATH . 'uploads/pegawai/' . $pegawai_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pegawai/' . $pegawai_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pegawai_foto_profil_name_copy;
			}
		
			if (!empty($pegawai_slip_gaji_name)) {
				$pegawai_slip_gaji_name_copy = date('YmdHis') . '-' . $pegawai_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pegawai_slip_gaji_uuid . '/' . $pegawai_slip_gaji_name, 
						FCPATH . 'uploads/pegawai/' . $pegawai_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pegawai/' . $pegawai_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pegawai_slip_gaji_name_copy;
			}
		
			
			$save_pegawai = $this->model_pegawai->store($save_data);
            

			if ($save_pegawai) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_pegawai;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/pegawai/edit/' . $save_pegawai, 'Edit Pegawai'),
						anchor('administrator/pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/pegawai/edit/' . $save_pegawai, 'Edit Pegawai')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai');
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
	* Update view Pegawais
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('pegawai_update');

		$this->data['pegawai'] = $this->model_pegawai->find($id);

		$this->template->title('Pegawai Update');
		$this->render('backend/standart/administrator/pegawai/pegawai_update', $this->data);
	}

	/**
	* Update Pegawais
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[255]');
		$this->form_validation->set_rules('nuptk', 'Nuptk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npp', 'Npp', 'trim|max_length[255]');
		$this->form_validation->set_rules('kk', 'Kk', 'trim|max_length[255]');
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|max_length[255]');
		$this->form_validation->set_rules('token', 'Token', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim|max_length[50]');
		$this->form_validation->set_rules('presensi_role', 'Presensi Role', 'trim|max_length[50]');
		
		if ($this->form_validation->run()) {
			$pegawai_foto_profil_uuid = $this->input->post('pegawai_foto_profil_uuid');
			$pegawai_foto_profil_name = $this->input->post('pegawai_foto_profil_name');
			$pegawai_slip_gaji_uuid = $this->input->post('pegawai_slip_gaji_uuid');
			$pegawai_slip_gaji_name = $this->input->post('pegawai_slip_gaji_name');
		
			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nik' => $this->input->post('nik'),
				'nuptk' => $this->input->post('nuptk'),
				'npp' => $this->input->post('npp'),
				'kk' => $this->input->post('kk'),
				'npwp' => $this->input->post('npwp'),
				'alamat' => $this->input->post('alamat'),
				'agama' => $this->input->post('agama'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'status_menikah' => $this->input->post('status_menikah'),
				'jumlah_anak' => $this->input->post('jumlah_anak'),
				'no_telp' => $this->input->post('no_telp'),
				'email' => $this->input->post('email'),
				'id_posisi' => $this->input->post('id_posisi'),
				'unit' => $this->input->post('unit'),
				'status_kepegawaian' => $this->input->post('status_kepegawaian'),
				'informasi_kepala_pimpinan' => $this->input->post('informasi_kepala_pimpinan'),
				'emp_code' => $this->input->post('emp_code'),
				'token' => $this->input->post('token'),
				'token_expired' => $this->input->post('token_expired'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'no_kk' => $this->input->post('no_kk'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
			];

			if (!is_dir(FCPATH . '/uploads/pegawai/')) {
				mkdir(FCPATH . '/uploads/pegawai/');
			}

			if (!empty($pegawai_foto_profil_uuid)) {
				$pegawai_foto_profil_name_copy = date('YmdHis') . '-' . $pegawai_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $pegawai_foto_profil_uuid . '/' . $pegawai_foto_profil_name, 
						FCPATH . 'uploads/pegawai/' . $pegawai_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/pegawai/' . $pegawai_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $pegawai_foto_profil_name_copy;
			}
		
			if (!empty($pegawai_slip_gaji_uuid)) {
				$pegawai_slip_gaji_name_copy = date('YmdHis') . '-' . $pegawai_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $pegawai_slip_gaji_uuid . '/' . $pegawai_slip_gaji_name, 
						FCPATH . 'uploads/pegawai/' . $pegawai_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/pegawai/' . $pegawai_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $pegawai_slip_gaji_name_copy;
			}
		
			$get_data = $this->mymodel->withquery("select * from pegawai where id_pegawai = ?","row",[$id]);
			$save_pegawai = $this->model_pegawai->change($id, $save_data);

			if ($save_pegawai) {
				//cek apakah npp berubah
				if ($get_data->npp != $this->input->post('npp')) {
					$this->mymodel->update('pegawai_slip', ['npp' => $this->input->post('npp')], ['npp' => $get_data->npp]);
					$this->mymodel->update('presensi_office', ['npp' => $this->input->post('npp')], ['npp' => $get_data->npp]);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/pegawai', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/pegawai');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/pegawai');
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
	* delete Pegawais
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('pegawai_delete');

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
            set_message(cclang('has_been_deleted', 'pegawai'), 'success');
        } else {
            set_message(cclang('error_delete', 'pegawai'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Pegawais
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('pegawai_view');

		$this->data['pegawai'] = $this->model_pegawai->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Pegawai Detail');
		$this->render('backend/standart/administrator/pegawai/pegawai_view', $this->data);
	}
	
	/**
	* delete Pegawais
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$pegawai = $this->model_pegawai->find($id);

		if (!empty($pegawai->foto_profil)) {
			$path = FCPATH . '/uploads/pegawai/' . $pegawai->foto_profil;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($pegawai->slip_gaji)) {
			$path = FCPATH . '/uploads/pegawai/' . $pegawai->slip_gaji;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_pegawai->remove($id);
	}
	
	/**
	* Upload Image Pegawai	* 
	* @return JSON
	*/
	public function upload_foto_profil_file()
	{
		if (!$this->is_allowed('pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pegawai',
		]);
	}

	/**
	* Delete Image Pegawai	* 
	* @return JSON
	*/
	public function delete_foto_profil_file($uuid)
	{
		if (!$this->is_allowed('pegawai_delete', false)) {
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
            'table_name'        => 'pegawai',
            'primary_key'       => 'id_pegawai',
            'upload_path'       => 'uploads/pegawai/'
        ]);
	}

	/**
	* Get Image Pegawai	* 
	* @return JSON
	*/
	public function get_foto_profil_file($id)
	{
		if (!$this->is_allowed('pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pegawai = $this->model_pegawai->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_profil', 
            'table_name'        => 'pegawai',
            'primary_key'       => 'id_pegawai',
            'upload_path'       => 'uploads/pegawai/',
            'delete_endpoint'   => 'administrator/pegawai/delete_foto_profil_file'
        ]);
	}
	
	/**
	* Upload Image Pegawai	* 
	* @return JSON
	*/
	public function upload_slip_gaji_file()
	{
		if (!$this->is_allowed('pegawai_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'pegawai',
		]);
	}

	/**
	* Delete Image Pegawai	* 
	* @return JSON
	*/
	public function delete_slip_gaji_file($uuid)
	{
		if (!$this->is_allowed('pegawai_delete', false)) {
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
            'table_name'        => 'pegawai',
            'primary_key'       => 'id_pegawai',
            'upload_path'       => 'uploads/pegawai/'
        ]);
	}

	/**
	* Get Image Pegawai	* 
	* @return JSON
	*/
	public function get_slip_gaji_file($id)
	{
		if (!$this->is_allowed('pegawai_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$pegawai = $this->model_pegawai->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'slip_gaji', 
            'table_name'        => 'pegawai',
            'primary_key'       => 'id_pegawai',
            'upload_path'       => 'uploads/pegawai/',
            'delete_endpoint'   => 'administrator/pegawai/delete_slip_gaji_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('pegawai_export');

		$this->model_pegawai->export('pegawai', 'pegawai');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('pegawai_export');

		$this->model_pegawai->pdf('pegawai', 'pegawai');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('pegawai_export');

		$table = $title = 'pegawai';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_pegawai->find($id);
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

	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_pegawai"]["name"]));
		$this->db->trans_begin();

		if (isset($_FILES["file_pegawai"]["name"])) {
			$path = $_FILES["file_pegawai"]["tmp_name"];
			$p=$_FILES["file_pegawai"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' && $ext!='xls'){
				$this->load->library("session");
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				$totalAll=PHPExcel_Cell::columnIndexFromString($highestColumn);

				// if($totalAll!=22){
				// 	$this->db->trans_rollback();
				// 	$this->load->library("session");
				// 	$this->session->set_flashdata('error', 'Jumlah kolom tidak sesuai');
				// 	redirect($_SERVER['HTTP_REFERER']);
				// }

				for ($row = 2; $row <= $highestRow; $row++) {

					$data_pendaftaran = array(
						"nama_lengkap" => ucwords($worksheet->getCellByColumnAndRow(0, $row)->getValue()),
						"emp_code" => (int)$worksheet->getCellByColumnAndRow(1, $row)->getValue(),
						"email_ms_office" => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
						"no_telp" => (int)$worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"nuptk" => $worksheet->getCellByColumnAndRow(4, $row)->getValue(),
						"npp" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
						"nik" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
						"agama" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
						"jenis_kelamin" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
						"jumlah_anak" => (int)$worksheet->getCellByColumnAndRow(9, $row)->getValue(),
						"status_menikah" => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
						"unit" => $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
						"alamat" => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
						"status_kepegawaian" => $worksheet->getCellByColumnAndRow(13, $row)->getValue(),
						"informasi_kepala_pimpinan" => $worksheet->getCellByColumnAndRow(14, $row)->getValue(),

					);
					/* print_r($data_pendaftaran);
					echo "<br/><br/>" */
															//check null
					foreach ($data_pendaftaran as $key => $item) {
						if(!empty($item->email_ms_office)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong (Email)");
							//print_r($data_pendaftaran);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
					
					$pegawai=$this->mymodel->withquery("select * from pegawai where email_ms_office = '".$worksheet->getCellByColumnAndRow(2, $row)->getValue()."'", 'row');
					if($pegawai){
						$insertId = $this->mymodel->update("pegawai", $data_pendaftaran, 'id_pegawai', $pegawai->id_pegawai);
						$insertId = $pegawai->id_pegawai;
					}else{
						$insertId = $this->mymodel->insertid("pegawai", $data_pendaftaran);
					}
					// dd($this->db->last_query());
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', 'Import pegawai gagal atas nama '.$data_pendaftaran["nama_lengkap"]);
						redirect($_SERVER['HTTP_REFERER']);
					}

				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import pegawai berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function upload_slip_gaji(){
		$data=array();
        $total_file = count($_FILES['file_slip_gaji']['name']);
        $data_file = array();
        for ($i=0; $i < $total_file; $i++) {
            if (!empty($_FILES['file_slip_gaji']['name'][$i])) {
                  $trans_folder = './uploads/pegawai/'; //path htrans
                  if(!is_dir($trans_folder)){
                      mkdir($trans_folder, 0777);
                  }
                  $uploaddir = $trans_folder;
                  $img = explode('.', $_FILES['file_slip_gaji']['name'][$i]);
                  $extension = end($img);
                  $filename_original = "";
                  for ($j=0; $j < count($img)-1; $j++) {
                  	if ($filename_original == "") {
                  		$filename_original .= $img[$j];
                  	}
                  	else{
                  		$filename_original .= ".".$img[$j];
                  	}
                  }
                  $filename_no_extension = $filename_original;//md5(date('y-m-d h:i:s').$_FILES['file_slip_gaji']['name'][$i]);
                  $file_name =  $filename_no_extension.".".$extension;
                  $uploadfile = $uploaddir.$file_name;
                  if (move_uploaded_file($_FILES['file_slip_gaji']['tmp_name'][$i], $uploadfile)) {
                    if($extension == "pdf"){
                      $data = array(
                        "slip_gaji" => $file_name
                      );
                    }
                    $up = $this->mymodel->update("pegawai",$data, "npp", $filename_original);
                    $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                    $filenya = array(
                      "file_upload" => base_url("uploads/pegawai/").$filename_no_extension.".pdf",
                      "nama_asli" => $_FILES['file_slip_gaji']['name'][$i],
                      "ukuran" => $_FILES['file_slip_gaji']['size'][$i]
                    );
                    array_push($data_file, $filenya);
                    $file_terupload++;
                  }
                  else{
                    $msg = array('success'=>0,'message'=>'Upload File Gagal');
                  }
                }
              }
       redirect($_SERVER['HTTP_REFERER']);
	}

	public function update_password(){
		//refresh token first
		$this->refresh_token();
		$access_token = $this->session->userdata('access_token');
		$user_microsoft_id = $_POST['user_microsoft_id'];
		$password = $_POST['password'];
		$mail = $_POST['user_microsoft_mail'];
		//perlu forceChangePasswordNextSignIn untuk reset password
		$post_params = array(
			'passwordProfile' => array(
				'forceChangePasswordNextSignIn' => false,
				'password' => $password
			),
		);
		//request reset password Microsoft Account
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token, 'Content-type: application/json'));
		curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/users/".$user_microsoft_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_params));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		$rez = json_decode(curl_exec($ch), 1);
		var_dump($rez);
		curl_close($ch);
		
		$this->session->set_flashdata('success', 'Password berhasil diubah <b>'.$password.'</b>');
		redirect($_SERVER['HTTP_REFERER']);
	}

	public function reset_password(){
		//refresh token first
		$this->refresh_token();
		$access_token = $this->session->userdata('access_token');
		$user_microsoft_id = $_GET['user_microsoft_id'];
		$password = "Labschool123456";
		//request reset password Microsoft Account
		//perlu forceChangePasswordNextSignIn untuk reset password
		$post_params = array(
			'passwordProfile' => array(
				'forceChangePasswordNextSignIn' => true,
				'password' => $password
			),
		);
		//request reset password Microsoft Account
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token, 'Content-type: application/json'));
		curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/users/".$user_microsoft_id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_params));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		$rez = json_decode(curl_exec($ch), 1);
		// var_dump($rez);
		curl_close($ch);
		
		$this->session->set_flashdata('success', 'Password berhasil diatur ulang (<b>'.$password.'</b>)');
		redirect($_SERVER['HTTP_REFERER']);
		
	}

	public function refresh_token(){
		$appid = CLIENT_ID;
	    $tennantid = TENANT_ID;
	    $refresh_token_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/token";
		$data_token = array(
			'client_id' => $appid,
			'grant_type' => 'refresh_token',
			'refresh_token' => $this->session->userdata('refresh_token'),
			'scope' => 'user.read openid profile offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All',
			'client_secret' => CLIENT_SECRET
		);
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_URL, $refresh_token_url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_token));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		$rez = json_decode(curl_exec($ch), 1);

		if (array_key_exists('error', $rez)) {
			var_dump($rez);
			var_dump($data_token);
			die();
		  } else {
			$newdata = array(
			  'access_token' => $rez['access_token'],
			  'refresh_token' => $rez['refresh_token'],
			);
			$this->session->set_userdata($newdata);
		  }
		  curl_close($ch);
	}
	
}


/* End of file pegawai.php */
/* Location: ./application/controllers/administrator/Pegawai.php */