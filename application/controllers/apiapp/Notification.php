<?php
/**
 * ============================================================================
 *  DEPRECATED — 2024-xx
 * ----------------------------------------------------------------------------
 *  Controller ini pakai format BRIVA lama (field brivaNo, billAmount,
 *  transactionDateTime, journalSeq, terminalId). BRI sudah migrasi ke
 *  SNAP BI v3.1 — pakai controller baru:
 *    - apiapp/Bri_snap_token_get.php    (POST /snap/v1.0/access-token/b2b)
 *    - apiapp/Bri_snap_notification.php (POST /snap/v1.0/transfer-va/notify-payment-intrabank)
 *
 *  File ini DITAHAN untuk:
 *    1. Perbandingan implementasi (diff dengan controller baru).
 *    2. Fallback kalau BRI masih kirim payload legacy di sandbox lama.
 *
 *  TODO: hapus setelah BRI konfirmasi 100% pakai SNAP BI v3.1.
 * ============================================================================
 */

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
    if (isset($headers['x-token'])) $token =  $headers['x-token'];

    /*if (!$headers['BRI-Signature']) {
      $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalid Signature');
      $this->response($msg, $status);
    }*/
    $data = file_get_contents('php://input');

    // $data = $this->mymodel->update("jenjang_siswa",array("jenjang"=>"TEST"),"id_jenjang",4);

    $data_json = json_decode($data, true);
    $resp=array(
      "text"=>$data
    );
    $this->mymodel->insertid("bri_res", $resp);
    // dd(json_encode($data_json));
    if (!$data_json) {
      $msg = array('responseCode' => '0102', 'responseDescription' => 'Invalid Signature');
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
        $billAmount = filter_var($data_json['billAmount'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($billAmount === false || $billAmount <= 0) {
          return $this->response(array('responseCode' => '0102', 'responseDescription' => 'Invalid bill amount'), '400');
        }

        $this->db->trans_begin();
        $date=$data_json['transactionDateTime'];
        
        $data_response=array(
          "briva_no"=>$data_json['brivaNo'],
          "bill_amount"=>$data_json['billAmount'],
          "transaction_date"=>$date,
          "journal_id"=>$data_json['journalSeq'],
          "terminal_id"=>$data_json['terminalId'],
        );
        $ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'", "row");
        //request data ke bri untuk mendapatkan kode tagihan
        $tipe_tagihan = substr($data_json['brivaNo'], 5, 2);
        $kode_pendaftaran = substr($data_json['brivaNo'], 7, 2);
        $no_transaksi = $data_json['brivaNo'];
        $jenjang = substr($data_json['brivaNo'], 7, 2);
        if ($jenjang == "01") {
          $jenjang = "sd";
        }
        if (($tipe_tagihan == "65" || $tipe_tagihan == "75" || $tipe_tagihan == "85" || $tipe_tagihan == "95") && $kode_pendaftaran != "01" ) {
        $jenjang = substr($data_json['brivaNo'], 5, 2);
          if ($jenjang == "85") {
            $jenjang = "sd";
          }
          else if($jenjang == "65"){
            $jenjang = "smp";
          }
          else if ($jenjang == "75") {
            $jenjang = "sma";
          }
          else if ($jenjang == "95") {
            $jenjang = "ft";
          }
          //proses tagihan lain
          $get_tagihan = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, s.id_tahun_ajaran, s.id_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id where t.va_number = '". $no_transaksi."' and t.status_transaksi = 1" , "row");
          if (!empty($get_tagihan) && (float) $billAmount == (float) $get_tagihan->nominal_bayar) {
            $this->mymodel->update("transaksi_lain_".$jenjang, array("status_transaksi" => 2, 'tanggal_bayar' => date("Y-m-d H:i:s"), "file_kwitansi" => $get_tagihan->kode_tagihan . '-' . str_replace(' ', '_', $get_tagihan->nama_lengkap) . '.pdf'), "id", $get_tagihan->id);
            //cetak kwitansi hanya setelah tagihan ditemukan dan nominal cocok
            $cetak_kwitansi = $this->cetak_kwitansi_ot(array("jenjang" => $jenjang, "nama_lengkap" => $get_tagihan->nama_lengkap, "va_number" => $get_tagihan->va_number, "kode_tagihan" => $get_tagihan->kode_tagihan, "total_biaya" => $get_tagihan->nominal_bayar, "total_biaya_terbilang" => terbilang($get_tagihan->nominal_bayar) . " Rupiah", "id_siswa" => $get_tagihan->id_siswa_aktif, "jenis_kwitansi" => $get_tagihan->nama_transaksi));
          }

        }
        else{
          //$get_transaksi = $this->mymodel->getbywherelimitsort("transaksi", "va_number = '".$data_json['brivaNo']."' and status_transaksi = ",1, 0, 1, "id_transaksi", "DESC");
          $get_transaksi = $this->mymodel->withquery("select * from transaksi where va_number = '".$data_json['brivaNo']."' and status_transaksi = 0 order by id_transaksi DESC limit 1","row");
          if ($get_transaksi && (float) $billAmount == (float) $get_transaksi->total_biaya) {
            $no_peserta = "";
            $no_transaksi = $get_transaksi->no_transaksi; #$data_asli['trx_id'];
            $ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'", "row");
            if (empty($ajaran_aktif)) {
              $this->db->trans_rollback();
              return $this->response(array('responseCode' => '0102', 'responseDescription' => 'Active academic year not found'), '400');
            }
            $tahun_pelajaran = date('y', strtotime($ajaran_aktif->tanggal_mulai."+1 years")) . date("y", strtotime($ajaran_aktif->tanggal_selesai."+1 years"));

            $data_transaksi = array(
              "status_transaksi" => 1,
              "updated_at" => date("Y-m-d H:i:s")
            );
            $transaksi = $this->mymodel->update("transaksi", $data_transaksi, "id_transaksi", $get_transaksi->id_transaksi);

            $unit = "11";
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
              $tahun_ajaran_psb = $this->mymodel->withquery("select * from tahun_ajaran_psb where id = '1'", "row")->label;
              $gelombang = $this->mymodel->withquery("select * from web_periode_daftar where kelas = 'SD'", "row")->gelombang;
              $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta, "tahun_ajaran" => $tahun_ajaran_psb, "gelombang" => $gelombang), "id_siswa_sd", $cek_no_peserta->id_siswa);
            }
            if (empty($cek_no_peserta)) {
              $this->db->trans_rollback();
              return $this->response(array('responseCode' => '0102', 'responseDescription' => 'Student data not found'), '400');
            }
            $id_siswa = $cek_no_peserta->id_siswa;
            $jalur = "PSB";
            $tipe_siswa = "sd";

            //CETAK KARTU PESERTA
            $cetak_kartu = $this->cetak_kartu(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
            $cek_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime, status_transaksi from transaksi where no_transaksi = '" . $no_transaksi . "'", "row");
            if (empty($cek_transaksi)) {
              $this->db->trans_rollback();
              return $this->response(array('responseCode' => '0102', 'responseDescription' => 'Transaction data not found'), '400');
            }

            $nama_ortu = $cek_no_peserta->nama_ibu;
            if (empty($cek_no_peserta->nama_ibu)) {
              $nama_ortu = $cek_no_peserta->nama_ayah;
            }
            //CETAK KWITANSI
            $cetak_kwitansi = $this->cetak_kwitansi(array(
              "tipe_siswa" => $tipe_siswa, 
              "jenjang" => $jenjang, 
              "nama_ortu" => $nama_ortu, 
              "nama_lengkap" => $cek_no_peserta->nama_lengkap, 
              "va_number" => $cek_transaksi->va_number, 
              "total_biaya" => $cek_transaksi->total_biaya, 
              "total_biaya_terbilang" => terbilang($cek_transaksi->total_biaya) . " Rupiah", 
              "id_siswa" => $id_siswa, 
              "no_transaksi" => $get_transaksi->no_transaksi, 
              "jalur" => $jalur, "jenis_kwitansi" => "uang pendaftaran"));

            //print_r($cetak_kwitansi);
            //kirim email Kwitansi dan kartu peserta
            $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb", "id", "1","row")->label;
            $data_email = array(
              "email" => $cek_no_peserta->email,
              "nama_lengkap" => $cek_no_peserta->nama_lengkap,
              "tipe_pendaftaran" => "PSB " . strtoupper($tipe_siswa),
              "jenjang" => strtoupper($tipe_siswa),
              "transaksi" => $cek_transaksi,
              "nama_panitia" => "Panitia PSB " . strtoupper($tipe_siswa) . " Labschool Cibubur " . $tahun_ajaran,
              "kartu_peserta" => $tipe_siswa . '-' . $no_peserta . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              // "kwitansi" => $data_asli['trx_id'] . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
              "kwitansi" => $cek_transaksi->no_transaksi . '-' . $cek_no_peserta->nama_lengkap . '.pdf',
            );
            if ($cek_transaksi->status_transaksi == "0") {
              $this->send_email_paid("", $data_email['email'], $data_email);
            }
            $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
            $status = "200";
          }

          //$get_transaksi = $this->mymodel->getbywherelimitsort("transaksi", "va_number = '".$data_json['brivaNo']."' and status_transaksi = ",1, 0, 1, "id_transaksi", "DESC");
          $get_transaksi = $this->mymodel->withquery("select * from transaksi where va_number = '".$data_json['brivaNo']."' and status_transaksi = 1 order by id_transaksi DESC limit 1","row");
          if ($get_transaksi) {
            $paymentAmount=(float) $get_transaksi->payment_amount + $billAmount;
            // $paymentAmount=$data_json['billAmount'];
            if($paymentAmount>=$get_transaksi->total_biaya){
              $this->mymodel->update("transaksi", array("status_transaksi" => 1, "updated_at" => date("Y-m-d H:i:s")), "id_transaksi", $get_transaksi->id_transaksi);
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
              if ($jenis_pembayaran == "LDUI" && empty($get_siswa)) {
                $this->db->trans_rollback();
                return $this->response(array('responseCode' => '0102', 'responseDescription' => 'Student data not found'), '400');
              }
              $this->mymodel->update("transaksi", array("payment_amount" => $paymentAmount), "id_transaksi", $get_transaksi->id_transaksi);

              if ($jenis_pembayaran == "LDUI") {
                //CETAK KARTU SEMENTARA
                //$cetak_kartu_siswa_sementara = $this->cetak_kartu_siswa_sementara(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
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
                $this->mymodel->update("status_daftar_ulang_sd", array("status" => 2, "kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"],"tgl_bayar" => date("Y-m-d H:i:s")), "id_siswa_sd", $id_siswa);
                if ($get_transaksi->status_transaksi == "1") {
                  $this->send_email_paid_daftar_ulang("", $data_email['email'], $data_email);
                }
              }

            }else if($paymentAmount<$get_transaksi->total_biaya){
              $this->mymodel->update("transaksi", array("status_transaksi" => 0,"payment_amount" => $paymentAmount), "id_transaksi", $get_transaksi->id_transaksi);
            }
          }
          if ($kode_pendaftaran != "01") {
            $this->load->library('Bri_spp_matcher');
            $journalId = $this->db->escape($data_json['journalSeq']);
          $duplicate_payment = $this->mymodel->withquery("select id from payment_response_bri where journal_id = " . $journalId . " limit 1", "row");
          if (!empty($duplicate_payment)) {
            $this->db->trans_commit();
            return $this->response(array('responseCode' => '0000', 'responseDescription' => 'Payment already processed'), '200');
          }

          $candidate_rows = $this->mymodel->withquery("select * from transaksi_spp where va_number = " . $this->db->escape($data_json['brivaNo']) . " and status_transaksi = '1' and expired_datetime >= '" . date("Y-m-d H:i:s") . "' and kode_tagihan is not null and kode_tagihan != '' order by id_transaksi DESC", "result");
          $get_transaksi_spp = $this->bri_spp_matcher->resolve($candidate_rows, $billAmount);
          if (empty($get_transaksi_spp)) {
            $this->db->trans_rollback();
            return $this->response(array('responseCode' => '0102', 'responseDescription' => 'SPP invoice is missing or ambiguous'), '400');
          }

          $trx_id = explode("-", $get_transaksi_spp->no_transaksi);
          $jenis_pembayaran = $trx_id[0];
          $jenjang = strtoupper($trx_id[1]);
          $id_siswa = $trx_id[3];
          if ($jenjang == 'SD') {
              $tipe_siswa = "sd";
              $get_siswa = $this->mymodel->getbywhere("siswa_sd", "id_siswa_sd", $id_siswa, "row");
          }
          $total_biaya = $get_transaksi_spp->total_biaya * $get_transaksi_spp->count_bill;

          $data_transaksi = array(
            "status_transaksi" => '2',
            "updated_at" => date("Y-m-d H:i:s"),
            "updated_from" => "push_bri_notification",
            "file_kwitansi" => $get_transaksi_spp->no_transaksi . '-' . str_replace(' ', '_', $get_transaksi_spp->user_name) . '.pdf'
          );

          // UPDATE ONLY SELECTED INVOICE GROUP. VA IS REUSED BETWEEN MONTHS.
          $where_tagihan = "kode_tagihan = " . $this->db->escape($get_transaksi_spp->kode_tagihan) . " and va_number = " . $this->db->escape($data_json['brivaNo']) . " and status_transaksi = '1'";
          $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, $where_tagihan);

          // CEK UJIAN TERDEKAT BILA SUDAH MEMBAYAR DIPERBOLEHKAN UJIAN
          $setting_ujian = $this->mymodel->getbywhere("setting_sync_ujian", "jenjang", strtolower($jenjang), "row");
          if (empty($setting_ujian) || empty($ajaran_aktif)) {
            $this->db->trans_rollback();
            return $this->response(array('responseCode' => '0102', 'responseDescription' => 'SPP configuration not found'), '400');
          }
          $bulan_ini = formatBulan($setting_ujian->tanggal_terakhir_bayar);
          $cek_transaksi = $this->mymodel->withquery("select status_transaksi, user_name, bulan, id_siswa_aktif from transaksi_spp where id_spp = '" . $get_transaksi_spp->id_spp . "' and no_transaksi like '%" . $jenjang . "%' and status_transaksi = '2' and bulan = '" . $bulan_ini . "' and id_tahun_ajaran = '" . $ajaran_aktif->id_tahun_ajaran . "' order by id_transaksi DESC", "row");
          $get_siswa_spp = !empty($cek_transaksi) ? $this->mymodel->withquery("select nomor_peserta_ujian from siswa_" . strtolower($jenjang) . "_aktif where id_siswa_" . strtolower($jenjang) . "_aktif = '" . $cek_transaksi->id_siswa_aktif . "' ", "row") : null;
          if (!empty($cek_transaksi) && !empty($get_siswa_spp) && $cek_transaksi->status_transaksi == 2) {
            $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => $cek_transaksi->user_name, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa_spp->nomor_peserta_ujian);
          }

          // UPDATE SPP DETAIL FOR EVERY MONTH IN GROUP.
          // Guard id_siswa_aktif + id_tahun_ajaran karena id_spp tidak global unik antar jenjang.
          if (!$this->_update_spp_detail($get_transaksi_spp, $data_transaksi["updated_at"])) {
            $this->db->trans_rollback();
            return $this->response(array('responseCode' => '5000', 'responseDescription' => 'Payment detail SPP update failed'), '500');
          }

          $this->mymodel->insertid("payment_response_bri", $data_response);

          // CETAK KWITANSI SPP
          $cetak_kwitansi = $this->cetak_kwitansi_spp(array(
            "tipe_siswa" => $tipe_siswa,
            "jenjang" => $jenjang,
            "nama_lengkap" => $get_transaksi_spp->user_name,
            "va_number" => $get_transaksi_spp->va_number,
            "total_biaya" => $total_biaya,
            "total_biaya_terbilang" => terbilang($total_biaya) . " Rupiah",
            "id_siswa" => $get_transaksi_spp->id_siswa_aktif,
            "detail_bulan" => $get_transaksi_spp->detail_bulan,
            "tanggal_transaksi" => $data_transaksi["updated_at"],
            "no_transaksi" => $get_transaksi_spp->no_transaksi,
          ));

          }
        }
      }

      if ($this->db->trans_status() === false) {
        $this->db->trans_rollback();
        return $this->response(array('responseCode' => '5000', 'responseDescription' => 'Payment update failed'), '500');
      }
      $this->db->trans_commit();
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
      $url = $endpoint . '?id_siswa=' . urlencode($get['id_siswa']) . '&tipe_siswa=' . urlencode($get['tipe_siswa']) . '&jenjang=' . urlencode($get['jenjang']) . '&nama_lengkap=' . urlencode($get['nama_lengkap']) . '&total_biaya=' . urlencode($get['total_biaya']) . '&total_biaya_terbilang=' . urlencode($get['total_biaya_terbilang']) . '&va_number=' . urlencode($get['va_number']) . '&no_transaksi=' . urlencode($get['no_transaksi']) . '&detail_bulan=' . urlencode($get['detail_bulan']) . '&tanggal_transaksi=' . urlencode($get['tanggal_transaksi']);
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
    $transaction_parts = explode('-', $transaction->no_transaksi);
    $jenjang = isset($transaction_parts[1]) ? $transaction_parts[1] : '';
    $table = $this->spp_payment_detail->table_for($jenjang);
    $months = $this->spp_payment_detail->months($transaction->detail_bulan);
    if ($table === false || $months === false || empty($transaction->id_siswa_aktif) || empty($transaction->id_tahun_ajaran)) {
      return false;
    }

    $where = array(
      'id' => $transaction->id_spp,
      'id_siswa_aktif' => $transaction->id_siswa_aktif,
      'id_tahun_ajaran' => $transaction->id_tahun_ajaran
    );

    foreach ($months as $month) {
      $this->db->where($where)->update($table, array($month => $updated_at));
      $check = $this->db->select($month)->where($where)->get($table)->row();
      if (empty($check) || (string) $check->$month !== (string) $updated_at) {
        return false;
      }
    }
    return true;
  }
}

