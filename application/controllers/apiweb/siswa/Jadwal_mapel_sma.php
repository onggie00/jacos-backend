<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Jadwal_mapel_sma extends REST_Controller {
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
      $date=date('Y-m-d');
      $where = ""; $where_siswa="";
      $id_siswa = $this->post('id_siswa_aktif');
      $id_guru = $this->post('id_guru');
      $hari = $this->post('hari');
      
      if($id_siswa){
        $get_siswa=$this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sma k on k.id_kelas_sma = sa.id_kelas where id_siswa_sma_aktif = $id_siswa","row");
        if(!$get_siswa){
          $msg = array('status' => 0, 'message' => 'Siswa tidak ditemukan', 'data' => array());
          $status = "200";
          $this->response($msg,$status);
        }
        $where_siswa="and j.id_kelas = $get_siswa->id_kelas
        and j.id_tahun_ajaran = $get_siswa->id_tahun_ajaran";
      }
      if ($id_guru) {
        $where = "and j.id_guru = " . $id_guru;
      }

      if($hari){
        $day=$hari;
      }else{
        $day = get_day(date('w'));
      }  
      $now =  date('H:i:s');
        $data = $this->mymodel->withquery("
        SELECT j.*,t.*,k.*,m.* 
        FROM `jadwal_mapel_sma` j 
        join tahun_ajaran t on t.id_tahun_ajaran = j.id_tahun_ajaran 
        join kelas_sma k on k.id_kelas_sma = j.id_kelas 
        join mata_pelajaran_sma m on m.id_mapel = j.id_mapel 
        where hari = '$day' 
        $where_siswa
        $where
                                           "
                                           ,"result");

        if (!empty($data)) {
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
