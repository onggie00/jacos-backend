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
        $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label;
        $nama_lengkap = $this->input->get("nama_lengkap");
        $total_biaya = $this->input->get("total_biaya");
        $total_biaya_terbilang = $this->input->get("total_biaya_terbilang");
        $no_transaksi = $this->input->get("no_transaksi");
        $get_siswa = $this->mymodel->withquery("select * from siswa_".strtolower($jenjang)." where id_siswa_".strtolower($jenjang)." = '".$id_siswa."'","row");
        $get_transaksi = $this->mymodel->withquery("select * from transaksi where no_transaksi = '".$no_transaksi."'","row");
        $nama_ortu = (!empty($get_siswa->nama_ibu) && $get_siswa->nama_ibu != "-") ? $get_siswa->nama_ibu : $get_siswa->nama_ayah;//$this->input->get("nama_ortu");
        $va_number = $get_transaksi->va_number;//$this->input->get("va_number");
        $tahun_ajaran = $this->mymodel->getbywhere("tahun_ajaran_psb","id","1","row")->label;
        $nama_lengkap = $get_siswa->nama_lengkap;//$this->input->get("nama_lengkap");
        $total_biaya = (!empty($get_transaksi->total_biaya)) ? $get_transaksi->total_biaya : $this->input->get("total_biaya");
        $total_biaya_terbilang = (!empty($get_transaksi->total_biaya)) ? terbilang($get_transaksi->total_biaya)." Rupiah"  : $this->input->get("total_biaya_terbilang");
        $jalur = $this->input->get("jalur");
        $jenis_kwitansi = $this->input->get("jenis_kwitansi");
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

        //$data->no_peserta = $no_peserta;
        $datas = array(
          "tipe_siswa" => $tipe_siswa, 
          "jenjang" => $jenjang, 
          "nama_ortu" => $nama_ortu, 
          "nama_lengkap" => $nama_lengkap, 
          "va_number" => $va_number, 
          "tahun_ajaran" => $tahun_ajaran, 
          "total_biaya" => $total_biaya, 
          "total_biaya_terbilang" => $total_biaya_terbilang , 
          "id_siswa" => $id_siswa, 
          "no_transaksi" => $no_transaksi, 
          "jalur" => $jalur , 
          "jenis_kwitansi" => $jenis_kwitansi,
          "tipe_tagihan" => $get_transaksi->tipe_tagihan
        );

        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        if($get_transaksi->tipe_tagihan == "open"){
          $sisa_tagihan = $get_transaksi->total_biaya;
          $termin = 0;
          //get riwayat
          $total_pay = 0;
          $arrBri = [];

          $va = $get_transaksi->va_number;
          // $va='2147483647';  
          $riwayat = $this->mymodel->getbywhere("payment_response_bri", "briva_no", $va, "result");
          $table_riwayat = "<table border='1' cellspacing='0'><tr><th style='width: 100px;padding: 5px 10px;text-align: center;'>Virtual Account</th><th style='width: 100px;padding: 5px 10px;text-align: center;'>Tanggal</th><th style='width: 150px;padding: 5px 10px;text-align: center;'>Nominal</th></tr>";
          foreach ($riwayat as $key => $i) {
            $total_pay += $i->bill_amount;
            $arrBri[] = $i;
            $table_riwayat .= "<tr>";
            $table_riwayat .= "<td style='text-align: center;padding: 10px;'>";
            $table_riwayat .= $i->briva_no;
            $table_riwayat .= "</td>";
            $table_riwayat .= "<td style='text-align: center;padding: 10px;'>";
            $table_riwayat .= date("d-m-Y H:i", strtotime($i->transaction_date))." WIB";
            $table_riwayat .= "</td>";
            $table_riwayat .= "<td style='text-align: right;padding: 10px 5px;'>";
            $table_riwayat .= formatIDR($i->bill_amount);
            $table_riwayat .= "</td>";
            $table_riwayat .= "</tr>";
            $termin++;
          }
          $table_riwayat .= "</table>";
          $datas['riwayat'] = $table_riwayat;
          $datas['sisa_tagihan'] = $sisa_tagihan-$total_pay;
          $datas['termin'] = $termin;
          $datas['total_biaya'] = $total_pay;
          $this->load->view('template_kwitansi_open', $datas);
        }else{
          $this->load->view('template_kwitansi', $datas);
        }
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = $no_transaksi.'-'.$data->nama_lengkap.'.pdf';
        //var_dump($data);
        $pdf->Output(FCPATH.'/uploads/kwitansi/'.$nama_file, 'F');
        $data->url_file = base_url('uploads/kwitansi/').$nama_file;

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