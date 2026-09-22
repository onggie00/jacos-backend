<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Pilihan_ptn_list extends REST_Controller {
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
        $where="";
        
        // dd($where);
        $jenjang = $this->post("jenjang");
        $id_siswa = $this->post("id_siswa");
        $tahun_ajaran = $this->post("tahun_ajaran");

        if (empty($tahun_ajaran)) {
          $data_siswa = $this->mymodel->withquery("select pt.nilai_utbk1, pt.nilai_utbk2, pt.nilai_utbk3, pt.nilai_utbk4, pt.nilai_nasional, pt.tahun_ajaran from pt_to_siswa pt where pt.id_siswa_aktif = '".$id_siswa."' and pt.jenjang='".$jenjang."' order by id DESC","row");
        }
        else{
          $data_siswa = $this->mymodel->withquery("select pt.nilai_utbk1, pt.nilai_utbk2, pt.nilai_utbk3, pt.nilai_utbk4, pt.nilai_nasional, pt.tahun_ajaran from pt_to_siswa pt where pt.id_siswa_aktif = '".$id_siswa."' and pt.jenjang='".$jenjang."' and pt.tahun_ajaran = '".$tahun_ajaran."' order by id DESC","row");
        }

        $data = $this->mymodel->withquery("select pp.*, pt.nama_pt, pt.logo_ptn, j.jurusan, j.passing_grade from pt_pilihan_ptn pp join pt_perguruan_tinggi pt on pp.id_pt = pt.id join pt_jurusan j on pp.id_jurusan = j.id where pp.jenjang = '".$jenjang."' and pp.id_siswa_aktif = '".$id_siswa."' order by pp.id ASC","result");
        if (!empty($data)) {
          foreach ($data as $key => $value) {
            if (!empty($value->logo_ptn)) {
              $base_url = "https://admin.labschoolcibubur.sch.id/";
              $value->logo_ptn = $base_url."uploads/pt_perguruan_tinggi/".$value->logo_ptn;
            }
            $value->persentase_ketercapaian_1 = ($data_siswa->nilai_utbk1*100)/$value->passing_grade;
            $value->persentase_ketercapaian_2 = ($data_siswa->nilai_utbk2*100)/$value->passing_grade;
            $value->persentase_ketercapaian_3 = ($data_siswa->nilai_utbk3*100)/$value->passing_grade;
            $value->persentase_ketercapaian_4 = ($data_siswa->nilai_utbk4*100)/$value->passing_grade;
            $value->created_at = formatTanggal($value->created_at)." ".date("H:i", strtotime($value->created_at))." WIB";
            $value->updated_at = formatTanggal($value->updated_at)." ".date("H:i", strtotime($value->updated_at))." WIB";
            unset($value->status);
          }
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' , 'data_siswa' => $data_siswa, 'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' , 'data_siswa' => $data_siswa, 'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}
