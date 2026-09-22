<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Presensi_ekskul_pelatih extends REST_Controller {
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

        $jenjang=$this->post('jenjang');
        $list_pelatih = explode(",",$this->post('id_pelatih'));
        $id_pelatih = $this->post('id_pelatih');
        $id_ekskul = $this->post('id_ekskul');
        $waktu_absen = $this->post('waktu_absen');
        $tanggal_absen = $this->post('tanggal_absen'); //2023-11-01
        $today = strtolower(formatHari(date("Y-m-d", strtotime($this->post('tanggal_absen')))));

        for ($i=0; $i < count($list_pelatih); $i++) {
            $where="where id_pelatih = '$list_pelatih[$i]' ";
            $where_tanggalabsen="and tanggal_absen = '$tanggal_absen'";
            $where_ekskul="and id_ekskul = '$id_ekskul'";

            $check_if_presented = $this->mymodel->withquery("select * from ekskul_presensi_pelatih_".$jenjang." $where $where_tanggalabsen $where_ekskul order by id desc","result");

            if ($check_if_presented){
                $msg = array('status' => 401, 'message'=>'pelatih sudah melakukan absen ekskul untuk hari ini!');
            }else{
                $data = array(
                    //"jenjang" => $jenjang,
                    "id_pelatih" => $list_pelatih[$i],
                    "id_ekskul" => $id_ekskul,
                    "hari_absen" => strtolower($today),
                    "waktu_absen" => $waktu_absen,
                    "tanggal_absen" => $tanggal_absen,
                    "status_absen" => $this->post('status_absen'),
                );
                if (!empty($data)) {
                    $insert=$this->mymodel->insertid("ekskul_presensi_pelatih_".$jenjang, $data);
                    if($insert){
                        $msg = array('status' => 200, 'message'=>'Berhasil melakukan absen!', 'data' => $data);
                    }else{
                        $msg = array('status' => 401, 'message'=>'Gagal melakukan absen!', 'data' => $data);
                    }
                }
            }
        }
        $this->response($msg);
    }
}
