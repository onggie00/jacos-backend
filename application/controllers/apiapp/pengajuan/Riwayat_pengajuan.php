<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Riwayat_pengajuan extends REST_Controller {
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

        $npp = $this->get("npp");
        $get_user = $this->get_user_by_npp($npp);
        $role = $get_user->presensi_role;
        $date_start = $this->get("date_start");
        $date_end = $this->get("date_end");
        $where = "";
        $bindings = array();

        if (!empty($date_start) && !empty($date_end)) {
            $where .= " and created_at >= ? and created_at <= ? ";
            $bindings[] = $date_start." 00:00:00";
            $bindings[] = $date_end." 23:59:59";
        }
        $data = $this->mymodel->withquery("select * from presensi_office_submission where npp = '".$npp."' ".$where." order by id_submission DESC","result",$bindings);

        if (!empty($data)) {
            foreach($data as $key => $value){
                if ($value->head_role == $role){
                    $value->is_approver = 1;
                }
                else{
                    $value->is_approver = 0;
                }
                if (!empty($value->file_submission)){
                    $value->file_submission = base_url($value->file_submission);
                }

                $value->pdf_url = base_url("apiapp/pengajuan/export_pdf?id_submission=").$value->id_submission;
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

    public function get_user_by_npp($npp)
    {
        $tables = [
            // pimpinan
            ["table" => "pimpinan_sma", "role" => "pimpinan", "jenjang" => "sma"],
            ["table" => "pimpinan_smp", "role" => "pimpinan", "jenjang" => "smp"],
            ["table" => "pimpinan_sd",  "role" => "pimpinan", "jenjang" => "sd"],

            // guru
            ["table" => "guru_ft", "role" => "guru", "jenjang" => "ft"],
            ["table" => "guru_sma", "role" => "guru", "jenjang" => "sma"],
            ["table" => "guru_smp", "role" => "guru", "jenjang" => "smp"],
            ["table" => "guru_sd", "role" => "guru", "jenjang" => "sd"],

            // pegawai
            ["table" => "pegawai", "role" => "employee", "jenjang" => null],
        ];

        foreach ($tables as $t) {

            $whereDeleted = (strpos($t["table"], 'guru') !== false) 
                ? " AND deleted_at IS NULL" 
                : "";

            $query = "SELECT * FROM {$t["table"]} WHERE npp = '$npp' $whereDeleted LIMIT 1";
            $data = $this->mymodel->withquery($query, "row");

            if (!empty($data)) {

                // set role & jenjang
                $data->role = $t["role"];
                if (!empty($t["jenjang"])) {
                    $data->jenjang = $t["jenjang"];
                }

                // refresh token
                // $data = $this->refresh_token($t["table"], "npp", $npp, $data);

                return $data;
            }
        }

        return null;
    }
}
