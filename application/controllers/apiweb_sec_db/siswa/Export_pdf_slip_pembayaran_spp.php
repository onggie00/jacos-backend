<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_slip_pembayaran_spp extends MY_Controller {
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

      if(isset($headers['user_role']))
        $role =  $headers['user_role'];

        $email = $this->input->get('email');
        $data = null;
        //cek data ft, sma, smp, sd
        $id_siswa = $this->input->get("id_siswa");
        $tipe_siswa = $this->input->get("tipe_siswa");
        if ($tipe_siswa == "ft") {
          $data = $this->second_db->withquery("select * from siswa_ft where id_siswa_ft = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sma") {
          $data = $this->second_db->withquery("select * from siswa_sma where id_siswa_sma = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "smp") {
          $data = $this->second_db->withquery("select * from siswa_smp where id_siswa_smp = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sd") {
          $data = $this->second_db->withquery("select * from siswa_sd where id_siswa_sd = '".$id_siswa."'","row");
        }

        if (!empty($data)) {

        //$data->no_peserta = $no_peserta;
        $datas['siswa'] = $data;
        
        $datas['tipe_siswa'] = $tipe_siswa;
        $datas['expired_datetime'] = $this->input->get("expired_datetime");
        $datas['va_number'] = $this->input->get("va_number");
        $datas['total_biaya'] = $this->input->get("total_biaya");
        $datas['detail_bulan'] = $this->input->get("detail_bulan");
        $datas['mohon_dibaca'] = '';
        $datas['logo']=($tipe_siswa=='sd')?'logo_bri.png':'logo_bni.png';
        $datas['id_tahun_ajaran'] = $this->input->get('id_tahun_ajaran');
        $pdf = new HTML2PDF('L', 'A5', 'en');
        ob_start();
        
        $this->load->view('template_slip_pembayaran_spp', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = $this->input->get("tagihan_code").'-'.str_replace(' ', '_', $data->nama_lengkap).'.pdf';
        //var_dump($data);
        $pdf->Output(FCPATH.'/uploads/slip_pembayaran/'.$nama_file, 'F');
        //$pdf->Output(FCPATH.'/uploads/kartu_peserta/'.$data->no_transaksi.'.pdf', 'F');

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