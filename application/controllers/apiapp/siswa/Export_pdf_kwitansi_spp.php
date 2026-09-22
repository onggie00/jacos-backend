<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_kwitansi_spp extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
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
        $id_siswa = $this->input->get("id_siswa");
        $tipe_siswa = $this->input->get("tipe_siswa");
        $jenjang = $this->input->get("jenjang");
        $va_number = $this->input->get("va_number");
        $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label;
        $nama_lengkap = $this->input->get("nama_lengkap");
        $total_biaya = $this->input->get("total_biaya");
        $total_biaya_terbilang = $this->input->get("total_biaya_terbilang");
        $no_transaksi = $this->input->get("no_transaksi");
        $jalur = $this->input->get("jalur");
        $detail_bulan = $this->input->get("detail_bulan");
        $get_transaksi_spp = $this->mymodel->getbywhere("transaksi_spp","no_transaksi",$no_transaksi,"row");
				if (strlen($detail_bulan) > 70) {

					$pos = strpos($detail_bulan, ',', 70);

					if ($pos !== false) {
						$detail_bulan =
							substr($detail_bulan, 0, $pos)
							. '<br/>'
							. substr($detail_bulan, $pos + 1);
					}
				}
        $this->load->library('HtmlPdf');
        //$data->no_peserta = $no_peserta;
        $datas = array("tipe_siswa" => $tipe_siswa, 
                       "jenjang" => $jenjang, 
                       "nama_lengkap" => $nama_lengkap, 
                       "va_number" => $va_number,
                       "tahun_ajaran" => $tahun_ajaran, 
                       "total_biaya" => $total_biaya, 
                       "total_biaya_terbilang" => $total_biaya_terbilang , 
                       "id_siswa" => $id_siswa, 
                       "detail_bulan" => $detail_bulan, 
                       "no_transaksi" => $no_transaksi, 
                       "tanggal_transaksi" => (!empty($get_transaksi_spp->updated_at)) ? date("d-m-Y", strtotime($get_transaksi_spp->updated_at)) : date("d-m-Y"),
                       "jalur" => $jalur );

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_kwitansi_spp', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = $no_transaksi.'-'.str_replace(' ','_',$nama_lengkap).'.pdf';
        //var_dump($data);
        $pdf->Output(FCPATH.'/uploads/kwitansi/'.$nama_file, 'F');

          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$datas);
          $status="200";

        $this->response($msg,$status);
    }
}