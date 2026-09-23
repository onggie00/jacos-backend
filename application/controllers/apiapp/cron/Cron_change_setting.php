<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');
require FCPATH . '/vendor/autoload.php';
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Cron_change_setting extends MY_Controller {
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

        //cron setting check
        $get_deactivate = $this->mymodel->withquery("select * from cron_setting 
        where date_deactivate <= '".$today."' ","result");
        $get_reactivate = $this->mymodel->withquery("select * from cron_setting 
        where date_reactivate <= '".$today."' ","result");
        
        if(!empty($get_deactivate)){
            foreach($get_deactivate as $key => $value){
                //update nonaktif semua data sesuai tanggal
                $this->mymodel->update("cron_setting",array("is_active" => "0"),array("id_cron_setting" => $value->id_cron_setting));
            }
        }

        if(!empty($get_reactivate)){
            foreach($get_reactivate as $key => $value){
                //update aktif semua data sesuai tanggal
                $this->mymodel->update("cron_setting",array("is_active" => "1"),array("id_cron_setting" => $value->id_cron_setting));
            }
        }

        //Aktivasi apps asset, banner, icon, splash screen, login page
        $update_assets = $this->mymodel->update("apps_assets",array("is_active" => "1"), "is_active = '0' and tanggal_aktif=", date("Y-m-d"));
        $update_banner = $this->mymodel->update("apps_banner",array("is_active" => "1"), "is_active = '0' and tanggal_aktif=", date("Y-m-d"));
        $update_icon = $this->mymodel->update("apps_icon",array("is_active" => "1"), "is_active = '0' and tanggal_aktif=", date("Y-m-d"));
        $update_splash_screen = $this->mymodel->update("apps_splash_screen",array("is_active" => "1"), "is_active = '0' and tanggal_aktif=", date("Y-m-d"));
        $update_login_page = $this->mymodel->update("apps_login_page",array("is_active" => "1"), "is_active = '0' and tanggal_aktif=", date("Y-m-d"));
        
        //Deaktivasi apps asset, banner, icon, splash screen, login page
        $update_assets = $this->mymodel->update("apps_assets",array("is_active" => "0"), "is_active = '1' and tanggal_selesai=", date("Y-m-d"));
        $update_banner = $this->mymodel->update("apps_banner",array("is_active" => "0"), "is_active = '1' and tanggal_selesai=", date("Y-m-d"));
        $update_icon = $this->mymodel->update("apps_icon",array("is_active" => "0"), "is_active = '1' and tanggal_selesai=", date("Y-m-d"));
        $update_splash_screen = $this->mymodel->update("apps_splash_screen",array("is_active" => "0"), "is_active = '1' and tanggal_selesai=", date("Y-m-d"));
        $update_login_page = $this->mymodel->update("apps_login_page",array("is_active" => "0"), "is_active = '1' and tanggal_selesai=", date("Y-m-d"));

        
    }
    
}
