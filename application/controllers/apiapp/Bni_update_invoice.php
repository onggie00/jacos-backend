<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Bni_update_invoice extends REST_Controller {
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

      $no_transaksi = $this->post("no_transaksi");
      $total_biaya = $this->post("total_biaya");
      $jenjang = $this->post("jenjang");

      if (empty($no_transaksi)) {
        $msg = array('status' => 0, 'message' => 'no_transaksi wajib diisi', 'data' => array());
        $this->response($msg, "200");
        return;
      }

      // spec §2.3: trx_amount wajib, integer tanpa desimal/pemisah ribuan, max 14 digit (error 013)
      if (!preg_match('/^[0-9]{1,14}$/', (string) $total_biaya) || (int)$total_biaya <= 0) {
        $msg = array('status' => 0, 'message' => 'total_biaya wajib diisi berupa angka bulat tanpa desimal (maks 14 digit)', 'data' => array());
        $this->response($msg, "200");
        return;
      }

      // spec §2.3: datetime_expired harus ISO 8601 (error 012 jika invalid)
      $datetime_expired = null;
      if (!empty($this->post("datetime_expired"))) {
        $ts = strtotime($this->post("datetime_expired"));
        if ($ts === false) {
          $msg = array('status' => 0, 'message' => 'datetime_expired tidak valid', 'data' => array());
          $this->response($msg, "200");
          return;
        }
        $datetime_expired = date("Y-m-d\TH:i:sO", $ts);
      }

      // Field yang tidak dikirim di updateBilling akan di-replace string kosong (spec §2.3 Note)
      // → fallback ke nilai di tabel transaksi supaya detail VA tidak hilang
      $trx = $this->mymodel->withquery(
        "select va_number, user_name, user_email, user_phone, description, expired_datetime
        from transaksi
        where no_transaksi = '".$this->db->escape_str($no_transaksi)."' and is_show = 1",
        "row"
      );
      if (empty($trx)) {
        $msg = array('status' => 0, 'message' => 'Transaksi tidak ditemukan', 'data' => array());
        $this->response($msg, "200");
        return;
      }

      $data_user = array(
        "nama" => (!empty($this->post("nama_lengkap"))) ? $this->post("nama_lengkap") : $trx->user_name,
        "va_number" => $trx->va_number, // spec §2.3: virtual_account tidak boleh berubah, harus match create → ambil dari DB
        "email" => (!empty($this->post("customer_email"))) ? $this->post("customer_email") : $trx->user_email,
        "phone" => (!empty($this->post("customer_phone"))) ? $this->post("customer_phone") : $trx->user_phone,
        "description" => (!empty($this->post("description"))) ? $this->post("description") : $trx->description,
        "expired_datetime" => $trx->expired_datetime,
      );

      $data = $this->update_billing(ENVIRONMENT, $total_biaya, $no_transaksi, $data_user, $jenjang, $datetime_expired);

      if (!empty($data) && isset($data['trx_id'])) {
        $msg = array('status' => 1, 'message'=>'Berhasil update VA' ,'data'=>$data);
        $status="200";
      }
      else{
        $detail = '';
        if (isset($data['status'])) {
          $detail = ' ['.$data['status'].(isset($data['message']) ? ' '.$data['message'] : '').']';
        }
        $msg = array('status' => 0, 'message'=>'Gagal update VA'.$detail ,'data'=>array());
        $status="200";
      }

      $this->response($msg,$status);
    }

    function update_billing($production, $total, $no_transaksi, $data_user, $jenjang = 'FT', $datetime_expired = null){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      if (strtoupper($jenjang) == "FT") {
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

      $data_asli = array(
        'type' => "updatebilling", // spec §2.3: lowercase
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
        'trx_amount' => $total,
        'billing_type' => 'c', // spec §2.3: wajib & harus match create (create pakai 'c')
        'customer_name' => $data_user['nama'],
        'customer_email' => $data_user['email'],
        'customer_phone' => $data_user['phone'],
        'virtual_account' => $data_user['va_number'], // spec §2.3: tidak dikirim = ter-replace kosong
        'description' => $data_user['description'],
      );

      // datetime_expired: dari input, fallback ke DB; kalau dua-duanya kosong → tidak dikirim
      if (!empty($datetime_expired)) {
        $data_asli['datetime_expired'] = $datetime_expired;
      } elseif (!empty($data_user['expired_datetime'])) {
        $data_asli['datetime_expired'] = date("Y-m-d\TH:i:sO", strtotime($data_user['expired_datetime']));
      }

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
        // handling jika gagal — detail kode status BNI (§3 spec) dioper ke caller
        if (empty($response_json)) {
          return array();
        }
        return array(
          'status' => $response_json['status'],
          'message' => isset($response_json['message']) ? $response_json['message'] : ''
        );
      }
      else {
        $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
        // $data_response will contains something like this:
        // array(
        //  'virtual_account' => 'xxxxx',
        //  'trx_id' => 'xxx',
        // );
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
