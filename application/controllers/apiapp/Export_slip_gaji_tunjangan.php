<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Export_slip_gaji_tunjangan extends MY_Controller {
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

        $tipe = $this->input->get('tipe');
        //$tipe_role = $this->input->get('tipe_role');
        $id = $this->input->get('id');
        $data = null;
        
        if($tipe == "thr"){
            $data = $this->mymodel->withquery("select * from pegawai_slip_custom where id_slip = '".$id."'","row");
        }
        else{
            $data = $this->mymodel->withquery("select s.* from pegawai_slip s where id_slip = '".$id."'","row");
        }
        
        $data_penghasilan = array();
        $data_potongan = array();

        if (!empty($data)) {
            //Penghasilan Table
            if(!empty($data->gaji_pokok) && $tipe != "tunjangan"){
                $data_penghasilan[] = array("tunjangan" => "Gaji Pokok", "jumlah" => format_nominal($data->gaji_pokok));
            }
            if(!empty($data->tunjangan_istri) && $tipe != "tunjangan"){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Istri", "jumlah" => format_nominal($data->tunjangan_istri));
            }
            if(!empty($data->tunjangan_anak) && $tipe != "tunjangan"){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Anak", "jumlah" => format_nominal($data->tunjangan_anak));
            }
            if(!empty($data->tunjangan_pengelolaan)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Pengelolaan", "jumlah" => format_nominal($data->tunjangan_pengelolaan));
            }
            if(!empty($data->tunjangan_jabatan)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Jabatan", "jumlah" => format_nominal($data->tunjangan_jabatan));
            }
            if(!empty($data->tunjangan_kesejahteraan)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Kesejahteraan", "jumlah" => format_nominal($data->tunjangan_kesejahteraan));
            }
            if(!empty($data->tunjangan_masa_kerja)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Masa Kerja", "jumlah" => format_nominal($data->tunjangan_masa_kerja));
            }
            if(!empty($data->tunjangan_fungsional)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Fungsional", "jumlah" => format_nominal($data->tunjangan_fungsional));
            }
            if(!empty($data->tunjangan_kehadiran)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Kehadiran", "jumlah" => format_nominal($data->tunjangan_kehadiran));
            }
            if(!empty($data->tunjangan_mengajar)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Mengajar", "jumlah" => format_nominal($data->tunjangan_mengajar));
            }
            if(!empty($data->tunjangan_piket)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Piket", "jumlah" => format_nominal($data->tunjangan_piket));
            }
            if(!empty($data->tunjangan_wali_kelas)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Wali Kelas", "jumlah" => format_nominal($data->tunjangan_wali_kelas));
            }
            if(!empty($data->tunjangan_pembina)){
                $data_penghasilan[] = array("tunjangan" => "Tunj. Pembina", "jumlah" => format_nominal($data->tunjangan_pembina));
            }
            if(!empty($data->tunjangan_insentif)){
                $data_penghasilan[] = array("tunjangan" => "Insentif", "jumlah" => format_nominal($data->tunjangan_insentif));
            }
            if(!empty($data->insentif_ft)){
                $data_penghasilan[] = array("tunjangan" => "Insenstif France Track", "jumlah" => format_nominal($data->insentif_ft));
            }
            if(!empty($data->bonus)){
                $data_penghasilan[] = array("tunjangan" => "Bonus", "jumlah" => format_nominal($data->bonus));
            }
            if(!empty($data->honor)){
                $data_penghasilan[] = array("tunjangan" => "Honor / Imbalan lain", "jumlah" => format_nominal($data->honor));
            }
            if(!empty($data->thr)){
                $data_penghasilan[] = array("tunjangan" => "THR", "jumlah" => format_nominal($data->thr));
            }
            if(!empty($data->gaji14)){
                $data_penghasilan[] = array("tunjangan" => "Gaji ke 14", "jumlah" => format_nominal($data->gaji14));
            }

            //Potongan Table
            if(!empty($data->bpjs_kesehatan)){
                $data_potongan[] = array("tunjangan" => "BPJS Kesehatan", "jumlah" => format_nominal($data->bpjs_kesehatan));
            }
            if(!empty($data->bpjs_ketenagakerjaan)){
                $data_potongan[] = array("tunjangan" => "BPJS Ketenagakerjaan", "jumlah" => format_nominal($data->bpjs_ketenagakerjaan));
            }
            if(!empty($data->bpjs_pensiun)){
                $data_potongan[] = array("tunjangan" => "BPJS Pensiun", "jumlah" => format_nominal($data->bpjs_pensiun));
            }
            if(!empty($data->iuran_dplk_bni)){
                $data_potongan[] = array("tunjangan" => "Iuran DPLK BNI", "jumlah" => format_nominal($data->iuran_dplk_bni));
            }
            if(!empty($data->simpanan_wajib_koperasi)){
                $data_potongan[] = array("tunjangan" => "Simpanan wajib koperasi", "jumlah" => format_nominal($data->simpanan_wajib_koperasi));
            }
            if(!empty($data->pinjaman_uang_koperasi)){
                $data_potongan[] = array("tunjangan" => "Pinjaman uang koperasi", "jumlah" => format_nominal($data->pinjaman_uang_koperasi));
            }
            if(!empty($data->pinjaman_barang_koperasi)){
                $data_potongan[] = array("tunjangan" => "Pinjaman barang koperasi", "jumlah" => format_nominal($data->pinjaman_barang_koperasi));
            }
            
            if(!empty($data->keterangan_lain1)){
                $data_potongan[] = array("tunjangan" => ucfirst($data->keterangan_lain1), "jumlah" => format_nominal($data->nominal_lain1));
            }
            if(!empty($data->keterangan_lain2)){
                $data_potongan[] = array("tunjangan" => ucfirst($data->keterangan_lain2), "jumlah" => format_nominal($data->nominal_lain2));
            }
            if(!empty($data->keterangan_lain3)){
                $data_potongan[] = array("tunjangan" => ucfirst($data->keterangan_lain3), "jumlah" => format_nominal($data->nominal_lain3));
            }
            if(!empty($data->keterangan_lain4)){
                $data_potongan[] = array("tunjangan" => ucfirst($data->keterangan_lain4), "jumlah" => format_nominal($data->nominal_lain4));
            }
            if(!empty($data->keterangan_lain5)){
                $data_potongan[] = array("tunjangan" => ucfirst($data->keterangan_lain5), "jumlah" => format_nominal($data->nominal_lain5));
            }
            if(!empty($data->pph21)){
                $data_potongan[] = array("tunjangan" => "PPH 21", "jumlah" => format_nominal($data->pph21));
            }
            $data->total_penghasilan_gaji = format_nominal($data->total_penghasilan_gaji);
            $data->total_potongan_gaji = format_nominal($data->total_potongan_gaji);
            $data->total_diterima_gaji = format_nominal($data->total_diterima_gaji);
            $data->total_penghasilan_tunjangan = format_nominal($data->total_penghasilan_tunjangan);
            $data->total_potongan_tunjangan = format_nominal($data->total_potongan_tunjangan);
            $data->total_diterima_tunjangan = format_nominal($data->total_diterima_tunjangan);

            $data->role = $tipe_role;
            $data->tanggal_gaji = "6 ".formatBulan(date("Y-m-d", strtotime($data->periode_selesai)))." ".date("Y", strtotime($data->periode_selesai));
            $data->tanggal_tunjangan = "25 ".formatBulan(date("Y-m-d", strtotime($data->periode_selesai)))." ".date("Y", strtotime($data->periode_selesai));
            $data->periode = formatTanggal($data->periode_mulai)." s.d. ".formatTanggal($data->periode_selesai);
            $datas['slip'] = $data;
            $datas['penghasilan'] = $data_penghasilan;
            $datas['potongan'] = $data_potongan;
        //print_r($data_potongan);
            $pdf = new HTML2PDF('P', 'A4', 'en');
            ob_start();
        
            if($tipe == "gaji") { 
                $this->load->view('template_slip_gaji', $datas);
            }
            else if($tipe == "tunjangan") {
                $this->load->view('template_slip_tunjangan', $datas);
            }
            else if($tipe == "gaji_kosong") {
                $this->load->view('template_slip_gaji_kosong', $datas);
            }
            else if($tipe == "tunjangan_kosong") {
                $this->load->view('template_slip_tunjangan_kosong', $datas);
            }
            else if($tipe == "thr"){
                $this->load->view('template_slip_thr', $datas);
            }
        
            $html = ob_get_contents(); 
            ob_end_clean();

            $pdf->WriteHTML($html);
            $nama_file = 'Slip '.ucfirst($tipe).' - '.$data->nama_lengkap.' - '.$data->tanggal_gaji.'.pdf';
            $nama_file = str_replace('Slip '.ucfirst($tipe).' - '.$data->nama_lengkap.' - '.$data->tanggal_gaji, '_', $data->nama_lengkap).'.pdf';
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