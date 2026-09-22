<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Siswa Sma Controller
*| --------------------------------------------------------------------------
*| Siswa Sma site
*|
*/
class Siswa_sma extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_sma');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Siswa Smas
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_sma_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');

		$this->data['siswa_smas'] = $this->model_siswa_sma->get($filter, $field, $this->limit_page, $offset,[],$sort,$sort_type);
		$this->data['siswa_sma_counts'] = $this->model_siswa_sma->count_all($filter, $field);
		
		// Info box data
		$this->data['total_daftar'] = $this->mymodel->withquery('SELECT COUNT(*) AS cnt FROM siswa_sma', 'row')->cnt;
		$this->data['total_aktif'] = $this->mymodel->withquery('SELECT COUNT(*) AS cnt FROM siswa_sma_aktif', 'row')->cnt;
		$tahun_ajaran_aktif = $this->mymodel->withquery('SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY id_tahun_ajaran DESC LIMIT 1', 'row');
		$this->data['tahun_ajaran_aktif'] = $tahun_ajaran_aktif;
		if ($tahun_ajaran_aktif) {
			$id_ta = $tahun_ajaran_aktif->id_tahun_ajaran;
			$this->data['total_daftar_ta'] = $this->mymodel->withquery('SELECT COUNT(*) AS cnt FROM siswa_sma WHERE id_siswa_sma IN (SELECT id_siswa_sma FROM siswa_sma_aktif WHERE id_tahun_ajaran = ' . (int)$id_ta . ')', 'row')->cnt;
			$this->data['total_aktif_ta'] = $this->mymodel->withquery('SELECT COUNT(*) AS cnt FROM siswa_sma_aktif WHERE id_tahun_ajaran = ' . (int)$id_ta, 'row')->cnt;
		} else {
			$this->data['total_daftar_ta'] = 0;
			$this->data['total_aktif_ta'] = 0;
		}

		$config = [
			'base_url'     => 'administrator/siswa_sma/index/',
			'total_rows'   => $this->model_siswa_sma->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Siswa SMA List');
		$this->render('backend/standart/administrator/siswa_sma/siswa_sma_list', $this->data);
	}
	
	/**
	* Add new siswa_smas
	*
	*/
	public function add()
	{
		$this->is_allowed('siswa_sma_add');

		$this->template->title('Siswa SMA New');
		$this->render('backend/standart/administrator/siswa_sma/siswa_sma_add', $this->data);
	}

	/**
	* Add New Siswa Smas
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms. Office', 'trim|max_length[255]');
		$this->form_validation->set_rules('nisn', 'NISN', 'trim|max_length[30]');
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
		$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kecamatan', 'Kecamatan', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('kode_pos', 'Kode Pos', 'trim|required|max_length[8]');
		$this->form_validation->set_rules('sekolah_asal', 'Sekolah Asal', 'trim|required');
		$this->form_validation->set_rules('siswa_sma_foto_peserta_name', 'Foto Peserta', 'trim|required');
		$this->form_validation->set_rules('siswa_sma_akte_lahir_name', 'Akte Lahir', 'trim|required');
		$this->form_validation->set_rules('siswa_sma_kartu_keluarga_name', 'Kartu Keluraga', 'trim|required');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('peminatan_sma', 'Peminatan', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim|required');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required');
		$this->form_validation->set_rules('jenis_ppsbb', 'Jenis PPSBB', 'trim|required');
		

		if ($this->form_validation->run()) {
			$siswa_sma_foto_peserta_uuid = $this->input->post('siswa_sma_foto_peserta_uuid');
			$siswa_sma_foto_peserta_name = $this->input->post('siswa_sma_foto_peserta_name');
			$siswa_sma_akte_lahir_uuid = $this->input->post('siswa_sma_akte_lahir_uuid');
			$siswa_sma_akte_lahir_name = $this->input->post('siswa_sma_akte_lahir_name');
			$siswa_sma_kartu_keluarga_uuid = $this->input->post('siswa_sma_kartu_keluarga_uuid');
			$siswa_sma_kartu_keluarga_name = $this->input->post('siswa_sma_kartu_keluarga_name');
		
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
				'kelurahan' => $this->input->post('kelurahan'),
				'kecamatan' => $this->input->post('kecamatan'),
				'kota' => $this->input->post('kota'),
				'kode_pos' => $this->input->post('kode_pos'),
				'sekolah_asal' => $this->input->post('sekolah_asal'),
				'sumber_informasi' => $this->input->post('sumber_informasi'),
				'alasan_tertarik' => $this->input->post('alasan_tertarik'),
				'peminatan_sma' => $this->input->post('peminatan_sma'),
				'ppsbb' => $this->input->post('ppsbb'),
				'status_lulus' => $this->input->post('status_lulus'),
				'provinsi' => $this->input->post('provinsi'),
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
				'ppsbb' => ($this->input->post('jenis_ppsbb')==3)?2:1,
			];

			if (!is_dir(FCPATH . '/uploads/siswa_sma/')) {
				mkdir(FCPATH . '/uploads/siswa_sma/');
			}

			if (!empty($siswa_sma_foto_peserta_name)) {
				$siswa_sma_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_sma_foto_peserta_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_foto_peserta_uuid . '/' . $siswa_sma_foto_peserta_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_foto_peserta_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_sma_foto_peserta_name_copy;
			}
		
			if (!empty($siswa_sma_akte_lahir_name)) {
				$siswa_sma_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_sma_akte_lahir_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_akte_lahir_uuid . '/' . $siswa_sma_akte_lahir_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_akte_lahir_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_sma_akte_lahir_name_copy;
			}
		
			if (!empty($siswa_sma_kartu_keluarga_name)) {
				$siswa_sma_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_sma_kartu_keluarga_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_kartu_keluarga_uuid . '/' . $siswa_sma_kartu_keluarga_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_kartu_keluarga_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_sma_kartu_keluarga_name_copy;
			}
		
			
			$save_siswa_sma = $this->model_siswa_sma->store($save_data);
            
			if ($save_siswa_sma) {
				//CREATE TRANSAKSI LUNAS
				//get biaya pendaftaran
				if($save_data['jenis_ppsbb']==3){
					$no_transaksi = "LI-PSBSMA-" . date("Ymd") . "-" . $save_siswa_sma;
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PSB SMA", "row");
				}else{
					$no_transaksi = "LI-PPSBBSMA-" . date("Ymd") . "-" . $save_siswa_sma;
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PPSBB SMA", "row");
				}
				$total_biaya = $get_biaya->nominal_pendaftaran;
				$data_transaksi = array(
					"no_transaksi" => $no_transaksi,
					"nama_bank" => 'BNI',
					"user_email" => $this->input->post('email'),
					"user_name" => $this->input->post('nama_lengkap'),
					"user_phone" => "",
					"description" => "Tagihan Pendaftaran SMA a.n " . strtoupper($this->input->post('nama_lengkap')),
					"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
					"total_biaya" => $total_biaya,
					"status_transaksi" => "1",
					"created_at" => date("Y-m-d H:i:s"),
					"expired_datetime" => date("Y-m-d H:i:s", strtotime("+3 days"))
				);
				$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
				$this->mymodel->update("siswa_sma", array("no_transaksi" => $no_transaksi), "id_siswa_sma", $save_siswa_sma);
			}

			if ($save_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_sma;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_sma/edit/' . $save_siswa_sma, 'Edit Siswa Sma'),
						anchor('administrator/siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/siswa_sma/edit/' . $save_siswa_sma, 'Edit Siswa Sma')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_sma');
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
	* Update view Siswa Smas
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('siswa_sma_update');

		$this->data['siswa_sma'] = $this->model_siswa_sma->find($id);

		$this->template->title('Siswa SMA Update');
		$this->render('backend/standart/administrator/siswa_sma/siswa_sma_update', $this->data);
	}

	/**
	* Update Siswa Smas
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim');
		$this->form_validation->set_rules('email', 'Email', 'trim|max_length[255]');
		$this->form_validation->set_rules('email_ms_office', 'Email Ms. Office', 'trim|max_length[255]');
		$this->form_validation->set_rules('nisn', 'NISN', 'trim|max_length[30]');
		$this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|max_length[100]|alpha');
		$this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|max_length[20]');
		$this->form_validation->set_rules('email_ms_office_ortu', 'Email Ms. Office Ortu', 'trim|max_length[255]');
		$this->form_validation->set_rules('nama_ibu', 'Nama Ibu', 'trim|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ibu', 'Pekerjaan Ibu', 'trim|max_length[255]');
		$this->form_validation->set_rules('notelp_ibu', 'No. Telepon Ibu', 'trim|max_length[14]');
		$this->form_validation->set_rules('nama_ayah', 'Nama Ayah', 'trim|max_length[255]');
		$this->form_validation->set_rules('pekerjaan_ayah', 'Pekerjaan Ayah', 'trim|max_length[255]');
		$this->form_validation->set_rules('notelp_ayah', 'No. Telepon Ayah', 'trim|max_length[14]');
		$this->form_validation->set_rules('alamat', 'Alamat', 'trim');
		$this->form_validation->set_rules('kelurahan', 'Kelurahan', 'trim|max_length[11]');
		$this->form_validation->set_rules('kecamatan', 'Kecamatan', 'trim|max_length[11]');
		$this->form_validation->set_rules('kota', 'Kota', 'trim|max_length[11]');
		$this->form_validation->set_rules('kode_pos', 'Kode Pos', 'trim|max_length[8]');
		$this->form_validation->set_rules('sekolah_asal', 'Sekolah Asal', 'trim');
		//$this->form_validation->set_rules('siswa_sma_foto_peserta_name', 'Foto Peserta', 'trim');
		//$this->form_validation->set_rules('siswa_sma_akte_lahir_name', 'Akte Lahir', 'trim');
		//$this->form_validation->set_rules('siswa_sma_kartu_keluarga_name', 'Kartu Keluraga', 'trim');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|max_length[255]');
		$this->form_validation->set_rules('peminatan_sma', 'Peminatan', 'trim|max_length[100]');
		$this->form_validation->set_rules('ppsbb', 'PPSBB', 'trim');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim');
		
		$get_siswa = $this->mymodel->getbywhere("siswa_sma","id_siswa_sma",$id,"row");

		if($get_siswa->ppsbb=='1'){
			$this->form_validation->set_rules('jenis_ppsbb', 'Jenis PPSBB', 'trim');
		}
		
		if ($this->form_validation->run()) {
			$siswa_sma_foto_peserta_uuid = $this->input->post('siswa_sma_foto_peserta_uuid');
			$siswa_sma_foto_peserta_name = $this->input->post('siswa_sma_foto_peserta_name');
			$siswa_sma_akte_lahir_uuid = $this->input->post('siswa_sma_akte_lahir_uuid');
			$siswa_sma_akte_lahir_name = $this->input->post('siswa_sma_akte_lahir_name');
			$siswa_sma_kartu_keluarga_uuid = $this->input->post('siswa_sma_kartu_keluarga_uuid');
			$siswa_sma_kartu_keluarga_name = $this->input->post('siswa_sma_kartu_keluarga_name');
			

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
				'kelurahan' => $this->input->post('kelurahan'),
				'kecamatan' => $this->input->post('kecamatan'),
				'kota' => $this->input->post('kota'),
				'kode_pos' => $this->input->post('kode_pos'),
				'sekolah_asal' => $this->input->post('sekolah_asal'),
				'sumber_informasi' => $this->input->post('sumber_informasi'),
				'alasan_tertarik' => $this->input->post('alasan_tertarik'),
				'peminatan_sma' => $this->input->post('peminatan_sma'),
				'ppsbb' => $this->input->post('ppsbb'),
				'status_lulus' => $this->input->post('status_lulus'),
				'no_transaksi' => $this->input->post('no_transaksi'),
				'no_peserta' => $this->input->post('no_peserta'),
				'provinsi' => $this->input->post('provinsi'),
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
				'va_number' => $this->input->post('va_number'),
				'is_show' => $this->input->post('is_show'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_sma/')) {
				mkdir(FCPATH . '/uploads/siswa_sma/');
			}

			if (!empty($siswa_sma_foto_peserta_uuid)) {
				$siswa_sma_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_sma_foto_peserta_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_foto_peserta_uuid . '/' . $siswa_sma_foto_peserta_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_foto_peserta_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_sma_foto_peserta_name_copy;
			}
		
			if (!empty($siswa_sma_akte_lahir_uuid)) {
				$siswa_sma_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_sma_akte_lahir_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_akte_lahir_uuid . '/' . $siswa_sma_akte_lahir_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_akte_lahir_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_sma_akte_lahir_name_copy;
			}
		
			if (!empty($siswa_sma_kartu_keluarga_uuid)) {
				$siswa_sma_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_sma_kartu_keluarga_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_sma_kartu_keluarga_uuid . '/' . $siswa_sma_kartu_keluarga_name, 
						FCPATH . 'uploads/siswa_sma/' . $siswa_sma_kartu_keluarga_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_sma/' . $siswa_sma_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_sma_kartu_keluarga_name_copy;
			}
		
			//input kosong tidak menimpa data lama di database
			foreach ($save_data as $key => $val) {
				if ($val === '' || $val === null) {
					unset($save_data[$key]);
				}
			}

			$old_data=$this->mymodel->withquery('select * from siswa_sma where id_siswa_sma='.$id,'row');
			$old_transaksi = $this->mymodel->getbywhere("transaksi","no_transaksi",$old_data->no_transaksi,"row");
			if($old_transaksi->status_transaksi==0){
				if(empty($old_data->no_peserta) && !empty($this->input->post('no_peserta'))){
					$this->data['success'] = false;
					$this->data['message'] = 'Siswa belum lunas membayar tidak dapat menginput no peserta';
					echo json_encode($this->data);
					exit;
				}
			}
			$update_transaksi = array();
			if (isset($save_data['email'])) {
				$update_transaksi['user_email'] = $save_data['email'];
			}
			if (count($update_transaksi)) {
				$this->mymodel->update3('transaksi', $update_transaksi, array('user_email' => $old_data->email),array('no_transaksi' => 'SMA'));
			}
			$save_siswa_sma = $this->model_siswa_sma->change($id, $save_data);
			$new_data=$this->mymodel->withquery('select * from siswa_sma where id_siswa_sma='.$id,'row');
			if ($save_siswa_sma) {
				$get_transaksi = $this->mymodel->getbywhere("transaksi","no_transaksi",$new_data->no_transaksi,"row");
				//if($get_transaksi->status_transaksi==1){ //jika sudah membayar 
					if($this->input->post('no_peserta')!=$old_data->no_peserta){ // cek jika ada perubahan no peserta
						$trx_id = explode("-", $new_data->no_transaksi);
						$jenis_pembayaran = $trx_id[0];
							if($jenis_pembayaran =='LI'){ //cetak kartu peserta dan kwitansi ulang
								$this->cetak_kartu(array("tipe_siswa" => 'sma', "id_siswa" => $id));
								$nama_ortu = $new_data->nama_ibu;
								if (empty($new_data->nama_ibu)) {
								  $nama_ortu = $new_data->nama_ayah;
								}
								$this->cetak_kwitansi(array("tipe_siswa" => 'sma', 
															"jenjang" => 'SMA', 
															"nama_ortu" => $nama_ortu, 
															"nama_lengkap" => $new_data->nama_lengkap, 
															"va_number" => $get_transaksi->va_number, 
															"total_biaya" => $get_transaksi->total_biaya, 
															"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah", 
															"id_siswa" => $id, 
															"no_transaksi" => $new_data->no_transaksi, 
															"jalur" => 'PSB', 
															"jenis_kwitansi" => "uang pendaftaran"));
							}else{ //cetak kartu peserta sementara dan kwitansi ulang
								$this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'sma', "id_siswa" => $id));
								$this->cetak_kwitansi(array("tipe_siswa" => 'sma', 
															"jenjang" => 'SMA', 
															"nama_ortu" => $nama_ortu, 
															"nama_lengkap" => $new_data->nama_lengkap, 
															"va_number" => $get_transaksi->va_number, 
															"total_biaya" => $get_transaksi->total_biaya, 
															"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah", 
															"id_siswa" => $id, 
															"no_transaksi" => $new_data->no_transaksi, 
															"jalur" => 'PSB', 
															"jenis_kwitansi" => "uang pendaftaran"));
		
							}
						
					//}
				}
				if($new_data->nama_lengkap!=$old_data->nama_lengkap){
					$this->cetak_slip(array("id_siswa" => $id, "tipe_siswa" => "sma"));
				}
			}

			if (!empty($save_siswa_sma) && $save_data['status_lulus'] == "2") {
				if($old_data->status_lulus!=2){
					$data_daftar_ulang = array(
						"id_siswa_sma" => $id,
						"tanggal_lulus" =>  date("Y-m-d H:i:s"),
						"tgl_daftar_ulang" =>  $this->input->post('tgl_daftar_ulang')
					);
					$check_exist_daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sma","id_siswa_sma",$id,"row");
					if($check_exist_daftar_ulang){
						$this->data['success'] = false;
						$this->data['message'] = 'Siswa sudah terdaftar pada daftar ulang';
						echo json_encode($this->data);
						exit;
					}
					$this->mymodel->insertid("status_daftar_ulang_sma", $data_daftar_ulang);
				}
			}


			if ($save_siswa_sma) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/siswa_sma', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_sma');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = true;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_sma');
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
	* delete Siswa Smas
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('siswa_sma_delete');

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
            set_message(cclang('has_been_deleted', 'siswa_sma'), 'success');
        } else {
            set_message(cclang('error_delete', 'siswa_sma'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Siswa Smas
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('siswa_sma_view');

		$this->data['siswa_sma'] = $this->model_siswa_sma->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Siswa SMA Detail');
		$this->render('backend/standart/administrator/siswa_sma/siswa_sma_view', $this->data);
	}

	public function view_no_peserta($no_peserta)
	{
		$this->is_allowed('siswa_sma_view');

		$this->data['siswa_sma'] = $this->mymodel->getbywhere("siswa_sma", "no_peserta", $no_peserta, "row");

		$this->template->title('Siswa SMA Detail');
		$this->render('backend/standart/administrator/siswa_sma/siswa_sma_view', $this->data);
	}
	
	/**
	* delete Siswa Smas
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$siswa_sma = $this->model_siswa_sma->find($id);

		if (!empty($siswa_sma->foto_peserta)) {
			$path = FCPATH . '/uploads/siswa_sma/' . $siswa_sma->foto_peserta;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_sma->akte_lahir)) {
			$path = FCPATH . '/uploads/siswa_sma/' . $siswa_sma->akte_lahir;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_sma->kartu_keluarga)) {
			$path = FCPATH . '/uploads/siswa_sma/' . $siswa_sma->kartu_keluarga;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_siswa_sma->remove($id);
	}
	
	public function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	public function update_status_daftar_ulang($id = null)
	{
		$this->db->trans_begin();
		$validate_date=$this->validateDate($this->input->get('tgl_du'));
		if($validate_date==false){
			set_message('Format tanggal tidak sesuai', 'error');
			redirect_back();
		}
		$arr_id = $this->input->get('id');
		foreach ($arr_id as $id) {
			
			$data_daftar_ulang = array(
				"id_siswa_sma" => $id,
				"tanggal_lulus" =>  date("Y-m-d H:i:s"),
				"tgl_daftar_ulang" =>  $this->input->get('tgl_du')
			);
			
			$check_exist_daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sma","id_siswa_sma",$id,"row");
			if($check_exist_daftar_ulang){
				$this->db->trans_rollback();
				set_message('Siswa sudah terdaftar pada daftar ulang', 'error');
				redirect_back();
			}
			$this->mymodel->update('siswa_sma',array('status_lulus'=>$this->input->get('st')),'id_siswa_sma',$id);
			$insert=$this->mymodel->insertid("status_daftar_ulang_sma", $data_daftar_ulang);
			// dd(!$insert);
			if(!$insert){
				$this->db->trans_rollback();
				set_message('Update status gagal', 'error');
				redirect_back();
			}
		}

		$this->db->trans_commit();
		set_message('Update status berhasil', 'success');

		redirect_back();
	}

	public function update_status($id = null)
	{
		$this->db->trans_begin();

		$arr_id = $this->input->get('id');
		foreach ($arr_id as $id) {
			$get_siswa = $this->mymodel->getbywhere("siswa", "id_siswa_sma", $id, "row");
			if($get_siswa->status_lulus==2){
				$get_status_du = $this->mymodel->getbywhere("status_daftar_ulang_sma", "id_siswa_sma", $id, "row");
				if($get_status_du->status==2 || $get_status_du->status==1){
					$this->db->trans_rollback();
					set_message('Siswa sudah terdaftar pada daftar ulang', 'error');
					redirect_back();
				}

			}
			$this->mymodel->update('siswa_sma',array('status_lulus'=>$this->input->get('st')),'id_siswa_sma',$id);
		}

		$this->db->trans_commit();
		set_message('Update status berhasil', 'success');

		redirect_back();
	}

	public function download_file($id = null){
		$arr_id = $this->input->get('id');
		
		$this->load->library('zip');
		$data=[];
		foreach($arr_id as $id){
			$get_siswa = $this->mymodel->getbywhere("siswa_sma", "id_siswa_sma", $id, "row");
			$data[]=[
				"file_foto_path"=>FCPATH.'/uploads/siswa_sma/'.$get_siswa->foto_peserta,
				"file_foto"=>$get_siswa->foto_peserta,
				"file_kk_path"=>FCPATH.'/uploads/siswa_sma/'.$get_siswa->kartu_keluarga,
				"file_kk"=>$get_siswa->kartu_keluarga,
				"file_akte_path"=>FCPATH.'/uploads/siswa_sma/'.$get_siswa->akte_lahir,
				"file_akte"=>$get_siswa->akte_lahir,
				"nama_folder"=>$get_siswa->id_siswa_sma."_".str_replace(" ","_",$get_siswa->nama_lengkap)
			];
		}
		
		foreach($data as $item){
			$this->zip->read_file($item['file_foto_path'],$item['nama_folder']."/foto_siswa/".$item['file_foto']);
			$this->zip->read_file($item['file_kk_path'],$item['nama_folder']."/kartu_keluarga/".$item['file_kk']);
			$this->zip->read_file($item['file_akte_path'],$item['nama_folder']."/akte_kelahiran/".$item['file_akte']);
		}
 
		 // Download
		 $filename = "export_sma_".rand(10,100)."_".date('YmdHis').".zip";
		 $this->zip->download($filename);
	}

	public function download_kartu_peserta($id = null)
	{
		$arr_id = $this->input->get('id');

		$this->load->library('zip');
		$data = [];
		foreach ($arr_id as $id) {
			$get_siswa = $this->mymodel->getbywhere("siswa_sma", "id_siswa_sma", $id, "row");
			$kartu_peserta = 'sma' . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . ".pdf";

			$data[] = [
				"file_kartu_path" => FCPATH.'uploads/kartu_peserta/'  . $kartu_peserta,
				"file_kartu" => $kartu_peserta,
				"nama_folder" => $get_siswa->id_siswa_sma . "_" . str_replace(" ", "_", $get_siswa->nama_lengkap)
			];
		}

		foreach ($data as $item) {
			$this->zip->read_file($item['file_kartu_path'], $item['nama_folder'] . "/" . $item['file_kartu']);
		}

		// Download
		$filename = "export-kartu_sma_" . rand(10, 100) . "_" . date('YmdHis') . ".zip";
		$this->zip->download($filename);
	}
	/**
	* Upload Image Siswa Sma	* 
	* @return JSON
	*/
	public function upload_foto_peserta_file()
	{
		if (!$this->is_allowed('siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_sma',
		]);
	}

	/**
	* Delete Image Siswa Sma	* 
	* @return JSON
	*/
	public function delete_foto_peserta_file($uuid)
	{
		if (!$this->is_allowed('siswa_sma_delete', false)) {
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
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/'
        ]);
	}

	/**
	* Get Image Siswa Sma	* 
	* @return JSON
	*/
	public function get_foto_peserta_file($id)
	{
		if (!$this->is_allowed('siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_sma = $this->model_siswa_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_peserta', 
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/',
            'delete_endpoint'   => 'administrator/siswa_sma/delete_foto_peserta_file'
        ]);
	}
	
	/**
	* Upload Image Siswa Sma	* 
	* @return JSON
	*/
	public function upload_akte_lahir_file()
	{
		if (!$this->is_allowed('siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_sma',
		]);
	}

	/**
	* Delete Image Siswa Sma	* 
	* @return JSON
	*/
	public function delete_akte_lahir_file($uuid)
	{
		if (!$this->is_allowed('siswa_sma_delete', false)) {
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
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/'
        ]);
	}

	function cetak_slip($get = '') {
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
  
		if ($get)
		{
		  $endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran');
		  $params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
		  $url = $endpoint . '?' . http_build_query($params);
		  curl_setopt($ch, CURLOPT_URL, $url);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
		$rs = curl_exec($ch);
  
		if(empty($rs)){
		  var_dump($rs, curl_error($ch));
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
	/**
	* Get Image Siswa Sma	* 
	* @return JSON
	*/
	public function get_akte_lahir_file($id)
	{
		if (!$this->is_allowed('siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_sma = $this->model_siswa_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'akte_lahir', 
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/',
            'delete_endpoint'   => 'administrator/siswa_sma/delete_akte_lahir_file'
        ]);
	}
	function create_billing($production, $total, $no_transaksi, $data_user){
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
		  }
		  else if ($production == "development" || $production == "testing"){
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
		  var_dump($response_json);
		}
		else {
		  $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
		  return($data_response);
		}
	}
	/**
	* Upload Image Siswa Sma	* 
	* @return JSON
	*/
	public function upload_kartu_keluarga_file()
	{
		if (!$this->is_allowed('siswa_sma_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_sma',
		]);
	}

	/**
	* Delete Image Siswa Sma	* 
	* @return JSON
	*/
	public function delete_kartu_keluarga_file($uuid)
	{
		if (!$this->is_allowed('siswa_sma_delete', false)) {
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
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/'
        ]);
	}

	/**
	* Get Image Siswa Sma	* 
	* @return JSON
	*/
	public function get_kartu_keluarga_file($id)
	{
		if (!$this->is_allowed('siswa_sma_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_sma = $this->model_siswa_sma->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'kartu_keluarga', 
            'table_name'        => 'siswa_sma',
            'primary_key'       => 'id_siswa_sma',
            'upload_path'       => 'uploads/siswa_sma/',
            'delete_endpoint'   => 'administrator/siswa_sma/delete_kartu_keluarga_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('siswa_sma_export');
		$value 	= $this->input->get('q');
		$col 	= $this->input->get('f');
		// dd($col);
		$this->model_siswa_sma->export_siswa('siswa_sma', 'siswa_sma',$value,$col);
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('siswa_sma_export');

		$this->model_siswa_sma->export_siswa_pdf('siswa_sma', 'siswa_sma');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_sma_export');

		$table = $title = 'siswa_sma';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_siswa_sma->find($id);
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
	 * Wrapper ke helper parse_excel_date agar reusable.
	 */
	private function parseExcelDate($cell)
	{
		return parse_excel_date($cell);
	}

	public function import()
	{
		// Load plugin PHPExcel nya
		$this->load->library('excel');
		// dd(isset($_FILES["file_siswa"]["name"]));
		$this->db->trans_begin();

		if (isset($_FILES["file_siswa"]["name"])) {
			$path = $_FILES["file_siswa"]["tmp_name"];
			$p=$_FILES["file_siswa"]["name"];
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
				$totalAll = PHPExcel_Cell::columnIndexFromString($highestColumn);

				// if ($totalAll != 22) {
				// 	$this->db->trans_rollback();
				// 	$this->load->library("session");
				// 	$this->session->set_flashdata('error', 'Jumlah kolom tidak sesuai');
				// 	redirect($_SERVER['HTTP_REFERER']);
				// }

				for ($row = 2; $row <= $highestRow; $row++) {
					if (empty($worksheet->getCellByColumnAndRow(2, $row)->getValue()) ) {
						break;
					}
					// inisial variabel
					$jk = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					$kel=$worksheet->getCellByColumnAndRow(13, $row)->getValue();
					$kecamatan=$worksheet->getCellByColumnAndRow(14, $row)->getValue();
					$kota=$worksheet->getCellByColumnAndRow(15, $row)->getValue();
					$provinsi=$worksheet->getCellByColumnAndRow(16, $row)->getValue();

					// check email office and ortu
					$check=$this->mymodel->withquery("select * from siswa_sma where email_ms_office = '".$worksheet->getCellByColumnAndRow(2, $row)->getValue()."'", 'row');
					if(!empty($check)){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Email Siswa ".$worksheet->getCellByColumnAndRow(2, $row)->getValue()." sudah digunakan!");
						redirect($_SERVER['HTTP_REFERER']);
					}
					$check2=$this->mymodel->withquery("select * from siswa_sma where email_ms_office_ortu = '".$worksheet->getCellByColumnAndRow(3, $row)->getValue()."'", 'row');
					if(!empty($check2)){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Email Ortu ".$worksheet->getCellByColumnAndRow(3, $row)->getValue()." sudah digunakan!");
						redirect($_SERVER['HTTP_REFERER']);
					}

					// check data kelurahan kecamatan kota provinsi
					$kel_id=$this->mymodel->withquery("select * from areas where name like '%$kel%'", 'row')->id;
					if($kel == null){
						$kel_id = null;
					}else if(!$kel_id){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Kelurahan $kel tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					
					$kecamatan_id=$this->mymodel->withquery("select * from districts where name like '%$kecamatan%'", 'row')->id;
					if($kecamatan == null){
						$kecamatan_id = null;
					}else if(!$kecamatan_id){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Kecamatan $kecamatan tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					
					$kota_id=$this->mymodel->withquery("select * from regencies where name like '%$kota%'", 'row')->id;
					if($kota == null){
						$kota_id = null;
					}else if(!$kota_id){
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', "Kota $kota tidak ditemukan");
						redirect($_SERVER['HTTP_REFERER']);
					}
					
					$provinsi_id=$this->mymodel->withquery("select * from provinces where name like '%$provinsi%'", 'row')->id;
					if($provinsi == null){
						$provinsi_id = null;
					}else{
						if (!$provinsi_id) {
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('error', "Provinsi $provinsi tidak ditemukan");
							redirect($_SERVER['HTTP_REFERER']);
						}
					}

					$data_pendaftaran = array(
						"nama_lengkap" => ucwords($worksheet->getCellByColumnAndRow(0, $row)->getValue()),
						"email" => $worksheet->getCellByColumnAndRow(1, $row)->getValue(),
						"token" => md5($worksheet->getCellByColumnAndRow(1, $row)->getValue() . " " . date("YmdHis")),
						"email_ms_office" => $worksheet->getCellByColumnAndRow(2, $row)->getValue(),
						"email_ms_office_ortu" => $worksheet->getCellByColumnAndRow(3, $row)->getValue(),
						"jenis_kelamin" => ($jk == 'L') ? 'Laki-laki' : 'Perempuan',
						"agama" => $worksheet->getCellByColumnAndRow(5, $row)->getValue(),
						"nama_ibu" => $worksheet->getCellByColumnAndRow(6, $row)->getValue(),
						"pekerjaan_ibu" => $worksheet->getCellByColumnAndRow(7, $row)->getValue(),
						"notelp_ibu" => $worksheet->getCellByColumnAndRow(8, $row)->getValue(),
						"nama_ayah" => $worksheet->getCellByColumnAndRow(9, $row)->getValue(),
						"pekerjaan_ayah" => $worksheet->getCellByColumnAndRow(10, $row)->getValue(),
						"notelp_ayah" => $worksheet->getCellByColumnAndRow(11, $row)->getValue(),
						"alamat" => $worksheet->getCellByColumnAndRow(12, $row)->getValue(),
						"kelurahan" =>$kel_id,
						"kecamatan" => $kecamatan_id,
						"kota" => $kota_id,
						"provinsi" => $provinsi_id,
						"kode_pos" => $worksheet->getCellByColumnAndRow(17, $row)->getValue(),
						"sekolah_asal" => $worksheet->getCellByColumnAndRow(18, $row)->getValue(),
						"sumber_informasi" => $worksheet->getCellByColumnAndRow(19, $row)->getValue(),
						"alasan_tertarik" => $worksheet->getCellByColumnAndRow(20, $row)->getValue(),
						"status_lulus" => 2,
						"token_expired" => date("Y-m-d H:i:s", strtotime("+3 days")),
						"jenis_ppsbb" => $worksheet->getCellByColumnAndRow(21, $row)->getValue(),
						"tempat_lahir" => $worksheet->getCellByColumnAndRow(22, $row)->getValue(),
						"tgl_lahir" => parse_excel_date($worksheet->getCellByColumnAndRow(23, $row)),
						"nisn" => $worksheet->getCellByColumnAndRow(24, $row)->getValue(),
						'ppsbb' => ($worksheet->getCellByColumnAndRow(21, $row)->getValue()==3)?2:1,
						"peminatan_sma" => $worksheet->getCellByColumnAndRow(25, $row)->getValue(),
						'no_peserta' => '',
						'foto_peserta' => '',
						'akte_lahir' => '',
						'kartu_keluarga' => '',
						'no_transaksi' => '',
						'va_number' => '',
					);
					$nis=$worksheet->getCellByColumnAndRow(26, $row)->getValue();
															//check null
					// cek($data_pendaftaran, true);
//					foreach ($data_pendaftaran as $key => $item) {
//							// Skip field yang boleh kosong
//							if(in_array($key, array('no_peserta', 'tempat_lahir', 'nama_ibu', 'pekerjaan_ibu', 'notelp_ibu', 'nama_ayah', 'pekerjaan_ayah', 'notelp_ayah', 'sumber_informasi', 'alasan_tertarik'))) continue;
//						if(!$item){
//							$this->db->trans_rollback();
//							$this->load->library("session");
//							$this->session->set_flashdata('error', "Data ada yang kosong baris ".$row." : $key");
//							redirect($_SERVER['HTTP_REFERER']);
//						}
//					}

					$check=$this->mymodel->withquery("select * from siswa_sma where nama_lengkap like '%".ucwords($worksheet->getCellByColumnAndRow(0, $row)->getValue())."%'", 'row');
					if(!empty($check)){
						$insertId = $this->mymodel->update("siswa_sma", $data_pendaftaran, 'id_siswa_sma', $check->id_siswa_sma);
						$insertId = $check->id_siswa_sma;
					}else{
						$insertId = $this->mymodel->insertid("siswa_sma", $data_pendaftaran);
					}

					// dd($data_pendaftaran);
					if ($insertId == 0) {
						$this->db->trans_rollback();
						$this->load->library("session");
						$this->session->set_flashdata('error', 'Import siswa gagal atas nama ' . $data_pendaftaran["nama_lengkap"]." ".$this->db->last_query());
						redirect($_SERVER['HTTP_REFERER']);
					}
					if(empty($check)){
						if($data_pendaftaran['ppsbb']==1){
							$no_transaksi = "LI-PPSBBSMA-" . date("Ymd") . "-" . $insertId;
							$jenjang='PSB SMA';
						}else{
							$no_transaksi = "LI-PSBSMA-" . date("Ymd") . "-" . $insertId;
							$jenjang='PPSBB SMA';
	
						}
						//CREATE TRANSAKSI LUNAS
						
						//get biaya pendaftaran
						$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", $jenjang, "row");
						$total_biaya = $get_biaya->nominal_pendaftaran;
						$data_transaksi = array(
							"no_transaksi" => $no_transaksi,
							"nama_bank" => 'BNI',
							"user_email" => $data_pendaftaran['email'],
							"user_name" => $data_pendaftaran['nama_lengkap'],
							"user_phone" => "",
							"description" => "Tagihan Pendaftaran SMA a.n " . strtoupper($data_pendaftaran['nama_lengkap']),
							"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
							"total_biaya" => $total_biaya,
							"status_transaksi" => "1",
							"created_at" => date("Y-m-d H:i:s"),
							"expired_datetime" => date("Y-m-d H:i:s", strtotime("+3 days")),
							"is_show" => 1,
							"payment_amount" => 0
						);
						
						$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
						$this->mymodel->update("siswa_sma", array("no_transaksi" => $no_transaksi), "id_siswa_sma", $insertId);
	
						$data_siswa_aktif = [
							"id_siswa_sma" => $insertId,
							"nama_lengkap" => $data_pendaftaran["nama_lengkap"],
							"nis" => $nis,
							"id_tahun_ajaran" => $this->input->post('tahun_ajaran'),
							"id_kelas" => $this->input->post('kelas_sma'),
							"acc_ujian" => 0,
							"is_active" => 1,
							"spp_custom" => 0
						];
	
						$insertAktif = $this->mymodel->insertid("siswa_sma_aktif", $data_siswa_aktif);
						// dd($insertAktif);
						if ($insertAktif == 0) {
							$this->db->trans_rollback();
							$this->load->library("session");
							$this->session->set_flashdata('error', 'Import siswa gagal atas nama ' . $data_siswa_aktif["nama_lengkap"]);
							redirect($_SERVER['HTTP_REFERER']);
						}
					}
				}
			}
			$this->db->trans_commit();
			$this->load->library("session");
			$this->session->set_flashdata('success', 'Import siswa berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
}


/* End of file siswa_sma.php */
/* Location: ./application/controllers/administrator/Siswa Sma.php */