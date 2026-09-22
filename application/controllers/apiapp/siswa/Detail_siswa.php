<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_siswa extends REST_Controller {
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
        $id = $this->post('id');
        $jenjang = $this->post('jenjang');
        
        if($jenjang=='sd'){
          $data = $this->mymodel->withquery("select sa.*,s.*,k.*,t.id_tahun_ajaran,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sd k on k.id_kelas_sd = sa.id_kelas where id_siswa_sd_aktif = ".$id."","row");
          if(!empty($data)){
            if(!empty($data->foto_profil)) $data->foto_profil=base_url('uploads/siswa_sd/').$data->foto_profil;
            if(!empty($data->id_tingkatan)){
              $tingkatan = $this->mymodel->withquery("SELECT label from tingkatan_sd where id_tingkatan_sd = ".intval($data->id_tingkatan),"row");
              $data->nama_tingkatan = !empty($tingkatan) ? $tingkatan->label : '';
            } else { $data->nama_tingkatan = ''; }
            if(!empty($data->file_raport)) $data->file_raport=base_url('uploads/siswa_sd_aktif/').$data->file_raport;
          }
        }else if($jenjang=='smp'){
          $data = $this->mymodel->withquery("select sa.*,s.*,k.*,t.id_tahun_ajaran,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_smp_aktif sa join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_smp k on k.id_kelas_smp = sa.id_kelas where id_siswa_smp_aktif = ".$id."","row");
          if(!empty($data)){
            if(!empty($data->foto_profil)) $data->foto_profil=base_url('uploads/siswa_smp/').$data->foto_profil;
            if(!empty($data->id_tingkatan)){
              $tingkatan = $this->mymodel->withquery("SELECT label from tingkatan_smp where id_tingkatan_smp = ".intval($data->id_tingkatan),"row");
              $data->nama_tingkatan = !empty($tingkatan) ? $tingkatan->label : '';
            } else { $data->nama_tingkatan = ''; }
            if(!empty($data->file_raport)) $data->file_raport=base_url('uploads/siswa_smp_aktif/').$data->file_raport;
          }
        }else if($jenjang=='sma'){
          $data = $this->mymodel->withquery("select sa.*,s.*,k.*,t.id_tahun_ajaran,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sma k on k.id_kelas_sma = sa.id_kelas where id_siswa_sma_aktif = ".$id."","row");
          if(!empty($data)){
            if(!empty($data->foto_profil)) $data->foto_profil=base_url('uploads/siswa_sma/').$data->foto_profil;
            if(!empty($data->id_tingkatan)){
              $tingkatan = $this->mymodel->withquery("SELECT label from tingkatan_sma where id_tingkatan_sma = ".intval($data->id_tingkatan),"row");
              $data->nama_tingkatan = !empty($tingkatan) ? $tingkatan->label : '';
            } else { $data->nama_tingkatan = ''; }
            if(!empty($data->file_raport)) $data->file_raport=base_url('uploads/siswa_sma_aktif/').$data->file_raport;
          }
        }else if($jenjang=='ft'){
          $data = $this->mymodel->withquery("select sa.*,s.*,k.*,t.id_tahun_ajaran,t.label as tahun_ajaran,sa.nama_lengkap as nama_lengkap from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_ft k on k.id_kelas_ft = sa.id_kelas where id_siswa_ft_aktif = ".$id."","row");
          if(!empty($data)){
            if(!empty($data->foto_profil)) $data->foto_profil=base_url('uploads/siswa_ft/').$data->foto_profil;
            if(!empty($data->id_tingkatan)){
              $tingkatan = $this->mymodel->withquery("SELECT label from tingkatan_ft where id_tingkatan_ft = ".intval($data->id_tingkatan),"row");
              $data->nama_tingkatan = !empty($tingkatan) ? $tingkatan->label : '';
            } else { $data->nama_tingkatan = ''; }
            if(!empty($data->file_raport)) $data->file_raport=base_url('uploads/siswa_ft_aktif/').$data->file_raport;
          }
        }

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
