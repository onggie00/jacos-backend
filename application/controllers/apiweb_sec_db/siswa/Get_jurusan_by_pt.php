<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_jurusan_by_pt extends REST_Controller {
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
        $where="";
        
        // dd($where);
        $id_pt = $this->get("id_pt");
        $tahun_ajaran = $this->get("tahun_ajaran");
        $where = "";
        if (!empty($tahun_ajaran) && $where == "") {
          $where .= "where j.tahun_ajaran = '".$tahun_ajaran."'";
        }
        else if(!empty($tahun_ajaran)){
          $where .= "and j.tahun_ajaran = '".$tahun_ajaran."'";
        }
        if (!empty($id_pt) && $where == "") {
          $where .= "where j.id_perguruan_tinggi = '".$id_pt."'";
        }
        else if(!empty($id_pt)){
          $where .= "and j.id_perguruan_tinggi = '".$id_pt."'";
        }

        if (empty($id_pt)) {
          $data = $this->second_db->withquery("select j.id, j.id_perguruan_tinggi as id_pt, j.jurusan, j.passing_grade, pt.nama_pt, pt. inisial from pt_jurusan j join pt_perguruan_tinggi pt on j.id_perguruan_tinggi = pt.id ".$where." order by j.passing_grade DESC, pt.nama_pt ASC","result");
        }
        else{
          $data = $this->second_db->withquery("select j.id, j.id_perguruan_tinggi as id_pt, j.jurusan, j.passing_grade, pt.nama_pt, pt. inisial from pt_jurusan j join pt_perguruan_tinggi pt on j.id_perguruan_tinggi = pt.id ".$where." order by j.passing_grade DESC, pt.nama_pt ASC","result");
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
