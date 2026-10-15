<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Career_base — fitur bootstrap API kandidat karir (JACOS Careers).
 *
 * File APR ( hanya file baru career): aplikasi ini hanya untuk endpoint
 * `apicareer/` (publik + kandidat login JWT). Tidak menyentuh apiweb/apiapp.
 *
 * Diposisikan di application/libraries/ — selaras satu ruang dengan
 * REST_Controller.php dan dimuat otomatis oleh HMVC Modules::autoload
 * (is_file APPPATH.'libraries/'.ucfirst($class).EXT). Controller `apicareer`
 * tidak perlu require manual; class hadir saat `extends Career_base`.
 *
 * Kontrak respons seragam:
 *   {"status": true|false, "message": "...", "data": {...}|null}
 * HTTP code: 200/201/204/400/401/403/404/409/422/429/500.
 */

// Muat JWT lib existing (Firebase style, HS256).
require_once APPPATH . 'libraries/Jwt/JWT.php';
require_once APPPATH . 'libraries/Jwt/BeforeValidException.php';
require_once APPPATH . 'libraries/Jwt/ExpiredException.php';
require_once APPPATH . 'libraries/Jwt/SignatureInvalidException.php';

use \Firebase\JWT\JWT as CareerJWT;

abstract class Career_base extends REST_Controller {

	/** id kandidat hasil verifikasi token (NULL bila belum auth) */
	protected $career_candidate_id = NULL;
	/** payload JWT setelah verifikasi */
	protected $career_token_payload = NULL;

	/**
	 * Pengecualian API key (warisan SEMUA controller apicareer):
	 * rest.php bersifat SHARE (rest_enable_keys=TRUE milik API web/mobile
	 * lama) dan Fase 4 melarang menyentuh rest.php/table `keys`.
	 * apicareer tidak memakai API key dari table `keys` — otentikasi murni
	 * JWT Bearer. Mapping method → key = FALSE mencegah fork
	 * "Invalid API key" di REST_Controller::_remap (baris ~648, cek
	 * use_key) tanpa menyentuh config share maupun database.
	 */
	protected $methods = array(
		'register_post'   => array('key' => FALSE),
		'login_post'      => array('key' => FALSE),
		'logout_post'     => array('key' => FALSE),
		'me_get'          => array('key' => FALSE),
		'index_get'       => array('key' => FALSE),
		'index_post'      => array('key' => FALSE),
		'index_put'       => array('key' => FALSE),
		'detail_get'      => array('key' => FALSE),
		'detail_put'      => array('key' => FALSE),
		'detail_delete'   => array('key' => FALSE),
		'submit_post'     => array('key' => FALSE),
		'upload_post'     => array('key' => FALSE),
		'read_put'        => array('key' => FALSE),
	);

	public function __construct()
	{
		parent::__construct();

		// credentials.php (gitignored) — sumber CAREER_JWT_KEY.
		if (file_exists(APPPATH . 'config/credentials.php'))
		{
			require_once APPPATH . 'config/credentials.php';
		}

		$this->config->load('career', FALSE, TRUE);

		$this->_career_fix_cors();
	}

	// ------------------------------------------------------------------
	// CORS allowlist career (kandidat) — TIDAK memakai Cors_hook.php
	// (hook itu hanya untuk /apiweb + /apiapp dengan `*`, tidak menyentuh
	// /apicareer; diverifikasi via curl di smoke test).
	// ------------------------------------------------------------------
	protected function _career_fix_cors()
	{
		$origin  = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
		$allowed = (array) $this->config->item('career_allowed_origins');

		// Buang `*` yang dicat REST_Controller sendiri (rest.php
		// allow_any_cors_domain=true) hanya bila origin tidak allowlist.
		if ($origin === '' OR ! in_array($origin, $allowed))
		{
			// tanpa ACAO — browser blokir akses JS cross-origin.
			@header_remove('Access-Control-Allow-Origin');
			@header('Vary: Origin');
			return;
		}

		@header_remove('Access-Control-Allow-Origin');
		header('Access-Control-Allow-Origin: ' . $origin);
		header('Vary: Origin');
		header('Access-Control-Allow-Headers: Authorization, Content-Type, Accept, X-Requested-With');
		header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
		header('Access-Control-Max-Age: 600');
	}

	// --------------------------------------------------------------
	// Response wrapper
	// --------------------------------------------------------------
	protected function _ok($data = NULL, $message = 'OK', $http_code = 200)
	{
		$this->set_response(array(
			'status'  => TRUE,
			'message' => $message,
			'data'    => $data
		), $http_code);
	}

