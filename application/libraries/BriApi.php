<?php

class BriApi
{
	//	date_default_timezone_set("Asia/Makassar");
	
	// [JACOS] TODO(manual): isi clientID/clientSecret sandbox BRI milik Jacos.
	// Kredensial lama (LabSchool) sudah dihapus. Nilai runtime sebenarnya diambil
	// dari tabel `setting` (bri_client_id / bri_client_secret) di tiap controller.
	var $clientID     = '';
	var $clientSecret = '';
	var $endpoint     = "https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials";
	var $institutionCode = "J104408"; //This institution code will be given by BRI
	var $brivaNo = "77777"; // BRIVA number unique to your institution
	var $expiredDate = "2022-11-27 23:59:00"; //static
	
	
	// private $ci;
	
	// function __construct()
	// {
	// 	$this->ci =& get_instance();
	// }
	

	/* Generate Token */
	private static function BRIVAgenerateToken($client_id, $secret_id,$endpoint) {
		$url = $endpoint;
		$data = "client_id=".$client_id."&client_secret=".$secret_id;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$json = json_decode($result, true);
		$accesstoken = $json['access_token'];

		return $accesstoken;
	}

	/* Generate signature */
	private static function BRIVAgenerateSignature($path, $verb, $token, $timestamp, $payload, $secret) {
		$payloads = "path=$path&verb=$verb&token=Bearer $token&timestamp=$timestamp&body=$payload";
		$signPayload = hash_hmac('sha256', $payloads, $secret, true);
		return base64_encode($signPayload);
	}


