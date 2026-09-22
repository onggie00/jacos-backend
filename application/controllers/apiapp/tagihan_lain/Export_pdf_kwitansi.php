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
        $id_tagihan = $this->input->get("id_tagihan");
        $jenjang = $this->input->get("jenjang");
        $va_number = $this->input->get("va_number");
        $nama_lengkap = $this->input->get("nama_lengkap");
        $total_biaya = $this->input->get("total_biaya");
        $total_biaya_terbilang = $this->input->get("total_biaya_terbilang");
        $no_transaksi = $this->input->get("no_transaksi");

        $data = $this->mymodel->withquery("select t.*, tm.tipe_bank, tm.nama_transaksi, tm.keterangan, s.id_tahun_ajaran, ta.label as tahun_ajaran, s.id_kelas, k.label as nama_kelas, s.nama_lengkap from transaksi_lain_".strtolower($jenjang)." t join siswa_".strtolower($jenjang)."_aktif s on t.id_siswa_aktif = s.id_siswa_".strtolower($jenjang)."_aktif join kelas_".strtolower($jenjang)." k on s.id_kelas = k.id_kelas_".strtolower($jenjang)." join transaksi_lain_".strtolower($jenjang)."_manajemen tm on t.id_transaksi_lain = tm.id join tahun_ajaran ta on tm.id_tahun_ajaran = ta.id_tahun_ajaran where t.kode_tagihan = '". $no_transaksi."'" , "row");

        if (!empty($data)) {

        //$data->no_peserta = $no_peserta;
        $datas = array("id_tagihan" => $data->id, "jenjang" => $jenjang, "nama_transaksi" => $data->nama_transaksi, "nama_lengkap" => $nama_lengkap, "va_number" => $va_number, "tahun_ajaran" => $data->tahun_ajaran, "total_biaya" => $total_biaya, "total_biaya_terbilang" => $total_biaya_terbilang , "id_siswa" => $id_siswa, "no_transaksi" => $no_transaksi, "nama_kelas" => $data->nama_kelas , "tipe_bank" => $data->tipe_bank, "keterangan" => $data->keterangan);
        //print_r($datas);

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kwitansi_ot', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_lengkap = str_replace(" ", "_",$data->nama_lengkap);
        $nama_file = $no_transaksi.'-'.$nama_lengkap.'.pdf';
        //var_dump($data);
        $pdf->Output(FCPATH.'/uploads/transaksi_lain/'.$nama_file, 'F');
        $pdf->Output(FCPATH.'/uploads/transaksi_lain/'.$nama_file, 'I');

          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data, 'data_transaksi'=>$datas, 'file_pdf'=>base_url('uploads/transaksi_lain/'.$nama_file));
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array(), 'data_transaksi'=>$datas, 'file_pdf'=>base_url('uploads/transaksi_lain/'.$nama_file));
          $status="200";
        }

        $this->response($msg,$status);
    }
}