<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Generate_tagihan extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_post()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    $jenjang = $this->post('jenjang');
    $id_siswa = $this->post('id_siswa_aktif');
    //$id_trans = explode(',',$this->post('id_transaksi'));
    $id_tagihan = $this->post('id_tagihan');
    $total_biaya = $this->post('total_biaya');
    $thn_ajar = "";
    $va_number = "";
    $name = "";
    $email = "";
    $nis="";
    $brivaNo = "";
    //foreach ($id_trans as $key => $item) {
      $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, s.id_tahun_ajaran, s.id_kelas from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.id = ". $id_tagihan , "row");
    //}

    if($jenjang=='sd'){
      $code='85';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_sd as id_siswa, sa.id_siswa_sd_aktif as id_siswa_aktif from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran left join kelas_sd k on k.id_kelas_sd = sa.id_kelas where id_siswa_sd_aktif = ".$id_siswa."","row");
    }else if($jenjang=='smp'){
      $code='65';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_smp as id_siswa, sa.id_siswa_smp_aktif as id_siswa_aktif from siswa_smp_aktif sa join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran left join kelas_smp k on k.id_kelas_smp = sa.id_kelas where id_siswa_smp_aktif = ".$id_siswa."","row");
    }else if($jenjang=='sma'){
      $code='75';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_sma as id_siswa, sa.id_siswa_sma_aktif as id_siswa_aktif from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran left join kelas_sma k on k.id_kelas_sma = sa.id_kelas where id_siswa_sma_aktif = ".$id_siswa."","row");
    }else if($jenjang=='ft'){
      $code='95';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_ft as id_siswa, sa.id_siswa_ft_aktif as id_siswa_aktif from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran left join kelas_ft k on k.id_kelas_ft = sa.id_kelas where id_siswa_ft_aktif = ".$id_siswa."","row");
    }
    
    //get va prefix
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    if ($get_tagihan->tipe_bank == "BNI") {
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
    }
    else if($get_tagihan->tipe_bank == "BRI"){
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
    }

    //cek va sudah tersedia / belum
    $cek_tagihan = $this->mymodel->withquery("select t.va_number from transaksi_lain_".strtolower($jenjang)." t join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.id_siswa_aktif = '".$get_siswa->id_siswa_aktif."' and t.va_number != '' and tm.tipe_bank = '".$get_tagihan->tipe_bank."' order by t.id ASC" , "row");
    if (!empty($cek_tagihan) && $va_number == "") {
      $va_number = $cek_tagihan->va_number;
    }
    else{
      //cek VA dari tabel spp
      $cek_tagihan_spp = $this->mymodel->withquery("select va_number from transaksi_spp where id_siswa_aktif = '".$get_siswa->id_siswa_aktif."' and no_transaksi like '%".$jenjang."%' and id_tahun_ajaran = '".$get_siswa->id_tahun_ajaran."' and va_number != ''" , "row");
      if (!empty($cek_tagihan_spp) && $va_number == "") {
        $va_number = $cek_tagihan_spp->va_number;
      }
    }
    $nis = $get_siswa->nis;
    $nis = substr($nis, 3);

    if ($va_number == "") {
      if ($get_tagihan->tipe_bank == "BNI") {
        $thn_ajar = substr($thn_ajar, 0,2);
        $va_number = $prefix.$client_id.$code.$nis;
        $tagihan_code="OT".date("imy")."-".strtoupper($jenjang)."-".$va_number;
        $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $tagihan_code,strtoupper($jenjang), array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
        //print_r($payment_response);
        //print_r($get_siswa); echo "test1";
      }
      else if($get_tagihan->tipe_bank == "BRI"){
        //print_r($get_siswa); echo "test2";
        $thn_ajar = substr($thn_ajar, 0,2);
        $va_number = $brivaNo.$code.$nis;
        $tagihan_code="OT".date("imy")."-".strtoupper($jenjang)."-".$va_number;
        $datas = array(
          'brivaNo' => $brivaNo,
          'custCode' => substr($va_number, 5),
          'nama' => $get_siswa->nama_lengkap,
          'amount' => $total_biaya,
          'keterangan' => $tagihan_code,
          'expiredDate' => date("Y-m-d H:i:s", strtotime("+24 hours")),
        );
        $payment_response = $this->create_va_bri($datas);
        if ($payment_response['responseCode'] != '00' && $get_tagihan->tipe_bank == "BRI") {
          /* $payment_response = $this->update_va_bri($datas);
          $update_payment = $this->update_va_bri_status($datas); */
          $delete_response = $this->delete_va_bri(array("brivaNo" => $datas["brivaNo"], "custCode" => $datas["custCode"]));
          $payment_response = $this->create_va_bri($datas);
        }
      }
    }
    else{
      if ($get_tagihan->tipe_bank == "BNI") {
        $tagihan_code="OT".date("imy")."-".strtoupper($jenjang)."-".$va_number;
        $datas = array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number);
        $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $tagihan_code,strtoupper($jenjang), $datas);
        $datas["tagihan_code"] = $tagihan_code;
        $datas["total_biaya"] = $total_biaya;
      }
      else if($get_tagihan->tipe_bank == "BRI" && $va_number != ""){
        $tagihan_code="OT".date("imy")."-".strtoupper($jenjang)."-".$va_number;
        $datas = array(
          'brivaNo' => $brivaNo,
          'custCode' => substr($va_number, 5),
          'nama' => $get_siswa->nama_lengkap,
          'amount' => $total_biaya,
          'keterangan' => $tagihan_code,
          'expiredDate' => date("Y-m-d H:i:s", strtotime("+24 hours")),
        );
        //$payment_response = $this->create_va_bri($datas);
        $payment_response = $this->update_va_bri($datas);
        $update_payment = $this->update_va_bri_status($datas);
      }
    }
    //print_r($payment_response);
    //echo "<br/>";
    //print_r($total_biaya."<br/>".$tagihan_code."<br/>".$jenjang."<br/>nama". $get_siswa->nama_lengkap."<br/>". "email"." ". $get_siswa->email."<br/>". "va_number"." ". $va_number);
    // $data['payment_respon']=$payment_response;
   
    if(!$payment_response['virtual_account'] && $get_tagihan->tipe_bank == "BNI"){
      $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>$payment_response, 'req_parameters' => $datas);
      $status="200";
      $this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));
      $this->response($msg,$status);
    }
    else if ($payment_response['responseCode'] != '00' && $get_tagihan->tipe_bank == "BRI") {
      $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>$payment_response, 'req_parameters' => $datas);
      $status="200";
      $this->response($msg,$status);
    }

    if ($get_tagihan->tipe_bank == "BNI") {
      $logo = base_url().'/uploads/logo_bni.png';
    }
    else if($get_tagihan->tipe_bank == "BRI"){
      $logo = base_url().'/uploads/logo_bri.png';
    }

        $data = array(
          "status_transaksi" => '1',
          "va_number" => $va_number,
          "kode_tagihan" => $tagihan_code,
          "expired_at" => date("Y-m-d H:i:s", strtotime("+24 hours")),
          "updated_at" => date("Y-m-d H:i:s")
        );
      //ubah semua tagihan yg ber status 1 (menunggu pembayaran) / 3 (kadaluarsa) ke 0 (Belum dibayar)
        $this->mymodel->update("transaksi_lain_".strtolower($jenjang), array("status_transaksi" => 0), "va_number = '".$va_number."' and status_transaksi=", 1);
      //Ubah status menjadi 1 (menunggu pembayaran)
        $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data, "id", $id_tagihan);
    //}
      $data['logo_bank'] = $logo;
      $data['expired_datetime'] = date("Y-m-d H:i:s", strtotime("+24 hours"));
    $msg = array('status' => 1, 'message' => 'Tagihan Berhasil Dibuat', 'data' => $data, 'req_parameters' => $datas);
    $status = "200";

    $this->response($msg, $status);
  }

  function create_billing($production, $total, $no_transaksi,$jenjang, $data_user){
    $this->load->library('BniEnc');
    // FROM BNI
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    foreach ($get_setting as $key => $value) {
      if($jenjang=="ft"){
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
      }else{
        if ($value->name_setting == "bni_client_id_spp") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_spp") {
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
    
    $date_va=(24) * 3600;

    $data_asli = array(
      'type' => "createbilling",
      'client_id' => $client_id,
      'trx_id' => $no_transaksi,
      'trx_amount' => $total,
      'billing_type' => 'c',
      'datetime_expired' => date("Y-m-d H:i:s", strtotime("+24 hours")), // billing will be expired in 6 hours
      'virtual_account' => $data_user['va_number'],
      'customer_name' => $data_user['nama'],
      'customer_email' => $data_user['email'],
      //'customer_phone' => $data_user['notelp'],
    );
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

  function update_billing($production, $total, $no_transaksi,$jenjang, $data_user){
    $this->load->library('BniEnc');
    // FROM BNI
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    foreach ($get_setting as $key => $value) {
      if($jenjang=="ft"){
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
      }else{
        if ($value->name_setting == "bni_client_id_spp") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_spp") {
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
          $date_va=($item->value*24) * 3600;
        }else{
          $date_va=($item->value) * 3600;
        }
      }
    }

    $data_asli = array(
      'type' => "updateBilling",
      'client_id' => $client_id,
      'trx_id' => $no_transaksi,
      'trx_amount' => $total,
      'datetime_expired' => date("Y-m-d H:i:s", strtotime("+24 hours")), // billing will be expired in 6 hours
      'customer_name' => $data_user['nama'],
      'customer_email' => $data_user['email'],
      'description' => "Payment of ".$no_transaksi
      //'billing_type' => 'c',
      //'virtual_account' => $data_user['va_number'],
      //'customer_phone' => $data_user['notelp'],
    );
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
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return $rs;
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

    $endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

    $data = array(
      'brivaNo' => $datas['brivaNo'],
      'expiredDate' => $datas['expiredDate'],
      'custCode' => $datas['custCode'],
      'nama' => $datas['nama'],
      'amount' => $datas['amount'],
      'keterangan' => $datas['keterangan']
    );
    return BriApi::update($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
  }

  function update_va_bri_status($datas)
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
    
    return BriApi::updateBayar($clientID, $clientSecret, $endpoint, $institutionCode, $datas['brivaNo'], $datas['custCode'], 'N', $url);
  }
  
}
