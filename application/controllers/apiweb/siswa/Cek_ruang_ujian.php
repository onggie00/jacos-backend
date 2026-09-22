<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Cek_ruang_ujian extends REST_Controller {
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

      if(isset($headers['user_role']))
        $role =  $headers['user_role'];

        $jenjang = $this->get("jenjang");
        //CEK RUANGAN TERSEDIA
        $cek_ruangan = $this->mymodel->withquery("select r.id_ruang_pendaftaran, r.nama_ruang, r.maks_peserta, 
        (select count(*) from ujian_ruang_pendaftaran_detail_".strtolower($jenjang)." 
        where r.id_ruang_pendaftaran = ujian_ruang_pendaftaran_detail_".strtolower($jenjang).".id_ruang_pendaftaran) as total_peserta_now 
        from ujian_ruang_pendaftaran_".strtolower($jenjang)." r order by nama_ruang ASC","result");
        if (!empty($cek_ruangan)) {
            foreach ($cek_ruangan as $key => $item) {
                if ($item->total_peserta_now < $item->maks_peserta) {
                    $data['id_ruang_pendaftaran'] = $item->id_ruang_pendaftaran;
                    $data['nama_ruang'] = $item->nama_ruang;
                    $data['is_available'] = true;
                    $data['total_peserta_now'] = $item->total_peserta_now;
                    $data['total_peserta_max'] = $item->maks_peserta;
                    break;
                }
                else{
                    $data['id_ruang_pendaftaran'] = null;
                    $data['nama_ruang'] = null;
                    $data['is_available'] = false;
                    $data['total_peserta_now'] = null;
                    $data['total_peserta_max'] = null;
                }
            }
            $msg = array('status' => 1, 'message'=>'Ruangan tersedia, silahkan melanjutkan pendaftaran' ,'data'=>$data);
            $status="200";
        }
        else{
            $msg = array('status' => 0, 'message'=>'Ruangan tidak tersedia, mohon hubungi humas sekolah' ,'data'=>array());
            $status="200";
        }

        

        $this->response($msg,$status);
    }
}