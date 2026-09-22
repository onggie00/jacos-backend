<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_cek_spp extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        //get all transaksi spp with status = 1 (menunggu pembayaran)
        $get_transaksi = $this->mymodel->withquery("select id_transaksi, no_transaksi, user_name as nama_siswa, id_siswa_aktif, nama_bank, va_number, status_transaksi, id_spp, total_biaya, bulan, updated_at, expired_datetime from transaksi_spp where (va_number like '13803%' or va_number like '13802%') and expired_datetime >= now()", "result");

        if (!empty($get_transaksi)) {
            foreach ($get_transaksi as $key => $value) {
                $no_transaksi = $value->no_transaksi;
                $bank = "BRI";//$value->nama_bank;
                $va_number = $value->va_number;
                $institution_code = substr($va_number, 0,5);
                $customer_code = substr($va_number, 5);
                $jenjang = explode("-", $no_transaksi);
                $jenjang = strtolower($jenjang[1]);
                $bulan = strtolower($value->bulan);

                if ($value->status_transaksi == 1) {
                    $data = $this->check_inquiry($bank, $no_transaksi, $va_number);
                    $data_bayar = $data['data'];
                    $status_bayar = $data_bayar['statusBayar'];
                    $keterangan = $data_bayar['Keterangan'];
                    $data_id_transaksi = explode("-", $keterangan);
                    if ($status_bayar == "Y") {
                        //cek data update at SPP
                        $cek_spp = $this->mymodel->withquery("select ".$bulan." from spp_".$jenjang." where id = '".$value->id_spp."' ","row");
                        if (empty($cek_spp->$bulan)) {
                            $update_spp = $this->mymodel->update("spp_".$jenjang, array($bulan => date("Y-m-d H:i:s")), "id", $value->id_spp);
                        }
                        //$update_transaksi = $this->mymodel->update("transaksi_spp", array("status_transaksi" => 2), "id_spp = '".$value->id_spp."' and va_number='".$va_number."' and bulan=", strtolower($data_bulan[1]));
                        $update_transaksi = $this->mymodel->update("transaksi_spp", array("status_transaksi" => 2), "id_transaksi", $data_id_transaksi);
                        $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$value->id_siswa_aktif."' ","row");
                        if (!empty($get_siswa->nomor_peserta_ujian)) {
                            $update_ujian = $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => strtoupper($value->nama_siswa), "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
                        }
                    }
                    else if($status_bayar == "N"){
                        //get report transaksi di rekening
                        if(!empty($value->updated_at) && !empty($value->expired_datetime)){
                            $start_date= date("Ymd",strtotime("-14 days"));
                            $end_date= date("Ymd",strtotime($value->expired_datetime));
                        }else{
                            $start_date=date("Ymd");
                            $end_date=date("Ymd");
                        }
                        $get_report = $this->get_report_bri($start_date, $end_date);
                        if (!empty($get_report)) {
                            foreach ($get_report as $key_report => $value_report) {
                                if ($value_report['brivaNo'] == $institution_code && $value_report['custCode'] == $customer_code && $value_report['nama'] == strtoupper($value->nama_siswa) && $value_report['no_rek'] != "" && $value_report['tellerid'] != "" && ((int)$value_report['amount'] == $value->total_biaya) ) {
                                    //jika ditemukan pembayaran dengan amount, va, dan nama siswa yg sama maka update spp lunas
                                    //cek data update at SPP
                                    $cek_spp = $this->mymodel->withquery("select ".$bulan." from spp_".$jenjang." where id = '".$value->id_spp."' ","row");
                                    if (empty($cek_spp->$bulan)) {
                                        $update_spp = $this->mymodel->update("spp_".$jenjang, array($bulan => date("Y-m-d H:i:s")), "id", $value->id_spp);
                                    }
                                    $update_transaksi = $this->mymodel->update("transaksi_spp", array("status_transaksi" => 2), "id_transaksi", $value->id_transaksi);
                                    $get_siswa = $this->mymodel->withquery("select nomor_peserta_ujian from siswa_".strtolower($jenjang)."_aktif where id_siswa_".strtolower($jenjang)."_aktif = '".$value->id_siswa_aktif."' ","row");
                                    if (!empty($get_siswa->nomor_peserta_ujian)) {
                                        $update_ujian = $this->mymodel->update("ujian_ruang_detail", array("nama_siswa" => strtoupper($value->nama_siswa), "boleh_ujian" => "YA"), "nomor_peserta_ujian", $get_siswa->nomor_peserta_ujian);
                                    }
                                    $data = $value_report;
                                    echo "DITEMUKAN<br/>";
                                }
                            }
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/><br/>";
                        }
                        else{
                            print_r($data);
                            print_r($bank." ".$no_transaksi." ".$va_number." (".$institution_code."-".$customer_code.")");
                            echo "<br/>Get Rekening Transaksi Tidak berfungsi<br/>";
                        }
                    }

                }
                else if ($value->status_transaksi == 2) {
                    //update riwayat spp siswa
                    //cek data update at SPP
                    $cek_spp = $this->mymodel->withquery("select ".$bulan." from spp_".$jenjang." where id = '".$value->id_spp."' ","row");
                    if (empty($cek_spp->$bulan)) {
                        $update_spp = $this->mymodel->update("spp_".$jenjang, array($bulan => date("Y-m-d H:i:s")), "id", $value->id_spp);
                    }
                }
            }
        }
        else{
            echo "tidak ada data";
        }

        //$this->response($msg,$status);
    }

    public function check_inquiry($bank, $no_transaksi, $va_number){
        //get va prefix
        $get_setting = $this->mymodel->getall("pengaturan_akun");
        if ($bank == "BNI" || $bank == "bni") {
            if ($jenjang == 'ft') {
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
            }

            $data = $this->check_billing_bni(ENVIRONMENT,$no_transaksi);
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
            $send_data = array(
                'brivaNo' => $brivaNo,
                'custCode' => substr($va_number, 5),
            );
            $data = $this->check_billing_bri($send_data);
        }
        return $data;
    }
    function check_billing_bni($production, $no_transaksi){
      $this->load->library('BniEnc');
      // FROM BNI
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
        'data' => $hashed_string,
      );

      $response = $this->get_content($url, json_encode($data));
      $response_json = json_decode($response, true);

      if ($response_json['status'] !== '000') {
        // handling jika gagal
        return($response_json);
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
        var_dump($rs, curl_error($ch));
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

    public function get_report_bri($start_date, $end_date){
        $this->load->library('BriApi');
          //get config bri key
          $get_setting = $this->mymodel->getall("pengaturan_akun");
          foreach ($get_setting as $key => $item) {
            if(ENVIRONMENT == "development" || ENVIRONMENT == "testing"){
              if($item->name_setting=='bri_dev_url'){
                $url = $item->value;
              }
              if ($item->name_setting == "bri_no_briva_dev") {
                $brivaNo = $item->value;
              }
            }else if(ENVIRONMENT == 'production'){
              if($item->name_setting=='bri_prod_url'){
                $url = $item->value;
              }
              if ($item->name_setting == "bri_no_briva_prod_close") {
                $brivaNo = $item->value;
              }
            }
            if($item->name_setting=='bri_client_id'){
              $clientID = $item->value;
            }
            if($item->name_setting=='bri_client_secret'){
              $clientSecret = $item->value;
            }
            if($item->name_setting=='bri_institution_code_close'){
              $institutionCode = $item->value;
            }
        }

          $endpoint     = $url."oauth/client_credential/accesstoken?grant_type=client_credentials";
          /*if($this->input->get('end_date')){
            $start_date=$this->input->get('start_date');
            $end_date=$this->input->get('end_date');
            
          }else{
            $start_date=date('Y').date('m').date('d');
            $end_date=date('Y').date('m').date('d');
          }*/

      $date=$this->echoDate(strtotime($start_date),strtotime($end_date));

        $data=[];
        foreach($date as $key => $d){
            $formated=str_replace('-','',$d);
            $datas=array(
              'brivaNo'=>$brivaNo,
              'startDate'=>$formated,
              'endDate'=>$formated
            );
        
            $arr=BriApi::getReportDate($clientID,$clientSecret,$endpoint,$institutionCode,$datas,$url);
            if($arr['responseCode']=='00'){
              foreach($arr['data'] as $item){
                $data[]=$item;
              }
            }
            /*print_r($arr);
            echo "<br/>";*/
        }
      return $data;
    }

    function echoDate( $start, $end ){

      $current = $start;
    
      $ret = array();
    
      while( $current<=$end ){
        
        
        $format=date('Y-m-d',$current);
        $ret[] = $format;
        $current = @date('Y-m-d', $current) . "+1 days";
        $current = @strtotime($current);
      }
    
      return $ret;
    }

}
