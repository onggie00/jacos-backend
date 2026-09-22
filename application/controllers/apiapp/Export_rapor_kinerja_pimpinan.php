<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_rapor_kinerja_pimpinan extends MY_Controller {
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

        $jenjang= $this->input->get('jenjang');
        $id_rapor  = $this->input->get('id');
        $data = null;
        
        if(!empty($jenjang)){
            $data = $this->mymodel->withquery("select p.id_pimpinan, p.nama_lengkap, k.jenjang, k.jabatan, p.npp, k.unit_kerja, k.umur, k.masa_kerja, k.golongan, k.kepribadian_sosial, k.leadership, k.pengembangan_sekolah, k.bidang_tugas_wakil_akademik_kesiswaan, k.rank, k.total_skor, k.tahun_ajaran from laporan_kinerja_pimpinan k 
			join pimpinan_".strtolower($jenjang)." p on k.id_pimpinan = p.id_pimpinan 
			where k.id_laporan = '".$id_rapor."'","row");
        }
        else{
            $msg = array('status' => 0, 'message'=>'Data Input tidak valid' ,'data'=>null);
            $status="200";
            return $this->response($msg,$status);
        }

        if (!empty($data)) {
            //predikat kepribadian_sosial
            if ($data->kepribadian_sosial >= 91) {
                $data->predikat_kepribadian_sosial = 'Sangat Baik';
                $data->warna_kepribadian_sosial = '#2F2FFF';
            }
            else if ($data->kepribadian_sosial >= 81 && $data->kepribadian_sosial <= 90) {
                $data->predikat_kepribadian_sosial = 'Baik';
                $data->warna_kepribadian_sosial = '#2C7BE5';
            }
            else if($data->kepribadian_sosial >= 71 && $data->kepribadian_sosial <= 80) {
                $data->predikat_kepribadian_sosial = 'Cukup';
                $data->warna_kepribadian_sosial = '#2C7BE5';
            }
            else if($data->kepribadian_sosial > 60 && $data->kepribadian_sosial <= 70) {
                $data->predikat_kepribadian_sosial = 'Kurang';
                $data->warna_kepribadian_sosial = '#FF8040';
            }
            else{
                $data->predikat_kepribadian_sosial = 'Sangat Kurang';
                $data->warna_kepribadian_sosial = '#FF4A4A';
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
            //predikat pengembangan_sekolah
            if ($data->pengembangan_sekolah >= 91) {
                $data->predikat_pengembangan_sekolah = 'Sangat Baik';
                $data->warna_pengembangan_sekolah = '#2F2FFF';
            }
            else if ($data->pengembangan_sekolah >= 81 && $data->pengembangan_sekolah <= 90) {
                $data->predikat_pengembangan_sekolah = 'Baik';
                $data->warna_pengembangan_sekolah = '#2C7BE5';
            }
            else if($data->pengembangan_sekolah >= 71 && $data->pengembangan_sekolah <= 80) {
                $data->predikat_pengembangan_sekolah = 'Cukup';
                $data->warna_pengembangan_sekolah = '#FFFF00';
            }
            else if($data->pengembangan_sekolah > 60 && $data->pengembangan_sekolah <= 70) {
                $data->predikat_pengembangan_sekolah = 'Kurang';
                $data->warna_pengembangan_sekolah = '#FF8040';
            }
            else{
                $data->predikat_pengembangan_sekolah = 'Sangat Kurang';
                $data->warna_pengembangan_sekolah = '#FF4A4A';
            }
            //predikat bidang_tugas_wakil_akademik_kesiswaan
            if ($data->bidang_tugas_wakil_akademik_kesiswaan >= 91) {
                $data->predikat_bidang_tugas_wakil_akademik_kesiswaan = 'Sangat Baik';
                $data->warna_bidang_tugas_wakil_akademik_kesiswaan = '#2F2FFF';
            }
            else if ($data->bidang_tugas_wakil_akademik_kesiswaan >= 81 && $data->bidang_tugas_wakil_akademik_kesiswaan <= 90) {
                $data->predikat_bidang_tugas_wakil_akademik_kesiswaan = 'Baik';
                $data->warna_bidang_tugas_wakil_akademik_kesiswaan = '#2C7BE5';
            }
            else if($data->bidang_tugas_wakil_akademik_kesiswaan >= 71 && $data->bidang_tugas_wakil_akademik_kesiswaan <= 80) {
                $data->predikat_bidang_tugas_wakil_akademik_kesiswaan = 'Cukup';
                $data->warna_bidang_tugas_wakil_akademik_kesiswaan = '#FFFF00';
            }
            else if($data->bidang_tugas_wakil_akademik_kesiswaan > 60 && $data->bidang_tugas_wakil_akademik_kesiswaan <= 70) {
                $data->predikat_bidang_tugas_wakil_akademik_kesiswaan = 'Kurang';
                $data->warna_bidang_tugas_wakil_akademik_kesiswaan = '#FF8040';
            }
            else{
                $data->predikat_bidang_tugas_wakil_akademik_kesiswaan = 'Sangat Kurang';
                $data->warna_bidang_tugas_wakil_akademik_kesiswaan = '#FF4A4A';
            }

        $datas['rapor'] = $data;
        
        $pdf = new HTML2PDF('P', 'A4', 'en');
        ob_start();
        
        $this->load->view('template_rapor_kinerja_pimpinan', $datas);
        $html = ob_get_contents(); 
        ob_end_clean();

        $pdf->WriteHTML($html);
        $nama_file = str_replace('Rapor Kinerja Pimpinan -'.$data->nama_lengkap.' - '.$data->tahun_ajaran, '_', $data->nama_lengkap).'.pdf';
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