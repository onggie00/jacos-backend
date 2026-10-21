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

	/** Peta kolom sort (whitelist) → ekspresi SQL. */
	private $_order_map = array(
		'id'      => 'c.id_candidate',
		'nama'    => 'c.full_name',
		'email'   => 'c.email',
		'telepon' => 'p.phone',
		'lamaran' => 'jumlah_lamaran',
		'login'   => 'c.last_login_at',
	);

	/** Daftar kandidat untuk admin: email, nama, lamaran count + lamaran aktif. */
	public function daftar($filter = array(), $limit = 20, $offset = 0, $sort_col = NULL, $sort_dir = 'asc')
	{
		$limit  = max(1, (int) $limit);
		$offset = max(0, (int) $offset);
		$this->_where_filter($filter);
		$this->db->select("c.id_candidate, c.email, c.full_name, c.is_active, c.last_login_at,
				p.phone,
				(SELECT COUNT(*) FROM career_applications a
				  WHERE a.id_candidate = c.id_candidate) AS jumlah_lamaran")
			->from('career_candidates c')
			->join('career_candidate_profiles p', 'p.id_candidate = c.id_candidate', 'left');
		// Default (sort tak valid): terlama dulu — id ASC (poin user).
		if ($sort_col !== NULL && isset($this->_order_map[$sort_col])) {
			$this->db->order_by($this->_order_map[$sort_col], $sort_dir === 'desc' ? 'DESC' : 'ASC');
		}
		$this->db->order_by('c.id_candidate', 'ASC')
			->limit((int) $limit, (int) $offset);
		return $this->db->get()->result();
	}

	public function daftar_count($filter = array())
	{
		$this->_where_filter($filter);
		$row = $this->db->select('COUNT(*) AS n')
			->from('career_candidates c')
			->get()
			->row();
		return $row ? (int) $row->n : 0;
	}

	/** Hitung utk 4 infobox list kandidat (satu query). */
	public function infobox_counts()
	{
		$row = $this->db->select("COUNT(*) AS total,
				SUM(c.is_active = 1) AS aktif,
				SUM(c.is_active = 0) AS nonaktif,
				SUM(EXISTS(SELECT 1 FROM career_applications a
				  WHERE a.id_candidate = c.id_candidate)) AS punya_lamaran")
			->from('career_candidates c')
			->get()
			->row();
		return array(
			'total'         => $row ? (int) $row->total : 0,
			'aktif'         => $row ? (int) $row->aktif : 0,
			'nonaktif'      => $row ? (int) $row->nonaktif : 0,
			'punya_lamaran' => $row ? (int) $row->punya_lamaran : 0,
		);
	}

	private function _where_filter($filter)
	{
		// M1 6B.3: Query Builder like() — escape satu lapis oleh CI
		// (ESCAPE '!'): literal %, _, !, kutip, backslash dicari apa adanya.
		if (isset($filter['q']) && career_q($filter['q']) !== '') {
			$q = career_q($filter['q']);
			$this->db->group_start();
			$this->db->like('c.full_name', $q, 'both');
			$this->db->or_like('c.email', $q, 'both');
			$this->db->group_end();
		}
	}

	/**
	 * Tambah kandidat baru dari admin panel (perm career_candidate_add).
	 * Aturan input mengikuti register FE (apicareer/Auth::register_post):
	 * email sah maks 190, nama maks 150 tanpa emoji (tabel utf8mb3).
	 * Telepon opsional untuk kolom phone di career_candidate_profiles.
	 * Password dibuat server lewat sandi_acak(), di-hash bcrypt (PASSWORD_BCRYPT);
	 * plaintext sandi dikembalikan SEKALI di sini, controller yang menampilkannya,
	 * tidak pernah masuk audit/log. Dup email dicek dulu (pesan ramah) dan
	 * UNIQ pada insert tetap diantisipasi (err 1062) untuk kondisi balapan.
	 * return array(bool, payload) — payload gagal: 'kode|pesan', sukses: password.
	 */
	public function tambah($email, $nama, $telepon)
	{
		$email   = trim((string) $email);
		$nama    = trim((string) $nama);
		$telepon = trim((string) $telepon);

		if ($email === '' OR $nama === '') {
			return array(FALSE, '422|Email dan nama wajib diisi.');
		}
		if ( ! filter_var($email, FILTER_VALIDATE_EMAIL) OR strlen($email) > 190) {
			return array(FALSE, '422|Email tidak sah (maksimum 190 karakter).');
		}
		if (career_panjang($nama) > 150 OR career_ada_emoji($nama)) {
			return array(FALSE, '422|Nama maksimum 150 karakter dan tidak boleh berisi emoji.');
		}
		if ($telepon !== '' AND ! preg_match('/^[0-9+\-\(\) ]{5,30}$/', $telepon)) {
			return array(FALSE, '422|Nomor telepon tidak sah (5-30 karakter, angka/+-() spasi).');
		}

		$ada = $this->db->where('email', $email)
			->count_all_results('career_candidates');
		if ($ada > 0) {
			return array(FALSE, '409|Email sudah terdaftar.');
		}

		$sandi = $this->sandi_acak(14);
		$this->db->trans_begin();
		$this->db->insert('career_candidates', array(
			'email'         => $email,
			'password_hash' => password_hash($sandi, PASSWORD_BCRYPT),
			'full_name'     => $nama,
			'is_active'     => 1,
			'token_version' => 1,
		));
		$err = (int) $this->db->error()['code'];
		$id  = (int) $this->db->insert_id();
		if ($id >= 1 AND $err !== 1062) {
			$this->db->insert('career_candidate_profiles', array(
				'id_candidate' => $id,
				'phone'        => $telepon !== '' ? $telepon : NULL,
			));
		}
		if ($this->db->trans_status() === FALSE OR $id < 1 OR $err === 1062) {
			$this->db->trans_rollback();
			if ($err === 1062) {
				return array(FALSE, '409|Email sudah terdaftar.');
			}
			return array(FALSE, '500|Gagal menyimpan kandidat baru.');
		}
		$this->db->trans_commit();
		return array(TRUE, array('password' => $sandi, 'id_candidate' => $id));
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

		$this->db->trans_begin();
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
		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array(FALSE, '500|Gagal menyimpan password baru.');
		}
		$this->db->trans_commit();
		return array(TRUE, array('password' => $sandi));
	}
}
