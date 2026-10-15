<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fase 6B — Kandidat Karier: daftar akun kandidat baca + reset password D2.
 * Baca = perm career_kandidat_list (004); reset = perm
 * career_candidate_reset_password (005). Kandidat password hash disimpan
 * dengan PASSWORD_BCRYPT (pola sama dengan apicareer Auth::register).
 */
class Model_career_kandidat extends CI_Model
{
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
			$q = $this->db->escape_like_str((string) $filter['q']);
			$w[] = "(c.full_name LIKE '%$q%' ESCAPE '!' OR c.email LIKE '%$q%' ESCAPE '!')";
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
	 * Reset password D2: hash PASSWORD_BCRYPT di sisi backend, simpan.
	 * TIDAK mengembalikan sandi dalam respons (null); TANPA email.
	 * return array(bool, pesan 'kode|teks').
	 */
	public function reset_password($id_candidate, $sandi)
	{
		if ($sandi === '' || strlen($sandi) < 8) {
			return array(FALSE, '422|Password minimal 8 karakter.');
		}
		$kandidat = $this->cari($id_candidate);
		if (!$kandidat) {
			return array(FALSE, '404|Kandidat tidak ditemukan.');
		}
		$hash = password_hash($sandi, PASSWORD_BCRYPT);
		$this->db->trans_start();
		$this->db->where('id_candidate', $id_candidate)
			->update('career_candidates', array(
				'password_hash' => $hash,
				'updated_at'    => date('Y-m-d H:i:s'),
			));
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return array(FALSE, '500|Gagal menyimpan password baru.');
		}
		return array(TRUE, 'Password kandidat direset.');
	}
}
