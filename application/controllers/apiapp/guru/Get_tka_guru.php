<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';

class Get_tka_guru extends REST_Controller {
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
        $id_guru = $this->mymodel->withquery("select id_guru from guru_$jenjang where token='".$token."'","row")->id_guru;
        $tahun = $this->get('tahun');

        $tabel="laporan_tka_guru_".$jenjang;
        $tipe_guru = "guru_".$jenjang;

        if (!empty($tahun)){
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_guru as id_guru, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.bhs_indonesia, t.bhs_inggris, t.numerasi, t.total_skor, t.rank, t.tahun from $tabel t 
            join guru_".$jenjang." g on t.id_guru = g.id_guru 
            where t.id_guru='".$id_guru."' and t.tahun='".$tahun."' order by t.id_laporan DESC","result");
        }
        else{
            $data = $this->mymodel->withquery("select t.id_laporan, t.id_guru as id_guru, g.nama_lengkap, t.unit_kerja, t.umur, t.masa_kerja, t.golongan, t.bhs_indonesia, t.bhs_inggris, t.numerasi, t.total_skor, t.rank, t.tahun from $tabel t 
            join guru_".$jenjang." g on t.id_guru = g.id_guru 
            where t.id_guru='".$id_guru."' order by t.id_laporan DESC","result");
        }

        if (!empty($data)) {
            foreach($data as $key => $value){
                $value->link_rapor = base_url("apiapp/export_tka_guru?tipe_guru=".$tipe_guru."&id=".$value->id_laporan);
                $value->rank = null;
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
