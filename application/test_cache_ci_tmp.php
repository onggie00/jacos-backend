<?php
// Harness uji driver cache file CI3 persis seperti dipakai model:
// $this->load->driver('cache', array('adapter'=>'file','backup'=>'dummy','key_prefix'=>'kd_spp_'))
define('BASEPATH', '/var/www/html/system/');
define('APPPATH', '/var/www/html/application/');
define('EXT', '.php');
define('CI_VERSION', '3.1.13');
define('ENVIRONMENT', 'development');
error_reporting(E_ALL);

// Stub CI superobject minimal: config_item() dari Common.php butuh get_instance()
class CI_Controller {
	public static $instance;
	public $config;
	public function __construct() {
		self::$instance =& $this;
		require BASEPATH . 'core/Config.php';
		$this->config = new CI_Config();
	}
}
function &get_instance() { return CI_Controller::$instance; }
function log_message($a, $b) {}

require BASEPATH . 'core/Common.php';
require BASEPATH . 'libraries/Driver.php';
require BASEPATH . 'libraries/Cache/Cache.php';

$CI = new CI_Controller();
$CI->load = new stdClass();
// replika perilaku CI_Loader::driver()
require BASEPATH . 'libraries/Cache/drivers/Cache_file.php';
require BASEPATH . 'libraries/Cache/drivers/Cache_dummy.php';
$cache = new CI_Cache(array('adapter' => 'file', 'backup' => 'dummy', 'key_prefix' => 'kd_spp_'));

// simulasi helper model: get (miss) -> set -> get (hit) -> TTL
$miss = $cache->get('kelas_options');
var_dump($miss === false ? 'MISS (benar, kosong)' : 'UNEXPECTED HIT');

$val = array('SD' => array('1A', '1B'), 'SMP' => array());
$ok = $cache->save('kelas_options', $val, 300);
var_dump($ok ? 'SAVE OK' : 'SAVE FAIL');

$hit = $cache->get('kelas_options');
var_dump($hit === $val ? 'HIT + nilai identik' : print_r($hit, true));

// cek file fisik
$files = glob(APPPATH . 'cache/kd_spp_*');
echo "file cache: " . (empty($files) ? 'TIDAK ADA' : basename($files[0])) . "\n";

// expired: TTL 1 detik
$cache->save('ttl_test', 'x', 1);
sleep(2);
var_dump($cache->get('ttl_test') === false ? 'EXPIRED sesudah TTL (benar)' : 'MASIH ADA (salah)');

// cleanup
$cache->delete('kelas_options');
echo is_array($cache->get('kelas_options')) ? 'DELETE FAIL' : 'DELETE OK + kembali MISS';
echo "\n";
