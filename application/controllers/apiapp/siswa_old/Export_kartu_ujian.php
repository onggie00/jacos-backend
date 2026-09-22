<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_kartu_ujian extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
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

        $data = null;
        //cek data ft, sma, smp, sd
        $id_siswa = $this->input->get("id_siswa_aktif");
        $tipe_siswa = $this->input->get("tipe_siswa");
        if ($tipe_siswa == "ft") {
          $data = $this->mymodel->withquery("select * from siswa_ft_aktif where id_siswa_ft_aktif = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sma") {
          $data = $this->mymodel->withquery("select * from siswa_sma_aktif where id_siswa_sma_aktif = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "smp") {
          $data = $this->mymodel->withquery("select * from siswa_smp_aktif where id_siswa_smp_aktif = '".$id_siswa."'","row");
        }

        if ($tipe_siswa == "sd") {
          $data = $this->mymodel->withquery("select * from siswa_sd_aktif where id_siswa_sd_aktif = '".$id_siswa."'","row");
        }

        if (!empty($data)) {
          if ($data->acc_ujian == '1'){
            $get_siswa = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, d.id_detail from ujian_ruang_detail d 
            left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas 
            left join siswa_".$tipe_siswa."_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian 
            where d.nomor_peserta_ujian = '".$data->nomor_peserta_ujian."' 
            order by d.urutan_kursi ASC limit 0,1","result");
          }
          else{
            $get_siswa = $this->mymodel->withquery("select d.*, k.kelas, k.id_tingkatan, k.id_ruang_kelas, s.nis, d.id_detail from ujian_ruang_detail d 
            left join ujian_ruang_kelas k on d.id_ruang_kelas = k.id_ruang_kelas 
            left join siswa_".$tipe_siswa."_aktif s on d.nomor_peserta_ujian = s.nomor_peserta_ujian 
            where d.boleh_ujian = 'YA' and d.nomor_peserta_ujian = '".$data->nomor_peserta_ujian."' 
            order by d.urutan_kursi ASC limit 0,1","result");
          }
          $get_ujian = $this->mymodel->withquery("select j.id_jadwal, j.id_jenis_ujian, j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, j.id_tahun_ajaran, m.nama_mapel from jadwal_ujian_".$tipe_siswa." j 
          join ujian_mapel_".$tipe_siswa." m on j.id_mapel = m.id_mapel 
          where j.id_tingkatan = '".$get_siswa[0]->id_tingkatan."' 
          order by tanggal ASC, jam_mulai ASC","result");
          
          $arr_ujian = array();
        foreach ($get_ujian as $key => $value) {
          $value->tgl_format = formatTanggal($value->tanggal);
            // Create a key using tanggal, jam_mulai, and jam_selesai
          $keyword = $value->tanggal . '|' . $value->jam_mulai . '|' . $value->jam_selesai;
          
          // If the key already exists, concatenate nama_mapel
          if (isset($arr_ujian[$keyword])) {
              $arr_ujian[$keyword]->nama_mapel .= '/ ' . $value->nama_mapel;
          } else {
              // Otherwise, create a new entry
              $arr_ujian[$keyword] = clone $value;
          }
        }
        $arr_ujian = array_values($arr_ujian);
        foreach ($get_siswa as $key => $value) {
          $value->judul_ujian = $this->mymodel->withquery("select ju.nama_ujian from jenis_ujian ju join jadwal_ujian_".$tipe_siswa." j on ju.id_jenis_ujian = j.id_jenis_ujian join ujian_ruang_kelas k on j.id_tingkatan = k.id_tingkatan where k.id_tingkatan = '".$get_siswa[0]->id_tingkatan."'","row")->nama_ujian;
        }

        //$get_nama_ujian = $this->mymodel->withquery("select nama_ujian from jenis_ujian where id_jenis_ujian = '".$get_ujian[0]->id_jenis_ujian."'","row");
        $tahun_ajaran = $this->mymodel->withquery("select label from tahun_ajaran where id_tahun_ajaran = '".$get_ujian[0]->id_tahun_ajaran."'","row");
        $get_ruang = $this->mymodel->withquery("select nama_ruang from ujian_ruang where id_ruang ='".$get_siswa[0]->id_ruang."'", "row");

        $datas['list_siswa'] = $get_siswa;
        $datas['jenjang'] = $tipe_siswa;
        $datas['ujian'] = $arr_ujian;
        $datas['tahun_ajar'] = $tahun_ajaran->label;
        //$datas['judul_ujian'] = $get_nama_ujian->nama_ujian;
        $datas['ruang'] = $get_ruang;
        $datas['today_date'] = formatTanggal(date("Y-m-d"));
        $datas['kepsek'] = $this->mymodel->withquery("select nama_kepsek,file_ttd from data_kepala_sekolah where jenjang = '".$tipe_siswa."'","row");

          if ($get_siswa[0]->boleh_ujian == "YA" || $data->acc_ujian == "1") {
            //catat tgl download
            $tgl_download_kartu = date("Y-m-d H:i:s");
            $this->mymodel->update("ujian_ruang_detail", array("tgl_download_kartu" => $tgl_download_kartu), "id_detail", $get_siswa[0]->id_detail); 
            $pdf = new HTML2PDF('P', 'A4', 'en');
            ob_start();
            
            $this->load->view('template_kartu_ujian', $datas);
            //$html="<html><h1>This is test pdf</h1></html>";
            $html = ob_get_contents(); 
            ob_end_clean();

            $pdf->WriteHTML($html);
            $nama_file = 'KARTU_UJIAN-'.$data->nomor_peserta_ujian."-".strtoupper(str_replace(" ","_", $data->nama_lengkap))."-".$tipe_siswa.'.pdf';
            // $pdf->Output(FCPATH.'/uploads/kartu_ujian/'.$nama_file, 'F');
            //$pdf->Output('KARTU_UJIAN '.$data->nomor_peserta_ujian."-".strtoupper($data->nama_lengkap)."-".$tipe_siswa.'.pdf', 'I');

              $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'boleh_ujian'=>$get_siswa[0]->boleh_ujian, 'kartu_ujian' => base_url().'apiapp/siswa/export_kartu_ujian_pdf?id_siswa_aktif='.$id_siswa."&tipe_siswa=".$tipe_siswa, 'nama_lengkap' => $get_siswa[0]->nama_siswa);
              $status="200";
          }
          else{
            $msg = array('status' => 0, 'message'=>'Generate Kartu Ujian Gagal, Harap melunasi Tagihan SPP dahulu.' ,'boleh_ujian'=>$get_siswa[0]->boleh_ujian, 'kartu_ujian' => '', 'nama_lengkap' => $get_siswa[0]->nama_siswa);
              $status="200";
          }
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}