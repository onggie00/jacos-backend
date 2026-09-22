<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

/**
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Ft Controller
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Ft site
 *|
 */
class Status_daftar_ulang_ft extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_status_daftar_ulang_ft');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Status Daftar Ulang Fts
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('status_daftar_ulang_ft_list');

		// $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = 72","row");

		// $data_email = array(
		// 	"email" => "dnxdenny@gmail.com",
		// 	"nama_lengkap" => "Deni",
		// 	"tipe_pendaftaran" => "PSB FT",
		// 	"jenjang" => "FT",
		// 	"transaksi" => $get_transaksi,
		// 	"nama_panitia" => "Panitia PSB FT Labschool Cibubur ".date("Y", strtotime("+1 years"))."-".date("Y", strtotime("+2 years")),
		// 	"slip_pembayaran" => 'LDUI-SD-20221019114112-56-santi.pdf',
		//   );

		//   dd($this->send_email_file2("",$data_email['email'],$data_email));

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['status_daftar_ulang_fts'] = $this->model_status_daftar_ulang_ft->get($filter, $field, $this->limit_page, $offset);
		$this->data['status_daftar_ulang_ft_counts'] = $this->model_status_daftar_ulang_ft->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/status_daftar_ulang_ft/index/',
			'total_rows'   => $this->model_status_daftar_ulang_ft->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Status Daftar Ulang Ft List');
		$this->render('backend/standart/administrator/status_daftar_ulang_ft/status_daftar_ulang_ft_list', $this->data);
	}

	/**
	 * Add new status_daftar_ulang_fts
	 *
	 */
	public function add()
	{
		$this->is_allowed('status_daftar_ulang_ft_add');

		$this->template->title('Status Daftar Ulang Ft New');
		$this->render('backend/standart/administrator/status_daftar_ulang_ft/status_daftar_ulang_ft_add', $this->data);
	}

	/**
	 * Add New Status Daftar Ulang Fts
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		if (!$this->is_allowed('status_daftar_ulang_ft_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('id_siswa_ft', 'Id Siswa Ft', 'trim|required|max_length[11]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		$this->form_validation->set_rules('slip_pembayaran', 'Slip Pembayaran', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kwitansi', 'Kwitansi', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('kartu_sementara', 'Kartu Sementara', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('tanggal_lulus', 'Tanggal Lulus', 'trim|required');
		$this->form_validation->set_rules('tgl_daftar_ulang', 'Tgl Daftar Ulang', 'trim|required');


		if ($this->form_validation->run()) {

			$save_data = [
				'id_siswa_ft' => $this->input->post('id_siswa_ft'),
				'status' => $this->input->post('status'),
				'slip_pembayaran' => $this->input->post('slip_pembayaran'),
				'kwitansi' => $this->input->post('kwitansi'),
				'kartu_sementara' => $this->input->post('kartu_sementara'),
				'tanggal_lulus' => $this->input->post('tanggal_lulus'),
				'tgl_daftar_ulang' => $this->input->post('tgl_daftar_ulang'),
			];


			$save_status_daftar_ulang_ft = $this->model_status_daftar_ulang_ft->store($save_data);


			if ($save_status_daftar_ulang_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_status_daftar_ulang_ft;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/status_daftar_ulang_ft/edit/' . $save_status_daftar_ulang_ft, 'Edit Status Daftar Ulang Ft'),
						anchor('administrator/status_daftar_ulang_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
							anchor('administrator/status_daftar_ulang_ft/edit/' . $save_status_daftar_ulang_ft, 'Edit Status Daftar Ulang Ft')
						]),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_ft');
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
	 * Update view Status Daftar Ulang Fts
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('status_daftar_ulang_ft_update');

		$this->data['status_daftar_ulang_ft'] = $this->model_status_daftar_ulang_ft->find($id);
		$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_ft", "id_daftar_ulang", $id, "row");

		$this->template->title('Status Daftar Ulang Ft Update');
		$this->render('backend/standart/administrator/status_daftar_ulang_ft/status_daftar_ulang_ft_update', $this->data);
	}

	/**
	 * Update Status Daftar Ulang Fts
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('status_daftar_ulang_ft_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('status', 'Status', 'trim|required');

		if ($this->form_validation->run()) {

			$save_data = [
				'status' => $this->input->post('status'),
			];


			$save_status_daftar_ulang_ft = $this->model_status_daftar_ulang_ft->change($id, $save_data);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_ft", "id_daftar_ulang", $id, "row");

			if ($this->input->post('status') == 1) {
				$no_transaksi = "LDUI-FT-" . date("Ymd") . "-" . $get_daftar_ulang->id_siswa_ft;
				//update tgl aktivasi
				$this->mymodel->update("status_daftar_ulang_ft", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);

				$customPayment = $this->input->post('custom_total_payment');
				// dd($customPayment);
				if ($customPayment != '0' && $customPayment != '') {
					$total_biaya = $customPayment;
					$this->mymodel->update("status_daftar_ulang_ft", array("custom_payment" => $customPayment), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "FRANCE TRACK", "row");
				} else {
					//get biaya pendaftaran
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "FRANCE TRACK", "row");
					$total_biaya = $get_biaya->nominal_daftar_ulang;
				}

				$get_siswa = $this->mymodel->getbywhere("siswa_ft", "id_siswa_ft", $get_daftar_ulang->id_siswa_ft, "row");
				//buat tagihan uang pangkal saat update menjadi lulus
				$va_number = $get_siswa->va_number;
				$payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
				// if ($payment_response['responseCode'] != '00') {
				// 	$this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));

				// 	echo json_encode([
				// 		'success' => false,
				// 		'message' => $payment_response['errDesc']
				// 		]);
				// 	exit;
				// }
				//masa aktif virtual account
				$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
				foreach ($get_pengaturan_masa_aktif as $key => $item) {
					if ($item->label == 'daftar_ulang') {
						if ($item->tipe_date == 'day') {
							$date_va = ($item->value * 24);
						} else {
							$date_va = $item->value;
						}
					}
				}

				$data_transaksi = array(
					"no_transaksi" => $no_transaksi,
					"nama_bank" => "BNI", //$this->input->post('nama_bank'),
					"user_email" => $get_siswa->email,
					"va_number" => $va_number,
					"user_name" => $get_siswa->nama_lengkap,
					"user_phone" => "",
					"description" => "Tagihan Pendaftaran Ulang FT a.n " . strtoupper($get_siswa->nama_lengkap),
					"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
					"total_biaya" => $total_biaya,
					"status_transaksi" => "0",
					"created_at" => date("Y-m-d H:i:s"),
					"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
				);
				if (!empty($data_transaksi)) {
					$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
					$this->mymodel->update("siswa_ft", array("no_transaksi" => $no_transaksi), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);
					$cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_ft, "tipe_siswa" => "ft", "no_transaksi" => $no_transaksi));
					$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
					//kirim email slip pembayaran
					$data_email = array(
						"email" => $get_siswa->email,
						"nama_lengkap" => $get_siswa->nama_lengkap,
						"tipe_pendaftaran" => "PSB FT",
						"jenjang" => "FT",
						"transaksi" => $get_transaksi,
						"nama_panitia" => "Panitia PSB FT Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
						"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
					);
					$this->mymodel->update("status_daftar_ulang_ft", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);

					$this->send_email_file("", $data_email['email'], $data_email);
				}
			}

			if ($this->input->post('status') == 2) {
				$get_siswa = $this->mymodel->getbywhere("siswa_ft", "id_siswa_ft", $get_daftar_ulang->id_siswa_ft, "row");

				$get_transaksi = $this->mymodel->getbywhere("transaksi", array("no_transaksi" => $get_siswa->no_transaksi), "row");
				$get_transaksi = $get_transaksi[0];
				$this->mymodel->update2("transaksi", array("status_transaksi" => 1, "updated_at" => date("Y-m-d H:i:s")), "no_transaksi", $get_siswa->no_transaksi);
				$trx_id = explode("-", $get_transaksi->no_transaksi);
				$jenis_pembayaran = $trx_id[0];
				$jenjang = $trx_id[1];
				$id_siswa = $trx_id[3];

				//CETAK KARTU SEMENTARA
				$cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'ft', "id_siswa" => $get_daftar_ulang->id_siswa_ft));
				$nama_ortu = $get_siswa->nama_ibu;
				if (empty($get_siswa->nama_ibu)) {
					$nama_ortu = $get_siswa->nama_ayah;
				}
				$jalur = "PSB";


				//CETAK KWITANSI
				$cetak_kwitansi = $this->cetak_kwitansi(array(
					"tipe_siswa" => 'ft',
					"jenjang" => $jenjang,
					"nama_ortu" => $nama_ortu,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"email" => $get_siswa->email,
					"va_number" => $get_transaksi->va_number,
					"total_biaya" => $get_transaksi->total_biaya,
					"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
					"id_siswa" => $get_daftar_ulang->id_siswa_ft,
					"no_transaksi" => $get_transaksi->no_transaksi,
					"jalur" => $jalur,
					"jenis_kwitansi" => "uang pangkal"
				));

				$kartu = 'ft' . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . '.pdf';
				$kwitansi = $get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf';
				$this->mymodel->update("status_daftar_ulang_ft", array("status" => 2, "kwitansi" => $kwitansi, "kartu_sementara" => $kartu, "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);
			}
			if ($save_status_daftar_ulang_ft) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/status_daftar_ulang_ft', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_ft');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_ft');
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
	 * delete Status Daftar Ulang Fts
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('status_daftar_ulang_ft_delete');

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
			set_message(cclang('has_been_deleted', 'status_daftar_ulang_ft'), 'success');
		} else {
			set_message(cclang('error_delete', 'status_daftar_ulang_ft'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Status Daftar Ulang Fts
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('status_daftar_ulang_ft_view');

		$this->data['status_daftar_ulang_ft'] = $this->model_status_daftar_ulang_ft->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Status Daftar Ulang Ft Detail');
		$this->render('backend/standart/administrator/status_daftar_ulang_ft/status_daftar_ulang_ft_view', $this->data);
	}

	/**
	 * delete Status Daftar Ulang Fts
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$status_daftar_ulang_ft = $this->model_status_daftar_ulang_ft->find($id);



		return $this->model_status_daftar_ulang_ft->remove($id);
	}

	public function update_status($id = null)
	{
		$arr_id = $this->input->get('id');
		if (count($arr_id) > 5) {
			set_message('Update aktivasi va maksimal per 5 data', 'error');
			redirect_back();
		}
		foreach ($arr_id as $id) {
			$this->mymodel->update('status_daftar_ulang_ft', array('status' => '1'), 'id_daftar_ulang', $id);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_ft", "id_daftar_ulang", $id, "row");
			$this->mymodel->update("status_daftar_ulang_ft", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);

			$get_siswa = $this->mymodel->getbywhere("siswa_ft", "id_siswa_ft", $get_daftar_ulang->id_siswa_ft, "row");

			$no_transaksi = "LDUI-FT-" . date("Ymd") . "-" . $get_daftar_ulang->id_siswa_ft;
			$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "FRANCE TRACK", "row");
			$tipe = 'PSB FT';
			$total_biaya = $get_biaya->nominal_daftar_ulang;
			//buat tagihan uang pangkal saat update menjadi lulus
			$va_number = $get_siswa->va_number;
			$payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
			// dd($payment_response);
			// if ($payment_response['responseCode'] != '00') {
			// 	$this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));

			// 	set_message($payment_response['errDesc'], 'error');
			// 	redirect_back();
			// }
			//masa aktif virtual account
			$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
			foreach ($get_pengaturan_masa_aktif as $key => $item) {
				if ($item->label == 'daftar_ulang') {
					if ($item->tipe_date == 'day') {
						$date_va = ($item->value * 24);
					} else {
						$date_va = $item->value;
					}
				}
			}

			$data_transaksi = array(
				"no_transaksi" => $no_transaksi,
				"nama_bank" => "BNI", //$this->input->post('nama_bank'),
				"user_email" => $get_siswa->email,
				"va_number" => $va_number,
				"user_name" => $get_siswa->nama_lengkap,
				"user_phone" => "",
				"description" => "Tagihan Pendaftaran Ulang FT a.n " . strtoupper($get_siswa->nama_lengkap),
				"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
				"total_biaya" => $total_biaya,
				"status_transaksi" => "0",
				"created_at" => date("Y-m-d H:i:s"),
				"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
			);
			if (!empty($data_transaksi)) {
				$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
				$this->mymodel->update("siswa_ft", array("no_transaksi" => $no_transaksi), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);
				$cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_ft, "tipe_siswa" => "ft", "no_transaksi" => $no_transaksi));
				$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
				//kirim email slip pembayaran
				$data_email = array(
					"email" => $get_siswa->email,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"tipe_pendaftaran" => $tipe,
					"jenjang" => "FT",
					"transaksi" => $get_transaksi,
					"nama_panitia" => "Panitia " . $tipe . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
					"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
				);
				$this->mymodel->update("status_daftar_ulang_ft", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_ft", $get_daftar_ulang->id_siswa_ft);

				$kirim_email = $this->send_email_file("", $data_email['email'], $data_email);
			}
		}

		set_message('Update status berhasil', 'success');

		redirect_back();
	}
	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('status_daftar_ulang_ft_export');

		$this->model_status_daftar_ulang_ft->export_siswa('status_daftar_ulang_ft', 'status_daftar_ulang_ft');
	}
	public function siswa_aktif()
	{
		$this->db->trans_begin();
		$arr_id = json_decode($this->input->post('ids'));
		$ids = [];
		// dd($arr_id);
		foreach ($arr_id as $key => $item) {
			if ($item->name == 'id[]') {
				$ids[] = $item->value;
			}
		}

		foreach ($ids as $key => $i) {
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_ft", "id_daftar_ulang", $i, "row");
			$get_siswa = $this->mymodel->getbywhere("siswa_ft", "id_siswa_ft", $get_daftar_ulang->id_siswa_ft, "row");
			$data_siswa_aktif = [
				"id_siswa_ft" => $get_daftar_ulang->id_siswa_ft,
				"nama_lengkap" => $get_siswa->nama_lengkap,
				"id_tahun_ajaran" => $this->input->post('tahun_ajaran'),
				"id_kelas" => $this->input->post('kelas_ft'),
				"spp_custom" => $this->input->post('spp_custom')
			];

			$insertAktif = $this->mymodel->insertid("siswa_ft_aktif", $data_siswa_aktif);
			// dd($insertAktif);
			if ($insertAktif == 0) {
				$this->db->trans_rollback();
				$this->load->library("session");
				$this->session->set_flashdata('error', 'Siswa aktif gagal atas nama ' . $get_siswa->nama_lengkap);
				redirect($_SERVER['HTTP_REFERER']);
			}
		}
		$this->db->trans_commit();
		$this->load->library("session");
		$this->session->set_flashdata('success', 'Siswa aktif berhasil');
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('status_daftar_ulang_ft_export');

		$this->model_status_daftar_ulang_ft->pdf('status_daftar_ulang_ft', 'status_daftar_ulang_ft');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('status_daftar_ulang_ft_export');

		$table = $title = 'status_daftar_ulang_ft';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_status_daftar_ulang_ft->find($id);
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

	function create_billing($production, $total, $no_transaksi, $data_user)
	{
		$this->load->library('BniEnc');
		// FROM BNI
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $value) {
			if ($value->name_setting == "bni_client_id_ft") {
				$client_id = $value->value;
			}
			if ($value->name_setting == "bni_secret_key_ft") {
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

		$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		foreach ($get_pengaturan_masa_aktif as $key => $item) {
			if ($item->label == 'daftar_ulang') {
				if ($item->tipe_date == 'day') {
					$date_va = ($item->value * 24) * 3600;
				} else {
					$date_va = ($item->value) * 3600;
				}
			}
		}

		$data_asli = array(
			'type' => "createbilling",
			'client_id' => $client_id,
			'trx_id' => $no_transaksi,
			'trx_amount' => $total,
			'billing_type' => 'o',
			'datetime_expired' => date('c', time() + $date_va), // billing will be expired in 24 hours
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
			// return($response_json);
			return false;
		} else {
			$data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
			// return ($data_response);
			return true;
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
			$endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran_du');
			$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa'], 'no_transaksi' => $get['no_transaksi']);
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
		return true;
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
		$mail->Password = 'b4EyMREFlFOc';
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

	public function send_email_file2($file = "", $to = '', $data)
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
		$mail->Password = 'b4EyMREFlFOc';
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
}


/* End of file status_daftar_ulang_ft.php */
/* Location: ./application/controllers/administrator/Status Daftar Ulang Ft.php */