	protected function _err($message, $http_code = 400, $data = NULL)
	{
		$this->set_response(array(
			'status'  => FALSE,
			'message' => $message,
			'data'    => $data
		), $http_code);
	}

	// ------------------------------------------------------------------
	// JWT / auth
	// ------------------------------------------------------------------
	protected function _career_jwt_key()
	{
		if (defined('CAREER_JWT_KEY') AND CAREER_JWT_KEY !== '')
		{
			return CAREER_JWT_KEY;
		}
		return NULL; // fail-closed: key belum diisi
	}

	/**
	 * Terima Bearer token HTTP, decode HS256, verifikasi kandidat.
	 * Set FALSE bila gagal (pembaca cukup tak reliabel); $this->response.
	 * Billar: hitung kandidat milik token saja (mencegah IDOR).
	 * @return object|NULL kandidat row
	 */
	protected function _auth_candidate($allow_inactive = FALSE)
	{
		$header = $this->input->get_request_header('Authorization', FALSE);
		if ($header === NULL OR stripos($header, 'bearer ') !== 0)
		{
			$this->_err('Token otentikasi wajib (header Authorization: Bearer <token>).', 401);
			return NULL;
		}

		$token = trim(substr($header, 7));
		if ($token === '')
		{
			$this->_err('Token kosong.', 401);
			return NULL;
		}

		$key = $this->_career_jwt_key();
		if ($key === NULL)
		{
			$this->_err('Server belum dikonfigurasi (career_jwt_key kosong).', 500);
			return NULL;
		}

		try
		{
			$payload = CareerJWT::decode($token, $key, array('HS256'));
		}
		catch (\Firebase\JWT\ExpiredException $e)
		{
			$this->_err('Token kedaluwarsa. Silakan login ulang.', 401);
			return NULL;
		}
		catch (Exception $e)
		{
			$this->_err('Token tidak valid.', 401);
			return NULL;
		}
		$payload = (array) $payload;
		if ( ! isset($payload['sub'], $payload['ver'])
			OR ! ctype_digit((string) $payload['sub']))
		{
			$this->_err('Token tidak valid (payload).', 401);
			return NULL;
		}

		// Scope ketat ke kandidat pemilik token (anti-IDOR di semua helper).
		$id_candidate = (int) $payload['sub'];
		$candidate = $this->db->select('c.id_candidate, c.email, c.full_name, c.is_active, c.token_version')
			->where('c.id_candidate', $id_candidate)
			->where('c.is_active', 1)
			->get('career_candidates c')
			->row();

		if ( ! $candidate)
		{
			$this->_err('Akun tidak ditemukan atau nonaktif.', 401);
			return NULL;
		}

		if ((int) $candidate->token_version !== (int) $payload['ver'])
		{
			$this->_err('Token tidak berlaku (versi token lama). Silakan login ulang.', 401);
			return NULL;
		}

		$this->career_candidate_id   = $candidate->id_candidate;
		$this->career_token_payload = $payload;
		return $candidate;
	}

	/** Return kandidat bila sudah login (401 exit bila tidak). */
	protected function _require_auth()
	{
		return $this->_auth_candidate();
	}

	/**
	 * Cek + jit lamaran (application) milik kandidat ini saja.
	 * Bila ditemukan milik kandidat lain → 404 (bukan 403, anti IDOR).
	 */
	protected function _own_application($id_application, $allow_locked = TRUE)
	{
		$row = $this->db->select('a.*, p.title AS posting_title')
			->where('a.id_application', (int) $id_application)
			->where('a.id_candidate', (int) $this->career_candidate_id)
			->join('career_job_postings p', 'p.id_job_posting = a.id_job_posting')
			->get('career_applications a')
			->row();

		if ( ! $row)
		{
			$this->_err('Lamaran tidak ditemukan.', 404);
			return NULL;
		}

		if ( ! $allow_locked AND $row->status !== 'draft')
		{
			$this->_err('Lamaran sudah dikunci (sudah submit). Tidak dapat diubah atau diunggah ulang.', 409);
			return NULL;
		}
		return $row;
	}

	// ------------------------------------------------------------------
	// Rate limit / lockout (data: career_login_attempts + kolom candidates)
	// ------------------------------------------------------------------
	protected function _client_ip()
	{
		$ip = $this->input->ip_address();
		return ($ip === '0.0.0.0') ? '127.0.0.1' : $ip;
	}

	/** Catat percobaan attempt_type login|register. */
	protected function _log_attempt($type, $email = NULL, $succeeded = FALSE)
	{
		$this->db->insert('career_login_attempts', array(
			'ip_address'   => $this->_client_ip(),
			'email'        => $email,
			'attempt_type' => $type,
			'succeeded'    => $succeeded ? 1 : 0,
		));
	}

