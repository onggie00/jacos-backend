<?php
define( 'CLIENT_ID', 'cb3ca268-e619-4b43-8aff-81538fa687c0');
define( 'TENANT_ID', '10fc2260-5c60-4800-af0e-789640a374a3'); // Only for user with mail Labschoolcibubur or just fill common to accept all microsoft mail
define( 'SECRET_ID', '4aa8a802-af7f-41cf-b7f6-34e50fc432e4'); //backup active secret : bf68c70f-691c-4b2b-a3c6-0314d60d51a5
define( 'CLIENT_SECRET', ''); //April 2028
define( 'GRAPH_USER_SCOPES', 'user.read mail.read mail.send offline_access User.ReadWrite.All User-PasswordProfile.ReadWrite.All');
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *| --------------------------------------------------------------------------
 *| Siswa Sma Aktif Controller
 *| --------------------------------------------------------------------------
 *| Siswa Sma Aktif site
 *|
 */
class Siswa_sma_aktif extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_sma_aktif');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Siswa Sma Aktifs
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_sma_aktif_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		// Multi-filter support
		$multi_filters = $this->_parse_filters();
		$has_multi_filter = !empty($multi_filters);

		if ($has_multi_filter) {
			$this->data['siswa_sma_aktifs'] = $this->model_siswa_sma_aktif->get(null, null, $this->limit_page, $offset, [], $sort, $sort_type, $multi_filters);
			$this->data['siswa_sma_aktif_counts'] = $this->model_siswa_sma_aktif->count_all(null, null, $multi_filters);
		} else {
			$this->data['siswa_sma_aktifs'] = $this->model_siswa_sma_aktif->get($filter, $field, $this->limit_page, $offset, [], $sort, $sort_type);
			$this->data['siswa_sma_aktif_counts'] = $this->model_siswa_sma_aktif->count_all($filter, $field);
		}

		$config = [
			'base_url'     => 'administrator/siswa_sma_aktif/index/',
			'total_rows'   => $has_multi_filter ? $this->model_siswa_sma_aktif->count_all(null, null, $multi_filters) : $this->model_siswa_sma_aktif->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		// Dropdown data for filter builder
		$this->data['list_kelas'] = $this->mymodel->withquery("SELECT id_kelas_sma, label FROM kelas_sma ORDER BY label", "result");
		$this->data['list_tahun_ajaran'] = $this->mymodel->withquery("SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY label DESC", "result");
		$this->data['multi_filters'] = $multi_filters;

		$this->template->title('Siswa Sma Aktif List');
		$this->render('backend/standart/administrator/siswa_sma_aktif/siswa_sma_aktif_list', $this->data);
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
	 * Add new siswa_sma_aktifs
	 *
	 */
	public function add()
	{
		$this->is_allowed('siswa_sma_aktif_add');

		$this->template->title('Siswa Sma Aktif New');
		$this->render('backend/standart/administrator/siswa_sma_aktif/siswa_sma_aktif_add', $this->data);
	}

	/**
	 * Add New Siswa Sma Aktifs
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		if (!$this->is_allowed('siswa_sma_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nis', 'Nis', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa_sma', 'Id Siswa Sma', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kewarganegaraan', 'Kewarganegaraan', 'trim|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[20]');
		$this->form_validation->set_rules('golongan_darah', 'Golongan Darah', 'trim|max_length[5]');
		$this->form_validation->set_rules('telp', 'Telp', 'trim|max_length[20]');
		$this->form_validation->set_rules('pendidikan_ayah', 'Pendidikan Ayah', 'trim|max_length[255]');
		$this->form_validation->set_rules('pendidikan_ibu', 'Pendidikan Ibu', 'trim|max_length[255]');
		$this->form_validation->set_rules('penghasilan_ayah', 'Penghasilan Ayah', 'trim|max_length[11]');
		$this->form_validation->set_rules('penghasilan_ibu', 'Penghasilan Ibu', 'trim|max_length[11]');
		$this->form_validation->set_rules('tgl_lahir_ayah', 'Tgl Lahir Ayah', 'trim');
		$this->form_validation->set_rules('tgl_lahir_ibu', 'Tgl Lahir Ibu', 'trim');
		$this->form_validation->set_rules('spp_custom', 'SPP Khusus', 'trim|required');

		if ($this->form_validation->run()) {

			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nis' => $this->input->post('nis'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_siswa_sma' => $this->input->post('id_siswa_sma'),
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
				'spp_custom' => $this->input->post('spp_custom') !== false
				? (int) preg_replace('/[^0-9]/', '', (string) $this->input->post('spp_custom'))
				: 0,
				'spp_type' => $this->input->post('spp_type'),
				'acc_ujian' => $this->input->post('acc_ujian'),
				'nomor_peserta_ujian' => $this->input->post('nomor_peserta_ujian'),
			];


			$save_siswa_sma_aktif = $this->model_siswa_sma_aktif->store($save_data);


			
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_sma_aktif;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_sma_aktif/edit/' . $save_siswa_sma_aktif, 'Edit Siswa Sma Aktif'),
						anchor('administrator/siswa_sma_aktif', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
							anchor('administrator/siswa_sma_aktif/edit/' . $save_siswa_sma_aktif, 'Edit Siswa Sma Aktif')
						]),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_sma_aktif');
				}
			
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	 * Update view Siswa Sma Aktifs
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('siswa_sma_aktif_update');
		$this->model_siswa_sma_aktif->join_avaiable();
		$this->data['siswa_sma_aktif'] = $this->model_siswa_sma_aktif->find($id);

		$this->template->title('Siswa Sma Aktif Update');
		$this->render('backend/standart/administrator/siswa_sma_aktif/siswa_sma_aktif_update', $this->data);
	}

	/**
	 * Update Siswa Sma Aktifs
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_sma_aktif_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('nis', 'Nis', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('id_tahun_ajaran', 'Id Tahun Ajaran', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_kelas', 'Id Kelas', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('id_siswa_sma', 'Id Siswa Sma', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kewarganegaraan', 'Kewarganegaraan', 'trim|max_length[255]');
		$this->form_validation->set_rules('nik', 'Nik', 'trim|max_length[20]');
		$this->form_validation->set_rules('golongan_darah', 'Golongan Darah', 'trim|max_length[5]');
		$this->form_validation->set_rules('telp', 'Telp', 'trim|max_length[20]');
		$this->form_validation->set_rules('pendidikan_ayah', 'Pendidikan Ayah', 'trim|max_length[255]');
		$this->form_validation->set_rules('pendidikan_ibu', 'Pendidikan Ibu', 'trim|max_length[255]');
		$this->form_validation->set_rules('penghasilan_ayah', 'Penghasilan Ayah', 'trim|max_length[11]');
		$this->form_validation->set_rules('penghasilan_ibu', 'Penghasilan Ibu', 'trim|max_length[11]');
		$this->form_validation->set_rules('tgl_lahir_ayah', 'Tgl Lahir Ayah', 'trim');
		$this->form_validation->set_rules('tgl_lahir_ibu', 'Tgl Lahir Ibu', 'trim');
		$this->form_validation->set_rules('spp_custom', 'SPP Khusus', 'trim|required');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms Office', 'trim');
		$this->form_validation->set_rules('email_ms_office_ortu', 'Email Ms Office Ortu', 'trim');
		
		if ($this->form_validation->run()) {

			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'nis' => $this->input->post('nis'),
				'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
				'id_kelas' => $this->input->post('id_kelas'),
				'id_siswa_sma' => $this->input->post('id_siswa_sma'),
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

			if ($this->input->post("spp_custom") !== false) {
				$_spp_custom_raw = $this->input->post("spp_custom");
				$_spp_custom_clean = ($_spp_custom_raw !== null && strlen($_spp_custom_raw) > 0)
					? (int) preg_replace('/[^0-9]/', '', (string) $_spp_custom_raw)
					: null;
				$save_data["spp_custom"] = $_spp_custom_clean;
			}
			if ($this->input->post("spp_type") !== false) {
				$save_data["spp_type"] = $this->input->post("spp_type") ?: 'FULL';
			}
			
			$this->model_siswa_sma_aktif->change($id, $save_data);

			// Direct update spp_custom & spp_type (bypass MY_Model scope issue)
			if (isset($save_data['spp_custom'])) {
				$this->db->where('id_siswa_sma_aktif', $id)->update('siswa_sma_aktif', array('spp_custom' => $save_data['spp_custom']));
			}
			if (isset($save_data['spp_type'])) {
				$this->db->where('id_siswa_sma_aktif', $id)->update('siswa_sma_aktif', array('spp_type' => $save_data['spp_type']));
			}

			// --- Restore akun ---
				$restore_siswa = $this->input->post('restore_siswa');
				if ($restore_siswa === 'restore') {
					$this->mymodel->update('siswa_sma', array('deleted_at' => NULL), 'id_siswa_sma', $this->input->post('id_siswa_sma'));
					$this->mymodel->update('siswa_sma_aktif', array('deleted_at' => NULL), 'id_siswa_sma_aktif', $id);
				}
				//restore akun ortu jika dropdown = restore
				$restore_ortu = $this->input->post('restore_ortu');
				if ($restore_ortu === 'restore') {
					$this->mymodel->update('siswa_sma', array('deleted_at_ortu' => NULL), 'id_siswa_sma', $this->input->post('id_siswa_sma'));
					$this->mymodel->update('siswa_sma_aktif', array('deleted_at_ortu' => NULL), 'id_siswa_sma_aktif', $id);
				}
				//simpan ke data siswa
				$save_data_siswa = array(
					'nama_lengkap' => strtoupper($this->input->post('nama_lengkap')),
					'email_ms_office' => $this->input->post('email_ms_office'),
					'email_ms_office_ortu' => $this->input->post('email_ms_office_ortu'),
					/* 'nis' => $this->input->post('nis'),
					'id_kelas' => $this->input->post('id_kelas'),
					'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran') */
				);
				$this->mymodel->update("siswa_sma", $save_data_siswa, "id_siswa_sma", $this->input->post("id_siswa_sma"));
				//simpan ke data spp
				$save_data_spp = array(
					'nama' => strtoupper($this->input->post('nama_lengkap')),
					'id_tahun_ajaran' => $this->input->post('id_tahun_ajaran'),
					'nominal' => $this->input->post('spp_custom'), 
				);
				$get_tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran where id_tahun_ajaran = '".$this->input->post("id_tahun_ajaran")."'","row");
				$get_spp = $this->mymodel->withquery("select * from spp_sma where tahun_ajaran = '".$get_tahun_ajaran->label."' ","result");
				$nama_kelas = $this->mymodel->withquery(
					"SELECT label FROM kelas_sma WHERE id_kelas_sma = '".$this->input->post('id_kelas')."'",
					"row"
				)->label;
				$is_keluar = (stripos($nama_kelas, 'keluar') !== false);
				$is_alumni = (stripos($nama_kelas, 'alumni') !== false);
				// daftar kolom bulan
				$bulan = [
					'januari','februari','maret','april','mei','juni',
					'juli','agustus','september','oktober','november','desember'
				];
					$spp_custom_post = $this->input->post("spp_custom");
					//normalisasi pemisah digit (titik dari format ribuan) -> integer murni
					if ($spp_custom_post !== false && $spp_custom_post !== null) {
						$spp_custom_post = preg_replace('/[^0-9]/', '', (string) $spp_custom_post);
					}
					$spp_custom_changed = ($spp_custom_post !== false && strlen($spp_custom_post) > 0);

					foreach ($get_spp as $value) {

						$save_data_spp = [];

						if ($spp_custom_changed) {
							// Jangan ubah nominal jika ada kolom bulan yang sudah terisi
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
					
						// hanya update jika ada data yang berubah
						if (!empty($save_data_spp)) {
							$save_data_spp['kelas'] = $nama_kelas;
							$this->mymodel->update(
								'spp_sma',
								$save_data_spp,
								"tahun_ajaran = '".$value->tahun_ajaran."' AND id_siswa_aktif = ".$id
							);
						}
					}
					//simpan ke data transaksi spp hanya jika spp_custom diubah
					//hanya tagihan non-lunas (status_transaksi 0 atau 1) yang nominalnya diubah,
					//tagihan sudah lunas (status_transaksi=2) tidak diubah nominalnya.
					if ($spp_custom_changed) {
						$save_data_transaksi = array(
							'user_name' => strtoupper($this->input->post('nama_lengkap')),
							'total_biaya' => (int) $spp_custom_post,
						);
						$this->db->where("id_siswa_aktif", $id);
						$this->db->where("id_tahun_ajaran", $this->input->post("id_tahun_ajaran"));
						$this->db->like("no_transaksi", "sma");
						$this->db->group_start();
						$this->db->where("status_transaksi", '0');
						$this->db->or_where("status_transaksi", '1');
						$this->db->group_end();
						$this->db->update("transaksi_spp", $save_data_transaksi);
					}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/siswa_sma_aktif', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_sma_aktif');
				}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	/**
	 * delete Siswa Sma Aktifs
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('siswa_sma_aktif_delete');

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
			set_message(cclang('has_been_deleted', 'siswa_sma_aktif'), 'success');
		} else {
			set_message(cclang('error_delete', 'siswa_sma_aktif'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Siswa Sma Aktifs
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('siswa_sma_aktif_view');

		$this->data['siswa_sma_aktif'] = $this->model_siswa_sma_aktif->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Siswa Sma Aktif Detail');
		$this->render('backend/standart/administrator/siswa_sma_aktif/siswa_sma_aktif_view', $this->data);
	}

	/**
	 * Get siswa detail via AJAX (JSON)
	 */
	public function get_detail($id)
	{
		if (!$this->is_allowed('siswa_sma_aktif_view', false)) {
			echo json_encode(['error' => 'Unauthorized']);
			exit;
		}

		$siswa = $this->model_siswa_sma_aktif->join_avaiable()->filter_avaiable()->find($id);

		if ($siswa) {
			echo json_encode($siswa);
		} else {
			echo json_encode(['error' => 'Data tidak ditemukan']);
		}
	}

	/**
	 * delete Siswa Sma Aktifs
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$siswa_sma_aktif = $this->model_siswa_sma_aktif->find($id);



		return $this->model_siswa_sma_aktif->remove($id);
	}


	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('siswa_sma_aktif_export');

		//$this->model_siswa_sma_aktif->export_siswa('siswa_sma_aktif', 'siswa_sma_aktif');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search = ['nama_lengkap', 'nis', 'kelas', 'tahun_ajaran', 'nisn', 'email_ms_office', 'email_ms_office_ortu', 'notelp_ibu', 'notelp_ayah', 'kewarganegaraan', 'nik', 'golongan_darah', 'telp', 'pendidikan_ayah', 'pendidikan_ibu', 'penghasilan_ayah', 'penghasilan_ibu', 'tgl_lahir_ayah', 'tgl_lahir_ibu', 'spp_type', 'acc_ujian', 'spp_custom', 'nomor_peserta_ujian', 'device_id_siswa', 'device_id_ortu', 'file_raport', 'tgl_lahir', 'id_siswa_sma_aktif'];
		$case_spp = "case when sa.spp_custom = '0' then (select biaya_spp from tingkatan_sma ts where ts.id_tingkatan_sma = k.id_tingkatan) else sa.spp_custom end";
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_sma_aktif sa join kelas_sma k on sa.id_kelas = k.id_kelas_sma join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma","result");
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
			$get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_sma_aktif sa join kelas_sma k on sa.id_kelas = k.id_kelas_sma join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma where ".$where,"result");
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
	            $get_data = $this->mymodel->withquery("select sa.*, s.*, k.label as kelas, t.label as tahun_ajaran, (".$case_spp.") as spp_siswa from siswa_sma_aktif sa join kelas_sma k on sa.id_kelas = k.id_kelas_sma join tahun_ajaran t on sa.id_tahun_ajaran = t.id_tahun_ajaran join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma where ".$where,"result");
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
			if (strpos($field_search[$i], "id_siswa_sma_aktif") !== false && $i == 0) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('NO'));
			}
			else if (strpos($field_search[$i], "device_id_siswa") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('SISWA SUDAH PAKAI APLIKASI?'));
			}
			else if (strpos($field_search[$i], "device_id_ortu") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('ORTU SUDAH PAKAI APLIKASI?'));
			}
			else if (strpos($field_search[$i], "tgl_lahir") !== false) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL LAHIR'));
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
		        if (strpos($kolom, "id_siswa_sma_aktif") !== false && $i == 0) {
		        	$value_data = ($key+1);
		        }
				if (strpos($kolom, "file_raport") !== false) {
		        	$value_data = base_url("uploads/siswa_sma_aktif/").$value_data;
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
				if (strpos($kolom, "tgl_lahir") !== false) {
					$value_data = date("d-m-Y", strtotime($value_data));
				}
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Data Siswa SMA Aktif - '.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		if (ob_get_length()) { ob_clean(); }
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('siswa_sma_aktif_export');

		$this->model_siswa_sma_aktif->pdf('siswa_sma_aktif', 'siswa_sma_aktif');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_sma_aktif_export');

		$table = $title = 'siswa_sma_aktif';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_siswa_sma_aktif->find($id);
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

	public function ajax_id_tahun_ajaran($id = null)
	{
		if (!$this->is_allowed('siswa_sma_aktif_list', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$results = db_get_all_data('tahun_ajaran', ['id_tahun_ajaran' => $id]);
		$this->response($results);
	}

	public function ajax_id_kelas($id = null)
	{
		if (!$this->is_allowed('siswa_sma_aktif_list', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$results = db_get_all_data('kelas_sma', ['id_kelas_sma' => $id]);
		$this->response($results);
	}

	public function ajax_id_siswa_sma($id = null)
	{
		if (!$this->is_allowed('siswa_sma_aktif_list', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$results = db_get_all_data('siswa_sma', ['id_siswa_sma' => $id]);
		$this->response($results);
	}
	public function add_raport()
	{
		$this->is_allowed('siswa_sma_aktif_add');

		$this->template->title('Siswa SMA Tambah Raport');
		$this->render('backend/standart/administrator/siswa_sma_aktif/siswa_sma_aktif_add_raport', $this->data);
	}

	public function add_raport_save()
	{
		if (!$this->is_allowed('siswa_sma_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		$ids = $this->input->post('id_siswa_sma');
		$uuid_files = $this->input->post('file_raport_uuid');
		$name_files = $this->input->post('file_raport_name');

		// dd($this->input->post('id_siswa_sma'));

		foreach ($ids as $key => $item) {
			$file_raport_uuid = $uuid_files[$key];
			$file_raport_name = $name_files[$key];

			$save_data = [];

			if (!is_dir(FCPATH . '/uploads/siswa_sma_aktif/')) {
				mkdir(FCPATH . '/uploads/siswa_sma_aktif/');
			}

			if (!empty($file_raport_name)) {
				$file_raport_name_copy = date('YmdHis') . '-' . $file_raport_name;

				rename(
					FCPATH . 'uploads/tmp/' . $file_raport_uuid . '/' . $file_raport_name,
					FCPATH . 'uploads/siswa_sma_aktif/' . $file_raport_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_sma_aktif/' . $file_raport_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['file_raport'] = $file_raport_name_copy;
			}


			$save_siswa_sma_aktif = $this->model_siswa_sma_aktif->change($item, $save_data);
		}


		if ($save_siswa_sma_aktif) {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = true;
				$this->data['id'] 	   = $save_siswa_sma_aktif;
				$this->data['message'] = cclang('success_save_data_stay', [

					anchor('administrator/siswa_sma_aktif', ' Go back to list')
				]);
			} else {
				set_message(
					cclang('success_save_data_redirect', [
						anchor('administrator/siswa_sma_aktif/edit/' . $save_siswa_sma_aktif, 'Edit Siswa Ft')
					]),
					'success'
				);

				$this->data['success'] = true;
				$this->data['redirect'] = base_url('administrator/siswa_sma_aktif');
			}
		} else {
			if ($this->input->post('save_type') == 'stay') {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
			} else {
				$this->data['success'] = false;
				$this->data['message'] = cclang('data_not_change');
				$this->data['redirect'] = base_url('administrator/siswa_sma_aktif');
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
		if (!$this->is_allowed('siswa_sma_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_sma_aktif',
		]);
	}

	/**
	 * Delete Image Siswa Ft	* 
	 * @return JSON
	 */
	public function delete_file_raport_file($uuid)
	{
		if (!$this->is_allowed('siswa_sma_aktif_add', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		echo $this->delete_file([
			'uuid'              => $uuid,
			'delete_by'         => $this->input->get('by'),
			'field_name'        => 'foto_peserta',
			'upload_path_tmp'   => './uploads/tmp/',
			'table_name'        => 'siswa_sma_aktif',
			'primary_key'       => 'id_siswa_sma_aktif',
			'upload_path'       => 'uploads/siswa_sma_aktif/'
		]);
	}

	public function generate_nis()
	{
		$id_tahun_ajaran = $this->input->post("id_tahun_ajaran");
		//get all data di unit SMA , FT & tahun ajaran terpilih diurutkan berdasarkan abjad ASC
		$get_data = $this->mymodel->withquery("select id_siswa_ft_aktif, id_siswa_ft, nis, nama_lengkap from siswa_ft_aktif where id_tahun_ajaran = '".$id_tahun_ajaran."' order by nama_lengkap ASC","result");
		$get_data_sma = $this->mymodel->withquery("select id_siswa_sma_aktif, id_siswa_sma, nis, nama_lengkap from siswa_sma_aktif where id_tahun_ajaran = '".$id_tahun_ajaran."' order by nama_lengkap ASC","result");
		$get_tahun = $this->mymodel->withquery("select code from tahun_ajaran where id_tahun_ajaran = '".$id_tahun_ajaran."'","row")->code;
		$tahun_ajaran = str_replace("_", "", $get_tahun);
		$array_siswa = array();
		foreach ($get_data as $key => $value) {
			$arr_temp = array(
				"id_siswa" => $value->id_siswa_ft,
				"id_siswa_aktif" => $value->id_siswa_ft_aktif,
				"nis" => (!empty($value->nis)) ? $value->nis : "",
				"nama_lengkap" => $value->nama_lengkap,
				"jenjang" => "ft"
			);
			array_push($array_siswa, $arr_temp);
		}
		foreach ($get_data_sma as $key => $value) {
			$arr_temp = array(
				"id_siswa" => $value->id_siswa_sma,
				"id_siswa_aktif" => $value->id_siswa_sma_aktif,
				"nis" => (!empty($value->nis)) ? $value->nis : "",
				"nama_lengkap" => $value->nama_lengkap,
				"jenjang" => "sma"
			);
			array_push($array_siswa, $arr_temp);
		}
		//sort by nama_lengkap ascending
		usort($array_siswa, function($a, $b)
    	{
            if ($a["nama_lengkap"] == $b["nama_lengkap"])
                return (0);
            return (($a["nama_lengkap"] < $b["nama_lengkap"]) ? -1 : 1);
        });
		
		foreach ($array_siswa as $key => $value) {
			//cek apakah nis kosong
			$nis = $tahun_ajaran;
			$kelas_mutasi = sprintf("%02d", 10);
			//cek apakah nis sudah terdaftar di email master siswa (jika terdaftar, maka gunakan nis lama sesuai email ms office nya)
			$cek_master_siswa = $this->mymodel->withquery("select id_siswa_ft, email, email_ms_office, email_ms_office_ortu from siswa_ft where id_siswa_ft = '".$value['id_siswa']."'", "row");
			$cek_master_siswa_sma = $this->mymodel->withquery("select id_siswa_sma, email, email_ms_office, email_ms_office_ortu from siswa_sma where id_siswa_sma = '".$value['id_siswa']."'", "row");
			if ($value['nis'] == "-" || $value['nis'] == "1" || empty($value['nis'])) {
				$no_urut = "";
				if (empty($value['nis']) && $key == 0) {
                  $no_urut = "001";
                }
                else{
                  $no_urut = (int)$key;
                  $no_urut = $no_urut+1;
                  $no_urut = sprintf("%03d", $no_urut);
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
							$cek = $this->mymodel->withquery("select id_siswa_ft_aktif, nis from siswa_ft_aktif where nis = '".$nis."'","result");
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
				else if(!empty($cek_master_siswa_sma) && !empty($cek_master_siswa_sma->email_ms_office)){
					$nis_temp = explode("@",$cek_master_siswa_sma->email_ms_office);
					if ((int)$nis_temp[0] > 0) {
						$nis = $nis_temp[0];
					}
					else{
						//cek apakah nis baru tersedia?
						for ($i=0; $i < count($get_data_sma); $i++) { 
							$cek = $this->mymodel->withquery("select id_siswa_sma_aktif, nis from siswa_sma_aktif where nis = '".$nis."'","result");
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
						$cek = $this->mymodel->withquery("select id_siswa_ft_aktif, nis from siswa_ft_aktif where nis = '".$nis."'","result");
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
					//cek apakah nis baru tersedia?
					for ($i=0; $i < count($get_data_sma); $i++) { 
						$cek = $this->mymodel->withquery("select id_siswa_sma_aktif, nis from siswa_sma_aktif where nis = '".$nis."'","result");
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
				$up = $this->mymodel->update("siswa_sma_aktif",$data_update, "id_siswa_sma_aktif", $value['id_siswa_aktif']);
				//update data siswa
				$data_update2 = array(
					"email_ms_office" => $nis."@labschoolcibubur.sch.id",
					"email_ms_office_ortu" => $nis."p@labschoolcibubur.sch.id"
				);
				$up2 = $this->mymodel->update("siswa_sma",$data_update2, "id_siswa_sma", $value['id_siswa']);
			}
		}
		redirect_back();		
	}

	public function import_siswa()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		$this->db->trans_begin();

		if (isset($_FILES["file_import"]["name"])) {
			$path = $_FILES["file_import"]["tmp_name"];
			$p=$_FILES["file_import"]["name"];
			$ext = pathinfo($p, PATHINFO_EXTENSION);
			if($ext!='xlsx' || $ext!='xls'){
				$this->session->set_flashdata('error', 'Format harus .xlsx atau .xls');
				//redirect($_SERVER['HTTP_REFERER']);
			}
			$object = PHPExcel_IOFactory::load($path);

			foreach ($object->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				$highestColumn = $worksheet->getHighestColumn();
				//$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				for ($row = 2; $row <= $highestRow; $row++) {
					// Helper: ambil nilai cell, kosongkan jika null/empty
					$cellVal = function($col, $row) use ($worksheet) {
						$v = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
						return ($v !== null && $v !== '') ? $v : '';
					};
					$cellInt = function($col, $row) use ($worksheet) {
						$v = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
						return ($v !== null && $v !== '' && is_numeric($v)) ? (int)$v : 0;
					};

					// Kolom (0=NO): 1=nama, 2=nis, ..., 27=tgl_lahir, 28=id_siswa_sma_aktif
					$id_siswa_aktif = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
					
					if (!is_numeric($id_siswa_aktif)) continue;
					
					$get_siswa = $this->mymodel->withquery("select sa.id_siswa_sma_aktif, s.id_siswa_sma, s.nisn from siswa_sma_aktif sa join siswa_sma s on sa.id_siswa_sma = s.id_siswa_sma where sa.id_siswa_sma_aktif = ".(int)$id_siswa_aktif, "row");
					if (!$get_siswa) continue;

					$kelas_text = $cellVal(3, $row);
					$kelas_obj = $this->mymodel->withquery("select id_kelas_sma from kelas_sma where label like '%".$this->db->escape_str($kelas_text)."%'","row");
					$kelas = !empty($kelas_obj) ? $kelas_obj->id_kelas_sma : null;
					
					$ta_text = $cellVal(4, $row);
					$ta_obj = $this->mymodel->withquery("select id_tahun_ajaran from tahun_ajaran where label like '%".$this->db->escape_str($ta_text)."%'","row");
					$tahun_ajaran = !empty($ta_obj) ? $ta_obj->id_tahun_ajaran : null;

					$data_siswa_aktif = array(
						"nama_lengkap" => ucwords($cellVal(1, $row)),
						"nis" => (string)$cellVal(2, $row),
						"id_kelas" => $kelas,
						"id_tahun_ajaran" => $tahun_ajaran,
						"kewarganegaraan" => (string)$cellVal(10, $row),
						"nik" => (string)$cellVal(11, $row),
						"golongan_darah" => (string)$cellVal(12, $row),
						"telp" => (string)$cellVal(13, $row),
						"pendidikan_ayah" => (string)$cellVal(14, $row),
						"pendidikan_ibu" => (string)$cellVal(15, $row),
						"penghasilan_ayah" => $cellInt(16, $row),
						"penghasilan_ibu" => $cellInt(17, $row),
						"tgl_lahir_ayah" => parse_excel_date($worksheet->getCellByColumnAndRow(18, $row)),
						"tgl_lahir_ibu" => parse_excel_date($worksheet->getCellByColumnAndRow(19, $row)),
						"spp_type" => $cellVal(20, $row) ?: 'FULL',
						"acc_ujian" => in_array($worksheet->getCellByColumnAndRow(21, $row)->getValue(), [1, '1', 'Boleh', 'Ya', 'YES', true], true) ? 1 : 0,
						"spp_custom" => $cellInt(22, $row),
						"nomor_peserta_ujian" => (string)$cellVal(23, $row),
					);
					$data_siswa = array(
						"nama_lengkap" => ucwords($cellVal(1, $row)),
						"nisn" => (string)$cellVal(5, $row),
						"email_ms_office" => (string)$cellVal(6, $row),
						"email_ms_office_ortu" => (string)$cellVal(7, $row),
						"notelp_ayah" => str_replace("'", "", (string)$cellVal(9, $row)),
						"notelp_ibu" => str_replace("'", "", (string)$cellVal(8, $row)),
						"tgl_lahir" => parse_excel_date($worksheet->getCellByColumnAndRow(27, $row)),
					);
					//check null
					foreach ($data_siswa_aktif as $key => $item) {
						if($item === null){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong");
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					//check null
					foreach ($data_siswa as $key => $item) {
						if($item === null){
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', "Data ada yang kosong");
							//redirect($_SERVER['HTTP_REFERER']);
						}
					}
					$update_siswa_aktif = $this->mymodel->update("siswa_sma_aktif", $data_siswa_aktif, "id_siswa_sma_aktif", $id_siswa_aktif);
						// dd($data_pendaftaran);
						if (!$update_siswa_aktif) {
							$this->db->trans_rollback();
							//$this->load->library("session");
							$this->session->set_flashdata('error', 'Import program gagal ' . $data_siswa_aktif["nama_lengkap"]);
							// redirect($_SERVER['HTTP_REFERER']);
						}
						else{
							$this->mymodel->update("siswa_sma", $data_siswa, "id_siswa_sma", $get_siswa->id_siswa_sma);
							//update data spp bila tahun ajaran sama
							$this->mymodel->update("spp_sma", array("id_tahun_ajaran" => $tahun_ajaran, "kelas" => $kelas_text, "nama" => $data_siswa_aktif["nama_lengkap"], "nominal" => $data_siswa_aktif["spp_custom"]), "id_tahun_ajaran = '".$tahun_ajaran."' and id_siswa_aktif=", $get_siswa->id_siswa_sma_aktif);
							$this->mymodel->update("transaksi_spp", array("id_tahun_ajaran" => $tahun_ajaran, "user_name" => $data_siswa_aktif["nama_lengkap"], "total_biaya" => $data_siswa_aktif["spp_custom"]), "id_tahun_ajaran='".$tahun_ajaran."' and no_transaksi like '%SMA%' and id_siswa_aktif=", $get_siswa->id_siswa_sma_aktif);
						}
					}
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('success', 'Import data siswa berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function import_raport_siswa(){
		$data=array();
        $total_file = count($_FILES['file_raport']['name']);
        $data_file = array();
        for ($i=0; $i < $total_file; $i++) {
            if (!empty($_FILES['file_raport']['name'][$i])) {
                  $trans_folder = './uploads/siswa_sma_aktif/'; //path htrans
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
                    $up = $this->mymodel->update("siswa_sma_aktif",$data, "nis", $filename_original);
                    $msg = array('success'=>1,'message'=>'Upload File Berhasil');
                    $filenya = array(
                      "file_upload" => base_url("uploads/siswa_sma_aktif/").$filename_no_extension.".pdf",
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
	 * Restore akun siswa (set deleted_at = NULL)
	 */
	public function restore($id = null)
	{
		$this->is_allowed('siswa_sma_aktif_update');

		if (empty($id)) {
			set_message('ID tidak valid', 'error');
			redirect_back();
		}

		$siswa = $this->model_siswa_sma_aktif->find($id);
		if (!$siswa) {
			set_message('Data siswa tidak ditemukan', 'error');
			redirect_back();
		}

		// Set deleted_at = NULL di tabel siswa_sma
		$this->mymodel->update('siswa_sma', array('deleted_at' => NULL), 'id_siswa_sma', $siswa->id_siswa_sma);
		// Set deleted_at = NULL di tabel siswa_sma_aktif
		$this->mymodel->update('siswa_sma_aktif', array('deleted_at' => NULL), 'id_siswa_sma_aktif', $id);

		set_message('Akun siswa berhasil dipulihkan', 'success');
		redirect_back();
	}

	/**
	 * Restore akun ortu (set deleted_at_ortu = NULL)
	 */
	public function restore_ortu($id = null)
	{
		$this->is_allowed('siswa_sma_aktif_update');

		if (empty($id)) {
			set_message('ID tidak valid', 'error');
			redirect_back();
		}

		$siswa = $this->model_siswa_sma_aktif->find($id);
		if (!$siswa) {
			set_message('Data siswa tidak ditemukan', 'error');
			redirect_back();
		}

		// Set deleted_at_ortu = NULL di tabel siswa_sma
		$this->mymodel->update('siswa_sma', array('deleted_at_ortu' => NULL), 'id_siswa_sma', $siswa->id_siswa_sma);
		// Set deleted_at_ortu = NULL di tabel siswa_sma_aktif
		$this->mymodel->update('siswa_sma_aktif', array('deleted_at_ortu' => NULL), 'id_siswa_sma_aktif', $id);

		set_message('Akun ortu berhasil dipulihkan', 'success');
		redirect_back();
	}

}

/* End of file siswa_sma_aktif.php */
/* Location: ./application/controllers/administrator/Siswa Sma Aktif.php */
