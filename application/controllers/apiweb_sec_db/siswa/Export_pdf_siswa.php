<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_siswa extends MY_Controller {
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
          if (empty($data->no_peserta)) {
            $no_peserta = $data->nisn;
          }
          else{
            $no_peserta = $data->no_peserta;
          }

        $data->no_peserta = $no_peserta;
        $datas['siswa'] = $data;
        $get_waktu_materi = $this->second_db->withquery("select * from waktu_materi_tes where jenjang = '".$tipe_siswa."' order by tgl_ujian asc","result");
        if (!empty($get_waktu_materi)) {
          $hari_ujian = array();
          foreach ($get_waktu_materi as $key => $value) {
            //nentukan tgl
            array_push($hari_ujian, $value->tgl_ujian);
          }
          $hari_ujian = array_unique($hari_ujian);
          $convert_hari = "";
          $convert_tgl = "";
          foreach ($hari_ujian as $key => $value) {
            if ($convert_hari == "") {
              $convert_hari = formatHari($value);
            }
            else{
              $convert_hari = $convert_hari.", ".formatHari($value);
            }
            if ($convert_tgl == "") {
              $convert_tgl = date("d", strtotime($value));
            }
            else{
              $convert_tgl = $convert_tgl.", ".date("d", strtotime($value));
            }
            if (!empty($convert_tgl)) {
              $convert_tgl = $convert_tgl." ".formatBulan($value)." ".date("Y", strtotime($value));
            }
          }
        }
        $datas['waktu_materi'] = $get_waktu_materi;
        $datas['hari_ujian'] = $convert_hari;
        $datas['tgl_ujian'] = $convert_tgl;
        $datas['tipe_siswa'] = $tipe_siswa;
        $datas['kontak'] = $this->second_db->withquery("select k.*, t.tipe from kontak_labschool k join tipe_kontak t on k.tipe_kontak = t.id_tipe_kontak where jenjang = '".$tipe_siswa."'","result");
        $datas['catatan_kp'] = $this->second_db->withquery("select * from catatan_kartu_peserta where jenjang = '".$tipe_siswa."'","row");
        if($tipe_siswa=="ft"){
          $datas['title'] = "JADWAL TES";
        }else{
          $datas['title'] = "JADWAL READINESS OF LEARNING (ROLE) OBSERVATION";
        }
        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kartu_peserta', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        //$pdf->Output(FCPATH.'/uploads/kartu_peserta/'.$tipe_siswa.'-'.$no_peserta.'-'.$data->nama_lengkap.'.pdf', 'F');
        $pdf->Output('KARTU_PESERTA '.$tipe_siswa.'-'.$no_peserta.'-'.$data->nama_lengkap.'.pdf', 'I');

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