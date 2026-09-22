<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_tka_guru extends MY_Controller {
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

        $tipe_guru= $this->input->get('tipe_guru');
        $id_tka  = $this->input->get('id');
        $data = null;
        
        if($tipe_guru == 'staff'){
            $data = $this->mymodel->withquery("select p.id_pegawai, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_staff k 
			join pegawai p on k.id_staff = p.id_pegawai 
			where id_laporan = '".$id_tka."'","row");
        }
        else if($tipe_guru == 'guru_sd'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_sd k 
			join guru_sd p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_tka."'","row");
            $data->jenjang = "sd";
        }
        else if($tipe_guru == 'guru_smp'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_smp k 
			join guru_smp p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_tka."'","row");
            $data->jenjang = "smp";
        }
        else if($tipe_guru == 'guru_sma'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_sma k 
			join guru_sma p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_tka."'","row");
            $data->jenjang = "sma";
        }

        if (!empty($data)) {
            $total_user = 0;
            $total_skor = 0;
            //sd
            $get_all_data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_sd k 
            join guru_sd p on k.id_guru = p.id_guru
            where k.tahun = '".$data->tahun."' 
            order by k.tahun DESC, p.nama_lengkap ASC","result");
            $total_user += count($get_all_data);
            foreach ($get_all_data as $key => $value) {
                $total_skor += $value->bhs_indonesia + $value->bhs_inggris + $value->numerasi;
            }
            //smp
            $get_all_data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_smp k 
            join guru_smp p on k.id_guru = p.id_guru
            where k.tahun = '".$data->tahun."' 
            order by k.tahun DESC, p.nama_lengkap ASC","result");
            $total_user += count($get_all_data);
            foreach ($get_all_data as $key => $value) {
                $total_skor += $value->bhs_indonesia + $value->bhs_inggris + $value->numerasi;
            }
            //sma
            $get_all_data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.bhs_indonesia, k.bhs_inggris, k.numerasi, k.tahun from laporan_tka_guru_sma k 
            join guru_sma p on k.id_guru = p.id_guru
            where k.tahun = '".$data->tahun."' 
            order by k.tahun DESC, p.nama_lengkap ASC","result");
            $total_user += count($get_all_data);
            foreach ($get_all_data as $key => $value) {
                $total_skor += $value->bhs_indonesia + $value->bhs_inggris + $value->numerasi;
            }

            $data->rata_rata_all = round(($total_skor/ 3) / $total_user,2);

            $data->rata_rata_individu = round((($data->bhs_indonesia + $data->bhs_inggris + $data->numerasi) / 3),2);
            //predikat bhs_indonesia
            if ($data->bhs_indonesia >= 91) {
                $data->predikat_bhs_indonesia = 'Distinguished Teacher';
                $data->warna_bhs_indonesia = '#0002ee';
            }
            else if ($data->bhs_indonesia >= 81 && $data->bhs_indonesia <= 90) {
                $data->predikat_bhs_indonesia = 'Proficient Teacher';
                $data->warna_bhs_indonesia = '#0440f6';
            }
            else if($data->bhs_indonesia >= 65 && $data->bhs_indonesia <= 80) {
                $data->predikat_bhs_indonesia = 'Competent Teacher';
                $data->warna_bhs_indonesia = '#00a65a';
            }
            else if($data->bhs_indonesia >= 51 && $data->bhs_indonesia <= 64) {
                $data->predikat_bhs_indonesia = 'Developing Teacher';
                $data->warna_bhs_indonesia = '#c48f0f';
            }
            else{
                $data->predikat_bhs_indonesia = 'Intensive Improvement <br/>Teacher';
                $data->warna_bhs_indonesia = '#a7030a';
            }
            //predikat bhs_inggris
            if ($data->bhs_inggris >= 91) {
                $data->predikat_bhs_inggris = 'Distinguished Teacher';
                $data->warna_bhs_inggris = '#0002ee';
            }
            else if ($data->bhs_inggris >= 81 && $data->bhs_inggris <= 90) {
                $data->predikat_bhs_inggris = 'Proficient Teacher';
                $data->warna_bhs_inggris = '#0440f6';
            }
            else if($data->bhs_inggris >= 65 && $data->bhs_inggris <= 80) {
                $data->predikat_bhs_inggris = 'Competent Teacher';
                $data->warna_bhs_inggris = '#00a65a';
            }
            else if($data->bhs_inggris >= 51 && $data->bhs_inggris <= 64) {
                $data->predikat_bhs_inggris = 'Developing Teacher';
                $data->warna_bhs_inggris = '#c48f0f';
            }
            else{
                $data->predikat_bhs_inggris = 'Intensive Improvement <br/>Teacher';
                $data->warna_bhs_inggris = '#a7030a';
            }
            //predikat numerasi
            if ($data->numerasi >= 91) {
                $data->predikat_numerasi = 'Distinguished Teacher';
                $data->warna_numerasi = '#0002ee';
            }
            else if ($data->numerasi >= 81 && $data->numerasi <= 90) {
                $data->predikat_numerasi = 'Proficient Teacher';
                $data->warna_numerasi = '#0440f6';
            }
            else if($data->numerasi >= 65 && $data->numerasi <= 80) {
                $data->predikat_numerasi = 'Competent Teacher';
                $data->warna_numerasi = '#00a65a';
            }
            else if($data->numerasi >= 51 && $data->numerasi <= 64) {
                $data->predikat_numerasi = 'Developing Teacher';
                $data->warna_numerasi = '#c48f0f';
            }
            else{
                $data->predikat_numerasi = 'Intensive Improvement <br/>Teacher';
                $data->warna_numerasi = '#a7030a';
            }
            //predikat rata_rata_individu
            if ($data->rata_rata_individu >= 91) {
                $data->predikat_rata_rata_individu = 'Distinguished Teacher';
                $data->warna_rata_rata_individu = '#0002ee';
            }
            else if ($data->rata_rata_individu >= 81 && $data->rata_rata_individu <= 90) {
                $data->predikat_rata_rata_individu = 'Proficient Teacher';
                $data->warna_rata_rata_individu = '#0440f6';
            }
            else if($data->rata_rata_individu >= 65 && $data->rata_rata_individu <= 80) {
                $data->predikat_rata_rata_individu = 'Competent Teacher';
                $data->warna_rata_rata_individu = '#0440f6';
            }
            else if($data->rata_rata_individu >= 51 && $data->rata_rata_individu <= 64) {
                $data->predikat_rata_rata_individu = 'Developing Teacher';
                $data->warna_rata_rata_individu = '#c48f0f';
            }
            else{
                $data->predikat_rata_rata_individu = 'Intensive Improvement <br/>Teacher';
                $data->warna_rata_rata_individu = '#a7030a';
            }
            //predikat rata_rata_all
            if ($data->rata_rata_all >= 91) {
                $data->predikat_rata_rata_all = 'Distinguished Teacher';
                $data->warna_rata_rata_all = '#0002ee';
            }
            else if ($data->rata_rata_all >= 81 && $data->rata_rata_all <= 90) {
                $data->predikat_rata_rata_all = 'Proficient Teacher';
                $data->warna_rata_rata_all = '#0440f6';
            }
            else if($data->rata_rata_all >= 71 && $data->rata_rata_all <= 80) {
                $data->predikat_rata_rata_all = 'Competent Teacher';
                $data->warna_rata_rata_all = '#0440f6';
            }
            else if($data->rata_rata_all >= 51 && $data->rata_rata_all <= 64) {
                $data->predikat_rata_rata_all = 'Developing Teacher';
                $data->warna_rata_rata_all = '#c48f0f';
            }
            else{
                $data->predikat_rata_rata_all = 'Intensive Improvement <br/>Teacher';
                $data->warna_rata_rata_all = '#a7030a';
            }

            //tambahkan detail nilai guru (tabel laporan_tka_detail_jenjang) dan cetak pdf di halaman selanjutnya
            $get_detail_nilai = $this->mymodel->withquery("select li.judul_kategori, li.soal, li.no_urut, lt.keterangan, li.tahun from laporan_tka_detail_".strtolower($data->jenjang)." as lt 
            join laporan_tka_indikator as li on lt.id_indikator = li.id_indikator
            where id_laporan = '".$id_tka."' order by li.id_indikator asc","result");

            $data->detail_nilai = $get_detail_nilai;
            
            //if ada nilai siswa
            /* if (!empty($data->nilai_siswa)){
                if ($data->nilai_siswa >= 91) {
                    $data->predikat_siswa = 'Distinguished Teacher';
                    $data->warna_prestasi = '#0002ee';
                }
               else if ($data->nilai_siswa >= 81 && $data->nilai_siswa <= 90) {
                    $data->predikat_siswa = 'Proficient Teacher';
                    $data->warna_prestasi = '#0440f6';
                }
                else if($data->nilai_siswa >= 71 && $data->nilai_siswa <= 80) {
                    $data->predikat_siswa = 'Competent Teacher';
                    $data->warna_prestasi = '#00a65a';
                }
                else if($data->nilai_siswa >= 51 && $data->nilai_siswa <= 64) {
                    $data->predikat_siswa = 'Developing Teacher';
                    $data->warna_prestasi = '#c48f0f';
                }
                else{
                    $data->predikat_siswa = 'Intensive Improvement <br/>Teacher';
                    $data->warna_prestasi = '#a7030a';
                }
            } */

            $datas['tka'] = $data;
            $datas['tipe_guru'] = $tipe_guru;
            
            $pdf = new HTML2PDF('P', 'A4', 'en');
            ob_start();
            
            $this->load->view('template_tka_guru', $datas);
            $html = ob_get_contents(); 
            ob_end_clean();

            $pdf->WriteHTML($html);
            $nama_file = str_replace('TKA Guru -'.$data->nama_lengkap.' - '.$data->tahun, '_', $data->nama_lengkap).'.pdf';
            //var_dump($data);
            $pdf->Output($nama_file, 'I');
            //$pdf->Output(FCPATH.'/uploads/slip_pembayaran/'.$nama_file, 'D');
            //$pdf->Output(FCPATH.'/uploads/kartu_peserta/'.$data->no_transaksi.'.pdf', 'F');
            exit();

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