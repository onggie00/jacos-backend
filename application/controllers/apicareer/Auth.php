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

	// --------------------------------------------------------------
	// POST apicareer/Auth/register
	// body: {"email","password","full_name"}
	// --------------------------------------------------------------
	public function register_post()
	{
		$email    = trim((string) $this->post('email'));
		$password = (string) $this->post('password');
		$name     = trim((string) $this->post('full_name'));

		if ($email === '' OR $password === '' OR $name === '')
		{
return $this->_err('email, password, dan full_name wajib diisi.', 400);
		}
		if ( ! filter_var($email, FILTER_VALIDATE_EMAIL) OR strlen($email) > 190)
		{
return $this->_err('Format email tidak valid (maks 190 karakter).', 422);
		}
		if (strlen($password) < 8)
		{
return $this->_err('Password minimal 8 karakter.', 422);
		}
		if (strlen($name) > 150)
		{
return $this->_err('Nama maksimal 150 karakter.', 422);
		}

		if ($this->_register_blocked())
		{
return $this->_err('Terlalu banyak percobaan pendaftaran. Coba lagi nanti.', 429);
		}

		// Email unik di level DB; cek dulu untuk pesan ramah (idempotent race tetap aman).
		$exists = $this->db->where('email', $email)->count_all_results('career_candidates');
		if ($exists > 0)
		{
			$this->_log_attempt('register', $email, FALSE);
return $this->_err('Email sudah terdaftar.', 409);
		}

		$this->db->trans_start();
		$this->db->insert('career_candidates', array(
			'email'         => $email,
			'password_hash' => password_hash($password, PASSWORD_BCRYPT),
			'full_name'     => $name,
			'is_active'     => 1,
			'token_version' => 1,
		));
		$id = (int) $this->db->insert_id();
		$this->db->insert('career_candidate_profiles', array('id_candidate' => $id));
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
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
		$email    = trim((string) $this->post('email'));
		$password = (string) $this->post('password');

		if ($email === '' OR $password === '')
		{
return $this->_err('email dan password wajib diisi.', 400);
		}

		// Lock aktif di level kandidat (independen dari email/password benar).
		$candidate = $this->db->select('id_candidate, email, password_hash, full_name, is_active, failed_login_count, locked_until, token_version')
			->where('email', $email)
			->get('career_candidates')
			->row();

		if ($candidate === NULL)
		{
			$this->_log_attempt('login', $email, FALSE);
return $this->_err('Email atau password salah.', 401);
		}

		if ($this->_login_locked($candidate))
		{
			$this->_log_attempt('login', $email, FALSE);
return $this->_err('Akun terkunci sementara karena gagal login berulang. Coba lagi nanti.', 429);
		}

		if ($candidate->is_active != 1 OR ! password_verify($password, $candidate->password_hash))
		{
			$this->_log_attempt('login', $email, FALSE);
			$this->career_candidate_id = $candidate->id_candidate;
			$this->_login_note_failure($candidate);
			$this->_audit('login_gagal', 'career_candidates', $candidate->id_candidate);
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
		$key = $this->_career_jwt_key();
		if ($key === NULL)
		{
			// Tidak selamat berlanjut; hentikan response 500 eksplisit.
			$this->set_response(array(
				'status' => FALSE,
				'message' => 'Server belum dikonfigurasi (career_jwt_key).',
				'data' => NULL,
			), 500);
			log_message('error', 'CAREER_JWT_KEY belum diisi di credentials.php');
			exit;
		}
		$expire = (int) $this->config->item('career_jwt_expire');
		$now = time();
		return CareerJWT::encode(array(
			'iss' => 'jacos-career',
			'sub' => (string) $id_candidate,
			'ver' => (int) $version,
			'iat' => $now,
			'exp' => $now + $expire,
		), $key);
	}
}
