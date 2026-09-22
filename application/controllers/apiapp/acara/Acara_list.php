<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Acara_list extends REST_Controller {
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
        
        $where = "";
        $role = $this->get("role");
        $npp = $this->get("npp");
        $search = $this->get("search");
        $unique_code = $this->get("unique_code");
        if (!empty($search)) {
            $where  = "and nama_acara like '%".$this->db->escape_like_str($search)."%' ";
        }
        if (!empty($unique_code)) {
            $where  = "and unique_code = ".$this->db->escape($unique_code)." ";
        }


        $data = $this->mymodel->withquery("select id_acara, nama_acara, peserta_acara, waktu_mulai, waktu_selesai, lokasi, narasumber, keterangan, is_certificated, no_certificate, evaluation_url from acara where peserta_acara like '%".$role."%' ".$where." order by waktu_mulai DESC, id_acara DESC ","result");
        //echo $this->db->last_query();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                //get absensi user di acara tsb
                $get_presensi = $this->mymodel->withquery("select npp, role, waktu_presensi, waktu_presensi_selesai, custom_sertifikat from acara_presensi where id_acara = '".$value->id_acara."' and npp = ".$this->db->escape($npp),"row");
                if (!empty($get_presensi)){
                    if (!empty($get_presensi->waktu_presensi_selesai) && !empty($get_presensi->custom_sertifikat)){
                        $value->is_done = 1;
                        //get_custom_sertifikat
                        $custom_sertifikat = $this->mymodel->withquery("select custom_sertifikat from acara_presensi where id_acara = ".$this->db->escape($value->id_acara)." and npp = ".$this->db->escape($npp),"row")->custom_sertifikat;
                        //echo $this->db->last_query();
                        if (!empty($custom_sertifikat)){
                            //$value->certificate_url = base_url("apiapp/acara/get_custom_sertifikat")."?id_acara=".$value->id_acara."&npp=".$npp."&role=".$role;
                            $value->certificate_url = base_url("uploads/acara_presensi/").$custom_sertifikat;
                        }
                        else{
                            $value->certificate_url = null;
                        }
                    }
                    else if (!empty($get_presensi->waktu_presensi_selesai) && empty($get_presensi->custom_sertifikat)){
                        $value->is_done = 1;
                        $value->certificate_url = base_url("apiapp/acara/lihat_sertifikat")."?id_acara=".$value->id_acara."&npp=".$npp."&role=".$role;
                    }
                    else{
                        $value->is_done = 0;
                        $value->certificate_url = null;
                    }
                    $value->presensi = $get_presensi;
                }
                else{
                    $value->is_done = 0;
                    $value->presensi = null;
                    $value->certificate_url = null;
                }

                /* if (!empty($value->qr_code)){
                    $value->qr_code = base_url("uploads/acara/").$value->qr_code;
                }
                if (!empty($value->qr_code_finish)){
                    $value->qr_code_finish = base_url("uploads/acara/").$value->qr_code_finish;
                } */
               //cek evaluation_form 
                $get_evaluasi_form = $this->mymodel->withquery("select id_evaluasi_form from acara_evaluasi_form where id_acara = '".$value->id_acara."'","row");
                if (!empty($get_evaluasi_form)){
                    $value->is_have_evaluasi_form = 1;
                    //cek jika user sudah mengisi evaluasi_form
                    $get_evaluasi_user = $this->mymodel->withquery("select id_evaluasi_form from acara_presensi_evaluasi where id_acara = '".$value->id_acara."' and npp = '".$npp."'", "row");
                    if (!empty($get_evaluasi_user)){
                        $value->is_done_evaluasi_form = 1;
                    }
                    else{
                        $value->is_done_evaluasi_form = 0;
                    }
                }
                else{
                    if($value->is_done == 1 && empty($value->evaluation_url)){
                        $value->is_done_evaluasi_form = 1; //belum presensi

                    }
                    $value->is_have_evaluasi_form = 0;
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
