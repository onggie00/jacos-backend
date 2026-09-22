<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_mail extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
      $this->load->library('HtmlPdf');
      $status = "";
      $token = "";
      $headers=array();
      foreach (getallheaders() as $name => $value) {
          $headers[$name] = $value;
      }
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $id_mail = $this->input->get('id_mail');
        $data = null;
        
        $get_mail = $this->mymodel->getbywhere("mailbox_mail", "id_mail", $id_mail, "row");
        if(!empty($get_mail) && !empty($get_mail->mail_content) && !empty($get_mail->mail_address) && !empty($get_mail->mail_date)){
          $get_signature = $this->mymodel->withquery("select sign_name, sign_position, sign_signature, sign_order from mailbox_mail_signed where id_mail = '".$get_mail->id_mail."' order by sign_order ASC","result");
          $where = "";
          if (!empty($this->input->get('npp'))){
            $where = " and npp = '".$this->input->get('npp')."'";
          }
          else{
            $where = " and npp = '0123456789'";
          }
          $get_recipient = $this->mymodel->withquery("select id as id_detail,npp, nama_lengkap, role from mailbox_recipient where id_mail = '".$get_mail->id_mail."' $where","result");
          foreach($get_recipient as $key => $value){
            $data_update = array(
              'is_read' => 1,
              'updated_at' => date('Y-m-d H:i:s')
            );
            $update = $this->mymodel->update("mailbox_recipient", $data_update, "id", $value->id_detail);
          }
          if(empty($get_recipient)){
            $get_recipient[0]->npp = "0123456789";
            $get_recipient[0]->nama_lengkap = "N/A";
            $get_recipient[0]->role = "N/A";
          }
          if(!empty($get_mail->mail_header_file)){
            $get_mail->mail_header_file = FCPATH . '/uploads/mailbox_mail/' . $get_mail->mail_header_file;
          }
          $get_mail->mail_date = formatTanggal($get_mail->mail_date);
          $get_mail->mail_time = (!empty($get_mail->mail_time)) ? date("H:i", strtotime($get_mail->mail_time))." WIB" : "";
          $datas['mail_data'] = $get_mail;
          $datas['signature'] = $get_signature;
          $datas['recipient'] = $get_recipient;

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_mailbox', $datas);
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $pdf->Output('MAILBOX '.date("d-m-Y").'-'.$get_mail->mail_number.'.pdf', 'I');

          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}