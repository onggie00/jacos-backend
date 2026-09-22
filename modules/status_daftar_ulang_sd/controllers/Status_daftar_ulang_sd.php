<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

/**
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Sd Controller
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Sd site
 *|
 */
class Status_daftar_ulang_sd extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_status_daftar_ulang_sd');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Status Daftar Ulang Sds
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('status_daftar_ulang_sd_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['status_daftar_ulang_sds'] = $this->model_status_daftar_ulang_sd->get($filter, $field, $this->limit_page, $offset);
		$this->data['status_daftar_ulang_sd_counts'] = $this->model_status_daftar_ulang_sd->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/status_daftar_ulang_sd/index/',
			'total_rows'   => $this->model_status_daftar_ulang_sd->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Status Daftar Ulang Sd List');
		$this->render('backend/standart/administrator/status_daftar_ulang_sd/status_daftar_ulang_sd_list', $this->data);
	}


	/**
	 * Update view Status Daftar Ulang Sds
	 *
	 * @var $id String
	 */
	public function edit($id, $date = null)
	{
		$this->is_allowed('status_daftar_ulang_sd_update');

		$this->model_status_daftar_ulang_sd->join_avaiable();

		$this->data['status_daftar_ulang_sd'] = $this->model_status_daftar_ulang_sd->find($id);

		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
				if ($item->name_setting == 'bri_dev_url') {
					$url = $item->value;
				}
			} else if (ENVIRONMENT == 'production') {
				if ($item->name_setting == 'bri_prod_url') {
					$url = $item->value;
				}
			}
			if ($item->name_setting == 'bri_client_id') {
				$clientID = $item->value;
			}
			if ($item->name_setting == 'bri_client_secret') {
				$clientSecret = $item->value;
			}
			if ($item->name_setting == 'bri_institution_code') {
				$institutionCode = $item->value;
			}
		}

		// $clientID     = "GLlLSkSt0mjiBc2S95UNWyEfi2vvmxCI";
		// $clientSecret = "W0FaYYRRwP4bDwbO";
		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";
		// $institutionCode = "J104408"; //This institution code will be given by BRI

		if ($date == null) {
			$date = date('Ymd');
		}
		$brivaNo = substr($this->data['status_daftar_ulang_sd']->va_bri, 0, 5);
		$custCode = substr($this->data['status_daftar_ulang_sd']->va_bri, 5);
		$datas = array(
			'brivaNo' => $brivaNo,
			'custCode' => $custCode,
			'startDate' => $date,
			'endDate' => $date
		);
		// $arr=BriApi::getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$datas);
		// if($arr['responseCode']=='00'){
		// 	foreach($arr['data'] as $key => $item){
		// 		if($item['custCode']==$custCode){
		// 			$arrBri[]=$item;
		// 		}
		// 	}
		// }

		$total_pay = 0;
		$arrBri = [];

		$va = $this->data['status_daftar_ulang_sd']->va_bri;
		// $va='2147483647';	
		$riwayat = $this->mymodel->getbywhere("payment_response_bri", "briva_no", $va, "result");

		foreach ($riwayat as $key => $i) {
			$total_pay += $i->bill_amount;
			$arrBri[] = $i;
		}
		// $detailBri=BriApi::getData($clientID,$clientSecret,$url,$endpoint,$institutionCode,$datas);

		// if($detailBri['responseCode']=='00'){
		// 	$this->data['detail_bri'] = $detailBri['data'];
		// }
		// dd($riwayat);	
		$this->data['riwayat'] = $arrBri;
		$this->data['trans_bri_total'] = $total_pay;
		$this->data['trans_bri_counts'] = count($arrBri);

		$this->template->title('Status Daftar Ulang Sd Update');
		$this->render('backend/standart/administrator/status_daftar_ulang_sd/status_daftar_ulang_sd_update', $this->data);
	}

	/**
	 * Update Status Daftar Ulang Sds
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('status_daftar_ulang_sd_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[11]');

		if ($this->form_validation->run()) {

			$save_data = [
				'status' => $this->input->post('status'),
			];

			$save_status_daftar_ulang_sd = $this->model_status_daftar_ulang_sd->change($id, $save_data);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_daftar_ulang", $id, "row");

			if ($this->input->post('status') == 1) {
				$no_transaksi = "LDUI-SD-" . date("YmdHis") . "-" . $get_daftar_ulang->id_siswa_sd;
				$this->mymodel->update("status_daftar_ulang_sd", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);

				$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $get_daftar_ulang->id_siswa_sd, "row");
				$customPayment = $this->input->post('custom_total_payment');
				// dd($customPayment);
				if ($customPayment != '0' && $customPayment != '') {
					$total_biaya = $customPayment;
					$this->mymodel->update("status_daftar_ulang_sd", array("custom_payment" => $customPayment), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);

					if ($get_siswa->is_mutasi == 2) {
						$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD", "row");
					} else {
						$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD MUTASI", "row");
					}
				} else {
					// get biaya pendaftaran
					if ($get_siswa->is_mutasi == 2) {
						$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD", "row");
						$total_biaya = $get_biaya->nominal_daftar_ulang;
					} else {
						$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD MUTASI", "row");
						$total_biaya = $get_biaya->nominal_daftar_ulang;
					}
				}

				// dd($total_biaya);
				// $brivaNo='77777';
				// $brivaNo = '88888';

				//get no briva
				$get_setting = $this->mymodel->getall("pengaturan_akun");
				foreach ($get_setting as $key => $value) {
					if (ENVIRONMENT == "production") {
						if ($value->name_setting == "bri_no_briva_prod_close") {
							$brivaNo = $value->value;
						}
					} else if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
						if ($value->name_setting == "bri_no_briva_dev_close") {
							$brivaNo = $value->value;
						}
					}
				}

				// buat tagihan uang pangkal saat update menjadi lulus
				if (!empty($get_siswa->va_number_bri)) {
					$va_number = $get_siswa->va_number_bri;
				} else {
					$get_no_urut = $this->mymodel->withquery("select va_number_bri, id_siswa_sd as id_siswa from siswa_sd where va_number_bri like '%" . $brivaNo . date('y', strtotime('+1 years')) . "01%' and va_number_bri != '' and is_mutasi = '2' order by va_number_bri DESC", "row");
					if (empty($get_no_urut)) {
						$no_urut = "0001";
					} else {
						$no_urut = (int) substr($get_no_urut->va_number_bri, -4);
						$no_urut = $no_urut + 1;
						$no_urut = sprintf("%04d", $no_urut);
					}
					$va_number = $brivaNo . date("y", strtotime('+1 years')) . "01" . $no_urut;
					// $va_number = '173114001';
				}
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
				$datas = array(
					'brivaNo' => $brivaNo,
					'custCode' => substr($va_number, 5),
					'nama' => $get_siswa->nama_lengkap,
					'amount' => $total_biaya,
					'keterangan' => 'Pembayaran Daftar Ulang SD',
					//'expiredDate' => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
					'expiredDate' => date("Y-m-d H:i:s", strtotime($date_va))
				);

				$payment_response = $this->create_va_bri($datas);
				// dd($payment_response);
				if ($payment_response['responseCode'] != '00') {
					$this->data['success'] = false;
					$this->data['message'] = $payment_response['errDesc'];
					echo json_encode($this->data);
					exit;
				}
				$res_data = $payment_response['data'];


				$this->mymodel->update("siswa_sd", array("va_number_bri" => $res_data['brivaNo'] . $res_data['custCode']), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
				$this->mymodel->update("status_daftar_ulang_sd", array("va_bri" => $res_data['brivaNo'] . $res_data['custCode']), "id_daftar_ulang", $id);


				// $this->mymodel->update("siswa_sd", array("va_number_bri" => $va_number), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
				// $this->mymodel->update("status_daftar_ulang_sd", array("va_bri" => $va_number), "id_daftar_ulang", $id);

				$data_transaksi = array(
					"no_transaksi" => $no_transaksi,
					"nama_bank" => "BRI",
					"user_email" => $get_siswa->email,
					"va_number" => $va_number,
					"user_name" => $get_siswa->nama_lengkap,
					"user_phone" => "",
					"description" => "Tagihan Pendaftaran Ulang SD a.n " . strtoupper($get_siswa->nama_lengkap),
					"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
					"total_biaya" => $total_biaya,
					"status_transaksi" => "0",
					"created_at" => date("Y-m-d H:i:s"),
					"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
				);
				if (!empty($data_transaksi)) {
					$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
					$this->mymodel->update("siswa_sd", array("no_transaksi" => $no_transaksi), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
					$cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_sd, "tipe_siswa" => "sd"));
					// dd($cetak_slip);
					$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
					//kirim email slip pembayaran
					$data_email = array(
						"email" => $get_siswa->email,
						"nama_lengkap" => $get_siswa->nama_lengkap,
						"tipe_pendaftaran" => "PSB SD",
						"jenjang" => "SD",
						"transaksi" => $get_transaksi,
						"nama_panitia" => "Panitia PSB SD Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
						"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
					);
					$this->mymodel->update("status_daftar_ulang_sd", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);

					$kirim_email = $this->send_email_file("", $data_email['email'], $data_email);
					// dd($kirim_email);
				}
			}

			if ($this->input->post('status') == 2) {
				$get_transaksi = $this->mymodel->getbywhere("transaksi", array("va_number" => $get_daftar_ulang->va_bri), "row");
				$get_transaksi = $get_transaksi[0];
				$this->mymodel->update2("transaksi", array("status_transaksi" => 1, "updated_at" => date("Y-m-d H:i:s")), "va_number", $get_daftar_ulang->va_bri);
				$trx_id = explode("-", $get_transaksi->no_transaksi);
				$jenis_pembayaran = $trx_id[0];
				$jenjang = $trx_id[1];
				$id_siswa = $trx_id[3];
				$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $get_daftar_ulang->id_siswa_sd, "row");

				//CETAK KARTU SEMENTARA
				$cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'sd', "id_siswa" => $get_daftar_ulang->id_siswa_sd));
				$nama_ortu = $get_siswa->nama_ibu;
				if (empty($get_siswa->nama_ibu)) {
					$nama_ortu = $get_siswa->nama_ayah;
				}
				if ($get_siswa->is_mutasi == 2) {
					$jalur = "PSB";
				} else {
					$jalur = "PSB MUTASI";
				}


				//CETAK KWITANSI
				$cetak_kwitansi = $this->cetak_kwitansi(array(
					"tipe_siswa" => 'sd',
					"jenjang" => $jenjang,
					"nama_ortu" => $nama_ortu,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"email" => $get_siswa->email,
					"va_number" => $get_transaksi->va_number,
					"total_biaya" => $get_transaksi->total_biaya,
					"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
					"id_siswa" => $get_daftar_ulang->id_siswa_sd,
					"no_transaksi" => $get_transaksi->no_transaksi,
					"jalur" => $jalur,
					"jenis_kwitansi" => "uang pangkal"
				));

				$kartu = 'sd' . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . '.pdf';
				$kwitansi = $get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf';
				$this->mymodel->update("status_daftar_ulang_sd", array("status" => 2, "kwitansi" => $kwitansi, "kartu_sementara" => $kartu, "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
			}

			if ($this->input->post('status') == 0) {
				$get_transaksi = $this->mymodel->getbywhere("transaksi", array("va_number" => $get_daftar_ulang->va_bri), "row");
				$get_transaksi = $get_transaksi[0];
				if ($get_transaksi->status_transaksi == 1) {
					echo json_encode([
						'success' => false,
						'message' => "Update status gagal karena transaksi sudah terbayarkan!"
					]);
					exit;
				}
				// $this->mymodel->delete("transaksi", "id_transaksi", $get_transaksi->id_transaksi);
			}

			if ($save_status_daftar_ulang_sd) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/status_daftar_ulang_sd', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_sd');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_sd');
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
	 * delete Status Daftar Ulang Sds
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('status_daftar_ulang_sd_delete');

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
			set_message(cclang('has_been_deleted', 'status_daftar_ulang_sd'), 'success');
		} else {
			set_message(cclang('error_delete', 'status_daftar_ulang_sd'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Status Daftar Ulang Sds
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('status_daftar_ulang_sd_view');

		$this->data['status_daftar_ulang_sd'] = $this->model_status_daftar_ulang_sd->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Status Daftar Ulang Sd Detail');
		$this->render('backend/standart/administrator/status_daftar_ulang_sd/status_daftar_ulang_sd_view', $this->data);
	}

	private function _remove($id)
	{
		$siswa_sd = $this->model_status_daftar_ulang_sd->find($id);

		return $this->model_status_daftar_ulang_sd->remove($id);
	}

	/**
	 * delete Status Daftar Ulang Sds
	 *
	 * @var $id String
	 */
	public function update_status($id = null)
	{
		$arr_id = $this->input->get('id');
		if (count($arr_id) >= 10) {
			set_message('Update aktivasi va maksimal per 5 data', 'error');
			redirect_back();
		}
		foreach ($arr_id as $id) {
			$this->mymodel->update('status_daftar_ulang_sd', array('status' => '1'), 'id_daftar_ulang', $id);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_daftar_ulang", $id, "row");

			//update tgl aktivasi
			$this->mymodel->update("status_daftar_ulang_sd", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);

			$no_transaksi = "LDUI-SD-" . date("YmdHis") . "-" . $get_daftar_ulang->id_siswa_sd;
			// get biaya pendaftaran
			$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "SD", "row");
			$total_biaya = $get_biaya->nominal_daftar_ulang;
			$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $get_daftar_ulang->id_siswa_sd, "row");

			// $brivaNo='77777';
			// $brivaNo = '88888';

			//get no briva
			$get_setting = $this->mymodel->getall("pengaturan_akun");
			foreach ($get_setting as $key => $value) {
				if (ENVIRONMENT == "production") {
					if ($value->name_setting == "bri_no_briva_prod_close") {
						$brivaNo = $value->value;
					}
				} else if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
					if ($value->name_setting == "bri_no_briva_dev_close") {
						$brivaNo = $value->value;
					}
				}
			}

			// buat tagihan uang pangkal saat update menjadi lulus
			if (!empty($get_siswa->va_number_bri)) {
				$va_number = $get_siswa->va_number_bri;
			} else {
				$get_no_urut = $this->mymodel->withquery("select va_number_bri, id_siswa_sd as id_siswa from siswa_sd where va_number_bri like '%" . $brivaNo . date('y', strtotime('+1 years')) . "01%' and va_number_bri != '' and is_mutasi = '2' order by va_number_bri DESC", "row");
				if (empty($get_no_urut)) {
					$no_urut = "0001";
				} else {
					$no_urut = (int) substr($get_no_urut->va_number_bri, -4);
					$no_urut = $no_urut + 1;
					$no_urut = sprintf("%04d", $no_urut);
				}
				$va_number = $brivaNo . date("y", strtotime('+1 years')) . "01" . $no_urut;
				// $va_number = '173114001';
			}
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
			$datas = array(
				'brivaNo' => $brivaNo,
				'custCode' => substr($va_number, 5),
				'nama' => $get_siswa->nama_lengkap,
				'amount' => $total_biaya,
				'keterangan' => 'Pembayaran Daftar Ulang SD',
				//'expiredDate' => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
				'expiredDate' => date("Y-m-d H:i:s", strtotime($date_va))
			);

			$payment_response = $this->create_va_bri($datas);

			if ($payment_response['responseCode'] != '00') {
				$this->data['success'] = false;
				$this->data['message'] = $payment_response['errDesc'];
				echo json_encode($this->data);
				exit;
			}
			$res_data = $payment_response['data'];

			$this->mymodel->update("siswa_sd", array("va_number_bri" => $res_data['brivaNo'] . $res_data['custCode']), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
			$this->mymodel->update("status_daftar_ulang_sd", array("va_bri" => $res_data['brivaNo'] . $res_data['custCode']), "id_daftar_ulang", $id);

			// $this->mymodel->update("siswa_sd", array("va_number_bri" => $va_number), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
			// $this->mymodel->update("status_daftar_ulang_sd", array("va_bri" => $va_number), "id_daftar_ulang", $id);

			$data_transaksi = array(
				"no_transaksi" => $no_transaksi,
				"nama_bank" => "BRI", //$this->input->post('nama_bank'),
				"user_email" => $get_siswa->email,
				"va_number" => $va_number,
				"user_name" => $get_siswa->nama_lengkap,
				"user_phone" => "",
				"description" => "Tagihan Pendaftaran Ulang SD a.n " . strtoupper($get_siswa->nama_lengkap),
				"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
				"total_biaya" => $total_biaya,
				"status_transaksi" => "0",
				"created_at" => date("Y-m-d H:i:s"),
				"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
			);
			if (!empty($data_transaksi)) {
				$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
				$this->mymodel->update("siswa_sd", array("no_transaksi" => $no_transaksi), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);
				$cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_sd, "tipe_siswa" => "sd"));
				// dd($cetak_slip);
				$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
				//kirim email slip pembayaran
				$data_email = array(
					"email" => $get_siswa->email,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"tipe_pendaftaran" => "PSB SD",
					"jenjang" => "SD",
					"transaksi" => $get_transaksi,
					"nama_panitia" => "Panitia PSB SD Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
					"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
				);
				$this->mymodel->update("status_daftar_ulang_sd", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_sd", $get_daftar_ulang->id_siswa_sd);

				$kirim_email = $this->send_email_file("", $data_email['email'], $data_email);
			}
		}

		set_message('Update status berhasil', 'success');

		redirect_back();
	}

	public function update_status2($id = null)
	{
		$arr_id = $this->input->get('id');
		if (count($arr_id) > 10) {
			set_message('Update aktivasi va maksimal per 10 data', 'error');
			redirect_back();
		}

		foreach ($arr_id as $id) {
			$this->mymodel->update('status_daftar_ulang_sd', array('status' => '1'), 'id_daftar_ulang', $id);
		}

		set_message('Update status berhasil', 'success');

		redirect_back();
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
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_daftar_ulang", $i, "row");
			$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $get_daftar_ulang->id_siswa_sd, "row");
			$data_siswa_aktif = [
				"id_siswa_sd" => $get_daftar_ulang->id_siswa_sd,
				"nama_lengkap" => $get_siswa->nama_lengkap,
				"id_tahun_ajaran" => $this->input->post('tahun_ajaran'),
				"id_kelas" => $this->input->post('kelas_sd'),
				"spp_custom" => $this->input->post('spp_custom')
			];

			$insertAktif = $this->mymodel->insertid("siswa_sd_aktif", $data_siswa_aktif);
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

	public function update_expired_va()
	{
		$this->load->library('BriApi');

		$arr_id = json_decode($this->input->post('ids'));
		$ids = [];
		// dd($arr_id);
		foreach ($arr_id as $key => $item) {
			if ($item->name == 'id[]') {
				$ids[] = $item->value;
			}
		}

		foreach ($ids as $key => $i) {
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_daftar_ulang", $i, "row");
			$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $get_daftar_ulang->id_siswa_sd, "row");
			$get_transaksi = $this->mymodel->getbywhere("transaksi", "va_number", $get_daftar_ulang->va_bri, "row");

			//get no briva
			$get_setting = $this->mymodel->getall("pengaturan_akun");
			foreach ($get_setting as $key => $value) {
				if (ENVIRONMENT == "production") {
					if ($value->name_setting == "bri_no_briva_prod_close") {
						$brivaNo = $value->value;
					}
				} else if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
					if ($value->name_setting == "bri_no_briva_dev_close") {
						$brivaNo = $value->value;
					}
				}
			}



			//get config bri key
			$get_setting = $this->mymodel->getall("pengaturan_akun");
			foreach ($get_setting as $key => $item) {
				if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
					if ($item->name_setting == 'bri_dev_url') {
						$url = $item->value;
					}
				} else if (ENVIRONMENT == 'production') {
					if ($item->name_setting == 'bri_prod_url') {
						$url = $item->value;
					}
				}
				if ($item->name_setting == 'bri_client_id') {
					$clientID = $item->value;
				}
				if ($item->name_setting == 'bri_client_secret') {
					$clientSecret = $item->value;
				}
				if ($item->name_setting == 'bri_institution_code') {
					$institutionCode = $item->value;
				}
			}
			$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";
			$data = array(
				'brivaNo' => $brivaNo,
				'custCode' => substr($get_daftar_ulang->va_bri, 5),
				'expiredDate' => $this->input->post('expired_date') . ':00',
				'nama' => $get_siswa->nama_lengkap,
				'amount' => $get_transaksi->total_biaya,
				'keterangan' => 'Pembayaran Daftar Ulang SD'
			);
			$updateBri = BriApi::update($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
			// dd($this->input->post('expired_date').':00');
			if ($updateBri['responseCode'] != '00') {
				$this->db->trans_rollback();
				$this->load->library("session");
				$this->session->set_flashdata('error', $updateBri['errDesc']);
				redirect($_SERVER['HTTP_REFERER']);
			}
			$this->mymodel->update('transaksi', array('expired_datetime' => $this->input->post('expired_date') . ':00'), 'id_transaksi', $get_transaksi->id_transaksi);
		}
		$this->load->library("session");
		$this->session->set_flashdata('success', 'Update Expired Date Berhasil');
		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('status_daftar_ulang_sd_export');

		$this->model_status_daftar_ulang_sd->export_siswa('status_daftar_ulang_sd', 'status_daftar_ulang_sd');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('status_daftar_ulang_sd_export');

		$this->model_status_daftar_ulang_sd->pdf('status_daftar_ulang_sd', 'status_daftar_ulang_sd');
	}

	public function single_pdf($id = null)
	{
		$this->is_allowed('status_daftar_ulang_sd_export');

		$table = $title = 'status_daftar_ulang_sd';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_status_daftar_ulang_sd->find($id);
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

	function create_va_bri($datas)
	{
		$this->load->library('BriApi');

		//get config bri key
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
				if ($item->name_setting == 'bri_dev_url') {
					$url = $item->value;
				}
			} else if (ENVIRONMENT == 'production') {
				if ($item->name_setting == 'bri_prod_url') {
					$url = $item->value;
				}
			}
			if ($item->name_setting == 'bri_client_id') {
				$clientID = $item->value;
			}
			if ($item->name_setting == 'bri_client_secret') {
				$clientSecret = $item->value;
			}
			if ($item->name_setting == 'bri_institution_code') {
				$institutionCode = $item->value;
			}
		}

		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

		$data = array(
			'brivaNo' => $datas['brivaNo'],
			'expiredDate' => $datas['expiredDate'],
			'custCode' => $datas['custCode'],
			'nama' => $datas['nama'],
			'amount' => $datas['amount'],
			'keterangan' => $datas['keterangan']
		);
		return BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
	}

	function update_va_bri($datas)
	{
		$this->load->library('BriApi');

		//get config bri key
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
				if ($item->name_setting == 'bri_dev_url') {
					$url = $item->value;
				}
			} else if (ENVIRONMENT == 'production') {
				if ($item->name_setting == 'bri_prod_url') {
					$url = $item->value;
				}
			}
			if ($item->name_setting == 'bri_client_id') {
				$clientID = $item->value;
			}
			if ($item->name_setting == 'bri_client_secret') {
				$clientSecret = $item->value;
			}
			if ($item->name_setting == 'bri_institution_code') {
				$institutionCode = $item->value;
			}
		}

		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

		$data = array(
			'brivaNo' => $datas['brivaNo'],
			'expiredDate' => date("Y-m-d H:i:s", strtotime("+".$datas['day']." days")),
			'custCode' => $datas['custCode'],
			'nama' => $datas['nama'],
			'amount' => $datas['amount'],
			'keterangan' => $datas['keterangan']
		);
		return BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
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

		$data_asli = array(
			'type' => "createbilling",
			'client_id' => $client_id,
			'trx_id' => $no_transaksi,
			'trx_amount' => $total,
			'billing_type' => 'c',
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
			$endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran_bri');
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
			// var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		//return $rs;
		return true;
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
			// var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		return true;
	}

	public function send_email_file($file = "", $to = '', $data)
	{
		try {
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
			$mail->send();
			return true;
		} catch (Exception $e) {
			return false;
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
			// var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		return $rs;
	}
}


/* End of file status_daftar_ulang_sd.php */
/* Location: ./application/controllers/administrator/Status Daftar Ulang Sd.php */
