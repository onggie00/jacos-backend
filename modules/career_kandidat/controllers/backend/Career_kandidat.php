<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*| --------------------------------------------------------------------------
*| Career Kandidat Controller (Fase 6B)
*| --------------------------------------------------------------------------
*| Daftar akun kandidat (baca, perm career_kandidat_list) + reset password
*| D2 (perm career_candidate_reset_password — lupa password ditangani
*| admin, TANPA email apa pun sesuai keputusan D2). Reset TIDAK pernah
*| menampilkan hash lama; audit hanya mencatat aksi, bukan sandi.
*/
class Career_kandidat extends Admin
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_career_kandidat');
		$this->load->helper('career_core/career_core');
	}

	/** GET administrator/career_kandidat — daftar + cari + paging. */
	public function index($offset = 0)
	{
		if (!$this->is_allowed('career_kandidat_list', false)) {
			show_error('Forbidden', 403);
		}

		$filter = array(
			'q' => career_q($this->input->get('q')),
		);
		$offset = max(0, (int) $offset);

		// Sort server-side (whitelist di model); default: terlama dulu (id ASC).
		list($sort_col, $sort_dir) = career_sort_state(
			array('id', 'nama', 'email', 'telepon', 'lamaran', 'login'), 'id', 'asc');
		$base_query = http_build_query(array('q' => $filter['q']));

		$this->data['q']            = $filter['q'];
		$this->data['sort_col']     = $sort_col;
		$this->data['sort_dir']     = $sort_dir;
		$this->data['base_query']   = $base_query;
		$this->data['kandidats']    = $this->model_career_kandidat->daftar($filter, $this->limit_page, $offset, $sort_col, $sort_dir);
		$this->data['jumlah']       = $this->model_career_kandidat->daftar_count($filter);
		$this->data['infobox']      = $this->model_career_kandidat->infobox_counts();
		$this->data['csrf_field']   = career_csrf_field();

		$config = [
			'base_url'           => 'administrator/career_kandidat/index/',
			'total_rows'         => $this->data['jumlah'],
			'per_page'           => $this->limit_page,
			'uri_segment'        => 4,
			'reuse_query_string' => TRUE, // pertahankan ?q=&sort=&dir= di pagination
		];
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kandidat Karier');
		$this->render('backend/standart/administrator/career_kandidat/career_kandidat_list', $this->data);
	}

	/**
	 * Tambah kandidat baru dari admin panel (keputusan user 2026-10-21).
	 * POST administrator/career_kandidat/add — perm career_candidate_add.
	 * Password DIBUAT SERVER (sandi acak ala reset) dan dikembalikan SEKALI
	 * di respons (kunci 'password'); admin tetap bisa memakai aksi reset
	 * password bila kandidat lupa sandinya. TANPA email (keputusan D2).
	 */
	public function add()
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Method harus POST.');
		}
		if ( ! career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}
		if ( ! $this->is_allowed('career_candidate_add', false)) {
			return $this->_json_deny('Forbidden');
		}

		list($ok, $buah) = $this->model_career_kandidat->tambah(
			$this->input->post('email'),
			$this->input->post('full_name'),
			$this->input->post('phone')
		);
		if ( ! $ok) {
			list($kode, $teks) = $this->_pecah($buah);
			return $this->_json_deny($teks, $kode);
		}

		// Audit: hanya aksi + id, tanpa menyimpan sandi apa pun.
		career_audit('tambah_kandidat', 'career_candidates', (int) $buah['id_candidate']);
		return $this->_json_done(
			'Kandidat baru dibuat. Password di bawah terlihat SEKALI — catat dan bagikan '
			.'melalui saluran administrasi (BUKAN email — keputusan D2); '
			.'sarankan kandidat menggantinya setelah masuk.',
			array(
				'password'     => (string) $buah['password'],
				'id_candidate' => (int) $buah['id_candidate'],
			),
			TRUE
		);
	}

	/**
	 * Reset password (D2, 6B.1): POST administrator/career_kandidat/
	 * reset_password/<id_candidate>. Password dibuat server (acak >=12,
	 * tanpa karakter ambigu), bcrypt, token_version+1 → sesi/API lama
	 * langsung mati; percobaan login gagal email itu direset.
	 * Password baru DIKEMBALIKAN SEKALI di respons (kunci 'password');
	 * tidak disimpan di audit/flash/log. TANPA email (keputusan D2).
	 */
	public function reset_password($id_candidate = NULL)
	{
		if ($this->input->method() !== 'post') {
			return $this->_json_deny('Method harus POST (reset password tidak lewat GET).');
		}
		if (! career_csrf_ok()) {
			return $this->_json_deny('Sesi tidak sah (CSRF). Muat ulang halaman.');
		}
		if (!$this->is_allowed('career_candidate_reset_password', false)) {
			return $this->_json_deny('Forbidden');
		}

		$id = (int) $id_candidate;
		if ($id < 1) return $this->_json_deny('Kandidat tidak ditemukan.', 404);

		list($ok, $buah) = $this->model_career_kandidat->reset_password($id);
		if (!$ok) {
			list($kode, $teks) = $this->_pecah($buah);
			return $this->_json_deny($teks, $kode);
		}

		// Audit: tanpa menyimpan sandi apa pun (bukan plaintext, bukan hash).
		career_audit('reset_password_kandidat', 'career_candidates', $id);
		return $this->_json_done(
			'Sandi baru dibuat server. Catat dan bagikan lewat saluran administrasi '
			.'(BUKAN email — keputusan D2); sarankan kandidat ganti sandi setelah masuk.',
			array('password' => (string) $buah['password']),
			TRUE
		);
	}

	private function _pecah($pesan)
	{
		$pisah = explode('|', (string) $pesan, 2);
		$kode = ctype_digit($pisah[0]) ? (int) $pisah[0] : 422;
		return array($kode, isset($pisah[1]) ? $pisah[1] : $pesan);
	}

	private function _json_done($pesan, $data = array(), $no_store = false)
	{
		$badan = array('success' => true, 'message' => (string) $pesan);
		if (! empty($data)) { $badan['data'] = $data; }
		$this->output->set_status_header(200);
		$this->output->set_content_type('application/json');
		if ($no_store) {
			// Reset sandi: respons TIDAK boleh dicache apa pun (#10).
			$this->output->set_header('Cache-Control: no-store');
			$this->output->set_header('Pragma: no-cache');
		}
		$this->output->set_output(json_encode($badan));
	}

	private function _json_deny($pesan = 'Forbidden', $kode = 403)
	{
		$this->output->set_status_header($kode);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('success' => false, 'message' => (string) $pesan)));
	}
}
