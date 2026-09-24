<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Payment_notification extends MY_Controller
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
      if ($value->name_setting == "bni_client_id") {
        $client_id = $value->value;
      }
      if ($value->name_setting == "bni_secret_key") {
        $secret_key = $value->value;
      }
      if ($value->name_setting == "bni_prefix") {
        $prefix = $value->value;
      }
    }
    // FROM BNI

    // URL utk simulasi pembayaran: http://dev.bni-ecollection.com/dev/flagging
    $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label;
    $data = file_get_contents('php://input');

    $data_json = json_decode($data, true);
    // dd($data_json);
    $resp = array(
      "text" => $data,
      "sumber_endpoint" => "payment_notification"
    );
    $this->mymodel->insertid("bni_res", $resp);

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
          $jenjang = $trx_id[1];
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }*/
              
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }*/
              
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }*/
              
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }*/
              
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }*/
              
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
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }*/
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_sma where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_sma", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "sma";
            }
            if ($jenjang == "FT" || $jenjang == "PPSBBFT") {
              //unit code no_peserta: Jalur Prestasi (PPSBBFT) = 41, Jalur Tes (FT) = 42
              $unit = ($jenjang == "PPSBBFT") ? "41" : "42";
              $jalur = ($jenjang == "PPSBBFT") ? "PPSBB" : "PSB";
              $filter_jalur = ($jenjang == "PPSBBFT") ? " and ppsbb = '1'" : " and ppsbb != '1'";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa from siswa_ft where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != ''" . $filter_jalur . " order by id_siswa_ft DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              for ($i=0; $i < 10; $i++) { 
                $no_peserta = $tahun_pelajaran . $unit . ($no_urut+$i);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
                if (empty($cek_no)) {
                  $cek_no = null;
                  break;
                }
                else{
                  continue;
                }
              }
              /*while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              }*/
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_ft where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_ft", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "ft";
            }
            if ($jenjang == "KB") {
              //unit code no_peserta KB = 51
              $unit = "51";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_kb as id_siswa from siswa_kb where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and is_mutasi='2' order by no_peserta DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_kb where no_peserta = '".$no_peserta."'","row");

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_kb as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_kb where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_kb", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "kb";
            }
            if ($jenjang == "TK") {
              //unit code no_peserta TK = 52
              $unit = "52";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_tk as id_siswa from siswa_tk where no_peserta like '%" . $tahun_pelajaran . "%' and no_peserta != '' and is_mutasi='2' order by no_peserta DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_tk where no_peserta = '".$no_peserta."'","row");

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_tk as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_tk where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta == '') {
                $data_siswa = $this->mymodel->update("siswa_tk", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $jalur = "PSB";
              $tipe_siswa = "tk";
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
                "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . $tahun_ajaran,
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
                "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . $tahun_ajaran,
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
              } elseif ($tipe_siswa == 'kb') {
                $this->mymodel->update("status_daftar_ulang_kb", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_kb", $cek_no_peserta->id_siswa);
              } elseif ($tipe_siswa == 'tk') {
                $this->mymodel->update("status_daftar_ulang_tk", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"], "tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_tk", $cek_no_peserta->id_siswa);
              }
            } else if ($jenis_pembayaran == "SP" || $jenis_pembayaran == "SPP") {
              $get_transaksi_spp = $this->mymodel->withquery("select * from transaksi_spp where kode_tagihan = '" . $data_asli['trx_id'] . "'", "row");
              $total_biaya = $get_transaksi_spp->total_biaya * $get_transaksi_spp->count_bill;

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
                "no_transaksi" => $data_asli['trx_id']
              ));

              $data_transaksi = array(
                "status_transaksi" => '2',
                "updated_at" => date("Y-m-d H:i:s"),
                "file_kwitansi" => $data_asli['trx_id'] . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf'
              );

              //UPDATE TRANSAKSI SPP
              $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, "kode_tagihan", $data_asli['trx_id']);

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

              $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
              $status = "200";
            }
            /*else if ($jenis_pembayaran == "OT" || strpos($jenis_pembayaran, "OT") !== false) {
              //update transaksi
                $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.kode_tagihan = ". $no_transaksi , "row");
                $data_transaksi = array(
                  "status_transaksi" => 2,
                  "file_kwitansi" => $data_asli['trx_id'],
                  "updated_at" => date("Y-m-d H:i:s"),
                );
                $transaksi_update = $this->mymodel->update("transaksi_lain_".strtolower($jenjang), $data_transaksi, "kode_tagihan", $data_asli['trx_id']);

                    $nama_ortu = $cek_no_peserta->nama_ibu;
                    if (empty($cek_no_peserta->nama_ibu)) {
                      $nama_ortu = $cek_no_peserta->nama_ayah;
                    }
                    //CETAK KWITANSI
                    $cetak_kwitansi = $this->cetak_kwitansi_ot(array("jenjang" => $jenjang, "nama_lengkap" => $get_tagihan->nama_lengkap, "va_number" => $get_tagihan->va_number, "kode_tagihan" => $no_transaksi, "total_biaya" => $get_tagihan->nominal_bayar, "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", "id_siswa" => $get_tagihan->id_siswa_aktif, "jenis_kwitansi" => $get_tagihan->nama_transaksi));

              $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
              $status = "200";
            }*/
            
          } else {
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
              "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . $tahun_ajaran,
              "kartu_peserta" => $tipe_siswa . '-' . $no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              //"kwitansi" => $data_asli['trx_id'].'.pdf',
            );
            //$this->send_email_file("",$data_email['email'],$data_email);
            $msg = array('status' => 0, 'message' => 'Tagihan belum dibayar', 'data' => $data_transaksi);
            $status = "200";
          }
          //print_r($msg);
          echo '{"status":"000"}';
        }
      }
    }

    //$this->response($msg, $status);
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
