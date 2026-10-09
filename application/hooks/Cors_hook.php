<?php
/**
 * CORS hook (pre_system)
 *
 * Header Access-Control-Allow-* harus terkirim di SEMUA respons apiweb/apiapp,
 * termasuk halaman error CodeIgniter (404/500) yang tidak melewati controller —
 * tanpa ini browser memblokir respons web PSB (client lintas origin).
 *
 * Hanya dipasang untuk path API; admin panel tidak tersentuh.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors_hook {

	public function send_headers()
	{
		$uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		if (strpos($uri, '/apiweb') !== false || strpos($uri, '/apiapp') !== false) {
			header('Access-Control-Allow-Origin: *');
			header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
			header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, x-token, user_role, X-Api-Key');
		}
	}
}
