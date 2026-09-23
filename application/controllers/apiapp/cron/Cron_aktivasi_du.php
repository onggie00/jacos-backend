<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_aktivasi_du extends MY_Controller {
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $status = "";
        $token = "";
        $headers=array();
        foreach (getallheaders() as $name => $value) {
            $headers[$name] = $value;
        }
        if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $today = date("Y-m-d");

        $get_pengaturan_masa_aktif = $this->mymodel->getall("pengaturan_masa_aktif_va");
		foreach ($get_pengaturan_masa_aktif as $key => $item) {
			if ($item->label == 'daftar_ulang') {
				if ($item->tipe_date == 'day') {
					$date_va = ($item->value * 24);
				} else {
					$date_va = $item->value;
				}
			}
		}

        $list_jenjang = ["SD", "SMP", "SMA", "FT"];
        //cron 
        for($i = 0; $i < count($list_jenjang); $i++){
        $jenjang = strtolower($list_jenjang[$i]);
        // get biaya pendaftaran
		$get_biaya = $this->mymodel->getbywhere("biaya_pendaftaran", "jenjang", $jenjang, "row");
		$total_biaya = $get_biaya->nominal_daftar_ulang;
            if($jenjang == "sd"){
                $jenjang = "sd";
                $data_siswa = $this->mymodel->withquery("select st.id_daftar_ulang, st.id_siswa_".$jenjang." as id_siswa, st.status, st.va_bri as va_number, st.tgl_daftar_ulang, st.tgl_aktivasi, st.tgl_bayar, st.custom_payment, s.nama_lengkap from status_daftar_ulang_".$jenjang." st 
                join siswa_".$jenjang." s on s.id_siswa_".$jenjang." =  as id_siswa
                where st.tgl_daftar_ulang = '".$today."' ","result");
            }
            else{
                $data_siswa = $this->mymodel->withquery("select st.id_daftar_ulang, st.id_siswa_".$jenjang." as id_siswa, st.status, st.va_bri as va_number, st.tgl_daftar_ulang, st.tgl_aktivasi, st.tgl_bayar, st.custom_payment, s.nama_lengkap from status_daftar_ulang_".$jenjang." st 
                join siswa_".$jenjang." s on s.id_siswa_".$jenjang." = st.id_siswa_".$jenjang."
                where st.tgl_daftar_ulang = '".$today."' ","result");
            }

            if(!empty($data_siswa)){
                foreach($data_siswa as $key => $value){
                    $va_number = "";
                    $va_number = $value->va_number;
                    if (!empty($value->custom_payment)) {
                        $total_biaya = $value->custom_payment;
                    }
                    //aktivasi va 
                    $datas = array(
                        'brivaNo' => $brivaNo,
                        'custCode' => substr($va_number, 5),
                        'nama' => $value->nama_lengkap,
                        'amount' => $total_biaya,
                        'keterangan' => 'Pembayaran Daftar Ulang '.$jenjang.' '.date('d-m-Y', strtotime($value->tgl_daftar_ulang)),
                        'expiredDate' => date("Y-m-d H:i:s", strtotime($date_va))
                    );
        
                    $delete_response = $this->delete_va_bri(array("brivaNo" => $datas["brivaNo"], "custCode" => $datas["custCode"]));
                    $payment_response = $this->create_va_bri($datas);

                    $data_update = array(
                        'status' => '1',
                        'tgl_aktivasi' => date("Y-m-d H:i:s"),
                        'slip_pembayaran' => "LDUI-SD-20250908110131-".$value->id_siswa."-".$value->nama_lengkap.".pdf"
                    );
                    $update_status = $this->mymodel->update("status_daftar_ulang_".$jenjang, $data_update, "id_daftar_ulang", $value->id_daftar_ulang);

                }
            }
        }
        
        
    }


    
}
