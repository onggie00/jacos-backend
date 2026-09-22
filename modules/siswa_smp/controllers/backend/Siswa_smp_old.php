<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Siswa Smp Controller
*| --------------------------------------------------------------------------
*| Siswa Smp site
*|
*/
class Siswa_smp_old extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_siswa_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Siswa Smps
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('siswa_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['siswa_smps'] = $this->model_siswa_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['siswa_smp_counts'] = $this->model_siswa_smp->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/siswa_smp/index/',
			'total_rows'   => $this->model_siswa_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Siswa SMP List');
		$this->render('backend/standart/administrator/siswa_smp/siswa_smp_list', $this->data);
	}
	
	/**
	* Add new siswa_smps
	*
	*/
	public function add()
	{
		$this->is_allowed('siswa_smp_add');

		$this->template->title('Siswa SMP New');
		$this->render('backend/standart/administrator/siswa_smp/siswa_smp_add', $this->data);
	}

	/**
	* Add New Siswa Smps
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('siswa_smp_add', false)) {
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
		$this->form_validation->set_rules('tempat_lahir', 'Tempat Lahir', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('tgl_lahir', 'Tanggal Lahir', 'trim|required');
		$this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'trim|required');
		$this->form_validation->set_rules('agama', 'Agama', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('email_ms_office_ortu', 'Email Ms. Office Ortu', 'trim|required|max_length[255]');
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
		$this->form_validation->set_rules('siswa_smp_foto_peserta_name', 'Foto Peserta', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_akte_lahir_name', 'Akte Lahir', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_kartu_keluarga_name', 'Kartu Keluraga', 'trim|required');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim|required');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required');
		$this->form_validation->set_rules('jenis_ppsbb', 'Jenis PPSBB', 'trim|required');
		

		if ($this->form_validation->run()) {
			$siswa_smp_foto_peserta_uuid = $this->input->post('siswa_smp_foto_peserta_uuid');
			$siswa_smp_foto_peserta_name = $this->input->post('siswa_smp_foto_peserta_name');
			$siswa_smp_akte_lahir_uuid = $this->input->post('siswa_smp_akte_lahir_uuid');
			$siswa_smp_akte_lahir_name = $this->input->post('siswa_smp_akte_lahir_name');
			$siswa_smp_kartu_keluarga_uuid = $this->input->post('siswa_smp_kartu_keluarga_uuid');
			$siswa_smp_kartu_keluarga_name = $this->input->post('siswa_smp_kartu_keluarga_name');
		
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
				'status_lulus' => $this->input->post('status_lulus'),
				'no_transaksi' => $this->input->post('no_transaksi'),
				'provinsi' => $this->input->post('provinsi'),
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_smp/')) {
				mkdir(FCPATH . '/uploads/siswa_smp/');
			}

			if (!empty($siswa_smp_foto_peserta_name)) {
				$siswa_smp_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_smp_foto_peserta_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_foto_peserta_uuid . '/' . $siswa_smp_foto_peserta_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_foto_peserta_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_smp_foto_peserta_name_copy;
			}
		
			if (!empty($siswa_smp_akte_lahir_name)) {
				$siswa_smp_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_smp_akte_lahir_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_akte_lahir_uuid . '/' . $siswa_smp_akte_lahir_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_akte_lahir_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_smp_akte_lahir_name_copy;
			}
		
			if (!empty($siswa_smp_kartu_keluarga_name)) {
				$siswa_smp_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_smp_kartu_keluarga_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_kartu_keluarga_uuid . '/' . $siswa_smp_kartu_keluarga_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_kartu_keluarga_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_smp_kartu_keluarga_name_copy;
			}
		
			
			$save_siswa_smp = $this->model_siswa_smp->store($save_data);
            

			if ($save_siswa_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_siswa_smp;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/siswa_smp/edit/' . $save_siswa_smp, 'Edit Siswa Smp'),
						anchor('administrator/siswa_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/siswa_smp/edit/' . $save_siswa_smp, 'Edit Siswa Smp')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_smp');
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
	* Update view Siswa Smps
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('siswa_smp_update');

		$this->data['siswa_smp'] = $this->model_siswa_smp->find($id);

		$this->template->title('Siswa SMP Update');
		$this->render('backend/standart/administrator/siswa_smp/siswa_smp_update', $this->data);
	}

	/**
	* Update Siswa Smps
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('siswa_smp_update', false)) {
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
		$this->form_validation->set_rules('siswa_smp_foto_peserta_name', 'Foto Peserta', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_akte_lahir_name', 'Akte Lahir', 'trim|required');
		$this->form_validation->set_rules('siswa_smp_kartu_keluarga_name', 'Kartu Keluraga', 'trim|required');
		$this->form_validation->set_rules('sumber_informasi', 'Sumber Informasi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('alasan_tertarik', 'Alasan Tertarik', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('status_lulus', 'Status Lulus', 'trim|required');
		$this->form_validation->set_rules('provinsi', 'Provinsi', 'trim|required');
		$this->form_validation->set_rules('jenis_ppsbb', 'Jenis PPSBB', 'trim|required');
		
		if ($this->form_validation->run()) {
			$siswa_smp_foto_peserta_uuid = $this->input->post('siswa_smp_foto_peserta_uuid');
			$siswa_smp_foto_peserta_name = $this->input->post('siswa_smp_foto_peserta_name');
			$siswa_smp_akte_lahir_uuid = $this->input->post('siswa_smp_akte_lahir_uuid');
			$siswa_smp_akte_lahir_name = $this->input->post('siswa_smp_akte_lahir_name');
			$siswa_smp_kartu_keluarga_uuid = $this->input->post('siswa_smp_kartu_keluarga_uuid');
			$siswa_smp_kartu_keluarga_name = $this->input->post('siswa_smp_kartu_keluarga_name');
		
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
				'status_lulus' => $this->input->post('status_lulus'),
				'no_transaksi' => $this->input->post('no_transaksi'),
				'no_peserta' => $this->input->post('no_peserta'),
				'provinsi' => $this->input->post('provinsi'),
				'jenis_ppsbb' => $this->input->post('jenis_ppsbb'),
				'va_number' => $this->input->post('va_number'),
			];

			if (!is_dir(FCPATH . '/uploads/siswa_smp/')) {
				mkdir(FCPATH . '/uploads/siswa_smp/');
			}

			if (!empty($siswa_smp_foto_peserta_uuid)) {
				$siswa_smp_foto_peserta_name_copy = date('YmdHis') . '-' . $siswa_smp_foto_peserta_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_foto_peserta_uuid . '/' . $siswa_smp_foto_peserta_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_foto_peserta_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_foto_peserta_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['foto_peserta'] = $siswa_smp_foto_peserta_name_copy;
			}
		
			if (!empty($siswa_smp_akte_lahir_uuid)) {
				$siswa_smp_akte_lahir_name_copy = date('YmdHis') . '-' . $siswa_smp_akte_lahir_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_akte_lahir_uuid . '/' . $siswa_smp_akte_lahir_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_akte_lahir_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_akte_lahir_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['akte_lahir'] = $siswa_smp_akte_lahir_name_copy;
			}
		
			if (!empty($siswa_smp_kartu_keluarga_uuid)) {
				$siswa_smp_kartu_keluarga_name_copy = date('YmdHis') . '-' . $siswa_smp_kartu_keluarga_name;

				rename(FCPATH . 'uploads/tmp/' . $siswa_smp_kartu_keluarga_uuid . '/' . $siswa_smp_kartu_keluarga_name, 
						FCPATH . 'uploads/siswa_smp/' . $siswa_smp_kartu_keluarga_name_copy);

				if (!is_file(FCPATH . '/uploads/siswa_smp/' . $siswa_smp_kartu_keluarga_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['kartu_keluarga'] = $siswa_smp_kartu_keluarga_name_copy;
			}
		
			$old_no_urut=$this->mymodel->withquery('select * from siswa_smp where id_siswa_smp='.$id,'row');
			$old_transaksi = $this->mymodel->getbywhere("transaksi","no_transaksi",$old_no_urut->no_transaksi,"row");
			if($old_transaksi->status_transaksi==0){
				if(empty($old_no_urut->no_peserta) && !empty($this->input->post('no_peserta'))){
					$this->data['success'] = false;
					$this->data['message'] = 'Siswa belum lunas membayar tidak dapat menginput no peserta';
					echo json_encode($this->data);
					exit;
				}
			}
			$save_siswa_smp = $this->model_siswa_smp->change($id, $save_data);
			$new_data=$this->mymodel->withquery('select * from siswa_smp where id_siswa_smp='.$id,'row');
			if ($save_siswa_smp) {
				$get_transaksi = $this->mymodel->getbywhere("transaksi","no_transaksi",$new_data->no_transaksi,"row");
				if($get_transaksi->status_transaksi==1){ //jika sudah membayar 
					if($this->input->post('no_peserta')!=$old_no_urut->no_peserta){ // cek jika ada perubahan no peserta
						$trx_id = explode("-", $new_data->no_transaksi);
						$jenis_pembayaran = $trx_id[0];
							if($jenis_pembayaran =='LI'){ //cetak kartu peserta dan kwitansi ulang
								$this->cetak_kartu(array("tipe_siswa" => 'smp', "id_siswa" => $id));
								$nama_ortu = $new_data->nama_ibu;
								if (empty($new_data->nama_ibu)) {
								  $nama_ortu = $new_data->nama_ayah;
								}
								$this->cetak_kwitansi(array("tipe_siswa" => 'smp', 
															"jenjang" => 'SMP', 
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
								$this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'smp', "id_siswa" => $id));
								$this->cetak_kwitansi(array("tipe_siswa" => 'smp', 
															"jenjang" => 'SMP', 
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
						
					}
				}
				if($new_data->nama_lengkap!=$old_no_urut->nama_lengkap){
					$this->cetak_slip(array("id_siswa" => $id, "tipe_siswa" => "smp"));
				}
			}

			if ($save_siswa_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/siswa_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/siswa_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/siswa_smp');
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
	* delete Siswa Smps
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('siswa_smp_delete');

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
            set_message(cclang('has_been_deleted', 'siswa_smp'), 'success');
        } else {
            set_message(cclang('error_delete', 'siswa_smp'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Siswa Smps
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('siswa_smp_view');

		$this->data['siswa_smp'] = $this->model_siswa_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Siswa SMP Detail');
		$this->render('backend/standart/administrator/siswa_smp/siswa_smp_view', $this->data);
	}
	
	/**
	* delete Siswa Smps
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$siswa_smp = $this->model_siswa_smp->find($id);

		if (!empty($siswa_smp->foto_peserta)) {
			$path = FCPATH . '/uploads/siswa_smp/' . $siswa_smp->foto_peserta;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_smp->akte_lahir)) {
			$path = FCPATH . '/uploads/siswa_smp/' . $siswa_smp->akte_lahir;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		if (!empty($siswa_smp->kartu_keluarga)) {
			$path = FCPATH . '/uploads/siswa_smp/' . $siswa_smp->kartu_keluarga;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_siswa_smp->remove($id);
	}
	
	/**
	* Upload Image Siswa Smp	* 
	* @return JSON
	*/
	public function upload_foto_peserta_file()
	{
		if (!$this->is_allowed('siswa_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_smp',
		]);
	}

	/**
	* Delete Image Siswa Smp	* 
	* @return JSON
	*/
	public function delete_foto_peserta_file($uuid)
	{
		if (!$this->is_allowed('siswa_smp_delete', false)) {
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
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/'
        ]);
	}

	/**
	* Get Image Siswa Smp	* 
	* @return JSON
	*/
	public function get_foto_peserta_file($id)
	{
		if (!$this->is_allowed('siswa_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_smp = $this->model_siswa_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'foto_peserta', 
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/',
            'delete_endpoint'   => 'administrator/siswa_smp/delete_foto_peserta_file'
        ]);
	}
	
	/**
	* Upload Image Siswa Smp	* 
	* @return JSON
	*/
	public function upload_akte_lahir_file()
	{
		if (!$this->is_allowed('siswa_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_smp',
		]);
	}

	/**
	* Delete Image Siswa Smp	* 
	* @return JSON
	*/
	public function delete_akte_lahir_file($uuid)
	{
		if (!$this->is_allowed('siswa_smp_delete', false)) {
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
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/'
        ]);
	}

	/**
	* Get Image Siswa Smp	* 
	* @return JSON
	*/
	public function get_akte_lahir_file($id)
	{
		if (!$this->is_allowed('siswa_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_smp = $this->model_siswa_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'akte_lahir', 
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/',
            'delete_endpoint'   => 'administrator/siswa_smp/delete_akte_lahir_file'
        ]);
	}
	
	/**
	* Upload Image Siswa Smp	* 
	* @return JSON
	*/
	public function upload_kartu_keluarga_file()
	{
		if (!$this->is_allowed('siswa_smp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'siswa_smp',
		]);
	}

	/**
	* Delete Image Siswa Smp	* 
	* @return JSON
	*/
	public function delete_kartu_keluarga_file($uuid)
	{
		if (!$this->is_allowed('siswa_smp_delete', false)) {
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
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/'
        ]);
	}

	/**
	* Get Image Siswa Smp	* 
	* @return JSON
	*/
	public function get_kartu_keluarga_file($id)
	{
		if (!$this->is_allowed('siswa_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$siswa_smp = $this->model_siswa_smp->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'kartu_keluarga', 
            'table_name'        => 'siswa_smp',
            'primary_key'       => 'id_siswa_smp',
            'upload_path'       => 'uploads/siswa_smp/',
            'delete_endpoint'   => 'administrator/siswa_smp/delete_kartu_keluarga_file'
        ]);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('siswa_smp_export');

		$this->model_siswa_smp->export_siswa('siswa_smp', 'siswa_smp');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('siswa_smp_export');

		$this->model_siswa_smp->export_siswa_pdf('siswa_smp', 'siswa_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('siswa_smp_export');

		$table = $title = 'siswa_smp';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_siswa_smp->find($id);
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


/* End of file siswa_smp.php */
/* Location: ./application/controllers/administrator/Siswa Smp.php */