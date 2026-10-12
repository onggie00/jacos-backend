<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_guru extends REST_Controller {
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
          $headers[strtolower($name)] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $id = $this->post('id');
        $jenjang = $this->post('jenjang');
        $join="left join mata_pelajaran_$jenjang m on m.id_mapel = g.id_mapel";
        
        if($jenjang=='sd'){
          $data = $this->mymodel->withquery("select g.*,m.* from guru_sd g $join where id_guru = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/guru_sd/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/guru_sd/').$data->slip_gaji;
          }
        }else if($jenjang=='smp'){
          $data = $this->mymodel->withquery("select g.*,m.* from guru_smp g $join where id_guru = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/guru_smp/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/guru_smp/').$data->slip_gaji;
          }
        }else if($jenjang=='sma'){
          $data = $this->mymodel->withquery("select g.*,m.* from guru_sma g $join where id_guru = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/guru_sma/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/guru_sma/').$data->slip_gaji;
          }
        }else if($jenjang=='ft'){
          $data = $this->mymodel->withquery("select g.*,m.* from guru_ft g $join where id_guru = ".$id."","row");
          if($data->foto_profil){
            $data->foto_profil=base_url('uploads/guru_ft/').$data->foto_profil;
          }
          if($data->slip_gaji){
            $data->slip_gaji=base_url('uploads/guru_ft/').$data->slip_gaji;
          }
        }

        if (!empty($data)) {
          if (empty($data->nik)){
            $data->nik = "";
          }
          if (empty($data->nuptk)){
            $data->nuptk = "";
          }
          if (empty($data->npp)){
            $data->npp = "";
          }
          if (empty($data->npwp)){
            $data->npwp = "";
          }
          // [JACOS] bahasa terpilih user utk aplikasi (fase 7)
          $tok = '';
          foreach (getallheaders() as $hk => $hv) { if (strtolower($hk) === 'x-token') { $tok = trim($hv); break; } }
          $this->load->helper('bahasa_token_helper');
          $this->load->helper('translator_safe_db');
          $this->load->helper('guru_token');
          $this->load->config('translator');
          $auth = validate_guru_token($tok, (bool) translator_cfg('translator_language_check_expiry', false));
          $data->selected_language = 'id';
          if (!empty($auth['valid']) && !empty($auth['data']->email_ms_office)) {
            $this->load->model('Setting_bahasa_model');
            $data->selected_language = $this->Setting_bahasa_model->get_kode_bahasa($auth['data']->email_ms_office, 'guru');
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
