<?php
defined('BASEPATH') OR exit('No direct script access allowed');
define( 'CLIENT_ID', 'cb3ca268-e619-4b43-8aff-81538fa687c0');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '4aa8a802-af7f-41cf-b7f6-34e50fc432e4'); //backup active secret : bf68c70f-691c-4b2b-a3c6-0314d60d51a5
define( 'CLIENT_SECRET', ''); //April 2028
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All');

/**
*| --------------------------------------------------------------------------
*| Guru Ft Controller
*| --------------------------------------------------------------------------
*| Guru Ft site
*|
*/
class Guru_ft extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_guru_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Guru Fts
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('guru_ft_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['guru_fts'] = $this->model_guru_ft->get($filter, $field, $this->limit_page, $offset,[],$sort,$sort_type);
		$this->data['guru_ft_counts'] = $this->model_guru_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/guru_ft/index/',
			'total_rows'   => $this->model_guru_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Guru Ft List');
		$this->render('backend/standart/administrator/guru_ft/guru_ft_list', $this->data);
	}
	
	public function add_slip_gaji()
	{
		$this->is_allowed('guru_ft_add');

		$this->template->title('Guru FT Slip Gaji');
		$this->render('backend/standart/administrator/guru_ft/guru_ft_add_slip_gaji', $this->data);
	}

	public function add_slip_gaji_save()
	{
		if (!$this->is_allowed('guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$ids = $this->input->post('id_guru');
		$uuid_files = $this->input->post('slip_gaji_uuid');
		$name_files = $this->input->post('slip_gaji_name');

		// dd($this->input->post('id_siswa_sd'));

		foreach ($ids as $key => $item) {
			$slip_gaji_uuid = $uuid_files[$key];
			$slip_gaji_name = $name_files[$key];

			$save_data = [];

			if (!is_dir(FCPATH . '/uploads/guru_ft/')) {
				mkdir(FCPATH . '/uploads/guru_ft/');
			}

			if (!empty($slip_gaji_name)) {
				$slip_gaji_name_copy = date('YmdHis') . '-' . $slip_gaji_name;

				rename(
					FCPATH . 'uploads/tmp/' . $slip_gaji_uuid . '/' . $slip_gaji_name,
					FCPATH . 'uploads/guru_ft/' . $slip_gaji_name_copy
				);

				if (!is_file(FCPATH . '/uploads/guru_ft/' . $slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['slip_gaji'] = $slip_gaji_name_copy;
			}


			$save_guru_ft = $this->model_guru_ft->change($item, $save_data);
		}


		if ($save_guru_ft) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_guru_ft;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/guru_ft', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/guru_ft/edit/' . $save_guru_ft, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/guru_ft');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/guru_ft');
			}
		}

		echo json_encode($this->data);
	}

	/**
	* Add new guru_fts
	*
	*/
	public function add()
	{
		$this->is_allowed('guru_ft_add');

		$this->template->title('Guru Ft New');
		$this->render('backend/standart/administrator/guru_ft/guru_ft_add', $this->data);
	}

	/**
	* Add New Guru Fts
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('guru_ft_add', false)) {
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
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'Emp Code', 'trim|required');
		$this->form_validation->set_rules('presensi_role', 'Presensi Role', 'required');
		$this->form_validation->set_rules('keterangan_jabatan', 'Keterangan Jabatan', 'trim|max_length[50]');
		$this->form_validation->set_rules('jam_ajar', 'Jam Ajar', 'trim|max_length[50]');
		$this->form_validation->set_rules('pendidikan_terakhir', 'Pendidikan Terakhir', 'trim|max_length[50]');
		$this->form_validation->set_rules('universitas', 'Universitas', 'trim|max_length[200]');
		$this->form_validation->set_rules('jurusan', 'Jurusan', 'trim|max_length[150]');
		$this->form_validation->set_rules('tahun_lulus', 'Tahun Lulus', 'trim|integer');

		if ($this->form_validation->run()) {
			$guru_ft_foto_profil_uuid = $this->input->post('guru_ft_foto_profil_uuid');
			$guru_ft_foto_profil_name = $this->input->post('guru_ft_foto_profil_name');
			$guru_ft_slip_gaji_uuid = $this->input->post('guru_ft_slip_gaji_uuid');
			$guru_ft_slip_gaji_name = $this->input->post('guru_ft_slip_gaji_name');

			$check=$this->mymodel->withquery("select * from guru_ft where emp_code = ".$this->input->post('emp_code'), 'row');
			if($check){
				$this->db->trans_rollback();
				$this->load->library("session");
				$this->session->set_flashdata('error', "Emp Code sudah digunakan!");
				redirect($_SERVER['HTTP_REFERER']);
			}

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
				'keterangan_jabatan' => $this->input->post('keterangan_jabatan'),
				'jam_ajar' => $this->input->post('jam_ajar'),
				'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
				'universitas' => $this->input->post('universitas'),
				'jurusan' => $this->input->post('jurusan'),
				'tahun_lulus' => $this->input->post('tahun_lulus'),
			];

			if (!is_dir(FCPATH . '/uploads/guru_ft/')) {
				mkdir(FCPATH . '/uploads/guru_ft/');
			}

			if (!empty($guru_ft_foto_profil_name)) {
				$guru_ft_foto_profil_name_copy = date('YmdHis') . '-' . $guru_ft_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $guru_ft_foto_profil_uuid . '/' . $guru_ft_foto_profil_name, 
						FCPATH . 'uploads/guru_ft/' . $guru_ft_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/guru_ft/' . $guru_ft_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $guru_ft_foto_profil_name_copy;
			}
		
			if (!empty($guru_ft_slip_gaji_name)) {
				$guru_ft_slip_gaji_name_copy = date('YmdHis') . '-' . $guru_ft_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $guru_ft_slip_gaji_uuid . '/' . $guru_ft_slip_gaji_name, 
						FCPATH . 'uploads/guru_ft/' . $guru_ft_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/guru_ft/' . $guru_ft_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $guru_ft_slip_gaji_name_copy;
			}
		
			
			$save_guru_ft = $this->model_guru_ft->store($save_data);
            

			if ($save_guru_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_guru_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/guru_ft/edit/' . $save_guru_ft, 'Edit Guru Ft'),
						anchor('administrator/guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/guru_ft/edit/' . $save_guru_ft, 'Edit Guru Ft')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_ft');
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
	* Update view Guru Fts
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('guru_ft_update');

		$this->data['guru_ft'] = $this->model_guru_ft->find($id);

		$this->template->title('Guru Ft Update');
		$this->render('backend/standart/administrator/guru_ft/guru_ft_update', $this->data);
	}

	/**
	* Update Guru Fts
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('guru_ft_update', false)) {
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
		$this->form_validation->set_rules('npwp', 'Npwp', 'trim|max_length[255]');
		$this->form_validation->set_rules('jumlah_anak', 'Jumlah Anak', 'trim|max_length[11]');
		$this->form_validation->set_rules('no_telp', 'No Telp', 'trim|max_length[25]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_posisi', 'Id Posisi', 'trim|max_length[11]');
		$this->form_validation->set_rules('unit', 'Unit', 'trim|max_length[255]');
		$this->form_validation->set_rules('id_mapel', 'Id Mapel', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_kepegawaian', 'Status Kepegawaian', 'trim|max_length[255]');
		$this->form_validation->set_rules('informasi_kepala_pimpinan', 'Informasi Kepala Pimpinan', 'trim|max_length[255]');
		$this->form_validation->set_rules('emp_code', 'emp code', 'trim|required');
		$this->form_validation->set_rules('presensi_role', 'Presensi role', 'required');
		$this->form_validation->set_rules('keterangan_jabatan', 'Keterangan Jabatan', 'trim|max_length[50]');
		$this->form_validation->set_rules('jam_ajar', 'Jam Ajar', 'trim|max_length[50]');
		$this->form_validation->set_rules('pendidikan_terakhir', 'Pendidikan Terakhir', 'trim|max_length[50]');
		$this->form_validation->set_rules('universitas', 'Universitas', 'trim|max_length[200]');
		$this->form_validation->set_rules('jurusan', 'Jurusan', 'trim|max_length[150]');
		$this->form_validation->set_rules('tahun_lulus', 'Tahun Lulus', 'trim|integer');
		
		if ($this->form_validation->run()) {
			$guru_ft_foto_profil_uuid = $this->input->post('guru_ft_foto_profil_uuid');
			$guru_ft_foto_profil_name = $this->input->post('guru_ft_foto_profil_name');
			$guru_ft_slip_gaji_uuid = $this->input->post('guru_ft_slip_gaji_uuid');
			$guru_ft_slip_gaji_name = $this->input->post('guru_ft_slip_gaji_name');
		
			$check=$this->mymodel->withquery("select * from guru_ft where id_guru != $id and emp_code = ".$this->input->post('emp_code'), 'row');
			if($check){
				$this->db->trans_rollback();
				$this->load->library("session");
				$this->session->set_flashdata('error', "Emp Code sudah digunakan!");
				redirect($_SERVER['HTTP_REFERER']);
			}

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
				'keterangan_jabatan' => $this->input->post('keterangan_jabatan'),
				'jam_ajar' => $this->input->post('jam_ajar'),
				'pendidikan_terakhir' => $this->input->post('pendidikan_terakhir'),
				'universitas' => $this->input->post('universitas'),
				'jurusan' => $this->input->post('jurusan'),
				'tahun_lulus' => $this->input->post('tahun_lulus'),
			];

			if (!is_dir(FCPATH . '/uploads/guru_ft/')) {
				mkdir(FCPATH . '/uploads/guru_ft/');
			}

			if (!empty($guru_ft_foto_profil_uuid)) {
				$guru_ft_foto_profil_name_copy = date('YmdHis') . '-' . $guru_ft_foto_profil_name;

				rename(FCPATH . 'uploads/tmp/' . $guru_ft_foto_profil_uuid . '/' . $guru_ft_foto_profil_name, 
						FCPATH . 'uploads/guru_ft/' . $guru_ft_foto_profil_name_copy);

				if (!is_file(FCPATH . '/uploads/guru_ft/' . $guru_ft_foto_profil_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_profil'] = $guru_ft_foto_profil_name_copy;
			}
		
			if (!empty($guru_ft_slip_gaji_uuid)) {
				$guru_ft_slip_gaji_name_copy = date('YmdHis') . '-' . $guru_ft_slip_gaji_name;

				rename(FCPATH . 'uploads/tmp/' . $guru_ft_slip_gaji_uuid . '/' . $guru_ft_slip_gaji_name, 
						FCPATH . 'uploads/guru_ft/' . $guru_ft_slip_gaji_name_copy);

				if (!is_file(FCPATH . '/uploads/guru_ft/' . $guru_ft_slip_gaji_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['slip_gaji'] = $guru_ft_slip_gaji_name_copy;
			}
		
			$get_data = $this->mymodel->withquery("select * from guru_ft where id_guru = ?", "row", [$id]);
			$save_guru_ft = $this->model_guru_ft->change($id, $save_data);

			if ($save_guru_ft) {
				//cek apakah npp berubah
				if ($get_data->npp != $this->input->post('npp')) {
					$this->mymodel->update('pegawai_slip', ['npp' => $this->input->post('npp')], ['npp' => $get_data->npp]);
					$this->mymodel->update('presensi_office', ['npp' => $this->input->post('npp')], ['npp' => $get_data->npp]);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/guru_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/guru_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/guru_ft');
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
	* delete Guru Fts
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('guru_ft_delete');

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
            set_message(cclang('has_been_deleted', 'guru_ft'), 'success');
        } else {
            set_message(cclang('error_delete', 'guru_ft'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Guru Fts
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('guru_ft_view');

		$this->data['guru_ft'] = $this->model_guru_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Guru Ft Detail');
		$this->render('backend/standart/administrator/guru_ft/guru_ft_view', $this->data);
	}
	
	/**
	* delete Guru Fts
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$guru_ft = $this->model_guru_ft->find($id);

		if (!empty($guru_ft->foto_profil)) {
			$path = FCPATH . '/uploads/guru_ft/' . $guru_ft->foto_profil;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($guru_ft->slip_gaji)) {
			$path = FCPATH . '/uploads/guru_ft/' . $guru_ft->slip_gaji;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_guru_ft->remove($id);
	}
	
	/**
	* Upload Image Guru Ft	* 
	* @return JSON
	*/
	public function upload_foto_profil_file()
	{
		if (!$this->is_allowed('guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'guru_ft',
		]);
	}

	/**
	* Delete Image Guru Ft	* 
	* @return JSON
	*/
	public function delete_foto_profil_file($uuid)
	{
		if (!$this->is_allowed('guru_ft_delete', false)) {
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
            'table_name'        => 'guru_ft',
            'primary_key'       => 'id_guru',
            'upload_path'       => 'uploads/guru_ft/'
        ]);
	}

	/**
	* Get Image Guru Ft	* 
	* @return JSON
	*/
	public function get_foto_profil_file($id)
	{
		if (!$this->is_allowed('guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$guru_ft = $this->model_guru_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_profil', 
            'table_name'        => 'guru_ft',
            'primary_key'       => 'id_guru',
            'upload_path'       => 'uploads/guru_ft/',
            'delete_endpoint'   => 'administrator/guru_ft/delete_foto_profil_file'
        ]);
	}
	
	/**
	* Upload Image Guru Ft	* 
	* @return JSON
	*/
	public function upload_slip_gaji_file()
	{
		if (!$this->is_allowed('guru_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'guru_ft',
		]);
	}

	/**
	* Delete Image Guru Ft	* 
	* @return JSON
	*/
	public function delete_slip_gaji_file($uuid)
	{
		if (!$this->is_allowed('guru_ft_delete', false)) {
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
            'table_name'        => 'guru_ft',
            'primary_key'       => 'id_guru',
            'upload_path'       => 'uploads/guru_ft/'
        ]);
	}

	/**
	* Get Image Guru Ft	* 
	* @return JSON
	*/
	public function get_slip_gaji_file($id)
	{
		if (!$this->is_allowed('guru_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$guru_ft = $this->model_guru_ft->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'slip_gaji', 
            'table_name'        => 'guru_ft',
            'primary_key'       => 'id_guru',
            'upload_path'       => 'uploads/guru_ft/',
            'delete_endpoint'   => 'administrator/guru_ft/delete_slip_gaji_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('guru_ft_export');

		$this->model_guru_ft->export('guru_ft', 'guru_ft');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('guru_ft_export');

		$this->model_guru_ft->pdf('guru_ft', 'guru_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('guru_ft_export');

		$table = $title = 'guru_ft';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_guru_ft->find($id);
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
		// dd(isset($_FILES["file_guru"]["name"]));
		$this->db->trans_begin();

		if (isset($_FILES["file_guru"]["name"])) {
			$path = $_FILES["file_guru"]["tmp_name"];
			$p=$_FILES["file_guru"]["name"];
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
					// check emp code
					$check=$this->mymodel->withquery("select * from guru_ft where emp_code = ".(int)$worksheet->getCellByColumnAndRow(1, $row)->getValue(), 'row');
					/* if($check){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Emp Code $check->emp_code sudah digunakan!");
						redirect($_SERVER['HTTP_REFERER']);
					} */
					
						if (!empty($worksheet->getCellByColumnAndRow(0, $row)->getValue())) {
							$data_pendaftaran["nama_lengkap"] = ucwords($worksheet->getCellByColumnAndRow(0, $row)->getValue());
						}
						if (!empty($worksheet->getCellByColumnAndRow(1, $row)->getValue())) {
							$data_pendaftaran["emp_code"] = (int)$worksheet->getCellByColumnAndRow(1, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(2, $row)->getValue())) {
							$data_pendaftaran["email_ms_office"] = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(3, $row)->getValue())) {
							$data_pendaftaran["id_mapel"] = (int)$worksheet->getCellByColumnAndRow(3, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(4, $row)->getValue())) {
							$data_pendaftaran["nuptk"] = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(5, $row)->getValue())) {
							$data_pendaftaran["npp"] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(6, $row)->getValue())) {
							$data_pendaftaran["nik"] = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(7, $row)->getValue())) {
							$data_pendaftaran["agama"] = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(8, $row)->getValue())) {
							$data_pendaftaran["jenis_kelamin"] = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(9, $row)->getValue())) {
							$data_pendaftaran["jumlah_anak"] = (int)$worksheet->getCellByColumnAndRow(9, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(10, $row)->getValue())) {
							$data_pendaftaran["status_menikah"] = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(11, $row)->getValue())) {
							$data_pendaftaran["unit"] = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(12, $row)->getValue())) {
							$data_pendaftaran["alamat"] = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(13, $row)->getValue())) {
							$data_pendaftaran["status_kepegawaian"] = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
						}
						if (!empty($worksheet->getCellByColumnAndRow(14, $row)->getValue())) {
							$data_pendaftaran["informasi_kepala_pimpinan"] = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
						}
					if (!empty($worksheet->getCellByColumnAndRow(15, $row)->getValue())) {
						$data_pendaftaran["keterangan_jabatan"] = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
					}
					if (!empty($worksheet->getCellByColumnAndRow(16, $row)->getValue())) {
						$data_pendaftaran["jam_ajar"] = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
					}
					if (!empty($worksheet->getCellByColumnAndRow(17, $row)->getValue())) {
						$data_pendaftaran["pendidikan_terakhir"] = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
					}
					if (!empty($worksheet->getCellByColumnAndRow(18, $row)->getValue())) {
						$data_pendaftaran["universitas"] = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
					}
					if (!empty($worksheet->getCellByColumnAndRow(19, $row)->getValue())) {
						$data_pendaftaran["jurusan"] = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
					}
					if (!empty($worksheet->getCellByColumnAndRow(20, $row)->getValue())) {
						$data_pendaftaran["tahun_lulus"] = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
					}
				
						//check null
					/* foreach ($data_pendaftaran as $key => $item) {
						if(!empty($item->email_ms_office)){
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong");
							redirect($_SERVER['HTTP_REFERER']);
						}
					} */

					$check=$this->mymodel->withquery("select * from guru_ft where email_ms_office = '".$worksheet->getCellByColumnAndRow(2, $row)->getValue()."'", 'row');
					if($check){
						$insertId = $this->mymodel->update("guru_ft", $data_pendaftaran, 'email_ms_office', $worksheet->getCellByColumnAndRow(2, $row)->getValue());
						$insertId = $check->id_guru;
					}else{
						$insertId = $this->mymodel->insertid("guru_ft", $data_pendaftaran);
					}

					// dd($this->db->last_query());
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', 'Import guru gagal atas nama '.$data_pendaftaran["nama_lengkap"]);
						redirect($_SERVER['HTTP_REFERER']);
					}

				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import guru berhasil');
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
                  $trans_folder = './uploads/guru_ft/'; //path htrans
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
                    $up = $this->mymodel->update("guru_ft",$data, "npp", $filename_original);
                    $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                    $filenya = array(
                      "file_upload" => base_url("uploads/guru_ft/").$filename_no_extension.".pdf",
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


/* End of file guru_ft.php */
/* Location: ./application/controllers/administrator/Guru Ft.php */