<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

class Generate_all_tagihan extends REST_Controller {
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
        $get_siswa=null;
        $id_siswa=$this->post('id_siswa_aktif');
        $id_tahun_ajaran=$this->post('id_tahun_ajaran');
        $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran", "id_tahun_ajaran", $id_tahun_ajaran, "row");
        $code_tahun_ajaran=$tahun_ajaran->code;

        if($this->post('jenjang')=='sd'){
          $join='join siswa_sd s on s.id_siswa_sd = sa.id_siswa_sd join kelas_sd k on k.id_kelas_sd = sa.id_kelas join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran';
          $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,k.label as kelas from siswa_sd_aktif sa $join where sa.id_siswa_sd_aktif =".$id_siswa,'row');
          $bank='BRI';
          if($get_siswa){
            $get_biaya = $this->mymodel->getbywhere("tingkatan_sd", "id_tingkatan_sd", $get_siswa->id_tingkatan, "row");
          }

        }elseif($this->post('jenjang')=='smp'){
          $join='join siswa_smp s on s.id_siswa_smp = sa.id_siswa_smp join kelas_smp k on k.id_kelas_smp = sa.id_kelas join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran';
          $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,k.label as kelas from siswa_smp_aktif sa $join where sa.id_siswa_smp_aktif =".$id_siswa,'row');
          $bank='BNI';
          if($get_siswa){
            $get_biaya = $this->mymodel->getbywhere("tingkatan_smp", "id_tingkatan_smp", $get_siswa->id_tingkatan, "row");
          }

        }elseif($this->post('jenjang')=='sma'){
          $join='join siswa_sma s on s.id_siswa_sma = sa.id_siswa_sma join kelas_sma k on k.id_kelas_sma = sa.id_kelas join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran';
          $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,k.label as kelas from siswa_sma_aktif sa $join where sa.id_siswa_sma_aktif =".$id_siswa,'row');
          $bank='BNI';
          if($get_siswa){
            $get_biaya = $this->mymodel->getbywhere("tingkatan_sma", "id_tingkatan_sma", $get_siswa->id_tingkatan, "row");
          }

        }elseif($this->post('jenjang')=='ft'){
          $join='join siswa_ft s on s.id_siswa_ft = sa.id_siswa_ft join kelas_ft k on k.id_kelas_ft = sa.id_kelas join tahun_ajaran t on t.id_tahun_ajaran = sa.id_tahun_ajaran';
          $get_siswa = $this->mymodel->withquery("select sa.*,s.*,k.*,t.*,k.label as kelas from siswa_ft_aktif sa $join where sa.id_siswa_ft_aktif =".$id_siswa,'row');
          $bank='BNI';
          if($get_siswa){
            $get_biaya = $this->mymodel->getbywhere("tingkatan_ft", "id_tingkatan_ft", $get_siswa->id_tingkatan, "row");
          }

        }
        if(!$get_siswa){
          $msg = array('status' => 0, 'message'=>'Siswa tidak ada' ,'data'=>array());
          $status="200";
          $this->response($msg,$status);
        }

       
        $jenjang=strtoupper($this->post('jenjang'));

