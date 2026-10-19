<?php
use Firebase\JWT\JWT as CareerJWT;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth kandidat career — register / login / logout / me.
 *
 * Tidak ada verifikasi/pengiriman email (keputusan proyek). Rate-limit login &
 * register via career_login_attempts + kolom lock di career_candidates.
 */

class Auth extends Career_base {

	/** H4: hash bcrypt dummy — email tak terdaftar tetap menjalankan
	 * password_verify supaya timing seragam (anti-enumerasi). */
	const DUMMY_HASH = '$2y$10$SZ2ogSkTcDn1enkhRQpIwetV7U/egu5AnJu4FLGWoYG2im.ARWw7y';

	// --------------------------------------------------------------
	// POST apicareer/Auth/register
	// body: {"email","password","full_name"}
	// --------------------------------------------------------------
	public function register_post()
	{
		// M9: kunci JWT dicek SEBELUM akun dibuat — bukan setelahnya.
		$this->_career_require_key();

		// H3: blokir dulu, lalu catat SEMUA percobaan (termasuk gagal validasi).
		if ($this->_register_blocked())
		{
			//$email mungkin belum sah; log tanpa email supaya tetap terhitung
			$this->_log_attempt('register', NULL, FALSE);
		return $this->_err('Terlalu banyak percobaan pendaftaran. Coba lagi nanti.', 429);
		}

		$email = $this->post('email');
		$password = $this->post('password');
		$name = $this->post('full_name');

		// L5: is_string dulu (array input tidak boleh memicu notice).
		$email = is_string($email) ? trim($email) : '';
		$password = is_string($password) ? $password : '';
		$name = is_string($name) ? trim($name) : '';

		$invalid = FALSE;
		if ($email === '' OR $password === '' OR $name === '') $invalid = TRUE;
		if ($invalid === FALSE AND ( ! filter_var($email, FILTER_VALIDATE_EMAIL) OR strlen($email) > 190)) $invalid = TRUE;
		if ($invalid === FALSE AND (strlen($password) < 8 OR strlen($password) > 72)) $invalid = TRUE;
		if ($invalid === FALSE AND (strlen($name) > 150 OR $this->_has_4byte_char($name))) $invalid = TRUE;

		if ($invalid)
		{
			$this->_log_attempt('register', ($email !== '' ? $email : NULL), FALSE);
		return $this->_err('Data pendaftaran tidak valid (email sah, password 8-72 karakter, nama maks 150).', 422);
		}

		// M2 (keputusan Lead): 409 eksplisit diterima sadar
		// (risiko enumerasi diketahui; mitigasi = rate-limit /IP 5/jam).
		try
		{
			$this->db->trans_start();
			$insert_ok = $this->db->insert('career_candidates', array(
				'email'         => $email,
				'password_hash' => password_hash($password, PASSWORD_BCRYPT),
				'full_name'     => $name,
				'is_active'     => 1,
				'token_version' => 1,
			));
			// db_debug=FALSE: error() hanya valid SEKARANG (query berikutnya
			// termasuk ROLLBACK menimpanya).
			$err_code = (int) $this->db->error()['code'];
			$id = (int) $this->db->insert_id();
			if ($insert_ok !== FALSE)
			{
				$this->db->insert('career_candidate_profiles', array('id_candidate' => $id));
			}
			$this->db->trans_complete();
		}
		catch (Exception $e)
		{
			$this->db->trans_complete();
		}

		// H5: dup-key (uq_candidate_email) → 409 ramah, bukan 500
		if ($insert_ok === FALSE OR $this->db->trans_status() === FALSE)
		{
			$this->_log_attempt('register', $email, FALSE);
			if ($err_code === 1062)
			{
		return $this->_err('Email sudah terdaftar.', 409);
			}
			log_message('error', 'career/register gagal (non-dup) untuk email: ' . substr($email, 0, 190));
		return $this->_err('Gagal menyimpan akun. Coba lagi.', 500);
		}

		$this->career_candidate_id = $id;
		$this->_log_attempt('register', $email, TRUE);
		$this->_audit('register', 'career_candidates', $id);

		$this->_ok(array(
			'id_candidate' => $id,
			'full_name'    => $name,
			'token'        => $this->_issue_token($id, 1),
			'token_type'   => 'Bearer',
		), 'Pendaftaran berhasil.', 201);
	}

