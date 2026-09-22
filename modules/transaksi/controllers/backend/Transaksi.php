<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Transaksi Controller
*| --------------------------------------------------------------------------
*| Transaksi site
*|
*/
class Transaksi extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_transaksi');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Transaksis
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('transaksi_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');
		$sort 	= $this->input->get('s');
		$sort_type 	= $this->input->get('d');
		$limit=20;
		// dd($offset);
		$this->data['transaksis'] = $this->model_transaksi->get($filter, $field, $limit, $offset ,$sort, $sort_type);
		$this->data['transaksi_counts'] = $this->model_transaksi->count_all($filter, $field);
		$this->data['summary'] = $this->model_transaksi->get_summary();

		$config = [
			'base_url'     => 'administrator/transaksi/index/',
			'total_rows'   => $this->model_transaksi->count_all($filter, $field),
			'per_page'     => $limit,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		
		$this->template->title('Transaksi List');
		$this->render('backend/standart/administrator/transaksi/transaksi_list', $this->data);
	}
	
	
		/**
	* Update view Transaksis
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('transaksi_update');

		$this->data['transaksi'] = $this->model_transaksi->find($id);

		$this->template->title('Transaksi Update');
		$this->render('backend/standart/administrator/transaksi/transaksi_update', $this->data);
	}

	/**
	* Update Transaksis
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('transaksi_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[255]');
		$this->form_validation->set_rules('status_transaksi', 'Status Transaksi', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'description' => $this->input->post('description'),
				'total_biaya' => $this->input->post('total_biaya'),
				'status_transaksi' => $this->input->post('status_transaksi'),
				'is_show' => $this->input->post('is_show'),
			];

			$pesan_cetak = '';

			$old_data=$this->model_transaksi->find($id);

			if($old_data->status_transaksi==0 && $this->input->post('status_transaksi')==1){
				$save_data['updated_at']=date("Y-m-d H:i:s");
			}
			
			$save_transaksi = $this->model_transaksi->change($id, $save_data);

			if ($save_transaksi) {
				if ($save_data['status_transaksi'] == "1") {
					$get_transaksi = $this->mymodel->withquery("select va_number, nama_bank, no_transaksi, total_biaya from transaksi where id_transaksi = '".$id."'","row");
					//get data siswa
					$trx = explode("-", $get_transaksi->no_transaksi);
					$jenjang = $trx[1];
					$id_siswa = $trx[3];
					//peta kode jenjang di no_transaksi ke tabel siswa & kode unit no peserta
					$peta_jenjang = array(
						"SD" => array("tabel" => "sd", "unit" => "11"),
						"PSBSD" => array("tabel" => "sd", "unit" => "11"),
						"SDM" => array("tabel" => "sd", "unit" => "13"),
						"SMP" => array("tabel" => "smp", "unit" => "21"),
						"PSBSMP" => array("tabel" => "smp", "unit" => "22"),
						"PPSBBSMP" => array("tabel" => "smp", "unit" => "21"),
						"SMA" => array("tabel" => "sma", "unit" => "31"),
						"PSBSMA" => array("tabel" => "sma", "unit" => "32"),
						"PPSBBSMA" => array("tabel" => "sma", "unit" => "31"),
						"FT" => array("tabel" => "ft", "unit" => "42"),
						"PPSBBFT" => array("tabel" => "ft", "unit" => "41"),
					);
					if (isset($peta_jenjang[$jenjang])) {
						$tabel_siswa = $peta_jenjang[$jenjang]["tabel"];
						$unit = $peta_jenjang[$jenjang]["unit"];
					}
					else {
						$tabel_siswa = strtolower($jenjang);
						$unit = "";
					}
					$id_field_siswa = "id_siswa_".$tabel_siswa;
					$get_siswa = $this->mymodel->withquery("select * from siswa_".$tabel_siswa." where ".$id_field_siswa." = '".$id_siswa."' ","row");
					//susunan nomor peserta
						$tahun_ajar_psb = $this->mymodel->getbywhere("tahun_ajaran_psb", "id",1,"row")->label;
						$tahun_pelajaran = substr($tahun_ajar_psb,2,2);
						$tahun_pelajaran = $tahun_pelajaran.substr($tahun_ajar_psb,7,2);
						/* $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_".strtolower($jenjang)."  as id_siswa from siswa_".strtolower($jenjang)."  where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' order by no_peserta DESC", "row");
						if (empty($get_no_urut)) {
						$no_urut = "0001";
						} else {
						// $no_urut = $this->mymodel->withquery("select count(nama_lengkap) from siswa_".strtolower($jenjang)."  WHERE no_peserta like '%".$tahun_pelajaran.$unit."%'", "row");
						$no_urut = (int) substr($get_no_urut->no_peserta, -4);
						$no_urut = $no_urut + 1;
						$no_urut = sprintf("%04d", $no_urut);
						}
						$no_peserta = $tahun_pelajaran . $unit . $no_urut; */
						$get_list = $this->mymodel->withquery("
							SELECT CAST(RIGHT(no_peserta, 4) AS UNSIGNED) AS urut
							FROM siswa_" . $tabel_siswa . "
							WHERE no_peserta LIKE '%" . $tahun_pelajaran . $unit . "%'
								AND no_peserta != ''
							ORDER BY urut ASC
						", "result");

						// Default no urut
						$no_urut = 1;

						// Cek nomor urut kosong
						if (!empty($get_list)) {
							foreach ($get_list as $row) {
								if ((int)$row->urut != $no_urut) {
									// Ada celah, gunakan no urut ini
									break;
								}
								$no_urut++;
							}
						}

						// Format ke 4 digit
						$no_urut = sprintf("%04d", $no_urut);

						// Gabungkan ke format akhir
						$no_peserta = $tahun_pelajaran . $unit . $no_urut;
						$cek_no_peserta = $this->mymodel->withquery("select no_peserta, ".$id_field_siswa."  as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_".$tabel_siswa."  where ".$id_field_siswa." = '".$id_siswa."'", "row");
						if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
							//cek duplikat no peserta, geser urut bila sudah dipakai
							$cek_duplikat = $this->mymodel->withquery("select no_peserta from siswa_".$tabel_siswa." where no_peserta = '".$no_peserta."'", "row");
							while (!empty($cek_duplikat)) {
								$no_urut = (int)$no_urut + 1;
								$no_urut = sprintf("%04d", $no_urut);
								$no_peserta = $tahun_pelajaran . $unit . $no_urut;
								$cek_duplikat = $this->mymodel->withquery("select no_peserta from siswa_".$tabel_siswa." where no_peserta = '".$no_peserta."'", "row");
							}
							$data_siswa = $this->mymodel->update("siswa_".$tabel_siswa, array("no_peserta" => $no_peserta, "password_ujian" => rand(100000, 999999)), $id_field_siswa, $cek_no_peserta->id_siswa);
						}

					//CETAK KWITANSI (meniru Notification.php)
					$nama_ortu = $cek_no_peserta->nama_ibu;
					if (empty($nama_ortu)) {
						$nama_ortu = $cek_no_peserta->nama_ayah;
					}
					$cetak_kwitansi = $this->cetak_kwitansi(array(
						"id_siswa" => $id_siswa,
						"tipe_siswa" => $tabel_siswa,
						"jenjang" => $tabel_siswa,
						"nama_lengkap" => $cek_no_peserta->nama_lengkap,
						"nama_ortu" => $nama_ortu,
						"total_biaya" => $get_transaksi->total_biaya,
						"total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
						"va_number" => $get_transaksi->va_number,
						"no_transaksi" => $get_transaksi->no_transaksi,
						"jalur" => "PSB",
						"jenis_kwitansi" => "uang pendaftaran"
					));

					//link cetak kwitansi & kartu peserta untuk ditampilkan ke admin
					$link_kwitansi = base_url('uploads/kwitansi/') . $get_transaksi->no_transaksi . '-' . $cek_no_peserta->nama_lengkap . '.pdf';
					if ($cetak_kwitansi) {
						$res_kwitansi = json_decode($cetak_kwitansi);
						if (!empty($res_kwitansi->data->url_file)) {
							$link_kwitansi = $res_kwitansi->data->url_file;
						}
					}
					$link_kartu = site_url('apiapp/siswa/export_pdf_siswa') . '?id_siswa=' . $id_siswa . '&tipe_siswa=' . $tabel_siswa;
					$pesan_cetak = '<br>Cetak <a href="' . $link_kwitansi . '" target="_blank">Kwitansi (PDF)</a> &nbsp;|&nbsp; <a href="' . $link_kartu . '" target="_blank">Kartu Peserta (PDF)</a>';

					//MASUKKAN KE RUANG KELAS YANG TERSEDIA DENGAN ADD TO ujian_ruang_pendaftaran_detail_ft
					$cek_ruangan = $this->mymodel->withquery("select r.id_ruang_pendaftaran, r.nama_ruang, r.maks_peserta, 
					(select count(*) from ujian_ruang_pendaftaran_detail_".$tabel_siswa." 
					where r.id_ruang_pendaftaran = ujian_ruang_pendaftaran_detail_".$tabel_siswa.".id_ruang_pendaftaran) as total_peserta_now 
					from ujian_ruang_pendaftaran_".$tabel_siswa." r order by nama_ruang ASC","result");
					foreach ($cek_ruangan as $key => $item) {
						if ($item->total_peserta_now < $item->maks_peserta) {
							$data_ruangan = array(
								"id_ruang_pendaftaran" => $item->id_ruang_pendaftaran,
								"nomor_peserta" => $no_peserta,
								"added_at" => date("Y-m-d H:i:s"),
							);
							//cek jika sudah ada data peserta di ruang ini
							$cek_registered = $this->mymodel->withquery("select * from ujian_ruang_pendaftaran_detail_".$tabel_siswa." 
							where id_ruang_pendaftaran = ".$item->id_ruang_pendaftaran." and nomor_peserta = '".$no_peserta."'","row");
							if(empty($cek_registered)){
								$ruangan_peserta = $this->mymodel->insertid("ujian_ruang_pendaftaran_detail_".$tabel_siswa, $data_ruangan);
								$this->data['success'] = true;
								$this->data['message'] = 'Siswa berhasil terdaftar di ruang '.$item->nama_ruang;
							}
							else{
								$this->data['success'] = false;
								$this->data['message'] = 'Siswa sudah terdaftar di ruang '.$item->nama_ruang;
							}
							break;
						}
					}
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/transaksi', ' Go back to list')
					]) . $pesan_cetak;
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]) . $pesan_cetak, 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/transaksi');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/transaksi');
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
	* delete Transaksis
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('transaksi_delete');

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
            set_message(cclang('has_been_deleted', 'transaksi'), 'success');
        } else {
            set_message(cclang('error_delete', 'transaksi'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Transaksis
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('transaksi_view');

		$this->data['transaksi'] = $this->model_transaksi->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Transaksi Detail');
		$this->render('backend/standart/administrator/transaksi/transaksi_view', $this->data);
	}
	
	/**
	* delete Transaksis
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$transaksi = $this->model_transaksi->find($id);

		
		
		return $this->model_transaksi->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('transaksi_export');

		$this->model_transaksi->export_transaksi('transaksi', 'transaksi');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('transaksi_export');

		$this->model_transaksi->pdf('transaksi', 'transaksi');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('transaksi_export');

		$table = $title = 'transaksi';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_transaksi->find($id);
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

	function cetak_kwitansi($get = '')
  	{
	    //meniru cetak_kwitansi() di application/controllers/apiapp/Notification.php
	    $header[] = 'Content-Type: application/json';
	    $header[] = "Accept-Encoding: gzip, deflate";
	    $header[] = "Cache-Control: max-age=0";
	    $header[] = "Connection: keep-alive";
	    $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_VERBOSE, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_ENCODING, true);
	    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

	    if ($get) {
	      $endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi');
	      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&tipe_siswa=' . urlencode($get['tipe_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&nama_ortu=' . urlencode($get['nama_ortu']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&jalur=' . urlencode($get['jalur']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&jenis_kwitansi=' . urlencode($get['jenis_kwitansi']);
	      curl_setopt($ch, CURLOPT_URL, $url);
	    }
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	    $rs = curl_exec($ch);

	    if (empty($rs)) {
	      curl_close($ch);
	      return false;
	    }
	    curl_close($ch);
	    return $rs;
  	}

	public function multi_reactivate_va($id = null)
	{
		$arr_id = $this->input->get('id');
		$updated = false;
		if (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$get_transaksi = $this->mymodel->withquery("select * from transaksi where id_transaksi = '".$id."'","row");
				if($get_transaksi->status_transaksi==0 && !empty($get_transaksi->va_number)){
					if($get_transaksi->nama_bank=="BNI"){
						$no_transaksi = $get_transaksi->no_transaksi;
						$no_transaksi = explode("-", $no_transaksi);
						$no_transaksi = $no_transaksi[0]."-".$no_transaksi[1]."-".date("YmdHi")."-".$no_transaksi[3];
						$get_transaksi->jenjang = $no_transaksi[1];
						//update database
						$this->mymodel->update("transaksi", array("no_transaksi" => $no_transaksi), "id_transaksi", $id);
						$payment_response = $this->create_billing_bni("production", $get_transaksi->total_biaya, $no_transaksi, $get_transaksi);
					}
		
					if($get_transaksi->nama_bank=="BRI"){
						$datas = array(
							'brivaNo' => substr($get_transaksi->va_number, 0, 5),
							'custCode' => substr($get_transaksi->va_number, 5),
							'nama' => $get_transaksi->user_name,
							'amount' => $get_transaksi->total_biaya,
							'keterangan' => substr($get_transaksi->va_number, 0,25),
							'expiredDate' => date("Y-m-d H:i:s", strtotime("+1 days"))
						);
						// $payment_response = $this->update_va_bri($datas);
						//delete va dahulu
						$delete_response = $this->delete_va_bri(array("brivaNo" => $datas["brivaNo"], "custCode" => $datas["custCode"]));
						$payment_response = $this->create_va_bri($datas);
					}
					//print_r($payment_response);
					if($payment_response){
						$this->mymodel->update("transaksi",array("status_transaksi"=>0, "expired_datetime"=>date("Y-m-d H:i:s", strtotime("+1 days")) ),array("id_transaksi"=>$id));
						set_message("Transaksi berhasil diaktifkan!","success");
					}else{
						set_message("Transaksi sudah aktif","danger");
					}
					set_message("Transaksi berhasil diaktifkan","success");
				}else{
					set_message("Transaksi sudah selesai","warning");
				}
			}
		}
		
		redirect_back();
	}

	function reactivate_va($id = null){
		$get_transaksi = $this->mymodel->withquery("select * from transaksi where id_transaksi = '".$id."'","row");
		if($get_transaksi->status_transaksi==0 && !empty($get_transaksi->va_number)){
			if($get_transaksi->nama_bank=="BNI"){
				$no_transaksi = $get_transaksi->no_transaksi;
				$no_transaksi = explode("-", $no_transaksi);
				$no_transaksi = $no_transaksi[0]."-".$no_transaksi[1]."-".date("Ymd")."-".$no_transaksi[3];
				$get_transaksi->jenjang = $no_transaksi[1];
				//update database
				$payment_response = $this->create_billing_bni("production", $get_transaksi->total_biaya, $no_transaksi, $get_transaksi);
				//$payment_response_check = $this->check_billing_bni("production", $get_transaksi->total_biaya, $get_transaksi->no_transaksi, $get_transaksi);
				if($payment_response['status'] !== "000"){
					set_message("Gagal mengaktifkan ulang VA (".json_encode($payment_response).")","warning");
					// redirect_back();
				}
				else{
					$this->mymodel->update("transaksi",array("status_transaksi"=>0, "expired_datetime"=>date("Y-m-d H:i:s", strtotime("+1 days")) ),array("id_transaksi"=>$id));
				}
			}
			else if($get_transaksi->nama_bank=="BRI"){
				$datas = array(
					'brivaNo' => substr($get_transaksi->va_number, 0, 5),
					'custCode' => substr($get_transaksi->va_number, 5),
					'nama' => $get_transaksi->user_name,
					'amount' => $get_transaksi->total_biaya,
					'keterangan' => substr($get_transaksi->va_number, 0,25),
					'expiredDate' => date("Y-m-d H:i:s", strtotime("+1 days"))
				);
				// $payment_response = $this->update_va_bri($datas);
				//delete va dahulu
				$delete_response = $this->delete_va_bri(array("brivaNo" => $datas["brivaNo"], "custCode" => $datas["custCode"]));
				$payment_response = $this->create_va_bri($datas);
			}
			//print_r($get_transaksi);
			// print_r($payment_response);
			// print_r($payment_response_check);
			if($payment_response){
				if ($payment_response['status'] == "000") {
					//$this->mymodel->update("transaksi", array("no_transaksi" => $no_transaksi), "id_transaksi", $id);
				}
				$this->mymodel->update("transaksi",array("status_transaksi"=>0, "expired_datetime"=>date("Y-m-d H:i:s", strtotime("+1 days")) ),array("id_transaksi"=>$id));
				set_message("Transaksi berhasil diaktifkan! ","success");
			}else{
				set_message("Transaksi sudah aktif","danger");
			}
			set_message("Transaksi berhasil diaktifkan","success");
		}else{
			set_message("Transaksi sudah selesai","warning");
		}

		redirect_back();
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
			if ($item->name_setting == 'bri_institution_code_close') {
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

	public function delete_va_bri($datas)
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
			if ($item->name_setting == 'bri_institution_code_close') {
				$institutionCode = $item->value;
			}
		}

		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

		$data = array(
			'brivaNo' => $datas['brivaNo'],
			'custCode' => $datas['custCode'],
		);
		return BriApi::delete($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
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
			if ($item->name_setting == 'bri_institution_code_close') {
				$institutionCode = $item->value;
			}
		}

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

		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

		$data = array(
			'brivaNo' => $brivaNo,//$datas['brivaNo'],
			'expiredDate' => $datas['expiredDate'],//date("Y-m-d H:i:s", strtotime("+".$datas['day']." days")),
			'custCode' => $datas['custCode'],
			'nama' => $datas['nama'],
			'amount' => $datas['amount'],
			'keterangan' => $datas['keterangan']
		);
		//update status bayar
		$data_bayar = array(
			"institutionCode" => $institutionCode,
        	"brivaNo" => $brivaNo,//$datas['brivaNo'],
        	"custCode" => $datas['custCode'],
        	"statusBayar" => "N"
		);

		$update =  BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
		if ($update['responseCode'] != '00') {
			$update_bayar = BriApi::updateBayar($clientID,$clientSecret,$endpoint,$institutionCode,$data_bayar['brivaNo'],$data_bayar['custCode'],$data_bayar['statusBayar'],$url);
			return $update_bayar;
		}
		else{
			return $update;
		}
	}

	function create_billing_bni($production, $total, $no_transaksi, $data_user){
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
			}
			else if ($production == "development" || $production == "testing"){
			if ($value->name_setting == "bni_api_dev_url") {
				$url = $value->value;
			}
			}
		}

		// $get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		// foreach ($get_pengaturan_masa_aktif as $key => $item) {
		// 	if($item->label=='pendaftaran'){
		// 	if($item->tipe_date=='day'){
		// 		$date_va=($item->value*24) * 3600;
		// 	}else{
		// 		$date_va=($item->value) * 3600;
		// 	}
		// 	}
		// }

		$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		foreach ($get_pengaturan_masa_aktif as $key => $item) {
		  if($item->label=='pendaftaran'){
			if($item->tipe_date=='day'){
			  $date_va= date('c',time() + (($item->value*24) * 3600) );
			}else if($item->label == 'hour'){
			  $date_va= date('c',time() + (($item->value) * 3600) );
			}
			else if($item->label == 'date'){
			  $date_va= date('c',($item->value));
			}
		  }
		}
		$data_asli = array(
		  'type' => "createbilling",
		  'client_id' => $client_id,
		  'trx_id' => $no_transaksi,
		  'trx_amount' => $total,
		  'virtual_account' => $data_user->va_number,
		  'billing_type' => 'c',
		  'datetime_expired' => $date_va, 
		  'customer_name' => $data_user->user_name,
		  'customer_email' => $data_user->user_email,
		  'description' => "Pembayaran Tagihan ".$no_transaksi
		);
		//print_r($data_asli);
		  //'customer_phone' => $data_user['notelp'],
  
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
		//   var_dump($data_response);
		   return($data_response);
		}
	  }

	  function update_billing_bni($production, $total, $no_transaksi, $data_user){
		$this->load->library('BniEnc');
		// FROM BNI
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $value) {
			if($data_user->jenjang == "FT"){
				if ($value->name_setting == "bni_client_id_ft") {
					$client_id = $value->value;
				}
				if ($value->name_setting == "bni_secret_key_ft") {
					$secret_key = $value->value;
				}
			}
			else{
				if ($value->name_setting == "bni_client_id") {
					$client_id = $value->value;
				}
				if ($value->name_setting == "bni_secret_key") {
					$secret_key = $value->value;
				}
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

		$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		foreach ($get_pengaturan_masa_aktif as $key => $item) {
		  if($item->label=='pendaftaran'){
			if($item->tipe_date=='day'){
			  $date_va= date('c',time() + (($item->value*24) * 3600) );
			}else if($item->label == 'hour'){
			  $date_va= date('c',time() + (($item->value) * 3600) );
			}
			else if($item->label == 'date'){
			  $date_va= date('c',($item->value));
			}
		  }
		}
		$data_asli = array(
		  'type' => "updatebilling",
		  'client_id' => $client_id,
		  'trx_id' => $no_transaksi,
		  'trx_amount' => $total,
		  'virtual_account' => $data_user->va_number,
		  'datetime_expired' => date('c'), 
		  'customer_name' => $data_user->user_name,
		  'customer_email' => $data_user->user_email,
		  'description' => "Pembayaran Tagihan ".$no_transaksi
		);
		  //'customer_phone' => $data_user['notelp'],
  		//print_r($data_asli);
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

	  function check_billing_bni($production, $total, $no_transaksi, $data_user){
		$this->load->library('BniEnc');
		// FROM BNI
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $value) {
			if($data_user->jenjang == "FT"){
				if ($value->name_setting == "bni_client_id_ft") {
					$client_id = $value->value;
				}
				if ($value->name_setting == "bni_secret_key_ft") {
					$secret_key = $value->value;
				}
			}
			else{
				if ($value->name_setting == "bni_client_id") {
					$client_id = $value->value;
				}
				if ($value->name_setting == "bni_secret_key") {
					$secret_key = $value->value;
				}
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
  
		$get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		foreach ($get_pengaturan_masa_aktif as $key => $item) {
		  if($item->label=='pendaftaran'){
			if($item->tipe_date=='day'){
			  $date_va= date('c',time() + (($item->value*24) * 3600) );
			}else if($item->label == 'hour'){
			  $date_va= date('c',time() + (($item->value) * 3600) );
			}
			else if($item->label == 'date'){
			  $date_va= date('c',($item->value));
			}
		  }
		}
		$data_asli = array(
		  'type' => "inquirybilling",
		  'client_id' => $client_id,
		  'trx_id' => $no_transaksi,
		);
		//print_r($data_asli);
		  //'customer_phone' => $data_user['notelp'],
  
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

	  function get_content($url, $post = '') {
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
  
		if ($post)
		{
		  curl_setopt($ch, CURLOPT_POST, true);
		  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
		$rs = curl_exec($ch);
  
		if(empty($rs)){
		  var_dump($rs, curl_error($ch));
		  curl_close($ch);
		  return false;
		}
		curl_close($ch);
		return $rs;
	  }

	
}


/* End of file transaksi.php */
/* Location: ./application/controllers/administrator/Transaksi.php */