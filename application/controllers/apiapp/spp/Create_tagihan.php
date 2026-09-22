<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') or exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Create_tagihan extends REST_Controller
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

    $jenjang = $this->post('jenjang');
    $id_siswa = $this->post('id_siswa_aktif');
    $id_trans = explode(',',$this->post('id_transaksi'));
    $total_biaya = $this->post('total_biaya');
    $thn_ajar = "";
    $va_number = "";
    $name = "";
    $email = "";
    foreach ($id_trans as $key => $item) {
      $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");
      $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran", "id_tahun_ajaran", $get_transaksi->id_tahun_ajaran, "row");
      $thn_ajar = $tahun_ajaran->code;
      $email = $get_transaksi->user_email;
      $name = $get_transaksi->user_name;
      $get_tahun_ajaran_lalu=$this->mymodel->withquery("select * from tahun_ajaran where sequence < $tahun_ajaran->sequence", "result");
      
      foreach($get_tahun_ajaran_lalu as $key => $item){
        $cek_exist_tagihan=$this->mymodel->withquery("select * from transaksi_spp where status_transaksi in(0,1) and user_name = '".$name."' and id_siswa_aktif = $id_siswa and id_tahun_ajaran = $item->id_tahun_ajaran", "result");
        
        if($cek_exist_tagihan){
          $msg = array('status' => 0, 'message'=>'Ada tagihan tahun lalu yang belum dibayarkan!' ,'data'=>array());
          $status="200";
          $this->response($msg,$status);
        };
      }
    }
    if($jenjang=='sd'){
      $msg = array('status' => 0, 'message'=>'Invalid Jenjang' ,'data'=>array());
      $status="200";
      $this->response($msg,$status);
    }else if($jenjang=='smp'){
      $code='60';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_smp as id_siswa from siswa_smp_aktif sa join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_smp k on k.id_kelas_smp = sa.id_kelas where id_siswa_smp_aktif = ".$id_siswa."","row");
    }else if($jenjang=='sma'){
      $code='70';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_sma as id_siswa from siswa_sma_aktif sa join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_sma k on k.id_kelas_sma = sa.id_kelas where id_siswa_sma_aktif = ".$id_siswa."","row");
    }else if($jenjang=='ft'){
      $code='70';
      $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,t.label as tahun_ajaran,s.nama_lengkap as nama_lengkap,s.id_siswa_ft as id_siswa from siswa_ft_aktif sa join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran join kelas_ft k on k.id_kelas_ft = sa.id_kelas where id_siswa_ft_aktif = ".$id_siswa."","row");
    }
    
    //get va prefix
    $get_setting = $this->mymodel->getall("pengaturan_akun");
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

    //cek va sudah tersedia / belum
    $get_transaksi = $this->mymodel->withquery("select va_number from transaksi_spp where user_name = '".$name."' and no_transaksi like '%".strtoupper($jenjang)."%'" , "row");
    if (!empty($get_transaksi) && $va_number == "") {
      $va_number = $get_transaksi->va_number;
    }

    if ($va_number == "") {
      $thn_ajar = substr($thn_ajar, 0,2);
      $va_number = $prefix.$client_id.$code.$thn_ajar.rand(1000,9999);
      $tagihan_code="SP".date("imy")."-".strtoupper($jenjang)."-".$va_number;
   
      $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $tagihan_code,$jenjang, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
    }
    else{
      $tagihan_code="SP".date("imy")."-".strtoupper($jenjang)."-".$va_number;
   
      $payment_response = $this->create_billing(ENVIRONMENT, $total_biaya, $tagihan_code,$jenjang, array("nama" => $get_siswa->nama_lengkap, "email" => $get_siswa->email, "va_number" => $va_number));
    }
    //print_r($payment_response);
    //echo "<br/>";
    //print_r($total_biaya."<br/>".$tagihan_code."<br/>".$jenjang."<br/>nama". $get_siswa->nama_lengkap."<br/>". "email"." ". $get_siswa->email."<br/>". "va_number"." ". $va_number);
    // $data['payment_respon']=$payment_response;
   
    if(!$payment_response['virtual_account']){
      $msg = array('status' => 0, 'message'=>'Terjadi Kesalahan Ketika Pembuatan VA' ,'data'=>array(), 'errResponse' => $payment_response);
      $status="200";
      $this->mymodel->insertid("error_log_bni",array("status"=>$payment_response['status'],"message"=>$payment_response['message'],"va_number"=>$va_number));
      $this->response($msg,$status);
    }
    $detail_bulan="";
    foreach ($id_trans as $key => $item) {
      $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");
      $detail_bulan.=($key==0)?$get_transaksi->bulan:",".$get_transaksi->bulan;
    }

    $this->cetak_slip(array(
                      "id_siswa" => $get_siswa->id_siswa, //dari id siswa bukan id siswa aktif
                      "tipe_siswa" => strtolower($jenjang), 
                      "detail_bulan" => $detail_bulan,
                      "total_biaya" => $total_biaya,
                      "va_number" => $va_number,
                      "tagihan_code" => $tagihan_code,
                      "expired_datetime" => date("Y-m-d H:i:s", strtotime("+24 hours")),
                      "id_tahun_ajaran" => $get_transaksi->id_tahun_ajaran
                    ));

    foreach ($id_trans as $item) {
      $get_transaksi = $this->mymodel->withquery("select * from transaksi_spp where id_transaksi = ". $item , "row");

        $data = array(
          "status_transaksi" => '1',
          "va_number" => $va_number,
          "kode_tagihan" => $tagihan_code,
          "expired_datetime" => date("Y-m-d H:i:s", strtotime("+24 hours")),
          "detail_bulan" => $detail_bulan,
          "count_bill" => count($id_trans),
          "file_slip" => $tagihan_code . '-' . str_replace(' ', '_', $get_siswa->nama_lengkap) . '.pdf',
          "updated_at" => date("Y-m-d H:i:s")
        );

        if(!empty($tagihan_code)){
          $this->mymodel->update("transaksi_spp", $data, "id_transaksi", $item);
        }
    }
    $this->mymodel->update("transaksi_spp", array("va_number" => $va_number), "user_name", $name);

    $msg = array('status' => 1, 'message' => 'Tagihan Berhasil Dibuat', 'data' => $payment_response['virtual_account']);
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

  function create_billing($production, $total, $no_transaksi,$jenjang, $data_user){
    $this->load->library('BniEnc');
    // FROM BNI
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    foreach ($get_setting as $key => $value) {
      if($jenjang=="ft"){
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
      }else{
        if ($value->name_setting == "bni_client_id_spp") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_spp") {
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
      'type' => "createbilling",
      'client_id' => $client_id,
      'trx_id' => $no_transaksi,
      'trx_amount' => $total,
      'billing_type' => 'c',
      'datetime_expired' => date("Y-m-d H:i:s", strtotime("+24 hours")), // billing will be expired in 6 hours
      'virtual_account' => $data_user['va_number'],
      'customer_name' => $data_user['nama'],
      'customer_email' => $data_user['email'],
      //'customer_phone' => $data_user['notelp'],
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

  function update_billing($production, $total, $no_transaksi,$jenjang, $data_user){
    $this->load->library('BniEnc');
    // FROM BNI
    $get_setting = $this->mymodel->getall("pengaturan_akun");
    foreach ($get_setting as $key => $value) {
      if($jenjang=="ft"){
        if ($value->name_setting == "bni_client_id_ft") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_ft") {
          $secret_key = $value->value;
        }
      }else{
        if ($value->name_setting == "bni_client_id_spp") {
          $client_id = $value->value;
        }
        if ($value->name_setting == "bni_secret_key_spp") {
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
