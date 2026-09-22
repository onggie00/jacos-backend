<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Notification extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token'])) $token =  $headers['x-token'];

    if (!$headers['BRI-Signature']) {
      $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalid Signature');
      $this->response($msg, $status);
    }
    $data = file_get_contents('php://input');

    // $data = $this->mymodel->update2("jenjang_siswa",array("jenjang"=>"TEST"),"id_jenjang",4);

    $data_json = json_decode($data, true);
    $resp=array(
      "text"=>$data
    );
    $this->mymodel->insertid("bri_res", $resp);
    // dd(json_encode($data_json));
    if (!$data_json) {
      $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalidd Signature');
      $this->response($msg, $status);
    } else {
      // $get_setting = $this->mymodel->getall("pengaturan_akun");
      // foreach ($get_setting as $key => $value) {
      //   if(ENVIRONMENT=="development" || ENVIRONMENT=="testing"){
      //     $path="https://labschool.soldig.co.id/api/v1.0/notification";
      //   }else if(ENVIRONMENT=="production"){
      //     $path="http://admin.labschoolcibubur.sch.id/api/v1.0/notification";
      //   }
      //   if ($value->name_setting == "bri_client_id") {
      //     $client_id = $value->value;
      //   }
      //   if ($value->name_setting == "bri_client_secret") {
      //     $secret_id = $value->value;
      //   }
      // }

      // $token = $client_id;
      // $base64sign = self::BRIVAgenerateSignature($path, "POST", $token, $headers['BRI-Timestamp'], json_encode($data_json), $secret_id);

      // if(strlen($headers['BRI-Signature'])!=32){
      //   $msg = array('responseCode' => '0102', 'responseDescription' => $headers['BRI-Signature']);
      //   $this->response($msg, $status);
      // }

      // dd($base64sign);
      // if($base64sign!=$headers['BRI-Signature']){
      //   $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalid Signature');
      //   $this->response($msg, $status);
      // }

      if (!empty($data_json['brivaNo'])) {
        $date=$data_json['transactionDateTime'];
        
        $data_response=array(
          "briva_no"=>$data_json['brivaNo'],
          "bill_amount"=>$data_json['billAmount'],
          "transaction_date"=>$date,
          "journal_id"=>$data_json['journalSeq'],
          "terminal_id"=>$data_json['terminalId'],
        );
        $this->mymodel->insertid("payment_response_bri", $data_response);

        $get_transaksi = $this->mymodel->getbywhere("transaksi", "va_number = '".$data_json['brivaNo']."' and status_transaksi = ",1, "row");
        if ($get_transaksi) {
          $get_transaksi = $get_transaksi[0];
          $paymentAmount=$get_transaksi->payment_amount+$data_json['billAmount'];
          if($paymentAmount>=$get_transaksi->total_biaya){
            $this->mymodel->update2("transaksi", array("status_transaksi" => 1, "updated_at" => date("Y-m-d H:i:s")), "va_number", $data_json['brivaNo']);
            $trx_id = explode("-", $get_transaksi->no_transaksi);
            $jenis_pembayaran = $trx_id[0];
            $jenjang = $trx_id[1];
            $id_siswa = $trx_id[3];
            //update tanggal
            $this->mymodel->update("status_daftar_ulang_sd", array("tgl_bayar" => date("Y-m-d H:i:s")),"id_siswa_sd", $id_siswa);

            if ($jenjang == 'SD') {
              $tipe_siswa = "sd";
              $get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $id_siswa, "row");
            }
            $this->mymodel->update2("transaksi", array("payment_amount" => $paymentAmount), "va_number", $data_json['brivaNo']);

            if ($jenis_pembayaran == "LDUI") {
              //CETAK KARTU SEMENTARA
              $cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
              $nama_ortu = $get_siswa->nama_ibu;
              if (empty($get_siswa->nama_ibu)) {
                $nama_ortu = $get_siswa->nama_ayah;
              }
              if ($get_siswa->is_mutasi == 2) {
                $jalur = "PSB";
              } else {
                $jalur = "PSB MUTASI";
              }


              //CETAK KWITANSI
              $cetak_kwitansi = $this->cetak_kwitansi(array(
                "tipe_siswa" => $tipe_siswa,
                "jenjang" => $jenjang,
                "nama_ortu" => $nama_ortu,
                "nama_lengkap" => $get_siswa->nama_lengkap,
                "email" => $get_siswa->email,
                "va_number" => $get_transaksi->va_number,
                "total_biaya" => $get_transaksi->total_biaya,
                "total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah",
                "id_siswa" => $id_siswa,
                "no_transaksi" => $get_transaksi->no_transaksi,
                "jalur" => $jalur,
                "jenis_kwitansi" => "uang pangkal"
              ));
              $data_email = array(
                "email" => $get_siswa->email,
                "nama_lengkap" => $get_siswa->nama_lengkap,
                "tipe_pendaftaran" => "PSB " . strtoupper($tipe_siswa),
                "jenjang" => strtoupper($tipe_siswa),
                "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
                "kartu_siswa_sementara" => $tipe_siswa . '-' . $get_siswa->no_peserta . '-' . $get_siswa->nama_lengkap . '.pdf',
                "kwitansi" => $get_transaksi->no_transaksi . '-' . $get_siswa->nama_lengkap . '.pdf',
              );
              if ($get_transaksi->status_transaksi == "0") {
                $this->send_email_paid_daftar_ulang("", $data_email['email'], $data_email);
              }
              $this->mymodel->update("status_daftar_ulang_sd", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"],"tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_sd", $id_siswa);
            }
          }else{
            $this->mymodel->update2("transaksi", array("payment_amount" => $paymentAmount), "va_number", $data_json['brivaNo']);
          }
        }
        $get_transaksi_spp = $this->mymodel->getbywhere("transaksi_spp", "va_number = '".$data_json['brivaNo']."' and status_transaksi = ",1, "row");
        if($get_transaksi_spp){
          $trx_id = explode("-", $get_transaksi_spp->no_transaksi);
          $jenis_pembayaran = $trx_id[0];
          $jenjang = $trx_id[1];
          $id_siswa = $trx_id[3];
          if ($jenjang == 'SD') {
              $tipe_siswa = "sd";
              $get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $id_siswa, "row");
            }
          $total_biaya = $get_transaksi_spp->total_biaya * $get_transaksi_spp->count_bill;

          $data_transaksi = array(
            "status_transaksi" => '2',
            "updated_at" => date("Y-m-d H:i:s"),
            "file_kwitansi" => $get_transaksi_spp->no_transaksi . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf'
          );

          //UPDATE TRANSAKSI SPP
          $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, "status_transaksi = '1' and expired_datetime >= '".date("Y-m-d H:i:s")."' and va_number=", $data_json['brivaNo']);
          
           //UPDATE SPP DETAIL
           $bulan=explode(',',$get_transaksi_spp->detail_bulan);
              
           foreach($bulan as $key =>$item){
             $b=strtolower($item);
             $data_spp = array(
               $b => $data_transaksi["updated_at"],
             );
             // dd($data_spp);
             if($jenjang=='SD'){
               $id_spp = $this->mymodel->update("spp_sd", $data_spp, "id", $get_transaksi_spp->id_spp);
             }elseif($jenjang=='SMP'){
               $id_spp = $this->mymodel->update("spp_smp", $data_spp, "id", $get_transaksi_spp->id_spp);
             }elseif($jenjang=='SMA'){
               $id_spp = $this->mymodel->update("spp_sma", $data_spp, "id", $get_transaksi_spp->id_spp);
             }elseif($jenjang=='FT'){
               $id_spp = $this->mymodel->update("spp_ft", $data_spp, "id", $get_transaksi_spp->id_spp);
             }

           }

           //CETAK KWITANSI SPP
          $cetak_kwitansi = $this->cetak_kwitansi_spp(array(
            "tipe_siswa" => $tipe_siswa,
            "jenjang" => $jenjang,
            "nama_lengkap" => $get_transaksi_spp->user_name,
            "va_number" => $get_transaksi_spp->va_number,
            "total_biaya" => $total_biaya,
            "total_biaya_terbilang" => terbilang($total_biaya) . " Rupiah",
            "id_siswa" => $get_transaksi_spp->id_siswa_aktif,
            "detail_bulan" => $get_transaksi_spp->detail_bulan,
            "no_transaksi" => $get_transaksi_spp->no_transaksi,
          ));

        }
      }
    }

    $msg = array('responseCode' => "0000", 'responseDescription' => 'Success');

    $this->response($msg, $status);
  }

  private static function BRIVAgenerateToken($client_id, $secret_id, $endpoint)
  {
    $url = $endpoint;
    $data = "client_id=" . $client_id . "&client_secret=" . $secret_id;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($result, true);
    $accesstoken = $json['access_token'];

    return $accesstoken;
  }

  /* Generate signature */
  private static function BRIVAgenerateSignature($path, $verb, $token, $timestamp, $payload, $secret)
  {
    $payloads = "path=$path&verb=$verb&token=Bearer $token&timestamp=$timestamp&body=$payload";
    $signPayload = hash_hmac('sha256', $payloads, $secret, true);
    return base64_encode($signPayload);
  }

  function cetak_kartu($get = '')
  {
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

    if ($get) {
      $endpoint = site_url('/apiapp/siswa/export_pdf_siswa');
      $params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . $get['id_siswa'] . '&tipe_siswa=' . $get['tipe_siswa'];
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rs = curl_exec($ch);

    if (empty($rs)) {
      var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    //return $rs;
  }

  function cetak_kartu_siswa_sementara($get = '')
  {
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

    if ($get) {
      $endpoint = site_url('/apiapp/siswa/export_kartu_siswa_sementara');
      $params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . $get['id_siswa'] . '&tipe_siswa=' . $get['tipe_siswa'];
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rs = curl_exec($ch);

    if (empty($rs)) {
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    //return $rs;
    return true;
  }

  function cetak_kwitansi($get = '')
  {
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

    if ($get) {
      $endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi');
      //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&tipe_siswa=' . urlencode($get['tipe_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&nama_ortu=' . urlencode($get['nama_ortu']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&jalur=' . urlencode($get['jalur']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&jenis_kwitansi=' . urlencode($get['jenis_kwitansi']);
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $rs = curl_exec($ch);

    if (empty($rs)) {
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return true;
  }

  function cetak_kwitansi_spp($get = '')
  {
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

    if ($get) {
      $endpoint = site_url('/apiapp/siswa/export_pdf_kwitansi_spp');
      //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&tipe_siswa=' . urlencode($get['tipe_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&detail_bulan=' . urlencode($get['detail_bulan']);
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $rs = curl_exec($ch);

    if (empty($rs)) {
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return true;
  }

  public function send_email_file($file = "", $to = '', $data)
  {
    $to = urldecode($to);
    $mail = new PHPMailer;
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
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
    if (!empty($data['kartu_peserta'])) {
      $mail->AddAttachment('./uploads/kartu_peserta/' . $data['kartu_peserta']);
      $mail->AddAttachment('./uploads/slip_pembayaran/' . $data['slip_pembayaran']);
      //$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
    }
    if (!empty($data['kwitansi'])) {
      //$mail->AddAttachment('./uploads/kwitansi/'.$data['kwitansi']);
    }
    // Konten/isi
    $data_['to'] = $to;
    $data_['nama_lengkap'] = $data['nama_lengkap'];
    $data_['jenjang'] = $data['jenjang'];
    $data_['nama_panitia'] = $data['nama_panitia'];
    $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
    $data_['transaksi'] = $data['transaksi'];
    $mailContent = $this->load->view('template_email_pendaftaran', $data_, true);
    $mail->Body = $mailContent;
    // Menambahakn lampiran

    // Kirim email
    if (!$mail->send()) {
      //echo 'Pesan tidak dapat dikirim.';
      //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      //echo 'Pesan telah terkirim ';
    }
  }

  public function send_email_paid($file = "", $to = '', $data)
  {
    $to = urldecode($to);
    $mail = new PHPMailer;
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->SMTPDebug = 1;
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

    // Subjek email
    $mail->Subject = '[No Reply] KWITANSI DAN KARTU PESERTA SISWA LABSCHOOL CIBUBUR';

    // Mengatur format email ke HTML
    $mail->isHTML(true);
    if (!empty($data['kartu_peserta'])) {
      $mail->AddAttachment('./uploads/kartu_peserta/' . $data['kartu_peserta']);
    }
    if (!empty($data['kwitansi'])) {
      $mail->AddAttachment('./uploads/kwitansi/' . $data['kwitansi']);
    }
    // Konten/isi
    $data_['to'] = $to;
    $data_['nama_lengkap'] = $data['nama_lengkap'];
    $data_['jenjang'] = $data['jenjang'];
    $data_['email'] = $data['email'];
    $data_['nama_panitia'] = $data['nama_panitia'];
    $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
    $data_['transaksi'] = $data['transaksi'];
    $mailContent = $this->load->view('template_email_pembayaran', $data_, true);
    $mail->Body = $mailContent;
    // Menambahakn lampiran

    // Kirim email
    if (!$mail->send()) {
      //echo 'Pesan tidak dapat dikirim.';
      //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      //echo 'Pesan telah terkirim ';
    }
  }

  public function send_email_paid_daftar_ulang($file = "", $to = '', $data)
  {
    $to = urldecode($to);
    $mail = new PHPMailer;
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->SMTPDebug = 1;
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

    // Subjek email
    $mail->Subject = '[No Reply] KWITANSI DAN KARTU SISWA SEMENTARA LABSCHOOL CIBUBUR';

    // Mengatur format email ke HTML
    $mail->isHTML(true);
    if (!empty($data['kartu_siswa_sementara'])) {
      $mail->AddAttachment('./uploads/kartu_siswa_sementara/' . $data['kartu_siswa_sementara']);
    }
    if (!empty($data['kwitansi'])) {
      $mail->AddAttachment('./uploads/kwitansi/' . $data['kwitansi']);
    }
    // Konten/isi
    $data_['to'] = $to;
    $data_['nama_lengkap'] = $data['nama_lengkap'];
    $data_['jenjang'] = $data['jenjang'];
    $data_['nama_panitia'] = $data['nama_panitia'];
    $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
    $data_['email'] = $data['email'];
    $mailContent = $this->load->view('template_email_pendaftaran_ulang', $data_, true);
    $mail->Body = $mailContent;
    // Menambahakn lampiran

    // Kirim email
    if (!$mail->send()) {
      //echo 'Pesan tidak dapat dikirim.';
      //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
      //echo 'Pesan telah terkirim ';
    }
  }
}
