<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

// CORS utk API PSB: header harus terkirim di semua respons (termasuk 404/500 CI),
// karena error page CodeIgniter tidak melewati controller (Cek_data/Pendaftaran_sd web client)
$hook['pre_system'][] = array(
	'class'    => 'Cors_hook',
	'function' => 'send_headers',
	'filename' => 'Cors_hook.php',
	'filepath' => 'hooks'
);
