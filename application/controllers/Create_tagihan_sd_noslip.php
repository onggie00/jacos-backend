<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Create_tagihan_sd_noslip extends REST_Controller
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

    $id_siswa = $this->post('id_siswa_aktif');
    $id_trans = explode(',',$this->post('id_transaksi'));
    $total_biaya = $this->post('total_biaya');
    $va_number = "";
    $id_thn_ajar = "";
    $thn_ajar = "";
    $name = "";
    $email = "";
    $id_transaksi = "";
    $logo = "";
    foreach ($id_trans as $key => $item) {
      $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");
      $email = $get_transaksi->user_email;
      $name = $get_transaksi->user_name;
      $id_thn_ajar = $get_transaksi->id_tahun_ajaran;
      $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran", "id_tahun_ajaran", $get_transaksi->id_tahun_ajaran, "row");
      $thn_ajar = $tahun_ajaran->code;
      $get_tahun_ajaran_lalu=$this->mymodel->withquery("select * from tahun_ajaran where sequence < $tahun_ajaran->sequence", "result");
      
      foreach($get_tahun_ajaran_lalu as $key => $item){
        $cek_exist_tagihan=$this->mymodel->withquery("select * from transaksi_spp where status_transaksi in(0,1) and user_name = '".$name."' and id_siswa_aktif = $id_siswa and id_tahun_ajaran = $item->id_tahun_ajaran", "result");
        
        if($cek_exist_tagihan){
          $msg = array('status' => 0, 'message'=>'Ada tagihan tahun lalu yang belum dibayarkan!' ,'data'=>null,'detail_charge'=>null);
          $status="200";
          $this->response($msg,$status);
        };
      }
      $va_number = $get_transaksi->va_number;
      if (empty($id_transaksi)) {
        $id_transaksi = $get_transaksi->id_transaksi;
      }
    }
    //cek tagihan lain
    $tagihan_lain = $this->mymodel->withquery("select ts.* from transaksi_lain_sd ts
    join transaksi_lain_sd_manajemen t on t.id = ts.id_transaksi_lain 
    where ts.status_transaksi in(0,1) and t.id_kategori = '2' and t.id_tahun_ajaran = ".$this->db->escape($id_thn_ajar)." and ts.id_siswa_aktif = ".$this->db->escape($id_siswa),"result");
    if (!empty($tagihan_lain)) {
      $msg = array('status' => 0, 'message' => 'Ada tagihan lain yang belum dibayarkan!', 'data' => null, 'detail_charge' => null);
      $status="200";
      $this->response($msg,$status);
      exit;
    }

    $code='80';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap from siswa_sd_aktif sa join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran left join kelas_sd k on k.id_kelas_sd = sa.id_kelas where id_siswa_sd_aktif = ".$id_siswa."","row");


    //get va prefix
    $get_setting = $this->mymodel->getall("pengaturan_akun");
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
    //cek va sudah tersedia / belum
    $cek_last_va = $this->mymodel->withquery("select id_transaksi, va_number, bulan from transaksi_spp where user_name = '".$name."' and status_transaksi = '2' and no_transaksi like '%sd%'" , "row");
    //$get_transakses = $this->mymodel->withquery("select id_transaksi, va_number, bulan from transaksi_spp where user_name = '".$name."' and status_transaksi in(0,1) order by id_transaksi DESC" , "row");
    if (!empty($cek_last_va) && $va_number == "") {
      $va_number = $cek_last_va->va_number;
    }

    if ($va_number == "") {
      $thn_ajar = substr($thn_ajar, 0,2);
      $va_number = $brivaNo.$code.$thn_ajar.rand(1000,9999);
      $tagihan_code="SP-".date("dmy")."-SD-".$va_number;
    
      $datas = array(
        'brivaNo' => $brivaNo,
        'custCode' => substr($va_number, 5),
        'nama' => $get_siswa->nama_lengkap,
        'amount' => $total_biaya,
        'keterangan' => 'SPP SD-'.$get_transaksi->id_transaksi,
        'expiredDate' => date("Y-m-d H:i:s", strtotime("+24 hours")),
      );

      $payment_response = $this->create_va_bri($datas);
    }
    else{
      $tagihan_code="SP-".date("dmy")."-SD-".$va_number;
     
      $datas = array(
        'brivaNo' => $brivaNo,
        'custCode' => substr($va_number, 5),
        'nama' => $get_siswa->nama_lengkap,
        'amount' => $total_biaya,
        'keterangan' => 'SPP SD-'.$id_transaksi,
        'expiredDate' => date("Y-m-d H:i:s", strtotime("+24 hours")),
      );

      /* $payment_response = $this->update_va_bri($datas);
      $update_payment = $this->update_va_bri_status($datas); */
      //delete va dahulu
      $delete_response = $this->delete_va_bri(array("brivaNo" => $datas["brivaNo"], "custCode" => $datas["custCode"]));
      $payment_response = $this->create_va_bri($datas);
      //print_r($update_payment);
      $datas["logo_bank"] = $logo;
      $datas["nominal_tagihan"] = (string)$total_biaya;
    }
      
    // $data['payment_respon']=$payment_response;
   
    if ($payment_response['responseCode'] != '00') {
      $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>'', 'res' => $payment_response);
      $status="200";
      $this->response($msg,$status);
    }

    $detail_bulan="";
    foreach ($id_trans as $key => $item) {
      $get_transaksi_bulan = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");
      $detail_bulan.=($key==0)?$get_transaksi_bulan->bulan:",".$get_transaksi_bulan->bulan;
    }

    /* $this->cetak_slip(array(
      "id_siswa" => $get_siswa->id_siswa_sd, 
      "tipe_siswa" => 'sd', 
      "detail_bulan" => $detail_bulan,
      "total_biaya" => $total_biaya,
      "va_number" => $va_number,
      "tagihan_code" => $tagihan_code,
      "expired_datetime" => date("Y-m-d H:i:s", strtotime("+24 hours")),
      "id_tahun_ajaran" => $get_transaksi->id_tahun_ajaran
    )); */

    foreach ($id_trans as $item) {
      $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");
      
      if ($get_transaksi->nama_bank == "BNI") {
        $logo = base_url().'/uploads/logo_bni.png';
      }
      else if($get_transaksi->nama_bank == "BRI"){
        $logo = base_url().'/uploads/logo_bri.png';
      }
      
        $data = array(
          "status_transaksi" => '1',
          "va_number" => $va_number,
          "kode_tagihan" => $tagihan_code,
          "expired_datetime" => date("Y-m-d H:i:s", strtotime("+24 hours")),
          "detail_bulan" => $detail_bulan,
          "count_bill" => count($id_trans),
          "updated_at" => date("Y-m-d H:i:s"),
        );
        //"file_slip" => $tagihan_code . '-' . str_replace(' ', '_', $get_siswa->nama_lengkap) . '.pdf',

      $this->mymodel->update("transaksi_spp", $data, "id_transaksi", $item);
      $data['logo_bank'] = $logo;
      $data['nominal_tagihan'] = (string)$total_biaya;
    }
    //$this->mymodel->update("transaksi_spp", array("va_number" => $va_number), "user_name", $name);

    $msg = array('status' => 1, 'message' => 'Tagihan Berhasil Dibuat', 'data' => $va_number, 'detail_charge' => $data);
    $status = "200";

    $this->response($msg, $status);
  }

  function cetak_slip($get = '') {
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
      $endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran_spp');
      $params = array(
        'id_siswa' => $get['id_siswa'], 
        'tipe_siswa' => $get['tipe_siswa'],
        'detail_bulan' => $get['detail_bulan'],
        'tagihan_code' => $get['tagihan_code'],
        'expired_datetime' => $get['expired_datetime'],
        'total_biaya' => $get['total_biaya'],
        'va_number' => $get['va_number'],
        'id_tahun_ajaran' => $get['id_tahun_ajaran']
      );
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

  function create_va_bri($datas)
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
			'expiredDate' => $datas['expiredDate'],
			'custCode' => $datas['custCode'],
			'nama' => $datas['nama'],
			'amount' => $datas['amount'],
			'keterangan' => $datas['keterangan']
		);
		return BriApi::create($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
	}

  public function delete_va_bri($datas)
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
		return BriApi::delete($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
  }

  function update_va_bri($datas)
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
      'expiredDate' => $datas['expiredDate'],
      'custCode' => $datas['custCode'],
      'nama' => $datas['nama'],
      'amount' => $datas['amount'],
      'keterangan' => $datas['keterangan']
    );
    return BriApi::update($clientID, $clientSecret, $endpoint, $institutionCode, $data, $url);
  }

  function update_va_bri_status($datas)
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
    
    return BriApi::updateBayar($clientID, $clientSecret, $endpoint, $institutionCode, $datas['brivaNo'], $datas['custCode'], 'N', $url);
  }

  function create_billing($production, $total, $no_transaksi, $data_user){
    $this->load->library('BniEnc');
    // FROM BNI
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
      if ($production == "production") {
        if ($value->name_setting == "bni_api_prod_url") {
          $url = $value->value;
        }
      }
      else if ($production == "development" || $production == "testing"){
        if ($value->name_setting == "bni_api_dev_url") {
          $url = $value->value;
        }
      }
    }
    
    $get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
    foreach ($get_pengaturan_masa_aktif as $key => $item) {
      if($item->label=='pendaftaran'){
        if($item->tipe_date=='day'){
          $date_va=($item->value*24) * 3600;
        }else{
          $date_va=($item->value) * 3600;
        }
      }
    }

    $data_asli = array(
      'type' => "updateBilling",
      'client_id' => $client_id,
      'trx_id' => $no_transaksi,
      'trx_amount' => $total,
      'datetime_expired' => date("Y-m-d H:i:s", strtotime("+24 hours")), // billing will be expired in 6 hours
      'customer_name' => $data_user['nama'],
      'customer_email' => $data_user['email'],
      'description' => "Payment of ".$no_transaksi
      //'billing_type' => 'c',
      //'virtual_account' => $data_user['va_number'],
      //'customer_phone' => $data_user['notelp'],
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
      // var_dump($rs, curl_error($ch));
      curl_close($ch);
      return false;
    }
    curl_close($ch);
    return $rs;
  }

  public function send_email_file($file="",$to='',$data)
  {
    $to = urldecode($to);
    $mail = new PHPMailer;
    // Konfigurasi SMTP
    $mail->isSMTP();
    $mail->SMTPDebug =0;
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
    if (!empty($data['slip_pembayaran'])) {
      $mail->AddAttachment('./uploads/slip_pembayaran/'.$data['slip_pembayaran']);
      //$mail->AddEmbeddedImage('./uploads/slip_pembayaran/'.$data->slip_pembayaran, 'slip_pembayaran');
    }
    // Konten/isi
     $data_['to'] = $to;
     $data_['nama_lengkap'] = $data['nama_lengkap'];
     $data_['jenjang'] = $data['jenjang'];
     $data_['nama_panitia'] = $data['nama_panitia'];
     $data_['tipe_pendaftaran'] = $data['tipe_pendaftaran'];
     $data_['transaksi'] = $data['transaksi'];
     $mailContent = $this->load->view('template_email_pendaftaran',$data_,true);
     $mail->Body = $mailContent;
    // Menambahakn lampiran

    // Kirim email
    if(!$mail->send()){
        //echo 'Pesan tidak dapat dikirim.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
    }else{
        //echo 'Pesan telah terkirim ';
    }
  }
}