	// --------------------------------------------------------------
	// POST apicareer/Auth/login  body: {"email","password"}
	// --------------------------------------------------------------
	public function login_post()
	{
		$this->_career_require_key();

		$email = $this->post('email');
		$password = $this->post('password');
		$email = is_string($email) ? trim($email) : '';
		$password = is_string($password) ? $password : '';

		if ($email === '' OR $password === '')
		{
return $this->_err('email dan password wajib diisi.', 400);
		}

		// H3/L5: email divalidasi panjangnya juga (kolom VARCHAR(190) + rate table).
		if (strlen($email) > 190 OR ! filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->_log_attempt('login', NULL, FALSE);
		return $this->_err('Email atau password salah.', 401);
		}

		// H4: lock level akun DIHAPUS — limit per (email+IP) dan per-IP saja,
		// tanpa menblokir password benar dari IP yang tidak melebihi limit.
		if ($this->_login_blocked($email))
		{
			$this->_log_attempt('login', $email, FALSE);
		return $this->_err('Terlalu banyak percobaan login gagal. Coba lagi nanti.', 429);
		}

		$candidate = $this->db->select('id_candidate, email, password_hash, full_name, is_active, failed_login_count, locked_until, token_version')
			->where('email', $email)
			->get('career_candidates')
			->row();

		if ($candidate === NULL)
		{
			// H4: jalur sama (bcrypt tetap dijalankan) anti-enumerasi timing.
			password_verify($password, self::DUMMY_HASH);
			$this->_log_attempt('login', $email, FALSE);
		return $this->_err('Email atau password salah.', 401);
		}

		if ($candidate->is_active != 1 OR ! password_verify($password, $candidate->password_hash))
		{
			$this->_log_attempt('login', $email, FALSE);
			$this->career_candidate_id = (int) $candidate->id_candidate;
			$this->_login_note_failure($candidate->id_candidate);
			$this->_audit('login_gagal', 'career_candidates', (int) $candidate->id_candidate, array(), 'system');
return $this->_err('Email atau password salah.', 401);
		}

		$this->career_candidate_id = $candidate->id_candidate;
		$this->db->where('id_candidate', $candidate->id_candidate)
			->update('career_candidates', array(
				'failed_login_count' => 0,
				'last_login_at'      => date('Y-m-d H:i:s'),
			));
		$this->_log_attempt('login', $email, TRUE);
		$this->_audit('login', 'career_candidates', $candidate->id_candidate);

		$this->_ok(array(
			'id_candidate' => (int) $candidate->id_candidate,
			'full_name'    => $candidate->full_name,
			'token'        => $this->_issue_token($candidate->id_candidate, (int) $candidate->token_version),
			'token_type'   => 'Bearer',
		), 'Login berhasil.', 200);
	}

	// --------------------------------------------------------------
	// POST apicareer/Auth/logout — naikkan token_version (token lama mati)
	// --------------------------------------------------------------
	public function logout_post()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$this->db->set('token_version', 'token_version + 1', FALSE)
			->where('id_candidate', $candidate->id_candidate)
			->update('career_candidates');
		$this->_audit('logout', 'career_candidates', $candidate->id_candidate);

