<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fase 6B/6B.1 — Kandidat Karier: daftar akun kandidat baca + reset password D2.
 * Reset (6B.1): password DIBUAT server (random, >=12, tanpa karakter ambigu),
 * bcrypt PASSWORD_BCRYPT (pola apicareer Auth::register), token_version +1
 * dalam update yang sama (JWT/API lama mati: klaim ver != token_version),
 * failed_login_count & percobaan login gagal milik email kandidat itu
 * direset. Password baru dikembalikan SEKALI di respons controller;
 * tidak disimpan di audit/log/flash.
 */
class Model_career_kandidat extends CI_Model
{
	/** Alfabet tanpa karakter mudah tertukar (0/O, 1/l/I). */
	const ALFABET_SANDI = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

	public function __construct() { parent::__construct(); }

	/** Daftar kandidat untuk admin: email, nama, lamaran count + lamaran aktif. */
	public function daftar($filter = array(), $limit = 20, $offset = 0)
	{
		$limit  = max(1, (int) $limit);
		$offset = max(0, (int) $offset);
		$where  = $this->_where_filter($filter);
		return $this->db->query(
			"SELECT c.id_candidate, c.email, c.full_name, c.is_active, c.last_login_at,
				p.phone,
				(SELECT COUNT(*) FROM career_applications a
				  WHERE a.id_candidate = c.id_candidate) AS jumlah_lamaran
			 FROM career_candidates c
			 LEFT JOIN career_candidate_profiles p ON p.id_candidate = c.id_candidate
			 $where
			 ORDER BY c.id_candidate DESC
			 LIMIT $limit OFFSET $offset"
		)->result();
	}

	public function daftar_count($filter = array())
	{
		$where = $this->_where_filter($filter);
		$row = $this->db->query("SELECT COUNT(*) AS n FROM career_candidates c $where")->row();
		return $row ? (int) $row->n : 0;
	}

	private function _where_filter($filter)
	{
		$w = array('1=1');
		if (! empty($filter['q'])) {
			// Pencarian via subquery raw: LEAST escape manual agar _/% tetap
			// literal (CI like() dengan ESCAPE default membuat kueri rumit
			// di subquery string; nilai tetap ter-bind lewat placeholder).
			$q = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), (string) $filter['q']);
			$w[] = "(c.full_name LIKE '%{$q}%' OR c.email LIKE '%{$q}%')";
		}
		return empty($w) ? '' : ' WHERE ' . implode(' AND ', $w);
	}

	/** Hemat: kandidat yang belum pernah lamar tetap muncul (P3). */
	public function cari($id_candidate)
	{
		return $this->db->where('id_candidate', (int) $id_candidate)
			->get('career_candidates')->row();
	}

	/**
	 * Password acak server-side (6B.1): panjang 14, alfabet tanpa karakter
	 * ambigu, random_int per-posisi (CSPRNG).
	 */
	public function sandi_acak($panjang = 14)
	{
		$panjang = max(12, (int) $panjang);
		$n = strlen(self::ALFABET_SANDI);
		$s = '';
		for ($i = 0; $i < $panjang; $i++) {
			$s .= self::ALFABET_SANDI[random_int(0, $n - 1)];
		}
		return $s;
	}

	/**
	 * Reset password D2: buat password server, hash PASSWORD_BCRYPT,
	 * token_version+1 DALAM UPDATE YANG SAMA (sesi/API lama mati),
	 * reset hitungan gagal login kandidat + baris attempt 'login'
	 * milik email kandidat itu saja (career_login_attempts).
	 * return array(bool, payload) — payload password plaintext SEKALI di
	 * sini; controller yang mengeluarkannya ke respons (tidak di-audit/log).
	 */
	public function reset_password($id_candidate)
	{
		$kandidat = $this->cari($id_candidate);
		if (!$kandidat) {
			return array(FALSE, '404|Kandidat tidak ditemukan.');
		}

		$sandi   = $this->sandi_acak(14);
		$hash    = password_hash($sandi, PASSWORD_BCRYPT);
		$k	= (string) $kandidat->email;

		$this->db->trans_start();
		$this->db->where('id_candidate', $id_candidate)
			->set('password_hash', $hash)
			->set('token_version', 'token_version + 1', FALSE)
			->set('failed_login_count', 0)
			->set('updated_at', date('Y-m-d H:i:s'))
			->update('career_candidates');
		// (6B.1) hapus percobaan login GAGAL milik EMAIL kandidat ini saja —
		// bukan baris pengguna lain; jendela rate-limit login langsung beres.
		$this->db->where('email', $k)
			->where('attempt_type', 'login')
			->where('succeeded', 0)
			->delete('career_login_attempts');
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return array(FALSE, '500|Gagal menyimpan password baru.');
		}
		return array(TRUE, array('password' => $sandi));
	}
}
