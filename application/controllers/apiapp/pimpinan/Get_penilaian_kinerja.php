<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_penilaian_kinerja extends REST_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index_get()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
                $headers[strtolower($name)] = $value;
        }
        $token =  $headers['x-token'];
        if (empty($token)){
            $token =  $headers['x-api-token'];
        }
        $jenjang = $this->get('jenjang');
        $id_pimpinan = $this->mymodel->withquery("select id_pimpinan from pimpinan_$jenjang where token='".$token."'","row")->id_pimpinan;
        $tahun_ajaran = $this->get('tahun_ajaran');

        if($jenjang=="sd"){
            $tabel="laporan_kinerja_pimpinan";
            $tipe_user = "pimpinan_sd";
        }elseif($jenjang=="smp"){
            $tabel="laporan_kinerja_pimpinan";
            $tipe_user = "pimpinan_smp";
        }elseif($jenjang=="sma"){
            $tabel="laporan_kinerja_pimpinan";
            $tipe_user = "pimpinan_sma";
        }else if($jenjang=="staff"){
            $tabel="laporan_kinerja_staff";
            $tipe_user = "staff";
        }

        if (!empty($tahun_ajaran)){
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_pimpinan, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.kepribadian_sosial, t.leadership, t.pengembangan_sekolah, t.bidang_tugas_wakil_akademik_kesiswaan, t.total_skor, t.rank, t.tahun_ajaran from $tabel t 
            join pimpinan_$jenjang g on t.id_pimpinan = g.id_pimpinan where t.id_pimpinan='".$id_pimpinan."' and t.jenjang = '".strtolower($jenjang)."' and t.tahun_ajaran='".$tahun_ajaran."' order by t.id_laporan DESC","result");
        }
        else{
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_pimpinan, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.kepribadian_sosial, t.leadership, t.pengembangan_sekolah, t.bidang_tugas_wakil_akademik_kesiswaan, t.total_skor, t.rank, t.tahun_ajaran from $tabel t 
        join pimpinan_$jenjang g on t.id_pimpinan = g.id_pimpinan where t.id_pimpinan='".$id_pimpinan."' and t.jenjang = '".strtolower($jenjang)."' order by t.id_laporan DESC","result");
        }

        if (!empty($data)) {
            foreach($data as $key => $value){
                $value->link_rapor = base_url("apiapp/export_rapor_kinerja_pimpinan?jenjang=".strtolower($jenjang)."&id=".$value->id_laporan);
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
