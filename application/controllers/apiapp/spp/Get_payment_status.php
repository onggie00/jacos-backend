<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_payment_status extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $va_number=$this->post('va_number');
        $jenjang=$this->post('jenjang');

        if($jenjang=="sd"){
          $brivaNo=substr($va_number,0, 5);
          $custCode=substr($va_number, 5);

          //$data=$this->get_bri(array("brivaNo"=>$brivaNo,"custCode"=>$custCode));
          $data = null;
        }elseif($jenjang=="ft"){
          $get_no_transaksi = $this->mymodel->withquery("select * from transaksi_spp where va_number = '".$va_number."'","row");

          $data=$this->get_bni_ft($get_no_transaksi->kode_tagihan);
        }else{
          $get_no_transaksi = $this->mymodel->withquery("select * from transaksi_spp where va_number = '".$va_number."'","row");
          if (!empty($get_no_transaksi->kode_tagihan)){
            $data=$this->get_bni($get_no_transaksi->kode_tagihan);
          }
          else{
            $data = array(
              "status_bayar" => "Paid"
            );
          }
        }
            
        if (!empty($data) && $jenjang != "sd") {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }

    function get_bni($no_transaksi){
      $this->load->library('BniEnc');

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

    function get_bri($data){
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

		$endpoint = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

    return BriApi::getData($clientID, $clientSecret, $url, $endpoint, $institutionCode, $data);

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
