<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_kartu_siswa_sementara extends MY_Controller {
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
          $data = $this->mymodel->withquery("select * from siswa_ft where id_siswa_ft = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sma") {
          $data = $this->mymodel->withquery("select * from siswa_sma where id_siswa_sma = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "smp") {
          $data = $this->mymodel->withquery("select * from siswa_smp where id_siswa_smp = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sd") {
          $data = $this->mymodel->withquery("select * from siswa_sd where id_siswa_sd = '".$id_siswa."'","row");
        }

        if (!empty($data)) {
          $data->tgl_lahir = formatTanggal($data->tgl_lahir);
          $data->kota = $this->mymodel->withquery("select name from regencies where id = '".$data->kota."'","row")->name;
          $tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran_psb","row");

        $datas['siswa'] = $data;
        $datas['tipe_siswa'] = $tipe_siswa;
        $datas['tahun_ajar'] = $tahun_ajaran->label;
        $datas['catatan_kartu_siswa'] = $this->mymodel->withquery("select catatan from catatan_kartu_siswa_sementara where jenjang = '".$tipe_siswa."'","row")->catatan;
        $datas['kepsek'] = $this->mymodel->withquery("select * from data_kepala_sekolah where jenjang = '".$tipe_siswa."'","row");

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kartu_siswa_sementara', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        //$pdf->Output(/*FCPATH.*/'https://admin.labschoolcibubur.sch.id'.'/uploads/kartu_siswa_sementara/'.$tipe_siswa.'-'.$data->no_peserta.'-'.$data->nama_lengkap.'.pdf', 'F');
        $pdf->Output('KARTU SISWA SEMENTARA '.$tipe_siswa.'-'.$data->no_peserta.'-'.$data->nama_lengkap.'.pdf', 'I');

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