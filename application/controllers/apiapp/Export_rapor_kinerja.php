<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_rapor_kinerja extends MY_Controller {
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

        $tipe_rapor= $this->input->get('tipe_rapor');
        $id_rapor  = $this->input->get('id');
        $data = null;
        
        if($tipe_rapor == 'staff'){
            $data = $this->mymodel->withquery("select p.id_pegawai, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.rank, k.total_skor, k.tahun_ajaran from laporan_kinerja_staff k 
			join pegawai p on k.id_staff = p.id_pegawai 
			where id_laporan = '".$id_rapor."'","row");
        }
        else if($tipe_rapor == 'guru_sd'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.rank, k.total_skor, k.tahun_ajaran from laporan_kinerja_guru_sd k 
			join guru_sd p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_rapor."'","row");
        }
        else if($tipe_rapor == 'guru_smp'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.rank, k.total_skor, k.tahun_ajaran from laporan_kinerja_guru_smp k 
			join guru_smp p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_rapor."'","row");
        }
        else if($tipe_rapor == 'guru_sma'){
            $data = $this->mymodel->withquery("select p.id_guru, p.nama_lengkap, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.mata_pelajaran, k.kompetensi_pedagogik, k.kompetensi_profesional, k.kompetensi_kepribadian, k.kompetensi_sosial, k.leadership, k.nilai_prestasi, k.nilai_presensi, k.rank, k.total_skor, k.tahun_ajaran from laporan_kinerja_guru_sma k 
			join guru_sma p on k.id_guru = p.id_guru 
			where id_laporan = '".$id_rapor."'","row");
        }

        if (!empty($data)) {
            //predikat kompetensi_pedagogik
            if ($data->kompetensi_pedagogik >= 91) {
                $data->predikat_kompetensi_pedagogik = 'Sangat Baik';
                $data->warna_kompetensi_pedagogik = '#2F2FFF';
            }
            else if ($data->kompetensi_pedagogik >= 81 && $data->kompetensi_pedagogik <= 90) {
                $data->predikat_kompetensi_pedagogik = 'Baik';
                $data->warna_kompetensi_pedagogik = '#2C7BE5';
            }
            else if($data->kompetensi_pedagogik >= 71 && $data->kompetensi_pedagogik <= 80) {
                $data->predikat_kompetensi_pedagogik = 'Cukup';
                $data->warna_kompetensi_pedagogik = '#FFFF00';
            }
            else if($data->kompetensi_pedagogik > 60 && $data->kompetensi_pedagogik <= 70) {
                $data->predikat_kompetensi_pedagogik = 'Kurang';
                $data->warna_kompetensi_pedagogik = '#FF8040';
            }
            else{
                $data->predikat_kompetensi_pedagogik = 'Sangat Kurang';
                $data->warna_kompetensi_pedagogik = '#FF4A4A';
            }
            //predikat kompetensi_profesional
            if ($data->kompetensi_profesional >= 91) {
                $data->predikat_kompetensi_profesional = 'Sangat Baik';
                $data->warna_kompetensi_profesional = '#2F2FFF';
            }
            else if ($data->kompetensi_profesional >= 81 && $data->kompetensi_profesional <= 90) {
                $data->predikat_kompetensi_profesional = 'Baik';
                $data->warna_kompetensi_profesional = '#2C7BE5';
            }
            else if($data->kompetensi_profesional >= 71 && $data->kompetensi_profesional <= 80) {
                $data->predikat_kompetensi_profesional = 'Cukup';
                $data->warna_kompetensi_profesional = '#FFFF00';
            }
            else if($data->kompetensi_profesional > 60 && $data->kompetensi_profesional <= 70) {
                $data->predikat_kompetensi_profesional = 'Kurang';
                $data->warna_kompetensi_profesional = '#FF8040';
            }
            else{
                $data->predikat_kompetensi_profesional = 'Sangat Kurang';
                $data->warna_kompetensi_profesional = '#FF4A4A';
            }
            //predikat kompetensi_kepribadian
            if ($data->kompetensi_kepribadian >= 91) {
                $data->predikat_kompetensi_kepribadian = 'Sangat Baik';
                $data->warna_kompetensi_kepribadian = '#2F2FFF';
            }
            else if ($data->kompetensi_kepribadian >= 81 && $data->kompetensi_kepribadian <= 90) {
                $data->predikat_kompetensi_kepribadian = 'Baik';
                $data->warna_kompetensi_kepribadian = '#2C7BE5';
            }
            else if($data->kompetensi_kepribadian >= 71 && $data->kompetensi_kepribadian <= 80) {
                $data->predikat_kompetensi_kepribadian = 'Cukup';
                $data->warna_kompetensi_kepribadian = '#FFFF00';
            }
            else if($data->kompetensi_kepribadian > 60 && $data->kompetensi_kepribadian <= 70) {
                $data->predikat_kompetensi_kepribadian = 'Kurang';
                $data->warna_kompetensi_kepribadian = '#FF8040';
            }
            else{
                $data->predikat_kompetensi_kepribadian = 'Sangat Kurang';
                $data->warna_kompetensi_kepribadian = '#FF4A4A';
            }
            //predikat kompetensi_sosial
            if ($data->kompetensi_sosial >= 91) {
                $data->predikat_kompetensi_sosial = 'Sangat Baik';
                $data->warna_kompetensi_sosial = '#2F2FFF';
            }
            else if ($data->kompetensi_sosial >= 81 && $data->kompetensi_sosial <= 90) {
                $data->predikat_kompetensi_sosial = 'Baik';
                $data->warna_kompetensi_sosial = '#2C7BE5';
            }
            else if($data->kompetensi_sosial >= 71 && $data->kompetensi_sosial <= 80) {
                $data->predikat_kompetensi_sosial = 'Cukup';
                $data->warna_kompetensi_sosial = '#FFFF00';
            }
            else if($data->kompetensi_sosial > 60 && $data->kompetensi_sosial <= 70) {
                $data->predikat_kompetensi_sosial = 'Kurang';
                $data->warna_kompetensi_sosial = '#FF8040';
            }
            else{
                $data->predikat_kompetensi_sosial = 'Sangat Kurang';
                $data->warna_kompetensi_sosial = '#FF4A4A';
            }
            //predikat leadership
            if ($data->leadership >= 91) {
                $data->predikat_leadership = 'Sangat Baik';
                $data->warna_leadership = '#2F2FFF';
            }
            else if ($data->leadership >= 81 && $data->leadership <= 90) {
                $data->predikat_leadership = 'Baik';
                $data->warna_leadership = '#2C7BE5';
            }
            else if($data->leadership >= 71 && $data->leadership <= 80) {
                $data->predikat_leadership = 'Cukup';
                $data->warna_leadership = '#FFFF00';
            }
            else if($data->leadership > 60 && $data->leadership <= 70) {
                $data->predikat_leadership = 'Kurang';
                $data->warna_leadership = '#FF8040';
            }
            else{
                $data->predikat_leadership = 'Sangat Kurang';
                $data->warna_leadership = '#FF4A4A';
            }
            //predikat presensi
            if ($data->nilai_presensi >= 91) {
                $data->predikat_presensi = 'Sangat Baik';
                $data->warna_presensi = '#2F2FFF';
            }
            else if ($data->nilai_presensi >= 81 && $data->nilai_presensi <= 90) {
                $data->predikat_presensi = 'Baik';
                $data->warna_presensi = '#2C7BE5';
            }
            else if($data->nilai_presensi >= 71 && $data->nilai_presensi <= 80) {
                $data->predikat_presensi = 'Cukup';
                $data->warna_presensi = '#FFFF00';
            }
            else if($data->nilai_presensi > 60 && $data->nilai_presensi <= 70) {
                $data->predikat_presensi = 'Kurang';
                $data->warna_presensi = '#FF8040';
            }
            else{
                $data->predikat_presensi = 'Sangat Kurang';
                $data->warna_presensi = '#FF4A4A';
            }
            //predikat prestasi
            if ($data->nilai_prestasi >= 91) {
                $data->predikat_prestasi = 'Sangat Baik';
                $data->warna_prestasi = '#2F2FFF';
            }
            else if ($data->nilai_prestasi >= 81 && $data->nilai_prestasi <= 90) {
                $data->predikat_prestasi = 'Baik';
                $data->warna_prestasi = '#2C7BE5';
            }
            else if($data->nilai_prestasi >= 71 && $data->nilai_prestasi <= 80) {
                $data->predikat_prestasi = 'Cukup';
                $data->warna_prestasi = '#FFFF00';
            }
            else if($data->nilai_prestasi > 60 && $data->nilai_prestasi <= 70) {
                $data->predikat_prestasi = 'Kurang';
                $data->warna_prestasi = '#FF8040';
            }
            else{
                $data->predikat_prestasi = 'Sangat Kurang';
                $data->warna_prestasi = '#FF4A4A';
            }
            //if ada nilai siswa
            if (!empty($data->nilai_siswa)){
                if ($data->nilai_siswa >= 91) {
                    $data->predikat_siswa = 'Sangat Baik';
                    $data->warna_prestasi = '#2F2FFF';
                }
               else if ($data->nilai_siswa >= 81 && $data->nilai_siswa <= 90) {
                    $data->predikat_siswa = 'Baik';
                    $data->warna_prestasi = '#2C7BE5';
                }
                else if($data->nilai_siswa >= 71 && $data->nilai_siswa <= 80) {
                    $data->predikat_siswa = 'Cukup';
                    $data->warna_prestasi = '#FFFF00';
                }
                else if($data->nilai_siswa > 60 && $data->nilai_siswa <= 70) {
                    $data->predikat_siswa = 'Kurang';
                    $data->warna_prestasi = '#FF8040';
                }
                else{
                    $data->predikat_siswa = 'Sangat Kurang';
                    $data->warna_prestasi = '#FF4A4A';
                }
            }

        $datas['rapor'] = $data;
        $datas['tipe_rapor'] = $tipe_rapor;
        
        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_rapor_kinerja', $datas);
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = str_replace('Rapor Kinerja -'.$data->nama_lengkap.' - '.$data->tahun_ajaran, '_', $data->nama_lengkap).'.pdf';
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