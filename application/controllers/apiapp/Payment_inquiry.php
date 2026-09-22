<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Payment_inquiry extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $no_transaksi = $this->get("no_transaksi");
        $is_spp = (int) $this->get("is_spp");
        $get_tagihan = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi = '".$no_transaksi."' or kode_tagihan = '".$no_transaksi."'","row");
        if (empty($get_tagihan)) {
          $get_tagihan = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$no_transaksi."'","row");
        }
        else{
          $get_tagihan->no_transaksi = $get_tagihan->kode_tagihan;
        }
        $data = array();
        $data = $this->check_billing("production", $get_tagihan->no_transaksi, $is_spp);

        if (!empty($data)) {
          if ($data['va_status'] == "2" && !empty($data['payment_ntb'])) {
            //generate kartu peserta
            $no_peserta = "";
            $tahun_pelajaran = date("y", strtotime("+1 years")).date("y", strtotime("+2 years"));
            $unit = "";
            $trx_id = explode("-", $no_transaksi);
            $jenjang = $trx_id[1];
            $no_urut = "";
            $tipe_siswa = "";
            $id_siswa = "";
            if ($jenjang == "SD") {
              $unit = "10";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' order by id_siswa_sd DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "sd";
            }
            if ($jenjang == "SDM") {
              $unit = "13";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' and is_mutasi = '1' order by id_siswa_sd DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sd where no_peserta = '".$no_peserta."'","row");
              }

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sd where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "sd";
            }
            if ($jenjang == "PPSBBSMP") {
              $unit = "21";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' and ppsbb = '1' order by id_siswa_smp DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "smp";
            }
            if ($jenjang == "PSBSMP") {
              $unit = "22";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' and ppsbb = '2' order by id_siswa_smp DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_smp where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_smp as id_siswa from siswa_smp where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "smp";
            }
            if ($jenjang == "PPSBBSMA") {
              $unit = "31";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa from siswa_sma where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' and ppsbb = '1' order by id_siswa_sma DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa from siswa_sma where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "sma";
            }
            if ($jenjang == "PSBSMA") {
              $unit = "32";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_sma as id_siswa from siswa_sma where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' and ppsbb = '2' order by id_siswa_sma DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_sma where no_peserta = '".$no_peserta."'","row");
              }

              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_sd as id_siswa from siswa_sma where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "sma";
            }
            if ($jenjang == "FT") {
              $unit = "40";
              //get no_peserta terakhir di tahun ajaran tsb
              $get_no_urut = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa from siswa_ft where no_peserta like '%".$tahun_pelajaran."%' and no_peserta != '' order by id_siswa_ft DESC","row");
              if (empty($get_no_urut)) {
                $no_urut = "0001";
              }
              else{
                $no_urut = (int)substr($get_no_urut->no_peserta, -4);
                $no_urut = $no_urut+1;
                $no_urut = sprintf("%04d", $no_urut);
              }
              $no_peserta = $tahun_pelajaran.$unit.$no_urut;
              //cek duplikat data no peserta
              $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              while (!empty($cek_no)) {
                $no_peserta = $tahun_pelajaran.$unit.$no_urut+1;
                $cek_no = $this->mymodel->withquery("select no_peserta from siswa_ft where no_peserta = '".$no_peserta."'","row");
              }
              
              $cek_no_peserta = $this->mymodel->withquery("select no_peserta, id_siswa_ft as id_siswa from siswa_ft where no_transaksi = '".$no_transaksi."'","row");
              if (!empty($cek_no_peserta)) {
                $data_siswa = $this->mymodel->update("siswa_sd", array("no_peserta" => $no_peserta), "no_transaksi", $no_transaksi);
              }
              $id_siswa = $cek_no_peserta->id_siswa;
              $tipe_siswa = "ft";
            }
            $data_transaksi = array(
              "status_transaksi" => 1,
              "updated_at" => date("Y-m-d H:i:s"),
            );
            $transaksi = $this->mymodel->update("transaksi", $data_transaksi, "no_transaksi", $no_transaksi);
            $cetak_kartu = $this->cetak_kartu(array("tipe_siswa" => $tipe_siswa, "id_siswa" => $id_siswa));
          }
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data, 'request'=>$get_tagihan);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>$data, 'request'=>$get_tagihan);
          $status="200";
        }

        $this->response($msg,$status);
    }

    function cetak_kartu($get = '') {
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
        $endpoint = site_url('/apiapp/siswa/export_pdf_siswa');
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

    function check_billing($production, $no_transaksi, $is_spp = 0){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      $trx_id = explode("-", $no_transaksi);
      $jenjang = strtoupper($trx_id[1]);

      foreach ($get_setting as $key => $value) {
        if (!empty($is_spp)) {
          if ($value->name_setting == "bni_client_id_spp") {
            $client_id = $value->value;
          }
          if ($value->name_setting == "bni_secret_key_spp") {
            $secret_key = $value->value;
          }
        }
        else if ($jenjang == "FT") {
          if ($value->name_setting == "bni_client_id_ft") {
            $client_id = $value->value;
            //$client_id = "00599";
          }
          if ($value->name_setting == "bni_secret_key_ft") {
            $secret_key = $value->value;
          }
        }
        else{
          if ($value->name_setting == "bni_client_id") {
            $client_id = $value->value;
          }
          if ($value->name_setting == "bni_secret_key") {
            $secret_key = $value->value;
          }
        }
        if ($value->name_setting == "bni_prefix") {
          $prefix = $value->value;
        }
        if ($production == "production") {
          if ($value->name_setting == "bni_api_prod_url") {
            $url = $value->value;
          }
        }
        else if ($production == "development"){
          if ($value->name_setting == "bni_api_dev_url") {
            $url = $value->value;
          }
        }
      }

      $data_asli = array(
        'type' => "inquirybilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
      );
      //print_r($data_asli);
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
        // var_dump($response_json);
        return $response_json;
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
}
