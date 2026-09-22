<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_assets extends REST_Controller {
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
      if(isset($headers['x-token']))
        $token =  $headers['x-token'];

        $role = $this->get('role');
        $jenjang = $this->get('jenjang');
        $data = array();
        //basic assets
        $where_basic = "";
        if (!empty($jenjang)){
          $where_basic = "and is_default = '0' and jenjang like '%".$jenjang."%'";
        }
        else{
          $where_basic = "and (is_default = '1' or role like '%".$role."%')";
        }
        $data_basic_assets = $this->mymodel->withquery("select id_assets, jenjang, role, position, img_file, description, is_active, is_default from apps_assets 
        where is_active = '1'  ".$where_basic." ","result");
        if (!empty($data_basic_assets)) {
          foreach($data_basic_assets as $key => $value){
            if (!empty($value->img_file)){
              $value->img_file = base_url('uploads/apps_assets/'.$value->img_file);
            }
          }
          $data['basic_assets'] = $data_basic_assets;
        }
        else{
          $data['basic_assets'] = null;
        }

        //icons
        $data_icons = $this->mymodel->withquery("select id_icon, jenjang, role, kategori_icon, no_urut_icon, description, img_file, is_active, is_default from apps_icon 
        where is_active = '1' and role like '%".$role."%' order by no_urut_icon ASC","result");
        if (!empty($data_icons)) {
          foreach($data_icons as $key => $value){
            if (!empty($value->img_file)){
              $value->img_file = base_url('uploads/apps_icon/'.$value->img_file);
            }
          }
          $data['icons'] = $data_icons;
        }
        else{
          $data['icons'] = null;
        }

        //banner
        if (empty($jenjang)){
          $data_banner = $this->mymodel->withquery("select id_banner, jenjang, role, img_file, description, no_urut, is_active, is_default from apps_banner 
          where is_active = '1' and role like '%".$role."%' order by no_urut ASC","result");
        }
        else{
          $data_banner = $this->mymodel->withquery("select id_banner, jenjang, role, img_file, description, no_urut, is_active, is_default from apps_banner 
          where is_active = '1' and role like '%".$role."%' and jenjang like '%".$jenjang."%' order by no_urut ASC","result");
        }
        if (!empty($data_banner)) {
          foreach($data_banner as $key => $value){
            if (!empty($value->img_file)){
              $value->img_file = base_url('uploads/apps_banner/'.$value->img_file);
            }
          }
          $data['banner'] = $data_banner;
        }
        else{
          $data['banner'] = null;
        }

        //jika komplit
        if (!empty($data_basic_assets) && !empty($data_icons) && !empty($data_banner)) {
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          if (empty($data_basic_assets)){
            //basic assets
            $data_basic_assets = $this->mymodel->withquery("select id_assets, jenjang, role, position, img_file, description, is_active, is_default from apps_assets 
            where is_default = 1","result");
            if (!empty($data_basic_assets)) {
              foreach($data_basic_assets as $key => $value){
                if (!empty($value->img_file)){
                  $value->img_file = base_url('uploads/apps_assets/'.$value->img_file);
                }
              }
              $data['basic_assets'] = $data_basic_assets;
            }
            else{
              $data['basic_assets'] = null;
            }
          }
          if (empty($data_icons)){
            //icons
            $data_icons = $this->mymodel->withquery("select id_icon, jenjang, role, kategori_icon, no_urut_icon, description, img_file, is_active, is_default from apps_icon 
            where is_default = '1' and role like '%".$role."%' order by no_urut_icon ASC","result");
            if (!empty($data_icons)) {
              foreach($data_icons as $key => $value){
                if (!empty($value->img_file)){
                  $value->img_file = base_url('uploads/apps_icon/'.$value->img_file);
                }
              }
              $data['icons'] = $data_icons;
            }
            else{
              $data['icons'] = null;
            }
          }

          if (empty($data_banner)){
            //banner
            $data_banner = $this->mymodel->withquery("select id_banner, jenjang, role, img_file, description, no_urut, is_active, is_default from apps_banner 
            where is_default = '1' and role like '%".$role."%' order by no_urut ASC","result");
            if (!empty($data_banner)) {
              foreach($data_banner as $key => $value){
                if (!empty($value->img_file)){
                  $value->img_file = base_url('uploads/apps_banner/'.$value->img_file);
                }
              }
              $data['banner'] = $data_banner;
            }
            else{
              $data['banner'] = null;
            }
          }

          $msg = array('status' => 1, 'message'=>'Berhasil ambil data default' ,'data'=>$data);
          $status="200";
        }

        $this->response($msg,$status);
    }
}