	/** Atasan: hitung percobaan gagal jendela waktu (register per-IP). */
	protected function _register_blocked()
	{
		$window = (int) $this->config->item('career_register_rate_window');
		$cutoff = date('Y-m-d H:i:s', time() - $window);
		$cnt = $this->db->where('ip_address', $this->_client_ip())
			->where('attempt_type', 'register')
			->where('succeeded', 0)
			->where('created_at >=', $cutoff)
			->count_all_results('career_login_attempts');
		return ($cnt >= (int) $this->config->item('career_register_max_attempts'));
	}

	/**
	 * Login: cek locked_until di candidate + hitung fail baru (max, lockout)
	 * berhasil dipakai oleh Auth::login.
	 */
	protected function _login_locked($candidate)
	{
		if ($candidate->locked_until !== NULL AND $candidate->locked_until > date('Y-m-d H:i:s'))
		{
			return TRUE;
		}
		return FALSE;
	}

	protected function _login_fail_window_count($email)
	{
		$max = (int) $this->config->item('career_login_max_attempts');
		$window = (int) $this->config->item('career_login_window');
		$cutoff = date('Y-m-d H:i:s', time() - $window);
		return $this->db->where('email', $email)
			->where('attempt_type', 'login')
			->where('succeeded', 0)
			->where('created_at >=', $cutoff)
			->count_all_results('career_login_attempts');
	}

	/**
	 * Setelah gagal ke-$n, tandai locked_until di career_candidates bila
	 * capai max attempt; reset failed_login_count saat success.
	 */
	protected function _login_note_failure($candidate)
	{
		$max = (int) $this->config->item('career_login_max_attempts');
		$lockout = (int) $this->config->item('career_login_lockout');
		$fail = $this->_login_fail_window_count($candidate->email); // sudah dicatat
		$cnt = (int) $candidate->failed_login_count + 1;
		$data = array('failed_login_count' => $cnt);
		if ($cnt >= $max)
		{
			$data['locked_until'] = date('Y-m-d H:i:s', time() + $lockout);
			$data['failed_login_count'] = 0; // reset setelah lock dipasang
		}
		$this->db->where('id_candidate', $candidate->id_candidate)->update('career_candidates', $data);
		return ($cnt >= $max);
	}

	// ------------------------------------------------------------------
	// Audit log (career_audit_logs)
	// ------------------------------------------------------------------
	protected function _audit($action, $object_type, $object_id = 0, $detail = array(), $actor_type = 'candidate')
	{
		$this->db->insert('career_audit_logs', array(
			'actor_type'  => $actor_type,
			'actor_id'    => ($actor_type === 'candidate' AND $this->career_candidate_id) ? $this->career_candidate_id : 0,
			'action'      => $action,
			'object_type' => $object_type,
			'object_id'   => (int) $object_id,
			'detail_json' => json_encode(array(
				'action' => $action,
				'at'     => date('c'),
			)),
			'ip_address'  => $this->_client_ip(),
		));
	}

	// ------------------------------------------------------------
	// Notifikasi in-app
	// ------------------------------------------------------------
	protected function _notify($action, $title, $body)
	{
		// notifikasi ringan ke kandidat bila $this->career_candidate_id tersedia
		if ( ! $this->career_candidate_id) return;
		$this->db->insert('career_notifications', array(
			'id_candidate' => $this->career_candidate_id,
			'type'         => $action,
			'title'        => $title,
			'body'         => $body,
			'is_read'      => 0,
		));
	}

	// ------------------------------------------------------------------
	// Helper validasi primitif (5.2-style; dipanggil manual oleh controller)
	// ------------------------------------------------------------------
	protected function _json_int($value, $min = 1, $max = 2147483647)
	{
		if ( ! is_numeric($value)) return FALSE;
		$v = (int) $value;
		return ($v >= $min AND $v <= $max) ? $v : FALSE;
	}

	protected function _json_string($value, $maxlen = 255, $minlen = 1)
	{
		if ( ! is_string($value) OR ! is_scalar($value)) return FALSE;
		$value = trim($value);
		if (strlen($value) < $minlen OR strlen($value) > $maxlen) return FALSE;
		return $value;
	}

	// ------------------------------------------------------------------
	// Tanggal posting aktif (dipakai Lowongan + Lamaran submit)
	// ------------------------------------------------------------------
	protected function _posting_is_open($posting)
	{
		$today = date('Y-m-d');
		if ($posting->is_published != 1) return FALSE;
		if ($posting->opening_date !== NULL AND $posting->opening_date > $today) return FALSE;
		if ($posting->closing_date !== NULL AND $posting->closing_date < $today) return FALSE;
		return TRUE;
	}
}
