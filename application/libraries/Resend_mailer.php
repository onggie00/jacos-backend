<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library pengiriman email via Resend API (menggantikan PHPMailer/SMTP).
 * API key diambil dari tabel pengaturan_akun (name_setting = 'resend_api_key').
 * PHP 5.2 compatible.
 */
class Resend_mailer {

	var $ci;

	function __construct()
	{
		$this->ci =& get_instance();
	}

	function get_api_key()
	{
		$row = $this->ci->mymodel->getbywhere('pengaturan_akun', 'name_setting', 'resend_api_key', 'row');
		if (!empty($row) && !empty($row->value)) {
			return $row->value;
		}
		return '';
	}

	/**
	 * Tambah file lokal ke daftar attachment (dipakai array by-reference).
	 * Format attachment Resend: array('content' => base64, 'filename' => nama)
	 */
	function add_attachment(&$attachments, $path, $filename = '')
	{
		if (empty($filename)) {
			$filename = basename($path);
		}
		if (file_exists($path)) {
			$attachments[] = array(
				'content' => base64_encode(file_get_contents($path)),
				'filename' => $filename
			);
		} else {
			log_message('error', 'Resend: lampiran tidak ditemukan: ' . $path);
		}
	}

	/**
	 * Kirim email HTML via POST https://api.resend.com/emails
	 * return: array('ok' => bool, 'id' => string, 'error' => string)
	 */
	function send($to, $subject, $html, $attachments = array())
	{
		$api_key = $this->get_api_key();
		if (empty($api_key)) {
			log_message('error', 'Resend: resend_api_key kosong di pengaturan_akun');
			return array('ok' => false, 'id' => '', 'error' => 'resend_api_key kosong');
		}

		$payload = array(
			'from' => 'JAKARTA COSMOPOLITE ISLAMIC SCHOOL <noreply@jacos.id>',
			'to' => array($to),
			'subject' => $subject,
			'html' => $html
		);
		if (!empty($attachments)) {
			$payload['attachments'] = $attachments;
		}

		$ch = curl_init('https://api.resend.com/emails');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $api_key,
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_err = curl_error($ch);
		curl_close($ch);

		if ($response === false) {
			log_message('error', 'Resend curl error: ' . $curl_err);
			return array('ok' => false, 'id' => '', 'error' => $curl_err);
		}

		$body = json_decode($response, true);
		if ($http_code >= 200 && $http_code < 300) {
			$mail_id = isset($body['id']) ? $body['id'] : '';
			return array('ok' => true, 'id' => $mail_id, 'error' => '');
		}

		log_message('error', 'Resend HTTP ' . $http_code . ': ' . $response);
		return array('ok' => false, 'id' => '', 'error' => $response);
	}
}
