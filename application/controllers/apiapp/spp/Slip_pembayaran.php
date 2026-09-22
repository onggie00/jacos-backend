<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Slip_pembayaran extends REST_Controller {
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

        $id_transaksi = $this->post('id_transaksi');

        $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = '".$id_transaksi."'" ,"row");

        if (!empty($get_transaksi)) {
            $logo = "";
            if ($get_transaksi->nama_bank == "BNI") {
                $logo = base_url().'/uploads/logo_bni.png';
            }
            else if($get_transaksi->nama_bank == "BRI"){
                $logo = base_url().'/uploads/logo_bri.png';
            }
            $subtotal = (empty($get_transaksi->count_bill)) ? '' : $get_transaksi->total_biaya*$get_transaksi->count_bill;
            $data = array(
                "id_transaksi" => $get_transaksi->id_transaksi,
                "status_transaksi" => $get_transaksi->status_transaksi,
                "va_number" => $get_transaksi->va_number,
                "kode_tagihan" => $get_transaksi->kode_tagihan,
                "expired_datetime" => $get_transaksi->expired_datetime,
                "detail_bulan" => $get_transaksi->detail_bulan,
                "logo_bank" => $logo,
                "nominal_tagihan" => (string)$subtotal
            );
            $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
            $status="200";
        }

        $this->response($msg,$status);
    }
}
