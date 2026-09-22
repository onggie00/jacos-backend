<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_tagihan extends REST_Controller {
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
        $id_siswa_aktif = $this->post("id_siswa_aktif");
        $jenjang= strtolower($this->post("jenjang"));
        $id_tagihan = $this->post("id_tagihan");
        $where = "";
        
        if (!empty($id_tagihan)) {
          $where .= " and t.id = ".$id_tagihan;
        }

        $data = $this->mymodel->withquery("select t.id as id_tagihan, t.tanggal_bayar, t.va_number, t.file_kwitansi, t.nominal_bayar, t.status_transaksi, m.nama_transaksi, m.id_tahun_ajaran, m.tipe_bank, ta.label as tahun_ajaran, k.nama_kategori, t.updated_at, t.expired_at from transaksi_lain_".$jenjang." t join transaksi_lain_".$jenjang."_manajemen m on t.id_transaksi_lain = m.id join transaksi_lain_kategori k on m.id_kategori = k.id_kategori join tahun_ajaran ta on m.id_tahun_ajaran = ta.id_tahun_ajaran where t.id_siswa_aktif = '".$id_siswa_aktif."' ".$where." order by t.id DESC, m.id_tahun_ajaran DESC limit 1","result");
        // dd($where);
        //echo $this->db->last_query();

        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (!empty($value->file_kwitansi)) {
              $value->file_kwitansi = base_url("uploads/transaksi_lain/").$value->file_kwitansi;
            }
            if ($value->status_transaksi == "0") {
              $value->status_text = "Belum Dibayar";
            }
            $expired = date("Y-m-d H:i:s", strtotime($value->expired_at));
            //echo $value->updated_at." > ".$expired;
            if ($value->status_transaksi == "1" && date("Y-m-d H:i:s") > $expired ) {
              $value->status_transaksi = "3";
              $value->status_text = "Pembayaran Gagal";
            }
            else if($value->status_transaksi == "1" && date("Y-m-d H:i:s") <= $expired) {
              $value->status_text = "Menunggu Pembayaran";
            }
            if ($value->status_transaksi == "2") {
              $value->status_text = "Lunas";
            }
            if ($value->status_transaksi == "3") {
              $value->status_text = "Pembayaran Gagal";
            }
            if ($value->tipe_bank == "BNI") {
              $value->logo_bank = base_url().'/uploads/logo_bni.png';
            }
            else if($value->tipe_bank == "BRI"){
              $value->logo_bank = base_url().'/uploads/logo_bri.png';
            }
          }
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
