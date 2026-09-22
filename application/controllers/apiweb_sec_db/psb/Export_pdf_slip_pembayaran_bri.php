<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_slip_pembayaran_bri extends MY_Controller {
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
          $data = $this->second_db->withquery("select * from calon_siswa_sma where id_siswa_sma = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sma") {
          $data = $this->second_db->withquery("select * from calon_siswa_sma where id_siswa_sma = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "smp") {
          $data = $this->second_db->withquery("select * from calon_siswa_smp where id_siswa_smp = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sd") {
          $data = $this->second_db->withquery("select * from calon_siswa_sd where id_siswa_sd = '".$id_siswa."'","row");
        }

        if (!empty($data)) {

        //$data->no_peserta = $no_peserta;
        $datas['siswa'] = $data;
        $get_transaksi = $this->second_db->withquery("select * from transaksi where no_transaksi = '".$data->no_transaksi."'","row");
        $no_transaksi = $data->no_transaksi;
        if (!empty($get_transaksi)) {
          if (!empty($get_transaksi->expired_datetime)) {
            $waktu = date("H:i", strtotime($get_transaksi->expired_datetime));
            $get_transaksi->expired_datetime = formatTanggal($get_transaksi->expired_datetime)." ".$waktu;
          }
        }
        $datas['tipe_siswa'] = $tipe_siswa;
        $datas['transaksi'] = $get_transaksi;
        $datas['mohon_dibaca'] = $this->second_db->withquery("select * from catatan_invoice","row")->catatan_mohon_dibaca;

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_slip_pembayaran_bri', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = $no_transaksi.'-'.$data->nama_lengkap.'.pdf';
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