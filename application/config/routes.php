<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route_path = APPPATH . 'routes/';
require_once $route_path . 'routes_landing.php';

$route['404_override'] = 'not_found';
$route['translate_uri_dashes'] = FALSE;

$route['administrator/login'] = 'auth/backend/auth/login';
$route['administrator/register'] = 'not_found';//'auth/backend/auth/register';
$route['administrator/forgot-password'] = 'not_found';//'auth/backend/auth/forgot_password';

// Pagination presensi SMA memakai URL /index/{offset}. Tangani sebelum
// catch-all empat segmen agar offset tidak diteruskan dua kali.
$route[ADMIN_NAMESPACE_URL.'/presensi_sma/index/(:num)'] = 'presensi_sma/backend/presensi_sma/index/$1';

// Presensi Tadarus: route /sma|smk|sd|ft/export dan /export_pdf langsung
// ke method export() yang berdiri sendiri, agar URL yang dihasilkan JS
// view (site_url('presensi_tadarus/'.$url_method) . '/export?..') tidak
// salah parse jadi argumen method $url_method.
$route[ADMIN_NAMESPACE_URL.'/presensi_tadarus/(sd|smp|sma|ft)/export']      = 'presensi_tadarus/backend/presensi_tadarus/export';
$route[ADMIN_NAMESPACE_URL.'/presensi_tadarus/(sd|smp|sma|ft)/export_pdf']  = 'presensi_tadarus/backend/presensi_tadarus/export_pdf';

// Presensi PJOK
$route[ADMIN_NAMESPACE_URL.'/presensi_pjok/(sd|smp|sma|ft)/export']         = 'presensi_pjok/backend/presensi_pjok/export';
$route[ADMIN_NAMESPACE_URL.'/presensi_pjok/(sd|smp|sma|ft)/export_pdf']     = 'presensi_pjok/backend/presensi_pjok/export_pdf';

// Presensi Pramuka
$route[ADMIN_NAMESPACE_URL.'/presensi_pramuka/(sd|smp|sma|ft)/export']      = 'presensi_pramuka/backend/presensi_pramuka/export';
$route[ADMIN_NAMESPACE_URL.'/presensi_pramuka/(sd|smp|sma|ft)/export_pdf']  = 'presensi_pramuka/backend/presensi_pramuka/export_pdf';

$route['page/chart/pie_chart'] = 'page/chart/pie_chart';
$route['page/(:any)'] = 'page/detail/$1';
$route['blog/index'] = 'blog/index';
$route['blog/(:any)'] = 'blog/detail/$1';

// Public Peserta Acara (no auth required)
$route['peserta_acara/(:num)'] = 'peserta_acara/peserta_acara/index/$1';

// Public Realtime Tracker (no auth required)
$route['evaluasi/(:num)'] = 'acara_presensi_realtime_tracker/public_api/view/$1';
$route['evaluasi/status/(:num)'] = 'acara_presensi_realtime_tracker/public_api/status/$1';
$route['evaluasi/activity/(:num)'] = 'acara_presensi_realtime_tracker/public_api/activity/$1';
$route['evaluasi/breakdown/(:num)'] = 'acara_presensi_realtime_tracker/public_api/breakdown/$1';

// MHCU Tracker Publik (tanpa login, password gate, endpoint JSON agregat)
$route['mhcu_tracker'] = 'mhcu_tracker';
$route['mhcu_tracker/unlock'] = 'mhcu_tracker/unlock';
$route['mhcu_tracker/logout'] = 'mhcu_tracker/logout';
$route['mhcu_tracker/tracker_publik'] = 'mhcu_tracker/tracker_publik';

// MHCU Care — halaman publik tindak lanjut konseling (token di query string)
$route['mhcu_care']        = 'mhcu_care';
$route['mhcu_care/slots']  = 'mhcu_care/slots';
$route['mhcu_care/submit'] = 'mhcu_care/submit';

// Login admin JSON API (email+password) utk testing API — lihat Login_with_password.php
$route['login_with_password'] = 'login_with_password';

$route['administrator/web-page'] = 'page/backend/page/admin';


$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)'] = 'form/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)'] = 'form/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/manage-form/(:any)/(:any)/(:any)/(:any)'] = 'form/backend/$1/$2/$3/$4';

// Program Anggaran - akses berbasis jenjang (scope: sd/smp/sma; SMA mencakup SMA+FT)
$pa_scopes = array('sd', 'smp', 'sma');
foreach ($pa_scopes as $pa) {
    $base = ADMIN_NAMESPACE_URL . '/program_anggaran/' . $pa;
    $route[$base]                      = 'program_anggaran/backend/program_anggaran/index';
    $route[$base . '/(:num)']          = 'program_anggaran/backend/program_anggaran/index/$1';
    $route[$base . '/add']             = 'program_anggaran/backend/program_anggaran/add';
    $route[$base . '/add_save']        = 'program_anggaran/backend/program_anggaran/add_save';
    $route[$base . '/edit/(:num)']     = 'program_anggaran/backend/program_anggaran/edit/$1';
    $route[$base . '/edit_save/(:num)']= 'program_anggaran/backend/program_anggaran/edit_save/$1';
    $route[$base . '/view/(:num)']     = 'program_anggaran/backend/program_anggaran/view/$1';
    $route[$base . '/delete']          = 'program_anggaran/backend/program_anggaran/delete';
    $route[$base . '/export']          = 'program_anggaran/backend/program_anggaran/export';
    $route[$base . '/export_pdf']      = 'program_anggaran/backend/program_anggaran/export_pdf';
    $route[$base . '/single_pdf/(:num)']= 'program_anggaran/backend/program_anggaran/single_pdf/$1';
    $route[$base . '/active']          = 'program_anggaran/backend/program_anggaran/active';
    $route[$base . '/not_active']      = 'program_anggaran/backend/program_anggaran/not_active';
    $route[$base . '/import']          = 'program_anggaran/backend/program_anggaran/import';
}

$route[ADMIN_NAMESPACE_URL.'/(:any)'] = '$1/backend/$1';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)'] = '$1/backend/$1/$2';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3';
$route[ADMIN_NAMESPACE_URL.'/(:any)/(:any)/(:any)/(:any)'] = '$1/backend/$1/$2/$3/$3';




$route['api/user/(:any)'] = 'api/user/$1';
$route['api/group/(:any)'] = 'api/group/$1';

// $route['api/(:any)'] = '$1/api/$1';
// $route['api/(:any)/(:any)'] = '$1/api/$1/$2';
// $route['api/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3';
// $route['api/(:any)/(:any)/(:any)/(:any)'] = '$1/api/$1/$2/$3/$3';
$route['api/v1.0/notification'] = 'apiapp/notification';

// BRI SNAP BI - standar endpoint (BRI akan hit path persis ini)
$route['snap/v1.0/access-token/b2b']                 = 'apiapp/Bri_snap_access_token_b2b';
$route['snap/v1.0/transfer-va/notify-payment-intrabank'] = 'apiapp/Bri_snap_notify_payment_intrabank';
// BRI VA exclusive — path notifikasi yang didaftarkan di sisi BRI
$route['v2/exclusive/bri_va'] = 'apiapp/Bri_snap_notify_payment_intrabank';
$route['email_confirmation'] = 'email_confirmation/Program_anggaran_konfirmasi/index';
$route['email_confirmation/(:any)'] = 'email_confirmation/Program_anggaran_konfirmasi/$1';
