<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_kwitansi extends MY_Controller {
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
        $jenjang = $this->input->get("jenjang");
        $nama_ortu = $this->input->get("nama_ortu");
        $va_number = $this->input->get("va_number");
        $tahun_ajaran = $this->second_db->getbywhere("tahun_ajaran_psb","id","1","row")->label;
        $nama_lengkap = $this->input->get("nama_lengkap");
        $total_biaya = $this->input->get("total_biaya");
        $total_biaya_terbilang = $this->input->get("total_biaya_terbilang");
        $no_transaksi = $this->input->get("no_transaksi");
        $jalur = $this->input->get("jalur");
        $jenis_kwitansi = $this->input->get("jenis_kwitansi");
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
        $datas = array("tipe_siswa" => $tipe_siswa, "jenjang" => $jenjang, "nama_ortu" => $nama_ortu, "nama_lengkap" => $nama_lengkap, "va_number" => $va_number, "tahun_ajaran" => $tahun_ajaran, "total_biaya" => $total_biaya, "total_biaya_terbilang" => $total_biaya_terbilang , "id_siswa" => $id_siswa, "no_transaksi" => $no_transaksi, "jalur" => $jalur , "jenis_kwitansi" => $jenis_kwitansi);

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kwitansi', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = $no_transaksi.'-'.$data->nama_lengkap.'.pdf';
        //var_dump($data);
        //$pdf->Output(FCPATH.'/uploads/kwitansi/'.$nama_file, 'F');
        $pdf->Output('KWITANSI PENDAFTARAN '.$jenjang.'-'.$nama_lengkap.'.pdf', 'I');

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