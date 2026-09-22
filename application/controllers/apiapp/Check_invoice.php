<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Check_invoice extends REST_Controller {
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
      $bank = $this->get("bank");
      $va_number = $this->get("va_number");
      $jenjang = $this->get("jenjang");
      $is_spp = (int) $this->get("is_spp");

    //get va prefix
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    $status_bayar = null;
    $get_tagihan = $this->mymodel->withquery("select * from transaksi_spp where no_transaksi = '".$no_transaksi."' or kode_tagihan = '".$no_transaksi."'","row");
        if (empty($get_tagihan)) {
          $get_tagihan = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$no_transaksi."'","row");
        }
        else{
          $get_tagihan->no_transaksi = $get_tagihan->kode_tagihan;
        }
    $data = array();
    if ($bank == "BNI" || $bank == "bni") {
      /*if ($jenjang == 'ft') {
        foreach ($get_setting as $key => $value) {
          if ($value->name_setting == "bni_client_id_ft") {
            $client_id = $value->value;
          }

          if ($value->name_setting == "bni_prefix") {
            $prefix = $value->value;
          }
        }
      }
      else {
        foreach ($get_setting as $key => $value) {
          if ($value->name_setting == "bni_client_id_spp") {
            $client_id = $value->value;
          }
          if ($value->name_setting == "bni_prefix") {
            $prefix = $value->value;
          }
        }
      }*/
      $data = $this->check_billing_bni(ENVIRONMENT, $jenjang ,$get_tagihan->no_transaksi, $is_spp);

      // BNI gagal mengembalikan detail error (bukan data billing) → kosongkan agar masuk cabang gagal
      if (!empty($data) && !isset($data['trx_id'])) {
        $err_detail = '';
        if (isset($data['status'])) {
          $err_detail = ' ['.$data['status'].(isset($data['message']) ? ' '.$data['message'] : '').']';
        }
        // $data = array();
      }
      $data_bayar = $data;
      $status_bayar = (isset($data_bayar['payment_ntb']) && $data_bayar['payment_ntb'] !== '') ? "Paid" : "Unpaid";
    }
    else if($bank == "BRI" || $bank == "bri"){
      foreach ($get_setting as $key => $value) {
          if (ENVIRONMENT == "production") {
            if ($value->name_setting == "bri_no_briva_prod_close") {
              $brivaNo = $value->value;
            }
          } else if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
            if ($value->name_setting == "bri_no_briva_dev_close") {
              $brivaNo = $value->value;
            }
          }
        }
        $custCode = substr((string) $va_number, 5);
        if (empty($brivaNo) || $custCode === '') {
          $msg = array('status' => 0, 'message' => 'Parameter VA tidak lengkap atau konfigurasi BRI belum ada', 'data' => array());
          $this->response($msg, "200");
          return;
        }
        $send_data = array(
          'brivaNo' => $brivaNo,
          'custCode' => $custCode,
        );
        $data = $this->check_billing_bri($send_data);
        if (empty($data) || !isset($data['data'])) {
          $msg = array('status' => 0, 'message' => 'Gagal mengambil data VA dari BRI', 'data' => array());
          $this->response($msg, "200");
          return;
        }
        $data_bayar = $data['data'];
        $status_bayar = (isset($data_bayar['statusBayar']) && strtoupper((string) $data_bayar['statusBayar']) === 'Y') ? "Paid" : "Unpaid";
    }

        if (!empty($data)) {
          //$this->export_xml();
          /*$data_bayar = $data['data'];
          $status_bayar = $data_bayar['statusBayar'];*/
          $msg = array('status' => 1, 'message'=>'Berhasil lihat data VA' ,'data'=>$data, 'status_bayar' => $status_bayar);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Gagals lihat data VA'.(isset($err_detail) ? $err_detail : '') ,'data'=>$data);
          $status="200";
        }

        $this->response($msg,$status);
    }

    function check_billing_bni($production, $jenjang, $no_transaksi, $is_spp = 0){
      $this->load->library('BniEnc');
      // FROM BNI
      $get_setting = $this->mymodel->getall("pengaturan_akun");
      if (!empty($is_spp)) {
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
      else{
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
        'type' => "inquirybilling",
        'client_id' => $client_id,
        'trx_id' => $no_transaksi,
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
        // handling jika gagal — detail kode status BNI (§3 spec) dioper ke caller
        if (empty($response_json)) {
          return array();
        }
        return array(
          'status' => $response_json['status'],
          'message' => isset($response_json['message']) ? $response_json['message'] : '',
          'request_data' => $data_asli,
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

    function check_billing_bri($datas)
	{
		$this->load->library('BriApi');

		//get config bri key
		$get_setting = $this->mymodel->getall("pengaturan_akun");
		foreach ($get_setting as $key => $item) {
			if (ENVIRONMENT == "development" || ENVIRONMENT == "testing") {
				if ($item->name_setting == 'bri_dev_url') {
					$url = $item->value;
				}
			} else if (ENVIRONMENT == 'production') {
				if ($item->name_setting == 'bri_prod_url') {
					$url = $item->value;
				}
			}
			if ($item->name_setting == 'bri_client_id') {
				$clientID = $item->value;
			}
			if ($item->name_setting == 'bri_client_secret') {
				$clientSecret = $item->value;
			}
			if ($item->name_setting == 'bri_institution_code_close') {
				$institutionCode = $item->value;
			}
		}

		$endpoint     = $url . "oauth/client_credential/accesstoken?grant_type=client_credentials";

		$data = array(
			'brivaNo' => $datas['brivaNo'],
			'custCode' => $datas['custCode'],
		);

    return BriApi::getData($clientID,$clientSecret,$url,$endpoint,$institutionCode,$data);
	}

}
