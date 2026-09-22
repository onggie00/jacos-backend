<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Payment_notification_tagihan_bni extends MY_Controller
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
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    $this->load->library('BniEnc');
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    foreach ($get_setting as $key => $value) {
      if ($value->name_setting == "bni_client_id_spp") {
        $client_id = $value->value;
      }
      if ($value->name_setting == "bni_secret_key_spp") {
        $secret_key = $value->value;
      }
      if ($value->name_setting == "bni_prefix") {
        $prefix = $value->value;
      }
    }
    // FROM BNI

    // URL utk simulasi pembayaran: http://dev.bni-ecollection.com/dev/flagging

    $data = file_get_contents('php://input');

    $data_json = json_decode($data, true);
    // dd($data_json);
    $resp = array(
      "text" => $data
    );
    $this->mymodel->insertid("bri_res", $resp);

    if (!$data_json) {
      // handling orang iseng
      echo '{"status":"999","message":"jangan iseng :D"}';
    } else {
      if ($data_json['client_id'] === $client_id) {
        //$data_asli = $data_json['data'];
        $data_asli = BniEnc::decrypt(
          $data_json['data'],
          $client_id,
          $secret_key
        );
        //print_r($data_asli);
        if (!$data_asli) {
          // handling jika waktu server salah/tdk sesuai atau secret key salah
          //echo '{"status":"999","message":"waktu server tidak sesuai NTP atau secret key salah."}';
          $msg = array('status' => 0, 'message' => 'waktu server tidak sesuai NTP atau secret key salah.', 'data' => array());
          $status = "200";
        } else {
          $data_response = array(
            "payment_ntb" => $data_asli['payment_ntb'],
            "trx_id" => $data_asli['trx_id']
          );
          $this->mymodel->insertid("payment_response_bni", $data_response);

          //bila dibayar maka akan generate payment_ntb
          $no_peserta = "";
          $no_transaksi = $data_asli['trx_id'];
          
          $unit = "";
          $trx_id = explode("-", $no_transaksi);
          $jenis_pembayaran = $trx_id[0];
          $jenjang = substr($trx_id[0],10,4);
          $no_urut = "";
          $tipe_siswa = "";
          $id_siswa = "";

          if (!empty($data_asli['payment_ntb'])) {
            if (strpos($jenis_pembayaran, "OT") !== false) {
              //UNTUK SISWA BARU
              //update transaksi
              $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.kode_tagihan = ". $no_transaksi , "row");
              $data_transaksi = array(
                "status_transaksi" => 2,
                "updated_at" => date("Y-m-d H:i:s"),
                "tanggal_bayar" => date("Y-m-d H:i:s"),
              );
              $transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $data_asli['trx_id']);

              $nama_ortu = $cek_no_peserta->nama_ibu;
              if (empty($cek_no_peserta->nama_ibu)) {
                $nama_ortu = $cek_no_peserta->nama_ayah;
              }
              //CETAK KWITANSI
              $cetak_kwitansi = $this->cetak_kwitansi(array("jenjang" => $jenjang, "nama_lengkap" => $get_tagihan->nama_lengkap, "va_number" => $get_tagihan->va_number, "kode_tagihan" => $no_transaksi, "total_biaya" => $get_tagihan->nominal_bayar, "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", "id_siswa" => $get_tagihan->id_siswa_aktif, "jenis_kwitansi" => $get_tagihan->nama_transaksi));

              //print_r($cetak_kwitansi);
              $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
              $status = "200";
            }
          } else {
            $data_transaksi = array(
              "status_transaksi" => 0,
              "updated_at" => date("Y-m-d H:i:s"),
            );
            $transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $data_asli['trx_id']);
            $msg = array('status' => 0, 'message' => 'Tagihan belum dibayar', 'data' => $data_transaksi);
            $status = "200";
          }
          //print_r($msg);
          echo '{"status":"000"}';
        }
      }
    }

    $this->response($msg, $status);
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
      $endpoint = site_url('/apiapp/tagihan_lain/export_pdf_kwitansi');
      //$params = array('id_siswa' => $get['id_siswa'], 'tipe_siswa' => $get['tipe_siswa']);
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
        '&jenjang=' . urlencode($get['jenjang']) .
        '&nama_lengkap=' . urlencode($get['nama_lengkap']) .
        '&total_biaya=' . urlencode($get['total_biaya']) .
        '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
        '&va_number=' . urlencode($get['va_number']) .
        '&no_transaksi=' . urlencode($get['no_transaksi']);
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
    return $rs;
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
    $mail->Subject = '[No Reply] KWITANSI LABSCHOOL CIBUBUR';

    // Mengatur format email ke HTML
    $mail->isHTML(true);
    if (!empty($data['kartu_peserta'])) {
      $mail->AddAttachment('./uploads/tagihan_lain/' . $data['kartu_peserta']);
    }
    if (!empty($data['kwitansi'])) {
      $mail->AddAttachment('./uploads/tagihan_lain/' . $data['kwitansi']);
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

}
