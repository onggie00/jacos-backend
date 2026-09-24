<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Cek_data extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
  }
  public function index_post()
  {
    $status = "";
    $token = "";
    $headers = array();
    foreach (getallheaders() as $name => $value) {
      $headers[$name] = $value;
    }
    if (isset($headers['x-token']))
      $token =  $headers['x-token'];

    if (isset($headers['user_role']))
      $role =  $headers['user_role'];

    $email = (!empty($this->post('email'))) ? $this->post('email') : null;
    $nama_lengkap = (!empty($this->post('nama_lengkap'))) ? $this->post('nama_lengkap') : null;
    $jenjang = $this->post('jenjang');
    $data = null;


    //cek data urut ft, sma, smp, sd

    //ft
    if ($jenjang=="ft" && !empty($email) && !empty($nama_lengkap)) {
      $data = $this->mymodel->withquery("select s.id_siswa_ft as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta from siswa_ft s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and nama_lengkap like '%" . $this->db->escape_like_str(str_replace("'", "\'", $nama_lengkap)) . "%' and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "ft";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$data->no_transaksi."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_ft", "id_siswa_ft", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'ft' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    //sma
    if ($jenjang=="sma" && !empty($email) && !empty($nama_lengkap) ) {
      $data = $this->mymodel->withquery("select s.id_siswa_sma as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta, s.ppsbb as is_ppsbb from siswa_sma s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and nama_lengkap like '%" . $this->db->escape_like_str(str_replace("'", "\'", $nama_lengkap)) . "%' and is_show = 1 and is_ft = 0", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "sma";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$data->no_transaksi."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sma", "id_siswa_sma", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sma' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    //smp
    if ($jenjang=="smp" && !empty($email) && !empty($nama_lengkap)) {
      $data = $this->mymodel->withquery("select s.id_siswa_smp as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta, s.ppsbb as is_ppsbb from siswa_smp s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and nama_lengkap like '%" . $this->db->escape_like_str($nama_lengkap) . "%' and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "smp";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$data->no_transaksi."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_smp", "id_siswa_smp", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'smp' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    //sd
    if ($jenjang=="sd" && !empty($email) && !empty($nama_lengkap)) {
      $data = $this->mymodel->withquery("select s.id_siswa_sd as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta, concat('2') as is_ppsbb from siswa_sd s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and nama_lengkap like '%" . $this->db->escape_like_str(str_replace("'", "\'", $nama_lengkap)) . "%' and is_show = 1", "row");
      //echo $this->db->last_query();
      if (!empty($data)) {
        $data->tipe_siswa = "sd";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".str_replace("'", "\'", $data->no_transaksi)."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_siswa_sd", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          // $daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sd","id_siswa_sd",$data->id_siswa,"row");
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sd' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          // $daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sd","id_siswa_sd",$data->id_siswa,"row");
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          // $data->status_transaksi = "Menunggu Aktivasi VA Daftar Ulang";
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    //kb
    if ($jenjang=="kb" && !empty($email) && !empty($nama_lengkap)) {
      $data = $this->mymodel->withquery("select s.id_siswa_kb as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta, concat('2') as is_ppsbb from siswa_kb s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%'
       and nama_lengkap like '%" . $this->db->escape_like_str(str_replace("'", "\\'", $nama_lengkap)) . "%'
       and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "kb";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".str_replace("'", "\\'", $data->no_transaksi)."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_kb", "id_siswa_kb", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'kb' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    //tk
    if ($jenjang=="tk" && !empty($email) && !empty($nama_lengkap)) {
      $data = $this->mymodel->withquery("select s.id_siswa_tk as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta, s.foto_peserta, concat('2') as is_ppsbb from siswa_tk s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%'
       and nama_lengkap like '%" . $this->db->escape_like_str(str_replace("'", "\\'", $nama_lengkap)) . "%'
       and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "tk";
      }
      $transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".str_replace("'", "\\'", $data->no_transaksi)."' order by id_transaksi DESC", "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_tk", "id_siswa_tk", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'tk' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          $data->jenis_kwitansi = 'UANG PENDAFTARAN';
        }
      }
    }

    if (!empty($data)) {
      if(!empty($data->foto_peserta)){
        $data->foto_peserta = base_url("uploads/siswa_".strtolower($jenjang)."/").$data->foto_peserta;
      }
      //$transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $data->no_transaksi, "row");
      $transaksi = $this->mymodel->withquery("select * from transaksi where user_email = '".$data->email."' and description like '%".$this->db->escape_like_str(str_replace("'", "\'", $data->nama_lengkap))."%' order by id_transaksi ASC","result");
      $logo_bank = "";
      $no_transaksi = "";
      if (!empty($transaksi)) {
        foreach ($transaksi as $key => $value) {
          $logo_bank = "";
          if ($value->nama_bank == "BNI") {
            $logo_bank = base_url("uploads/logo_bni.png");
          }
          else if ($value->nama_bank == "BRI") {
            $logo_bank = base_url("uploads/logo_bri.png");
          }
          $no_transaksi = $value->no_transaksi;
          $trx_type = explode("-", $value->no_transaksi);
          $jenis_pembayaran = $trx_type[0];
          $kwitansi = "";
          if ($jenis_pembayaran == 'LDUI') {
          // $daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sd","id_siswa_sd",$data->id_siswa,"row");
          $data->tipe_transaksi = 'daftar_ulang';
          $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
          $data->tgl_daftar_ulang = '';
          // $data->status_transaksi = "Menunggu Aktivasi VA Daftar Ulang";
          } else if ($jenis_pembayaran == 'LI') {
            $data->tipe_transaksi = 'pendaftaran';
            $data->jenis_kwitansi = 'UANG PENDAFTARAN';
          }
          if($value->tipe_tagihan == "fixed" && $value->status_transaksi == "1" ){
            $kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$value->no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          }
          else if($value->tipe_tagihan == "open" && $value->status_transaksi == "0" && $value->payment_amount != 0){
            $kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$value->no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          }
          else{
            $kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$value->no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          }
          if ($jenis_pembayaran == 'LDUI') {
            $data->virtual_account = $value->va_number;
            $riwayat_payment = array();
            if($value->nama_bank == "BRI"){
              $riwayat_payment = $this->mymodel->withquery("select briva_no, bill_amount, transaction_date from payment_response_bri where briva_no = '".$value->va_number."'","result");
              if (!empty($riwayat_payment)) {
                foreach ($riwayat_payment as $key_payment => $value_payment) {
                  $value_payment->transaction_date = date("d-m-Y H:i", strtotime($value_payment->transaction_date))." WIB";
                  $value_payment->bill_amount = formatIDR($value_payment->bill_amount);
                }
              }
              $daftar_ulang = $this->mymodel->withquery("select * from status_daftar_ulang_sd where id_siswa_sd = '".$data->id_siswa."'","row");
            }
            else if($value->nama_bank == "BNI"){
              $riwayat_payment = array();//$this->mymodel->withquery("select trx_id, payment_ntb from payment_response_bni where va_number = '".$value->va_number."'","result");
              $daftar_ulang = $this->mymodel->withquery("select * from status_daftar_ulang_".$data->tipe_siswa." where id_siswa_".$data->tipe_siswa." = '".$data->id_siswa."'","row");
            }

            if ($daftar_ulang->status == "1") {
              $data->status_transaksi = "Menunggu Pembayaran";
              if ($daftar_ulang->slip_pembayaran != '') {
                //$data->slip_pembayaran_daftar_ulang = base_url('uploads/slip_pembayaran/') . $daftar_ulang->slip_pembayaran;
                $data->detail_transaksi[] = array(
                  "no_transaksi" => $value->no_transaksi,
                  "nama_bank" => $value->nama_bank,
                  "description" => $value->description,
                  "total_biaya" => $value->total_biaya,
                  "expired_datetime" => $value->expired_datetime,
                  "va_logo_bank" => $logo_bank,
                  "status_tagihan" => $value->status_transaksi,
                  "kwitansi" => $kwitansi,
                  "payment_amount" => formatIDR($value->payment_amount),
                  "sisa_tagihan" => formatIDR($value->total_biaya - $value->payment_amount),
                  "tipe_tagihan" => $value->tipe_tagihan,
                  "riwayat_payment" => $riwayat_payment
                );
              }
            } else if ($daftar_ulang->status == "2") {
              $data->status_transaksi = "Lunas";
              $data->detail_transaksi[] = array(
                "no_transaksi" => $value->no_transaksi,
                "nama_bank" => $value->nama_bank,
                "description" => $value->description,
                "total_biaya" => $value->total_biaya,
                "expired_datetime" => $value->expired_datetime,
                "va_logo_bank" => $logo_bank,
                "status_tagihan" => $value->status_transaksi,
                "kwitansi" => $kwitansi,
                "payment_amount" => formatIDR($value->payment_amount),
                "sisa_tagihan" => formatIDR($value->total_biaya - $value->payment_amount),
                "tipe_tagihan" => $value->tipe_tagihan,
                "riwayat_payment" => $riwayat_payment
              );
            }
          }
          else if ($jenis_pembayaran == 'LI') {
            if ($value->status_transaksi == "0") {
              $data->status_transaksi = "Menunggu Pembayaran";
            } else if ($value->status_transaksi == "1") {
              if ($data->status_lulus == "Lulus") {
                $data->tipe_transaksi = 'daftar_ulang';
                $data->jenis_kwitansi = 'UANG DAFTAR ULANG';
                $data->status_transaksi = "Menunggu Aktivasi VA";
                $data->tgl_daftar_ulang = $daftar_ulang->tgl_daftar_ulang;
              } else {
                $data->status_transaksi = "Lunas";
              }
            }
            $data->virtual_account = $value->va_number;
            $data->detail_transaksi[] = array(
              "no_transaksi" => $value->no_transaksi,
              "nama_bank" => $value->nama_bank,
              "description" => $value->description,
              "total_biaya" => $value->total_biaya,
              "expired_datetime" => $value->expired_datetime,
              "va_logo_bank" => $logo_bank,
              "status_tagihan" => $value->status_transaksi,
              "kwitansi" => $kwitansi,
              "payment_amount" => formatIDR($value->payment_amount),
              "sisa_tagihan" => formatIDR($value->total_biaya - $value->payment_amount),
              "tipe_tagihan" => $value->tipe_tagihan,
              "riwayat_payment" => array()
            );
          }
        }
        /* $data->detail_transaksi = array(
          "no_transaksi" => $transaksi->no_transaksi,
          "nama_bank" => $transaksi->nama_bank,
          "description" => $transaksi->description,
          "total_biaya" => $transaksi->total_biaya,
          "expired_datetime" => $transaksi->expired_datetime,
          "va_logo_bank" => $logo_bank,
          "status_tagihan" => $transaksi->status_transaksi
        ); */
        if($data->tipe_siswa=='ft'){
          //$data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'ft' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kartu_peserta = base_url()."apiweb/psb/export_pdf_siswa?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          //$data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          //$data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=UANG DAFTAR ULANG";
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url()."apiweb/psb/export_kartu_siswa_sementara?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          }
          else{
            $data->kartu_siswa_sementara = "";
          }
        }else if($data->tipe_siswa=='sma'){
          //$data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sma' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kartu_peserta = base_url()."apiweb/psb/export_pdf_siswa?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          //$data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          //$data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=UANG DAFTAR ULANG";
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url()."apiweb/psb/export_kartu_siswa_sementara?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          }
          else{
            $data->kartu_siswa_sementara = "";
          }
        }else if($data->tipe_siswa=='smp'){
          //$data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'smp' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kartu_peserta = base_url()."apiweb/psb/export_pdf_siswa?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          //$data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          //$data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=UANG DAFTAR ULANG";
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url()."apiweb/psb/export_kartu_siswa_sementara?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          }
          else{
            $data->kartu_siswa_sementara = "";
          }
        }else if($data->tipe_siswa=='sd'){
          //$data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sd' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kartu_peserta = base_url()."apiweb/psb/export_pdf_siswa?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          //$data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          //$data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=".$data->jenis_kwitansi;
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url()."apiweb/psb/export_pdf_kwitansi?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang)."&jenjang=".strtoupper($jenjang)."&no_transaksi=".$no_transaksi."&jalur=PSB&jenis_kwitansi=UANG DAFTAR ULANG";
          }
          if ($daftar_ulang->status == 2) {
            $data->kartu_siswa_sementara = base_url()."apiweb/psb/export_kartu_siswa_sementara?id_siswa=".$data->id_siswa."&tipe_siswa=".strtolower($jenjang);
          }
          else{
            $data->kartu_siswa_sementara = "";
          }
        }
      
      }
      else{
        $data->detail_transaksi[] = array(
          "no_transaksi" => null,
          "nama_bank" => null,
          "description" => null,
          "total_biaya" => null,
          "expired_datetime" => null,
          "va_logo_bank" => null,
          "status_tagihan" => null
        );
      }
      $msg = array('status' => 1, 'message' => 'Berhasil ambil data', 'data' => $data);
      $status = "200";
    } else {
      $msg = array('status' => 0, 'message' => 'Data tidak ditemukan', 'data' => array());
      $status = "200";
    }

    $this->response($msg, $status);
  }
}
