<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Daftar_ekskul extends REST_Controller {
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

        $jenjang = $this->post('jenjang');
        $id_siswa = $this->post('id_siswa');
        $id_ekskul = $this->post('id_ekskul');
        $tahun_ajaran = $this->post('tahun_ajaran');
        $semester = $this->post('semester');

        $data = array(
            "id_ekskul" => $id_ekskul,
            "id_siswa" => $id_siswa,
            "status_member" => 0,
            "joined_date" => date("Y-m-d H:i:s"),
            "biaya" => $this->post('biaya'),
            "tahun_ajaran" => $tahun_ajaran,
            "semester" => $semester
        );

        $cek = $this->second_db->withquery("select * from ekskul_member_".$jenjang." where id_siswa = '".$id_siswa."' and id_ekskul = '".$id_ekskul."' and tahun_ajaran = '".$tahun_ajaran."' and semester = '".$semester."'", "row");

        if (!empty($data) && empty($cek)) {
            //File Sertifikat
            if (!empty($_FILES['file_pembayaran']['name'])) {
                $uploaddir = './uploads/ekskul_member_'.$jenjang.'/';
                $img = explode('.', $_FILES['file_pembayaran']['name']);
                $extension = end($img);
                $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_pembayaran']['name']).".".$extension;
                $uploadfile = $uploaddir.$file_name;
                $status = 0;
                if (move_uploaded_file($_FILES['file_pembayaran']['tmp_name'], $uploadfile)) {
                  $data['file_pembayaran'] = $file_name;
                  $data['payment_date'] = date("Y-m-d H:i:s");
                  $msg = array('success'=>1,'message'=>'Upload Bukti Bayar Berhasil');
                }
            }
            $insert = $this->second_db->insertid("ekskul_member_".$jenjang, $data);
            $data["id_member"] = $insert;
            $msg = array('status' => 200, 'message'=>'Berhasil Registrasi, Silahkan refresh halaman agar tampil tombol uplod bukti pembayaran' ,'data'=>$data);
        }
        else if (!empty($cek)) {
            $msg = array('status' => 402, 'message'=>'Gagal melakukan registrasi (Akun telah terdaftar)' ,'data'=>$cek);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Gagal melakukan registrasi (Data tidak valid)' ,'data'=>null);
        }

        $this->response($msg);
    }
}
