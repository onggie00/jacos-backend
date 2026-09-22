<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Cek_pengumuman extends REST_Controller {
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

      if(isset($headers['user_role']))
        $role =  $headers['user_role'];

        $email = $this->post('email');
        $tipe_siswa = $this->post('tipe_siswa');
        $nama_lengkap = $this->post('nama_lengkap');
        $data = null;

          //get tahun ajaran
          $tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran_psb","row")->label;
          if ($tipe_siswa == "ft") {
            $gelombang = $this->mymodel->withquery("select gelombang from web_periode_daftar where kelas like '%france track%'","row")->gelombang;
          }
          else{
            $gelombang = $this->mymodel->withquery("select gelombang from web_periode_daftar where kelas like '%".$tipe_siswa."%'","row")->gelombang;
          }
          if (!empty($tipe_siswa == "ft") && !empty($email) && !empty($tipe_siswa)) {
            $data = $this->mymodel->withquery("select s.id_siswa_ft as id_siswa, s.email, s.nama_lengkap, s.va_number, s.no_peserta, s.sekolah_asal , s.status_lulus as status_lulus_int, st.status_lulus, s.foto_peserta, s.cadangan_no from siswa_ft s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$email."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and tahun_ajaran ='".$tahun_ajaran."'  ","row");
            if (!empty($data)) {
              $data->tipe_siswa = "ft";
              $data->label_tipe = "SMA";
              $get_ucapan = $this->mymodel->getbywhere("pengumuman_ucapan","jenjang","FRANCE TRACK","row");
            }
          }

          if (!empty($tipe_siswa == "sma") && !empty($email) && !empty($tipe_siswa)) {
            $data = $this->mymodel->withquery("select s.id_siswa_sma as id_siswa, s.email, s.nama_lengkap, s.va_number, s.no_peserta, s.sekolah_asal , s.status_lulus as status_lulus_int, st.status_lulus, s.foto_peserta from siswa_sma s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$email."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and tahun_ajaran ='".$tahun_ajaran."'  ","row");
            if (!empty($data)) {
              $data->tipe_siswa = "sma";
              $data->label_tipe = "SMA";
              $get_ucapan = $this->mymodel->getbywhere("pengumuman_ucapan","jenjang","SMA","row");
            }
          }

          if (!empty($tipe_siswa == "smp") && !empty($email) && !empty($tipe_siswa)) {
            $data = $this->mymodel->withquery("select s.id_siswa_smp as id_siswa, s.email, s.nama_lengkap, s.va_number, s.no_peserta, s.sekolah_asal , s.status_lulus as status_lulus_int, st.status_lulus, s.foto_peserta from siswa_smp s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$email."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and tahun_ajaran ='".$tahun_ajaran."'  ","row");
            if (!empty($data)) {
              $data->tipe_siswa = "smp";
              $data->label_tipe = "SMP";
              $get_ucapan = $this->mymodel->getbywhere("pengumuman_ucapan","jenjang","SMP","row");
            }
          }

          if (!empty($tipe_siswa == "sd") && !empty($email) && !empty($tipe_siswa)) {
            $data = $this->mymodel->withquery("select s.id_siswa_sd as id_siswa, s.email, s.nama_lengkap, s.va_number, s.no_peserta, s.sekolah_asal , s.status_lulus as status_lulus_int, st.status_lulus, s.foto_peserta from siswa_sd s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%".$email."%' and nama_lengkap like '%".$this->db->escape_like_str($nama_lengkap)."%' and tahun_ajaran ='".$tahun_ajaran."'  ","row");
            if (!empty($data)) {
              $data->tipe_siswa = "sd";
              $data->label_tipe = "SD";
              $get_ucapan = $this->mymodel->getbywhere("pengumuman_ucapan","jenjang","SD","row");
            }
          }
          // echo $this->db->last_query();
        if (!empty($data)) {
          $get_judul = $this->mymodel->getbywhere("judul_kartu_sementara","jenjang",$tipe_siswa,"row");
          $data->judul_pengumuman=$get_judul->judul;
          if ($data->status_lulus_int == "1") {
            $data->ucapan_pengumuman = $get_ucapan->ucapan_belum_tersedia;
            $data->status_lulus = "";
            $data->file_attachment = "";
          }
          else if($data->status_lulus_int == "2"){
            $data->ucapan_pengumuman = $get_ucapan->ucapan_lulus;
            $data->status_lulus = "LULUS";
            $file_attachment = $this->mymodel->withquery("select * from web_pengumuman_lulus where jenjang = '".$tipe_siswa."'" ,"row")->dokumen;
            $data->file_attachment = base_url("uploads/web_pengumuman_lulus/").$file_attachment;
          }
          else if($data->status_lulus_int == "3"){
            $data->ucapan_pengumuman = $get_ucapan->ucapan_tidak_lulus;
            $data->status_lulus = "TIDAK LULUS";
            $data->file_attachment = "";
          }
          else if($data->status_lulus_int == "4"){
            $ucapan = str_replace("CADANGAN", "CADANGAN ".$data->cadangan_no, $get_ucapan->ucapan_cadangan);
            $data->ucapan_pengumuman = $ucapan;
            $data->status_lulus = "CADANGAN";
            $data->file_attachment = "";
          }

          if(!empty($data->foto_peserta)){
            $data->foto_peserta = base_url("uploads/siswa_".strtolower($tipe_siswa)."/").$data->foto_peserta;
          }
          $data->tahun_ajaran = $tahun_ajaran;
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