        $cek_transaksi=$this->mymodel->withquery("select * from transaksi_spp where no_transaksi LIKE '%LISPP-".$jenjang."-".$code_tahun_ajaran."-".$id_siswa."%'","row");
        if(!$cek_transaksi){

          $f=strtotime($tahun_ajaran->tanggal_mulai);
          $first=date('Y-m-01', $f);
          $l=strtotime($tahun_ajaran->tanggal_selesai);
          $last=date('Y-m-01', $l);
          
          if($get_siswa->spp_custom!=0 && $get_siswa->spp_custom!=null){
            $biaya = $get_siswa->spp_custom;
          }else{
            $biaya=$get_biaya->biaya_spp;
          }

          $detail_spp = null;
          if (date("Y-m-d") > $tahun_ajaran->tanggal_selesai) {
            $detail_spp=array(
              "id_siswa_aktif" => $id_siswa,
              "nama" => $get_siswa->nama_lengkap,
              "kelas" => $get_siswa->kelas,
              "tahun_ajaran" => $get_siswa->label,
              "id_tahun_ajaran" => $id_tahun_ajaran,
              "nominal" => $biaya,
              "juni" => NULL,//"-",
              "juli" => NULL,//"-",
              "agustus" => NULL,//"-",
              "september" => NULL,//"-",
              "oktober" => NULL,//"-",
              "november" => NULL,//"-",
              "desember" => NULL,//"-",
              "januari" => NULL,//"-",
              "februari" => NULL,//"-",
              "maret" => NULL,//"-",
              "april" => NULL,//"-",
              "mei" => NULL,//"-",
            );
          }
          else{
            $detail_spp=array(
              "id_siswa_aktif" => $id_siswa,
              "nama" => $get_siswa->nama_lengkap,
              "kelas" => $get_siswa->kelas,
              "tahun_ajaran" => $get_siswa->label,
              "id_tahun_ajaran" => $id_tahun_ajaran,
              "nominal" => $biaya
            );
          }
          
          if($jenjang=='SD'){
            //cek apakah sudah ada transaksi tsb
            $cek_data = $this->mymodel->withquery("select * from spp_sd where id_siswa_aktif = '".$id_siswa."' and tahun_ajaran = '".$get_siswa->label."'","result");
            if (empty($cek_data)) {
              $id_detail_spp = $this->mymodel->insertid("spp_sd",$detail_spp);
            }
          }elseif($jenjang=='SMP'){
            $cek_data = $this->mymodel->withquery("select * from spp_smp where id_siswa_aktif = '".$id_siswa."' and tahun_ajaran = '".$get_siswa->label."'","result");
            if (empty($cek_data)) {
              $id_detail_spp = $this->mymodel->insertid("spp_smp",$detail_spp);
            }
          }elseif($jenjang=='SMA'){
            $cek_data = $this->mymodel->withquery("select * from spp_sma where id_siswa_aktif = '".$id_siswa."' and tahun_ajaran = '".$get_siswa->label."'","result");
            if (empty($cek_data)) {
              $id_detail_spp = $this->mymodel->insertid("spp_sma",$detail_spp);
            }
          }elseif($jenjang=='FT'){
            $cek_data = $this->mymodel->withquery("select * from spp_ft where id_siswa_aktif = '".$id_siswa."' and tahun_ajaran = '".$get_siswa->label."'","result");
            if (empty($cek_data)) {
              $id_detail_spp = $this->mymodel->insertid("spp_ft",$detail_spp);
            }
          }

          // dd($biaya);
          $arrMonth=$this->echoDate(strtotime($first),strtotime($last));
          // dd($arrMonth);
          if (date("Y-m-d") < $tahun_ajaran->tanggal_selesai) {
            foreach($arrMonth as $i){
              $date = DateTime::createFromFormat('Y_m', $i);
              $last_date=$date->format('Y-m-t');
              $bulan=explode("_", $i);
              $nama_bulan=get_bulan((int)$bulan[1]);
              $no_transaksi='LISPP-'.$jenjang.'-'.$code_tahun_ajaran.'-'.$id_siswa.'-'.$nama_bulan;
              $data_transaksi = array(
                "no_transaksi" => $no_transaksi,
                "nama_bank" => $bank,
                "user_email" => $get_siswa->email,
                "user_name" => $get_siswa->nama_lengkap,
                "user_phone" => "",
                "description" => "-Tagihan SPP Bulan ".$nama_bulan." ".$bulan[0] ,
                "id_biaya_pendaftaran" => 1,
                "total_biaya" => $biaya,
                "status_transaksi" => "0",
                "created_at" => date("Y-m-d H:i:s"),
                "bulan" => $nama_bulan,
                "id_siswa_aktif" => $id_siswa,
                "id_tahun_ajaran" => $id_tahun_ajaran,
                "id_spp" => $id_detail_spp,
                "deadline" => $last_date
              );
              // dd($data_transaksi);
              $id_transaksi = $this->mymodel->insertid("transaksi_spp",$data_transaksi);
            }
          }

          $msg = array('status' => 1, 'message'=>'Generate SPP berhasil' ,'data'=>array());
          $status="200";
        }else{
          $msg = array('status' => 0, 'message'=>'Tagihan SPP sudah ada' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }

    function echoDate( $start, $end ){

      $current = $start;

      $ret = array();

      while( $current<=$end ){
          
          
          $format=date('Y_m',$current);
          $ret[] = $format;
          $current = @date('Y-M-01', $current) . "+1 month";
          $current = @strtotime($current);
      }

      return $ret;
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
        $endpoint = site_url('/apiapp/siswa/export_pdf_slip_pembayaran');
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

    function create_billing($production, $total, $no_transaksi, $data_user){
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
        'datetime_expired' => date('c', time() + $date_va), // billing will be expired in 6 hours
        'virtual_account' => $data_user['va_number'],
        'customer_name' => $data_user['nama'],
        'customer_email' => $data_user['email'],
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
        var_dump($rs, curl_error($ch));
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
