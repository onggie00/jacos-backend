<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_category extends REST_Controller {
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

        $func='core_course_get_categories';
        $params=array('criteria[0][key]' => $this->post('type'),'criteria[0][value]' => $this->post('value'));
        //$params=array('criteria[0][key]' => 'id','criteria[0][value]' => $this->post('id'));

        $category = $this->moodle_rest($func,$params);

        if ($category === null) {
          $msg = array('status' => 0, 'message'=>'Kredensial Moodle belum diisi di pengaturan_akun', 'data'=>null);
          $status="503";
          $this->response($msg,$status);
          return;
        }

        $res=[
          "category"=>$category
        ];

        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$res);
          $status="200";

        $this->response($msg,$status);
    }

    function moodle_rest($func,$params,$jenjang){

      $this->load->library('MoodleRest');

      // Kredensial Moodle dari pengaturan_akun (moodle_url_*/moodle_token_*)
      // [JACOS] TODO(manual): isi token webservice Moodle Jacos di pengaturan_akun
      $jenjang = strtoupper($jenjang);
      $key = ($jenjang == "SD" || $jenjang == "SMP" || $jenjang == "SMA") ? strtolower($jenjang) : "smp";
      $row_url = $this->db->get_where("pengaturan_akun", array("name_setting" => "moodle_url_" . $key))->row();
      $row_token = $this->db->get_where("pengaturan_akun", array("name_setting" => "moodle_token_" . $key))->row();
      $url = (!empty($row_url) && !empty($row_url->value)) ? $row_url->value : "";
      $token = (!empty($row_token) && !empty($row_token->value)) ? $row_token->value : "";

      if ($url == "" || $token == "") {
        return null; // kredensial belum diisi di pengaturan_akun
      }


      $MoodleRest = new MoodleRest($url , $token);
  
      $res = $MoodleRest->request($func,$params);
  
      return $res;
    }

    function like($str, $searchTerm) {
      $searchTerm = strtolower($searchTerm);
      $str = strtolower($str);
      $pos = strpos($str, $searchTerm);
      if ($pos === false)
          return false;
      else
          return true;
  }
}
