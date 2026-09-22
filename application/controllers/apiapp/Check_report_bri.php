<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Check_report_bri extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

      $va_number = $this->get("va_number");

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
      if($this->input->get('end_date')){
        $start_date=$this->input->get('start_date');
        $end_date=$this->input->get('end_date');
        
      }else{
        $start_date=date('Y').date('m').date('d');
        $end_date=date('Y').date('m').date('d');
      }

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
        
      }

        if (!empty($data)) {
          //$this->export_xml();
          /*$data_bayar = $data['data'];
          $status_bayar = $data_bayar['statusBayar'];*/
          $msg = array('status' => 1, 'message'=>'Berhasil lihat data Riwayat Transaksi' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Gagal lihat data Riwayat Transaksi' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
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

}
