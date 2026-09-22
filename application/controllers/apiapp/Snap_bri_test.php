<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
define( 'PRIVATE_BRI_KEY', FCPATH . 'key_bri_sd_private.pem');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Snap_bri_test extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $status = "";
      $token = "";
      $headers=array();
      $res=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        // $token = $this->get_token();
        $clientId = "bmf114bUB3BjHsJfRFqXtiafpcG6MMH5";
        $privateKey = file_get_contents(PRIVATE_BRI_KEY);
        $generate_signature = $this->generateSignature($clientId, $privateKey);
        $timestamp = $generate_signature['timestamp'];
        $signature = $generate_signature['signature'];
        $token = $this->get_token($generate_signature);
        $data['timestamp'] = $timestamp;
        $data['signature'] = $signature;
        $customerNo = "".rand(100000000,999999999)."";
        $total_amount = (string) rand(10000, 10000);

        $data_req = array(
            "partnerServiceId" => "   22216", //22216 dummy id
            "customerNo" => $customerNo,
            "virtualAccountNo" => "   22216".$customerNo,
            "virtualAccountName" => "Onggie Test ".$customerNo,
            "trxId" => "TSLC ".$customerNo,
            "totalAmount" => $total_amount,
            "expiredDate" => date('Y-m-d\TH:i:s', strtotime('+1 day'))."+07:00",
            "additionalInfo" =>  "Tagihan Sandbox Labschool Cibubur ".$customerNo,
            "token" => $token['accessToken']
        );
        $data = $this->create_va($data_req);
        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data, 'token'=>$token, 'req_data'=>$data_req);
        $status="200";

        $this->response($msg,$status);
    }

    public function get_token($data)
    {
        $url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/access-token/b2b";

        $clientId = "bmf114bUB3BjHsJfRFqXtiafpcG6MMH5";
        $privateKey = file_get_contents(PRIVATE_BRI_KEY);

        // $result = $this->generateSignature($clientId, $privateKey);

        $timestamp = $data['timestamp'];
        $signature = $data['signature'];

        // Header
        $headers = [
            "X-CLIENT-KEY: $clientId",
            "X-TIMESTAMP: $timestamp",
            "X-SIGNATURE: $signature",
            "Content-Type: application/json"
        ];

        // Body
        $body = json_encode([
            "grantType" => "client_credentials"
        ]);

        // CURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    function generateSignature($clientId, $privateKeyPem)
    {
        $dt = new DateTime("now", new DateTimeZone("Asia/Jakarta"));

        // FIX: ambil millisecond (3 digit, bukan 6)
        $milliseconds = substr($dt->format('u'), 0, 3);

        $timestamp = $dt->format("Y-m-d\TH:i:s") . "." . $milliseconds . $dt->format("P");

        $stringToSign = $clientId . '|' . $timestamp;

        $privateKey = openssl_pkey_get_private($privateKeyPem);

        if (!$privateKey) {
            throw new Exception("Invalid private key");
        }

        openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        $signatureBase64 = base64_encode($signature);

        return [
            'timestamp' => $timestamp,
            'signature' => $signatureBase64,
            'string_to_sign' => $stringToSign
        ];
    }

    public function create_va($data)
    {
        $url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/transfer-va/create-va";

        $token = $data['token'];

        $clientSecret = "ed59fvnJx3VsA4lc";
        $partnerId = "labschool";
        $channelId = "00004"; //00004 lainnya

        // timestamp
        $dt = new DateTime("now", new DateTimeZone("Asia/Jakarta"));
        $milliseconds = substr($dt->format('u'), 0, 3);
        $timestamp = $dt->format("Y-m-d\TH:i:s") . "." . $milliseconds . $dt->format("P");

        // body
        $body = json_encode([
            "partnerServiceId" => $data['partnerServiceId'],
            "customerNo" => $data['customerNo'],
            "virtualAccountNo" => $data['virtualAccountNo'],
            "virtualAccountName" => $data['virtualAccountName'],
            "trxId" => $data['trxId'],
            "totalAmount" => [
                "value" => $data['totalAmount'],
                "currency" => "IDR"
            ],
            "expiredDate" => $data['expiredDate'],
            "additionalInfo" => [
                "description" => $data['additionalInfo']
            ]
        ]);

        $endpoint = "/snap/v1.0/transfer-va/create-va";

        $signature = $this->generateSignatureVA("POST", $endpoint, $token, $body, $timestamp, $clientSecret);

        $headers = [
            "Authorization: Bearer $token",
            "X-TIMESTAMP: $timestamp",
            "X-SIGNATURE: $signature",
            "X-PARTNER-ID: $partnerId",
            "CHANNEL-ID: $channelId",
            "X-EXTERNAL-ID: " . rand(100000000, 999999999),
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function delete_va($data)
    {
        $url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/transfer-va/delete-va";

        $token = $this->get_token()['access_token'];

        $clientSecret = "ed59fvnJx3VsA4lc";
        $partnerId = "labschool";
        $channelId = "12345";

        // timestamp
        $dt = new DateTime("now", new DateTimeZone("Asia/Jakarta"));
        $milliseconds = substr($dt->format('u'), 0, 3);
        $timestamp = $dt->format("Y-m-d\TH:i:s") . "." . $milliseconds . $dt->format("P");

        $body = json_encode([
            "partnerServiceId" => $data['partnerServiceId'],
            "customerNo" => $data['customerNo'],
            "virtualAccountNo" => $data['virtualAccountNo'],
            "trxId" => $data['trxId']
        ]);

        $endpoint = "/snap/v1.0/transfer-va/delete-va";

        $signature = $this->generateSignatureVA("DELETE", $endpoint, $token, $body, $timestamp, $clientSecret);

        $headers = [
            "Authorization: Bearer $token",
            "X-TIMESTAMP: $timestamp",
            "X-SIGNATURE: $signature",
            "X-PARTNER-ID: $partnerId",
            "CHANNEL-ID: $channelId",
            "X-EXTERNAL-ID: " . rand(100000000, 999999999),
            "Content-Type: application/json"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    function generateSignatureVA($method, $endpoint, $token, $body, $timestamp, $clientSecret)
    {
        $hashedBody = hash('sha256', $body);
        $stringToSign = $method . ":" . $endpoint . ":" . $token . ":" . $hashedBody . ":" . $timestamp;

        return base64_encode(hash_hmac('sha512', $stringToSign, $clientSecret, true));
    }
}
