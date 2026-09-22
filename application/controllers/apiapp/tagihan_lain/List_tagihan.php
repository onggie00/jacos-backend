<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class List_tagihan extends REST_Controller {
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
        $id_kategori_tagihan = $this->post("id_kategori_tagihan");
        $status_tagihan = $this->post("status_tagihan");
        $tahun_ajaran = $this->post("id_tahun_ajaran");
        $where = "";
        
        if (!empty($id_kategori_tagihan)) {
          $where .= " and m.id_kategori = ".$id_kategori_tagihan;
        }
        /*if ($status_tagihan != "") {
          $where .= " and t.status_transaksi = ".$status_tagihan;
        }*/
        if ($tahun_ajaran != "") {
          $where .= " and m.id_tahun_ajaran = ".$tahun_ajaran;
        }

        $data = $this->mymodel->withquery("select t.id as id_tagihan, t.tanggal_bayar, t.va_number, t.file_kwitansi, t.nominal_bayar, t.status_transaksi, m.nama_transaksi, m.id_tahun_ajaran, ta.label as tahun_ajaran, k.nama_kategori, t.updated_at, t.expired_at from transaksi_lain_".$jenjang." t 
        join transaksi_lain_".$jenjang."_manajemen m on t.id_transaksi_lain = m.id 
        join transaksi_lain_kategori k on m.id_kategori = k.id_kategori 
        join tahun_ajaran ta on m.id_tahun_ajaran = ta.id_tahun_ajaran 
        where t.id_siswa_aktif = '".$id_siswa_aktif."' and (t.status_transaksi = 0) and m.tanggal_tagihan_selesai > '".date("Y-m-d H:i:s")."' ".$where." order by t.id DESC, m.id_tahun_ajaran DESC ","result");
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
            if ($value->status_transaksi == "1") {
              $value->status_text = "Menunggu Pembayaran";
            }
            if ($value->status_transaksi == "2") {
              $value->status_text = "Lunas";
            }
            if (!empty($value->expired_at)) {
              $expired = date("Y-m-d H:i:s", strtotime($value->expired_at));
            }
            else{
              $expired = null;
            }
            //echo $value->updated_at." > ".$expired;
            if ($value->status_transaksi == "1" && !empty($expired) && date("Y-m-d H:i:s") > $expired ) {
              $value->status_transaksi = "3";
              $value->status_text = "Pembayaran Gagal";
            }
            else if($value->status_transaksi == "1" && !empty($expired) && date("Y-m-d H:i:s") <= $expired) {
              $value->status_text = "Menunggu Pembayaran";
            }
            else if ($value->status_transaksi == "2") {
              $value->status_text = "Lunas";
            }
            else if ($value->status_transaksi == "3") {
              $value->status_text = "Pembayaran Gagal";
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
