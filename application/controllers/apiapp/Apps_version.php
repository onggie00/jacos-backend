<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header('Access-Control-Request-Headers: origin, x-requested-with');
date_default_timezone_set('Asia/Jakarta');

defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . '/libraries/REST_Controller.php';


class Apps_version extends REST_Controller {
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

        $data = $this->mymodel->withquery("select * from apps_version where is_active_ver = '1' order by id DESC limit 1","result");

        if (!empty($data)) {
          $this->export_xml();
          $msg = array('status' => 1, 'message'=>'Berhasil ambil data' ,'data'=>$data);
          $status="200";
        }
        else{
          $msg = array('status' => 0, 'message'=>'Data tidak ditemukan' ,'data'=>array());
          $status="200";
        }

        $this->response($msg,$status);
    }

    public function export_xml(){
      $data_version = $this->mymodel->withquery("select * from apps_version where is_active_ver = '1'","row");
      //$xml = "<root_contact>";
$xml = '<rss version="2.0" xmlns:sparkle="http://www.andymatuschak.org/xml-namespaces/sparkle">
    <channel>
      <title>Labscib App</title>
      <item>
          <title>Version '.$data_version->android_ver.'</title>
          <description>'.$data_version->release_note.'</description>
          <pubDate>'.formatTanggal($data_version->created_at).' '.date('H:i:s', strtotime($data_version->created_at)).' +0007</pubDate>
          <enclosure url="https://play.google.com/store/apps/details?id=com.labschool.labscib" sparkle:version="'.$data_version->android_ver.'" sparkle:os="android" />
      </item>
      <item>
        <title>Version '.$data_version->ios_ver.'</title>
        <description>'.$data_version->release_note.'</description>
        <pubDate>'.formatTanggal($data_version->created_at).' '.date('H:i:s', strtotime($data_version->created_at)).' +0007</pubDate>
        <enclosure url="https://apps.apple.com/id/app/labscib-app/id1666453482" sparkle:version="'.$data_version->ios_ver.'" sparkle:os="iOS" />
        <sparkle:minimumSystemVersion>12.0.0</sparkle:minimumSystemVersion>
      </item>
    </channel>
</rss>';
      //$xml .= "</root_contact>";

      $sxe = new SimpleXMLElement($xml);
      $dom = new DOMDocument('1,0');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($sxe->asXML());

      //echo $dom->saveXML();

      $dom->save('labscib_ver.xml');
    }
}
