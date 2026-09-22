<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Upload_pembayaran_ekskul extends REST_Controller {
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

        $cek = $this->mymodel->withquery("select * from ekskul_member_".$jenjang." where id_siswa = '".$id_siswa."' and id_ekskul = '".$id_ekskul."' and tahun_ajaran = '".$tahun_ajaran."' and semester = '".$semester."'", "row");

        if (!empty($cek)) {
            //File Pembayaran
            if (!empty($_FILES['file_pembayaran']['name'])) {
                $uploaddir = './uploads/ekskul_member_'.$jenjang.'/';
                $img = explode('.', $_FILES['file_pembayaran']['name']);
                $extension = end($img);
                $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_pembayaran']['name']).".".$extension;
                $uploadfile = $uploaddir.$file_name;
                $status = 0;
                if (move_uploaded_file($_FILES['file_pembayaran']['tmp_name'], $uploadfile)) {
                    $data['file_pembayaran'] = $file_name;
                    $data['payment_date'] = date('Y-m-d H:i:s');
                    $this->mymodel->update("ekskul_member_".strtolower($jenjang), $data, "id_ekskul='".$id_ekskul."' and id_siswa=",$id_siswa);
                    $msg = array('success'=>1,'message'=>'Upload Bukti Bayar Berhasil');
                }
            }
            $msg = array('status' => 200, 'message'=>'Berhasil Unggah File, Terima Kasih' ,'data'=>$data);
        }
        else{
            $msg = array('status' => 401, 'message'=>'Siswa belum melakukan pendaftaran pada eksktrakurikuler tersebut.' ,'data'=>null);
        }

        $this->response($msg);
    }
}
