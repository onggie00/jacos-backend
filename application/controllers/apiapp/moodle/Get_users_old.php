<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Get_users extends REST_Controller {
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

        $func='core_user_get_users';
        $params=array('criteria[0][key]' => 'email','criteria[0][value]' => $this->post('email'));
        

        $user = $this->moodle_rest($func,$params);

        $users=$user['users'][0];

        $func='core_enrol_get_users_courses';
        $params=array('userid' => $users['id']);

        $courses = $this->moodle_rest($func,$params);
        // dd($user);
        $ids='';
        foreach($courses as $key => $item){
          $func='core_course_get_categories';
          $ids.=($key==0)?$item['category']:','.$item['category'];
          
        }

        $params=array('criteria[0][key]' => 'ids','criteria[0][value]' => $ids);
        $category = $this->moodle_rest($func,$params);

        foreach($courses as $key => $item){
          foreach($category as $c){
            if($item['category']==$c['id']){
              $courses[$key]['detail_category']=$c;
            }
          }
        }
        $res=[
          "user"=>$users,
          "courses"=>$courses
        ];

        $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$res);
          $status="200";

        $this->response($msg,$status);
    }

    function moodle_rest($func,$params){

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
