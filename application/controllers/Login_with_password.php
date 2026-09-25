<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Login With Password (JSON API)
*| --------------------------------------------------------------------------
*| Endpoint login admin via email + password utk keperluan testing API (Bruno)
*| — mendapat session cookie admin tanpa login browser.
*|
*| POST /login_with_password
*| Body (JSON atau form): email, password
*| Sukses  : JSON status=1 + Set-Cookie session admin (pakai cookie itu di
*|           request /administrator/* lain, header Cookie: ci_session=...)
*| Gagal   : JSON status=0 (pesan generik)
*|
*| Keamanan:
*| - Hanya POST.
*| - Dapat dimatikan via pengaturan_akun name_setting=login_with_password_enabled
*|   value=0 (default ON bila tidak ada — matikan di production bila tak dipakai).
*| - Brute-force dibatasi oleh ddos_protection Aauth (login attempts).
*/
class Login_with_password extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		// POST only
		if ($this->input->method(TRUE) !== 'POST') {
			$this->_respond(array('status' => 0, 'message' => 'Method tidak diizinkan, gunakan POST'), 405);
			return;
		}

		// kill-switch via pengaturan_akun (default ON bila belum di-set)
		$row_flag = $this->db->select('value')->where('name_setting', 'login_with_password_enabled')->get('pengaturan_akun')->row();
		if (!empty($row_flag) && strtoupper(trim($row_flag->value)) === '0') {
			$this->_respond(array('status' => 0, 'message' => 'Endpoint login dinonaktifkan'), 403);
			return;
		}

		// terima form-urlencoded ATAU JSON body
		$email = trim((string) $this->input->post('email'));
		$password = (string) $this->input->post('password');
		if ($email === '' && $password === '') {
			$json = json_decode($this->input->raw_input_stream, TRUE);
			if (is_array($json)) {
				$email = trim((string) (isset($json['email']) ? $json['email'] : ''));
				$password = (string) (isset($json['password']) ? $json['password'] : '');
			}
		}

		if ($email === '' || $password === '') {
			$this->_respond(array('status' => 0, 'message' => 'Email dan password wajib diisi'), 400);
			return;
		}

		if ($this->aauth->login($email, $password, FALSE)) {
			$user_id = $this->aauth->get_user_id();
			$user = $this->aauth->get_user($user_id);
			$this->_respond(array(
				'status' => 1,
				'message' => 'Login berhasil. Gunakan cookie session yang dikirim untuk request /administrator/* (header Cookie: ci_session=<nilai>).',
				'data' => array(
					'id' => $user_id,
					'name' => isset($user->name) ? $user->name : '',
					'email' => isset($user->email) ? $user->email : $email,
					'group' => $this->aauth->get_user_groups($user_id)
				)
			), 200);
			return;
		}

		// pesan generik (jangan bocorkan apakah email/password yang salah)
		$this->_respond(array('status' => 0, 'message' => 'Email atau password salah'), 401);
	}

	private function _respond($data, $http_code)
	{
		$this->output
			->set_status_header($http_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data));
	}
}
