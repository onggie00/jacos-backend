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

    $email = $this->post('email');
    $jenjang = $this->post('jenjang');
    $data = null;


    //cek data urut ft, sma, smp, sd

    //ft
    if ($jenjang=="ft") {
      $data = $this->mymodel->withquery("select s.id_siswa_ft as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta from siswa_ft s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and is_show = 1", "row");
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
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->kartu_sementara != '') {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'ft' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
        }
      }
    }

    //sma
    if ($jenjang=="sma") {
      $data = $this->mymodel->withquery("select s.id_siswa_sma as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta from siswa_sma s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "sma";
      }
      $transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $data->no_transaksi, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->kartu_sementara != '') {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sma' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
        }
      }
    }

    //smp
    if ($jenjang=="smp") {
      $data = $this->mymodel->withquery("select s.id_siswa_smp as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta from siswa_smp s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "smp";
      }
      $transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $data->no_transaksi, "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_smp", "id_siswa_smp", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->kartu_sementara != '') {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
          if (empty($data->no_peserta)) {
            $data->kartu_peserta = "";
          } else {
            $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'smp' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          }
        }
      } else if (!empty($data) && $transaksi->status_transaksi == "0") {
        if ($jenis_pembayaran == 'LDUI') {
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
        }
      }
    }

    //sd
    if ($jenjang=="sd") {
      $data = $this->mymodel->withquery("select s.id_siswa_sd as id_siswa, s.email, s.nama_lengkap, s.no_peserta, s.no_transaksi, st.status_lulus, s.no_peserta from siswa_sd s join status_lulus st on s.status_lulus = st.id_status_lulus where email like '%" . $email . "%' and is_show = 1", "row");
      if (!empty($data)) {
        $data->tipe_siswa = "sd";
      }
      $transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $data->no_transaksi, "row");
      $trx_type = explode("-", $transaksi->no_transaksi);
      $jenis_pembayaran = $trx_type[0];
      $daftar_ulang = $this->mymodel->getbywhere("status_daftar_ulang_sd", "id_siswa_sd", $data->id_siswa, "row");
      if (!empty($data) && $transaksi->status_transaksi == "1") {
        if ($jenis_pembayaran == 'LDUI') {
          // $daftar_ulang=$this->mymodel->getbywhere("status_daftar_ulang_sd","id_siswa_sd",$data->id_siswa,"row");
          $data->tipe_transaksi = 'daftar_ulang';
          $data->tgl_daftar_ulang = '';
          if ($daftar_ulang->kwitansi != '') {
            $data->kwitansi_daftar_ulang = base_url('uploads/kwitansi/') . $daftar_ulang->kwitansi;
          }
          if ($daftar_ulang->kartu_sementara != '') {
            $data->kartu_siswa_sementara = base_url('uploads/kartu_siswa_sementara/') . $daftar_ulang->kartu_sementara;
          }
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
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
          $data->tgl_daftar_ulang = '';
          // $data->status_transaksi = "Menunggu Aktivasi VA Daftar Ulang";
        } else if ($jenis_pembayaran == 'LI') {
          $data->tipe_transaksi = 'pendaftaran';
        }
      }
    }

    if (!empty($data)) {
      $transaksi = $this->mymodel->getbywhere("transaksi", "no_transaksi", $data->no_transaksi, "row");
      if (!empty($transaksi)) {
        $trx_type = explode("-", $data->no_transaksi);
        $jenis_pembayaran = $trx_type[0];
        if ($jenis_pembayaran == 'LDUI') {
          if ($daftar_ulang->status == "1") {
            $data->status_transaksi = "Menunggu Pembayaran";
            if ($daftar_ulang->slip_pembayaran != '') {
              $data->slip_pembayaran_daftar_ulang = base_url('uploads/slip_pembayaran/') . $daftar_ulang->slip_pembayaran;
            }
          } else if ($daftar_ulang->status == "2") {
            $data->status_transaksi = "Lunas";
          }
        } else if ($jenis_pembayaran == 'LI') {
          if ($transaksi->status_transaksi == "0") {
            $data->status_transaksi = "Menunggu Pembayaran";
          } else if ($transaksi->status_transaksi == "1") {
            if ($data->status_lulus == "Lulus") {
              $data->tipe_transaksi = 'daftar_ulang';
              $data->status_transaksi = "Menunggu Aktivasi VA";
              $data->tgl_daftar_ulang = $daftar_ulang->tgl_daftar_ulang;
            } else {
              $data->status_transaksi = "Lunas";
            }
          }
          $data->virtual_account = $transaksi->va_number;
        }
        if($data->tipe_siswa=='ft'){
          $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'ft' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
        }else if($data->tipe_siswa=='sma'){
          $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sma' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
        }else if($data->tipe_siswa=='smp'){
          $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'smp' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
        }else if($data->tipe_siswa=='sd'){
          $data->kartu_peserta = base_url('uploads/kartu_peserta/') . 'sd' . '-' . $data->no_peserta . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->slip_pembayaran = base_url('uploads/slip_pembayaran/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
          $data->kwitansi = base_url('uploads/kwitansi/') . $data->no_transaksi . '-' . str_replace(" ", "%20", $data->nama_lengkap) . ".pdf";
        }
        
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
