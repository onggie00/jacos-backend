<?php
define( 'CLIENT_ID', 'cb3ca268-e619-4b43-8aff-81538fa687c0');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '4aa8a802-af7f-41cf-b7f6-34e50fc432e4'); //backup active secret : bf68c70f-691c-4b2b-a3c6-0314d60d51a5
define( 'CLIENT_SECRET', ''); // [JACOS] TODO(manual): client secret Azure AD milik Jacos — secret LabSchool dihapus //April 2028
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All');
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *| --------------------------------------------------------------------------
 *| Siswa KB Aktif Controller
 *| --------------------------------------------------------------------------
 *| Siswa KB Aktif site
 *|
 */
class Siswa_kb_aktif extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_kb_aktif');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Siswa KB Aktifs
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_kb_aktif_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		// Multi-filter support
		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['siswa_kb_aktifs'] = $this->model_siswa_kb_aktif->get(null, null, $this->limit_page, $offset, [], $sort, $sort_type, $multi_filters);
			$this->data['siswa_kb_aktif_counts'] = $this->model_siswa_kb_aktif->count_all(null, null, $multi_filters);
		} else {
			$this->data['siswa_kb_aktifs'] = $this->model_siswa_kb_aktif->get($filter, $field, $this->limit_page, $offset, [], $sort, $sort_type);
			$this->data['siswa_kb_aktif_counts'] = $this->model_siswa_kb_aktif->count_all($filter, $field);
		}

		$config = [
			'base_url'     => 'administrator/siswa_kb_aktif/index/',
			'total_rows'   => $has_multi_filter ? $this->model_siswa_kb_aktif->count_all(null, null, $multi_filters) : $this->model_siswa_kb_aktif->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Dropdown data for filter builder
		$this->data['list_kelas'] = $this->mymodel->withquery("SELECT id_kelas_kb, label FROM kelas_kb ORDER BY label", "result");
		$this->data['list_tahun_ajaran'] = $this->mymodel->withquery("SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY label DESC", "result");
		$this->data['multi_filters'] = $multi_filters;
		$this->data['export_lengkap_cols'] = $this->model_siswa_kb_aktif->export_lengkap_columns();

		$this->template->title('Siswa KB Aktif List');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_list', $this->data);
	}

	/**
	 * Parse multi-filter from GET params
	 */
	private function _parse_filters()
	{
		$fields    = $this->input->get('ff');
		$operators = $this->input->get('fo');
		$values    = $this->input->get('fv');

		if (!is_array($fields) || !is_array($operators) || !is_array($values)) {
			return [];
		}

		$filters = [];
		for ($i = 0; $i < count($fields); $i++) {
			if (!empty($fields[$i]) && !empty($values[$i])) {
				$filters[] = [
					'field'    => $fields[$i],
					'operator' => isset($operators[$i]) ? $operators[$i] : 'contains',
					'value'    => $values[$i],
				];
			}
		}
		return $filters;
	}

	/**
	 * Add new siswa_kb_aktifs
	 *
	 */
	public function add()
	{
		$this->is_allowed('siswa_kb_aktif_add');

		$this->template->title('Siswa KB Aktif New');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_add', $this->data);
	}

	public function add_raport()
	{
		$this->is_allowed('siswa_kb_aktif_add');

		$this->template->title('Siswa SD Tambah Raport');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_add_raport', $this->data);
	}

	public function add_raport_save()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$ids = $this->input->post('id_siswa_kb');
		$uuid_files = $this->input->post('file_raport_uuid');
		$name_files = $this->input->post('file_raport_name');

		// dd($this->input->post('id_siswa_kb'));

		foreach ($ids as $key => $item) {
			$file_raport_uuid = $uuid_files[$key];
			$file_raport_name = $name_files[$key];

			$save_data = [];

			if (!is_dir(FCPATH . '/uploads/siswa_kb_aktif/')) {
				mkdir(FCPATH . '/uploads/siswa_kb_aktif/');
			}

			if (!empty($file_raport_name)) {
				$file_raport_name_copy = date('YmdHis') . '-' . $file_raport_name;

				rename(
					FCPATH . 'uploads/tmp/' . $file_raport_uuid . '/' . $file_raport_name,
					FCPATH . 'uploads/siswa_kb_aktif/' . $file_raport_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_kb_aktif/' . $file_raport_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['file_raport'] = $file_raport_name_copy;
			}


			$save_siswa_kb_aktif = $this->model_siswa_kb_aktif->change($item, $save_data);
		}


		if ($save_siswa_kb_aktif) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_siswa_kb_aktif;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/siswa_kb_aktif', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/siswa_kb_aktif/edit/' . $save_siswa_kb_aktif, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/siswa_kb_aktif');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/siswa_kb_aktif');
			}
		}

		echo json_encode($this->data);
	}

	/**
	 * Upload Image Siswa Ft	* 
	 * @return JSON
	 */
	public function upload_file_raport_file()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_kb_aktif',
		]);
	}

	/**
	 * Delete Image Siswa Ft	* 
	 * @return JSON
	 */
	public function delete_file_raport_file($uuid)
	{
		if (!$this->is_allowed('siswa_kb_aktif_add', false)) {
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
			'table_name'        => 'siswa_kb_aktif',
			'primary_key'       => 'id_siswa_kb_aktif',
			'upload_path'       => 'uploads/siswa_kb_aktif/'
		]);
	}

	/**
	 * Add New Siswa KB Aktifs
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nis', 'Nis', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa_kb', 'Id Siswa KB', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required');


		if ($this->form_validation->run()) {

			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nis' => $this->input->post('nis'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_siswa_kb' => $this->input->post('id_siswa_kb'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'kewarganegaraan' => $this->input->post('kewarganegaraan'),
				'nik' => $this->input->post('nik'),
				'golongan_darah' => $this->input->post('golongan_darah'),
				'telp' => $this->input->post('telp'),
				'pendidikan_ayah' => $this->input->post('pendidikan_ayah'),
				'pendidikan_ibu' => $this->input->post('pendidikan_ibu'),
				'penghasilan_ayah' => $this->input->post('penghasilan_ayah'),
				'penghasilan_ibu' => $this->input->post('penghasilan_ibu'),
				'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah'),
				'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu'),
				'spp_custom' => $this->input->post('spp_custom'),
				'spp_type' => $this->input->post('spp_type'),
				'acc_ujian' => $this->input->post('acc_ujian'),
				'nomor_peserta_ujian' => $this->input->post('nomor_peserta_ujian'),
			];


			$save_siswa_kb_aktif = $this->model_siswa_kb_aktif->store($save_data);


			if ($save_siswa_kb_aktif) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_kb_aktif;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_kb_aktif/edit/' . $save_siswa_kb_aktif, 'Edit Siswa KB Aktif'),
						anchor('administrator/siswa_kb_aktif', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
							anchor('administrator/siswa_kb_aktif/edit/' . $save_siswa_kb_aktif, 'Edit Siswa KB Aktif')
						]),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_kb_aktif');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_kb_aktif');
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
	 * Update view Siswa KB Aktifs
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('siswa_kb_aktif_update');
		$this->model_siswa_kb_aktif->join_avaiable();
		$this->data['siswa_kb_aktif'] = $this->model_siswa_kb_aktif->find($id);

		$this->template->title('Siswa KB Aktif Update');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_update', $this->data);
	}

	/**
	 * Update Siswa KB Aktifs
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_kb_aktif_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nis', 'Nis', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa_kb', 'Id Siswa KB', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required');
		$this->form_validation->set_rules('spp_custom', 'SPP Khusus', 'trim');

		if ($this->form_validation->run()) {

			$save_data = [
				'nama_lengkap' => strtoupper($this->input->post('nama_lengkap')),
				'nis' => $this->input->post('nis'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_siswa_kb' => $this->input->post('id_siswa_kb'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'kewarganegaraan' => $this->input->post('kewarganegaraan'),
				'nik' => $this->input->post('nik'),
				'golongan_darah' => $this->input->post('golongan_darah'),
				'telp' => $this->input->post('telp'),
				'pendidikan_ayah' => $this->input->post('pendidikan_ayah'),
				'pendidikan_ibu' => $this->input->post('pendidikan_ibu'),
				'penghasilan_ayah' => $this->input->post('penghasilan_ayah'),
				'penghasilan_ibu' => $this->input->post('penghasilan_ibu'),
				'tgl_lahir_ayah' => $this->input->post('tgl_lahir_ayah'),
				'tgl_lahir_ibu' => $this->input->post('tgl_lahir_ibu'),
				'acc_ujian' => $this->input->post('acc_ujian'),
				'nomor_peserta_ujian' => $this->input->post('nomor_peserta_ujian'),
			];
			$spp_custom_raw = $this->input->post("spp_custom");
			if ($spp_custom_raw !== false) {
				$save_data["spp_custom"] = ($spp_custom_raw !== '') ? $spp_custom_raw : null;
			}
			$spp_type_raw = $this->input->post("spp_type");
			if ($spp_type_raw !== false) {
				$save_data["spp_type"] = ($spp_type_raw !== '') ? $spp_type_raw : 'FULL';
			}

			$this->model_siswa_kb_aktif->change($id, $save_data);

			// Backup: direct update spp_custom (MY_Model::change bisa miss kalau private property scope issue)
			if (isset($save_data['spp_custom'])) {
				$this->db->where('id_siswa_kb_aktif', $id)->update('siswa_kb_aktif', array('spp_custom' => $save_data['spp_custom']));
			}
			if (isset($save_data['spp_type'])) {
				$this->db->where('id_siswa_kb_aktif', $id)->update('siswa_kb_aktif', array('spp_type' => $save_data['spp_type']));
			}

			// --- Restore akun ---
			$restore_siswa = $this->input->post('restore_siswa');
			if ($restore_siswa === 'restore') {
				$this->mymodel->update('siswa_kb', array('deleted_at' => NULL), 'id_siswa_kb', $this->input->post('id_siswa_kb'));
				$this->mymodel->update('siswa_kb_aktif', array('deleted_at' => NULL), 'id_siswa_kb_aktif', $id);
			}
			$restore_ortu = $this->input->post('restore_ortu');
			if ($restore_ortu === 'restore') {
				$this->mymodel->update('siswa_kb', array('deleted_at_ortu' => NULL), 'id_siswa_kb', $this->input->post('id_siswa_kb'));
				$this->mymodel->update('siswa_kb_aktif', array('deleted_at_ortu' => NULL), 'id_siswa_kb_aktif', $id);
			}

			// --- Sync data siswa_kb ---
			$save_data_siswa = array(
				'nama_lengkap' => strtoupper($this->input->post('nama_lengkap')),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'email_ms_office_ortu' => $this->input->post('email_ms_office_ortu'),
			);
			$this->mymodel->update('siswa_kb', $save_data_siswa, 'id_siswa_kb', $this->input->post('id_siswa_kb'));

			// --- Sync SPP & transaksi_spp ---
			$get_tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran where id_tahun_ajaran = '".$this->input->post('id_tahun_ajaran')."'","row");
			$get_spp = $this->mymodel->withquery("select * from spp_sd where tahun_ajaran = '".$get_tahun_ajaran->label."' ","result");
			$nama_kelas = $this->mymodel->withquery(
				"SELECT label FROM kelas_kb WHERE id_kelas_kb = '".$this->input->post('id_kelas')."'",
				"row"
			)->label;
			$is_keluar = (stripos($nama_kelas, 'keluar') !== false);
			$is_alumni = (stripos($nama_kelas, 'alumni') !== false);
			$bulan = array(
				'januari','februari','maret','april','mei','juni',
				'juli','agustus','september','oktober','november','desember'
			);
			$spp_custom_post = $this->input->post('spp_custom');
			$spp_custom_changed = ($spp_custom_post !== false && $spp_custom_post !== '');

			foreach ($get_spp as $value) {
				$save_data_spp = array();
				if ($spp_custom_changed) {
					// Jangan ubah nominal jika ada kolom bulan yang sudah terisi (sudah ada pembayaran)
					$sudah_ada_bayar = false;
					foreach ($bulan as $bln) {
						if (isset($value->$bln) && $value->$bln !== NULL && $value->$bln !== '') {
							$sudah_ada_bayar = true;
							break;
						}
					}
					if (!$sudah_ada_bayar) {
						$save_data_spp['nominal'] = (int) $spp_custom_post;
					}
				}
				if ($is_keluar) {
					foreach ($bulan as $bln) {
						if ($value->$bln === NULL || $value->$bln === '') {
							$save_data_spp[$bln] = 'Keluar';
						}
					}
				}
				if ($is_alumni) {
					foreach ($bulan as $bln) {
						if ($value->$bln === NULL || $value->$bln === '') {
							$save_data_spp[$bln] = 'Alumni';
						}
					}
				}
				if (!empty($save_data_spp)) {
					$save_data_spp['kelas'] = $nama_kelas;
					$this->mymodel->update(
						'spp_sd',
						$save_data_spp,
						"tahun_ajaran = '".$value->tahun_ajaran."' AND id_siswa_aktif = ".$id
					);
				}
			}
			if ($spp_custom_changed) {
				$save_data_transaksi = array(
					'user_name' => strtoupper($this->input->post('nama_lengkap')),
					'total_biaya' => (int) $spp_custom_post,
				);
				$this->mymodel->update('transaksi_spp', $save_data_transaksi, "no_transaksi like '%sd%' and id_tahun_ajaran = '".$this->input->post('id_tahun_ajaran')."' and id_siswa_aktif=" . $id . " AND status_transaksi < 2");
			}

			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] = $id;
				$this->data['message'] = cclang('success_update_data_stay', array(
					anchor('administrator/siswa_kb_aktif', ' Go back to list')
				));
			} else {
				set_message(
					cclang('success_update_data_redirect', array()),
					'success'
				);
				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/siswa_kb_aktif');
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	 * delete Siswa KB Aktifs
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('siswa_kb_aktif_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'siswa_kb_aktif'), 'success');
		} else {
			set_message(cclang('error_delete', 'siswa_kb_aktif'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Siswa KB Aktifs
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('siswa_kb_aktif_view');

		$this->data['siswa_kb_aktif'] = $this->model_siswa_kb_aktif->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Siswa KB Aktif Detail');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_view', $this->data);
	}

	/**
	 * Get siswa detail via AJAX (JSON)
	 */
	public function get_detail($id)
	{
		if (!$this->is_allowed('siswa_kb_aktif_view', false)) {
			echo json_encode(['error' => 'Unauthorized']);
			exit;
		}

		$siswa = $this->model_siswa_kb_aktif->join_avaiable()->filter_avaiable()->find($id);

		if ($siswa) {
			echo json_encode($siswa);
		} else {
			echo json_encode(['error' => 'Data tidak ditemukan']);
		}
	}

	/**
	 * delete Siswa KB Aktifs
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$siswa_kb_aktif = $this->model_siswa_kb_aktif->find($id);



		return $this->model_siswa_kb_aktif->remove($id);
	}

	/**
	 * Restore akun siswa (set deleted_at = NULL)
	 */
	public function restore($id = null)
	{
		$this->is_allowed('siswa_kb_aktif_update');

		if (empty($id)) {
			set_message('ID tidak valid', 'error');
			redirect_back();
		}

		$siswa = $this->model_siswa_kb_aktif->find($id);
		if (!$siswa) {
			set_message('Data siswa tidak ditemukan', 'error');
			redirect_back();
		}

		// Set deleted_at = NULL di tabel siswa_kb
		$this->mymodel->update('siswa_kb', array('deleted_at' => NULL), 'id_siswa_kb', $siswa->id_siswa_kb);
		// Set deleted_at = NULL di tabel siswa_kb_aktif
		$this->mymodel->update('siswa_kb_aktif', array('deleted_at' => NULL), 'id_siswa_kb_aktif', $id);

		set_message('Akun siswa berhasil dipulihkan', 'success');
		redirect_back();
	}

	/**
	 * Restore akun ortu (set deleted_at_ortu = NULL)
	 */
	public function restore_ortu($id = null)
	{
		$this->is_allowed('siswa_kb_aktif_update');

		if (empty($id)) {
			set_message('ID tidak valid', 'error');
			redirect_back();
		}

		$siswa = $this->model_siswa_kb_aktif->find($id);
		if (!$siswa) {
			set_message('Data siswa tidak ditemukan', 'error');
			redirect_back();
		}

		// Set deleted_at_ortu = NULL di tabel siswa_kb
		$this->mymodel->update('siswa_kb', array('deleted_at_ortu' => NULL), 'id_siswa_kb', $siswa->id_siswa_kb);
		// Set deleted_at_ortu = NULL di tabel siswa_kb_aktif
		$this->mymodel->update('siswa_kb_aktif', array('deleted_at_ortu' => NULL), 'id_siswa_kb_aktif', $id);

		set_message('Akun ortu berhasil dipulihkan', 'success');
		redirect_back();
	}

	/**
	 * delete Prestasi Siswas
	 *
	 * @var $id String
	 */
	public function alumni($id = null)
	{
		$arr_id = $this->input->get('id');

		foreach ($arr_id as $id) {

			$this->mymodel->update('siswa_kb_aktif', array('is_active' => '0'), 'id_siswa_kb_aktif', $id);

			$data_alumni = [
				"id_siswa_aktif" => $id
			];

			$insertAlumni = $this->mymodel->insertid("alumni_sd", $data_alumni);
			// dd($insertAlumni);
			if ($insertAlumni == 0) {
				$this->load->library("session");
				$this->session->set_flashdata('error', 'Update alumni gagal');
				redirect($_SERVER['HTTP_REFERER']);
			}
		}

		set_message('Update alumni berhasil', 'success');

		redirect_back();
	}

	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('siswa_kb_aktif_export');

		//$this->model_siswa_kb_aktif->export_siswa('siswa_kb_aktif', 'siswa_kb_aktif');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		// Export columns: fields 1..N untuk import data, kolom terakhir = id_siswa_kb_aktif (primary key untuk import).
		// Kolom 0 = NO (nomor urut, tidak di-import).
		// Urutan fields 1..N-1 harus sama dengan urutan yang dibaca import_siswa().
		$field_search = array(
			'nama_lengkap', 'nis', 'kelas', 'tahun_ajaran',
			'nisn', 'email_ms_office', 'email_ms_office_ortu', 'notelp_ibu', 'notelp_ayah',
			'kewarganegaraan', 'nik', 'golongan_darah', 'telp', 'pendidikan_ayah',
			'pendidikan_ibu', 'penghasilan_ayah', 'penghasilan_ibu', 'tgl_lahir_ayah',
			'tgl_lahir_ibu', 'spp_type', 'acc_ujian', 'spp_custom', 'nomor_peserta_ujian',
			'device_id_siswa', 'device_id_ortu', 'spp_siswa', 'file_raport', 'tgl_lahir',
			'id_siswa_kb_aktif'
		);
		$case_spp = "case when sa.spp_custom = '0' then (select biaya_spp from tingkatan_sd ts where ts.id_tingkatan_sd = k.id_tingkatan) else sa.spp_custom end";
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_kb_aktif sa join kelas_kb k on sa.id_kelas = k.id_kelas_kb join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb","result");
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
			$get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_kb_aktif sa join kelas_kb k on sa.id_kelas = k.id_kelas_kb join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb where ".$where,"result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "sa.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "kelas" || $field == "tahun_ajaran" || $field == "nisn" || $field == "tahun_ajaran" || $field == "email_ms_office" || $field == "email_ms_office_ortu" || $field == "notelp_ibu" || $field == "notelp_ayah" || $field == "spp_siswa" || $field == "tgl_lahir"){
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
	            $get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_kb_aktif sa join kelas_kb k on sa.id_kelas = k.id_kelas_kb join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb where ".$where,"result");
		}
		//export excel
		$this->load->library('Excel/PHPExcel');
		// Instantiate a new PHPExcel object 
		$objPHPExcel = new PHPExcel();  
		// Set the active Excel worksheet to sheet 0 
		$objPHPExcel->setActiveSheetIndex(0);  
		// Initialise the Excel row number 
		$rowCount = 1;

		// start of printing column names as names of MySQL fields
		$column = 'A';
		// Header kolom 0 = "NO"
		$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, 'NO');
		$column++;
		for ($i = 0; $i < count($field_search); $i++)
		{
			// Kolom terakhir (id_siswa_kb_aktif) → label "ID SISWA KB AKTIF"
			if ($i == count($field_search) - 1) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, 'ID SISWA KB AKTIF');
			}
			else if (strpos($field_search[$i], "device_id_siswa") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, 'SISWA SUDAH PAKAI APLIKASI?');
			}
			else if (strpos($field_search[$i], "device_id_ortu") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, 'ORTU SUDAH PAKAI APLIKASI?');
			}
			else if (strpos($field_search[$i], "tgl_lahir") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, 'TANGGAL LAHIR');
			}
			else {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper(str_replace("_", " ", $field_search[$i])));
			}
		    $column++;
		}
		// end of adding column names

		// start while loop to get data
		$rowCount = 2;
		foreach ($get_data as $key => $value) {
			$column = 'A';
			// Kolom 0 = nomor urut
			$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, ($key + 1));
			$column++;
			for ($j = 0; $j < count($field_search); $j++) {
				$kolom = $field_search[$j];
				if(!isset($value->$kolom)) {
		            $value_data = "";
		        }
		        elseif ($value->$kolom != "") {
		            $value_data = strip_tags($value->$kolom);
		        }
		        else {
		            $value_data = "";
		        }
				// file_raport - nama file saja (tanpa URL)
				if (strpos($kolom, "file_raport") !== false) {
			        $value_data = !empty($value_data) ? $value_data : "";
			    }
				// device_id_siswa - nilai asli
				if (strpos($kolom, "device_id_siswa") !== false) {
		        	$value_data = !empty($value_data) ? $value_data : "";
		        }
				// device_id_ortu - nilai asli
				if (strpos($kolom, "device_id_ortu") !== false) {
		        	$value_data = !empty($value_data) ? $value_data : "";
		        }
				// tgl_lahir - YYYY-MM-DD agar import bisa parse
				if (strpos($kolom, "tgl_lahir") !== false && !empty($value_data)) {
					$value_data = date("Y-m-d", strtotime($value_data));
				}
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Siswa SD Aktif - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	/**
	 * AJAX: daftar siswa untuk dropdown Export Data Lengkap (select2).
	 * Filter: pencarian q, id_tingkatan, id_kelas.
	 */
	public function get_siswa_options()
	{
		if (!$this->is_allowed('siswa_kb_aktif_export', false)) {
			echo json_encode(array('items' => array()));
			exit;
		}

		$q            = $this->input->get('q');
		$id_tingkatan = (int) $this->input->get('id_tingkatan');
		$id_kelas     = (int) $this->input->get('id_kelas');

		$where = 'sa.deleted_at IS NULL AND s.deleted_at IS NULL';
		if ($id_tingkatan) {
			$where .= ' AND k.id_tingkatan = ' . $id_tingkatan;
		}
		if ($id_kelas) {
			$where .= ' AND sa.id_kelas = ' . $id_kelas;
		}
		if (!empty($q)) {
			$qs = $this->db->escape_str($q);
			$where .= " AND (sa.nama_lengkap LIKE '%" . $qs . "%' OR sa.nis LIKE '%" . $qs . "%')";
		}

		$rows = $this->mymodel->withquery(
			"SELECT sa.id_siswa_kb_aktif AS id, CONCAT(sa.nama_lengkap, ' (', COALESCE(k.label, '-'), ')') AS text
			 FROM siswa_kb_aktif sa
			 JOIN kelas_kb k ON sa.id_kelas = k.id_kelas_kb
			 JOIN siswa_kb s ON sa.id_siswa_kb = s.id_siswa_kb
			 WHERE " . $where . "
			 ORDER BY sa.nama_lengkap LIMIT 50", 'result');

		echo json_encode(array('items' => $rows));
		exit;
	}

	/**
	 * Export Data Lengkap: excel sesuai filter tingkatan/kelas/siswa
	 * dan kolom yang dipilih di modal.
	 */
	public function export_lengkap()
	{
		$this->is_allowed('siswa_kb_aktif_export');

		$all_cols = $this->model_siswa_kb_aktif->export_lengkap_columns();

		// whitelist kolom terpilih (default: nama, nis, kelas)
		$selected = $this->input->post('cols');
		if (!is_array($selected) || empty($selected)) {
			$selected = array('nama_lengkap', 'nis', 'kelas');
		}

		$select  = array();
		$headers = array('NO');
		$keys    = array();
		foreach ($all_cols as $group) {
			foreach ($group['cols'] as $key => $def) {
				if (in_array($key, $selected)) {
					$select[]  = $def[1] . ' AS ' . $key;
					$headers[] = $def[0];
					$keys[]    = $key;
				}
			}
		}

		$id_tingkatan = (int) $this->input->post('id_tingkatan');
		$id_kelas     = (int) $this->input->post('id_kelas');
		$siswa        = $this->input->post('siswa');

		$where = 'sa.deleted_at IS NULL AND s.deleted_at IS NULL';
		if ($id_tingkatan) {
			$where .= ' AND k.id_tingkatan = ' . $id_tingkatan;
		}
		if ($id_kelas) {
			$where .= ' AND sa.id_kelas = ' . $id_kelas;
		}
		if (is_array($siswa) && !in_array('all', $siswa)) {
			$ids = array();
			foreach ($siswa as $v) {
				$ids[] = (int) $v;
			}
			$ids = array_filter($ids);
			if (!empty($ids)) {
				$where .= ' AND sa.id_siswa_kb_aktif IN (' . implode(',', $ids) . ')';
			}
		}

		$get_data = $this->mymodel->withquery(
			'SELECT ' . implode(', ', $select) .
			' FROM siswa_kb_aktif sa
			  JOIN kelas_kb k ON sa.id_kelas = k.id_kelas_kb
			  LEFT JOIN tahun_ajaran t ON sa.id_tahun_ajaran = t.id_tahun_ajaran
			  JOIN siswa_kb s ON sa.id_siswa_kb = s.id_siswa_kb
			  WHERE ' . $where . '
			  ORDER BY k.id_tingkatan, k.label, sa.nama_lengkap', 'result');

		//export excel
		$this->load->library('Excel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		$column = 'A';
		foreach ($headers as $h) {
			$objPHPExcel->getActiveSheet()->setCellValue($column . '1', $h);
			$objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth(20);
			$objPHPExcel->getActiveSheet()->getStyle($column . '1')->getFont()->setBold(true);
			$column++;
		}

		$rowCount = 2;
		$no = 1;
		foreach ($get_data as $row) {
			$column = 'A';
			$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $no);
			$column++;
			foreach ($keys as $key) {
				$val = isset($row->$key) ? strip_tags($row->$key) : '';
				$objPHPExcel->getActiveSheet()->setCellValue($column . $rowCount, $val);
				$column++;
			}
			$rowCount++;
			$no++;
		}

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Data Lengkap Siswa SD Aktif - ' . date('Y-m-d Hi') . '.xls"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('siswa_kb_aktif_export');

		$this->model_siswa_kb_aktif->pdf('siswa_kb_aktif', 'siswa_kb_aktif');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_kb_aktif_export');

		$table = $title = 'siswa_kb_aktif';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_siswa_kb_aktif->find($id);
		$fields = $result->list_fields();

		$content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
			'data' => $data,
			'fields' => $fields,
			'title' => $title
		], TRUE);

		$this->pdf->initialize($config);
		$this->pdf->pdf->SetDisplayMode('fullpage');
		$this->pdf->writeHTML($content);
		$this->pdf->Output($table . '.pdf', 'H');
	}

	public function generate_nis()
	{
		$id_tahun_ajaran = $this->input->post("id_tahun_ajaran");
		//get all data di unit & tahun ajaran terpilih diurutkan berdasarkan abjad ASC
		$get_data = $this->mymodel->withquery("select * from siswa_kb_aktif where id_tahun_ajaran = '".$id_tahun_ajaran."' order by nama_lengkap ASC","result");
		$get_tahun = $this->mymodel->withquery("select code from tahun_ajaran where id_tahun_ajaran = '".$id_tahun_ajaran."'","row")->code;
		$tahun_ajaran = str_replace("_", "", $get_tahun);
		foreach ($get_data as $key => $value) {
			//cek apakah nis kosong
			$nis = $tahun_ajaran;
			$kelas_mutasi = sprintf("%02d", 1);
			//cek apakah nis sudah terdaftar di email master siswa (jika terdaftar, maka gunakan nis lama sesuai email ms office nya)
			$cek_master_siswa = $this->mymodel->withquery("select id_siswa_kb, email, email_ms_office, email_ms_office_ortu, is_mutasi, kelas_mutasi from siswa_kb where id_siswa_kb = '".$value->id_siswa_kb."'", "row");
			if ($value->nis == "-" || empty($value->nis)) {
				$no_urut = "";
				if (empty($value->nis) && $key == 0) {
                  $no_urut = "001";
                }
                else{
                  $no_urut = (int)$key;
                  $no_urut = $no_urut+1;
                  $no_urut = sprintf("%03d", $no_urut);
                }
				if ($cek_master_siswa->is_mutasi == 1 && !empty($cek_master_siswa->kelas_mutasi)) {
					$kelas_mutasi = sprintf("%02d", $cek_master_siswa->kelas_mutasi);
				}
                $nis = $nis.$kelas_mutasi.$no_urut;
				
				if(!empty($cek_master_siswa) && !empty($cek_master_siswa->email_ms_office)){
					$nis_temp = explode("@",$cek_master_siswa->email_ms_office);
					if ((int)$nis_temp[0] > 0) {
						$nis = $nis_temp[0];
					}
					else{
						//cek apakah nis baru tersedia?
						for ($i=0; $i < count($get_data); $i++) { 
							$cek = $this->mymodel->withquery("select id_siswa_kb_aktif, nis from siswa_kb_aktif where nis = '".$nis."'","result");
							if (empty($cek)) {
								break;
							}
							else{
								$no_urut = (int)$key;
								$no_urut = $no_urut+$i;
								$no_urut = sprintf("%03d", $no_urut);
								$nis = $nis.$no_urut;
							}
						}
					}
				}
                else{
					//cek apakah nis baru tersedia?
					for ($i=0; $i < count($get_data); $i++) { 
						$cek = $this->mymodel->withquery("select id_siswa_kb_aktif, nis from siswa_kb_aktif where nis = '".$nis."'","result");
						if (empty($cek)) {
							break;
						}
						else{
							$no_urut = (int)$key;
							$no_urut = $no_urut+$i;
							$no_urut = sprintf("%03d", $no_urut);
							$nis = $nis.$no_urut;
						}
					}
				}
                //update nis utk siswa tsb
                $data_update = array(
					"nis" => $nis,
				);
				//update siswa aktif
				$up = $this->mymodel->update("siswa_kb_aktif",$data_update, "id_siswa_kb_aktif", $value->id_siswa_kb_aktif);
				//update data siswa
				$data_update2 = array(
					"email_ms_office" => $nis."@labschoolcibubur.sch.id",
					"email_ms_office_ortu" => $nis."p@labschoolcibubur.sch.id"
				);
				$up2 = $this->mymodel->update("siswa_kb",$data_update2, "id_siswa_kb", $value->id_siswa_kb);
			}
		}
		redirect_back();		
	}

	public function import_siswa()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');

		if (isset($_FILES["file_import"]["name"]) && !empty($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p = $_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			
			// Validasi ekstensi file
			if ($ext !== 'xls' && $ext !== 'xlsx') {
				$this->session->set_flashdata('error', 'Format harus .xls atau .xlsx');
				redirect($_SERVER['HTTP_REFERER']);
			}
			
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();

				for ($row = 2; $row <= $highestRow; $row++) {
					// Kolom Excel (0-indexed):
					// 0=NO, 1=nama_lengkap, 2=nis, 3=kelas, 4=tahun_ajaran,
					// 5=nisn, 6=email_ms_office, 7=email_ms_office_ortu, 8=notelp_ibu, 9=notelp_ayah,
					// 10=kewarganegaraan, 11=nik, 12=golongan_darah, 13=telp,
					// 14=pendidikan_ayah, 15=pendidikan_ibu, 16=penghasilan_ayah, 17=penghasilan_ibu,
					// 18=tgl_lahir_ayah, 19=tgl_lahir_ibu, 20=spp_type, 21=acc_ujian,
					// 22=spp_custom, 23=nomor_peserta_ujian, 24=device_id_siswa,
					// 25=device_id_ortu, 26=spp_siswa, 27=file_raport, 28=tgl_lahir,
					// 29=id_siswa_kb_aktif (primary key untuk import)
					$id_siswa_aktif_import = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
					$nama_input = ucwords($worksheet->getCellByColumnAndRow(1, $row)->getValue());
					
					// Skip jika baris kosong
					if (empty($id_siswa_aktif_import) && empty($nama_input)) {
						continue;
					}
					
					// Cari siswa berdasarkan id_siswa_kb_aktif (kolom 29) jika ada
					if (!empty($id_siswa_aktif_import)) {
						$get_siswa = $this->mymodel->withquery(
							"select sa.id_siswa_kb_aktif, s.id_siswa_kb from siswa_kb_aktif sa join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb where sa.id_siswa_kb_aktif = " . $this->db->escape_str($id_siswa_aktif_import),
							"row"
						);
					}
					
					// Fallback: cari berdasarkan NIS
					if (!$get_siswa || empty($get_siswa->id_siswa_kb)) {
						$nis = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
						$get_siswa = $this->mymodel->withquery(
							"select sa.id_siswa_kb_aktif, s.id_siswa_kb from siswa_kb_aktif sa join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb where sa.nis = " . $this->db->escape_str($nis),
							"row"
						);
					}
					
					// Fallback: cari berdasarkan nama_lengkap
					if (!$get_siswa || empty($get_siswa->id_siswa_kb)) {
						$nama_lower = strtolower(trim($nama_input));
						$get_siswa = $this->mymodel->withquery(
							"select sa.id_siswa_kb_aktif, s.id_siswa_kb from siswa_kb_aktif sa join siswa_kb s on sa.id_siswa_kb = s.id_siswa_kb where lower(sa.nama_lengkap) = lower(" . $this->db->escape_str($nama_lower) . ")",
							"row"
						);
					}
					
					// Jika siswa tidak ditemukan, skip baris ini
					if (!$get_siswa || empty($get_siswa->id_siswa_kb)) {
						continue;
					}
				
					$this->db->trans_begin();
					
					$id_siswa_aktif = (int)$get_siswa->id_siswa_kb_aktif;
					$id_siswa_kb    = (int)$get_siswa->id_siswa_kb;
					
					// Ambil data kelas dan tahun ajaran dari Excel
					$nis          = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					$nisn         = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
					$kelas_text   = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					$ta_text      = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					
					$kelas_obj   = $this->mymodel->withquery(
						"select id_kelas_kb from kelas_kb where label like '%" . $this->db->escape_str($kelas_text) . "%'",
						"row"
					);
					$ta_obj      = $this->mymodel->withquery(
						"select id_tahun_ajaran from tahun_ajaran where label like '%" . $this->db->escape_str($ta_text) . "%'",
						"row"
					);
					
					$kelas  = (!empty($kelas_obj))   ? $kelas_obj->id_kelas_kb   : null;
					$ta     = (!empty($ta_obj))      ? $ta_obj->id_tahun_ajaran : null;

					// Helper: ambil nilai cell, kosongkan jika null/empty
					$cellVal = function($col, $row) use ($worksheet) {
						$v = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
						return ($v !== null && $v !== '') ? $v : '';
					};
					$cellInt = function($col, $row) use ($worksheet) {
						$v = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
						return ($v !== null && $v !== '' && is_numeric($v)) ? (int)$v : 0;
					};

					$data_siswa_aktif = array(
						"nama_lengkap"      => $nama_input,
						"nis"               => (string)($nis ?: ''),
						"id_kelas"          => $kelas,
						"id_tahun_ajaran"   => $ta,
						"kewarganegaraan"   => (string)$cellVal(10, $row),
						"nik"               => (string)$cellVal(11, $row),
						"golongan_darah"    => (string)$cellVal(12, $row),
						"telp"              => (string)$cellVal(13, $row),
						"pendidikan_ayah"   => (string)$cellVal(14, $row),
						"pendidikan_ibu"    => (string)$cellVal(15, $row),
						"penghasilan_ayah"  => $cellInt(16, $row),
						"penghasilan_ibu"   => $cellInt(17, $row),
						"tgl_lahir_ayah"    => parse_excel_date($worksheet->getCellByColumnAndRow(18, $row)),
						"tgl_lahir_ibu"     => parse_excel_date($worksheet->getCellByColumnAndRow(19, $row)),
						"spp_type"          => $cellVal(20, $row) ?: 'FULL',
						"acc_ujian"         => in_array($worksheet->getCellByColumnAndRow(21, $row)->getValue(), [1, '1', 'Boleh', 'Ya', 'YES', true], true) ? 1 : 0,
						"spp_custom"        => $cellInt(22, $row),
						"nomor_peserta_ujian" => (string)$cellVal(23, $row),
					);
					
					$data_siswa = array(
						"nama_lengkap"       => $nama_input,
						"nisn"               => (string)$cellVal(5, $row),
						"email_ms_office"    => (string)$cellVal(6, $row),
						"email_ms_office_ortu" => (string)$cellVal(7, $row),
						"notelp_ayah"        => str_replace("'", "", (string)$cellVal(9, $row)),
						"notelp_ibu"         => str_replace("'", "", (string)$cellVal(8, $row)),
						"tgl_lahir"          => parse_excel_date($worksheet->getCellByColumnAndRow(27, $row)),
					);
					
					// Update siswa_kb_aktif berdasarkan id_siswa_kb_aktif
					$update_siswa_aktif = $this->mymodel->update("siswa_kb_aktif", $data_siswa_aktif, "id_siswa_kb_aktif", $id_siswa_aktif);
					
					if ($update_siswa_aktif === false) {
						$this->db->trans_rollback();
						$this->session->set_flashdata('error', 'Gagal update data siswa ' . $data_siswa_aktif["nama_lengkap"]);
						continue 2;
					}
					
					// Update siswa_kb
					$this->mymodel->update("siswa_kb", $data_siswa, "id_siswa_kb", $id_siswa_kb);
					
					// Update spp_sd jika kelas dan tahun ajaran ditemukan
					if (!empty($kelas) && !empty($ta)) {
						$this->mymodel->update(
							"spp_sd",
							array(
								"id_tahun_ajaran" => $ta,
								"kelas"           => $kelas_text,
								"nama"            => $data_siswa_aktif["nama_lengkap"],
								"nominal"         => $data_siswa_aktif["spp_custom"]
							),
							"id_tahun_ajaran = '" . $ta . "' and id_siswa_aktif = " . $id_siswa_aktif
						);
						
						// Update transaksi_spp yang belum lunas
						$this->db->query(
							"UPDATE transaksi_spp SET " .
							"id_tahun_ajaran = '" . $ta . "', " .
							"user_name = '" . $this->db->escape_str($data_siswa_aktif["nama_lengkap"]) . "', " .
							"total_biaya = " . (int)$data_siswa_aktif["spp_custom"] . " " .
							"WHERE no_transaksi LIKE '%SD%' " .
							"AND id_tahun_ajaran = '" . $ta . "' " .
							"AND id_siswa_aktif = " . $id_siswa_aktif . " " .
							"AND status_transaksi != '2'"
						);
					}
					
					$this->db->trans_commit();
				}
			}
			
			$this->session->set_flashdata('success', 'Import data siswa berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$this->session->set_flashdata('error', 'Tidak ada file yang diupload');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}


	public function import_raport_siswa(){
		$data=array();
        $total_file = count($_FILES['file_raport']['name']);
        $data_file = array();
        for ($i=0; $i < $total_file; $i++) {
            if (!empty($_FILES['file_raport']['name'][$i])) {
                  $trans_folder = './uploads/siswa_kb_aktif/'; //path htrans
                  if(!is_dir($trans_folder)){
                      mkdir($trans_folder, 0777);
                  }
                  $uploaddir = $trans_folder;
                  $img = explode('.', $_FILES['file_raport']['name'][$i]);
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
                  $filename_no_extension = $filename_original;//md5(date('y-m-d h:i:s').$_FILES['file_raport']['name'][$i]);
                  $file_name =  $filename_no_extension.".".$extension;
                  $uploadfile = $uploaddir.$file_name;
                  if (move_uploaded_file($_FILES['file_raport']['tmp_name'][$i], $uploadfile)) {
                    if($extension == "pdf" || $extension == "doc" || $extension == "docx" || $extension == "docs"){
                      $data = array(
                        "file_raport" => $file_name
                      );
                    }
                    $up = $this->mymodel->update("siswa_kb_aktif",$data, "nis", $filename_original);
                    $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                    $filenya = array(
                      "file_upload" => base_url("uploads/siswa_kb_aktif/").$filename_no_extension.".pdf",
                      "nama_asli" => $_FILES['file_raport']['name'][$i],
                      "ukuran" => $_FILES['file_raport']['size'][$i]
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

	/**
	 * IMPORT MASSAL CREATE (data historis, TIDAK terhubung PSB; id_siswa_kb = 0).
	 * 2 langkah: upload+parse+validasi (preview) -> konfirmasi (commit).
	 * Manual via upload file — tidak ada cron/trigger otomatis.
	 */
	public function import_create()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add')) {
			redirect('/', 'refresh');
		}
		$this->data['list_tahun_ajaran'] = $this->mymodel->withquery("SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY label DESC", "result");
		$this->data['preview'] = $this->session->userdata('import_create_kb');
		$this->template->title('Siswa Kb Aktif - Import Buat Baru');
		$this->render('backend/standart/administrator/siswa_kb_aktif/siswa_kb_aktif_import_create', $this->data);
	}

	/**
	 * Download template Excel import massal create.
	 */
	public function template_import_create()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add')) {
			redirect('/', 'refresh');
		}
		$this->load->library('excel');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setTitle("Template Import Siswa KB Aktif (Buat Baru)");
		$sheet = $objPHPExcel->getActiveSheet();
		$headers = array(
			'NO', 'NAMA LENGKAP', 'NIS', 'KELAS', 'TINGKATAN',
			'KEWARGANEGARAAN', 'NIK', 'GOLONGAN DARAH', 'TELP',
			'PENDIDIKAN AYAH', 'PENDIDIKAN IBU', 'PENGHASILAN AYAH', 'PENGHASILAN IBU',
			'TGL LAHIR AYAH', 'TGL LAHIR IBU', 'TGL LAHIR SISWA', 'NOMOR PESERTA UJIAN'
		);
		$col = 0;
		foreach ($headers as $h) {
			$sheet->setCellValueByColumnAndRow($col, 1, $h);
			$sheet->getStyleByColumnAndRow($col, 1)->getFont()->setBold(true);
			$col++;
		}
		// contoh baris (baris 2) — kosongkan nilai, hanya panduan format tanggal
		$sheet->setCellValueByColumnAndRow(14, 2, 'YYYY-MM-DD');
		$sheet->setCellValueByColumnAndRow(15, 2, 'YYYY-MM-DD');
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="template_import_siswa_kb_aktif.xls"');
		header('Cache-Control: max-age=0');
		PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5')->save('php://output');
		exit;
	}

	/**
	 * Langkah 1: parse + validasi file, simpan hasil di session utk preview.
	 * Kolom Excel (0-indexed): 0=NO, 1=nama_lengkap, 2=nis, 3=kelas(label kelas_kb),
	 * 4=tingkatan(label tingkatan_kb), 5=kewarganegaraan, 6=nik, 7=golongan_darah, 8=telp,
	 * 9=pendidikan_ayah, 10=pendidikan_ibu, 11=penghasilan_ayah, 12=penghasilan_ibu,
	 * 13=tgl_lahir_ayah, 14=tgl_lahir_ibu, 15=tgl_lahir, 16=nomor_peserta_ujian.
	 * Tahun ajaran dipilih per-batch dari form (bukan dari kolom).
	 */
	public function import_create_parse()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add')) {
			redirect('/', 'refresh');
		}
		$id_tahun_ajaran = (int) $this->input->post('id_tahun_ajaran');
		if (empty($id_tahun_ajaran)) {
			$this->session->set_flashdata('error', 'Pilih tahun ajaran dulu');
			redirect('administrator/siswa_kb_aktif/import_create');
		}
		if (empty($_FILES['file_import']['name'])) {
			$this->session->set_flashdata('error', 'File Excel belum dipilih');
			redirect('administrator/siswa_kb_aktif/import_create');
		}
		$ext = pathinfo($_FILES['file_import']['name'], PATHINFO_EXTENSION);
		if ($ext !== 'xls' && $ext !== 'xlsx') {
			$this->session->set_flashdata('error', 'Format harus .xls atau .xlsx');
			redirect('administrator/siswa_kb_aktif/import_create');
		}

		$this->load->library('excel');
		$object = PHPExcel_IOFactory::load($_FILES['file_import']['tmp_name']);
		$worksheet = $object->getSheet(0);
		$highestRow = $worksheet->getHighestRow();

		// lookup map kelas & tingkatan (hindari query per baris)
		$map_kelas = array();
		foreach ($this->mymodel->withquery("SELECT id_kelas_kb, label FROM kelas_kb", "result") as $k) {
			$map_kelas[strtolower(trim($k->label))] = $k->id_kelas_kb;
		}
		$map_tingkatan = array();
		foreach ($this->mymodel->withquery("SELECT id_tingkatan_kb, label FROM tingkatan_kb", "result") as $t) {
			$map_tingkatan[strtolower(trim($t->label))] = $t->id_tingkatan_kb;
		}

		$rows = array();    // baris valid utk preview/commit
		$skipped = array(); // baris di-skip + alasan
		$peringatan = array();
		$seen_nis = array();

		for ($row = 2; $row <= $highestRow; $row++) {
			$nama = ucwords(trim((string) $worksheet->getCellByColumnAndRow(1, $row)->getValue()));
			$nis = trim((string) $worksheet->getCellByColumnAndRow(2, $row)->getValue());
			$kelas_text = trim((string) $worksheet->getCellByColumnAndRow(3, $row)->getValue());
			$tingkatan_text = trim((string) $worksheet->getCellByColumnAndRow(4, $row)->getValue());

			if ($nama === '' && $nis === '' && $kelas_text === '') {
				continue; // baris benar-benar kosong
			}
			if ($nama === '') {
				$skipped[] = array('row' => $row, 'nama' => '', 'alasan' => 'Nama kosong');
				continue;
			}
			if ($nis === '') {
				$skipped[] = array('row' => $row, 'nama' => $nama, 'alasan' => 'NIS kosong');
				continue;
			}
			if (isset($seen_nis[$nis])) {
				$skipped[] = array('row' => $row, 'nama' => $nama, 'alasan' => 'NIS ' . $nis . ' duplikat di dalam file (baris ' . $seen_nis[$nis] . ')');
				continue;
			}
			$cek_nis = $this->mymodel->getbywhere('siswa_kb_aktif', 'nis', $nis, 'row');
			if (!empty($cek_nis)) {
				$skipped[] = array('row' => $row, 'nama' => $nama, 'alasan' => 'NIS ' . $nis . ' sudah terdaftar (id aktif ' . $cek_nis->id_siswa_kb_aktif . ')');
				continue;
			}

			$id_kelas = 0;
			if ($kelas_text !== '') {
				$key_kelas = strtolower($kelas_text);
				if (isset($map_kelas[$key_kelas])) {
					$id_kelas = (int) $map_kelas[$key_kelas];
				} else {
					// fallback LIKE (kelas label sering "TK A BINTANG" dsb)
					$found = false;
					foreach ($map_kelas as $label => $idk) {
						if ($label !== '' && strpos($label, $key_kelas) !== false) {
							$id_kelas = (int) $idk;
							$found = true;
							break;
						}
					}
					if (!$found) {
						$skipped[] = array('row' => $row, 'nama' => $nama, 'alasan' => 'Kelas "' . $kelas_text . '" tidak ditemukan di kelas_kb');
						continue;
					}
				}
			}

			$id_tingkatan = 0;
			if ($tingkatan_text !== '') {
				$key_t = strtolower($tingkatan_text);
				if (isset($map_tingkatan[$key_t])) {
					$id_tingkatan = (int) $map_tingkatan[$key_t];
				} else {
					$peringatan[] = array('row' => $row, 'nama' => $nama, 'catatan' => 'Tingkatan "' . $tingkatan_text . '" tidak ditemukan — disimpan 0');
				}
			}

			$rows[] = array(
				'nama_lengkap' => $nama,
				'nis' => $nis,
				'id_kelas' => $id_kelas,
				'id_tingkatan' => $id_tingkatan,
				'id_tahun_ajaran' => $id_tahun_ajaran,
				'kewarganegaraan' => (string) $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
				'nik' => (string) $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
				'golongan_darah' => (string) $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
				'telp' => (string) $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
				'pendidikan_ayah' => (string) $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
				'pendidikan_ibu' => (string) $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
				'penghasilan_ayah' => (int) $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
				'penghasilan_ibu' => (int) $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
				'tgl_lahir_ayah' => $this->_ic_tgl($worksheet->getCellByColumnAndRow(13, $row)->getValue()),
				'tgl_lahir_ibu' => $this->_ic_tgl($worksheet->getCellByColumnAndRow(14, $row)->getValue()),
				'tgl_lahir' => $this->_ic_tgl($worksheet->getCellByColumnAndRow(15, $row)->getValue()),
				'nomor_peserta_ujian' => (string) $worksheet->getCellByColumnAndRow(16, $row)->getValue(),
				'acc_ujian' => 0,
				'is_active' => 1,
				'spp_custom' => 0,
				'id_siswa_kb' => 0
			);
			$seen_nis[$nis] = $row;
		}

		$this->session->set_userdata('import_create_kb', array(
			'rows' => $rows,
			'skipped' => $skipped,
			'peringatan' => $peringatan,
			'id_tahun_ajaran' => $id_tahun_ajaran,
			'file' => $_FILES['file_import']['name']
		));
		redirect('administrator/siswa_kb_aktif/import_create');
	}

	/**
	 * Langkah 2: commit insert baris yang sudah di-preview.
	 */
	public function import_create_commit()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add')) {
			redirect('/', 'refresh');
		}
		$payload = $this->session->userdata('import_create_kb');
		if (empty($payload) || empty($payload['rows'])) {
			$this->session->set_flashdata('error', 'Tidak ada data preview. Upload ulang file.');
			redirect('administrator/siswa_kb_aktif/import_create');
		}

		$this->db->trans_begin();
		$berhasil = 0;
		foreach ($payload['rows'] as $r) {
			$this->mymodel->insertid('siswa_kb_aktif', $r);
			$berhasil++;
		}
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('error', 'Import gagal total (transaction rollback) — tidak ada baris yang masuk. Cek log error.');
			redirect('administrator/siswa_kb_aktif/import_create');
		}
		$this->db->trans_commit();

		$skip_count = count($payload['skipped']);
		$this->session->unset_userdata('import_create_kb');
		$this->session->set_flashdata('success', 'Import selesai: ' . $berhasil . ' baris berhasil di-insert, ' . $skip_count . ' baris di-skip (detail ada di halaman preview sebelum commit).');
		redirect('administrator/siswa_kb_aktif');
	}

	/**
	 * Batalkan preview import.
	 */
	public function import_create_cancel()
	{
		if (!$this->is_allowed('siswa_kb_aktif_add')) {
			redirect('/', 'refresh');
		}
		$this->session->unset_userdata('import_create_kb');
		redirect('administrator/siswa_kb_aktif/import_create');
	}

	/**
	 * Helper: konversi nilai tanggal Excel (serial number / string) ke Y-m-d.
	 */
	private function _ic_tgl($v)
	{
		if ($v === null || $v === '') {
			return '';
		}
		if (is_numeric($v) && $v > 20000 && $v < 60000) {
			return date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($v));
		}
		$t = strtotime((string) $v);
		return $t ? date('Y-m-d', $t) : '';
	}
}

/* End of file siswa_kb_aktif.php */
/* Location: ./application/controllers/administrator/Siswa KB Aktif.php */
