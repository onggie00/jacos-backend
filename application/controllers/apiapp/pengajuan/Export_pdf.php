<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_pdf extends MY_Controller {
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

        $id_submission = $this->input->get('id_submission');
        $data = null;

        $data = $this->mymodel->withquery("select p.*, t.name as submission_name from presensi_office_submission p 
        join presensi_submission_type t on t.code = p.submission_code
        where p.id_submission = '$id_submission'","row");
        if (!empty($data)) {
        
            $datas['submission'] = $data;
            $pdf = new HTML2PDF('P', 'A4', 'en');
            ob_start();
            
            if(strtoupper($data->head_role)=="OFFICE"){
                $datas['yth'] = "Kepala Sekretariat Labschool Cibubur ";
            }
            else if(strtoupper($data->head_role)=="SMA" || strtoupper($data->head_role)=="SMP" || strtoupper($data->head_role)=="SD"){
                $datas['yth'] = "Kepala Sekolah ".strtoupper($data->head_role)." Labschool Cibubur";
            }
            else{
                $datas['yth'] = "Kepala Divisi ".strtoupper($data->head_role)." Labschool Cibubur";
            }
            $total_hari = $this->hitungHariIzin($data->date_start, $data->date_end);

            if($data->submission_code == "SAKIT"){
                $datas['kalimat_permohonan'] = "dengan ini mengajukan permintaan izin ".strtolower($data->submission_name)." selama ".$total_hari." (".angkaKeHuruf($total_hari).") hari kerja, terhitung mulai tanggal ".formatTanggalRange($data->date_start, $data->date_end);
            }
            else {
                $datas['kalimat_permohonan'] = "dengan ini mengajukan permintaan ".strtolower($data->submission_name)." untuk tahun ".date('Y', strtotime($data->created_at))." selama ".$total_hari." (".angkaKeHuruf($total_hari).") hari kerja, terhitung mulai tanggal ".formatTanggalRange($data->date_start, $data->date_end);
            }
            $datas['kalimat_permohonan'] = wordwrap($datas['kalimat_permohonan'], 100, "<br>");
            $datas['kalimat_penutup'] = "Demikianlah permintaan ini saya buat untuk dapat dipertimbangkan sebagaimana mestinya.";
            $this->load->view('template_submission', $datas);
            //$html="<html><h1>This is test pdf</h1></html>";
            $html = ob_get_contents(); 
            ob_end_clean();

            $pdf->WriteHTML($html);
            $pdf->Output('Surat Permintaan '.$data->submission_name.' '.$data->nama_lengkap.'.pdf', 'I');
        }
        else{
            echo json_encode(array("success" => false, "message" => "Data tidak ditemukan"));
        }
    }

    function hitungHariIzin($date_start, $date_end) {
        $start = new DateTime($date_start);
        $end   = new DateTime($date_end);
    
        // Pastikan urutan tanggal benar
        if ($start > $end) return 0;
    
        $total = 0;
        $current = clone $start;
    
        while ($current <= $end) {
            $day_of_week = (int) $current->format('N'); // 1=Senin ... 7=Minggu
            if ($day_of_week < 6) {
                // 1-5 = Senin s/d Jumat
                $total++;
            }
            $current->modify('+1 day');
        }
    
        return $total;
    }
}