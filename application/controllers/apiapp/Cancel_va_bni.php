<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cancel_va_bni extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

      $va_number = $this->post("va_number");
      $no_transaksi = $this->post("no_transaksi");
      $jenjang = $this->post("jenjang");
      $is_spp = (int) $this->post("is_spp");

      // no_transaksi (trx_id BNI) wajib — BNI mengidentifikasi billing lewat ini.
      if (empty($no_transaksi)) {
        $msg = array('status' => 0, 'message' => 'no_transaksi wajib diisi', 'data' => array());
        $this->response($msg, "200");
        return;
      }

      // Fix: data billing (nama, email, nominal, va_number) diambil dari DB,
      // bukan dipercaya dari POST. BNI menolak updatebilling jika trx_amount
      // berbeda dari billing asli (status 107 "Amount can not be changed").
      // Pembatalan = updatebilling dengan datetime_expired di masa lalu
      // (spec BNI eCollection VA Credit v3.0.9 §2.3).
      // - SPP   : no_transaksi = kode_tagihan di transaksi_spp (bisa multi
      //   baris/bulan dengan kode_tagihan sama) → nominal = SUM(total_biaya)
      //   baris yang masih aktif.
      // - Non-SPP: no_transaksi = no_transaksi di tabel transaksi.
      // Billing sudah lunas / tidak ada → tolak sebelum hit BNI.
      if (!empty($is_spp)) {
        $get_rows = $this->mymodel->withquery("select user_name, user_email, va_number, total_biaya from transaksi_spp where kode_tagihan = '".$this->db->escape_str($no_transaksi)."' and status_transaksi in (0,1)", "result");
      } else {
        $get_rows = $this->mymodel->withquery("select user_name, user_email, va_number, total_biaya from transaksi where no_transaksi = '".$this->db->escape_str($no_transaksi)."' and status_transaksi in (0,1)", "result");
      }

      if (empty($get_rows)) {
        $msg = array('status' => 0, 'message' => 'Billing tidak ditemukan / sudah dibayar / sudah tidak aktif', 'data' => array());
        $this->response($msg, "200");
        return;
      }

      $total_biaya = 0;
      $nama_lengkap = "";
      $email_user = "";
      $db_va_number = "";
      foreach ($get_rows as $key => $row) {
        $total_biaya += (int)$row->total_biaya;
        $nama_lengkap = $row->user_name;
        $email_user = $row->user_email;
        $db_va_number = $row->va_number;
      }
      if (empty($db_va_number)) {
        $db_va_number = $va_number; // fallback ke POST kalau va_number DB kosong
      }

      $data = $this->update_billing(ENVIRONMENT, $total_biaya, $no_transaksi, array("nama" => $nama_lengkap, "va_number" => $db_va_number, "email" => $email_user), $jenjang, $is_spp);

        if (!empty($data) && !empty($data['virtual_account'])) {
          $msg = array('status' => 1, 'message'=>'Berhasil batalkan VA' ,'data'=>$data);
          $status="200";
        }
        else{
          // errResponse memuat status code + message BNI (mis. 103 Billing has
          // been expired, 107 Amount can not be changed) supaya bisa didiagnose.
          $msg = array('status' => 0, 'message'=>'Gagal batalkan VA' ,'data'=>array(), 'errResponse' => $data);
          $status="200";
        }

        $this->response($msg,$status);
    }

    function update_billing($production, $total, $no_transaksi, $data_user, $jenjang = 'FT', $is_spp = 0){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      if (!empty($is_spp)) {
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
          if ($production == "production") {
            if ($value->name_setting == "bni_api_prod_url") {
              $url = $value->value;
            }
          } else if ($production == "development" || $production == "testing") {
            if ($value->name_setting == "bni_api_dev_url") {
              $url = $value->value;
            }
          }
        }
      }
      else if (strtoupper($jenjang) == "FT") {
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
          if ($production == "production") {
            if ($value->name_setting == "bni_api_prod_url") {
              $url = $value->value;
            }
          } else if ($production == "development" || $production == "testing") {
            if ($value->name_setting == "bni_api_dev_url") {
              $url = $value->value;
            }
          }
        }
      }
      else {
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
          if ($production == "production") {
            if ($value->name_setting == "bni_api_prod_url") {
              $url = $value->value;
            }
          } else if ($production == "development" || $production == "testing") {
            if ($value->name_setting == "bni_api_dev_url") {
              $url = $value->value;
            }
          }
        }
      }
      
      // Pembatalan: expire sekarang juga (spec §2.3, datetime_expired ISO 8601).
      // Fix format: 'P' menghasilkan offset dengan titik dua (+07:00) sesuai
      // contoh spec; 'O' lama menghasilkan +0700 yang bisa ditolak BNI (status 012).
      $date_va = date("Y-m-d\TH:i:sP", strtotime("-1 minutes"));

      $data_asli = array(
        'type' => "updatebilling", // spec §2.3: lowercase
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
        'trx_amount' => $total,
        'billing_type' => 'c', // spec §2.3: wajib & harus match create (create pakai 'c')
        'datetime_expired' => $date_va, // expire sekarang juga = pembatalan VA
        'customer_name' => $data_user['nama'],
        'customer_email' => $data_user['email'],
        'virtual_account' => $data_user['va_number'], // spec §2.3: harus match create
        //'customer_phone' => $data_user['notelp'],
      );
      $hashed_string = BniEnc::encrypt(
        $data_asli,
        $client_id,
        $secret_key
      );
      
      $data = array(
        'client_id' => $client_id,
        'prefix' => $prefix, // spec BNI VA Credit v3.0.3+: prefix wajib di request body
        'data' => $hashed_string,
      );

      $response = $this->get_content($url, json_encode($data));
      $response_json = json_decode($response, true);

      if (empty($response_json) || $response_json['status'] !== '000') {
        // handling jika gagal — bawa status code + message BNI ke caller
        // (mis. 100 Billing has been paid, 103 Billing has been expired,
        //  107 Amount can not be changed) agar bisa didiagnose.
        return array(
          'bni_status' => isset($response_json['status']) ? $response_json['status'] : '',
          'bni_message' => isset($response_json['message']) ? $response_json['message'] : (empty($response_json) ? 'no response from BNI API' : '')
        );
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

    function get_content($url, $post = '')
    {
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

      if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      }

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $rs = curl_exec($ch);

      if (empty($rs)) {
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      return $rs;
    }
}
