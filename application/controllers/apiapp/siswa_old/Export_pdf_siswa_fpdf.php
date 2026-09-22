<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf_siswa extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
      $this->load->library('fpdf');
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

        $email = $this->post('email');
        $data = null;
        //cek data ft, sma, smp, sd
        $id_siswa = $this->post("id_siswa");
        $tipe_siswa = $this->post("tipe_siswa");
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
          if (empty($data->no_peserta)) {
            $no_peserta = $data->nisn;
          }
          else{
            $no_peserta = $data->no_peserta;
          }
          $logo = FCPATH . "/uploads/logo_labschool.png";
          //eksport pdf
          $pdf = new FPDF('L', 'mm','A4');
          $pdf->AddPage();
          $pdf->SetLineWidth(2);
          $pdf->Ln(5);
          $pos_Y = 10;
          $pos_X = 50;
           $pdf->SetFont('Arial','I',12);
           $pdf->SetTextColor(128);

           //Your text cell
           $pdf->SetY($pos_Y);
           $pdf->SetX($pos_X);
           $pdf->writeHTML('This is my disclaimer. <b>THESE WORDS NEED TO BE BOLD.</b> These words do not need to be bold.');

           //Your bordered cell
           $pdf->SetY($pos_Y);
           $pdf->SetX($pos_X);
           $pdf->Cell($width, $height, '', 1, 0, 'C');

          $pdf->Line(10, 100, 285, 100);
          //$pdf->Cell( 40, 40, $pdf->Image($logo, $pdf->GetX(), $pdf->GetY(), 33.78), 0, 0, 'L', false );
          
          $pdf->SetLineWidth(1);
          $pdf->SetFont('Arial','B',12);
          //$pdf->SetMargins(5, 1 , 5);
          //$pdf->Cell(50,70,'Photo',1,1,'C');
          //$pdf->SetLineWidth(1);
          //$pdf->Line(65, 10, 65, 120);
          //$pdf->Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, boolean fill [, mixed link]]]]]]]);
          $pdf->Ln(50);
          $pdf->Cell(10,7,'',0,1);
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(10,6,'No',1,0,'C');
          $pdf->Cell(90,6,'Nama Lengkap',1,0,'C');
          $pdf->Cell(120,6,'Email',1,1,'C');
          $siswa = $data;
          $no=1;

            $pdf->Cell(10,6,$no,1,0, 'C');
            $pdf->Cell(90,6,$siswa->nama_lengkap,1,0);
            $pdf->Cell(120,6,$siswa->email,1,0,'C');
            $no++;

          $pdf->Output(FCPATH.'/uploads/kartu_peserta/'.$tipe_siswa.'-'.$no_peserta.'-'.$siswa->nama_lengkap.'.pdf','F');

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