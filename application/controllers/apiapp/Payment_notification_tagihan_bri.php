<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Payment_notification_tagihan_bri extends MY_Controller
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

    /*if (!$headers['BRI-Signature']) {
      $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalid Signature');
      $this->response($msg, $status);
    }*/
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
      if (!empty($data_json['brivaNo'])) {
        $date=$data_json['transactionDateTime'];
        $trx_id = explode("-", $data_json['brivaNo']);
        $jenis_pembayaran = $trx_id[0];
        $jenjang = substr($trx_id[0],10,4);
        $no_transaksi = $data['brivaNo'];
        $data_response=array(
          "briva_no"=>$data_json['brivaNo'],
          "bill_amount"=>$data_json['billAmount'],
          "transaction_date"=>$date,
          "journal_id"=>$data_json['journalSeq'],
          "terminal_id"=>$data_json['terminalId'],
        );
        $this->mymodel->insertid("payment_response_bri", $data_response);

        $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.kode_tagihan = ". $no_transaksi , "row");

        if ($get_tagihan) {
          $no_peserta = "";
          $data_transaksi = array(
            "status_transaksi" => 2,
            "updated_at" => date("Y-m-d H:i:s"),
          );
          $transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $no_transaksi);
          
          //CETAK KWITANSI
          $cetak_kwitansi = $this->cetak_kwitansi(array(
            "jenjang" => $jenjang, 
            "nama_lengkap" => $cek_no_peserta->nama_lengkap, 
            "va_number" => $get_tagihan->va_number, 
            "total_biaya" => $get_tagihan->nominal_bayar, 
            "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", 
            "id_siswa" => $get_tagihan->id_siswa_aktif, 
            "no_transaksi" => $no_transaksi, 
            "jenis_kwitansi" => $get_tagihan->nama_transaksi));

          //print_r($cetak_kwitansi);
          $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
          $status = "200";
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
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&jenis_kwitansi=' . urlencode($get['jenis_kwitansi']);
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

