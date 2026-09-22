<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Detail_raport extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_post()
    {
        $status = "";
        $token = "";
        $headers=array();

    foreach (getallheaders() as $name => $value) {
        $headers[$name] = $value;
    }
    if(isset($headers['x-token']))
        $token =  $headers['x-token'];
        $id = $this->post('id');
        $jenjang = $this->post('jenjang');
        $tahun_ajaran = $this->post('id_tahun_ajaran');
        
        $data = $this->mymodel->withquery("select * from siswa_".strtolower($jenjang)."_aktif_raport where id_siswa_aktif = ".$this->db->escape($id),"result");
        $get_tahun_ajaran = $this->mymodel->withquery("select * from tahun_ajaran where id_tahun_ajaran = '".$tahun_ajaran."'","row");
        if (!empty($data)) {
            foreach($data as $key => $value){
                //cek transaksi spp sesuai tanggal sync terakhir
                $bulan_ini = formatBulan($value->tanggal_sync_valid);
                $bulan_ini_kecil = strtolower($bulan_ini);
                $get_transaksi_spp = $this->mymodel->withquery("select * from spp_".strtolower($jenjang)." where id_siswa_aktif = '".$value->id_siswa_aktif."' and id_tahun_ajaran = '".$get_tahun_ajaran->id_tahun_ajaran."'", "row");
                //$get_transaksi = $this->mymodel->withquery("select status_transaksi, user_name ,bulan, id_siswa_aktif from transaksi_spp where id_spp = '".$get_transaksi_spp->id_spp."' and no_transaksi like '%".$jenjang."%' and status_transaksi = '2' and bulan = '".$bulan_ini."' and id_tahun_ajaran = '".$get_tahun_ajaran->id_tahun_ajaran."' order by id_transaksi DESC","row");
                if (!empty($value->file_raport) && !empty($get_transaksi_spp->$bulan_ini_kecil) ) {
                    $value->file_raport = base_url().'uploads/siswa_'.strtolower($jenjang).'_aktif_raport/'.$value->file_raport;
                    $value->is_allowed = 1;
                }
                else if(!empty($get_transaksi_spp->$bulan_ini_kecil)){
                    $value->file_raport = "";
                    $value->is_allowed = 1;
                }
                else{
                    $value->file_raport = "";
                    $value->is_allowed = $get_transaksi_spp->september;
                }
            }
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
