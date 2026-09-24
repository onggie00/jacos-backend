<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Payment_notification_spp extends MY_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->library('Spp_payment_detail');
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
      "text" => $data,
      "sumber_endpoint" => "payment_notification_spp"
    );
    $this->mymodel->insertid("bni_res", $resp);

    if (!$data_json) {
      // handling orang iseng
      echo '{"status":"999","message":"jangan iseng :D"}';
      exit;
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
          $ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'", "row");
          $tahun_pelajaran = date('y', strtotime($ajaran_aktif->tanggal_mulai."+1 years")) . date("y", strtotime($ajaran_aktif->tanggal_selesai."+1 years"));
          $unit = "";
          $trx_id = explode("-", $no_transaksi);
          $jenis_pembayaran = $trx_id[0];
          $jenjang = substr($trx_id[0],10,4);
          $no_urut = "";
          $tipe_siswa = "";
          $id_siswa = "";
          if ($jenis_pembayaran == "LI" || $jenis_pembayaran == "LDUI") {
            if ($jenjang == "SD") {
              $unit = "12";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and is_mutasi='2' order by no_peserta DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                // $no_urut = $this->mymodel->withquery("select count(nama_lengkap) from siswa_sd WHERE no_peserta like '%".$tahun_pelajaran.$unit."%'", "row");
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sd where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "id_siswa_sd", $cek_no_peserta->id_siswa);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "sd";
            }
            if ($jenjang == "SDM") {
              $unit = "13";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and is_mutasi = '1' order by id_siswa_sd DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sd where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB MUTASI";
              $tipe_siswa = "sd";
            }
            if ($jenjang == "PPSBBSMP") {
              $unit = "21";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and ppsbb = '1' order by id_siswa_smp DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_smp where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_smp", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PPSBB";
              $tipe_siswa = "smp";
            }
            if ($jenjang == "PSBSMP") {
              $unit = "22";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and ppsbb = '2' order by id_siswa_smp DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_smp where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_smp", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "smp";
            }
            if ($jenjang == "PPSBBSMA") {
              $unit = "31";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa from siswa_sma where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and ppsbb = '1' order by id_siswa_sma DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sma where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_sma", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PPSBB";
              $tipe_siswa = "sma";
            }
            if ($jenjang == "PSBSMA") {
              $unit = "32";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa from siswa_sma where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and ppsbb = '2' order by id_siswa_sma DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sma where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_sma", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "sma";
            }
            if ($jenjang == "FT") {
              $unit = "40";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa from siswa_ft where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' order by id_siswa_ft DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_ft where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_ft", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "ft";
            }
          }

          if (!empty($data_asli['payment_ntb'])) {
            if ($jenis_pembayaran == "LI") {
              //UNTUK SISWA BARU
              //generate kartu peserta
              $get_transaksi_status = $this->mymodel->withquery("select status_transaksi from transaksi where no_transaksi = '" . $data_asli['trx_id'] . "'", "row");
              $data_transaksi = array(
                "status_transaksi" => 1,
                "updated_at" => date("Y-m-d H:i:s"),
              );
              $transaksi = $this->mymodel->update("transaksi", $data_transaksi, "no_transaksi", $no_transaksi);

              //CETAK KARTU PESERTA
              $cetak_kartu = $this->cetak_kartu(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
              $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where no_transaksi = '" . $data_asli['trx_id'] . "'", "row");

              $nama_ortu = $cek_no_peserta->nama_ibu;
              if (empty($cek_no_peserta->nama_ibu)) {
                $nama_ortu = $cek_no_peserta->nama_ayah;
              }
              //CETAK KWITANSI
              $cetak_kwitansi = $this->cetak_kwitansi(array("tipe_siswa" => $tipe_siswa, "jenjang" => $jenjang, "nama_ortu" => $nama_ortu, "nama_lengkap" => $cek_no_peserta->nama_lengkap, "va_number" => $get_transaksi->va_number, "total_biaya" => $get_transaksi->total_biaya, "total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah", "id_siswa" => $id_siswa, "no_transaksi" => $data_asli['trx_id'], "jalur" => $jalur, "jenis_kwitansi" => "uang pendaftaran"));

              //print_r($cetak_kwitansi);
              //kirim email Kwitansi dan kartu peserta
              $data_email = array(
                "email" => $cek_no_peserta->email,
                "nama_lengkap" => $cek_no_peserta->nama_lengkap,
                "tipe_pendaftaran" => "PSB " . strtoupper($tipe_siswa),
                "jenjang" => strtoupper($tipe_siswa),
                "transaksi" => $get_transaksi,
                "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
                "kartu_peserta" => $tipe_siswa . '-' . $no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
                "kwitansi" => $data_asli['trx_id'] . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              );
              if ($get_transaksi_status->status_transaksi == "0") {
                $this->send_email_paid("", $data_email['email'], $data_email);
              }
              $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
              $status = "200";
            } else if ($jenis_pembayaran == "LDUI") {
              //UNTUK DAFTAR ULANG
              $get_transaksi_status = $this->mymodel->withquery("select status_transaksi from transaksi where no_transaksi = '" . $data_asli['trx_id'] . "'", "row");
              $data_transaksi = array(
                "status_transaksi" => 1,
                "updated_at" => date("Y-m-d H:i:s"),
              );
              $transaksi = $this->mymodel->update("transaksi", $data_transaksi, "no_transaksi", $no_transaksi);
              //CETAK KARTU SEMENTARA
              $cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
              $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where no_transaksi = '" . $data_asli['trx_id'] . "'", "row");
              $nama_ortu = $cek_no_peserta->nama_ibu;
              if (empty($cek_no_peserta->nama_ibu)) {
                $nama_ortu = $cek_no_peserta->nama_ayah;
              }
              //CETAK KWITANSI
              $cetak_kwitansi = $this->cetak_kwitansi(array("tipe_siswa" => $tipe_siswa, "jenjang" => $jenjang, "nama_ortu" => $nama_ortu, "nama_lengkap" => $cek_no_peserta->nama_lengkap, "email" => $cek_no_peserta->email, "va_number" => $get_transaksi->va_number, "total_biaya" => $get_transaksi->total_biaya, "total_biaya_terbilang" => terbilang($get_transaksi->total_biaya) . " Rupiah", "id_siswa" => $id_siswa, "no_transaksi" => $data_asli['trx_id'], "jalur" => $jalur, "jenis_kwitansi" => "uang pangkal"));
              $data_email = array(
                "email" => $cek_no_peserta->email,
                "nama_lengkap" => $cek_no_peserta->nama_lengkap,
                "tipe_pendaftaran" => "PSB " . strtoupper($tipe_siswa),
                "jenjang" => strtoupper($tipe_siswa),
                "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
                "kartu_siswa_sementara" => $tipe_siswa . '-' . $cek_no_peserta->no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
                "kwitansi" => $data_asli['trx_id'] . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              );
              if ($get_transaksi_status->status_transaksi == "0") {
                $this->send_email_paid_daftar_ulang("", $data_email['email'], $data_email);
              }
              if ($tipe_siswa == 'sd') {
                $this->mymodel->update("status_daftar_ulang_sd", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_sd", $cek_no_peserta->id_siswa);
              } elseif ($tipe_siswa == 'ft') {
                $this->mymodel->update("status_daftar_ulang_ft", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_ft", $cek_no_peserta->id_siswa);
              } elseif ($tipe_siswa == 'smp') {
                $this->mymodel->update("status_daftar_ulang_smp", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_smp", $cek_no_peserta->id_siswa);
              } elseif ($tipe_siswa == 'sma') {
                $this->mymodel->update("status_daftar_ulang_sma", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_sma", $cek_no_peserta->id_siswa);
              }
            } 
            else if ($jenis_pembayaran == "SP" || $jenis_pembayaran == "SPP" || strpos($jenis_pembayaran, "SP") !== false) {
              // 1 row transaksi_spp = 1 bulan; satu kode_tagihan bisa mencakup banyak bulan lintas tahun ajaran (bayar sampai lulus)
              $get_transaksi_spp = $this->mymodel->withquery("select * from transaksi_spp where kode_tagihan = '" . $data_asli['trx_id'] . "' and status_transaksi = '1'", "result");
              $transaksi_pertama = !empty($get_transaksi_spp) ? $get_transaksi_spp[0] : null;
              $total_biaya = 0;
              foreach ($get_transaksi_spp as $t) {
                $total_biaya += (float)$t->total_biaya;
              }

              // ponytail: guard false-lunas — dulu push BNI dilunaskan tanpa cek status/nominal (push replay/iseng jadi lunas palsu)
              if (empty($transaksi_pertama) || (isset($data_asli['payment_amount']) && (float)$data_asli['payment_amount'] != $total_biaya)) {
                $msg = array('status' => 0, 'message' => 'Tagihan SPP tidak ditemukan / nominal pembayaran tidak sesuai', 'data' => array());
                $status = "200";
                unset($msg['data']);
                echo '{"status":"000"}';
                return;
              }
              $first_transaction_parts = explode('-', $transaksi_pertama->no_transaksi);
              $jenjang = isset($first_transaction_parts[1]) ? strtoupper($first_transaction_parts[1]) : '';
              if ($this->spp_payment_detail->table_for($jenjang) === false) {
                echo '{"status":"000"}';
                return;
              }

              //CETAK KWITANSI SPP (total gabungan lintas tahun ajaran)
              $cetak_kwitansi = $this->cetak_kwitansi_spp(array(
                "tipe_siswa" => $tipe_siswa,
                "jenjang" => $jenjang,
                "nama_lengkap" => $transaksi_pertama->user_name,
                "va_number" => $transaksi_pertama->va_number,
                "total_biaya" => $total_biaya,
                "total_biaya_terbilang" => terbilang($total_biaya) . " Rupiah",
                "id_siswa" => $transaksi_pertama->id_siswa_aktif,
                "detail_bulan" => $transaksi_pertama->detail_bulan,
                "no_transaksi" => $data_asli['trx_id']
              ));

              $data_transaksi = array(
                "status_transaksi" => '2',
                "updated_at" => date("Y-m-d H:i:s"),
                "updated_from" => "push_bni_payment_notification_spp",
                "file_kwitansi" => $data_asli['trx_id'] . '-' . str_replace(' ', '_', $transaksi_pertama->user_name) . '.pdf'
              );

              // Update detail dulu. Jika target jenjang/siswa/TA tidak cocok, rollback.
              $this->db->trans_begin();
              foreach ($get_transaksi_spp as $t) {
                if (!$this->_update_spp_detail($t, $data_transaksi["updated_at"])) {
                  $this->db->trans_rollback();
                  echo '{"status":"000"}';
                  return;
                }
              }

              // UPDATE TRANSAKSI SPP hanya row yang masih menunggu pembayaran.
              $where_kode_tagihan = "kode_tagihan = " . $this->db->escape($data_asli['trx_id']) . " and status_transaksi = '1'";
              $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, $where_kode_tagihan);
              if ($transaksi < 1 || $this->db->trans_status() === false) {
                $this->db->trans_rollback();
                echo '{"status":"000"}';
                return;
              }
              $this->db->trans_commit();

              //CEK UJIAN TERDEKAT BILA SUDAH MEMBAYAR DIPERBOLEHKAN UJIAN
              //$bulan_ini = date("Y-m-d", strtotime("-1 months"));//formatBulan(date("n", strtotime("-1 months")));
              $row_setting_sync = $this->mymodel->getbywhere("setting_sync_ujian","jenjang",strtolower($jenjang),"row");
              $bulan_ini = (!empty($row_setting_sync) && !empty($row_setting_sync->tanggal_terakhir_bayar)) ? $row_setting_sync->tanggal_terakhir_bayar : date("Y-m-d", strtotime("-1 months"));
              $bulan_ini = formatBulan($bulan_ini);
              // pakai row bulan milik tahun ajaran aktif (tagihan bisa lintas tahun ajaran)
              $row_ujian = null;
              foreach ($get_transaksi_spp as $t) {
                if ($t->id_tahun_ajaran == $ajaran_aktif->id_tahun_ajaran) {
                  $row_ujian = $t;
                  break;
                }
              }
              if (empty($row_ujian)) {
                $row_ujian = $transaksi_pertama;
              }
              $get_transaksi = $this->mymodel->withquery("select status_transaksi, user_name ,bulan, id_siswa_aktif from transaksi_spp where id_spp = '".$row_ujian->id_spp."' and no_transaksi like '%".$jenjang."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_transaksi DESC","row");
              $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$get_transaksi->id_siswa_aktif."' ","row");
              if ($get_transaksi->status_transaksi == 2) {
                $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => $get_transaksi->user_name, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
              }

              $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
              $status = "200";
            }
            else if ($jenis_pembayaran == "OT" || strpos($jenis_pembayaran, "OT") !== false) {
              //update transaksi
              if (empty($jenjang)) {
                $trx_id = explode("-", $no_transaksi);
                $jenjang = $trx_id[1];
              }
              $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.kode_tagihan = '". $no_transaksi . "'" , "row");
              $data_transaksi = array(
                "status_transaksi" => 2,
                "updated_at" => date("Y-m-d H:i:s"),
                "tanggal_bayar" => date("Y-m-d H:i:s"),
                "file_kwitansi" => $get_tagihan->kode_tagihan . '-' . str_replace(' ', '_', $get_tagihan->nama_lengkap) . '.pdf'
              );
              $transaksi = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $data_asli['trx_id']);

              //CETAK KWITANSI
              $cetak_kwitansi = $this->cetak_kwitansi_ot(array("jenjang" => $jenjang, "nama_lengkap" => $get_tagihan->nama_lengkap, "va_number" => $get_tagihan->va_number, "kode_tagihan" => $get_tagihan->kode_tagihan, "total_biaya" => $get_tagihan->nominal_bayar, "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", "id_siswa" => $get_tagihan->id_siswa_aktif, "jenis_kwitansi" => $get_tagihan->nama_transaksi));
            }
            $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
            $status = "200";
          }
          else {
            $data_transaksi = array(
              "status_transaksi" => 0,
              "updated_at" => date("Y-m-d H:i:s"),
            );
            $transaksi = $this->mymodel->update("transaksi", $data_transaksi, "no_transaksi", $data_asli['trx_id']);
            //kirim email slip pembayaran
            $data_email = array(
              "email" => $cek_no_peserta->email,
              "nama_lengkap" => $cek_no_peserta->nama_lengkap,
              "tipe_pendaftaran" => "PSB " . strtoupper($tipe_siswa),
              "jenjang" => strtoupper($tipe_siswa),
              "transaksi" => $get_transaksi,
              "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . date("Y", strtotime("+1 years")) . "-" . date("Y", strtotime("+2 years")),
              "kartu_peserta" => $tipe_siswa . '-' . $no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              //"kwitansi" => $data_asli['trx_id'].'.pdf',
            );
            //$this->send_email_file("",$data_email['email'],$data_email);
            $msg = array('status' => 0, 'message' => 'Tagihan belum dibayar', 'data' => $data_transaksi);
            $status = "200";
          }
          //print_r($msg);
        }
      }
    }

    // ponytail: response standar BNI — bank cuma butuh status 000, raw request tetap tercatat di bni_res
    echo '{"status":"000"}';
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
      var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    //return $rs;
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
      var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return $rs;
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
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) .
        '&tipe_siswa=' . urlencode($get['tipe_siswa']) .
        '&jenjang=' . urlencode($get['jenjang']) .
        '&nama_lengkap=' . urlencode($get['nama_lengkap']) .
        '&total_biaya=' . urlencode($get['total_biaya']) .
        '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) .
        '&va_number=' . urlencode($get['va_number']) .
        '&detail_bulan=' . urlencode($get['detail_bulan']) .
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

  function cetak_kwitansi_ot($get = '')
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
        '&no_transaksi=' . urlencode($get['kode_tagihan']);
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

  private function _update_spp_detail($transaction, $updated_at)
  {
    $parts = explode('-', $transaction->no_transaksi);
    $jenjang = isset($parts[1]) ? $parts[1] : '';
    $table = $this->spp_payment_detail->table_for($jenjang);
    $detail_bulan = !empty($transaction->detail_bulan) ? $transaction->detail_bulan : $transaction->bulan;
    $months = $this->spp_payment_detail->months($detail_bulan);
    if ($table === false || $months === false || empty($transaction->id_siswa_aktif) || empty($transaction->id_tahun_ajaran)) {
      return false;
    }

    $where = array(
      'id' => $transaction->id_spp,
      'id_siswa_aktif' => $transaction->id_siswa_aktif,
      'id_tahun_ajaran' => $transaction->id_tahun_ajaran
    );

    // Satu row transaksi mewakili satu bulan; jangan menulis bulan lain dari detail_bulan.
    $month = strtolower(trim($transaction->bulan));
    if ($month == '' || !in_array($month, $months)) {
      return false;
    }

    $row = $this->db->select($month)->where($where)->get($table)->row();
    if (empty($row)) {
      return false;
    }

    // Selalu overwrite cell bulan. Cell terisi lama (dari proses sebelum status lunas
    // direset oleh Create_tagihan) tidak boleh menolak push pembayaran yang valid;
    // penentu lunas adalah status_transaksi = 1 pada transaksi_spp + guard nominal di branch SP.
    $this->db->where($where)->update($table, array($month => $updated_at));

    $check = $this->db->select($month)->where($where)->get($table)->row();
    return !empty($check) && (string) $check->$month === (string) $updated_at;
  }
}