		$this->_ok(NULL, 'Logout berhasil.');
	}

	// --------------------------------------------------------------
	// PUT apicareer/Auth/password — ganti sandi sendiri (6C #4).
	// Body: {"password_lama","password_baru"}. Auth wajib; sandi lama
	// wajib benar (bcrypt); baru 8-72 byte; token_version +1 tepat satu
	// (token lama mati 401, login dengan token baru harus memakai
	// sandi baru). Audit TANPA sandi/token. Respons no-store/no-cache.
	// Tanpa kolom baru (pasca-reset admin memakai jalur/kolom sama).
	// --------------------------------------------------------------
	public function password_put()
	{
		// 6C.2 LOW-1: sandi tidak boleh di-cache — atas SEMUA status
		// respons jalur ini (termasuk 401 token-mati dan 4xx). Tanpa
		// syarat: Output::get_header() selalu NULL, kondisi dulu dead code.
		$this->output->set_header('Cache-Control: no-store');
		$this->output->set_header('Pragma: no-cache');

		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		// F8: rate-limit ganti sandi memakai jalur _login_blocked (tabel
		// career_login_attempts) — TANPA _log_attempt agar percobaan gagal di
		// sini tidak menghitung lockout login.
		if ($this->_login_blocked($candidate->email))
		{
return $this->_err('Terlalu banyak percobaan. Coba lagi nanti.', 429);
		}
		$lama  = $this->put('password_lama');
		$baru  = $this->put('password_baru');
		$lama  = is_string($lama) ? $lama : '';
		$baru  = is_string($baru) ? $baru : '';
		if ($lama === '' OR $baru === '')
		{
return $this->_err('password_lama dan password_baru wajib diisi.', 400);
		}
		if (strlen($baru) < 8 OR strlen($baru) > 72)
		{
return $this->_err('password_baru harus 8-72 byte.', 422);
		}
		// F8: sandi sama persis dgn lama tidak berguna — tolak.
		if ($lama === $baru)
		{
return $this->_err('Password baru tidak boleh sama dengan password lama.', 422);
		}

		// Verifikasi sandi lama SEBELUM menulis; pesan generik (jangan
		// bocorkan detail), token lama tetap berlaku bila gagal.
		// hash diambili langsung dari DB (Career_base tidak melewatkan
		// kolom sensitif pada _auth_candidate).
		$baris = $this->db->select('password_hash')
			->where('id_candidate', (int) $candidate->id_candidate)
			->get('career_candidates')->row();
		if ($baris === NULL OR ! password_verify($lama, $baris->password_hash))
		{
			// F3: bukan 401 — token sesi valid; kesalahan sandi lama = 422.
return $this->_err('Password lama salah.', 422);
		}

		$this->db->trans_start();
		$ok = $this->db->set('password_hash', password_hash($baru, PASSWORD_BCRYPT))
			->set('token_version', 'token_version + 1', FALSE)
			->set('failed_login_count', 0)
			->set('locked_until', NULL)
			->where('id_candidate', (int) $candidate->id_candidate)
			->update('career_candidates');
		$this->db->trans_complete();
		if ($ok === FALSE OR $this->db->trans_status() === FALSE)
		{
return $this->_err('Gagal mengubah password. Coba lagi.', 500);
		}

		// Audit TANPA isi sandi/token (bentuk panjang saja).
		$this->_audit('ganti_password', 'career_candidates', (int) $candidate->id_candidate);

		$this->_ok(array(
			'id_candidate' => (int) $candidate->id_candidate,
			'token_lama_invalid' => TRUE,
		), 'Password berhasil diubah. Silakan masuk lagi dengan password baru.');
	}

	// --------------------------------------------------------------
	// GET apicareer/Auth/me
	// --------------------------------------------------------------
	public function me_get()
	{
		$candidate = $this->_require_auth();
		if ($candidate === NULL) return;

		$profile = $this->db->where('id_candidate', $candidate->id_candidate)
			->get('career_candidate_profiles')->row_array();
		unset($profile['created_at'], $profile['updated_at']);

		$this->_ok(array(
			'id_candidate'    => (int) $candidate->id_candidate,
			'email'           => $candidate->email,
			'full_name'       => $candidate->full_name,
			'profile'         => $profile,
		), 'OK');
	}

	// --------------------------------------------------------------
	// Terbitkan JWT HS256: sub=id_candidate, ver=token_version, exp.
	// --------------------------------------------------------------
	protected function _issue_token($id_candidate, $version)
	{
		// M9: _career_require_key() mengirim 500 nyata + exit; tidak ada
		// set_response+exit yang mengosongkan output.
		$this->_career_require_key();
		$expire = (int) $this->config->item('career_jwt_expire');
		$now = time();
		return CareerJWT::encode(array(
			'iss' => 'jacos-career',
			'sub' => (string) $id_candidate,
			'ver' => (int) $version,
			'iat' => $now,
			'exp' => $now + $expire,
		), $this->_career_jwt_key());
	}
}
