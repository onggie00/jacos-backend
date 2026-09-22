<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Pendaftaran_ft extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }

    public function cek_notelp($nohp){
          // ponytail: fix bug — default return agar nohp valid non-62/0 tidak jadi null
          $hp = trim($nohp);
          if(!preg_match("/[^+0-9]/",$hp)){
              // cek apakah no hp karakter ke 1 dan 2 adalah angka 62
          if(substr($hp, 0, 2)=="62"){
              $hp    =$hp;
          }
              // cek apakah no hp karakter ke 1 adalah angka 0
          else if(substr($hp, 0, 1)=="0"){
              $hp    ="62".substr($hp, 1);
          }
      }
      return $hp;
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

        $token_user = md5($this->post("email")." ".date("YmdHis"));
        $sekolah_asal = "";
          if ($this->post("sekolah_asal") > 0) {
            $sekolah_asal = $this->mymodel->getbywhere("list_sekolah_smp", "id_list_sekolah_smp",$this->post("sekolah_asal"),"row")->nama_sekolah;
            if(!empty($this->post("sekolah_asal_lainnya"))){
              $sekolah_asal = $this->post("sekolah_asal_lainnya");
            }
          }
          else if(!empty($this->post("sekolah_asal_lainnya"))){
            $sekolah_asal = $this->post("sekolah_asal_lainnya");
          }
          else if(empty($this->post("sekolah_asal"))){
            $sekolah_asal = null;
          }
          else{
            $sekolah_asal = $this->post("sekolah_asal");
          }
          $pekerjaan_ibu = "";
          $pekerjaan_ayah = "";
          if ($this->post("pekerjaan_ayah") > 0) {
            $pekerjaan_ayah = $this->mymodel->withquery("select * from pekerjaan_ortu where id_pekerjaan_ortu = '".$this->post("pekerjaan_ayah")."'","row")->nama_pekerjaan;
            if (!empty($this->post("pekerjaan_ayah_lainnya"))) {
              $pekerjaan_ayah = $this->post("pekerjaan_ayah_lainnya");
            }
          }
          else if(!empty($this->post("pekerjaan_ayah_lainnya"))){
            $pekerjaan_ayah = $this->post("pekerjaan_ayah_lainnya");
          }
          else{
            $pekerjaan_ayah = $this->post("pekerjaan_ayah");
          }
          if ($this->post("pekerjaan_ibu") > 0) {
            $pekerjaan_ibu = $this->mymodel->withquery("select * from pekerjaan_ortu where id_pekerjaan_ortu = '".$this->post("pekerjaan_ibu")."'","row")->nama_pekerjaan;
            if (!empty($this->post("pekerjaan_ibu_lainnya"))) {
              $pekerjaan_ibu = $this->post("pekerjaan_ibu_lainnya");
            }
          }
          else if(!empty($this->post("pekerjaan_ibu_lainnya"))){
            $pekerjaan_ibu = $this->post("pekerjaan_ibu_lainnya");
          }
          else{
            $pekerjaan_ibu = $this->post("pekerjaan_ibu");
          }
          $data = array(
            "nama_lengkap" => ucwords(strtolower($this->post("nama_lengkap"))),
            "email" => $this->post("email"),
            "token" => $token_user,
            //"nisn" => $this->post("nisn"),
            "tempat_lahir" => $this->post("tempat_lahir"),
            "tgl_lahir" => (($d = DateTime::createFromFormat('Y-m-d', $this->post("tgl_lahir"))) && $d->format('Y-m-d') === $this->post("tgl_lahir")) ? $this->post("tgl_lahir") : null,
            "jenis_kelamin" => $this->post("jenis_kelamin"),
            "agama" => $this->post("agama"),
            "nama_ibu" => $this->post("nama_ibu"),
            "pekerjaan_ibu" => $pekerjaan_ibu,
            "notelp_ibu" => $this->post("notelp_ibu"),
            "nama_ayah" => $this->post("nama_ayah"),
            "pekerjaan_ayah" => $pekerjaan_ayah,
            "notelp_ayah" => $this->post("notelp_ayah"),
            "alamat" => $this->post("alamat"),
            "kelurahan" => $this->post("kelurahan"),
            "kecamatan" => $this->post("kecamatan"),
            "kota" => $this->post("kota"),
            "provinsi" => $this->post("provinsi"),
            "kode_pos" => $this->post("kode_pos"),
            "sekolah_asal" => $sekolah_asal,
            "sumber_informasi" => $this->post("sumber_informasi"),
            "alasan_tertarik" => $this->post("alasan_tertarik"),
            "status_lulus" => 1,
            "token_expired" => date("Y-m-d H:i:s", strtotime("+3 days")),
            "peminatan_ft" => 0//$this->post("peminatan_ft")
          );

          if (!empty($this->post('nisn'))) {
            $data['nisn'] = $this->post("nisn");
          }
          if (!empty($this->post('npsn'))) {
            $data['npsn'] = $this->post("npsn");
          }

          //foto peserta
          if (!empty($_FILES['foto_peserta']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['foto_peserta']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['foto_peserta']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['foto_peserta']['tmp_name'], $uploadfile)) {
                $data['foto_peserta'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload Foto Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }
          //foto akte lahir
          if (!empty($_FILES['akte_lahir']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['akte_lahir']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['akte_lahir']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['akte_lahir']['tmp_name'], $uploadfile)) {
                $data['akte_lahir'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload File Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }
          //foto kartu keluarga
          if (!empty($_FILES['kartu_keluarga']['name'])) {
            $uploaddir = './uploads/siswa_ft/';
            $img = explode('.', $_FILES['kartu_keluarga']['name']);
            $extension = end($img);
            $file_name =  md5(date('y-m-d h:i:s').$_FILES['kartu_keluarga']['name']).".".$extension;
            $uploadfile = $uploaddir.$file_name;
            $status = 0;
              if (move_uploaded_file($_FILES['kartu_keluarga']['tmp_name'], $uploadfile)) {
                $data['kartu_keluarga'] = $file_name;
                $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
              }else{
                $msg = array('success'=>0,'message'=>'Upload File Gagal','data'=>[]);
                $this->response($msg,'200');
              }
          }

            //foto Raport 1 - 4
            if (!empty($_FILES['file_raport']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport']['tmp_name'], $uploadfile)) {
                  $file1 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 1 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 1 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport2']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport2']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport2']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport2']['tmp_name'], $uploadfile)) {
                  $file2 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 2 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 2 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport3']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport3']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport3']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport3']['tmp_name'], $uploadfile)) {
                  $file3 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 3 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 3 kosong','data'=>[]);
              $this->response($msg,'200');
            }

            if (!empty($_FILES['file_raport4']['name'])) {
              $uploaddir = './uploads/siswa_ft/raport/';
              $img = explode('.', $_FILES['file_raport4']['name']);
              $extension = end($img);
              $file_name =  md5(date('y-m-d h:i:s').$_FILES['file_raport4']['name']).".".$extension;
              $uploadfile = $uploaddir.$file_name;
              $status = 0;
                if (move_uploaded_file($_FILES['file_raport4']['tmp_name'], $uploadfile)) {
                  $file4 = $file_name;
                  $msg = array('success'=>1,'message'=>'Upload Foto Berhasil');
                }else{
                  $msg = array('success'=>0,'message'=>'File Raport 4 Gagal','data'=>[]);
                  $this->response($msg,'200');
                }
            }else{
              $msg = array('success'=>0,'message'=>'File Raport 4 kosong','data'=>[]);
              $this->response($msg,'200');
            }
        

          //cek email duplicate
          // ponytail: fix bug — tambah escape_like_str untuk mitigasi SQL injection
          $cek_email = $this->mymodel->withquery("select * from siswa_ft where email like '%".$this->db->escape_like_str($this->post("email"))."%'", "row");
          if(!empty($cek_email)){
            $msg = array('status' => 0, 'message'=>'Email "'.$this->input->post('email').'" sudah digunakan, silahkan menggunakan email lain.' ,'data'=>$cek_email, 'data_raport' => array(), 'transaksi' => array());
            $status="200";
          }
          else if (!empty($data) && empty($cek_email)) {
            $id_siswa = $this->mymodel->insertid("siswa_ft",$data);
            if (!empty($id_siswa)) {
              $data['id_siswa'] = $id_siswa;
              //isi nilai raport
              $data_raport = array(
                "id_siswa" => $id_siswa,
                "b_inggris" => $this->post("b_inggris"),
                "b_inggris2" => $this->post("b_inggris2"),
                "b_inggris3" => $this->post("b_inggris3"),
                "b_inggris4" => $this->post("b_inggris4"),
                "b_indonesia" => $this->post("b_indonesia"),
                "b_indonesia2" => $this->post("b_indonesia2"),
                "b_indonesia3" => $this->post("b_indonesia3"),
                "b_indonesia4" => $this->post("b_indonesia4"),
                "ipa" => $this->post("ipa"),
                "ipa2" => $this->post("ipa2"),
                "ipa3" => $this->post("ipa3"),
                "ipa4" => $this->post("ipa4"),
                "ips" => $this->post("ips"),
                "ips2" => $this->post("ips2"),
                "ips3" => $this->post("ips3"),
                "ips4" => $this->post("ips4"),
                "matematika" => $this->post("matematika"),
                "matematika2" => $this->post("matematika2"),
                "matematika3" => $this->post("matematika3"),
                "matematika4" => $this->post("matematika4"),
                "file_raport" => $file1,
                "file_raport2" => $file2,
                "file_raport3" => $file3,
                "file_raport4" => $file4,
              );
              $id_raport = $this->mymodel->insertid("nilai_raport_ft",$data_raport);
              //insert transaksi
              $no_transaksi = "LI-FT-".date("Ymd")."-".$id_siswa;
              //get biaya pendaftaran
              $get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran","jenjang","FRANCE TRACK","row");
              $total_biaya = $get_biaya->nominal_pendaftaran; //+ $get_biaya->nominal_daftar_ulang;
              $data_transaksi = array(
                "no_transaksi" => $no_transaksi,
                "nama_bank" => 'BNI',
                "user_email" => $this->post('email'),
                "user_name" => $this->post('nama_lengkap'),
                "user_phone" => "",
                "description" => "Tagihan Pendaftaran FT a.n ".strtoupper($this->post('nama_lengkap')),
                "id_biaya_pendaftaran" => $get_biaya->id_biaya_pendaftaran,
                "total_biaya" => $total_biaya,
                "status_transaksi" => "0",
                "created_at" => date("Y-m-d H:i:s"),
                "expired_datetime" => date("Y-m-d H:i:s", strtotime("+3 days"))
              );
              if (!empty($data_transaksi)) {
                //create billing
                $get_setting = $this->mymodel->getall("pengaturan_akun");
                foreach ($get_setting as $key => $value) {
                  if ($value->name_setting == "bni_client_id_ft") {
                    $client_id = $value->value;
                  }
                  
                  if ($value->name_setting == "bni_prefix") {
                    $prefix = $value->value;
                  }
                }
                $get_no_urut = $this->mymodel->withquery("select va_number, id_siswa_ft as id_siswa from siswa_ft where va_number like '".$prefix.$client_id.date('y', strtotime('+1 years'))."09%' and va_number != '' order by id_siswa_ft DESC","row");
                if (empty($get_no_urut)) {
                  $no_urut = "0001";
                }
                else{
                  $no_urut = (int)substr($get_no_urut->va_number, -4);
                  $no_urut = $no_urut+1;
                  $no_urut = sprintf("%04d", $no_urut);
                }
                $va_number = $prefix.$client_id.date("y", strtotime('+1 years'))."09".$no_urut;
                // $va_number='9881611399010025';
                $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $this->post('nama_lengkap'), "email" => $this->post('email'), "va_number" => $va_number ));
                // dd($payment_response);
                $increment = 2;

                if(!$payment_response['virtual_account']){
                  for ($i=2; $i < 100; $i++) { 
                    $no_urut = (int)substr($get_no_urut->va_number, -4);
                    $no_urut = $no_urut+$i;
                    $no_urut = sprintf("%04d", $no_urut);
                    $va_number = $prefix.$client_id.date("y", strtotime('+1 years'))."09".$no_urut;
                    $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $this->post('nama_lengkap'), "email" => $this->post('email'), "va_number" => $va_number ));
                    if (!empty($payment_response['virtual_account'])) {
                      break;
                    }
                  }
                  $payment_response['generated_va'] = $va_number;
                  $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>$payment_response);
                  $status="200";
                  $this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));
                  $this->response($msg,$status);
                }

                $data_transaksi['va_number'] = $payment_response['virtual_account'];

                $id_transaksi = $this->mymodel->insertid("transaksi",$data_transaksi);
                $this->mymodel->update("siswa_ft", array("no_transaksi" => $no_transaksi, "va_number" => $va_number), "id_siswa_ft", $id_siswa);

                $this->cetak_slip(array("id_siswa" => $id_siswa, "tipe_siswa" => "ft"));
                $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where id_transaksi = '".$id_transaksi."'","row");
                //kirim email slip pembayaran
                $data_email = array(
                  "email" => $this->post("email"),
                  "nama_lengkap" => $this->post("nama_lengkap"),
                  "tipe_pendaftaran" => "PSB FT",
                  "jenjang" => "FT",
                  "transaksi" => $get_transaksi,
                  "nama_panitia" => "Panitia PSB FT Labschool Cibubur ".date("Y", strtotime("+1 years"))."-".date("Y", strtotime("+2 years")),
                  "slip_pembayaran" => $no_transaksi.'-'.$this->post("nama_lengkap").'.pdf',
                );
                $this->send_email_file("",$data_email['email'],$data_email);

              }

              $msg = array('status' => 1, 'message'=>'Berhasil melakukan pendaftaran' ,'data'=>$data, 'data_raport' => $data_raport, 'transaksi' => $data_transaksi);
              $status="200";
            }
            else{
              $msg = array('status' => 0, 'message'=>'Form tidak diisi sesuai permintaan' ,'data'=>array(), 'data_raport' => array(), 'transaksi' => array());
              $status="200";
            }
          }
          else{
            $msg = array('status' => 0, 'message'=>'Form tidak diisi sesuai permintaan' ,'data'=>array(), 'data_raport' => array(), 'transaksi' => array());
            $status="200";
          }

        $this->response($msg,$status);
    }

    function cetak_slip($get = '') {
      //$usecookie = __DIR__ . "/cookie.txt";
      $header[] = 'Content-Type: application/json';
      $header[] = "Accept-Encoding: gzip, deflate";
      $header[] = "Cache-Control: max-age=0";
      $header[] = "Connection: keep-alive";
      $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

      $ch = curl_init();
      //curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_VERBOSE, false);
      // curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

      if ($get)
      {
        $endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran');
        $params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
        $url = $endpoint . '?' . http_build_query($params);
        curl_setopt($ch, CURLOPT_URL, $url);
      }
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $rs = curl_exec($ch);

      if(empty($rs)){
        var_dump($rs, curl_error($ch));
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      //return $rs;
    }

    function create_billing($production, $total, $no_transaksi, $data_user){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      foreach ($get_setting as $key => $value) {
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
        if ($value->name_setting == "bni_prefix") {
          $prefix = $value->value;
        }
        if ($production == "production") {
          if ($value->name_setting == "bni_api_prod_url") {
            $url = $value->value;
          }
        }
        else if ($production == "development" || $production == "testing"){
          if ($value->name_setting == "bni_api_dev_url") {
            $url = $value->value;
          }
        }
      }

      $get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
      foreach ($get_pengaturan_masa_aktif as $key => $item) {
        if($item->label=='pendaftaran'){
          if($item->tipe_date=='day'){
            $date_va=($item->value*24) * 3600;
          }else{
            $date_va=($item->value) * 3600;
          }
        }
      }

      $data_asli = array(
        'type' => "createbilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
        'trx_amount' => $total,
        'billing_type' => 'c',
        'datetime_expired' => date('c', time() + $date_va), // billing will be expired in 6 hours
        'virtual_account' => $data_user['va_number'],
        'customer_name' => $data_user['nama'],
        'customer_email' => $data_user['email'],
        //'customer_phone' => $data_user['notelp'],
      );

      $hashed_string = BniEnc::encrypt(
        $data_asli,
        $client_id,
        $secret_key
      );

      $data = array(
        'client_id' => $client_id,
        'data' => $hashed_string,
      );

      $response = $this->get_content($url, json_encode($data));
      $response_json = json_decode($response, true);

      if ($response_json['status'] !== '000') {
        // handling jika gagal
        return($response_json);
      }
      else {
        $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
        // $data_response will contains something like this: 
        // array(
        //  'virtual_account' => 'xxxxx',
        //  'trx_id' => 'xxx',
        // );
        //var_dump($data_response);
        return($data_response);
      }
    }

    function get_content($url, $post = '') {
      //$usecookie = __DIR__ . "/cookie.txt";
      $header[] = 'Content-Type: application/json';
      $header[] = "Accept-Encoding: gzip, deflate";
      $header[] = "Cache-Control: max-age=0";
      $header[] = "Connection: keep-alive";
      $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_VERBOSE, false);
      // curl_setopt($ch, CURLOPT_NOBODY, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_ENCODING, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

      if ($post)
      {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      }

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $rs = curl_exec($ch);

      if(empty($rs)){
        var_dump($rs, curl_error($ch));
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      return $rs;
    }

    public function send_email_file($file="",$to='',$data)
    {
      $to = urldecode($to);
      $mail = new PHPMailer;
      // Konfigurasi SMTP
      $mail->isSMTP();
      $mail->SMTPDebug =0;
      // $mail->Host = 'mail.namagz.com';
      $mail->Host = 'smtp.office365.com';
      $mail->SMTPOptions = array(
         'ssl' => array(
           'verify_peer' => false,
           'verify_peer_name' => false,
           'allow_self_signed' => true
          )
      );
      $mail->SMTPAuth = true;
      $mail->Username = 'noreply@labschoolcibubur.sch.id';
      $mail->Password = ''; // [JACOS] TODO(manual): password SMTP Jacos — kredensial lama dihapus
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
      $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');

      // Menambahkan penerima
      $mail->addAddress($to);

      // Menambahkan beberapa penerima


      // Subjek email
      $mail->Subject = '[No Reply] SLIP PEMBAYARAN PENDAFTARAN SISWA BARU';

      // Mengatur format email ke HTML
      $mail->isHTML(true);
      //$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
      //$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
      if (!empty($data['slip_pembayaran'])) {
        $mail->AddAttachment('./uploads/slip_pembayaran/'.$data['slip_pembayaran']);
        //$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
      }
      // Konten/isi
       $data_['to'] = $to;
       $data_['nama_lengkap'] = $data['nama_lengkap'];
       $data_['jenjang'] = $data['jenjang'];
       $data_['nama_panitia'] = $data['nama_panitia'];
       $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
       $data_['transaksi'] = $data['transaksi'];
       $mailContent = $this->load->view('template_email_pendaftaran',$data_,true);
       $mail->Body = $mailContent;
      // Menambahakn lampiran

      // Kirim email
      if(!$mail->send()){
          //echo 'Pesan tidak dapat dikirim.';
          //echo 'Mailer Error: ' . $mail->ErrorInfo;
      }else{
          //echo 'Pesan telah terkirim ';
      }
    }
}
