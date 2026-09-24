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
        if ($tipe_siswa == "kb") {
  $data = $this->mymodel->withquery("select * from siswa_kb where id_siswa_kb = '".$id_siswa."'","row");
}
        if ($tipe_siswa == "tk") {
  $data = $this->mymodel->withquery("select * from siswa_tk where id_siswa_tk = '".$id_siswa."'","row");
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
        $get_waktu_materi = $this->mymodel->withquery("select * from waktu_materi_tes where jenjang = '".$tipe_siswa."' order by tgl_ujian asc","result");
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
        $get_waktu_simulasi = $this->mymodel->withquery("select * from waktu_simulasi_tes where jenjang = '".$tipe_siswa."' order by tgl_ujian asc","result");
        if (!empty($get_waktu_simulasi)) {
          $hari_ujian = array();
          foreach ($get_waktu_simulasi as $key => $value) {
            //nentukan tgl
            array_push($hari_ujian, $value->tgl_ujian);
          }
          $hari_ujian = array_unique($hari_ujian);
          $convert_hari_simulasi = "";
          $convert_tgl_simulasi = "";
          foreach ($hari_ujian as $key => $value) {
            if ($convert_hari_simulasi == "") {
              $convert_hari_simulasi = formatHari($value);
            }
            else{
              $convert_hari_simulasi = $convert_hari_simulasi.", ".formatHari($value);
            }
            if ($convert_tgl_simulasi == "") {
              $convert_tgl_simulasi = date("d", strtotime($value));
            }
            else{
              $convert_tgl_simulasi = $convert_tgl_simulasi.", ".date("d", strtotime($value));
            }
            if (!empty($convert_tgl_simulasi)) {
              $convert_tgl_simulasi = $convert_tgl_simulasi." ".formatBulan($value)." ".date("Y", strtotime($value));
            }
          }
        }
        $datas['waktu_materi'] = $get_waktu_materi;
        $datas['waktu_simulasi'] = $get_waktu_simulasi;
        $datas['hari_ujian'] = $convert_hari;
        $datas['hari_ujian_simulasi'] = $convert_hari_simulasi;
        $datas['tgl_ujian'] = $convert_tgl;
        $datas['tgl_ujian_simulasi'] = $convert_tgl_simulasi;
        $datas['tipe_siswa'] = $tipe_siswa;
        $get_ruang = $this->mymodel->withquery("select u.id_ruang_pendaftaran, u.nama_ruang, u.lokasi_ujian, u.meeting_id, u.meeting_url, u.meeting_password, u.maks_peserta from ujian_ruang_pendaftaran_ft u 
        join ujian_ruang_pendaftaran_detail_ft d on u.id_ruang_pendaftaran = d.id_ruang_pendaftaran where d.nomor_peserta = '".$no_peserta."'","row");
        $datas['data_ruang_ujian'] = $get_ruang;
        $datas['kontak'] = $this->mymodel->withquery("select k.*, t.tipe from kontak_labschool k join tipe_kontak t on k.tipe_kontak = t.id_tipe_kontak where jenjang = '".$tipe_siswa."'","result");
        $datas['catatan_kp'] = $this->mymodel->withquery("select * from catatan_kartu_peserta where jenjang = '".$tipe_siswa."'","row");
        if($tipe_siswa=="ft"){
          $datas['title'] = "PELAKSANAAN TES";
          $datas['title_simulasi'] = "PELAKSANAAN SIMULASI";
        }else{
          $datas['title'] = "JADWAL READINESS OF LEARNING (ROLE) OBSERVATION";
          $datas['title_simulasi'] = "PELAKSANAAN SIMULASI";
        }
        $datas['tahun_ajaran_psb'] = $this->mymodel->getbywhere("tahun_ajaran_psb", "id", 1, "row")->label;
        ob_start();
        
        if(strtoupper($tipe_siswa)=="FT"){
          if (!empty($data->ppsbb)) {
            // PPSBB FT: landscape, sesuai template resmi panitia (field identitas dotted kosong)
            $pdf = new HTML2PDF('L', 'A4', 'en');
            $this->load->view('template_kartu_peserta_ppsbb_ft', $datas);
          }
          else{
            $pdf = new HTML2PDF('P', 'A4', 'en');
            $this->load->view('template_kartu_peserta_ft', $datas);
          }
        }
        else{
          $pdf = new HTML2PDF('P', 'A4', 'en');
          $this->load->view('template_kartu_peserta', $datas);
        }
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        //$pdf->Output(/*FCPATH.*/'https://admin.labschoolcibubur.sch.id'.'/uploads/kartu_peserta/'.$tipe_siswa.'-'.$no_peserta.'-'.$data->nama_lengkap.'.pdf', 'F');
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