	/* CURL Custom*/
	private static function curlHeader($urlPost,$payload,$datas,$path,$verb,$clientID,$clientSecret,$endpoint)
	{
		$client_id = $clientID;
		$secret_id = $clientSecret;

		$timestamp = gmdate("Y-m-d\TH:i:s.000\Z");//gmdate("Y-m-d\TH:i:s.000\Z", strtotime("-3 minutes"));
		$secret = $secret_id;

		$token = self::BRIVAgenerateToken($client_id, $secret_id,$endpoint);
		$base64sign = self::BRIVAgenerateSignature($path, $verb, $token, $timestamp, $payload, $secret);
		// dd($token);

		if($verb == "GET" || $verb == "DELETE") {
			$request_headers = array(
				"Authorization:Bearer " . $token,
				"BRI-Timestamp:" . $timestamp,
				"BRI-Signature:" . $base64sign,
			);
		}else{
			$request_headers = array(
				"Content-Type:"."application/json",
				"Authorization:Bearer " . $token,
				"BRI-Timestamp:" . $timestamp,
				"BRI-Signature:" . $base64sign,
			);
		}
		//print_r($request_headers);

		$chPost = curl_init();
		curl_setopt($chPost, CURLOPT_URL, $urlPost);
		curl_setopt($chPost, CURLOPT_HTTPHEADER, $request_headers);
		curl_setopt($chPost, CURLOPT_CUSTOMREQUEST, $verb); 
		curl_setopt($chPost, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($chPost, CURLINFO_HEADER_OUT, true);
		curl_setopt($chPost, CURLOPT_RETURNTRANSFER, true);

		$resultPost = curl_exec($chPost);
		$httpCodePost = curl_getinfo($chPost, CURLINFO_HTTP_CODE);
		curl_close($chPost);

		return $resultPost;
	}


	/* Create BRIVA  */
	public static function create($clientID,$clientSecret,$endpoint,$institutionCode,$data,$url) {

		// dd($institutionCode);
		$institutionCode = $institutionCode;
		$brivaNo = $data['brivaNo'];
		$custCode = $data['custCode'];
		$nama = $data['nama'];
		$amount=$data['amount'];
		$keterangan=$data['keterangan'];
		$expiredDate = $data['expiredDate'];

		$datas = array(
			'institutionCode' => $institutionCode ,
			'brivaNo' => $brivaNo,
			'custCode' => $custCode,
			'nama' => $nama,
			'amount' => $amount,
			'keterangan' => $keterangan,
			'expiredDate' => $expiredDate
		);

		$path = "/v1/briva";
		$verb = "POST";
		$payload = json_encode($datas, true);

		$urlPost = $url."v1/briva";
		$resultPost = self::curlHeader($urlPost,$payload,$datas,$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo $resultPost;
		return json_decode($resultPost, true);
	}


	/* get Data by CustCode BRIVA  */
	public static function getData($clientID,$clientSecret,$url,$endpoint,$institutionCode,$data) {

		$brivaNo = $data['brivaNo'];
		$custCode = $data['custCode'];

		$payload = NULL;
		$path = "/v1/briva/".$institutionCode."/".$brivaNo."/".$custCode;
		$verb = "GET";
	
		$urlPost =$url."v1/briva/".$institutionCode."/".$brivaNo."/".$custCode;
		$resultPost = self::curlHeader($urlPost,$payload,'',$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo "<br/> <br/>";
		// // dd();
		// echo $resultPost;
		return json_decode($resultPost, true);
	}


	/* get Status Payment  */
	public static function getStatus($clientID,$clientSecret,$endpoint,$institutionCode,$brivaNo,$custCode,$url) {

		$payload = NULL;
		$path = "/v1/briva/status/".$institutionCode."/".$brivaNo."/".$custCode;
		$verb = "GET";
	
		$urlPost =$url."v1/briva/status/".$institutionCode."/".$brivaNo."/".$custCode;
		
		$resultPost = self::curlHeader($urlPost,$payload,'',$path,$verb,$clientID,$clientSecret,$endpoint);

		return json_decode($resultPost, true);
	}


	/* Update Payment Status  */
	public static function updateBayar($clientID,$clientSecret,$endpoint,$institutionCode,$brivaNo,$custCode,$statusBayar,$url) {

		$institutionCode = $institutionCode;
		$brivaNo = $brivaNo;
		$custCode = $custCode;
		$statusBayar = $statusBayar;

		$datas = array(
			'institutionCode' => $institutionCode ,
			'brivaNo' => $brivaNo,
			'custCode' => $custCode,
			'statusBayar' => $statusBayar
		);

		$payload = json_encode($datas, true);
    	$path = "/v1/briva/status";
		$verb = "PUT";

		$urlPost = $url."v1/briva/status";
		 $resultPost = self::curlHeader($urlPost,$payload,$datas,$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo $resultPost;
		return json_decode($resultPost, true);
	}


	/* Update BRIVA Data */
	public static function update($clientID,$clientSecret,$endpoint,$institutionCode,$data,$url) {

		$institutionCode = $institutionCode;
		$brivaNo = $data['brivaNo'];
		$custCode = $data['custCode'];
		$nama = $data['nama'];
		$amount=$data['amount'];
		$keterangan=$data['keterangan'];
		$expiredDate = $data['expiredDate'];
		

		$datas = array(
			'institutionCode' => $institutionCode ,
			'brivaNo' => $brivaNo,
			'custCode' => $custCode,
			'nama' => $nama,
			'amount' => $amount,
			'keterangan' => $keterangan,
			'expiredDate' => $expiredDate
		);

		$payload = json_encode($datas, true);
    	$path = "/v1/briva";
		$verb = "PUT";

		$urlPost = $url."v1/briva";
		$resultPost = self::curlHeader($urlPost,$payload,$datas,$path,$verb,$clientID,$clientSecret,$endpoint);

		return json_decode($resultPost, true);
	}


	/* Delete BRIVA  */
	public static function delete($clientID,$clientSecret,$endpoint,$institutionCode,$data,$url) {

		$brivaNo = $data['brivaNo'];
		$custCode = $data['custCode'];
		
		$datas = array(
			'institutionCode' => $institutionCode ,
			'brivaNo' => $brivaNo,
			'custCode' => $custCode,
		);
		
		$payload = "institutionCode=".$institutionCode."&brivaNo=".$brivaNo."&custCode=".$custCode;
		//$payload = json_encode($datas, true);
		$path = "/v1/briva";
		$verb = "DELETE";
		
		$urlPost =$url."v1/briva";
		
		$resultPost = self::curlHeader($urlPost,$payload,$datas,$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo $resultPost;
		return json_decode($resultPost, true);
	}


	/* Payment Report by Date */
	public static function getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$data,$url) {

		$brivaNo = $data['brivaNo'];
		
		$startDate = $data['startDate']; // YYYYMMDD
		$endDate   = $data['endDate']; // YYYYMMDD
		// startDate and endDate should be same value
		 

		$payload = NULL;
		$path = "/v1/briva/report/".$institutionCode."/".$brivaNo."/".$startDate."/".$endDate;
		$verb = "GET";
	
		$urlPost =$url."v1/briva/report/".$institutionCode."/".$brivaNo."/".$startDate."/".$endDate;

		$resultPost = self::curlHeader($urlPost,$payload,'',$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo "<br/> <br/>";

		// echo $resultPost;
		return json_decode($resultPost, true);
	}


	/* Payment Report by DateTime */
	public static function getReportDateTime($clientID,$clientSecret,$endpoint,$institutionCode,$data) {

		$institutionCode = $institutionCode;
		$brivaNo = $data['brivaNo'];
		
		$startDate = $data['startDate']; // YYYYMMDD
		$endDate   = $data['endDate']; // YYYYMMDD
		// startDate and endDate should be same value

		$startTime = "00:30";
    	$endTime = "12:30";

		$payload = NULL;
		$path = "/v1/briva/report_time/".$institutionCode."/".$brivaNo."/".$startDate."/".$startTime."/".$endDate."/".$endTime;
		$verb = "GET";
	
		$urlPost ="https://partner.api.bri.co.id/v1/briva/report_time/".$institutionCode."/".$brivaNo."/".$startDate."/".$startTime."/".$endDate."/".$endTime;

		$resultPost = self::curlHeader($urlPost,$payload,'',$path,$verb,$clientID,$clientSecret,$endpoint);
		// echo "<br/> <br/>";

		// echo $resultPost;
		return json_decode($resultPost, true);
	}





}