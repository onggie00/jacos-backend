<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Payment_notification_ft extends MY_Controller
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
    $this->load->library('Spp_payment_detail');
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
    }
    // FROM BNI

    // URL utk simulasi pembayaran: http://dev.bni-ecollection.com/dev/flagging
    $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label;
    $data = file_get_contents('php://input');

    $data_json = json_decode($data, true);

    $resp = array(
      "text" => $data,
      "sumber_endpoint" => "payment_notification_ft"
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
        //$data_asli = explode('.',$data_asli,2);
        //$data_asli = json_decode($data_asli[1]);
        //print_r($data_asli->payment_ntb);
        if (!$data_asli) {
          // handling jika waktu server salah/tdk sesuai atau secret key salah
          //echo '{"status":"999","message":"waktu server tidak sesuai NTP atau secret key salah."}';
          $msg = array('status' => 0, 'message' => 'waktu server tidak sesuai NTP atau secret key salah.', 'data' => array());
          $status = "200";
        } else {
          $data_response=array(
            "payment_ntb"=>$data_asli['payment_ntb'],
            "trx_id"=>$data_asli['trx_id']
          );
          $this->mymodel->insertid("payment_response_bni", $data_response);

          //bila dibayar maka akan generate payment_ntb
          $no_peserta = "";
          $no_transaksi = $data_asli['trx_id'];
          $ajaran_aktif = $this->mymodel->withquery("select * from tahun_ajaran where tanggal_mulai <= '".date('Y-m-d')."' and tanggal_selesai >= '".date('Y-m-d')."'", "row");
          //$tahun_pelajaran = date('y', strtotime($ajaran_aktif->tanggal_mulai."+1 years")) . date("y", strtotime($ajaran_aktif->tanggal_selesai."+1 years"));
          $tahun_ajar_psb = $this->mymodel->getbywhere("tahun_ajaran_psb", "id",1,"row")->label;
          $tahun_pelajaran = substr($tahun_ajar_psb,2,2);
          $tahun_pelajaran = $tahun_pelajaran.substr($tahun_ajar_psb,7,2);
          $unit = "";
          $trx_id = explode("-", $no_transaksi);
          $jenis_pembayaran = $trx_id[0];
          $jenjang = $trx_id[1];
          $no_urut = "";
          $tipe_siswa = "";
          $id_siswa = "";
          if ($jenis_pembayaran == "LI" || $jenis_pembayaran == "LDUI") {
            if ($jenjang == "FT" || $jenjang == "PPSBBFT") {
              //unit code no_peserta: Jalur Prestasi (PPSBBFT) = 41, Jalur Tes (FT) = 42
              if ($jenjang == "PPSBBFT") {
                $unit = "41";
                $jalur = "PPSBB";
                $filter_jalur = " and ppsbb = '1'";
              }
              else {
                $unit = "42";
                $jalur = "PSB Jalur Tes";
                $filter_jalur = " and ppsbb != '1'";
              }
              //get no_peserta terakhir di tahun ajaran tsb
              /* $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa from siswa_ft where no_peserta like '%" . $tahun_pelajaran . "41%' and no_peserta != '' order by id_siswa_ft DESC", "row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              } else {
                $no_urut = (int) substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut + 1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran . $unit . $no_urut; */
              $get_list = $this->mymodel->withquery("
                  SELECT CAST(RIGHT(no_peserta, 4) AS UNSIGNED) AS urut
                  FROM siswa_ft
                  WHERE no_peserta LIKE '%" . $tahun_pelajaran . $unit . "%'
                    AND no_peserta != ''" . $filter_jalur . "
                  ORDER BY urut ASC
              ", "result");

              // Default no urut
              $no_urut = 1;

              // Cek nomor urut kosong
              if (!empty($get_list)) {
                  foreach ($get_list as $row) {
                      if ((int)$row->urut != $no_urut) {
                          // Ada celah, gunakan no urut ini
                          break;
                      }
                      $no_urut++;
                  }
              }

              // Format ke 4 digit
              $no_urut = sprintf("%04d", $no_urut);

              // Gabungkan ke format akhir
              $no_peserta = $tahun_pelajaran . $unit . $no_urut;
              //cek duplikat data no peserta
              $cek_no = 1;
              for ($i=0; $i < 10; $i++) { 
                $no_urut = $no_urut+$i;
                $no_urut = sprintf("%04d", $no_urut);
                $no_peserta = $tahun_pelajaran . $unit . $no_urut;
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
                $no_peserta = $tahun_pelajaran.$unit.($no_urut+1);
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              }*/
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta, tahun_ajaran, gelombang from siswa_ft where no_transaksi = '" . $no_transaksi . "'", "row");
              if (!empty($cek_no_peserta) && $cek_no_peserta->no_peserta=='' && $cek_no == null) {
                $data_siswa = $this->mymodel->update("siswa_ft", array("no_peserta" => $no_peserta, "password_ujian" => rand(100000, 999999)), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
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
              
              //MASUKKAN KE RUANG KELAS YANG TERSEDIA DENGAN ADD TO ujian_ruang_pendaftaran_detail_ft
              //hanya jalur tes (FT); jalur prestasi (PPSBBFT) tanpa ujian
              if ($jenjang == "FT") {
              $cek_ruangan = $this->mymodel->withquery("select r.id_ruang_pendaftaran, r.nama_ruang, r.maks_peserta, 
              (select count(*) from ujian_ruang_pendaftaran_detail_".strtolower($jenjang)." 
              where r.id_ruang_pendaftaran = ujian_ruang_pendaftaran_detail_".strtolower($jenjang).".id_ruang_pendaftaran) as total_peserta_now 
              from ujian_ruang_pendaftaran_".strtolower($jenjang)." r order by nama_ruang ASC","result");
              foreach ($cek_ruangan as $key => $item) {
                if ($item->total_peserta_now < $item->maks_peserta) {
                  $data_ruangan = array(
                    "id_ruang_pendaftaran" => $item->id_ruang_pendaftaran,
                    "nomor_peserta" => $cek_no_peserta->no_peserta,
                    "tahun_ajaran" => $cek_no_peserta->tahun_ajaran,
                    "gelombang" => $cek_no_peserta->gelombang,
                    "added_at" => date("Y-m-d H:i:s"),
                  );
                  //cek jika sudah ada data peserta di ruang ini
                  $cek_registered = $this->mymodel->withquery("select * from ujian_ruang_pendaftaran_detail_".strtolower($jenjang)." 
                  where id_ruang_pendaftaran = ".$item->id_ruang_pendaftaran." and nomor_peserta = '".$cek_no_peserta->no_peserta."'","row");
                  if(empty($cek_registered)){
                    $ruangan_peserta = $this->mymodel->insertid("ujian_ruang_pendaftaran_detail_ft", $data_ruangan);
                  }
                  break;
                }
              }
              }
              
              //CETAK KARTU PESERTA
              $cetak_kartu = $this->cetak_kartu(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
              $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, va_number, nama_bank, total_biaya, expired_datetime from transaksi where no_transaksi = '" . $data_asli['trx_id'] . "'", "row");

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa, nama_lengkap, email, nama_ibu, nama_ayah, no_peserta from siswa_ft where no_transaksi = '" . $no_transaksi . "'", "row");
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
              $this->mymodel->update("status_daftar_ulang_ft",array("status" => 2,"kwitansi" => $data_email["kwitansi"], "kartu_sementara" => $data_email["kartu_siswa_sementara"],"tgl_bayar" => date("Y-m-d H:i:s")),"id_siswa_ft",$cek_no_peserta->id_siswa);
            }
            else if ($jenis_pembayaran == "SP" || $jenis_pembayaran == "SPP" || strpos($jenis_pembayaran, "SP") !== false) {
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

              //UPDATE TRANSAKSI SPP + SPP DETAIL secara atomic.
              //Guard id_siswa_aktif + id_tahun_ajaran karena id_spp tidak global unik antar jenjang.
              $this->db->trans_begin();
              if (!$this->_update_spp_detail($get_transaksi_spp, $data_transaksi["updated_at"])) {
                $this->db->trans_rollback();
                $msg = array('status' => 0, 'message' => 'Gagal memperbarui detail SPP', 'data' => array());
                $status = "200";
              } else {
                $where_tagihan = "kode_tagihan = " . $this->db->escape($data_asli['trx_id']) . " and status_transaksi = '1'";
                $transaksi = $this->mymodel->update("transaksi_spp", $data_transaksi, $where_tagihan);
                if ($transaksi < 1 || $this->db->trans_status() === false) {
                  $this->db->trans_rollback();
                  $msg = array('status' => 0, 'message' => 'Gagal memperbarui transaksi SPP', 'data' => array());
                  $status = "200";
                } else {
                  $this->db->trans_commit();

                  //CEK UJIAN TERDEKAT BILA SUDAH MEMBAYAR DIPERBOLEHKAN UJIAN
                  //$bulan_ini = date("Y-m-d", strtotime("-1 months"));//formatBulan(date("n", strtotime("-1 months")));
                  $bulan_ini = $this->mymodel->getbywhere("setting_sync_ujian","jenjang",strtolower($jenjang),"row")->tanggal_terakhir_bayar;
                  $bulan_ini = formatBulan($bulan_ini);
                  $get_transaksi = $this->mymodel->withquery("select status_transaksi,bulan from transaksi_spp where id_spp = '".$get_transaksi_spp->id_spp."' and no_transaksi like '%".$jenjang."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$ajaran_aktif->id_tahun_ajaran."' order by id_transaksi DESC","row");
                  $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$get_transaksi_spp->id_siswa_aktif."' ","row");
                  if (!empty($get_transaksi) && $get_transaksi->status_transaksi == 2 && !empty($get_siswa)) {
                    $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => $get_transaksi_spp->user_name, "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
                  }

                  $msg = array('status' => 1, 'message' => 'Berhasil menerima data push notification', 'data' => $data_transaksi);
                  $status = "200";
                }
              }
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
    $mailContent = $this->load->view('template_email_pembayaran_ft', $data_, true);
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
