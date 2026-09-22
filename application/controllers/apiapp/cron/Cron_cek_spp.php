<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_cek_spp extends MY_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->library('Bri_spp_matcher');
        $this->load->library('Spp_payment_detail');
    }
    public function index()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $report_start_date_override = '';
        $report_end_date_override = '';
        $request_start_date = trim((string) $this->input->get('start_date'));
        $request_end_date = trim((string) $this->input->get('end_date'));

        if (($request_start_date == '' && $request_end_date != '') || ($request_start_date != '' && $request_end_date == '')) {
            $this->response(array('status' => 0, 'message' => 'start_date dan end_date wajib dikirim bersama'), 400);
        }

        if ($request_start_date != '') {
            $valid_start_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $request_start_date)
                && checkdate((int) substr($request_start_date, 5, 2), (int) substr($request_start_date, 8, 2), (int) substr($request_start_date, 0, 4));
            $valid_end_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $request_end_date)
                && checkdate((int) substr($request_end_date, 5, 2), (int) substr($request_end_date, 8, 2), (int) substr($request_end_date, 0, 4));

            if (!$valid_start_date || !$valid_end_date) {
                $this->response(array('status' => 0, 'message' => 'Format tanggal wajib Y-m-d'), 400);
            }
            if ($request_start_date > $request_end_date) {
                $this->response(array('status' => 0, 'message' => 'start_date tidak boleh lebih besar dari end_date'), 400);
            }

            $report_start_date_override = str_replace('-', '', $request_start_date);
            $report_end_date_override = str_replace('-', '', $request_end_date);
        }

        // Invoice cutoff normal = now; historical replay = akhir tanggal laporan.
        $invoice_cutoff = date('Y-m-d H:i:s');
        if ($report_end_date_override != '') {
            $invoice_cutoff = date('Y-m-d 23:59:59', strtotime($report_end_date_override));
        }
        $invoice_cutoff_sql = $this->db->escape($invoice_cutoff);

        // get all transaksi spp with status = 1 (menunggu pembayaran)
        $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, user_name as nama_siswa, id_siswa_aktif, id_tahun_ajaran, nama_bank, va_number, status_transaksi, id_spp, total_biaya, bulan, updated_at, expired_datetime, kode_tagihan, detail_bulan, count_bill from transaksi_spp where (va_number like '13803%' or va_number like '13802%') and (status_transaksi = 2 or expired_datetime >= " . $invoice_cutoff_sql . ") and status_transaksi in (1,2) order by id_transaksi DESC", "result");

        if (!empty($get_transaksi)) {
            foreach ($get_transaksi as $key => $value) {
                $no_transaksi = $value->no_transaksi;
                $bank = "BRI";//$value->nama_bank;
                $va_number = $value->va_number;
                $institution_code = substr($va_number, 0,5);
                $customer_code = substr($va_number, 5);
                $jenjang = explode("-", $no_transaksi);
                $jenjang = strtolower($jenjang[1]);
                $bulan = strtolower($value->bulan);

                if ($value->status_transaksi == 1) {
                    $data = $this->check_inquiry($bank, $no_transaksi, $va_number);
                    $data_bayar = $data['data'];
                    $status_bayar = $data_bayar['statusBayar'];
                    $keterangan = $data_bayar['Keterangan'];
                    $nominal_biaya = (int)$data_bayar['Amount'];
                    $data_id_transaksi = explode("-", $keterangan);
                    /* if ($status_bayar == "Y") {
                        $cek_nama = null;
                        $cek_nama = strpos(strtoupper($value->nama_siswa), $data_bayar['Nama'], 0);
                        if ($nominal_biaya == (int)$value->total_biaya && $cek_nama !== false) {
                          //cek data update at SPP
                          $cek_spp = $this->mymodel->withquery("select ".$bulan." from spp_".$jenjang." where id = '".$value->id_spp."' ","row");
                          if (empty($cek_spp->$bulan)) {
                              $update_spp = $this->mymodel->update("spp_".$jenjang, array($bulan => date("Y-m-d H:i:s")), "id", $value->id_spp);
                          }
                          //$update_transaksi = $this->mymodel->update("transaksi_spp", array("status_transaksi" => 2), "id_spp = '".$value->id_spp."' and va_number='".$va_number."' and bulan=", strtolower($data_bulan[1]));
                          $update_transaksi = $this->mymodel->update("transaksi_spp", array("status_transaksi" => 2), "id_transaksi", $data_id_transaksi[1]);
                          $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$value->id_siswa_aktif."' ","row");
                          if (!empty($get_siswa->nomor_peserta_ujian)) {
                            //cek periode yang boleh ujian
                              $update_ujian = $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => strtoupper($value->nama_siswa), "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
                          }
                        }
                        echo $data_id_transaksi[1]." YES<br/><br/>";
                    }
                    else if($status_bayar == "N"){ */
                        //get report transaksi di rekening
                        if ($report_start_date_override != '') {
                            $start_date = $report_start_date_override;
                            $end_date = $report_end_date_override;
                        } elseif(!empty($value->updated_at) && !empty($value->expired_datetime)){
                            $start_date= date("Ymd",strtotime("-1 days"));
                            $end_date= date("Ymd",strtotime($value->expired_datetime));
                        }else{
                            $start_date=date("Ymd");
                            $end_date=date("Ymd");
                        }
                        $get_report_ot = $this->get_report_bri($start_date, $end_date);
                        if (!empty($get_report_ot)) {
                            foreach ($get_report_ot as $key_report => $value_report) {
                              $cek_nama = null;
                              $nominal_biaya = (int)$value_report['amount'];
                              $cek_nama = strpos(strtoupper($value->nama_siswa), $value_report['nama'], 0);
                              // ponytail: nama report BRI kosong → strpos return 0 → false-match lunas
                                if ($value_report['brivaNo'] == $institution_code && $value_report['custCode'] == $customer_code && trim($value_report['nama']) != "" && $cek_nama !== false && $value_report['no_rek'] != "" && $value_report['tellerid'] != "" ) {
                                    // Skip payment already accepted by push. Report API has no journalSeq, so use conservative VA/amount/date guard.
                                    $payment_date_key = !empty($value_report['paymentDate']) ? date('Ymd', strtotime($value_report['paymentDate'])) : date('Ymd');
                                    $sudah_dipush = $this->mymodel->withquery("SELECT id FROM payment_response_bri WHERE briva_no = '" . $institution_code . $customer_code . "' AND bill_amount = " . intval($nominal_biaya) . " AND transaction_date LIKE '" . $payment_date_key . "%' LIMIT 1", "row");
                                    if (!empty($sudah_dipush)) {
                                        continue;
                                    }

                                    // Resolve one invoice group. Never choose between same-VA active invoices.
                                    $candidate_rows = $this->mymodel->withquery("select * from transaksi_spp where va_number = '" . $va_number . "' and status_transaksi = '1' and expired_datetime >= " . $invoice_cutoff_sql . " and kode_tagihan is not null and kode_tagihan != '' order by id_transaksi DESC", "result");
                                    $matched = $this->bri_spp_matcher->resolve($candidate_rows, $nominal_biaya);
                                    if (empty($matched) || $matched->id_transaksi != $value->id_transaksi) {
                                        continue;
                                    }

                                    $payment_timestamp = !empty($value_report['paymentDate']) ? strtotime($value_report['paymentDate']) : false;
                                    $updated_at = ($payment_timestamp !== false) ? date("Y-m-d H:i:s", $payment_timestamp) : date("Y-m-d H:i:s");
                                    $data_transaksi = array(
                                        "status_transaksi" => 2,
                                        "updated_at" => $updated_at,
                                        "updated_from" => "cron_cek_spp",
                                        "file_kwitansi" => $matched->no_transaksi . '-' . str_replace(' ', '_', $matched->user_name) . '.pdf'
                                    );

                                    // Atomic: detail spp_xx harus berhasil sebelum transaksi menjadi lunas.
                                    $this->db->trans_begin();
                                    if (!$this->_update_spp_detail($matched, $updated_at)) {
                                        $this->db->trans_rollback();
                                        continue;
                                    }

                                    $where_tagihan = "kode_tagihan = " . $this->db->escape($matched->kode_tagihan) . " and va_number = " . $this->db->escape($va_number) . " and status_transaksi = '1'";
                                    $update_transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, $where_tagihan);
                                    if ($update_transaksi < 1 || $this->db->trans_status() === false) {
                                        $this->db->trans_rollback();
                                        continue;
                                    }
                                    $this->db->trans_commit();

                                    $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_" . strtolower($jenjang) . "_aktif where id_siswa_" . strtolower($jenjang) . "_aktif = '" . $matched->id_siswa_aktif . "' ", "row");
                                    if (!empty($get_siswa->nomor_peserta_ujian)) {
                                        $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => strtoupper($matched->user_name), "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
                                    }
                                    $data = $value_report;
                                    echo "DITEMUKAN<br/>";
                                }
                            }
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/><br/>";
                        }
                        else{
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/>Get Rekening Transaksi Tidak berfungsi<br/>";
                        }
                    //}

                }
                else if ($value->status_transaksi == 2) {
                    // Backfill aman untuk transaksi lunas lama yang detail spp_xx belum terisi.
                    // Tidak memakai now(): gunakan waktu pembayaran transaksi.
                    if (!empty($value->updated_at)) {
                        if (empty($value->detail_bulan) && !empty($value->bulan)) {
                            $value->detail_bulan = $value->bulan;
                        }
                        $this->db->trans_begin();
                        if ($this->_update_spp_detail($value, $value->updated_at)) {
                            $this->db->trans_commit();
                        } else {
                            $this->db->trans_rollback();
                        }
                    }
                }
            }
        }
        else{
            echo "tidak ada data Transaksi SPP<br/>";
        }

        //get all transaksi Lain SD with status = 1 (menunggu pembayaran)
        $get_transaksi_ot = $this->mymodel->withquery("select id, kode_tagihan, id_siswa_aktif, tanggal_bayar, va_number, status_transaksi, nominal_bayar, updated_at, expired_at from transaksi_lain_sd where (va_number like '13803%' or va_number like '13802%') and status_transaksi = '1'", "result");

        if (!empty($get_transaksi_ot)) {
            foreach ($get_transaksi_ot as $key => $value) {
                $no_transaksi = $value->kode_tagihan;
                $bank = "BRI";//$value->nama_bank;
                $va_number = $value->va_number;
                $institution_code = substr($va_number, 0,5);
                $customer_code = substr($va_number, 5);
                $jenjang = explode("-", $no_transaksi);
                $jenjang = strtolower($jenjang[1]);
                $get_siswa = $this->mymodel->withquery("select nama_lengkap as nama_siswa from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$value->id_siswa_aktif."' ","row");

                if ($value->status_transaksi == 1 && $value->expired_at >= date("Y-m-d H:i:s")) {
                    $data = $this->check_inquiry($bank, $no_transaksi, $va_number);
                    $data_bayar = $data['data'];
                    $status_bayar = $data_bayar['statusBayar'];
                    $keterangan = $data_bayar['Keterangan'];
                    $nominal_biaya = (int)$data_bayar['Amount'];
                    /* if ($status_bayar == "Y") {
                        $cek_nama = null;
                        $cek_nama = strpos(strtoupper($value->nama_siswa), $data_bayar['Nama'], 0);
                        if ($nominal_biaya == (int)$value->total_biaya && $cek_nama !== false) {
                          //jika ditemukan pembayaran dengan amount, va, dan nama siswa yg sama maka update tagihan lain lunas
                          $update_transaksi_ot = $this->mymodel->update("transaksi_lain_sd", array("status_transaksi" => 2), "kode_tagihan", $value->kode_tagihan);
                        }
                    }
                    else if($status_bayar == "N"){ */
                        //get report transaksi di rekening
                        if ($report_start_date_override != '') {
                            $start_date = $report_start_date_override;
                            $end_date = $report_end_date_override;
                        } elseif(!empty($value->updated_at) && !empty($value->expired_datetime)){
                            $start_date= date("Ymd",strtotime("-1 days"));
                            $end_date= date("Ymd",strtotime($value->expired_datetime));
                        }else{
                            $start_date=date("Ymd");
                            $end_date=date("Ymd");
                        }
                        $get_report = $this->get_report_bri($start_date, $end_date);
                        if (!empty($get_report)) {
                            foreach ($get_report as $key_report => $value_report) {
                              $cek_nama = null;
                              $nominal_biaya = (int)$value_report['amount'];
                              $cek_nama = strpos(strtoupper($get_siswa->nama_siswa), $value_report['nama'], 0);
                                if ($value_report['brivaNo'] == $institution_code && $value_report['custCode'] == $customer_code && trim($value_report['nama']) != "" && $cek_nama !== false && $value_report['no_rek'] != "" && $value_report['tellerid'] != "" && ($nominal_biaya == (int)$value->nominal_bayar ) ) {
                                    //jika ditemukan pembayaran dengan amount, va, dan nama siswa yg sama maka update tagihan lain lunas
                                    $get_tgl_bayar = $this->mymodel->withquery("select transaction_date from payment_response_bri where briva_no = '".$va_number."' order by id DESC limit 1", "row");
                                    $tanggal_bayar = (!empty($get_tgl_bayar)) ? date("Y-m-d H:i:s", strtotime($get_tgl_bayar->transaction_date)) : date("Y-m-d H:i:s");
                                    $nama_file_kwitansi = $value->kode_tagihan.'-'.str_replace(" ", "_", $get_siswa->nama_siswa).'.pdf';
                                    $update_transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), array("status_transaksi" => 2, "tanggal_bayar" => $tanggal_bayar, "file_kwitansi" => $nama_file_kwitansi), "kode_tagihan", $value->kode_tagihan);
                                    $data = $value_report;
                                    //cetak kwitansi
                                    $cetak_kwitansi = $this->cetak_kwitansi_ot(array("jenjang" => $jenjang, "nama_lengkap" => $get_siswa->nama_siswa, "va_number" => $value->va_number, "kode_tagihan" => $value->kode_tagihan, "total_biaya" => $value->nominal_bayar, "total_biaya_terbilang" => terbilang($value->nominal_bayar) . " Rupiah", "id_siswa" => $value->id_siswa_aktif, "jenis_kwitansi" => "Pembayaran Lain"));
                                    echo "DITEMUKAN<br/>";
                                }
                            }
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/><br/>";
                        }
                        else{
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/>Get Rekening Transaksi Tidak berfungsi<br/>";
                        }
                    //}

                }
                else if ($value->status_transaksi == 1 && $value->expired_at < date("Y-m-d H:i:s")) {
                    //update riwayat tagihan lain siswa jadi belum aktif (expired)
                    //cek data update at tagihan lain
                    $update_expired = $this->mymodel->update("transaksi_lain_sd", array("status_transaksi" => 0, "expired_at" => null), "kode_tagihan", $value->kode_tagihan);
                }
            }
        }
        else{
            echo "tidak ada data Transaksi Lain SD<br/>";
        }

        //$this->response($msg,$status);
    }

    public function check_inquiry($bank, $no_transaksi, $va_number){
        //get va prefix
        $get_setting = $this->mymodel->getall("pengaturan_akun");
        if ($bank == "BNI" || $bank == "bni") {
            if ($jenjang == 'ft') {
                foreach ($get_setting as $key => $value) {
                if ($value->name_setting == "bni_client_id_ft") {
                    $client_id = $value->value;
                }

                if ($value->name_setting == "bni_prefix") {
                    $prefix = $value->value;
                }
                }
            }
            else {
                foreach ($get_setting as $key => $value) {
                if ($value->name_setting == "bni_client_id_spp") {
                    $client_id = $value->value;
                }
                if ($value->name_setting == "bni_prefix") {
                    $prefix = $value->value;
                    }
                }
            }

            $data = $this->check_billing_bni(ENVIRONMENT,$no_transaksi);
        }
        else if($bank == "BRI" || $bank == "bri"){
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
            $send_data = array(
                'brivaNo' => $brivaNo,
                'custCode' => substr($va_number, 5),
            );
            $data = $this->check_billing_bri($send_data);
        }
        return $data;
    }
    function check_billing_bni($production, $no_transaksi){
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

      $data_asli = array(
        'type' => "inquirybilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
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

    function check_billing_bri($datas)
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

    return BriApi::getData($clientID,$clientSecret,$url,$endpoint,$institutionCode,$data);
	}

    public function get_report_bri($start_date, $end_date){
        $this->load->library('BriApi');
          //get config bri key
          $get_setting = $this->mymodel->getall("pengaturan_akun");
          foreach ($get_setting as $key => $item) {
            if(ENVIRONMENT == "development" || ENVIRONMENT == "testing"){
              if($item->name_setting=='bri_dev_url'){
                $url = $item->value;
              }
              if ($item->name_setting == "bri_no_briva_dev") {
                $brivaNo = $item->value;
              }
            }else if(ENVIRONMENT == 'production'){
              if($item->name_setting=='bri_prod_url'){
                $url = $item->value;
              }
              if ($item->name_setting == "bri_no_briva_prod_close") {
                $brivaNo = $item->value;
              }
            }
            if($item->name_setting=='bri_client_id'){
              $clientID = $item->value;
            }
            if($item->name_setting=='bri_client_secret'){
              $clientSecret = $item->value;
            }
            if($item->name_setting=='bri_institution_code_close'){
              $institutionCode = $item->value;
            }
        }

          $endpoint     = $url."oauth/client_credential/accesstoken?grant_type=client_credentials";
          /*if($this->input->get('end_date')){
            $start_date=$this->input->get('start_date');
            $end_date=$this->input->get('end_date');
            
          }else{
            $start_date=date('Y').date('m').date('d');
            $end_date=date('Y').date('m').date('d');
          }*/

      $date=$this->echoDate(strtotime($start_date),strtotime($end_date));

        $data=[];
        foreach($date as $key => $d){
            $formated=str_replace('-','',$d);
            $datas=array(
              'brivaNo'=>$brivaNo,
              'startDate'=>$formated,
              'endDate'=>$formated
            );
        
            $arr=BriApi::getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$datas,$url);
            if($arr['responseCode']=='00'){
              foreach($arr['data'] as $item){
                $data[]=$item;
              }
            }
            /*print_r($arr);
            echo "<br/>";*/
        }
      return $data;
    }

    function echoDate( $start, $end ){

      $current = $start;
    
      $ret = array();
    
      while( $current<=$end ){
        
        
        $format=date('Y-m-d',$current);
        $ret[] = $format;
        $current = @date('Y-m-d', $current) . "+1 days";
        $current = @strtotime($current);
      }
    
      return $ret;
    }

    function cetak_kwitansi_ot($get = '')
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
        $endpoint = site_url('/apiapp/tagihan_lain/export_pdf_kwitansi');
        //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
        $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
          '&jenjang=' . urlencode($get['jenjang']) .
          '&nama_lengkap=' . urlencode($get['nama_lengkap']) .
          '&total_biaya=' . urlencode($get['total_biaya']) .
          '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
          '&va_number=' . urlencode($get['va_number']) .
          '&no_transaksi=' . urlencode($get['kode_tagihan']);
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

    private function _update_spp_detail($transaction, $updated_at)
    {
        $parts = explode('-', $transaction->no_transaksi);
        $jenjang = isset($parts[1]) ? $parts[1] : '';
        $table = $this->spp_payment_detail->table_for($jenjang);
        $months = $this->spp_payment_detail->months($transaction->detail_bulan);
        if ($table === false || $months === false || empty($transaction->id_siswa_aktif) || empty($transaction->id_tahun_ajaran)) {
            return false;
        }

        $where = array(
            'id' => $transaction->id_spp,
            'id_siswa_aktif' => $transaction->id_siswa_aktif,
            'id_tahun_ajaran' => $transaction->id_tahun_ajaran
        );

        foreach ($months as $month) {
            $row = $this->db->select($month)->where($where)->get($table)->row();
            if (empty($row)) {
                return false;
            }

            $current = $row->$month;
            if ($current !== null && $current !== '' && $current !== '-' && (string) $current !== (string) $updated_at) {
                return false;
            }

            if ((string) $current !== (string) $updated_at) {
                $this->db->where($where)->update($table, array($month => $updated_at));
            }

            $check = $this->db->select($month)->where($where)->get($table)->row();
            if (empty($check) || (string) $check->$month !== (string) $updated_at) {
                return false;
            }
        }
        return true;
    }

}
