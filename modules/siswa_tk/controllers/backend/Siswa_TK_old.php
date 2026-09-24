<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

/**
 *| --------------------------------------------------------------------------
 *| Siswa TK Controller
 *| --------------------------------------------------------------------------
 *| Siswa TK site
 *|
 */
class Siswa_tk_old extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_tk');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Siswa TKs
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_tk_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['siswa_tks'] = $this->model_siswa_tk->get($filter, $field, $this->limit_page, $offset, [], $sort, $sort_type);
		$this->data['siswa_tk_counts'] = $this->model_siswa_tk->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/siswa_tk/index/',
			'total_rows'   => $this->model_siswa_tk->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Siswa SD List');
		$this->render('backend/standart/administrator/siswa_tk/siswa_tk_list', $this->data);
	}

	/**
	 * Add new siswa_tks
	 *
	 */
	public function add()
	{
		$this->is_allowed('siswa_tk_add');

		$this->template->title('Siswa SD New');
		$this->render('backend/standart/administrator/siswa_tk/siswa_tk_add', $this->data);
	}

	/**
	 * Add New Siswa TKs
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		if (!$this->is_allowed('siswa_tk_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms. Office', 'trim|max_length[255]');
		$this->form_validation->set_rules('nisn', 'NISN', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]|alpha');
		$this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('email_ms_office_ortu', 'Email Ms. Office Ortu', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ibu', 'Pekerjaan Ibu', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notelp_ibu', 'No. Telepon Ibu', 'trim|required|max_length[14]');
		$this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ayah', 'Pekerjaan Ayah', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notelp_ayah', 'No. Telepon Ayah', 'trim|required|max_length[14]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|required');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required');
		$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kecamatan', 'Kecamatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kode_pos', 'Kode Pos', 'trim|required|max_length[8]');
		$this->form_validation->set_rules('sekolah_asal', 'Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_foto_peserta_name', 'Foto Peserta', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_akte_lahir_name', 'Akte Lahir', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_kartu_keluarga_name', 'Kartu Keluraga', 'trim|required');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('is_mutasi', 'Mutasi?', 'trim|required');
		$this->form_validation->set_rules('kelas_mutasi', 'Kelas Mutasi', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim|required');
		$this->form_validation->set_rules('no_peserta', 'No Peserta Ujian', 'trim');
		$this->form_validation->set_rules('provinsi_sekolah', 'Provinsi Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kota_sekolah', 'Kota Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kecamatan_sekolah', 'Kecamatan Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kelurahan_sekolah', 'Kelurahan Sekolah Asal', 'trim|required');


		if ($this->form_validation->run()) {
			$siswa_tk_foto_peserta_uuid = $this->input->post('siswa_tk_foto_peserta_uuid');
			$siswa_tk_foto_peserta_name = $this->input->post('siswa_tk_foto_peserta_name');
			$siswa_tk_akte_lahir_uuid = $this->input->post('siswa_tk_akte_lahir_uuid');
			$siswa_tk_akte_lahir_name = $this->input->post('siswa_tk_akte_lahir_name');
			$siswa_tk_kartu_keluarga_uuid = $this->input->post('siswa_tk_kartu_keluarga_uuid');
			$siswa_tk_kartu_keluarga_name = $this->input->post('siswa_tk_kartu_keluarga_name');

			$save_data = [
				'nama_lengkap' => ucwords($this->input->post('nama_lengkap')),
				'email' => $this->input->post('email'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'nisn' => $this->input->post('nisn'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'agama' => $this->input->post('agama'),
				'email_ms_office_ortu' => $this->input->post('email_ms_office_ortu'),
				'nama_ibu' => $this->input->post('nama_ibu'),
				'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu'),
				'notelp_ibu' => $this->input->post('notelp_ibu'),
				'nama_ayah' => $this->input->post('nama_ayah'),
				'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah'),
				'notelp_ayah' => $this->input->post('notelp_ayah'),
				'alamat' => $this->input->post('alamat'),
				'provinsi' => $this->input->post('provinsi'),
				'kelurahan' => $this->input->post('kelurahan'),
				'kecamatan' => $this->input->post('kecamatan'),
				'kota' => $this->input->post('kota'),
				'kode_pos' => $this->input->post('kode_pos'),
				'sekolah_asal' => $this->input->post('sekolah_asal'),
				'sumber_informasi' => $this->input->post('sumber_informasi'),
				'alasan_tertarik' => $this->input->post('alasan_tertarik'),
				'is_mutasi' => $this->input->post('is_mutasi'),
				'kelas_mutasi' => $this->input->post('kelas_mutasi'),
				'status_lulus' => $this->input->post('status_lulus'),
				'no_peserta' => $this->input->post('no_peserta'),
				'no_transaksi' => $this->input->post('no_transaksi'),
				'provinsi_sekolah' => $this->input->post('provinsi_sekolah'),
				'kota_sekolah' => $this->input->post('kota_sekolah'),
				'kecamatan_sekolah' => $this->input->post('kecamatan_sekolah'),
				'kelurahan_sekolah' => $this->input->post('kelurahan_sekolah'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_tk/')) {
				mkdir(FCPATH . '/uploads/siswa_tk/');
			}

			if (!empty($siswa_tk_foto_peserta_name)) {
				$siswa_tk_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_tk_foto_peserta_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_foto_peserta_uuid . '/' . $siswa_tk_foto_peserta_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_foto_peserta_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_tk_foto_peserta_name_copy;
			}

			if (!empty($siswa_tk_akte_lahir_name)) {
				$siswa_tk_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_tk_akte_lahir_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_akte_lahir_uuid . '/' . $siswa_tk_akte_lahir_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_akte_lahir_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_tk_akte_lahir_name_copy;
			}

			if (!empty($siswa_tk_kartu_keluarga_name)) {
				$siswa_tk_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_tk_kartu_keluarga_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_kartu_keluarga_uuid . '/' . $siswa_tk_kartu_keluarga_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_kartu_keluarga_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_tk_kartu_keluarga_name_copy;
			}


			$save_siswa_tk = $this->model_siswa_tk->store($save_data);


			if ($save_siswa_tk) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_tk;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_tk/edit/' . $save_siswa_tk, 'Edit Siswa TK'),
						anchor('administrator/siswa_tk', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
							anchor('administrator/siswa_tk/edit/' . $save_siswa_tk, 'Edit Siswa TK')
						]),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_tk');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_tk');
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
	 * Update view Siswa TKs
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('siswa_tk_update');

		$this->data['siswa_tk'] = $this->model_siswa_tk->find($id);

		$this->template->title('Siswa SD Update');
		$this->render('backend/standart/administrator/siswa_tk/siswa_tk_update', $this->data);
	}

	/**
	 * Update Siswa TKs
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_tk_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		// d
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms. Office', 'trim|max_length[255]');
		$this->form_validation->set_rules('nisn', 'NISN', 'trim|max_length[30]');
		$this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('email_ms_office_ortu', 'Email Ms. Office Ortu', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ibu', 'Pekerjaan Ibu', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notelp_ibu', 'No. Telepon Ibu', 'trim|required|max_length[14]');
		$this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ayah', 'Pekerjaan Ayah', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('notelp_ayah', 'No. Telepon Ayah', 'trim|required|max_length[14]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim|required');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required');
		$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kecamatan', 'Kecamatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kode_pos', 'Kode Pos', 'trim|required|max_length[8]');
		$this->form_validation->set_rules('sekolah_asal', 'Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_foto_peserta_name', 'Foto Peserta', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_akte_lahir_name', 'Akte Lahir', 'trim|required');
		$this->form_validation->set_rules('siswa_tk_kartu_keluarga_name', 'Kartu Keluraga', 'trim|required');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('is_mutasi', 'Mutasi?', 'trim|required');
		$this->form_validation->set_rules('kelas_mutasi', 'Kelas Mutasi', 'trim|max_length[11]');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim|required');
		$this->form_validation->set_rules('no_peserta', 'No Peserta Ujian', 'trim');
		$this->form_validation->set_rules('provinsi_sekolah', 'Provinsi Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kota_sekolah', 'Kota Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kecamatan_sekolah', 'Kecamatan Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('kelurahan_sekolah', 'Kelurahan Sekolah Asal', 'trim|required');

		if ($this->form_validation->run()) {
			$siswa_tk_foto_peserta_uuid = $this->input->post('siswa_tk_foto_peserta_uuid');
			$siswa_tk_foto_peserta_name = $this->input->post('siswa_tk_foto_peserta_name');
			$siswa_tk_akte_lahir_uuid = $this->input->post('siswa_tk_akte_lahir_uuid');
			$siswa_tk_akte_lahir_name = $this->input->post('siswa_tk_akte_lahir_name');
			$siswa_tk_kartu_keluarga_uuid = $this->input->post('siswa_tk_kartu_keluarga_uuid');
			$siswa_tk_kartu_keluarga_name = $this->input->post('siswa_tk_kartu_keluarga_name');

			$save_data = [
				'nama_lengkap' => $this->input->post('nama_lengkap'),
				'email' => $this->input->post('email'),
				'email_ms_office' => $this->input->post('email_ms_office'),
				'nisn' => $this->input->post('nisn'),
				'tempat_lahir' => $this->input->post('tempat_lahir'),
				'tgl_lahir' => $this->input->post('tgl_lahir'),
				'jenis_kelamin' => $this->input->post('jenis_kelamin'),
				'agama' => $this->input->post('agama'),
				'email_ms_office_ortu' => $this->input->post('email_ms_office_ortu'),
				'nama_ibu' => $this->input->post('nama_ibu'),
				'pekerjaan_ibu' => $this->input->post('pekerjaan_ibu'),
				'notelp_ibu' => $this->input->post('notelp_ibu'),
				'nama_ayah' => $this->input->post('nama_ayah'),
				'pekerjaan_ayah' => $this->input->post('pekerjaan_ayah'),
				'notelp_ayah' => $this->input->post('notelp_ayah'),
				'alamat' => $this->input->post('alamat'),
				'provinsi' => $this->input->post('provinsi'),
				'kelurahan' => $this->input->post('kelurahan'),
				'kecamatan' => $this->input->post('kecamatan'),
				'kota' => $this->input->post('kota'),
				'kode_pos' => $this->input->post('kode_pos'),
				'sekolah_asal' => $this->input->post('sekolah_asal'),
				'sumber_informasi' => $this->input->post('sumber_informasi'),
				'alasan_tertarik' => $this->input->post('alasan_tertarik'),
				'is_mutasi' => $this->input->post('is_mutasi'),
				'kelas_mutasi' => $this->input->post('kelas_mutasi'),
				'status_lulus' => $this->input->post('status_lulus'),
				'no_peserta' => $this->input->post('no_peserta'),
				'no_transaksi' => $this->input->post('no_transaksi'),
				'provinsi_sekolah' => $this->input->post('provinsi_sekolah'),
				'kota_sekolah' => $this->input->post('kota_sekolah'),
				'kecamatan_sekolah' => $this->input->post('kecamatan_sekolah'),
				'kelurahan_sekolah' => $this->input->post('kelurahan_sekolah'),
				'va_number' => $this->input->post('va_number'),
				'is_show' => $this->input->post('is_show'),
			];
			// $inq=$this->inquiry_billing("production","LI-SD-20220924160343-90");
			// dd($inq);
			if (!is_dir(FCPATH . '/uploads/siswa_tk/')) {
				mkdir(FCPATH . '/uploads/siswa_tk/');
			}

			if (!empty($siswa_tk_foto_peserta_uuid)) {
				$siswa_tk_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_tk_foto_peserta_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_foto_peserta_uuid . '/' . $siswa_tk_foto_peserta_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_foto_peserta_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_tk_foto_peserta_name_copy;
			}

			if (!empty($siswa_tk_akte_lahir_uuid)) {
				$siswa_tk_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_tk_akte_lahir_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_akte_lahir_uuid . '/' . $siswa_tk_akte_lahir_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_akte_lahir_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_tk_akte_lahir_name_copy;
			}

			if (!empty($siswa_tk_kartu_keluarga_uuid)) {
				$siswa_tk_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_tk_kartu_keluarga_name;

				rename(
					FCPATH . 'uploads/tmp/' . $siswa_tk_kartu_keluarga_uuid . '/' . $siswa_tk_kartu_keluarga_name,
					FCPATH . 'uploads/siswa_tk/' . $siswa_tk_kartu_keluarga_name_copy
				);

				if (!is_file(FCPATH . '/uploads/siswa_tk/' . $siswa_tk_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_tk_kartu_keluarga_name_copy;
			}

			$old_data = $this->mymodel->withquery('select * from siswa_tk where id_siswa_tk=' . $id, 'row');
			$old_transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $old_data->no_transaksi, "row");
			if ($old_transaksi->status_transaksi == 0) {
				if (empty($old_data->no_peserta) && !empty($this->input->post('no_peserta'))) {
					$this->data['success'] = false;
					$this->data['message'] = 'Siswa belum lunas membayar tidak dapat menginput no peserta';
					echo json_encode($this->data);
					exit;
				}
			}
			$save_siswa_tk = $this->model_siswa_tk->change($id, $save_data);
			$new_data = $this->mymodel->withquery('select * from siswa_tk where id_siswa_tk=' . $id, 'row');
			if ($save_siswa_tk) {
				$get_transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $new_data->no_transaksi, "row");
				if ($new_data->is_mutasi == 2) {
					$jenjang = 'SD';
					$jalur = 'PSB';
				} else {
					$jenjang = 'SDM';
					$jalur = 'PSB MUTASI';
				}
				if ($get_transaksi->status_transaksi == 1) { //jika sudah membayar 
					if ($this->input->post('no_peserta') != $old_data->no_peserta || $old_data->foto_peserta != $siswa_tk_foto_peserta_name_copy || $this->input->post('nama_lengkap') != $old_data->nama_lengkap) { // cek jika ada perubahan no peserta
						$trx_id = explode("-", $new_data->no_transaksi);
						$jenis_pembayaran = $trx_id[0];
						if ($jenis_pembayaran == 'LI') { //cetak kartu peserta dan kwitansi ulang
							$this->cetak_kartu(array("tipe_siswa" => 'sd', "id_siswa" => $id));
							$nama_ortu = $new_data->nama_ibu;
							if (empty($new_data->nama_ibu)) {
								$nama_ortu = $new_data->nama_ayah;
							}
							$this->cetak_kwitansi(array(
								"tipe_siswa" => 'sd',
								"jenjang" => $jenjang,
								"nama_ortu" => $nama_ortu,
								"nama_lengkap" => $new_data->nama_lengkap,
								"va_number" => $get_transaksi->va_number,
								"total_biaya" => $get_transaksi->total_biaya,
								"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
								"id_siswa" => $id,
								"no_transaksi" => $new_data->no_transaksi,
								"jalur" => $jalur,
								"jenis_kwitansi" => "uang pendaftaran"
							));
						} else { //cetak kartu peserta sementara dan kwitansi ulang
							$this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'sd', "id_siswa" => $id));
							$this->cetak_kwitansi(array(
								"tipe_siswa" => 'sd',
								"jenjang" => $jenjang,
								"nama_ortu" => $nama_ortu,
								"nama_lengkap" => $new_data->nama_lengkap,
								"va_number" => $get_transaksi->va_number,
								"total_biaya" => $get_transaksi->total_biaya,
								"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
								"id_siswa" => $id,
								"no_transaksi" => $new_data->no_transaksi,
								"jalur" => $jalur,
								"jenis_kwitansi" => "uang pendaftaran"
							));
						}
					}
				}
				if ($new_data->nama_lengkap != $old_data->nama_lengkap) {
					$this->cetak_slip(array("id_siswa" => $id, "tipe_siswa" => "sd"));
				}
			}


			if (!empty($save_siswa_tk) && $save_data['status_lulus'] == "2") {
				$data_daftar_ulang = array(
					"id_siswa_tk" => $id,
					"tanggal_lulus" =>  date("Y-m-d H:i:s")
				);
				// $check_exist_daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_tk","id_siswa_tk",$id,"row");
				// if($check_exist_daftar_ulang){
				// 	$this->data['success'] = false;
				// 	$this->data['message'] = 'Siswa sudah terdaftar pada daftar ulang';
				// 	echo json_encode($this->data);
				// 	exit;
				// }
				$this->mymodel->insertid("status_daftar_ulang_tk", $data_daftar_ulang);

				// $no_transaksi = "LDUI-SD-" . date("YmdHis") . "-" . $id;
				//get biaya pendaftaran
				// $get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD", "row");
				// $total_biaya = $get_biaya->nominal_daftar_ulang;
				// $get_siswa = $this->mymodel->getbywhere("siswa_tk", "id_siswa_tk", $id, "row");

				//buat tagihan uang pangkal saat update menjadi lulus
				// $va_number = $get_siswa->va_number;
				// $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $this->input->post('nama_lengkap'), "email" => $this->input->post('email'), "va_number" => $va_number));
				// $data_transaksi = array(
				// 	"no_transaksi" => $no_transaksi,
				// 	"nama_bank" => "BNI", //$this->input->post('nama_bank'),
				// 	"user_email" => $this->input->post('email'),
				// 	"va_number" => $va_number,
				// 	"user_name" => $this->input->post('nama_lengkap'),
				// 	"user_phone" => "",
				// 	"description" => "Tagihan Pendaftaran Ulang SD a.n " . strtoupper($this->input->post('nama_lengkap')),
				// 	"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
				// 	"total_biaya" => $total_biaya,
				// 	"status_transaksi" => "0",
				// 	"created_at" => date("Y-m-d H:i:s"),
				// 	"expired_datetime" => date("Y-m-d H:i:s", strtotime("+24 hours"))
				// );
				// if (!empty($data_transaksi)) {
				// 	$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
				// 	$this->mymodel->update("siswa_tk", array("no_transaksi" => $no_transaksi), "id_siswa_tk", $id);
				// 	$cetak_slip = $this->cetak_slip(array("id_siswa" => $id, "tipe_siswa" => "sd"));
				// 	$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
				// 	//kirim email slip pembayaran
				// 	$data_email = array(
				// 		"email" => $this->input->post("email"),
				// 		"nama_lengkap" => $this->input->post("nama_lengkap"),
				// 		"tipe_pendaftaran" => "PSB SD",
				// 		"jenjang" => "SD",
				// 		"transaksi" => $get_transaksi,
				// 		"nama_panitia" => "Panitia PSB SD Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
				// 		"slip_pembayaran" => $no_transaksi . '-' . $this->input->post("nama_lengkap") . '.pdf',
				// 	);
				// 	$this->send_email_file("", $data_email['email'], $data_email);
				// }
			}
			if ($save_siswa_tk) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/siswa_tk', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_tk');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_tk');
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
	 * delete Siswa TKs
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('siswa_tk_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		$remove = false;
		// dd($arr_id);
		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) > 0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
			set_message(cclang('has_been_deleted', 'siswa_tk'), 'success');
		} else {
			set_message(cclang('error_delete', 'siswa_tk'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Siswa TKs
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('siswa_tk_view');

		$this->data['siswa_tk'] = $this->model_siswa_tk->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Siswa SD Detail');
		$this->render('backend/standart/administrator/siswa_tk/siswa_tk_view', $this->data);
	}

	/**
	 * delete Siswa TKs
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$siswa_tk = $this->model_siswa_tk->find($id);

		if (!empty($siswa_tk->foto_peserta)) {
			$path = FCPATH . '/uploads/siswa_tk/' . $siswa_tk->foto_peserta;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_tk->akte_lahir)) {
			$path = FCPATH . '/uploads/siswa_tk/' . $siswa_tk->akte_lahir;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_tk->kartu_keluarga)) {
			$path = FCPATH . '/uploads/siswa_tk/' . $siswa_tk->kartu_keluarga;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}


		return $this->model_siswa_tk->remove($id);
	}

	/**
	 * Upload Image Siswa TK	* 
	 * @return JSON
	 */
	public function upload_foto_peserta_file()
	{
		if (!$this->is_allowed('siswa_tk_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_tk',
		]);
	}

	/**
	 * Delete Image Siswa TK	* 
	 * @return JSON
	 */
	public function delete_foto_peserta_file($uuid)
	{
		if (!$this->is_allowed('siswa_tk_delete', false)) {
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
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/'
		]);
	}

	/**
	 * Get Image Siswa TK	* 
	 * @return JSON
	 */
	public function get_foto_peserta_file($id)
	{
		if (!$this->is_allowed('siswa_tk_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
			]);
			exit;
		}

		$siswa_tk = $this->model_siswa_tk->find($id);

		echo $this->get_file([
			'uuid'              => $id,
			'delete_by'         => 'id',
			'field_name'        => 'foto_peserta',
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/',
			'delete_endpoint'   => 'administrator/siswa_tk/delete_foto_peserta_file'
		]);
	}

	/**
	 * Upload Image Siswa TK	* 
	 * @return JSON
	 */
	public function upload_akte_lahir_file()
	{
		if (!$this->is_allowed('siswa_tk_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_tk',
		]);
	}

	/**
	 * Delete Image Siswa TK	* 
	 * @return JSON
	 */
	public function delete_akte_lahir_file($uuid)
	{
		if (!$this->is_allowed('siswa_tk_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		echo $this->delete_file([
			'uuid'              => $uuid,
			'delete_by'         => $this->input->get('by'),
			'field_name'        => 'akte_lahir',
			'upload_path_tmp'   => './uploads/tmp/',
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/'
		]);
	}

	/**
	 * Get Image Siswa TK	* 
	 * @return JSON
	 */
	public function get_akte_lahir_file($id)
	{
		if (!$this->is_allowed('siswa_tk_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
			]);
			exit;
		}

		$siswa_tk = $this->model_siswa_tk->find($id);

		echo $this->get_file([
			'uuid'              => $id,
			'delete_by'         => 'id',
			'field_name'        => 'akte_lahir',
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/',
			'delete_endpoint'   => 'administrator/siswa_tk/delete_akte_lahir_file'
		]);
	}

	/**
	 * Upload Image Siswa TK	* 
	 * @return JSON
	 */
	public function upload_kartu_keluarga_file()
	{
		if (!$this->is_allowed('siswa_tk_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_tk',
		]);
	}

	/**
	 * Delete Image Siswa TK	* 
	 * @return JSON
	 */
	public function delete_kartu_keluarga_file($uuid)
	{
		if (!$this->is_allowed('siswa_tk_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		echo $this->delete_file([
			'uuid'              => $uuid,
			'delete_by'         => $this->input->get('by'),
			'field_name'        => 'kartu_keluarga',
			'upload_path_tmp'   => './uploads/tmp/',
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/'
		]);
	}

	/**
	 * Get Image Siswa TK	* 
	 * @return JSON
	 */
	public function get_kartu_keluarga_file($id)
	{
		if (!$this->is_allowed('siswa_tk_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
			]);
			exit;
		}

		$siswa_tk = $this->model_siswa_tk->find($id);

		echo $this->get_file([
			'uuid'              => $id,
			'delete_by'         => 'id',
			'field_name'        => 'kartu_keluarga',
			'table_name'        => 'siswa_tk',
			'primary_key'       => 'id_siswa_tk',
			'upload_path'       => 'uploads/siswa_tk/',
			'delete_endpoint'   => 'administrator/siswa_tk/delete_kartu_keluarga_file'
		]);
	}


	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('siswa_tk_export');

		$this->model_siswa_tk->export_siswa('siswa_tk', 'siswa_tk');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('siswa_tk_export');

		$this->model_siswa_tk->export_siswa_pdf('siswa_tk', 'siswa_tk');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_tk_export');

		$table = $title = 'siswa_tk';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_siswa_tk->find($id);
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

	function get_content($url, $post = '')
	{
		//$usecookie = __DIR__ . "/cookie.txt";
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		if ($post) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if (empty($rs)) {
			var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		return $rs;
	}

	function create_billing($production, $total, $no_transaksi, $data_user)
	{
		$this->load->library('BniEnc');
		// FROM BNI
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $value) {
			if ($value->name_setting == "bni_client_id") {
				$client_id = $value->value;
			}
			if ($value->name_setting == "bni_secret_key") {
				$secret_key = $value->value;
			}
			if ($value->name_setting == "bni_prefix") {
				$prefix = $value->value;
			}
			if ($production == "production") {
				if ($value->name_setting == "bni_api_prod_url") {
					$url = $value->value;
				}
			} else if ($production == "development" || $production == "testing") {
				if ($value->name_setting == "bni_api_dev_url") {
					$url = $value->value;
				}
			}
		}

		$data_asli = array(
			'type' => "createbilling",
			'client_id' => $client_id,
			'trx_id' => $no_transaksi,
			'trx_amount' => $total,
			'billing_type' => 'c',
			'datetime_expired' => date('c', time() + (24) * 3600), // billing will be expired in 6 hours
			'virtual_account' => $data_user['va_number'],
			'customer_name' => $data_user['nama'],
			'customer_email' => $data_user['email'],
			//'customer_phone' => $data_user['notelp'],
		);

		$hashed_string = BniEnc::encrypt(
			$data_asli,
			$client_id,
			$secret_key
		);

		$data = array(
			'client_id' => $client_id,
			'data' => $hashed_string,
		);

		$response = $this->get_content($url, json_encode($data));
		$response_json = json_decode($response, true);

		if ($response_json['status'] !== '000') {
			// handling jika gagal
			return($response_json);
		} else {
			$data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
			return ($data_response);
		}
	}

	function inquiry_billing($production, $no_transaksi)
	{
		$this->load->library('BniEnc');
		// FROM BNI
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $value) {
			if ($value->name_setting == "bni_client_id") {
				$client_id = $value->value;
			}
			if ($value->name_setting == "bni_secret_key") {
				$secret_key = $value->value;
			}
			if ($value->name_setting == "bni_prefix") {
				$prefix = $value->value;
			}
			if ($production == "production") {
				if ($value->name_setting == "bni_api_prod_url") {
					$url = $value->value;
				}
			} else if ($production == "development" || $production == "testing") {
				if ($value->name_setting == "bni_api_dev_url") {
					$url = $value->value;
				}
			}
		}

		$data_asli = array(
			'type' => "inquirybilling",
			'client_id' => $client_id,
			'trx_id' => $no_transaksi
		);

		$hashed_string = BniEnc::encrypt(
			$data_asli,
			$client_id,
			$secret_key
		);

		$data = array(
			'client_id' => $client_id,
			'data' => $hashed_string,
		);

		$response = $this->get_content($url, json_encode($data));
		$response_json = json_decode($response, true);

		if ($response_json['status'] !== '000') {
			// handling jika gagal
			return($response_json);
		} else {
			$data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
			return ($data_response);
		}
	}

	function cetak_slip($get = '')
	{
		//$usecookie = __DIR__ . "/cookie.txt";
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		if ($get) {
			//export pdf slip
			$endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran');
			$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
			$url = $endpoint . '?' . http_build_query($params);
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if (empty($rs)) {
			// var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		//return $rs;
	}

	function cetak_kartu($get = '')
	{
		//$usecookie = __DIR__ . "/cookie.txt";
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		if ($get) {
			$endpoint = site_url('/apiapp/siswa/export_pdf_siswa');
			$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
			$url = $endpoint . '?id_siswa=' . $get['id_siswa'] . '&tipe_siswa=' . $get['tipe_siswa'];
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if (empty($rs)) {
			var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		//return $rs;
	}

	function cetak_kartu_siswa_sementara($get = '')
	{
		//$usecookie = __DIR__ . "/cookie.txt";
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		if ($get) {
			$endpoint = site_url('/apiapp/siswa/export_kartu_siswa_sementara');
			$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
			$url = $endpoint . '?id_siswa=' . $get['id_siswa'] . '&tipe_siswa=' . $get['tipe_siswa'];
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if (empty($rs)) {
			var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		//return $rs;
	}

	function cetak_kwitansi($get = '')
	{
		//$usecookie = __DIR__ . "/cookie.txt";
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

		if ($get) {
			$endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi');
			//$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
			$url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&tipe_siswa=' . urlencode($get['tipe_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&nama_ortu=' . urlencode($get['nama_ortu']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&jalur=' . urlencode($get['jalur']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&jenis_kwitansi=' . urlencode($get['jenis_kwitansi']);
			curl_setopt($ch, CURLOPT_URL, $url);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$rs = curl_exec($ch);

		if (empty($rs)) {
			var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		return $rs;
	}

	public function send_email_file($file = "", $to = '', $data)
	{
		$to = urldecode($to);
		$mail = new PHPMailer;
		// Konfigurasi SMTP
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		// $mail->Host = 'mail.namagz.com';
		$mail->Host = 'smtp.office365.com';
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@labschoolcibubur.sch.id';
		$mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
		$mail->SMTPSecure = 'tls';
		$mail->Port = 587;

		$mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
		$mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');

		// Menambahkan penerima
		$mail->addAddress($to);

		// Menambahkan beberapa penerima


		// Subjek email
		$mail->Subject = '[No Reply] SLIP PEMBAYARAN PENDAFTARAN ULANG SISWA BARU';

		// Mengatur format email ke HTML
		$mail->isHTML(true);
		//$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
		//$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
		if (!empty($data['slip_pembayaran'])) {
			$mail->AddAttachment('./uploads/slip_pembayaran/' . $data['slip_pembayaran']);
			//$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
		}
		// Konten/isi
		$data_['to'] = $to;
		$data_['nama_lengkap'] = $data['nama_lengkap'];
		$data_['jenjang'] = $data['jenjang'];
		$data_['nama_panitia'] = $data['nama_panitia'];
		$data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
		$data_['transaksi'] = $data['transaksi'];
		$data_['daftar_ulang'] = "1";
		$mailContent = $this->load->view('template_email_pendaftaran', $data_, true);
		$mail->Body = $mailContent;
		// Menambahakn lampiran

		// Kirim email
		if (!$mail->send()) {
			//echo 'Pesan tidak dapat dikirim.';
			//echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			//echo 'Pesan telah terkirim ';
		}
	}
}


/* End of file siswa_tk.php */
/* Location: ./application/controllers/administrator/Siswa TK.php */
