<?php
defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

/**
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Smp Controller
 *| --------------------------------------------------------------------------
 *| Status Daftar Ulang Smp site
 *|
 */
class Status_daftar_ulang_smp extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_status_daftar_ulang_smp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Status Daftar Ulang Smps
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('status_daftar_ulang_smp_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['status_daftar_ulang_smps'] = $this->model_status_daftar_ulang_smp->get($filter, $field, $this->limit_page, $offset);
		$this->data['status_daftar_ulang_smp_counts'] = $this->model_status_daftar_ulang_smp->count_all($filter, $field);


		$config = [
			'base_url'     => 'administrator/status_daftar_ulang_smp/index/',
			'total_rows'   => $this->model_status_daftar_ulang_smp->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Status Daftar Ulang Smp List');
		$this->render('backend/standart/administrator/status_daftar_ulang_smp/status_daftar_ulang_smp_list', $this->data);
	}


	/**
	 * Update view Status Daftar Ulang Smps
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('status_daftar_ulang_smp_update');

		$this->data['status_daftar_ulang_smp'] = $this->model_status_daftar_ulang_smp->find($id);

		$this->template->title('Status Daftar Ulang Smp Update');
		$this->render('backend/standart/administrator/status_daftar_ulang_smp/status_daftar_ulang_smp_update', $this->data);
	}

	/**
	 * Update Status Daftar Ulang Smps
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('status_daftar_ulang_smp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('status', 'Status', 'trim|required|max_length[6]');

		if ($this->form_validation->run()) {

			$save_data = [
				'status' => $this->input->post('status'),
			];


			$save_status_daftar_ulang_smp = $this->model_status_daftar_ulang_smp->change($id, $save_data);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_smp", "id_daftar_ulang", $id, "row");

			if ($this->input->post('status') == 1) {
				$get_siswa = $this->mymodel->getbywhere("siswa_smp", "id_siswa_smp", $get_daftar_ulang->id_siswa_smp, "row");
				$this->mymodel->update("status_daftar_ulang_smp", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);

				if ($get_siswa->ppsbb == 1) {
					$no_transaksi = "LDUI-PPSBBSMP-" . date("YmdHi") . "-" . $get_daftar_ulang->id_siswa_smp;
					//get biaya pendaftaran
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PPSBB SMP", "row");
					$tipe = 'PPSBB SMP';
				} else {
					$no_transaksi = "LDUI-PSBSMP-" . date("YmdHi") . "-" . $get_daftar_ulang->id_siswa_smp;
					//get biaya pendaftaran
					$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PSB SMP", "row");
					$tipe = 'PSB SMP';
				}

				$customPayment = $this->input->post('custom_total_payment');
				// dd($customPayment);
				if ($customPayment != '0' && $customPayment != '') {
					$total_biaya = $customPayment;
					$this->mymodel->update("status_daftar_ulang_smp", array("custom_payment" => $customPayment), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);
				} else {
					$total_biaya = $get_biaya->nominal_daftar_ulang;
				}

				//buat tagihan uang pangkal saat update menjadi lulus
				$va_number = $get_siswa->va_number;
				$payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
				// dd($payment_response);
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
					"description" => "Tagihan Pendaftaran Ulang SMP a.n " . strtoupper($get_siswa->nama_lengkap),
					"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
					"total_biaya" => $total_biaya,
					"status_transaksi" => "0",
					"created_at" => date("Y-m-d H:i:s"),
					//"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
					"expired_datetime" => date("Y-m-d H:i:s", strtotime($date_va))
				);
				if (!empty($data_transaksi)) {
					//cek data transaksi daftar ulang
					$cek_data = $this->mymodel->withquery("select id_transaksi from transaksi where va_number = '".$va_number."' and no_transaksi like '%LDUI%' order by id_transaksi DESC","row");
					if (empty($cek_data)) {
						$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
					}
					else{
						$this->mymodel->update("transaksi", $data_transaksi, "id_transaksi", $cek_data->id_transaksi);
						$id_transaksi = $cek_data->id_transaksi;
					}
					$this->mymodel->update("siswa_smp", array("no_transaksi" => $no_transaksi), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);
					// $cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_smp, "tipe_siswa" => "smp"));
					$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
					//kirim email slip pembayaran
					$data_email = array(
						"email" => $get_siswa->email,
						"nama_lengkap" => $get_siswa->nama_lengkap,
						"tipe_pendaftaran" => $tipe,
						"jenjang" => "SMP",
						"transaksi" => $get_transaksi,
						"nama_panitia" => "Panitia " . $tipe . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
						"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
					);
					$this->mymodel->update("status_daftar_ulang_smp", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);

					$kirim_email = $this->send_email_file("", $data_email['email'], $data_email);
				}
			}

			if ($this->input->post('status') == 2) {
				$get_siswa = $this->mymodel->getbywhere("siswa_smp", "id_siswa_smp", $get_daftar_ulang->id_siswa_smp, "row");
				$get_transaksi = $this->mymodel->getbywhere("transaksi", array("no_transaksi" => $get_siswa->no_transaksi), "row");
				$get_transaksi = $get_transaksi[0];
				$this->mymodel->update("transaksi", array("status_transaksi" => 1, "updated_at" => date("Y-m-d H:i:s")), "no_transaksi", $get_siswa->no_transaksi);
				$trx_id = explode("-", $get_transaksi->no_transaksi);
				$jenis_pembayaran = $trx_id[0];
				$jenjang = $trx_id[1];
				$id_siswa = $trx_id[3];

				//CETAK KARTU SEMENTARA
				$cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => 'ft', "id_siswa" => $get_daftar_ulang->id_siswa_smp));
				$nama_ortu = $get_siswa->nama_ibu;
				if (empty($get_siswa->nama_ibu)) {
					$nama_ortu = $get_siswa->nama_ayah;
				}
				$jalur = "PSB";


				//CETAK KWITANSI
				$cetak_kwitansi = $this->cetak_kwitansi(array(
					"tipe_siswa" => 'smp',
					"jenjang" => $jenjang,
					"nama_ortu" => $nama_ortu,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"email" => $get_siswa->email,
					"va_number" => $get_transaksi->va_number,
					"total_biaya" => $get_transaksi->total_biaya,
					"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
					"id_siswa" => $get_daftar_ulang->id_siswa_smp,
					"no_transaksi" => $get_transaksi->no_transaksi,
					"jalur" => $jalur,
					"jenis_kwitansi" => "uang pangkal"
				));

				$kartu = 'smp' . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . '.pdf';
				$kwitansi = $get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf';
				$this->mymodel->update("status_daftar_ulang_smp", array("status" => 2, "kwitansi" => $kwitansi, "kartu_sementara" => $kartu, "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);
			}

			if ($save_status_daftar_ulang_smp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/status_daftar_ulang_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_smp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/status_daftar_ulang_smp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/status_daftar_ulang_smp');
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
	 * delete Status Daftar Ulang Smps
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('status_daftar_ulang_smp_delete');

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
			set_message(cclang('has_been_deleted', 'status_daftar_ulang_smp'), 'success');
		} else {
			set_message(cclang('error_delete', 'status_daftar_ulang_smp'), 'error');
		}

		redirect_back();
	}

	/**
	 * View view Status Daftar Ulang Smps
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('status_daftar_ulang_smp_view');

		$this->data['status_daftar_ulang_smp'] = $this->model_status_daftar_ulang_smp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Status Daftar Ulang Smp Detail');
		$this->render('backend/standart/administrator/status_daftar_ulang_smp/status_daftar_ulang_smp_view', $this->data);
	}

	/**
	 * delete Status Daftar Ulang Smps
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$status_daftar_ulang_smp = $this->model_status_daftar_ulang_smp->find($id);



		return $this->model_status_daftar_ulang_smp->remove($id);
	}

	public function update_status($id = null)
	{
		$this->db->trans_begin();
		$arr_id = $this->input->get('id');
		if (count($arr_id) > 5) {
			set_message('Update aktivasi va maksimal per 5 data', 'error');
			redirect_back();
		}
		foreach ($arr_id as $id) {
			$this->mymodel->update('status_daftar_ulang_sd', array('status' => "1"), 'id_daftar_ulang', $id);
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_smp", "id_daftar_ulang", $id, "row");
			$this->mymodel->update("status_daftar_ulang_smp", array("tgl_aktivasi" => date("Y-m-d H:i:s")), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);

			$get_siswa = $this->mymodel->getbywhere("siswa_smp", "id_siswa_smp", $get_daftar_ulang->id_siswa_smp, "row");

			if ($get_siswa->ppsbb == 1) {
				$no_transaksi = "LDUI-PPSBBSMP-" . date("YmdHi") . "-" . $get_daftar_ulang->id_siswa_smp;
				//get biaya pendaftaran
				$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PPSBB SMP", "row");
				$tipe = 'PPSBB SMP';
			} else {
				$no_transaksi = "LDUI-PSBSMP-" . date("YmdHi") . "-" . $get_daftar_ulang->id_siswa_smp;
				//get biaya pendaftaran
				$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", "PSB SMP", "row");
				$tipe = 'PSB SMP';
			}

			$total_biaya = $get_biaya->nominal_daftar_ulang;
			//buat tagihan uang pangkal saat update menjadi lulus
			$va_number = $get_siswa->va_number;
			//cek transaksi sebelumnya di cancel terlebih dahulu
			$cek_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, status_transaksi, user_name, total_biaya from transaksi where va_number = '".$va_number."' and status_transaksi = '0' and expired_datetime >= '".date('Y-m-d H:i:s')."' order by id_transaksi DESC","result");
			if (!empty($cek_transaksi)) {
				foreach ($cek_transaksi as $key_transaksi => $value_transaksi) {
					$cancel_transaksi = $this->cancel_billing(ENVIRONMENT, $value_transaksi->total_biaya, $value_transaksi->no_transaksi, $value_transaksi->user_name);
				}
			}
			$payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
			// dd($payment_response);
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
				"description" => "Tagihan Pendaftaran Ulang SMP a.n " . strtoupper($get_siswa->nama_lengkap),
				"id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
				"total_biaya" => $total_biaya,
				"status_transaksi" => "0",
				"created_at" => date("Y-m-d H:i:s"),
				//"expired_datetime" => date("Y-m-d H:i:s", strtotime("+" . $date_va . " hours"))
				"expired_datetime" => date("Y-m-d H:i:s", strtotime($date_va))
			);
			if (!empty($data_transaksi)) {
				//cek data transaksi daftar ulang
				$cek_data = $this->mymodel->withquery("select id_transaksi from transaksi where va_number = '".$va_number."' and no_transaksi like '%LDUI%' and status_transaksi = '0' order by id_transaksi DESC","row");
				if (empty($cek_data)) {
					$id_transaksi = $this->mymodel->insertid("transaksi", $data_transaksi);
				}
				else{
					$this->mymodel->update("transaksi", $data_transaksi, "id_transaksi", $cek_data->id_transaksi);
					$id_transaksi = $cek_data->id_transaksi;
				}
				$this->mymodel->update("siswa_smp", array("no_transaksi" => $no_transaksi), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);
				// $cetak_slip = $this->cetak_slip(array("id_siswa" => $get_daftar_ulang->id_siswa_smp, "tipe_siswa" => "smp"));
				$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
				//kirim email slip pembayaran
				$data_email = array(
					"email" => $get_siswa->email,
					"nama_lengkap" => $get_siswa->nama_lengkap,
					"tipe_pendaftaran" => $tipe,
					"jenjang" => "SMP",
					"transaksi" => $get_transaksi,
					"nama_panitia" => "Panitia " . $tipe . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
					"slip_pembayaran" => $no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
				);
				$this->mymodel->update("status_daftar_ulang_smp", array("slip_pembayaran" => $data_email["slip_pembayaran"]), "id_siswa_smp", $get_daftar_ulang->id_siswa_smp);

				$kirim_email = $this->send_email_file("", $data_email['email'], $data_email);
			}
		}

		$this->db->trans_commit();
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
			$get_daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_smp", "id_daftar_ulang", $i, "row");
			$get_siswa = $this->mymodel->getbywhere("siswa_smp", "id_siswa_smp", $get_daftar_ulang->id_siswa_smp, "row");
			$data_siswa_aktif = [
				"id_siswa_smp" => $get_daftar_ulang->id_siswa_smp,
				"nama_lengkap" => $get_siswa->nama_lengkap,
				"id_tahun_ajaran" => $this->input->post('tahun_ajaran'),
				"id_kelas" => $this->input->post('kelas_smp'),
				"spp_custom" => $this->input->post('spp_custom')
			];

			$insertAktif = $this->mymodel->insertid("siswa_smp_aktif", $data_siswa_aktif);
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
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('status_daftar_ulang_smp_export');

		$this->model_status_daftar_ulang_smp->export_siswa('status_daftar_ulang_smp', 'status_daftar_ulang_smp');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('status_daftar_ulang_smp_export');

		$this->model_status_daftar_ulang_smp->pdf('status_daftar_ulang_smp', 'status_daftar_ulang_smp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('status_daftar_ulang_smp_export');

		$table = $title = 'status_daftar_ulang_smp';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_status_daftar_ulang_smp->find($id);
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
				if($item->tipe_date=='day'){
				  $date_va= date('c',strtotime(time() + (($item->value*24) * 3600)) );
				}else if($item->tipe_date == 'hour'){
				  $date_va= date('c',strtotime(time() + (($item->value) * 3600)) );
				}
				else if($item->tipe_date == 'date'){
				  $date_va= date('c',strtotime($item->value));
				}
			}
		}

		$data_asli = array(
			'type' => "createbilling",
			'client_id' => $client_id,
			'trx_id' => $no_transaksi,
			'trx_amount' => $total,
			'billing_type' => 'c',
			'datetime_expired' => $date_va, // billing will be expired in 24 hours
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
			return ($response_json);
			return false;
		} else {
			$data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
			// return ($data_response);
			return true;
		}
	}

	function cancel_billing($production, $total, $no_transaksi, $nama){
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
      
      $date_va = date("Y-m-d H:i:s", strtotime("-1 minutes"));

      $data_asli = array(
        'type' => "updateBilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
        'trx_amount' => $total,
        'datetime_expired' => $date_va, // billing will be expired in 6 hours
        'customer_name' => $nama,
        //'billing_type' => 'c',
        //'virtual_account' => $data_user['va_number'],
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
      }
      else {
        $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
        // $data_response will contains something like this: 
        // array(
        //  'virtual_account' => 'xxxxx',
        //  'trx_id' => 'xxx',
        // );
        //var_dump($data_response);
        return($data_response);
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
			// var_dump($rs, curl_error($ch));
			curl_close($ch);
			return false;
		}
		curl_close($ch);
		return $rs;
	}
}


/* End of file status_daftar_ulang_smp.php */
/* Location: ./application/controllers/administrator/Status Daftar Ulang Smp.php */
