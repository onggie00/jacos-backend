<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Lihat_sertifikat extends MY_Controller {
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

        $npp = $this->input->get('npp');
        $role = $this->input->get('role');
        $id_acara = $this->input->get('id_acara');
        $data = null;
        //cek data pegawai, guru ft, guru sma, guru smp, guru sd
        if ($role == "guru_ft") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from guru_ft where npp = ".$this->db->escape($npp),"row");
        }

        if ($role == "guru_sma") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from guru_sma where npp = ".$this->db->escape($npp),"row");
        }

        if ($role == "guru_smp") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from guru_smp where npp = ".$this->db->escape($npp),"row");
        }

        if ($role == "guru_sd") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from guru_sd where npp = ".$this->db->escape($npp),"row");
        }

        if ($role == "pegawai") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from pegawai where npp = ".$this->db->escape($npp),"row");
        }

        if ($role == "pimpinan") {
            $data = $this->mymodel->withquery("select nama_lengkap, npp from pimpinan_sma where npp = ".$this->db->escape($npp),"row");
            if(empty($data)){
                $data = $this->mymodel->withquery("select nama_lengkap, npp from pimpinan_smp where npp = ".$this->db->escape($npp),"row");
                if(empty($data)){
                    $data = $this->mymodel->withquery("select nama_lengkap, npp from pimpinan_sd where npp = ".$this->db->escape($npp),"row");
                }
            }
        }

        if (!empty($data)) {
        $get_acara = $this->mymodel->withquery("select * from acara where id_acara = ".$this->db->escape($id_acara),"row");

        $datas['user'] = $data;
        $datas['acara'] = $get_acara;
        $get_presensi = $this->mymodel->withquery("select * from acara_presensi where id_acara = '".$get_acara->id_acara."' order by waktu_presensi ASC","result");
        $urutan = 0;
        foreach ($get_presensi as $key => $value) {
            $urutan++;
            if ($value->npp == $npp) {
                break;
            }
        }
        $urutan = sprintf("%d", $urutan);
        $start_num = (isset($get_acara->start_number_certificate) && (int)$get_acara->start_number_certificate > 0) ? (int)$get_acara->start_number_certificate : 1;
        $effective_num = $start_num + ($urutan - 1);
        $datas['nomor_sertifikat'] = (!empty($get_acara->no_certificate)) ? $get_acara->no_certificate . sprintf("%d", $effective_num) : "";
        $datas['tanggal_acara'] = formatHari($get_acara->waktu_mulai).", ".formatTanggal($get_acara->waktu_mulai)." - ".formatTanggal($get_acara->waktu_selesai);
        $datas['tanggal_sertifikat'] = formatTanggal($get_acara->waktu_selesai);

        // Nama instansi otomatis dari role (hanya guru)
        $datas['nama_instansi'] = '';
        if (strpos($role, 'guru_') !== false) {
            $jenjang_map = array(
                'guru_sd'  => 'SD',
                'guru_smp' => 'SMP',
                'guru_sma' => 'SMA',
                'guru_ft'  => 'FT',
            );
            $jenjang = isset($jenjang_map[$role]) ? $jenjang_map[$role] : '';
            if (!empty($jenjang)) {
                $datas['nama_instansi'] = strtoupper($jenjang) . ' LABSCHOOL CIBUBUR';
            }
        }

        // Fallback default sesuai hardcode lama (NULL = belum di-set admin)
        $has_nomor = !empty($datas['nomor_sertifikat']);
        $datas['style_sertifikat'] = array(
            'no_pos_top'     => isset($get_acara->sertifikat_no_pos_top) ? (float)$get_acara->sertifikat_no_pos_top : 19.0,
            'no_pos_left'    => isset($get_acara->sertifikat_no_pos_left) ? (float)$get_acara->sertifikat_no_pos_left : 23.1,
            'no_font_size'   => isset($get_acara->sertifikat_no_font_size) ? (int)$get_acara->sertifikat_no_font_size : 16,
            'nama_pos_top'   => isset($get_acara->sertifikat_nama_pos_top) ? (float)$get_acara->sertifikat_nama_pos_top : ($has_nomor ? 6.5 : 13.1),
            'nama_pos_left'  => isset($get_acara->sertifikat_nama_pos_left) ? (float)$get_acara->sertifikat_nama_pos_left : 13.0,
            'nama_font_size' => isset($get_acara->sertifikat_nama_font_size) ? (int)$get_acara->sertifikat_nama_font_size : 30,
            'show_npp'       => isset($get_acara->sertifikat_show_npp) ? (int)$get_acara->sertifikat_show_npp : 0,
            'npp_pos_top'    => isset($get_acara->sertifikat_npp_pos_top) ? (float)$get_acara->sertifikat_npp_pos_top : 20.9,
            'npp_pos_left'   => isset($get_acara->sertifikat_npp_pos_left) ? (float)$get_acara->sertifikat_npp_pos_left : 13.0,
            'npp_font_size'  => isset($get_acara->sertifikat_npp_font_size) ? (int)$get_acara->sertifikat_npp_font_size : 12,
            'instansi_pos_top'   => isset($get_acara->sertifikat_instansi_pos_top) ? (float)$get_acara->sertifikat_instansi_pos_top : 18.0,
            'instansi_pos_left'  => isset($get_acara->sertifikat_instansi_pos_left) ? (float)$get_acara->sertifikat_instansi_pos_left : 13.0,
            'instansi_font_size' => isset($get_acara->sertifikat_instansi_font_size) ? (int)$get_acara->sertifikat_instansi_font_size : 18,
            'show_instansi'      => isset($get_acara->sertifikat_show_instansi) ? (int)$get_acara->sertifikat_show_instansi : 1,
            'no_font_style'         => isset($get_acara->sertifikat_no_font_style) ? $get_acara->sertifikat_no_font_style : 'normal',
            'nama_font_style'       => isset($get_acara->sertifikat_nama_font_style) ? $get_acara->sertifikat_nama_font_style : 'bold_italic',
            'instansi_font_style'   => isset($get_acara->sertifikat_instansi_font_style) ? $get_acara->sertifikat_instansi_font_style : 'normal',
            'npp_font_style'        => isset($get_acara->sertifikat_npp_font_style) ? $get_acara->sertifikat_npp_font_style : 'normal',
        );

        $pdf = new HTML2PDF('L', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_sertifikat', $datas);
        //$html="<html><h1>This is test pdf</h1></html>";
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        //Present the file
        $pdf->Output($get_acara->nama_acara.'-'.strtoupper($role).'-'.$data->nama_lengkap.'.pdf', 'I');
        exit;
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }
}