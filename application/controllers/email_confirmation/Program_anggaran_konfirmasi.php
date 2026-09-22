<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/application/controllers/apiapp/phpmailer/PHPMailerAutoload.php';
ob_start();

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Program_anggaran_konfirmasi extends MY_Controller {
    function __construct()
    {
        //parent::__construct();
    }
    public function index()
    {
      //echo "test";
      $this->load->view('form_confirmation');
    }

    public function konfirmasi(){
      $id_program = $this->input->post("id_program");
      $jenjang = $this->input->post("jenjang");
      $pengajuan_laporan = $this->input->post("pengajuan_laporan");
      $catatan = $this->input->post("catatan");
      $status = "";
      if (!empty($this->input->post("btn_reject"))) {
        $status = "5";
        $status_email = "DITOLAK";
      }
      else if(!empty($this->input->post("btn_approve"))){
        $status = "4";
        $status_email = "DISETUJUI";
      }
      $get_program = $this->mymodel->getbywhere("program_anggaran_".strtolower($jenjang), "id", $id_program, "row");
      if (!empty($get_program)) {
        if ($pengajuan_laporan == "pengajuan") {
          $data["status_pengajuan"] = $status;
          $data["catatan_pengajuan"] = $catatan;
        }
        else if ($pengajuan_laporan == "laporan") {
          $data["status_laporan"] = $status;
          if ($status == 4) {
            $data["status"] = 2;
          }
          else if($status == 5){
            $data["status"] = 1;
          }
          else{
            $data["status"] = 0;
          }
          $data["catatan_laporan"] = $catatan;
        }

        if ($status == "4" && $pengajuan_laporan == "pengajuan") {
          $data["status_laporan"] = "1";
          $data["status"] = 0;
        }
        $data["token_confirmation"] = null;
        $update = $this->mymodel->update("program_anggaran_".strtolower($jenjang), $data, "id", $id_program);
        if ($update) {
          $get_penerima = $this->mymodel->getbywhere("program_anggaran_email", "jenjang", strtoupper($jenjang),"result");
          foreach ($get_penerima as $key => $value) {
            //kirim email ke Bagian Anggaran
            $data_email["email"] = $value->email;
            $data_email["jenis_anggaran"] = strtoupper($jenjang);
            $data_email["pengajuan_laporan"] = $pengajuan_laporan;
            $data_email["data_anggaran"] = $get_program;
            $data_email["status"] = $status_email;
            $this->send_email_file("",$data_email['email'],$data_email); 
          }
          $this->session->set_flashdata('success', 'Confirmation Success, Email already Sent');
        }
        else{
          $this->session->set_flashdata('failed', 'Confirmation Error, Please contact administrator for help');
        }
      }
      $this->load->view('result_confirmation');
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
      /*$mail->Username = 'sekretariat@labschoolcibubur.sch.id';
      $mail->Password = 'Son05743';*/
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      $mail->addReplyTo('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');
      $mail->setFrom('noreply@labschoolcibubur.sch.id', 'Labschool Cibubur');

      // Menambahkan penerima
      $mail->addAddress($to);

      // Menambahkan beberapa penerima


      // Subjek email
      $judul = 'PEMBERITAHUAN '.strtoupper($data['pengajuan_laporan']).' PROGRAM DAN ANGGARAN '.$data['jenis_anggaran'];
      $mail->Subject = '[No Reply] '.$judul;

      // Mengatur format email ke HTML
      $mail->isHTML(true);
      //$mail->AddEmbeddedImage('./assets/image/admin/bg_footer_mail_black.png', 'bg_footer_mail_black'); //ini yg dipakai utk
      //$mail->addStringAttachment(file_get_contents(base_url("assets/image/admin/")."bg_footer_mail"), "bg_footer_mail");
      
      // Konten/isi
       $data_['to'] = $to;
       $data_['judul'] = $judul;
       $data_['jenis_anggaran'] = $data['jenis_anggaran'];
       $data_['pengajuan_laporan'] = $data['pengajuan_laporan'];
       $data_['data_anggaran'] = $data['data_anggaran'];
       $data_['status'] = $data['status'];
       $mailContent = $this->load->view('template_email_konfirmasi',$data_,true);
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
