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
        $params=array('criteria[0][key]' => 'id','criteria[0][value]' => $this->post('id'));
        $jenjang = $this->post("jenjang");
        
        $category = $this->moodle_rest($func,$params, $jenjang);

        $res=[
          "category"=>$category
        ];

        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$res);
          $status="200";

        $this->response($msg,$status);
    }

    function moodle_rest($func,$params, $jenjang){

      $this->load->library('MoodleRest');

      $url='https://lms.labschoolcibubur.sch.id/webservice/rest/server.php';
      $token='24eeaadd9663e4bf17e5cf0d7196986e';

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
