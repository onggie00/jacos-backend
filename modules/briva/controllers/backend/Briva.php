<?php

defined('BASEPATH') or exit('No direct script access allowed');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Briva extends Admin
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library('BriApi');
	}

	public function send_notif()
    {
        $token = 'dBYXcGgUQcK9GemcDj2_v3:APA91bHUDbkmhnoFht0BYwNNzmdT8CdejkAE8YLeIekz5JcKuFzqi0s_w1JiaoMB3hZxKaTvNGaLSsEScseU1I6lXtqXgcYIp6DWj1H2QTezccjBRkc8LwyKCcDsD8KB2EGLlJpVq8kX'; // push token
        $message = "Test notification message";

        $this->load->library('fcm');
        $this->fcm->setTitle('Test FCM Notification');
        $this->fcm->setMessage($message);

        /**
         * set to true if the notificaton is used to invoke a function
         * in the background
         */
        $this->fcm->setIsBackground(false);

        /**
         * payload is userd to send additional data in the notification
         * This is purticularly useful for invoking functions in background
         * -----------------------------------------------------------------
         * set payload as null if no custom data is passing in the notification
         */
        $payload = array('notification' => '');
        $this->fcm->setPayload($payload);

        /**
         * Send images in the notification
         */
        $this->fcm->setImage('https://firebase.google.com/_static/9f55fd91be/images/firebase/lockup.png');

        /**
         * Get the compiled notification data as an array
         */
        $json = $this->fcm->getPush();

        $p = $this->fcm->send($token, $json);

        print_r($p);
    }

    /**
     * Send to multiple devices
     */
    public function sendToMultiple()
    {
        $token = array('Registratin_id1', 'Registratin_id2'); // array of push tokens
        $message = "Test notification message";

        $this->load->library('fcm');
        $this->fcm->setTitle('Test FCM Notification');
        $this->fcm->setMessage($message);
        $this->fcm->setIsBackground(false);
        // set payload as null
        $payload = array('notification' => '');
        $this->fcm->setPayload($payload);
        $this->fcm->setImage('https://firebase.google.com/_static/9f55fd91be/images/firebase/lockup.png');
        $json = $this->fcm->getPush();

        /** 
         * Send to multiple
         * 
         * @param array  $token     array of firebase registration ids (push tokens)
         * @param array  $json      return data from getPush() method
         */
        $result = $this->fcm->sendMultiple($token, $json);
    }

	public function index_1()
	{
		$this->load->library('BriApi');

		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "J104408"; //This institution code will be given by BRI
		$url = "https://sandbox.partner.api.bri.co.id/" ;

		$data = array(
			'brivaNo' => "88888",
			'expiredDate' => "2022-11-27 23:59:00",
			'custCode' => "1731102080",
			'nama' => 'Roby',
			'amount' => '',
			'keterangan' => 'Pembayaran Daftar Ulang'
		);
		dd(BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url));
	}

	public function create()
	{
		$this->load->library('BriApi');

		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "J104408"; //This institution code will be given by BRI
		$url = "https://sandbox.partner.api.bri.co.id/" ;

		$data = array(
			'brivaNo' => $this->input->get('brivaNo'),
			'expiredDate' => date("Y-m-d H:i:s", strtotime("+5 minute")),
			'custCode' => $this->input->get('custCode'),
			'nama' => 'Roby',
			'amount' => "0",
			'keterangan' => 'Pembayaran Daftar Ulang'
		);
		dd(BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url));
	}

	public function create_prod()
	{
		$this->load->library('BriApi');

		//open
		// $institutionCode = "DOSFLMV26IO";

		//close
		$institutionCode = "GYSFLMV267B";

		$clientID     = "xryikXqKHMbISBma7ayN0GJ8lcFkFNnN";
		$clientSecret = "FE4IKtgZsgBNUAJ0";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		 //This institution code will be given by BRI
		$url = "https://partner.api.bri.co.id/" ;

		$data = array(
			'brivaNo' => '13803',
			'expiredDate' => date("Y-m-d H:i:s", strtotime("+24 hours")),
			'custCode' => '66990099',
			'nama' => 'Test Bri',
			'amount' => "0",
			'keterangan' => 'Pembayaran Daftar Ulang'
		);
		dd(BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url));
	}

	
	public function get_prod()
	{
		$clientID     = "BiXr1GIOIEonmfqZGUg2IQVA8x0YmcZc";
		$clientSecret = "GC43CFNCIMQDAOhb";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "DOSFLMV26IO"; //This institution code will be given by BRI
		$url = "https://partner.api.bri.co.id/";

		$data = array(
			'brivaNo' => '13802',
			'custCode' => '23010122',
		);

		dd(BriApi::getData($clientID, $clientSecret, $url, $endpoint, $institutionCode, $data));
	}
	
	public function get()
	{
		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "J104308"; //This institution code will be given by BRI
		$url = "https://sandbox.partner.api.bri.co.id/";

		$data = array(
			'brivaNo' => $this->input->get('brivaNo'),
			'custCode' => $this->input->get('custCode')
		);

		dd(BriApi::getData($clientID, $clientSecret, $url, $endpoint, $institutionCode, $data));
	}

	public function status()
	{
		$this->load->library('BriApi');

		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$brivaNo = "88888"; // BRIVA number unique to your institution
		$custCode = "23010669";
		$institutionCode = "J104408"; //This institution code will be given by BRI
		$url='https://sandbox.partner.api.bri.co.id/';
		dd(BriApi::getStatus($clientID, $clientSecret, $endpoint, $institutionCode, $brivaNo, $custCode,$url));
	}

	public function status_prod()
	{
		$this->load->library('BriApi');

		$clientID     = "BiXr1GIOIEonmfqZGUg2IQVA8x0YmcZc";
		$clientSecret = "GC43CFNCIMQDAOhb";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$brivaNo = "13802"; // BRIVA number unique to your institution
		$custCode = "23010077";
		$institutionCode = "DOSFLMV26IO"; //This institution code will be given by BRI
		$url='https://partner.api.bri.co.id/';

		dd(BriApi::getStatus($clientID, $clientSecret, $endpoint, $institutionCode, $brivaNo, $custCode,$url));
	}

	public function updateStatus()
	{
		$this->load->library('BriApi');

		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$brivaNo = "88888"; // BRIVA number unique to your institution
		$custCode = "23010669";
		$institutionCode = "J104408"; //This institution code will be given by BRI

		// dd(BriApi::updateBayar($clientID, $clientSecret, $endpoint, $institutionCode, $brivaNo, $custCode));
	}

	public function update_prod()
	{
		$this->load->library('BriApi');

		$clientID     = "BiXr1GIOIEonmfqZGUg2IQVA8x0YmcZc";
		$clientSecret = "GC43CFNCIMQDAOhb";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "DOSFLMV26IO"; //This institution code will be given by BRI
		$url = "https://partner.api.bri.co.id/" ;

		$data = array(
			'brivaNo' => '13802',
			'expiredDate' => date("Y-m-d H:i:s", strtotime("+3 days")),
			// 'expiredDate' => date("Y-m-d H:i:s", strtotime("+1 minutes")),
			'custCode' => '23010122',
			'nama' => 'KHADIJA NUR ATHIYA WIDAYANA',
			'amount' => "0",
			'keterangan' => 'PEMBAYARAN DAFTAR ULANG SD'
		);
		dd(BriApi::update($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url));
	}


	public function delete()
	{
		$this->load->library('BriApi');

		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "J104408"; //This institution code will be given by BRI

		$data = array(
			'brivaNo' => $this->input->get('brivaNo'),
			'custCode' => $this->input->get('custCode')
		);
		dd(BriApi::delete($clientID, $clientSecret, $endpoint, $institutionCode, $data));
	}


	public function report()
	{
		$this->load->library('BriApi');
		$clientID     = "CIALK4p0m5vilRc5G21dV5h3rpuS4Saa";
		$clientSecret = "QsZIMam1Zqgmm22n";
		$endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "J104408"; //This institution code will be given by BRI
		$url = "https://sandbox.partner.api.bri.co.id/";
		
		$data = array(
			'brivaNo' => $this->input->get('brivaNo'),
			'startDate' => $this->input->get('startDate'),
			'endDate' => $this->input->get('endDate')
		);
		dd(BriApi::getReportDate($clientID, $clientSecret, $endpoint, $institutionCode, $data,$url));
	}

	public function report_prod()
	{
		$this->load->library('BriApi');
		$clientID     = "xryikXqKHMbISBma7ayN0GJ8lcFkFNnN";
		$clientSecret = "FE4IKtgZsgBNUAJ0";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "DOSFLMV26IO"; //This institution code will be given by BRI
		$url = "https://partner.api.bri.co.id/";
		
		$data = array(
			'brivaNo' => '13802',
			'startDate' => '20221116',
			'endDate' => '20221116'
		);
		dd(BriApi::getReportDate($clientID, $clientSecret, $endpoint, $institutionCode, $data,$url));
	}

	public function reportTime_prod()
	{
		$this->load->library('BriApi');
		$clientID     = "xryikXqKHMbISBma7ayN0GJ8lcFkFNnN";
		$clientSecret = "FE4IKtgZsgBNUAJ0";
		$endpoint     = "https://partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
		$institutionCode = "DOSFLMV26IO"; //This institution code will be given by BRI

		$data = array(
			'brivaNo' => '13802',
			'startDate' => '2022-11-15',
			'endDate' => '2022-11-15'
		);
		dd(BriApi::getReportDateTime($clientID, $clientSecret, $endpoint, $institutionCode, $data));
	}

	public function inquiry_ft()
	{
		// var_dump($this->input->get('trx'));
		var_dump($this->get_bni_ft($this->input->get('trx')));
	}

	public function generate_va_bni(){
		$id_siswa= $this->input->get('id_siswa');
		$get_siswa = $this->mymodel->getbywhere("siswa_ft","id_siswa_ft",$id_siswa,"row");

		//insert transaksi
		$no_transaksi = "LI-FT-".date("Ymd")."-".$id_siswa;
		//get biaya pendaftaran
		$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran","jenjang","FRANCE TRACK","row");
		$total_biaya = $get_biaya->nominal_pendaftaran; //+ $get_biaya->nominal_daftar_ulang;
		$data_transaksi = array(
		  "no_transaksi" => $no_transaksi,
		  "nama_bank" => 'BNI',
		  "user_email" => $get_siswa->email,
		  "user_name" => $get_siswa->nama_lengkap,
		  "user_phone" => "",
		  "description" => "Tagihan Pendaftaran FT a.n ".strtoupper($get_siswa->nama_lengkap),
		  "id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
		  "total_biaya" => $total_biaya,
		  "status_transaksi" => "0",
		  "created_at" => date("Y-m-d H:i:s"),
		  "expired_datetime" => date("Y-m-d H:i:s", strtotime("+3 days"))
		);
		if (!empty($data_transaksi)) {
		  //create billing
		  $get_setting = $this->mymodel->getall("pengaturan_akun");
		  foreach ($get_setting as $key => $value) {
			if ($value->name_setting == "bni_client_id_ft") {
			  $client_id = $value->value;
			}
			
			if ($value->name_setting == "bni_prefix") {
			  $prefix = $value->value;
			}
		  }
		  $get_no_urut = $this->mymodel->withquery("select va_number, id_siswa_ft as id_siswa from siswa_ft where va_number like '".$prefix.$client_id.date('y', strtotime('+1 years'))."09%' and va_number != '' order by id_siswa_ft DESC","row");
		  if (empty($get_no_urut)) {
			$no_urut = "0001";
		  }
		  else{
			$no_urut = (int)substr($get_no_urut->va_number, -4);
			$no_urut = $no_urut+1;
			$no_urut = sprintf("%04d", $no_urut);
		  }
		  $va_number = $prefix.$client_id.date("y", strtotime('+1 years'))."09".$no_urut;
		  // $va_number='9881611399010025';
		  $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number ));
		  // dd($payment_response);
		  if(!$payment_response['virtual_account']){
			$msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>array());
			$status="200";
			$this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));
			$this->response($msg,$status);
		  }

		  $data_transaksi['va_number'] = $payment_response['virtual_account'];

		  $id_transaksi = $this->mymodel->insertid("transaksi",$data_transaksi);
		  $this->mymodel->update("siswa_ft", array("no_transaksi" => $no_transaksi, "va_number" => $va_number), "id_siswa_ft", $id_siswa);

		  $this->cetak_slip(array("id_siswa" => $id_siswa, "tipe_siswa" => "ft"));
		  $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '".$id_transaksi."'","row");
		  //kirim email slip pembayaran
		  $data_email = array(
			"email" => $get_siswa->email,
			"nama_lengkap" => $get_siswa->nama_lengkap,
			"tipe_pendaftaran" => "PSB FT",
			"jenjang" => "FT",
			"transaksi" => $get_transaksi,
			"nama_panitia" => "Panitia PSB FT Labschool Cibubur ".date("Y", strtotime("+1 years"))."-".date("Y", strtotime("+2 years")),
			"slip_pembayaran" => $no_transaksi.'-'.$get_siswa->nama_lengkap.'.pdf',
		  );
		//   $this->send_email_file("",$data_email['email'],$data_email);

		}

	}

	public function create_slip(){
		$id_siswa=$this->input->get('id_siswa');
		$id_transaksi=$this->input->get('id_transaksi');

		$tipe=$this->input->get('tipe');
		$cetak_slip = $this->cetak_slip(array("id_siswa" => $id_siswa, "tipe_siswa" => "sd"));

		$get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '" . $id_transaksi . "'", "row");
		$get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $id_siswa, "row");

		$slip_pembayaran=$get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf';
		$this->mymodel->update("status_daftar_ulang_sd", array("slip_pembayaran" => $slip_pembayaran), "id_siswa_sd", $id_siswa);
		echo $slip_pembayaran;
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
  
	  function create_billing($production, $total, $no_transaksi, $data_user){
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
		  'type' => "createbilling",
		  'client_id' => $client_id,
		  'trx_id' => $no_transaksi,
		  'trx_amount' => $total,
		  'billing_type' => 'c',
		  'datetime_expired' => date('c', time() + $date_va), // billing will be expired in 6 hours
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
  
	  function get_bni_ft($no_transaksi){
		$this->load->library('BniEnc');
  
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
		  if (ENVIRONMENT == "production") {
			if ($value->name_setting == "bni_api_prod_url") {
			  $url = $value->value;
			}
		  }
		  else if (ENVIRONMENT == "development" || ENVIRONMENT == "testing"){
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
		  return ($response_json);
		} else {
		  $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
		  return ($data_response);
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
