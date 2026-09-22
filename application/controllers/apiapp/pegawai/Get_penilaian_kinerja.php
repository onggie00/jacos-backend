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
                $headers[$name] = $value;
        }
        $token =  $headers['x-token'];
        if (empty($token)){
            $token =  $headers['x-api-token'];
        }
        $jenjang = $this->get('jenjang');
        $id_pegawai = $this->mymodel->withquery("select id_pegawai from pegawai where token='".$token."'","row")->id_pegawai;
        $tahun_ajaran = $this->get('tahun_ajaran');

        $tabel="laporan_kinerja_staff";
        $tipe_user = "staff";

        if (!empty($tahun_ajaran)){
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_staff as id_pegawai, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.nilai_pimpinan, t.nilai_sejawat, t.nilai_sendiri, t.nilai_prestasi, t.nilai_presensi, t.total_skor, t.rank, t.tahun_ajaran from $tabel t 
            join pegawai g on t.id_staff = g.id_pegawai where t.id_staff='".$id_pegawai."' and t.tahun_ajaran='".$tahun_ajaran."' order by t.id_laporan DESC","result");
        }
        else{
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_staff as id_pegawai, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.nilai_pimpinan, t.nilai_sejawat, t.nilai_sendiri, t.nilai_prestasi, t.nilai_presensi, t.total_skor, t.rank, t.tahun_ajaran from $tabel t 
        join pegawai g on t.id_staff = g.id_pegawai where t.id_staff='".$id_pegawai."' order by t.id_laporan DESC","result");
        }

        if (!empty($data)) {
            foreach($data as $key => $value){
                $value->link_rapor = base_url("apiapp/export_rapor_kinerja?tipe_rapor=".$tipe_user."&id=".$value->id_laporan);
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
