<?php
defined('BASEPATH') or exit('No direct script access allowed');


/**
 *| --------------------------------------------------------------------------
 *| Transaksi Spp Controller
 *| --------------------------------------------------------------------------
 *| Transaksi Spp site
 *|
 */
class Transaksi_spp extends Admin
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_transaksi_spp');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	 * show all Transaksi Spps
	 *
	 * @var $offset String
	 */
	public function index($offset = 0)
	{
		$this->is_allowed('transaksi_spp_list');

		$limit = 20;

		// Multi-filter params
		$ff = $this->input->get('ff');
		$fo = $this->input->get('fo');
		$fv = $this->input->get('fv');
		$multi_filters = array();
		if (is_array($ff) && is_array($fo) && is_array($fv)) {
			for ($i = 0; $i < count($ff); $i++) {
				if (!empty($ff[$i]) && isset($fv[$i]) && $fv[$i] !== '') {
					$multi_filters[] = array(
						'field'    => $ff[$i],
						'operator' => isset($fo[$i]) ? $fo[$i] : 'contains',
						'value'    => $fv[$i]
					);
				}
			}
		}

		$filter = $this->input->get('q');
		$field  = $this->input->get('f');

		$this->data['transaksi_spps']       = $this->model_transaksi_spp->get($filter, $field, $limit, $offset, array(), $multi_filters);
		$this->data['transaksi_spp_counts'] = $this->model_transaksi_spp->count_all($filter, $field, $multi_filters);
		$this->data['multi_filters']        = $multi_filters;

		$config = array(
			'base_url'     => 'administrator/transaksi_spp/index/',
			'total_rows'   => $this->data['transaksi_spp_counts'],
			'per_page'     => $limit,
			'uri_segment'  => 4,
		);
		$this->data['pagination'] = $this->pagination($config);

		// Tahun ajaran aktif
		$get_ta_aktif = $this->mymodel->withquery(
			"SELECT * FROM tahun_ajaran WHERE tanggal_mulai <= '".date('Y-m-d')."' AND tanggal_selesai >= '".date('Y-m-d')."'",
			"row"
		);
		$id_ta_aktif = !empty($get_ta_aktif) ? $get_ta_aktif->id_tahun_ajaran : 0;
		$label_ta_aktif = !empty($get_ta_aktif) ? $get_ta_aktif->label : '-';

		// Semua tahun ajaran untuk dropdown filter chart
		$this->data['tahun_ajaran_options'] = $this->mymodel->withquery(
			"SELECT id_tahun_ajaran, label FROM tahun_ajaran ORDER BY tanggal_mulai DESC",
			"result"
		);
		$this->data['selected_ta'] = $this->input->get('ta') ? (int) $this->input->get('ta') : $id_ta_aktif;
		$this->data['label_ta_aktif'] = $label_ta_aktif;

		$ta_filter = $this->data['selected_ta'] ?: $id_ta_aktif;

		// ===== INFOBOX QUERIES =====
		// Total tagihan TA ini
		$q_total = $this->mymodel->withquery(
			"SELECT COALESCE(SUM(total_biaya), 0) AS total FROM transaksi_spp WHERE id_tahun_ajaran = " . (int) $ta_filter,
			"row"
		);
		$this->data['info_total_tagihan'] = !empty($q_total) ? (float) $q_total->total : 0;

		// Terbayar TA ini
		$q_bayar = $this->mymodel->withquery(
			"SELECT COALESCE(SUM(total_biaya), 0) AS total FROM transaksi_spp WHERE id_tahun_ajaran = " . (int) $ta_filter . " AND status_transaksi = '2'",
			"row"
		);
		$this->data['info_terbayar'] = !empty($q_bayar) ? (float) $q_bayar->total : 0;

		// Belum bayar TA ini
		$q_belum = $this->mymodel->withquery(
			"SELECT COALESCE(SUM(total_biaya), 0) AS total FROM transaksi_spp WHERE id_tahun_ajaran = " . (int) $ta_filter . " AND status_transaksi IN ('0','1')",
			"row"
		);
		$this->data['info_belum_bayar'] = !empty($q_belum) ? (float) $q_belum->total : 0;

		// Tunggakan TA sebelumnya
		$q_tunggakan = $this->mymodel->withquery(
			"SELECT COALESCE(SUM(total_biaya), 0) AS total FROM transaksi_spp WHERE id_tahun_ajaran < " . (int) $ta_filter . " AND status_transaksi IN ('0','1')",
			"row"
		);
		$this->data['info_tunggakan'] = !empty($q_tunggakan) ? (float) $q_tunggakan->total : 0;

		// ===== CHART DATA: per bulan =====
		$chart_rows = $this->mymodel->withquery(
			"SELECT bulan,
				COALESCE(SUM(total_biaya), 0) AS total_tagihan,
				COALESCE(SUM(CASE WHEN status_transaksi = '2' THEN total_biaya ELSE 0 END), 0) AS total_bayar
			 FROM transaksi_spp
			 WHERE id_tahun_ajaran = " . (int) $ta_filter . "
			 GROUP BY bulan
			 ORDER BY FIELD(bulan, 'Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni')",
			"result"
		);
		$this->data['chart_labels'] = array();
		$this->data['chart_tagihan'] = array();
		$this->data['chart_bayar'] = array();
		if (!empty($chart_rows)) {
			foreach ($chart_rows as $cr) {
				$this->data['chart_labels'][]  = $cr->bulan;
				$this->data['chart_tagihan'][]  = (float) $cr->total_tagihan;
				$this->data['chart_bayar'][]    = (float) $cr->total_bayar;
			}
		}

		$this->template->title('Transaksi Spp List');
		$this->render('backend/standart/administrator/transaksi_spp/transaksi_spp_list', $this->data);
	}

	/**
	 * Add new transaksi_spps
	 *
	 */
	public function add()
	{
		$this->is_allowed('transaksi_spp_add');

		$this->template->title('Transaksi Spp New');
		$this->render('backend/standart/administrator/transaksi_spp/transaksi_spp_add', $this->data);
	}

	/**
	 * Add New Transaksi Spps
	 *
	 * @return JSON
	 */
	public function add_save()
	{
		$this->load->library("session");
		if (!$this->is_allowed('transaksi_spp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}
		// dd($this->input->post('id_siswa_sd_aktif'));

		$tahun_ajaran = $this->input->post('tahun_ajaran');
		$get_biaya = $this->mymodel->getbywhere("biaya_spp", "jenjang", $this->input->post('jenjang'), "row");

		if ($this->input->post('jenjang') == 'sd') {
			$id_siswa = $this->input->post('id_siswa_sd_aktif');
			$get_siswa = $this->mymodel->withquery("select sa.*,s.* from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd where sa.id_siswa_sd_aktif =" . $id_siswa, 'row');
			$bank = 'BRI';
		} elseif ($this->input->post('jenjang') == 'smp') {
			$id_siswa = $this->input->post('id_siswa_smp_aktif');
			$get_siswa = $this->mymodel->withquery("select sa.*,s.* from siswa_smp_aktif sa join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp where sa.id_siswa_smp_aktif =" . $id_siswa, 'row');
			$bank = 'BNI';
		} elseif ($this->input->post('jenjang') == 'sma') {
			$id_siswa = $this->input->post('id_siswa_sma_aktif');
			$get_siswa = $this->mymodel->withquery("select sa.*,s.* from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma where sa.id_siswa_sma_aktif =" . $id_siswa, 'row');
			$bank = 'BNI';
		} elseif ($this->input->post('jenjang') == 'ft') {
			$id_siswa = $this->input->post('id_siswa_ft_aktif');
			$get_siswa = $this->mymodel->withquery("select sa.*,s.* from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft where sa.id_siswa_ft_aktif =" . $id_siswa, 'row');
			$bank = 'BNI';
		}
		// dd($get_siswa);
		$jenjang = strtoupper($this->input->post('jenjang'));

		$cek_transaksi = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi like '%LISPP-" . $jenjang . "-" . $tahun_ajaran . "-" . $id_siswa . "%'", "row");
		if (!$cek_transaksi) {
			$get_tahun_ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '" . date('Y-m-d') . "' and tanggal_selesai >= '" . date('Y-m-d') . "'", "row");

			$f = strtotime($get_tahun_ajaran_aktif->tanggal_mulai);
			$first = date('Y-m-01', $f);
			$l = strtotime($get_tahun_ajaran_aktif->tanggal_selesai);
			$last = date('Y-m-01', $l);


			$arrMonth = $this->echoDate(strtotime($first), strtotime($last));
			// dd($arrMonth);
			foreach ($arrMonth as $i) {

				$bulan = explode("_", $i);
				$nama_bulan = get_bulan((int) $bulan[1]);
				$no_transaksi = 'LISPP-' . $jenjang . '-' . $tahun_ajaran . '-' . $id_siswa . '-' . $nama_bulan;
				$data_transaksi = array(
					"no_transaksi" => $no_transaksi,
					"nama_bank" => $bank,
					"user_email" => $get_siswa->email,
					"user_name" => $get_siswa->nama_lengkap,
					"user_phone" => "",
					"description" => "Tagihan SPP Bulan " . $nama_bulan . " " . $bulan[0],
					"id_biaya_pendaftaran" => $get_biaya->id_biaya,
					"total_biaya" => $get_biaya->nominal,
					"status_transaksi" => "0",
					"created_at" => date("Y-m-d H:i:s"),
					"bulan" => $nama_bulan,
					"id_siswa_aktif" => $id_siswa,
					"updated_from" => 'admin:' . $this->session->userdata('username')
				);
				// dd($data_transaksi);
				$save_transaksi_spp = $this->mymodel->insertid("transaksi_spp", $data_transaksi);
			}
		} else {
			$this->session->set_flashdata('warning', "Tagihan siswa sudah ada" . $cek_transaksi->kode_tagihan);
			redirect($_SERVER['HTTP_REFERER']);
		}

		if ($save_transaksi_spp) {

			$this->session->set_flashdata('success', 'Generate SPP berhasil');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	function echoDate($start, $end)
	{

		$current = $start;

		$ret = array();

		while ($current <= $end) {


			$format = date('Y_m', $current);
			$ret[] = $format;
			$current = @date('Y-M-01', $current) . "+1 month";
			$current = @strtotime($current);
		}

		return $ret;
	}
	/**
	 * Update view Transaksi Spps
	 *
	 * @var $id String
	 */
	public function edit($id)
	{
		$this->is_allowed('transaksi_spp_update');

		$this->data['transaksi_spp'] = $this->model_transaksi_spp->find($id);

		$this->template->title('Transaksi Spp Update');
		$this->render('backend/standart/administrator/transaksi_spp/transaksi_spp_update', $this->data);
	}

	/**
	 * Update Transaksi Spps
	 *
	 * @var $id String
	 */
	public function edit_save($id)
	{
		if (!$this->is_allowed('transaksi_spp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('user_email', 'User Email', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('user_name', 'User Name', 'trim|required|max_length[200]');
		$this->form_validation->set_rules('status_transaksi', 'Status Transaksi', 'trim|required');
		$this->form_validation->set_rules('is_show', 'Is Show', 'trim|required|max_length[6]');

		if ($this->form_validation->run()) {
			$transaksi_spp_file_kwitansi_uuid = $this->input->post('transaksi_spp_file_kwitansi_uuid');
			$transaksi_spp_file_kwitansi_name = $this->input->post('transaksi_spp_file_kwitansi_name');

			$save_data = [
				'user_email' => $this->input->post('user_email'),
				'user_name' => $this->input->post('user_name'),
				'va_number' => $this->input->post('va_number'),
				'total_biaya' => $this->input->post('total_biaya'),
				'status_transaksi' => $this->input->post('status_transaksi'),
				'is_show' => $this->input->post('is_show'),
				'updated_from' => 'admin:' . $this->session->userdata('username'),
			];

			if (!is_dir(FCPATH . '/uploads/transaksi_spp/')) {
				mkdir(FCPATH . '/uploads/transaksi_spp/');
			}

			if (!empty($transaksi_spp_file_kwitansi_uuid)) {
				$transaksi_spp_file_kwitansi_name_copy = date('YmdHis') . '-' . $transaksi_spp_file_kwitansi_name;

				rename(
					FCPATH . 'uploads/tmp/' . $transaksi_spp_file_kwitansi_uuid . '/' . $transaksi_spp_file_kwitansi_name,
					FCPATH . 'uploads/transaksi_spp/' . $transaksi_spp_file_kwitansi_name_copy
				);

				if (!is_file(FCPATH . '/uploads/transaksi_spp/' . $transaksi_spp_file_kwitansi_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
					]);
					exit;
				}

				$save_data['file_kwitansi'] = $transaksi_spp_file_kwitansi_name_copy;
			}


			$save_transaksi_spp = $this->model_transaksi_spp->change($id, $save_data);

			if ($this->input->post('status_transaksi') == "2") {
				$va_number = $this->input->post('va_number');
				//cek va sudah tersedia / belum
				$get_last_va = $this->mymodel->withquery("select va_number from transaksi_spp where user_name = '".$this->input->post('user_name')."' and status_transaksi = '2' and no_transaksi like '%".strtoupper($jenjang)."%'" , "row");
				if (!empty($get_last_va) && $va_number == "") {
					$va_number = $get_last_va->va_number;
				}
				$get_transaksi_spp = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = '" . $id . "'", "row");
				$jenjang = explode('-',$get_transaksi_spp->no_transaksi)[1];
				$bulan = explode('-',$get_transaksi_spp->no_transaksi)[4];
				$id_spp = $get_transaksi_spp->id_spp;
				// $ajaran = explode('-',$get_transaksi_spp->no_transaksi)[2];
				// $id_siswa = explode('-',$get_transaksi_spp->no_transaksi)[3];
				if(strtoupper($jenjang) == "SD"){
					$table = "spp_sd";
				}else if(strtoupper($jenjang) == "SMP"){
					$table = "spp_smp";
				}else if(strtoupper($jenjang) == "SMA"){
					$table = "spp_sma";
				}else{
					$table = "spp_ft";
				}
				$upd_spp = $this->model_transaksi_spp->updateSpp($table, $id_spp, $bulan);
				
				 $kode_tagihan = $get_transaksi_spp->no_transaksi;
				 $total_biaya = $get_transaksi_spp->total_biaya; //* $get_transaksi_spp->count_bill;

				//CETAK KWITANSI SPP
				 $cetak_kwitansi = $this->cetak_kwitansi_spp(array(
				 	"tipe_siswa" => $jenjang,
				 	"jenjang" => strtoupper($jenjang),
				 	"nama_lengkap" => $get_transaksi_spp->user_name,
				 	"va_number" => $get_transaksi_spp->va_number,
				 	"total_biaya" => $total_biaya,
				 	"total_biaya_terbilang" => terbilang($total_biaya) . " Rupiah",
				 	"id_siswa" => $get_transaksi_spp->id_siswa_aktif,
				 	"detail_bulan" => $get_transaksi_spp->bulan,
				 	"no_transaksi" => $kode_tagihan
				 ));
				 //cek($cetak_kwitansi, true);
				 $data_transaksi = array(
				 	"status_transaksi" => '2',
				 	"updated_at" => date("Y-m-d H:i:s"),
					"updated_from" => 'admin:' . $this->session->userdata('username'),
					"count_bill" => 1,
					"detail_bulan" => $get_transaksi_spp->bulan,
					"kode_tagihan" => "Manual-".$this->session->userdata('id')."-".$this->session->userdata('username')."-".date("Y-m-d H:i:s"),
					"expired_datetime" => date('Y-m-d H:i:s', strtotime('+1 day', strtotime(date('Y-m-d H:i:s')))),
				 	"va_number" => $va_number,
				 	"file_kwitansi" => $kode_tagihan . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf'
				 );

				//UPDATE TRANSAKSI SPP
				 $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, "no_transaksi", $kode_tagihan);
				
				//Cek Update status boleh ujian
				$get_terakhir_bayar = $this->mymodel->getbywhere("setting_sync_ujian","jenjang", $jenjang, "row");
				$get_siswa = $this->mymodel->withquery("select id_siswa_".strtolower($jenjang)."_aktif as id_siswa_aktif, nomor_peserta_ujian, nama_lengkap, id_tahun_ajaran from siswa_".strtolower($jenjang)."_aktif where id_siswa_".$jenjang."_aktif = '".$get_transaksi_spp->id_siswa_aktif."' order by nomor_peserta_ujian ASC","result");
				foreach ($get_siswa as $key => $value) {
					$nama_aktif = "id_siswa_".strtolower($jenjang)."_aktif";
					$get_spp = $this->mymodel->withquery("select * from spp_".strtolower($jenjang)." where id_siswa_aktif = '".$value->id_siswa_aktif."' and id_tahun_ajaran = '".$value->id_tahun_ajaran."'", "row");
					
					//$bulan_ini = date("Y-m-d", strtotime("-1 months"));
					$bulan_ini = $tanggal;
					$bulan_ini = formatBulan($bulan_ini);
					$get_transaksi = $this->mymodel->withquery("select status_transaksi,bulan from transaksi_spp where id_spp = '".$get_spp->id."' and no_transaksi like '%".$jenjang."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$get_transaksi_spp->id_tahun_ajaran."' order by id_transaksi DESC","row");
					if ($get_transaksi->status_transaksi == 2) {
						$this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => $value->nama_lengkap, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
					}
					else{
						$this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => $value->nama_lengkap), "nomor_peserta_ujian", $value->nomor_peserta_ujian);
					}
				}
				//Done Update Status ujian
			}

			if ($this->input->post('status_transaksi') == "0") {
				$get_transaksi_spp = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = '" . $id . "'", "row");
				$kode_tagihan = $get_transaksi_spp->kode_tagihan;
				$update = $this->mymodel->update2("transaksi_spp", array('status_transaksi' => '0', 'expired_datetime' => NULL, 'kode_tagihan' => '', 'updated_at' => NULL, 'file_slip' => '', 'file_kwitansi' => '', 'detail_bulan' => NULL, 'updated_from' => 'admin:' . $this->session->userdata('username')), "kode_tagihan", $kode_tagihan);
				$no_tranksaksi = explode("-", $get_transaksi_spp->no_transaksi);
				$jenjang = strtolower($no_tranksaksi[1]);
				$id_siswa_aktif = $get_transaksi_spp->id_siswa_aktif;
				$id_tahun_ajaran = $get_transaksi_spp->id_tahun_ajaran;
				$bulan = strtolower($get_transaksi_spp->bulan);
				$update2 = $this->mymodel->update("spp_".$jenjang, array($bulan => NULL), "id", $get_transaksi_spp->id_spp);
			}

			if ($save_transaksi_spp) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/transaksi_spp', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', []),
						'success'
					);

					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi_spp');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
				} else {
					$this->data['success'] = true;
					$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi_spp');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = 'Opss validation failed';
			$this->data['errors'] = $this->form_validation->error_array();
		}

		echo json_encode($this->data);
	}

	function cetak_kwitansi_spp($get = '')
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
			$endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi_spp');
			//$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
			$url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
				'&tipe_siswa=' . urlencode($get['tipe_siswa']) .
				'&jenjang=' . urlencode($get['jenjang']) .
				'&nama_lengkap=' . urlencode($get['nama_lengkap']) .
				'&total_biaya=' . urlencode($get['total_biaya']) .
				'&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
				'&va_number=' . urlencode($get['va_number']) .
				'&detail_bulan=' . urlencode($get['detail_bulan']) .
				'&no_transaksi=' . urlencode($get['no_transaksi']);
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
		return $rs;
	}

	/**
	 * delete Transaksi Spps
	 *
	 * @var $id String
	 */
	public function delete($id = null)
	{
		$this->is_allowed('transaksi_spp_delete');

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
			set_message(cclang('has_been_deleted', 'transaksi_spp'), 'success');
		} else {
			set_message(cclang('error_delete', 'transaksi_spp'), 'error');
		}

		redirect_back();
	}

	/**
	 * Get detail Transaksi SPP (AJAX)
	 *
	 * @return JSON
	 */
	public function get_detail()
	{
		if (!$this->is_allowed('transaksi_spp_view', false)) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(403)
				->set_output(json_encode(['success' => false, 'message' => 'Forbidden']));
			exit;
		}

		$id = $this->input->post('id');
		$transaksi_spp = $this->model_transaksi_spp->find($id);

		if (!$transaksi_spp) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'message' => 'Data not found']));
			exit;
		}

		$status_text = '';
		$status_class = '';
		$status_icon = '';
		if ($transaksi_spp->status_transaksi == "0") {
			$status_text = "Menunggu Aktivasi";
			$status_class = "label-aktivasi";
			$status_icon = "fa-clock-o";
		} elseif ($transaksi_spp->status_transaksi == "1") {
			$status_text = "Menunggu Bayar";
			$status_class = "label-menunggu";
			$status_icon = "fa-hourglass-half";
		} else {
			$status_text = "Lunas";
			$status_class = "label-lunas";
			$status_icon = "fa-check";
		}

		$bank_color = '#95a5a6';
		if (strtoupper($transaksi_spp->nama_bank) == 'BRI') $bank_color = '#003d79';
		elseif (strtoupper($transaksi_spp->nama_bank) == 'BNI') $bank_color = '#f37021';

		echo json_encode([
			'success' => true,
			'data' => [
				'id_transaksi'    => $transaksi_spp->id_transaksi,
				'no_transaksi'    => $transaksi_spp->no_transaksi,
				'nama_bank'       => $transaksi_spp->nama_bank,
				'bank_color'      => $bank_color,
				'va_number'       => $transaksi_spp->va_number ?: '-',
				'user_email'      => $transaksi_spp->user_email,
				'user_name'       => $transaksi_spp->user_name,
				'description'     => $transaksi_spp->description,
				'total_biaya'     => $transaksi_spp->total_biaya,
				'status_text'     => $status_text,
				'status_class'    => $status_class,
				'status_icon'     => $status_icon,
				'bulan'           => $transaksi_spp->bulan,
				'kode_tagihan'    => $transaksi_spp->kode_tagihan ?: '-',
				'file_kwitansi'   => $transaksi_spp->file_kwitansi,
				'expired_datetime'=> $transaksi_spp->expired_datetime ? date('d/m/Y H:i', strtotime($transaksi_spp->expired_datetime)) : '-',
				'created_at'      => $transaksi_spp->created_at ? date('d/m/Y H:i', strtotime($transaksi_spp->created_at)) : '-',
				'updated_at'      => $transaksi_spp->updated_at ? date('d/m/Y H:i', strtotime($transaksi_spp->updated_at)) : '-',
			]
		]);
	}

	/**
	 * View view Transaksi Spps
	 *
	 * @var $id String
	 */
	public function view($id)
	{
		$this->is_allowed('transaksi_spp_view');

		$this->data['transaksi_spp'] = $this->model_transaksi_spp->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Transaksi Spp Detail');
		$this->render('backend/standart/administrator/transaksi_spp/transaksi_spp_view', $this->data);
	}

	/**
	 * delete Transaksi Spps
	 *
	 * @var $id String
	 */
	private function _remove($id)
	{
		$transaksi_spp = $this->model_transaksi_spp->find($id);

		if (!empty($transaksi_spp->file_kwitansi)) {
			$path = FCPATH . '/uploads/transaksi_spp/' . $transaksi_spp->file_kwitansi;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}


		return $this->model_transaksi_spp->remove($id);
	}

	/**
	 * Upload Image Transaksi Spp	* 
	 * @return JSON
	 */
	public function upload_file_kwitansi_file()
	{
		if (!$this->is_allowed('transaksi_spp_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'transaksi_spp',
		]);
	}

	/**
	 * Delete Image Transaksi Spp	* 
	 * @return JSON
	 */
	public function delete_file_kwitansi_file($uuid)
	{
		if (!$this->is_allowed('transaksi_spp_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		echo $this->delete_file([
			'uuid'              => $uuid,
			'delete_by'         => $this->input->get('by'),
			'field_name'        => 'file_kwitansi',
			'upload_path_tmp'   => './uploads/tmp/',
			'table_name'        => 'transaksi_spp',
			'primary_key'       => 'id_transaksi',
			'upload_path'       => 'uploads/transaksi_spp/'
		]);
	}

	/**
	 * Get Image Transaksi Spp	* 
	 * @return JSON
	 */
	public function get_file_kwitansi_file($id)
	{
		if (!$this->is_allowed('transaksi_spp_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
			]);
			exit;
		}

		$transaksi_spp = $this->model_transaksi_spp->find($id);

		echo $this->get_file([
			'uuid'              => $id,
			'delete_by'         => 'id',
			'field_name'        => 'file_kwitansi',
			'table_name'        => 'transaksi_spp',
			'primary_key'       => 'id_transaksi',
			'upload_path'       => 'uploads/transaksi_spp/',
			'delete_endpoint'   => 'administrator/transaksi_spp/delete_file_kwitansi_file'
		]);
	}


	/**
	 * Export to excel
	 *
	 * @return Files Excel .xls
	 */
	public function export()
	{
		$this->is_allowed('transaksi_spp_export');

		//$this->model_transaksi_spp->export('transaksi_spp', 'transaksi_spp');
		$field = $this->input->get("f");
		$inputan = $this->input->get("q");
		$where = null;
		$field_search   = ['no_transaksi', 'nama_bank', 'va_number', 'user_email', 'user_name', 'description', 'total_biaya', 'status_transaksi', 'expired_datetime', 'created_at', 'updated_at', 'bulan', 'kode_tagihan', 'file_kwitansi'];
		if (empty($field) && empty($inputan)) {
			//export all data
			$get_data = $this->mymodel->withquery("select ts.no_transaksi, ts.nama_bank, ts.va_number, ts.user_email, ts.user_name, ts.description, ts.total_biaya, ts.status_transaksi, ts.expired_datetime, ts.created_at, ts.updated_at, ts.bulan, ts.kode_tagihan, ts.file_kwitansi from transaksi_spp ts order by ts.updated_at DESC, ts.id_transaksi DESC","result");
		}
		else if(!empty($field) && !empty($inputan)){
			//export all data based on input where specific field
			if($field == "status_transaksi"){
                if (strpos($inputan, "menunggu aktivasi") == true || $inputan == "0") {
                    $where .= "(" . "ts.status_transaksi = '0' ) ";
                }
                else if (strpos($inputan, "menunggu pembayaran") == true || $inputan == "1") {
                    $where .= "(" . "ts.status_transaksi = '1' ) ";
                }
                else{
                    $where .= "(" . "ts.status_transaksi = '2' ) ";
                }
            }
            else{
                $where .= "(" . "ts.".$field . " LIKE '%" . $inputan . "%' ) ";
            }
			$get_data = $this->mymodel->withquery("select ts.no_transaksi, ts.nama_bank, ts.va_number, ts.user_email, ts.user_name, ts.description, ts.total_biaya, ts.status_transaksi, ts.expired_datetime, ts.created_at, ts.updated_at, ts.bulan, ts.kode_tagihan, ts.file_kwitansi from transaksi_spp ts where ".$where." order by ts.updated_at DESC, ts.id_transaksi DESC","result");
		}
		else{
			//export all data based on input where all field
			$iterasi = 1;
			foreach ($field_search as $field) {
	                if ($iterasi == 1) {
	                    $where .= "ts.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                else if($field == "status_transaksi"){
	                	if (strpos($inputan, "menunggu aktivasi") == true || $inputan == "0") {
		                    $where .= "OR " . "(" . "ts.status_transaksi = '0' ) ";
		                }
		                else if (strpos($inputan, "menunggu pembayaran") == true || $inputan == "1") {
		                    $where .= "OR " . "(" . "ts.status_transaksi = '1' ) ";
		                }
		                else{
		                    $where .= "OR " . "(" . "ts.status_transaksi = '2' ) ";
		                }
	                }
	                else {
	                    $where .= "OR " . "ts.".$field . " LIKE '%" . $inputan . "%' ";
	                }
	                $iterasi++;
	        }
	            $where = '('.$where.')';
	            $get_data = $this->mymodel->withquery("select ts.no_transaksi, ts.nama_bank, ts.va_number, ts.user_email, ts.user_name, ts.description, ts.total_biaya, ts.status_transaksi, ts.expired_datetime, ts.created_at, ts.updated_at, ts.bulan, ts.kode_tagihan, ts.file_kwitansi from transaksi_spp ts where ".$where." order by ts.updated_at DESC, ts.id_transaksi DESC","result");
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
			if (strpos($field_search[$i], "created")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL BUAT TRANSAKSI'));
			}
			else if (strpos($field_search[$i], "updated")) {
				$objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, strtoupper('TANGGAL UBAH TRANSAKSI'));
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
		        else if($kolom == "status_transaksi"){
		        	if ($value->$kolom == "0") {
		        		$value_data = "Menunggu Aktivasi";
		        	}
		        	else if($value->$kolom == "1"){
		        		$value_data = "Menunggu Pembayaran";
		        	}
		        	else if($value->$kolom == "2"){
		        		$value_data = "Pembayaran Berhasil";
		        	}
		        	else{
		        		$value_data = "";
		        	}
		        }
		        elseif ($value->$kolom != "")  {
		            $value_data = strip_tags($value->$kolom);  
		        }
		        else  {
		            $value_data = "";  
		        }
		        if (strpos($kolom, "file") !== false && !empty($value_data)) {
		        	$value_data = base_url("uploads/kwitansi/").$value_data;
		        }
		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value_data);
		        //echo "(".$column.$rowCount." - ".$field_search[$j].":".$value_data.") _ ";
				$column++;
			}
			$rowCount++;
		}
		// Redirect output to a client’s web browser (Excel5) 
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename="Transaksi SPP_'.date("Y-m-d Hi").'.xls"'); 
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5'); 
		$objWriter->save('php://output');
	}

	/**
	 * Export to PDF
	 *
	 * @return Files PDF .pdf
	 */
	public function export_pdf()
	{
		$this->is_allowed('transaksi_spp_export');

		$this->model_transaksi_spp->pdf('transaksi_spp', 'transaksi_spp');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('transaksi_spp_export');

		$table = $title = 'transaksi_spp';
		$this->load->library('HtmlPdf');

		$config = array(
			'orientation' => 'p',
			'format' => 'a4',
			'marges' => array(5, 5, 5, 5)
		);

		$this->pdf = new HtmlPdf($config);
		$this->pdf->setDefaultFont('stsongstdlight');

		$result = $this->db->get($table);

		$data = $this->model_transaksi_spp->find($id);
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

	function aktivasi_tagihan(){
		$jenjang = $this->input->post('jenjang');
		$id_siswa = $this->input->post('id_siswa_aktif');
		$id_trans = $this->input->post('id_transaksi');
		$total_biaya = $this->input->post('total_biaya');
		
		
	}

	public function get_siswa(){
		$jenjang = $this->input->post('jenjang');
		$data = $this->mymodel->withquery("select sa.id_siswa_".strtolower($jenjang)."_aktif as id_siswa, sa.nama_lengkap, k.label as nama_kelas from siswa_".strtolower($jenjang)."_aktif sa 
		join kelas_".strtolower($jenjang)." k on k.id_kelas_".strtolower($jenjang)." = sa.id_kelas 
		where sa.deleted_at is null order by k.label asc, sa.nama_lengkap asc","result");
		
		$daftar_select = "";
		if(!empty($data)){
			$daftar_select .= "<option value=''>-- Pilih Siswa --</option>";
			foreach ($data as $key => $item) {
				$daftar_select .= "<option value='".$item->id_siswa."'>".$item->nama_kelas." - ".$item->nama_lengkap."</option>";
			}
			$daftar_select .= "";
		}
		echo $daftar_select;
	}

	public function cek_tagihan() {
		$id_siswa = $this->input->post('id_siswa');
		$jenjang  = strtolower($this->input->post('jenjang'));
	
		$data = $this->mymodel->withquery("
			SELECT t.id_transaksi, t.bulan, t.kode_tagihan, th.label as tahun_ajaran, t.total_biaya
			FROM transaksi_spp t
			join tahun_ajaran th on th.id_tahun_ajaran = t.id_tahun_ajaran 
			WHERE t.id_siswa_aktif = '$id_siswa'
			  AND t.no_transaksi like '%".$jenjang."%'
			  AND t.status_transaksi = '0' 
			ORDER BY t.id_transaksi DESC
		", "result");
	
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($data));
	}

	/**
	 * AJAX: Hitung ketepatan pembayaran SPP untuk pie chart.
	 * POST params: bulan (string nama bulan atau kosong = semua), id_ta
	 * Return JSON: { success:true, data:{ all:{...}, sd:{...}, smp:{...}, sma:{...}, ft:{...} } }
	 * Tiap obj: { lebih_awal:int, tepat_waktu:int, telat_bayar:int }
	 */
	public function ajax_ketepatan_pembayaran()
	{
		$bulan = trim((string) $this->input->post('bulan'));
		$id_ta = (int) $this->input->post('id_ta');

		$where_status = "t.status_transaksi = '2'";
		$where_ta     = $id_ta > 0 ? "t.id_tahun_ajaran = " . $id_ta : "1=1";
		$where_bulan  = ($bulan !== '') ? "t.bulan = " . $this->db->escape($bulan) : "t.bulan IS NOT NULL";

		// Mapping bulan text -> ekspresi DATE tgl awal bulan tsb (pakai tanggal_mulai/tanggal_selesai TA)
		$bulan_expr = "
			CASE t.bulan
				WHEN 'Juli'     THEN CONCAT(YEAR(ta.tanggal_mulai),'-07-01')
				WHEN 'Agustus'  THEN CONCAT(YEAR(ta.tanggal_mulai),'-08-01')
				WHEN 'September' THEN CONCAT(YEAR(ta.tanggal_mulai),'-09-01')
				WHEN 'Oktober'  THEN CONCAT(YEAR(ta.tanggal_mulai),'-10-01')
				WHEN 'November' THEN CONCAT(YEAR(ta.tanggal_mulai),'-11-01')
				WHEN 'Desember' THEN CONCAT(YEAR(ta.tanggal_mulai),'-12-01')
				WHEN 'Januari'  THEN CONCAT(YEAR(ta.tanggal_selesai),'-01-01')
				WHEN 'Februari' THEN CONCAT(YEAR(ta.tanggal_selesai),'-02-01')
				WHEN 'Maret'    THEN CONCAT(YEAR(ta.tanggal_selesai),'-03-01')
				WHEN 'April'    THEN CONCAT(YEAR(ta.tanggal_selesai),'-04-01')
				WHEN 'Mei'      THEN CONCAT(YEAR(ta.tanggal_selesai),'-05-01')
				WHEN 'Juni'     THEN CONCAT(YEAR(ta.tanggal_selesai),'-06-01')
			END
		";

		$jenjang_expr = "LOWER(SUBSTRING_INDEX(SUBSTRING_INDEX(t.no_transaksi,'-',2),'-',-1))";

		// Klasifikasi row per baris (lebih_awal / tepat_waktu / telat_bayar)
		$kategori_expr = "
			CASE
				WHEN DATE(t.updated_at) < $bulan_expr                                THEN 'lebih_awal'
				WHEN DATE(t.updated_at) <= LAST_DAY($bulan_expr)
					AND YEAR(t.updated_at) = YEAR($bulan_expr)
					AND MONTH(t.updated_at) = MONTH($bulan_expr)                  THEN 'tepat_waktu'
				WHEN DATE(t.updated_at) > LAST_DAY($bulan_expr)                     THEN 'telat_bayar'
				ELSE 'telat_bayar'
			END
		";

		$sql = "
			SELECT $jenjang_expr AS jenjang, $kategori_expr AS kategori, COUNT(*) AS n
			FROM transaksi_spp t
			JOIN tahun_ajaran ta ON ta.id_tahun_ajaran = t.id_tahun_ajaran
			WHERE $where_status AND $where_ta AND $where_bulan
			GROUP BY jenjang, kategori
		";

		$rows = $this->db->query($sql)->result();

		// Inisialisasi bucket
		$buckets = array();
		$keys = array('all','sd','smp','sma','ft');
		foreach ($keys as $k) {
			$buckets[$k] = array('lebih_awal' => 0, 'tepat_waktu' => 0, 'telat_bayar' => 0);
		}

		if (!empty($rows)) {
			foreach ($rows as $r) {
				$kat = isset($r->kategori) ? $r->kategori : '';
				$jen = isset($r->jenjang) ? strtolower($r->jenjang) : '';
				$n   = (int) $r->n;
				if (!isset($buckets[$jen]) || $kat === '') {
					continue;
				}
				$buckets[$jen][$kat] += $n;
				$buckets['all'][$kat] += $n;
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(array('success' => true, 'data' => $buckets)));
	}

	public function aktivasi_va(){
		$tagihan = $this->input->post('tagihan');
		$total_biaya = 0;
		$id_siswa_aktif = $this->input->post('id_siswa_aktif');
		$jenjang = $this->input->post('jenjang');
		$id_transaksi = "";
		foreach ($tagihan as $key => $item) {
			if($id_transaksi == "") {
				$id_transaksi = $item['id_transaksi'];
			}
			else {
				$id_transaksi .= ",".$item['id_transaksi'];
			}
			$total_biaya += $item['total_biaya'];
		}

		//curl post ke apiapp/spp/create_tagihan_noslip
		if($jenjang == "sd"){
			$url = base_url('/apiapp/spp/create_tagihan_sd_noslip_admin');
		}
		else{
			$url = base_url('/apiapp/spp/create_tagihan_noslip_admin');
		}
		$ch = curl_init();
		//header
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json',
			'x-api-key: 708B6E99028D8E71E7A7B0BDCAA88E70'
		));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
			'id_transaksi' => $id_transaksi,
			'jenjang' => $jenjang,
			'id_siswa_aktif' => $id_siswa_aktif,
			'total_biaya' => $total_biaya,
		]));
		$response = curl_exec($ch);
		curl_close($ch);
		echo $response;
	}
}


/* End of file transaksi_spp.php */
/* Location: ./application/controllers/administrator/Transaksi Spp.php */
